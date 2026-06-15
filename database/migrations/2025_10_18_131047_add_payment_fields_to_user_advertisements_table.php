<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToUserAdvertisementsTable extends Migration
{
    public function up()
    {
        Schema::table('user_advertisements', function (Blueprint $table) {
            // Add payment_intent_id if it doesn't exist
            if (!Schema::hasColumn('user_advertisements', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('payment_id');
            }
            
            // Add paid_at if it doesn't exist
            if (!Schema::hasColumn('user_advertisements', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_intent_id');
            }
            
            // Add payment_type if it doesn't exist
            if (!Schema::hasColumn('user_advertisements', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_status');
            }
            
            // Make sure payment_status column exists and has correct values
            if (!Schema::hasColumn('user_advertisements', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('total_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('user_advertisements', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id', 'paid_at', 'payment_type']);
        });
    }
}