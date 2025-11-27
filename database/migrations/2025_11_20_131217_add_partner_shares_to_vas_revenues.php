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
            $table->decimal('dr_share_pct', 5, 2)->default(50)->after('aggregator_net_x');
            $table->decimal('aj_share_pct', 5, 2)->default(30)->after('dr_share_pct');
            $table->decimal('tj_share_pct', 5, 2)->default(20)->after('aj_share_pct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vas_revenues', function (Blueprint $table) {
            $table->dropColumn(['dr_share_pct', 'aj_share_pct', 'tj_share_pct']);
        });
    }
};
