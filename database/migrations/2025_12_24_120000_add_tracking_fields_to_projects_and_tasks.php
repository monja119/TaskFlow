<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('status');
            $table->decimal('risk_score', 5, 2)->nullable()->after('progress');
            $table->timestamp('archived_at')->nullable()->after('end_date');
            $table->softDeletes();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->timestamp('archived_at')->nullable()->after('completed_at');
            $table->unsignedInteger('estimate_minutes')->nullable()->after('archived_at');
            $table->unsignedInteger('actual_minutes')->nullable()->after('estimate_minutes');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'completed_at',
                'archived_at',
                'estimate_minutes',
                'actual_minutes',
            ]);
            $table->dropSoftDeletes();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'progress',
                'risk_score',
                'archived_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
