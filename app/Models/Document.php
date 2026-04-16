<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
     protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'file_type',
        'ocr_text',
        'ocr_data'
    ];

    protected $casts = [
        'ocr_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
