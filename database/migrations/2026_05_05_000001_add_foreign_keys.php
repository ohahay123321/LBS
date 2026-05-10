<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('author')->constrained('categories')->nullOnDelete();
            }
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->change()->constrained('books')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['book_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};
