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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer('book_id');
            $table->integer('user_id');
            $table->string('student_name');
            $table->string('student_id_num');
            $table->string('status')->default('PENDING');
            $table->timestamp('req_date')->useCurrent();
            $table->datetime('action_date')->nullable();
            $table->datetime('return_date')->nullable();
            $table->decimal('fine', 10, 2)->default(0.00);
            $table->boolean('fine_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
