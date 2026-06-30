<?php

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
        Schema::create('assign_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'evaluator_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'approver_id')->constrained()->cascadeOnDelete();
            $table->integer('sequence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_approvers');
    }
};
