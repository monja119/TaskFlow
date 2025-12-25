<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('tasks')
            ->where('status', 'pending')
            ->update(['status' => 'todo']);
    }

    public function down(): void
    {
        DB::table('tasks')
            ->where('status', 'todo')
            ->update(['status' => 'pending']);
    }
};
