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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('client_name');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->string('priority');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'started_at', 'ended_at', 'budget', 'priority', 'status']);
        });
    }
};
