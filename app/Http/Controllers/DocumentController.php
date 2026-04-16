<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\OCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this

class DocumentController extends Controller
{
    use AuthorizesRequests; // Add this trait

    protected $ocrService;

    public function __construct(OCRService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function index()
    {
        $documents = Auth::user()->documents()->latest()->paginate(10);
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,png,jpg,jpeg|max:10240',
        ]);

        try {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $file->storeAs('documents', $filename, 'public');

            $ocrData = null;
            $ocrText = '';

            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $result = $this->ocrService->processImage($path);
                $ocrText = $result['text'];
                $ocrData = $result;
            } else {
                $result = $this->ocrService->processDocument($path);
                $ocrText = $result['text'];
                $ocrData = $result;
            }

            $document = Document::create([
                'user_id' => Auth::id(),
                'original_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'ocr_text' => $ocrText,
                'ocr_data' => $ocrData,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'document' => $document,
                    'message' => 'Document uploaded and processed successfully',
                ]);
            }

            return redirect()->back()->with('success', 'Document uploaded successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process document: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to upload document');
        }
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('documents.show', compact('document'));
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully');
    }

    public function getText(Document $document)
    {
        $this->authorize('view', $document);
        
        return response()->json([
            'text' => $document->ocr_text,
            'name' => $document->original_name,
        ]);
    }
}