<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'number_of_urls',
        'is_index',
        'enabled',
        'errors',
        'is_pending',
        'last_downloaded',
        'last_submitted',
        'warnings',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
