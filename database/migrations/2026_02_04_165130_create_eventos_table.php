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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['global', 'local'])->default('local');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->foreignIdFor(\App\Models\Group::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(\App\Models\User::class, 'owner_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
