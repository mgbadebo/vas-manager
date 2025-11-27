<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_partner_shares', function (Blueprint $table) {
            $table->date('effective_from')->nullable()->after('service_id');
            $table->dropUnique('service_partner_shares_service_id_unique');
        });

        DB::table('service_partner_shares')->whereNull('effective_from')->update([
            'effective_from' => now()->toDateString(),
        ]);

        Schema::table('service_partner_shares', function (Blueprint $table) {
            $table->unique(['service_id', 'effective_from'], 'service_partner_shares_service_id_effective_from_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_partner_shares', function (Blueprint $table) {
            $table->dropUnique('service_partner_shares_service_id_effective_from_unique');
            $table->dropColumn('effective_from');
            $table->unique('service_id', 'service_partner_shares_service_id_unique');
        });
    }
};
