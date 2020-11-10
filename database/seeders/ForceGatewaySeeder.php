<?php

namespace Database\Seeders;

use App\Models\ForceGateway;
use Illuminate\Database\Seeder;

class ForceGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ForceGateway::factory(20);
    }
}
