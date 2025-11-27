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
        Schema::create('mandatory_expense_accumulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('mandatory_expense_type_id')->constrained('mandatory_expense_types')->onDelete('cascade');
            $table->integer('year');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->date('moved_to_bank_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['service_id', 'mandatory_expense_type_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
