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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string("category");
            $table->decimal("rating", 2,1);
            $table->string("status");

            $table->boolean('reviewTypeProbationary3');
            $table->boolean('reviewTypeProbationary5');

            $table->boolean('reviewTypeRegularQ1');
            $table->boolean('reviewTypeRegularQ2');
            $table->boolean('reviewTypeRegularQ3');
            $table->boolean('reviewTypeRegularQ4');

            $table->boolean('reviewTypeOthersImprovement');

            $table->string("reviewTypeOthersCustom");

            $table->integer("jobKnowledgeScore1");
            $table->integer("jobKnowledgeScore2");
            $table->integer("jobKnowledgeScore3");

            $table->string("jobKnowledgeComments1");
            $table->string("jobKnowledgeComments2");
            $table->string("jobKnowledgeComments3");

            $table->integer("qualityOfWorkScore1");
            $table->integer("qualityOfWorkScore2");
            $table->integer("qualityOfWorkScore3");
            $table->integer("qualityOfWorkScore4");
            $table->integer("qualityOfWorkScore5");

            $table->string("qualityOfWorkComments1");
            $table->string("qualityOfWorkComments2");
            $table->string("qualityOfWorkComments3");
            $table->string("qualityOfWorkComments4");
            $table->string("qualityOfWorkComments5");

            $table->integer("adaptabilityScore1");
            $table->integer("adaptabilityScore2");
            $table->integer("adaptabilityScore3");

            $table->string("adaptabilityComments1");
            $table->string("adaptabilityComments2");
            $table->string("adaptabilityComments3");

            $table->integer("teamworkScore1");
            $table->integer("teamworkScore2");
            $table->integer("teamworkScore3");

            $table->string("teamworkComments1");
            $table->string("teamworkComments2");
            $table->string("teamworkComments3");

            $table->integer("reliabilityScore1");
            $table->integer("reliabilityScore2");
            $table->integer("reliabilityScore3");
            $table->integer("reliabilityScore4");

            $table->string("reliabilityComments1");
            $table->string("reliabilityComments2");
            $table->string("reliabilityComments3");
            $table->string("reliabilityComments4");

            $table->integer("ethicalScore1");
            $table->integer("ethicalScore2");
            $table->integer("ethicalScore3");
            $table->integer("ethicalScore4");

            $table->string("ethicalExplanation1");
            $table->string("ethicalExplanation2");
            $table->string("ethicalExplanation3");
            $table->string("ethicalExplanation4");

            $table->integer("customerServiceScore1");
            $table->integer("customerServiceScore2");
            $table->integer("customerServiceScore3");
            $table->integer("customerServiceScore4");
            $table->integer("customerServiceScore5");

            $table->string("customerServiceExplanation1");
            $table->string("customerServiceExplanation2");
            $table->string("customerServiceExplanation3");
            $table->string("customerServiceExplanation4");
            $table->string("customerServiceExplanation5");

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
        Schema::dropIfExists('submissions');
    }
};

