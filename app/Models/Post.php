<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'summary',
        'content',
        'cover_photo_url',
        'visible_on_website',
        'rank',
        'active',
        'created_at',
    ];

    protected $casts = [
        'visible_on_website' => 'boolean',
        'active' => 'boolean',
        'rank' => 'integer',
    ];
}
