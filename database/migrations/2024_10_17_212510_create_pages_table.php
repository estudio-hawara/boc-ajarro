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
        Schema::create('pages', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('url');

            $table->longText('content')
                ->nullable();

            $table->bigInteger('shared_content_with_page_id')
                ->unsigned()
                ->nullable();

            $table->foreign('shared_content_with_page_id')
                ->references('id')
                ->on('pages');

            $table->timestamp('created_at')
                ->useCurrent();

            $table->index(['name', 'created_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
