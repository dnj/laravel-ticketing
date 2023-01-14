<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tickets_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')
                ->nullable()
                ->references('id')
                ->on('tickets_messages')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('file');
            $table->string('mime');
            $table->unsignedInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_attachments');
    }
};
