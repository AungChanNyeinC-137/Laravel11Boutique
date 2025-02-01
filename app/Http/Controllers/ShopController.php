<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $default_size = 12;
        $o_column = "";
        $o_order = "";
        $filter_brands = $request->query('brands');
        $filter_categories = $request->query('categories');
        #filter price
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 1000000; //max ten thousand
        $order = $request->query('order') ? $request->query('order') : -1;
        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_order = "DESC";
                break;
            default:
                $o_column = "id";
                $o_order = "DESC";
                break;
        }
        $size = $request->query('size') ? $request->query('size') : $default_size;
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::
        where(function ($query) use ($filter_brands) {
            $query->whereIn('brand_id', explode(",", $filter_brands))
                ->orWhereRaw("'" . $filter_brands . "' = ''");
        })
        ->where(function ($query) use ($filter_categories) {
            $query->whereIn('category_id', explode(",", $filter_categories))
                ->orWhereRaw("'" . $filter_categories . "' = ''");
        })
        ->where(function($query) use ($min_price,$max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])
            ->orwhereBetween('sale_price', [$min_price, $max_price]);
        })
        ->orderBy($o_column, $o_order)->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'filter_brands', 'categories','filter_categories','min_price','max_price'));
    }

    public function productDetails($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $related_products = Product::where('slug', '<>', $product_slug)->take(8)->get();
        if (!$product) {
            abort(404, 'Product not found');
        }

        return view('details', compact('product', 'related_products'));
    }
}
