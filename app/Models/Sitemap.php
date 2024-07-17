<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'url',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
