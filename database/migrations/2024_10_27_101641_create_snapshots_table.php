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
        Schema::create('snapshots', function (Blueprint $table) {
            $table->id();
            $table->integer('total_year_index');
            $table->integer('total_bulletin_index');
            $table->integer('total_bulletin_article');
            $table->integer('missing_year_index');
            $table->integer('missing_bulletin_index');
            $table->integer('missing_bulletin_article');
            $table->integer('disallowed_year_index');
            $table->integer('disallowed_bulletin_index');
            $table->integer('disallowed_bulletin_article');
            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapshots');
    }
};
