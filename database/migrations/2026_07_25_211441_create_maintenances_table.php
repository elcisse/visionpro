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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engin_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('cout', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('statut')->default('planifiee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
