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
            // SQLite doesn't support adding foreign keys easily, so we recreate the table
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Create new table with service_type_id
            DB::statement('
                CREATE TABLE services_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    type TEXT NULL,
                    service_type_id INTEGER NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (service_type_id) REFERENCES service_types(id) ON DELETE SET NULL
                )
            ');
            
            // Copy data
            DB::statement('INSERT INTO services_new (id, name, type, created_at, updated_at) SELECT id, name, type, created_at, updated_at FROM services;');
            
            // Drop old table
            DB::statement('DROP TABLE services;');
            
            // Rename new table
            DB::statement('ALTER TABLE services_new RENAME TO services;');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('services', function (Blueprint $table) {
                $table->foreignId('service_type_id')
                    ->nullable()
                    ->after('type')
                    ->constrained('service_types')
                    ->onDelete('set null');
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
            DB::statement('PRAGMA foreign_keys=off;');
            
            DB::statement('
                CREATE TABLE services_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    type TEXT NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL
                )
            ');
            
            DB::statement('INSERT INTO services_new (id, name, type, created_at, updated_at) SELECT id, name, type, created_at, updated_at FROM services;');
            
            DB::statement('DROP TABLE services;');
            DB::statement('ALTER TABLE services_new RENAME TO services;');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign(['service_type_id']);
                $table->dropColumn('service_type_id');
            });
        }
    }
};
