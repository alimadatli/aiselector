<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('selector_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selector_id')->constrained()->onDelete('cascade');
            $table->string('old_selector');
            $table->string('new_selector');
            $table->text('reason');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('selector_changes');
    }
};
