<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::query()->latest('id')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::query()->create($this->validatedData($request));

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product): ProductResource
    {
        $product->update($this->validatedData($request, updating: true));

        return new ProductResource($product->refresh());
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }

    /**
     * @return array{name?: string, description?: string|null, price?: numeric, stock?: int}
     */
    private function validatedData(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => [$required, 'numeric', 'min:0', 'max:9999999999.99'],
            'stock' => [$required, 'integer', 'min:0'],
        ]);
    }
}
