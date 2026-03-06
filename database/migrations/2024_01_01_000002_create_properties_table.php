<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Title généré automatiquement
            $table->string('title');

            // Type de bien
            $table->enum('type', [
                'appartement',
                'villa',
                'terrain',
                'bureau',
                'commerce',
                'maison',
                'studio',
            ]);

            // Détails
            $table->unsignedSmallInteger('rooms')->nullable();
            $table->decimal('surface', 10, 2)->nullable();   // en m²
            $table->decimal('price', 15, 2);                 // en DA ou devise
            $table->string('city');
            $table->string('address')->nullable();
            $table->text('description')->nullable();

            // Statut
            $table->enum('status', ['disponible', 'vendu', 'location'])->default('disponible');

            // Publication
            $table->boolean('is_published')->default(false);

            // Méta
            $table->timestamps();
            $table->softDeletes();

            // Index pour la recherche / filtres
            $table->index(['city', 'type', 'status']);
            $table->index('price');
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
