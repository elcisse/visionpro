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
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chauffeur_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->decimal('heures_travaillees', 5, 2)->default(0);
            $table->boolean('en_panne')->default(false);
            $table->decimal('heures_panne', 5, 2)->default(0);
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->unique(['contrat_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
