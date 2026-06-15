<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdZonesTable extends Migration
{
    public function up()
    {
        Schema::create('ad_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('page_location')->comment('e.g., home, search, post_detail');
            $table->decimal('price_per_day', 8, 2);
            $table->json('specifications')->comment('e.g., {"width": 728, "height": 90, "type": "image"}');
            $table->boolean('auto_approve')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_zones');
    }
}
