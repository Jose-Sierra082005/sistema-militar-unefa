<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_completions', function (Blueprint $table) {
            $table->unsignedSmallInteger('xp_earned')->default(0)->after('lesson_id');
            $table->unsignedTinyInteger('accuracy_percent')->default(0)->after('xp_earned');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_completions', function (Blueprint $table) {
            $table->dropColumn(['xp_earned', 'accuracy_percent']);
        });
    }
};
