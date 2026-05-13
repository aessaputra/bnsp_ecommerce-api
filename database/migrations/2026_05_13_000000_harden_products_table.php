<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku', 100)->nullable()->after('id');
            $table->string('slug')->nullable()->after('name');
            $table->string('status', 20)->default('active')->after('stock')->index();
            $table->softDeletes();
        });

        DB::table('products')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $slug = Str::slug((string) $product->name) ?: 'product';

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'sku' => sprintf('PRD-%06d', $product->id),
                            'slug' => $slug.'-'.$product->id,
                        ]);
                }
            });

        Schema::table('products', function (Blueprint $table): void {
            $table->unique('sku');
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropUnique(['sku']);
            $table->dropUnique(['slug']);
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
            $table->dropColumn(['sku', 'slug', 'status']);
        });
    }
};
