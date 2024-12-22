<?php

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Project;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tenant::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'assigned_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('priority_level')->default(PriorityLevel::LOW->value);
            $table->string('status')->default(Statuses::NOT_STARTED->value);
            $table->date('deadline_at')->nullable();
            $table->date('previous_deadline_at')->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
