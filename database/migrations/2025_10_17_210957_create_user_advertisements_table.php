<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAdvertisementsTable extends Migration
{
    public function up()
    {
        Schema::create('user_advertisements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // === YEH MUKAMMAL FIX HAI: user_id ab shuru se hi UUID hai ===
            $table->char('user_id', 36);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreignUuid('ad_zone_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('ad_template_id')->nullable()->constrained()->onDelete('set null');
            $table->json('content');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_status')->default('pending');
            $table->string('payment_id')->nullable();
            $table->string('status')->default('pending_approval');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_advertisements');
    }
}

