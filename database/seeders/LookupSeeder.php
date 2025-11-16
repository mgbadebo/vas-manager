<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aggregators
        DB::table('aggregators')->insert([
            ['name' => 'VAS2Nets', 'short_code' => 'V2N', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Content Connect', 'short_code' => 'CC', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // MNOs
        DB::table('mnos')->insert([
            ['name' => 'MTN', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Airtel', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Glo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '9mobile', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Services
        DB::table('services')->insert([
            ['name' => 'Lottery', 'type' => 'VAS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Games', 'type' => 'VAS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Podcast', 'type' => 'Content', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sportsbetting', 'type' => 'Gaming', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Operational Categories
        DB::table('operational_categories')->insert([
            ['name' => 'Salaries', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office Rent', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Equipment', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IT Infrastructure', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Winners Reward Vendors', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SMS Traffic Vendors', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jackpot Payments', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technical Support Vendors', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Brokers', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Mandatory Expense Types
        DB::table('mandatory_expense_types')->insert([
            ['name' => 'VAT', 'rule_type' => 'Percent_of_X', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AOL', 'rule_type' => 'Percent_of_X', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NLRC', 'rule_type' => 'Percent_of_X', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Taxes', 'rule_type' => 'Percent_of_X', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

