<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OCRService
{
    protected $client;

    public function __construct()
    {
        try {
            $this->client = new ImageAnnotatorClient([
                'credentials' => storage_path('app/google-credentials.json')
            ]);
        } catch (\Exception $e) {
            Log::error('OCR Client Error: ' . $e->getMessage());
            // Fallback to alternative OCR or throw exception
            throw $e;
        }
    }

    public function processDocument($filePath)
    {
        try {
            $imageContent = file_get_contents(storage_path('app/public/' . $filePath));
            
            $response = $this->client->documentTextDetection($imageContent);
            $annotation = $response->getFullTextAnnotation();

            if ($annotation) {
                return [
                    'text' => $annotation->getText(),
                    'pages' => $this->extractPages($annotation),
                    'blocks' => $this->extractBlocks($annotation),
                ];
            }

            return ['text' => '', 'pages' => [], 'blocks' => []];

        } catch (\Exception $e) {
            Log::error('OCR Processing Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processImage($filePath)
    {
        try {
            $imageContent = file_get_contents(storage_path('app/public/' . $filePath));
            
            $response = $this->client->textDetection($imageContent);
            $texts = $response->getTextAnnotations();

            if (!empty($texts)) {
                return [
                    'text' => $texts[0]->getDescription(),
                    'annotations' => $this->formatAnnotations($texts),
                ];
            }

            return ['text' => '', 'annotations' => []];

        } catch (\Exception $e) {
            Log::error('OCR Image Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function extractPages($annotation)
    {
        $pages = [];
        foreach ($annotation->getPages() as $page) {
            $pages[] = [
                'width' => $page->getWidth(),
                'height' => $page->getHeight(),
                'blocks_count' => count($page->getBlocks()),
            ];
        }
        return $pages;
    }

    private function extractBlocks($annotation)
    {
        $blocks = [];
        foreach ($annotation->getPages() as $page) {
            foreach ($page->getBlocks() as $block) {
                $blocks[] = [
                    'type' => $this->getBlockType($block->getBlockType()),
                    'text' => $this->extractBlockText($block),
                    'confidence' => $block->getConfidence(),
                ];
            }
        }
        return $blocks;
    }

    private function getBlockType($type)
    {
        $types = [
            1 => 'UNKNOWN',
            2 => 'TEXT',
            3 => 'TABLE',
            4 => 'FORMULA',
            5 => 'LIST',
        ];
        return $types[$type] ?? 'UNKNOWN';
    }

    private function extractBlockText($block)
    {
        $text = '';
        foreach ($block->getParagraphs() as $paragraph) {
            foreach ($paragraph->getWords() as $word) {
                foreach ($word->getSymbols() as $symbol) {
                    $text .= $symbol->getText();
                }
                $text .= ' ';
            }
            $text .= "\n";
        }
        return $text;
    }

    private function formatAnnotations($texts)
    {
        $annotations = [];
        foreach ($texts as $text) {
            $vertices = $text->getBoundingPoly()->getVertices();
            $annotations[] = [
                'text' => $text->getDescription(),
                'confidence' => $text->getConfidence(),
                'bounding_box' => [
                    'x1' => $vertices[0]->getX(),
                    'y1' => $vertices[0]->getY(),
                    'x2' => $vertices[2]->getX(),
                    'y2' => $vertices[2]->getY(),
                ],
            ];
        }
        return $annotations;
    }

    public function __destruct()
    {
        if ($this->client) {
            $this->client->close();
        }
    }
}