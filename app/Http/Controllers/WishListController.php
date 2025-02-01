<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishListController extends Controller
{
    public function index() {
        $items = Cart::instance('wishlist')->content();
        return view('wishlist', compact('items'));
    }

    public function addToWishList(Request $request) {
        Cart::instance('wishlist')
        ->add($request->id, $request->name, $request->quantity,$request->price)
        ->associate('App\Models\Product');
        return redirect()->back()->with('success','Item successfully added to wishList!');
    }

    public function removeFromWishList($row_id) {
        Cart::instance('wishlist')->remove($row_id);
        return redirect()->back()->with('success','Item successfully removed from wishList!');
    }

    public function emptyWishLList() {
        Cart::instance('wishlist')->destroy();
        return redirect()->back()->with('success','WishList successfully cleared!');
    }

    public function moveFromWishlistToCart($row_id) {
        $item = Cart::instance('wishlist')->get($row_id);
        Cart::instance('wishlist')->remove($row_id);
        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)->associate('App\Models\Product');
        return redirect()->back()->with('success','Item successfully moved to cart!');
    }

}
