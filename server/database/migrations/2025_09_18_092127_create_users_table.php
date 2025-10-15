<?php

use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
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
           $table->foreignIdFor(Position::class, 'position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Branch::class, 'branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Department::class, 'department_id')->nullable()->constrained()->nullOnDelete();
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
