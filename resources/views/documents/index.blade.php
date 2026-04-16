@extends('layouts.app')

@section('title', 'Documents')
@section('header', 'My Documents')

@section('content')
<div class="space-y-6">
    <!-- Upload Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload New Document</h3>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select File (PDF, PNG, JPG)</label>
                <input type="file" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload mr-2"></i>Upload & Process
            </button>
        </form>
    </div>
    
    <!-- Documents List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Uploaded Documents</h3>
        </div>
        
        @if($documents->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($documents as $doc)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-{{ in_array($doc->file_type, ['png','jpg','jpeg']) ? 'image' : 'pdf' }} text-orange-600 text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 truncate">{{ $doc->original_name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">Uploaded {{ $doc->created_at->diffForHumans() }}</p>
                                    
                                    @if($doc->ocr_text)
                                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Extracted Text Preview:</p>
                                            <p class="text-sm text-gray-700 line-clamp-3">{{ Str::limit($doc->ocr_text, 200) }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-orange-600 mt-2">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Text extraction pending or failed
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View File">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="p-4 border-t border-gray-100">
                {{ $documents->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-5xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium">No documents uploaded yet</p>
                <p class="text-sm mt-2">Upload PDFs or images to extract text and use them in chat conversations.</p>
            </div>
        @endif
    </div>
</div>
@endsection