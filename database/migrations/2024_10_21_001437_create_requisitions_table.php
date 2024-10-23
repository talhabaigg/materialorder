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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->date('date_required');
            $table->foreignId('supplier_id');  // supplier reference
            $table->foreignId('project_id');   // project reference
            $table->string('site_reference');
            $table->string('delivery_contact');
            $table->string('pickup_by');
            $table->foreignId('requested_by');  // user who requested
            $table->string('deliver_to');
            $table->text('notes')->nullable();
            $table->boolean('is_processed')->default(false); // Default to false
            $table->dateTime('processed_at')->nullable();    // Nullable for when it's not processed yet
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
