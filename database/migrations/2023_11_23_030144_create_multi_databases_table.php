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
        Schema::create('multi_databases', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->string('database')->index()->unique();
            $table->string('has_database_name');
            $table->string('port');
            $table->string('username')->index();
            $table->string('password')->nullable()->index();
            $table->string('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multi_databases');
    }
};
