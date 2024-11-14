<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;


class Document extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'role_applied', 'document_path', 'status'];

    protected $appends = ['document_url'];

    protected $with = ['user'];

    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->document_path) {
            if (str_starts_with($this->document_path, 'http')) {
                return $this->document_path;
            } else {
                return asset($this->document_path);
            }
        } else {
            return null;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
