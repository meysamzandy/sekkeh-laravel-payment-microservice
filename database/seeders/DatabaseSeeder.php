<?php

namespace Database\Seeders;

use App\Models\ForceGateway;
use App\Models\TransactionLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        TransactionLog::factory(20)->create();
        ForceGateway::factory(2)->create();
    }
}
