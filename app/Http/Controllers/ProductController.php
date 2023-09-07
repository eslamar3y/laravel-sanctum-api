<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
        $img = $request->file('image');
        $img_name = time() . '.' . $img->extension();
        $img->move(public_path('images'), $img_name);

        // validate the request...
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        // Product::create($request->all());
        Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $img_name,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        // get the image
        $img = $request->file('image');


        // update all
        $product->update($request->all());

        // update image
        if ($img) {
            $img_name = time() . '.' . $img->extension();
            $img->move(public_path('images'), $img_name);
            $product->image = $img_name;
        }


        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::destroy($id);
        return response()->json([
            'message' => 'Product Deleted successfully',
        ], 200);
    }

    /**
     * search for a name
     */
    public function search(string $name)
    {
        return Product::where('name', 'Like', '%' . $name . '%')->get();
    }

    /**
     * search for a category
     */
    public function category(string $category)
    {
        return Product::where('category', 'Like', '%' . $category . '%')->get();
    }

    /**
     * filter for a price
     */
    public function filter(string $smaller, string $bigger)
    {
        return Product::where('price', '>=', $smaller)->where('price', '<=', $bigger)->get();
    }
}
