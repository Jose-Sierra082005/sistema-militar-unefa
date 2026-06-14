<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guard_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('military_personnel')->onDelete('cascade');
            $table->string('post');
            $table->string('shift_time');
            $table->date('date');
            $table->string('status')->default('Programado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guard_shifts');
    }
};
