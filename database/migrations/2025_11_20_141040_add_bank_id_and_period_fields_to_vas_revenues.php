<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vas_revenues', function (Blueprint $table) {
            $table->foreignId('bank_id')
                ->nullable()
                ->after('aggregator_id')
                ->constrained('banks')
                ->nullOnDelete();
            $table->unsignedTinyInteger('payment_period_month')
                ->nullable()
                ->after('payment_date');
            $table->unsignedSmallInteger('payment_period_year')
                ->nullable()
                ->after('payment_period_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vas_revenues', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'payment_period_month', 'payment_period_year']);
        });
    }
};
