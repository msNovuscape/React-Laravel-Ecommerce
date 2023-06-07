<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::create($request->only('title','image','price','description'));
        event(new ProductUpdatedEvent);
        return response($product, Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->only('title','image','description','price'));
        event(new ProductUpdatedEvent);
        return response($product, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        event(new ProductUpdatedEvent);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function frontend()
    {
        if($products = Cache::get('products_frontend')){
            return $products;
        }

        $products = Product::all();
        Cache::set('products_frontend',$products,30*60);
        return $products;
    }

    public function backend(Request $request){

        $page = $request->input('page',1);
        $products =  Cache::remember('products_backend', 30*60, fn() => Product::all());
        if($s = $request->input('s')){
            $products = $products
            ->filter(fn(Product $product) => Str::contains($product->title, $s) || Str::contains($product->description, $s)
            );
        }
        if($request->input('sort') === 'asc'){
            $products = $products->sortBy([
                fn($a, $b) => $a['price'] <=> $b['price']
            ]);
        }
        elseif($request->input('sort') === 'desc'){
            $products = $products->sortBy([
                fn($a, $b) => $b['price'] <=> $a['price']
            ]);
        }
        $total = $products->count();

        return[
            'data' => $products->forPage($page, 9)->values(),
            'meta' => [
                'total' => $products->count(),
                'page' => $page,
                'last_page' => ceil($total/9)
            ]
            ];
    }
}
