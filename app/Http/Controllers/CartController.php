<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(Cart $cart)
    {
        $this->middleware('auth');
    }

public function add_to_cart(Product $product, Request $request)
{
    $user_id = Auth::id();
    $product_id = $product->id;
    $cart = Cart::where('user_id', $user_id)->where('product_id', $product_id)->first();

    if (!$cart) {
        // Create a new cart if not exists
        $cart = Cart::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'amount' => $request->amount
        ]);
    } else {
        // Update existing cart
        $cart->update([
            'amount' => $cart->amount + $request->amount
        ]);
    }

    return Redirect::route('index_product');
}


    public function show_cart()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();
        return view('show_cart', compact('carts'));
    }

    public function update_cart(Cart $cart, Request $request)
    {
        $request->validate([
            'amount' => 'required|gte:1' . $cart->product->stock
        ]);

        $cart->update([
            'amount' => $request->amount
        ]);

        return Redirect::route('show_cart');
    }

    public function delete_cart(Cart $cart)
    {
        $cart->delete();
        return Redirect::back();
    }
}
