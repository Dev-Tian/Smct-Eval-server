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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("position_id")->constrained()->onDelete("cascade");
            $table->foreignId("branch_id")->constrained()->onDelete("cascade");
            $table->foreignId("department_id")->constrained()->onDelete("cascade");
            $table->string("username");
            $table->string("fname");
            $table->string("lname");
            $table->string("email");
            $table->string("contact");
            $table->string("password");
            $table->boolean('is_active')->default(false);
            $table->date("date_hired");
            $table->date("employeeSignatureDate");
            $table->longText("signature");
            $table->string("avatar")->nullable();
            $table->string('bio')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
