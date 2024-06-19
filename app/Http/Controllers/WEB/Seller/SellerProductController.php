<?php

namespace App\Http\Controllers\WEB\Seller;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\ProductGallery;
use App\Models\Brand;
use App\Models\CategoryAttribute;
use App\Models\SubCategoryAttribute;
use App\Models\ChildCategoryAttribute;
use App\Models\Attribute;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Models\ProductSpecificationKey;
use App\Models\ProductSpecification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Wishlist;
use App\Models\Setting;
use App\Models\ShoppingCart;
use App\Models\FlashSaleProduct;
use App\Models\ShoppingCartVariant;
use App\Models\CompareProduct;
use Image;
use File;
use Str;
use Auth;

use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class SellerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $seller = Auth::guard('web')->user()->seller;
        $products = Product::with('category','seller','brand')->orderBy('id','desc')->where('approve_by_admin',1)->where('vendor_id',$seller->id)->orderBy('id','desc')->get();
        $orderProducts = OrderProduct::all();
        $setting = Setting::first();
        $user = "seller";
        return view('seller.product',compact('products','orderProducts','setting', 'user'));


    }

    public function pendingProduct(){
        $seller = Auth::guard('web')->user()->seller;
        $products = Product::with('category','seller','brand')->orderBy('id','desc')->where('approve_by_admin',0)->where('vendor_id',$seller->id)->orderBy('id','desc')->get();
        $orderProducts = OrderProduct::all();
        $setting = Setting::first();
        return view('seller.pending_product',compact('products','orderProducts','setting'));
    }

    public function stockoutProduct(){
        $seller = Auth::guard('web')->user()->seller;
        $products = Product::with('category','seller','brand')->orderBy('id','desc')->where('qty',0)->where('vendor_id',$seller->id)->get();
        $setting = Setting::first();

        return view('seller.stockout_product',compact('products','setting'));
    }

    public function selectCategory()
    {
        // Fetch categories from the database
        $data = [];

        // Fetch categories from the database
        $categories = Category::orderBy('priority', 'asc')->get();

        foreach ($categories as $category) {
            // Fetch subcategories for each category
            $subcategories = SubCategory::where('category_id', $category->id)->orderBy('priority', 'asc')->get();
            
            $categoryData = [
                'category' => $category->name,
                'category_id' => $category->id,
                'subcategories' => [],
            ];

            foreach ($subcategories as $subcategory) {
                // Fetch child categories for each subcategory
                $childCategories = ChildCategory::where('sub_category_id', $subcategory->id)->orderBy('priority', 'asc')->get();
                
                $subcategoryData = [
                    'subcategory' => $subcategory->name,
                    'subcategory_id' => $subcategory->id,
                    'childcategories' => $childCategories->pluck('name')->toArray(),
                ];
                
                $categoryData['subcategories'][] = $subcategoryData;
            }

            $data[] = $categoryData;
        }

        $user = "seller";
        return view('seller.create_product_select_category', compact('data', 'user'));
    }

    public function newProductCategory(Request $request)
    {
        $rules = [
            'category' => 'required',
        ];
        $customMessages = [
            'category.required' => trans('admin_validation.Category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = Category::where('id',$request->category)->first();
        $category_id = $category->id;

        $sub_category_id = 0;
        if($request->has('sub_category'))
        {
            $sub_category = SubCategory::where('id',$request->sub_category)->first();
            $sub_category_id = $sub_category->id;
        }
            
        $child_category_id = 0;
        if($request->has('child_category'))
        {
            $child_category = ChildCategory::where('name',$request->child_category)->first();
            $child_category_id = $child_category->id;
        }

        $brands = Brand::all();
        $specificationKeys = ProductSpecificationKey::all();


        $attributes_all = Attribute::where('categories', '=', 'ALL')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');

        $attributechild = null;
        if($child_category_id != 0)
        {
            $attributechild = ChildCategoryAttribute::where('category', '=', $child_category_id)
                ->leftjoin('attributes', 'child_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
                
        }
        $attributesub = null;
        if($sub_category_id != 0)
        {
            $attributesub = SubCategoryAttribute::where('category', '=', $sub_category_id)
                ->leftjoin('attributes', 'sub_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
        }
        $attribute = null;
        if($attributechild && $attributesub)
            $attribute = $attributechild->union($attributesub);
        else if($attributesub)
            $attribute = $attributesub;
        
        $attributecat = null;
        if($category_id != 0)
        {
            $attributecat = CategoryAttribute::where('category', '=', $category_id)
            ->leftjoin('attributes', 'category_attributes.attribute', '=', 'attributes.id')
            ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
            ->select('attribute_types.type as types', 'attributes.*')
            ->orderby('priority', 'asc');
        }
        $attributemix = null;
        if($attributecat && $attribute)
            $attributemix = $attribute->union($attributecat);
        else if($attributecat)
            $attributemix = $attributecat;

        if($attributemix && $attributes_all){
            $attributeunion = $attributes_all->union($attributemix);
            $attributes = $attributeunion->orderby('priority', 'asc')->get();
        }
        else if($attributes_all){
                $attributes = $attributes_all; 
        }
        else{
            $attributes = 0;
        }

        $user = "seller";
        return view('seller.create_product',compact('category_id', 'sub_category_id', 'child_category_id', 'brands','specificationKeys', 'attributes', 'user'));

    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $specificationKeys = ProductSpecificationKey::all();

        return view('seller.create_product',compact('categories','brands','specificationKeys'));
    }


    public function getSubcategoryByCategory($id){
        $subCategories=SubCategory::where('category_id',$id)->get();
        $response='<option value="">'.trans('admin_validation.Select Sub Category').'</option>';
        foreach($subCategories as $subCategory){
            $response .= "<option value=".$subCategory->id.">".$subCategory->name."</option>";
        }
        return response()->json(['subCategories'=>$response]);
    }

    public function getChildcategoryBySubCategory($id){
        $childCategories=ChildCategory::where('sub_category_id',$id)->get();
        $response='<option value="">'.trans('admin_validation.Select Child Category').'</option>';
        foreach($childCategories as $childCategory){
            $response .= "<option value=".$childCategory->id.">".$childCategory->name."</option>";
        }
        return response()->json(['childCategories'=>$response]);
    }

    public function store(Request $request)
    {   
        $rules = [
            'name' => 'required',
            'weight' => 'required',
            'images' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'images.required' => trans('admin_validation.Minimum 1 image is required'),
            'price.required' => trans('admin_validation.Price is required'),
            'quantity.required' => trans('admin_validation.Quantity is required'),
            'weight.required' => trans('admin_validation.Weight is required'),
        ];
        $this->validate($request, $rules,$customMessages);


        $seller = Auth::guard('web')->user()->seller;
        $product = new Product();
        $images_name = [];
        if($request->images){
            foreach($request->images as $image)
            {
                $extention = $image->getClientOriginalExtension();
                $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
                $image_name = 'uploads/custom-images/'.$image_name;
                Image::make($image)
                    ->save($image_name);
                array_push($images_name, $image_name);
            }
        }

        $product->vendor_id = $seller->id;
        $product->name = $request->name;
        $product->thumb_image = $images_name[0];
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->child_category_id = $request->child_category;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->qty = $request->quantity;
        $product->status = 1;
        $product->weight = $request->weight;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->is_top = $request->top_product ? 1 : 0;
        $product->new_product = $request->new_arrival ? 1 : 0;
        $product->is_best = $request->best_product ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;

        $product->save();

        foreach($images_name as $immage){
            $image_gallery = new ProductGallery();
            $image_gallery->product_id = $product->id;
            $image_gallery->image = $image;
            $image_gallery->status = 1;
            $image_gallery->save();
        }

        $category_id = $request->category;
        $sub_category_id = $request->sub_category;
        $child_category_id = $request->child_category;

        $attributes_all = Attribute::where('categories', '=', 'ALL')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');

        $attributechild = null;
        if($child_category_id != 0)
        {
            $attributechild = ChildCategoryAttribute::where('category', '=', $child_category_id)
                ->leftjoin('attributes', 'child_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
                
        }
        $attributesub = null;
        if($sub_category_id != 0)
        {
            $attributesub = SubCategoryAttribute::where('category', '=', $sub_category_id)
                ->leftjoin('attributes', 'sub_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
        }
        $attribute = null;
        if($attributechild && $attributesub)
            $attribute = $attributechild->union($attributesub);
        else if($attributesub)
            $attribute = $attributesub;
        
        $attributecat = null;
        if($category_id != 0)
        {
            $attributecat = CategoryAttribute::where('category', '=', $category_id)
            ->leftjoin('attributes', 'category_attributes.attribute', '=', 'attributes.id')
            ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
            ->select('attribute_types.type as types', 'attributes.*')
            ->orderby('priority', 'asc');
        }
        $attributemix = null;
        if($attributecat && $attribute)
            $attributemix = $attribute->union($attributecat);
        else if($attributecat)
            $attributemix = $attributecat;
    
        if($attributemix && $attributes_all){
            $attributeunion = $attributes_all->union($attributemix);
            $attributes = $attributeunion->orderby('priority', 'asc')->get();
        }
        else if($attributes_all){
                $attributes = $attributes_all;  
        }
        else{
            $attributes = 0;
        }

        $a_flag = 1;
        foreach($attributes as $att)
        {
            $name = $att->name;
            if($request->$name){    
                $a = new ProductAttribute();
                $a->product_id  = $product->id;
                $a->attribute_id = $att->id;
                $a->value = $request->$name;
                $a->save();
                if(!$a->save())
                {
                    $a_flag = 0;
                }
            }
        }

        if($request->is_specification){
            $exist_specifications=[];
            if($request->keys){
                foreach($request->keys as $index => $key){
                    if($key){
                        if($request->specifications[$index]){
                            if(!in_array($key, $exist_specifications)){
                                $productSpecification= new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                            $exist_specifications[] = $key;
                        }
                    }
                }
            }
        }

        if($a_flag == 0)
        {
            $product->delete();
        }

        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);
    }

    public function show($id)
    {
        $product = Product::with('category','brand','gallery','specifications','reviews','variants','variantItems')->find($id);
        if($product->vendor_id == 0){
            $notification = 'Something went wrong';
            return response()->json(['error'=>$notification],403);
        }

        return response()->json(['product' => $product], 200);
    }

    public function edit($id)
    {
        $product = Product::with('category','brand','gallery','variants','variantItems')->find($id);

        
        $categories = Category::where('id',$product->category_id)->first();
        $subCategories = SubCategory::where('id', $product->sub_category_id)->first();
        $childCategories = ChildCategory::where('id', $product->child_category_id)->first();


        $category_id = $categories->id;
        $sub_category_id = $subCategories->id;
        $child_category_id = $childCategories->id;

        $attributes_all = Attribute::where('categories', '=', 'ALL')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');

        $attributechild = null;
        if($child_category_id != 0)
        {
            $attributechild = ChildCategoryAttribute::where('category', '=', $child_category_id)
                ->leftjoin('attributes', 'child_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
                
        }
        $attributesub = null;
        if($sub_category_id != 0)
        {
            $attributesub = SubCategoryAttribute::where('category', '=', $sub_category_id)
                ->leftjoin('attributes', 'sub_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
        }
        $attribute = null;
        if($attributechild && $attributesub)
            $attribute = $attributechild->union($attributesub);
        else if($attributesub)
            $attribute = $attributesub;
        
        $attributecat = null;
        if($category_id != 0)
        {
            $attributecat = CategoryAttribute::where('category', '=', $category_id)
            ->leftjoin('attributes', 'category_attributes.attribute', '=', 'attributes.id')
            ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
            ->select('attribute_types.type as types', 'attributes.*')
            ->orderby('priority', 'asc');
        }
        $attributemix = null;
        if($attributecat && $attribute)
            $attributemix = $attribute->union($attributecat);
        else if($attributecat)
            $attributemix = $attributecat;
    
        if($attributemix && $attributes_all){
            $attributeunion = $attributes_all->union($attributemix);
            $attributes = $attributeunion->orderby('priority', 'asc')->get();
        }
        else if($attributes_all){
                $attributes = $attributes_all;  
        }
        else{
            $attributes = 0;
        }

        $specificationKeys = ProductSpecificationKey::all();
        $productSpecifications = ProductSpecification::where('product_id',$product->id)->get();

        $categories = Category::get();
        $subCategories = SubCategory::get();
        $childCategories = ChildCategory::get();

        return view('seller.edit_product',compact('product', 'categories', 'subCategories', 'childCategories', 'specificationKeys', 'attributes'));

    }

    public function update(Request $request, $id)
    {
        $seller = Auth::guard('web')->user()->seller;
        $product = Product::find($id);
        $rules = [
            'name' => 'required',
            'weight' => 'required',
            'sku' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'sku.required' => trans('admin_validation.SKU is required'),
            'price.required' => trans('admin_validation.Price is required'),
            'quantity.required' => trans('admin_validation.Quantity is required'),
            'weight.required' => trans('admin_validation.Weight is required'),
        ];
        $this->validate($request, $rules,$customMessages);


        $images_name = [];
        if($request->images){
            foreach($request->images as $image)
            {
                $extention = $image->getClientOriginalExtension();
                $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
                $image_name = 'uploads/custom-images/'.$image_name;
                Image::make($image)
                    ->save($image_name);
                array_push($images_name, $image_name);
            }
        }

        $product->vendor_id = $seller->id;
        $product->name = $request->name;
        $product->thumb_image = $images_name[0];
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->child_category_id = $request->child_category;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->qty = $request->quantity;
        $product->status = 1;
        $product->weight = $request->weight;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->is_top = $request->top_product ? 1 : 0;
        $product->new_product = $request->new_arrival ? 1 : 0;
        $product->is_best = $request->best_product ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;

        $product->save();

        foreach($images_name as $immage){
            $image_gallery = new ProductGallery();
            $image_gallery->product_id = $product->id;
            $image_gallery->image = $image;
            $image_gallery->status = 1;
            $image_gallery->save();
        }

        $category_id = $request->category;
        $sub_category_id = $request->sub_category;
        $child_category_id = $request->child_category;

        $attributes_all = Attribute::where('categories', '=', 'ALL')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');

        $attributechild = null;
        if($child_category_id != 0)
        {
            $attributechild = ChildCategoryAttribute::where('category', '=', $child_category_id)
                ->leftjoin('attributes', 'child_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
                
        }
        $attributesub = null;
        if($sub_category_id != 0)
        {
            $attributesub = SubCategoryAttribute::where('category', '=', $sub_category_id)
                ->leftjoin('attributes', 'sub_category_attributes.attribute', '=', 'attributes.id')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderby('priority', 'asc');
        }
        $attribute = null;
        if($attributechild && $attributesub)
            $attribute = $attributechild->union($attributesub);
        else if($attributesub)
            $attribute = $attributesub;
        
        $attributecat = null;
        if($category_id != 0)
        {
            $attributecat = CategoryAttribute::where('category', '=', $category_id)
            ->leftjoin('attributes', 'category_attributes.attribute', '=', 'attributes.id')
            ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
            ->select('attribute_types.type as types', 'attributes.*')
            ->orderby('priority', 'asc');
        }
        $attributemix = null;
        if($attributecat && $attribute)
            $attributemix = $attribute->union($attributecat);
        else if($attributecat)
            $attributemix = $attributecat;
    
        if($attributemix && $attributes_all){
            $attributeunion = $attributes_all->union($attributemix);
            $attributes = $attributeunion->orderby('priority', 'asc')->get();
        }
        else if($attributes_all){
             $attributes = $attributes_all;  
        }
        else{
            $attributes = 0;
        }

        $a_flag = 1;
        foreach($attributes as $att)
        {
            $name = $att->name;
            if($request->$name){    
                $a = ProductAttribute::where('attribute_id', '=', $att->id)
                    ->where('product_id', '=', $product->id)
                    ->first();
                if(!$a){
                    $a = new ProductAttribute();
                    $a->attribute_id = $att->id;
                    $a->product_id = $product->id;
                    $a->value = $request->$name;
                    $a->save();
                }
                else{
                    $a->value = $request->$name;
                    $a->save();
                }
            }
        }

        if($request->is_specification){
            $exist_specifications=[];
            if($request->keys){
                foreach($request->keys as $index => $key){
                    if($key){
                        if($request->specifications[$index]){
                            if(!in_array($key, $exist_specifications)){
                                $productSpecification= new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                            $exist_specifications[] = $key;
                        }
                    }
                }
            }
        }

        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $gallery = $product->gallery;
        $old_thumbnail = $product->thumb_image;
        $product->delete();
        if($old_thumbnail){
            if(File::exists($old_thumbnail))unlink($old_thumbnail);
        }
        foreach($gallery as $image){
            $old_image = $image->image;
            $image->delete();
            if($old_image){
                if(File::exists($old_image))unlink($old_image);
            }
        }

        $attributes = ProductAttribute::where('product_id',$id)->get();
        foreach($attributes as $attribute){
            $attribute->delete();
        }

        ProductVariant::where('product_id',$id)->delete();
        ProductVariantItem::where('product_id',$id)->delete();
        ProductReport::where('product_id',$id)->delete();
        FlashSaleProduct::where('product_id',$id)->delete();
        ProductReview::where('product_id',$id)->delete();
        ProductSpecification::where('product_id',$id)->delete();
        Wishlist::where('product_id',$id)->delete();

        $cartProducts = ShoppingCart::where('product_id',$id)->get();
        foreach($cartProducts as $cartProduct){
            ShoppingCartVariant::where('shopping_cart_id', $cartProduct->id)->delete();
            $cartProduct->delete();
        }
        CompareProduct::where('product_id',$id)->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.product.index')->with($notification);

    }

    public function changeStatus($id){
        $product = Product::find($id);
        if($product->status == 1){
            $product->status = 0;
            $product->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $product->status = 1;
            $product->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function removedProductExistSpecification($id){
        $productSpecification = ProductSpecification::find($id);
        $productSpecification->delete();
        $message = trans('admin_validation.Removed Successfully');
        return response()->json($message);
    }


    public function product_import_page()
    {
        $seller = Auth::guard('web')->user()->seller;
        return view('seller.product_import_page')->with(['seller' => $seller]);
    }

    public function product_export()
    {
        $is_dummy = false;
        return Excel::download(new ProductExport($is_dummy), 'products.xlsx');
    }


    public function demo_product_export()
    {
        $is_dummy = true;
        return Excel::download(new ProductExport($is_dummy), 'products.xlsx');
    }



    public function product_import(Request $request)
    {
        try{
            Excel::import(new ProductImport, $request->file('import_file'));

            $notification=trans('Uploaded Successfully');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);

        }catch(Exception $ex){
            $notification=trans('Please follow the instruction and input the value carefully');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }


    }

}
