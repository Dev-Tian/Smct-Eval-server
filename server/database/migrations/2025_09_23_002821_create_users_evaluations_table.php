<?php

use App\Models\QuarterUsersEvaluation;
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
        Schema::create('users_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(User::class, 'evaluator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(QuarterUsersEvaluation::class, 'quarter_of_submission_id')->nullable()->constrained()->nullOnDelete();
            $table->string("category");
            $table->integer("rating");
            $table->string("status");

            $table->integer('reviewTypeProbationary');

            $table->boolean('reviewTypeOthersImprovement');

            $table->string("reviewTypeOthersCustom");

            $table->string("priorityArea1");
            $table->string("priorityArea2");
            $table->string("priorityArea3");

            $table->string("remarks");

            $table->string("overallComments");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_evaluations');
    }
};
