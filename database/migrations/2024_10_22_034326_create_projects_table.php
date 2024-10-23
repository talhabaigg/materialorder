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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ensure project names are unique
            $table->string('coordinates')->nullable(); // Nullable if it's optional
            $table->string('site_reference')->nullable(); // Nullable if it's optional
            $table->string('delivery_contact')->nullable(); // Nullable if contact is optional
            $table->string('pickup_by')->nullable(); // Nullable if pickup is optional
            $table->string('requested_by'); // This could be a foreign key to a user, if needed
            $table->string('deliver_to')->nullable(); // Nullable if delivery is optional
            $table->string('notes')->nullable();
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
