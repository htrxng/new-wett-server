<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'category_id',
        'short_description',
        'description',
        'features',
        'details',
        'price',
        'photos',
        'demo_video_url',
        'visible_on_home_page',
        'rank',
        'active',
        'created_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'visible_on_home_page' => 'boolean',
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
