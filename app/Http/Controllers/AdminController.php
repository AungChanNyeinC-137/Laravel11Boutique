<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Surfsidemedia\Shoppingcart\Facades\Cart;

use function PHPUnit\Framework\fileExists;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    #region Brands

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
        //The compact() function in Laravel is used to create an array of variables and their corresponding values.
    }

    public function brandAdd()
    {
        return view('admin.brand-add');
    }

    public function brandStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:3064'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->generateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully');
    }

    public function brandEdit($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            return view('admin.brand-edit', compact('brand'));
        }
        return redirect()->route('admin.brands')->with('error', 'Brand cannot be Found');
    }

    public function brandUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:3072'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully');
    }

    public function brandDelete($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->name)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->name);
            }
            $brand->delete();
            return redirect()->route('admin.brands')->with('status', 'Brand hass been deleted successfully');
        }
        return redirect()->route('admin.brands')->with('status', 'Brand not found');
    }

    public function generateBrandThumbnailsImage($image, $image_name)
    {
        $destination_path = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destination_path . '/' . $image_name);
    }
    public function generateSlideThumbnailsImage($image, $image_name)
    {
        $destination_path = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400, 690, "top");
        $img->resize(400, 690, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destination_path . '/' . $image_name);
    }
    // public function generateSlideThumbnailsImage($image, $image_name)
    // {
    //     $destination_path = public_path('uploads/slides');
        
    //     // Create an image instance
    //     $img = Image::make($image->getRealPath());
    
    //     // Resize to 124x124 while maintaining aspect ratio
    //     $img->resize(124, 124, function ($constraint) {
    //         $constraint->aspectRatio();
    //         $constraint->upsize();
    //     });
    
    //     // Save the resized image
    //     $img->save($destination_path . '/' . $image_name);
    // }

    public function generateCategoryThumbnailsImage($image, $image_name)
    {
        $destination_path = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destination_path . '/' . $image_name);
    }
    public function generateProductThumbnailsImage($image, $image_name)
    {
        $destination_path = public_path('uploads/products');
        $destination_path_thumbnails = public_path('uploads/products/thumbnails');
        $img = Image::read($image->path());


        $img->
        // resize(124, 124, function ($constraint) {
        //     $constraint->aspectRatio();
        // })->
        save($destination_path . '/' . $image_name);
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destination_path_thumbnails . '/' . $image_name);
    }
    #endregion Brands

    #region Categories

    public function categories()
    {
        $categories = Category::orderBy('id', 'ASC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function categoryAdd()
    {
        return view('admin.category-add');
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:3064'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->generateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully');
    }

    public function categoryEdit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return view('admin.category-edit', compact('category'));
        }
        return redirect()->route('admin.categories')->with('status', 'Category cannot be found');
    }

    public function categoryUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,',
            'image' => 'mimes:png,jpg,jpeg|max:3072',
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->name)) {
                File::delete(public_path('uploads/categories') . '/' . $category->name);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function categoryDelete($id)
    {
        $category = Category::find($id);
        if ($category) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->name)) {
                File::delete(public_path('uploads/categories') . '/' . $category->name);
            }
            $category->delete();
            return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
        }
    }

    #regionend Categories

    #region Products
    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function productAdd()
    {
        $categories = Category::select('id', 'name')->orderBy('name', 'DESC')->get();
        $brands = Brand::select('id', 'name')->orderBy('name', 'DESC')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            //'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        #region processImage
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $current_time_stamp = Carbon::now()->timestamp;
            $image_name = $current_time_stamp . '.' . $image->extension();
            $this->generateProductThumbnailsImage($image, $image_name);
            $product->image = $image_name;
        }
        #endregion processImage

        #region processImages
        $gallery_arr = array();
        $gallery_images = "";
        if ($request->hasFile('images')) {
            $allowed_extension = ['jpg', 'png', 'jpeg']; // Fixed 'jpn' typo
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $gextension = strtolower($file->getClientOriginalExtension());
                $gcheck = in_array($gextension, $allowed_extension);
                if ($gcheck) {
                    // Generate a unique name for each image
                    $gfile_name = $current_time_stamp . '_' . $index . '.' . $gextension;
                    $this->generateProductThumbnailsImage($file, $gfile_name);
                    array_push($gallery_arr, $gfile_name);
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        #endregion processImages

        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }

    public function productEdit($id)
    {
        $product = Product::find($id);
        if ($product) {
            $categories = Category::select('id', 'name')->orderBy('name')->get();
            $brands = Brand::select('id', 'name')->orderBy('name')->get();
            return view('admin.product-edit', compact('product', 'categories', 'brands'));
        } else {
            return redirect()->route('admin.products')->with('status', 'cannot find the product');
        }
    }

    public function prodcutUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        #region processImage
        if ($request->hasFile('image')) {
            $this->deleteProductImage($product->image);
            $image = $request->file('image');
            $current_time_stamp = Carbon::now()->timestamp;
            $image_name = $current_time_stamp . '.' . $image->extension();
            $this->generateProductThumbnailsImage($image, $image_name);
            $product->image = $image_name;
        }
        #endregion processImage

        #region processImages
        $gallery_arr = array();
        $gallery_images = "";
        if ($request->hasFile('images')) {
            $this->deleteProductImages($product->images);
            $allowed_extension = ['jpg', 'png', 'jpeg']; 
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowed_extension);
                if ($gcheck) {
                    // Generate a unique name for each image
                    $gfile_name = $current_time_stamp . '_' . $index . '.' . $gextension;
                    $this->generateProductThumbnailsImage($file, $gfile_name);
                    array_push($gallery_arr, $gfile_name);
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        #endregion processImages

        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function productDelete($id) {
        $product = Product::find($id);
        if($product) {
            $this->deleteProductImage($product->image);
            $this->deleteProductImages($product->images);
            $product->delete();
            return redirect()->route('admin.products')->with('status','Product has been deleted successfully');
        } else {
            return redirect()->route('admin/products')->with('error','cannot find the product');
        }

        
    }

    public function deleteProductImage($image) {
        if(File::exists(public_path('uploads/products').'/'.$image)) {
            File::delete(public_path('uploads/products').'/'.$image);
        }
        if(File::exists(public_path('uploads/products/thumbnails').'/'.$image)) {
            File::delete(public_path('uploads/products/thumbnails').'/'.$image);
        }
    }
    
    public function deleteProductImages($images) {
        foreach(explode(',',$images) as $original_file) {
            if(File::exists(public_path('uploads/products').'/'.$original_file)) {
                File::delete(public_path('uploads/products').'/'.$original_file);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$original_file)) {
                File::delete(public_path('uploads/products/thumbnails').'/'.$original_file);
            }
        }
    }
    #endregion Products

    #region Coupons
    public function coupons() {
        $coupons = Coupon::orderBy('created_at','DESC')->paginate(12);
        return view('admin.coupons',compact('coupons'));
    }

    public function addCoupon() {
        return view('admin.coupon-add');
    }

    public function couponStore(Request $request) {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success','Coupon has been added successfully!');

    }

    public function couponEdit($id) {
        $coupon = Coupon::find($id);
        if($coupon) {
            return view('admin.coupon-edit',compact('coupon'));
        } else {
            return redirect()->back()->with('error','Coupon cannot be found');
        }
    }

    public function couponUpdate(Request $request) {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success','Coupon '.$request->code.' has been updated successfully!');

    }

    public function couponDelete($id) {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success','Coupon '.$coupon->code.' has been deleted successfully!');
    }

    public function applyCouponCode(Request $request) {
             $coupon = Coupon::where('code',$request->coupon_code)
             ->where('expiry_date','>=', Carbon::today())
             ->where('cart_value','<=', floatval(str_replace(',', '', Cart::instance('cart')->subtotal())))
             ->first();
            try {
            if(!$coupon) {
                return redirect()->back()->with('error','Expired or Invalid Coupon Code!'.$coupon);
            }
            Session::put('coupon',[
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value,
            ]);
           
            $this->calculateDiscount();
            return redirect()->back()->with('success','Coupon has been applied successfully!');

            } catch (Exception $e) {
                return redirect()->back()->with('error','an error occurred while applying the coupon code. Please try again');
            }
        }
    


    public function calculateDiscount() {
        $discount = 0;
        if(Session::has('coupon')) {
         //calculating based on discount type
            if(Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (floatval(str_replace(',', '', Cart::instance('cart')->subtotal())) * Session::get('coupon')['value']) / 100;
            }
            $subtotal_after_discount = floatval(str_replace(',', '', Cart::instance('cart')->subtotal())) - $discount;
            $tax_after_discount = $subtotal_after_discount * config('cart.tax') / 100 ;
            $total_after_discount = $subtotal_after_discount + $tax_after_discount;
            Session::put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval($subtotal_after_discount),2,'.',''),
                'tax' => number_format(floatval($tax_after_discount),2,'.',''),
                'total' => number_format(floatval($total_after_discount),2,'.',''),
            ]);

        }
    }

    public function couponRemove(){
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success','Copuon has been removed successfully');
    }

    #region Orders
    public function orders() {
        $orders = Order::orderBy('created_at','DESC')->paginate(12);
        return view('admin.orders',compact('orders'));
    }

    public function orderDetails($order_id) {
        $order = Order::find($order_id);
        $order_items = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id',$order_id)->first();
        return view ('admin.order-details',compact('order','order_items','transaction'));
    }

    public function orderUpdateStatus(Request $request) {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;

        if($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
            $transaction = Transaction::where('order_id',$request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }
        elseif($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
            $transaction = Transaction::where('order_id',$request->order_id)->first();
            $transaction->status = 'declined';
            $transaction->save();
        }
        $order->save();

        return back()->with('status','Successfully Changed Order Status!');
    }

    #region endOrders

    #region Slides
    public function slides() {
        $slides = Slide::orderBy('id','DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slideAdd() {
        return view('admin.slide-add');
    }

    public function slideStore(Request $request) {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:3072',
            'status' => 'required|in:0,1',
        ]);
    
        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status; 
    
        $image = $request->file('image');
        $file_extension = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
    
        $this->generateSlideThumbnailsImage($image, $file_name);
    
        $slide->image = $file_name;
        $slide->save();
    
        return redirect()->route('admin.slides')->with('success', 'New Slide added successfully!');
    }

    public function slideEdit($id) {
        $slide = Slide::find($id);
        if($slide) {
            return view('admin.slide-edit',compact('slide'));
        } else {
            return redirect()->back()->with('error','Slide cannot be found');
        }
    }

    public function slideUpdate(Request $request) {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:3072',
            'status' => 'required|in:0,1',
        ]);
        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status; 
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->name)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->name);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('success','Updated Slide '.$request->id.' successfully!');

    }

    public function slideDelete($id) {
        $slide = Slide::find($id);
        if(File::exists(public_path('uploads/slides').'/'.$slide->image)) {
            File::delete(public_path('uploads/slides').'/'.$slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('success','Deleted slide '.$id.' successfully!');
    }
    
    #endregion Slides
}



