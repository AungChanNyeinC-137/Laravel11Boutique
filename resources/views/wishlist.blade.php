@extends('layouts.app')
@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">
            Wishlist</h2>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="checkout-steps">
            <a href="javascript:void(0)" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Review And Submit Your Order</em>
                </span>
            </a>
        </div>
        <div class="shopping-cart">
            @if($items->count() > 0)
            <div class="cart-table__wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th></th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr>
                            <td>
                                <div class="shopping-cart__product-item">
                                    <img loading="lazy" src="{{asset('uploads/products/thumbnails')}}/{{$item->model->image}}"
                                        width="120" height="120" alt="{{$item->name}}" />
                                </div>
                            </td>
                            <td>
                                <div class="shopping-cart__product-item__detail">
                                    <h4>{{$item->name}}</h4>
                                    <ul class="shopping-cart__product-item__options">
                                        <li>Color: Yellow</li>
                                        <li>Size: L</li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <span class="shopping-cart__product-price">{{$item->price}}Ks</span>
                            </td>
                            <td>
                                {{$item->qty}}
                            </td>
                            <td>
                            <div class="d-flex align-items-center">
                                <form action="{{route('wishlist.move.to.cart',['row_id' => $item->rowId])}}" 
                                class="px-2" method="post" id="move-to-cart-item-{{$item->id}}">
                                    @csrf
                                    <a href="javascript:void(0)" title="Move to Cart" class="move-to-cart" onclick="document.getElementById('move-to-cart-item-{{$item->id}}').submit(); ">
                                        <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <use href="#icon_cart" />
                                        </svg>
                                    </a>
                                </form>
                                <form action="{{route('wishlist.item.remove',['row_id' => $item->rowId])}}" 
                                class="px-2" method="post" id="remove-cart-item-{{$item->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <a href="javascript:void(0)" title="delete" class="remove-cart" onclick="document.getElementById('remove-cart-item-{{$item->id}}').submit(); ">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                            <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                        </svg>
                                    </a>
                                </form>
                            </div>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="cart-table-footer">
                    <!-- <form action="#" class="position-relative bg-body">
                        <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code">
                        <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
                            value="APPLY COUPON">
                    </form> -->
                    <form action="{{route('wishlist.item.clear')}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-light">CLEAR WISHLIST</button>
                    </form>
                </div>
            </div>
            <div class="shopping-cart__totals-wrapper">
                <div class="sticky-content">
                    <div class="shopping-cart__totals">
                        <h3>Cart Totals</h3>
                        <table class="cart-totals">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>$1300</td>
                                </tr>
                                <tr>
                                    <th>Shipping</th>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input form-check-input_fill" type="checkbox" value=""
                                                id="free_shipping">
                                            <label class="form-check-label" for="free_shipping">Free shipping</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input form-check-input_fill" type="checkbox" value="" id="flat_rate">
                                            <label class="form-check-label" for="flat_rate">Flat rate: $49</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input form-check-input_fill" type="checkbox" value=""
                                                id="local_pickup">
                                            <label class="form-check-label" for="local_pickup">Local pickup: $8</label>
                                        </div>
                                        <div>Shipping to AL.</div>
                                        <div>
                                            <a href="#" class="menu-link menu-link_us-s">CHANGE ADDRESS</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>VAT</th>
                                    <td>$19</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>$1319</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mobile_fixed-btn_wrapper">
                        <div class="button-wrapper container">
                            <button class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-md-12 text-center pt-5 bp-5">
                    <p>No wishlist yet!</p>
                    <a href="{{route('shop.index')}}" class="btn btn-info">Shop Now</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</main>
@endsection