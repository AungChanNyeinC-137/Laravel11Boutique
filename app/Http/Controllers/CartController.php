<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart',compact('items'));
    }

    public function addToCart(Request $request)
    {

        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');

        return redirect()->back()->with('success','Product added to cart successfully!');
    }

    public function increaseCartQuantity($row_id) {
        $product = Cart::instance('cart')->get($row_id);
        $quantity = $product->qty + 1;
        Cart::instance('cart')->update($row_id, $quantity);
        return redirect()->back();
    }

    public function decreaseCartQuantity($row_id) {
        $product = Cart::instance('cart')->get($row_id);
        $quantity = $product->qty - 1;
        Cart::instance('cart')->update($row_id,$quantity);
        return redirect()->back();
    }

    public function removeFromCart($row_id) {
        Cart::instance('cart')->remove($row_id);
        return redirect()->back()->with('success','Item removed from cart successfully');
    }

    public function emptyCart() {
        Cart::instance('cart')->destroy();
        return redirect()->back()->with('success','Cart cleared successfully');
    }

    public function checkOut() {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
       // dd($address);
        return view('checkout',compact('address'));
    }
    public function placeOrder(Request $request) {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id',$user_id)->where('isdefault',true)->first();
        if (!$address) {        
            try {
                $request->validate([
                    'name' => 'required|max:100',
                    'phone' => 'required|numeric|digits:11',
                    'locality' => 'required|numeric|digits:6',
                    'address' => 'required',
                   // 'city' => 'required|string',
                    'state' => 'required|string',
                    'landmark' => 'required',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
//
            }
        
            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->locality = $request->locality;
            $address->address = $request->address;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->landmark = $request->landmark;
            $address->country = 'Myanmar';
            $address->zip = $request->zip;
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
            }
        $this->setAmountForCheckout();
         
            $order = new Order();
              $order->user_id = $user_id;
              $order->subtotal = floatval(Session::get('checkout')['subtotal']);
              $order->discount = Session::get('checkout')['discount'];
              $order->tax = floatval(Session::get('checkout')['tax']);
              $order->total = floatval(Session::get('checkout')['total']);
              $order->name = $address->name;
              $order->locality = $address->locality;
              $order->address = $address->address;
              $order->city = $address->city;
              $order->state = $address->state;
              $order->country = $address->country;
              $order->landmark = $address->landmark;
              $order->zip = $address->zip;
              $order->save();
    
              foreach(Cart::instance('cart')->content() as $item) {
                $order_item = new OrderItem();
                $order_item->product_id = $item->id;
                $order_item->order_id = $order->id;
                $order_item->price = $item->price;
                $order_item->quantity = $item->qty;
                $order_item->save();
              }
    
            if($request->mode == 'cod') {
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->order_id = $order->id;
                $transaction->mode = $request->mode;
                $transaction->status = "pending";
                $transaction->save();
            } elseif ($request->mode == 'card') {
                    //
            } elseif($request->mode == 'paypal') {
                    //
            }
              Cart::instance('cart')->destroy();
              Session::forget('coupon');
              Session::forget('checkout');
              Session::forget('discounts');           
              Session::put('order_id',$order->id);  
              return redirect()->route('cart.order.confirm');
        
    }
    

    public function setAmountForCheckout() {
        if(!Cart::instance('cart')->content()->count() > 0 )
        {
            Session::forget('checkout');
            return;
        }
        if(Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
            ]);

        } else {
            Session::put('checkout',[
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }

    public function confirmOrder() {
        if(Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view ('order-confirm',compact('order'));
        }
        return redirect()->route('cart.index');
    }

}
