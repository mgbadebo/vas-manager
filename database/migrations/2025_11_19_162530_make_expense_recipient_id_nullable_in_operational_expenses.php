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
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN directly, so we need to recreate the table
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Create new table with nullable expense_recipient_id
            DB::statement('
                CREATE TABLE operational_expenses_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    vas_revenue_id INTEGER NOT NULL,
                    operational_category_id INTEGER NOT NULL,
                    expense_recipient_id INTEGER NULL,
                    percentage NUMERIC(9,4) NULL,
                    fixed_amount NUMERIC(18,2) NULL,
                    final_amount NUMERIC(18,2) NOT NULL DEFAULT 0,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (vas_revenue_id) REFERENCES vas_revenues(id) ON DELETE CASCADE,
                    FOREIGN KEY (operational_category_id) REFERENCES operational_categories(id) ON DELETE CASCADE,
                    FOREIGN KEY (expense_recipient_id) REFERENCES expense_recipients(id) ON DELETE CASCADE
                )
            ');
            
            // Copy data
            DB::statement('INSERT INTO operational_expenses_new SELECT * FROM operational_expenses;');
            
            // Drop old table
            DB::statement('DROP TABLE operational_expenses;');
            
            // Rename new table
            DB::statement('ALTER TABLE operational_expenses_new RENAME TO operational_expenses;');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // For MySQL/PostgreSQL, use standard ALTER TABLE
            Schema::table('operational_expenses', function (Blueprint $table) {
                $table->foreignId('expense_recipient_id')
                    ->nullable()
                    ->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // Revert to NOT NULL (but this will fail if there are NULL values)
            DB::statement('PRAGMA foreign_keys=off;');
            
            DB::statement('
                CREATE TABLE operational_expenses_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    vas_revenue_id INTEGER NOT NULL,
                    operational_category_id INTEGER NOT NULL,
                    expense_recipient_id INTEGER NOT NULL,
                    percentage NUMERIC(9,4) NULL,
                    fixed_amount NUMERIC(18,2) NULL,
                    final_amount NUMERIC(18,2) NOT NULL DEFAULT 0,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (vas_revenue_id) REFERENCES vas_revenues(id) ON DELETE CASCADE,
                    FOREIGN KEY (operational_category_id) REFERENCES operational_categories(id) ON DELETE CASCADE,
                    FOREIGN KEY (expense_recipient_id) REFERENCES expense_recipients(id) ON DELETE CASCADE
                )
            ');
            
            // Copy data (excluding rows with NULL expense_recipient_id)
            DB::statement('INSERT INTO operational_expenses_new SELECT * FROM operational_expenses WHERE expense_recipient_id IS NOT NULL;');
            
            DB::statement('DROP TABLE operational_expenses;');
            DB::statement('ALTER TABLE operational_expenses_new RENAME TO operational_expenses;');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('operational_expenses', function (Blueprint $table) {
                $table->foreignId('expense_recipient_id')
                    ->nullable(false)
                    ->change();
            });
        }
    }
};
