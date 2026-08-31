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
        // Covers the three shapes the home page asks for: the full active brand
        // list ordered by `order`, that same list narrowed to one category, and
        // the featured strip.
        Schema::table('p_p_o_b_brands', function (Blueprint $table) {
            $table->index(['status', 'order']);
            $table->index(['status', 'p_p_o_b_category_id', 'order']);
            $table->index(['featured', 'status', 'order']);
        });

        Schema::table('p_p_o_b_categories', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->index(['status', 'order']);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['status', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_p_o_b_brands', function (Blueprint $table) {
            $table->dropIndex(['status', 'order']);
            $table->dropIndex(['status', 'p_p_o_b_category_id', 'order']);
            $table->dropIndex(['featured', 'status', 'order']);
        });

        Schema::table('p_p_o_b_categories', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->dropIndex(['status', 'order']);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['status', 'order']);
        });
    }
};
