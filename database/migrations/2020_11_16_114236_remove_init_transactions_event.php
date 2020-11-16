<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveInitTransactionsEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (App::environment() !== 'testing') {
            DB::unprepared(
                'CREATE EVENT `init_remove` ON SCHEDULE EVERY 1 DAY STARTS "2020-10-17 03:00:00.000000" ON COMPLETION NOT PRESERVE ENABLE DO
            DELETE FROM `transaction_logs` WHERE `status` = "init" AND transaction_logs.created_at < NOW() - INTERVAL 7 DAY'
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (App::environment() !== 'testing') {
            DB::unprepared('DROP EVENT IF EXISTS init_remove');
        }

    }
}
