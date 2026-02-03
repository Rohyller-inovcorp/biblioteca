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
        Schema::table('books', function (Blueprint $table) {
            $table->string('google_books_id')->nullable()->after('isbn');
            $table->timestamp('google_books_synced_at')->nullable()->after('google_books_id');
            $table->index('google_books_id');
            $table->string('language', 5)->nullable()->after('google_books_synced_at');
            $table->date('published_at')->nullable()->after('isbn');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['google_books_id']);
            $table->dropColumn(['google_books_id', 'google_books_synced_at']);
        });
    }
};
