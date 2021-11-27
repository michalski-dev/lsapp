<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlSession extends Model
{
    protected $fillable = ['hash','url','http_status_code'];
    use HasFactory;
}
