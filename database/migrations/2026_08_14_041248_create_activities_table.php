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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->useCurrent();
            $table->string('title');
            $table->text('descriptions')->nullable();
            $table->text('rules')->nullable();
            $table->text('tools')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, accept, reject
            $table->foreignId('accept_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reject_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reject_reason')->nullable();
            $table->text('dosen_note')->nullable();
            $table->timestamp('reject_at')->nullable();
            $table->timestamp('accept_at')->nullable();
            $table->timestamp('re_submit_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
