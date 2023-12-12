<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        if($carts == null) {
            return Redirect::back();
        }

        $order = Order::create([
            'user_id' => $user_id,
        ]);

        foreach($carts as $cart) {

            $product = Product::find($cart->product_id);

            $product->update([
                'stock' => $product->stock - $cart->amount
            ]);

            Transaction::create([
                'amount' => $cart->amount,
                'order_id' => $order->id,
                'product_id' => $cart->product_id
            ]);

            $cart->delete();
        }

        return Redirect::back();
    }

    public function index_order()
    {
        $orders = Order::all();
        return view('index_order', compact('orders'));
    }

    public function show_order(Order $order)
    {
        return view('show_order', compact('order'));
    }

    public function payment_receipt(Order $order, Request $request)
    {
        $file = $request->file('payment_receipt');
        $order->update([
            'payment_receipt' => $request->payment_receipt,
        ]);
    }
}
