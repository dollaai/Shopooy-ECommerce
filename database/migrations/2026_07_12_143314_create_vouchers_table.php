<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('(UUID())'));
            $table->index('uuid');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('voucher_type', ['discount', 'cashback'])->default('discount');
            $table->enum('voucher_cashback_type', ['percentage', 'fixed']);
            $table->boolean('is_public')->default(false);
            $table->decimal('discount_cashback_value', 12, 2);
            $table->decimal('discount_cashback_max', 12, 2)->nullable();
            $table->decimal('min_transaction', 12, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
