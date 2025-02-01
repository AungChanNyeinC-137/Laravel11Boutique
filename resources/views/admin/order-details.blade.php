@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Order Items</div>
                </li>
            </ul>
        </div>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Details</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr class="bg bg-light">
                        <th>Order No</th>
                        <th>Mobile</th>
                        <th>Zip Code</th>
                        <th>Ordered Date</th>
                        <th>Delivered Date</th>
                        <th>Canceled Date</th>
                        <th>Order Status</th>
                    </tr>
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->phone}}</td>
                        <td>{{$order->zip}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>{{$order->delivered_date}}</td>
                        <td>{{$order->canceled_date}}</td>
                        <td>
                            @if ($order->status == "delivered")
                            <span class="badge bg-success">Delivered</span>
                            @elseif ($order->status == "canceled")
                            <span class="badge bg-danger">Canceled</span>
                            @else
                            <span class="badge bg-warning">Ordered</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Items</h5>
                </div>
            </div>
            <div class="table-responsive">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr class="bg bg-light">
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Options</th>
                            <th class="text-center">Return Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order_items as $order_item)
                        <tr>

                            <td class="pname">
                                <div class="image">
                                    <img src="{{asset('uploads/products/thumbnails')}}/{{$order_item->product->image}}"
                                        alt="{{$order_item->product->name}}" class="image">
                                </div>
                                <div class="name">
                                    <a href="{{route('shop.product.details',['product_slug'=>$order_item->product->slug])}}" target="_blank"
                                        class="body-title-2">{{$order_item->product->name}}</a>
                                </div>
                            </td>
                            <td class="text-center">{{$order_item->product->sale_price}}</td>
                            <td class="text-center">{{$order_item->quantity}}</td>
                            <td class="text-center">{{$order_item->product->SKU}}</td>
                            <td class="text-center">{{$order_item->product->category->name}}</td>
                            <td class="text-center">{{$order_item->product->brand->name}}</td>
                            <td class="text-center"></td>
                            <td class="text-center">No</td>
                            <td class="text-center">
                                <div class="list-icon-function view-icon">
                                    <div class="item eye">
                                        <i class="icon-eye"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{$order_items->links('pagination::bootstrap-5')}}
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Shipping Address</h5>
            <div class="my-account__address-item col-md-6">
                <div class="my-account__address-item__detail">
                    <p>{{$order->name}}</p>
                    <p>{{$order->address}}</p>
                    <p>{{$order->locality}}</p>
                    <p>{{$order->city}},{{$order->country}}</p>
                    <p>{{$order->landmark}}</p>
                    <p>{{$order->zip}}</p>
                    <br>
                    <p>Mobile : {{$order->phone}}1</p>
                </div>
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Transactions</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-transaction">
                    <thead>
                        <tr class="bg bg-light">
                            <th>Subtotal</th>
                            <th>Tax</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$order->subtotal}}Ks</td>
                            <td>{{$order->tax}}Ks</td>
                            <td>{{$order->discount}}Ks</td>
                            <td>{{$order->total}}Ks</td>
                            <td>{{$transaction->mode}}</td>
                            <td>
                                @if ($transaction->status == 'approved' )
                                <span class="badge bg-success">Approved</span>
                                @elseif ($transaction->status == 'declined')
                                <span class="badge bg-danger">Declined</span>
                                @elseif ($transaction->status == 'refunded')
                                <span class="badge bg-danger">Refunded</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="wg-box mt-5">
            <h5>Update Order Status</h5>
            <form action="{{route('admin.order.status.update')}}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <div class="row">
                    <div class="col-md-3 select">
                        <select name="order_status" id="order_status">
                            <option value="ordered" {{$order->status == 'ordered'? "selected" : ""}}>Ordered</option>
                            <option value="delivered" {{$order->status == 'delivered'? "selected" : ""}}>Delivered</option>
                            <option value="canceled" {{$order->status == 'canceled'? "selected" : ""}}>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary tf-button w208">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection