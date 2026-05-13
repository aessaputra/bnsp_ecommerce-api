<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->select(['id', 'sku', 'slug'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $updates = [];

                    if ($product->sku === null) {
                        $updates['sku'] = sprintf('PRD-%06d', $product->id);
                    }

                    if ($product->slug === null) {
                        $updates['slug'] = 'product-'.$product->id;
                    }

                    if ($updates !== []) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update($updates);
                    }
                }
            });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku', 100)->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku', 100)->nullable()->change();
            $table->string('slug')->nullable()->change();
        });
    }
};
