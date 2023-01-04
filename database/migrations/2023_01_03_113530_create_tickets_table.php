<?php

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\ModelHelpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    use ModelHelpers;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('department_id');
            $table->enum('status', TicketStatus::getAllValues())->default(TicketStatus::UNREAD->value);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->cascadeOnDelete();

            $userTable = $this->getUserTable();
            if ($userTable) {
                $table->foreign("client_id")
                    ->references("id")
                    ->on($userTable)->cascadeOnDelete();
            } else {
                $table->index("client_id");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
