<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('ad_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ad_zone_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->longText('html_content'); // Placeholders like __IMAGE_URL__, __HEADLINE__, __LINK__
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_templates');
    }
}
