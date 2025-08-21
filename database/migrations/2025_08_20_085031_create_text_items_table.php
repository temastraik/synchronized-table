<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextItemsTable extends Migration
{
    public function up()
    {
        Schema::create('text_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['Allowed', 'Prohibited'])->default('Prohibited');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('text_items');
    }
}