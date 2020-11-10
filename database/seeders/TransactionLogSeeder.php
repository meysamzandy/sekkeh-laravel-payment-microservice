<?php

namespace Database\Seeders;

use App\Models\TransactionLog;
use Illuminate\Database\Seeder;

class TransactionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionLog::factory(20);
    }
}
