<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'site_url',
        'permissions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sitemaps()
    {
        return $this->hasMany(Sitemap::class);
    }
}
