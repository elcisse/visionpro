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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->restrictOnDelete();
            $table->string('numero')->unique();
            $table->string('type')->default('periodique');
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->decimal('heures_facturees', 8, 2)->default(0);
            $table->decimal('montant', 12, 2);
            $table->date('date_echeance')->nullable();
            $table->string('statut')->default('brouillon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
