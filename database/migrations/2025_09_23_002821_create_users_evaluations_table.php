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
        Schema::create('users_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'employee_id')->nullable()->constrained()->nullOnDelete();
            $table->date("hireDate");
            $table->foreignIdFor(User::class, 'evaluator_id')->nullable()->constrained()->nullOnDelete();

            $table->string("category");
            $table->decimal('rating', 3, 2);
            $table->enum('status', ['pending', 'completed'])->default('pending');

            $table->date('coverageFrom');
            $table->date('coverageTo');
            $table->integer('reviewTypeProbationary')->nullable();

            $table->string('reviewTypeRegular')->nullable();
            $table->boolean('reviewTypeOthersImprovement')->nullable();
            $table->string("reviewTypeOthersCustom")->nullable();

            $table->string("priorityArea1")->nullable();
            $table->string("priorityArea2")->nullable();
            $table->string("priorityArea3")->nullable();

            $table->string("remarks")->nullable();

            $table->string("overallComments")->nullable();
            $table->date('evaluatorApprovedAt')->nullable();
            $table->date('employeeApprovedAt')->nullable();

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
