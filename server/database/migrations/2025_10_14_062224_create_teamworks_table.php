<?php

use App\Models\UsersEvaluaion;
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
        Schema::create('teamworks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UsersEvaluaion::class, 'users_evalution_id')->constrained()->cascadeOnDelete();
            $table->integer('question_number');
            $table->integer('score');
            $table->string('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teamworks');
    }
};
