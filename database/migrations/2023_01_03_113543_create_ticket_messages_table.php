<?php

use dnj\Ticket\ModelHelpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    use ModelHelpers;

    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->foreignId('ticket_id')
                ->references('id')
                ->on('tickets')
                ->cascadeOnDelete();

            $table->text('message');
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();

            $userTable = $this->getUserTable();
            if ($userTable) {
                $table->foreign("user_id")
                    ->references("id")
                    ->on($userTable)->cascadeOnDelete();
            } else {
                $table->index("user_id");
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
