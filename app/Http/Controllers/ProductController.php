<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Product as ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'location' => 'required',
            'price' => 'required',
            'user_id' => 'required',
            'available' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'location' => 'required',
            'price' => 'required',
            'user_id' => 'required',
            'available' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product->name = $input['name'];
        $product->location = $input['location'];
        $product->price = $input['price'];
        $product->user_id = $input['user_id'];
        $product->available = $input['available'];
        $product->save();

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }

    /**
     * Check Avail Product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function check($id, $id_users, Product $product)
    {
        $product = Product::where('available', 1)->where('id', $id);
        $users_points = DB::table('users')
            ->where('id', $id_users)
            ->get();
        $update_points = DB::table('users')
            ->where('id', $id_users)
            ->update(
                [
                    'points' => $users_points[0]->points - 10,
                ]
            );
        if (is_null($product)) {
            return $this->sendResponse([],'Product not Avail.');
        } else {
            return $this->sendResponse([], 'Product Avail');
        }
    }
}
