<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawledPages extends Model
{
    protected $fillable = ['hash','number_pages_crawled','unique_images','unique_internal_links','unique_external_links','page_load','word_count','title_length'];
    use HasFactory;
}
