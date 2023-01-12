<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('name');
            $table->string('file');
            $table->string('mime');
            $table->unsignedInteger('size');
            $table->timestamps();

            $table->foreign('message_id')
                ->nullable()
                ->references('id')
                ->on('ticket_messages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};