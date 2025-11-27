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
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vas_revenue_id')
                ->constrained('vas_revenues')
                ->onDelete('cascade');
            $table->enum('payment_type', [
                'partner_share_dr',
                'partner_share_aj',
                'partner_share_tj',
                'operational_expense',
                'mandatory_expense'
            ]);
            $table->string('recipient_name'); // Name of who gets paid
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['paid', 'not_paid'])->default('not_paid');
            $table->text('comment')->nullable();
            // References to source data
            $table->foreignId('operational_expense_id')->nullable()->constrained('operational_expenses')->onDelete('cascade');
            $table->foreignId('mandatory_expense_id')->nullable()->constrained('mandatory_expenses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
