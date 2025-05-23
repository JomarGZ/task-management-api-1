<?php

use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Team::class)->nullable();
            $table->string('name');
            $table->text('description');
            $table->string('client_name');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->string('priority');
            $table->string('status');
            $table->timestamps();

            $table->index(['Tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
