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
        Schema::create('links', function (Blueprint $table) {

            $table->id();

            $table->string('type')
                ->nullable();

            $table->bigInteger('page_id')
                ->unsigned();

            $table->string('url');

            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('download_started_at')
                ->nullable();

            $table->timestamp('disallowed_at')
                ->nullable();

            $table->index(['page_id', 'type', 'created_at']);
            $table->index(['download_started_at', 'disallowed_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
