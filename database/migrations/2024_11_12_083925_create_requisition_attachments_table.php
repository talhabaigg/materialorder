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
        Schema::create('requisition_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')
                  ->constrained('requisitions') // Assumes a 'requisitions' table exists
                  ->onDelete('cascade'); // Automatically delete attachments if requisition is deleted
            $table->string('file_path'); // Path to the stored file
            $table->string('original_file_name'); // Original name of the uploaded file
            $table->string('mime_type')->nullable(); // MIME type of the file (optional)
            $table->unsignedBigInteger('file_size')->nullable(); // File size in bytes (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_attachments');
    }
};
