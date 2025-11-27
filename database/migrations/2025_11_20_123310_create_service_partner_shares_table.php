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
        Schema::create('service_partner_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->unique()
                ->constrained('services')
                ->onDelete('cascade');
            $table->decimal('dr_share', 5, 2)->default(50);
            $table->decimal('aj_share', 5, 2)->default(30);
            $table->decimal('tj_share', 5, 2)->default(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_partner_shares');
    }
};
