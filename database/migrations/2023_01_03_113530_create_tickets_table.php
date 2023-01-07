<?php

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\ModelHelpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    use ModelHelpers;

    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            if ($this->isTitleRequire()) {
                $table->string('title');
            }
            $table->foreignId('client_id');
            $table->foreignId('department_id')
                ->references('id')
                ->on('departments')
                ->cascadeOnDelete();
            $table->enum('status', TicketStatus::getAllValues())->default(TicketStatus::UNREAD->value);
            $table->timestamps();

            $userTable = $this->getUserTable();
            if ($userTable) {
                $table->foreign('client_id')
                    ->references('id')
                    ->on($userTable)->cascadeOnDelete();
            } else {
                $table->index('client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
