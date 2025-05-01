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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('pr_link')->nullable();
            $table->string('issue_link')->nullable();
            $table->string('doc_link')->nullable();
            $table->string('other_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('pr_link');
            $table->dropColumn('issue_link');
            $table->dropColumn('doc_link');
            $table->dropColumn('other_link');
        });
    }
};
