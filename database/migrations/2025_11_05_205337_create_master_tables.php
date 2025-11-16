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
        Schema::create('aggregators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_code')->nullable();
            $table->timestamps();
        });

        Schema::create('mnos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('operational_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('expense_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('operational_category_id')
                ->constrained('operational_categories')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('mandatory_expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rule_type', ['Percent_of_X', 'Percent_of_RA', 'Percent_of_RS']);
            $table->timestamps();
        });

        Schema::create('key_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->onDelete('set null');
            $table->decimal('share_percentage', 9, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->foreignId('aggregator_id')
                ->nullable()
                ->constrained('aggregators')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('key_stakeholders');
        Schema::dropIfExists('mandatory_expense_types');
        Schema::dropIfExists('expense_recipients');
        Schema::dropIfExists('operational_categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('mnos');
        Schema::dropIfExists('aggregators');
    }
};
