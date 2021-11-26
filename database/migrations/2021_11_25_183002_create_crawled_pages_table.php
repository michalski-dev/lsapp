<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrawledPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawled_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->integer('number_pages_crawled');
            $table->integer('unique_images');
            $table->integer('unique_internal_links');
            $table->integer('unique_external_links');
            $table->float('page_load');
            $table->float('word_count');
            $table->float('title_length');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crawled_pages');
    }
}
