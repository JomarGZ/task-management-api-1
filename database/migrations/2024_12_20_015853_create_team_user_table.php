<?php

use App\Enums\Role;
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
        Schema::create('team_user', function (Blueprint $table) {
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'member_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(model: Team::class)->constrained()->cascadeOnDelete();
            $table->string('role')->default(Role::MEMBER->value);
            $table->timestamps();

            $table->unique(['tenant_id', 'member_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
