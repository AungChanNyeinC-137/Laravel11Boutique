@extends('layouts.app')
@section('content')
<style>
  .text-success {
    color: rgb(11, 161, 14) !important;
  }
</style>
<main class="pt-90">
  <div class="mb-4 pb-4"></div>
  <section class="shop-checkout container">
    <h2 class="page-title">Cart</h2>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
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
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
            <tr>
              <td>
                <div class="shopping-cart__product-item">
                  <img loading="lazy" src="{{asset('uploads/products')}}/{{$item->model->image}}" width="120" height="120" alt="{{$item->name}}" />
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
                <div class="qty-control position-relative">
                  <input type="number" name="quantity" value="{{$item->qty}}" min="1" class="qty-control__number text-center">
                  <form action="{{route('cart.qty.decrease',['rowID'=>$item->rowId])}}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="qty-control__reduce">-</div>
                  </form>
                  <form action="{{route('cart.qty.increase',['rowID'=>$item->rowId])}}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="qty-control__increase">+</div>
                  </form>
                </div>
              </td>
              <td>
                <span class="shopping-cart__subtotal">{{$item->subTotal()}}Ks</span>
              </td>
              <td>
                <form action="{{route('cart.item.remove',['row_id'=>$item->rowId])}}" method="post" id="remove-cart-item-{{$item->id}}">
                  @csrf
                  @method('DELETE')
                  <a href="javascript:void(0)" onclick="document.getElementById('remove-cart-item-{{$item->id}}').submit();" class="remove-cart">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                      <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                    </svg>
                  </a>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="cart-table-footer">
          @if (!Session::has('coupon'))
          <form action="{{route('admin.coupon.apply')}}" class="position-relative bg-body" method="post">
            @csrf
            <input class="form-control" type="text" name="coupon_code"
              placeholder="Coupon Code">
            <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
              value="APPLY COUPON">
          </form>
          @else
          <form action="{{route('admin.coupon.remove')}}" class="position-relative bg-body" method="post">
            @csrf
            @method('delete')
            <input class="form-control" type="text" name="coupon_code" value="@if (Session::has('coupon'))
            {{Session::get('coupon')['code']}} Applied!@endif"
              placeholder="Coupon Code">
            <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
              value="REMOVE COUPON">
          </form>
          @endif
          <form action="{{route('cart.item.clear')}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-light">CLEAR CART</button>
          </form>
        </div>
        <div>
          @if (Session::has('success'))
          <p class="text-success">{{Session::get('success')}}</p>
          @elseif(Session::has('error'))
          <p class="text-danger">{{Session::get('error')}}</p>
          @endif
        </div>
      </div>
      <div class="shopping-cart__totals-wrapper">
        <div class="sticky-content">
          <div class="shopping-cart__totals">
            <h3>Cart Totals</h3>
            @if (Session::has('discounts'))
            <table class="cart-totals">
              <tbody>
                <tr>
                  <th>Subtotal</th>
                  <td>{{Cart::instance('cart')->subtotal()}}Ks</td>
                </tr>
                  <th>Discount <button class="btn btn-light btn-sm">{{Session::get('coupon')['code']}}</button></th>
                  <td>{{Session::get('discounts')['discount']}}Ks</td>
                </tr>
                  <th>Subtotal</th>
                  <td>{{Session::get('discounts')['subtotal']}}Ks</td>
                </tr>
                <tr>
                  <th>Shipping</th>
                  <td>
                    FREE
                    <!-- <div>Shipping to AL.</div> -->
                    <div>
                      <a href="#" class="menu-link menu-link_us-s">CHANGE ADDRESS</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Value Added Tax</th>
                  <td>{{Session::get('discounts')['tax']}}</td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td>{{Session::get('discounts')['total']}}Ks</td>
                </tr>
              </tbody>
            </table>
          @else
          <table class="cart-totals">
              <tbody>
                <tr>
                  <th>Subtotal</th>
                  <td>{{Cart::instance('cart')->subtotal()}}Ks</td>
                </tr>
                <tr>
                  <th>Shipping</th>
                  <td>
                    FREE
                    <!-- <div>Shipping to AL.</div> -->
                    <div>
                      <a href="#" class="menu-link menu-link_us-s">CHANGE ADDRESS</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Value Added Tax</th>
                  <td>{{Cart::instance('cart')->tax()}}</td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td>{{Cart::instance('cart')->total()}}Ks</td>
                </tr>
              </tbody>
            </table>
            @endif
 
          </div>
          <div class="mobile_fixed-btn_wrapper">
            <div class="button-wrapper container">
              <a href="{{route('cart.checkout')}}" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
            </div>
          </div>
        </div>
      </div>
      @else
      <div class="row">
        <div class="col-md-12 text-center pt-5 bp-5">
          <p>No Item found in your cart!</p>
          <a href="{{route('shop.index')}}" class="btn btn-info">Shop Now</a>
        </div>
      </div>
      @endif
    </div>
  </section>
</main>
@endsection
@push('scripts')
<script>
  $(
    function() {
      $(".qty-control__increase").on('click', function() {
        $(this).closest('form').submit();
      })
      $(".qty-control__reduce").on('click', function() {
        $(this).closest('form').submit();
      })
    }
  )
</script>

@endpush