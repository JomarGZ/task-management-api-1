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
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('priority_level');
            $table->string('status');
            $table->date('deadline_at')->nullable();
            $table->date('previous_deadline_at')->nullable();
            $table->string('pr_link')->nullable();
            $table->string('issue_link')->nullable();
            $table->string('doc_link')->nullable();
            $table->string('other_link')->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'project_id', 'created_at']);
            $table->index(['project_id', 'priority_level','tenant_id', 'created_at']);
            $table->index(['project_id', 'status', 'tenant_id', 'created_at']);
            $table->index(['tenant_id', 'project_id', 'status']);

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
