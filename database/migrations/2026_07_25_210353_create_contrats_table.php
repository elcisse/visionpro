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
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('engin_id')->constrained()->restrictOnDelete();
            $table->string('numero')->unique();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('lieu_chantier')->nullable();
            $table->decimal('tarif_horaire', 12, 2);
            $table->string('statut')->default('en_cours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
