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
        Schema::create('vas_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained('services')
                ->onDelete('cascade');
            $table->foreignId('mno_id')
                ->constrained('mnos')
                ->onDelete('cascade');
            $table->foreignId('aggregator_id')
                ->constrained('aggregators')
                ->onDelete('cascade');
            $table->date('payment_date')->nullable();
            $table->string('period_label')->nullable();
            $table->decimal('gross_revenue_a', 18, 2)->default(0);
            $table->decimal('aggregator_percentage', 9, 4)->default(0);
            $table->decimal('aggregator_net_x', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('mandatory_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vas_revenue_id')
                ->constrained('vas_revenues')
                ->onDelete('cascade');
            $table->foreignId('mandatory_expense_type_id')
                ->nullable()
                ->constrained('mandatory_expense_types')
                ->onDelete('cascade');
            $table->foreignId('key_stakeholder_id')
                ->nullable()
                ->constrained('key_stakeholders')
                ->onDelete('cascade');
            $table->decimal('percentage', 9, 4)->nullable();
            $table->decimal('fixed_amount', 18, 2)->nullable();
            $table->decimal('final_amount', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vas_revenue_id')
                ->constrained('vas_revenues')
                ->onDelete('cascade');
            $table->foreignId('operational_category_id')
                ->constrained('operational_categories')
                ->onDelete('cascade');
            $table->foreignId('expense_recipient_id')
                ->constrained('expense_recipients')
                ->onDelete('cascade');
            $table->decimal('percentage', 9, 4)->nullable();
            $table->decimal('fixed_amount', 18, 2)->nullable();
            $table->decimal('final_amount', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('partner_share_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vas_revenue_id')
                ->constrained('vas_revenues')
                ->onDelete('cascade');
            $table->decimal('mandatory_total_me', 18, 2)->default(0);
            $table->decimal('ra_after_mandatory', 18, 2)->default(0);
            $table->decimal('operational_total_oe', 18, 2)->default(0);
            $table->decimal('rs_share_pool', 18, 2)->default(0);
            $table->decimal('dr_share_50', 18, 2)->default(0);
            $table->decimal('aj_share_30', 18, 2)->default(0);
            $table->decimal('tj_share_20', 18, 2)->default(0);
            $table->timestamp('computed_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_share_summaries');
        Schema::dropIfExists('operational_expenses');
        Schema::dropIfExists('mandatory_expenses');
        Schema::dropIfExists('vas_revenues');
    }
};
