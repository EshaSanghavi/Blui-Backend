<?php

namespace App\Http\Controllers\WEB\Admin;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\CategoryAttribute;
use App\Models\SubCategoryAttribute;
use App\Models\ChildCategoryAttribute;
use App\Models\ProductGallery;
use App\Models\Brand;
use App\Models\ProductSpecificationKey;
use App\Models\ProductSpecification;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\OrderProductVariant;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Wishlist;
use App\Models\Setting;
use App\Models\FlashSaleProduct;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartVariant;
use App\Models\CompareProduct;
use Image;
use File;
use Str;

use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
class ProductAttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $categoryId = $request->query('category', null);
        $subCategoryId = $request->query('sub_category', null);
        $childCategoryId = $request->query('child_category', null);

        $childCategory = null;
        $subCategory = null;
        
        if($request->query('category')){
            $attributes_all = Attribute::where('categories', '=', 'ALL')
                ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                ->select('attribute_types.type as types', 'attributes.*')
                ->orderBy('attributes.id', 'DESC');
         
            if($categoryId){
                $attribute = CategoryAttribute::where('category', $categoryId)
                    ->leftjoin('attributes', 'category_attributes.attribute', '=', 'attributes.id')
                    ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                    ->select('attribute_types.type as types', 'attributes.*')
                    ->orderBy('attributes.id', 'DESC');
            }
            if($subCategoryId){
                $attribute = SubCategoryAttribute::where('category', $subCategoryId)
                    ->leftjoin('attributes', 'sub_category_attributes.attribute', '=', 'attributes.id')
                    ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                    ->select('attribute_types.type as types', 'attributes.*')
                    ->orderBy('attributes.id', 'DESC');
                    
                $subCategory = SubCategory::where('id', $subCategoryId)->first();
            }
            if($childCategoryId){
                $attribute = ChildCategoryAttribute::where('category', $childCategoryId)
                    ->leftjoin('attributes', 'child_category_attributes.attribute', '=', 'attributes.id')
                    ->leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
                    ->select('attribute_types.type as types', 'attributes.*')
                    ->orderBy('attributes.id', 'DESC');

                $childCategory = ChildCategory::where('id', $childCategoryId)->first();
            }
            
            $attributes = $attributes_all
                ->whereNotNull('attributes.id') 
                ->union($attribute->whereNotNull('attributes.id'))
                ->get();
        }
        else{
            $attributes = Attribute::leftjoin('attribute_types', 'attributes.type', '=', 'attribute_types.id')
            ->select('attribute_types.type as types', 'attributes.*')
            ->orderBy('attributes.id', 'DESC')
            ->get();
        }

        $categories = Category::all();
        $sub_categories = SubCategory::all();
        $child_categories = ChildCategory::all();

        return view('admin.product_attribute', compact('attributes', 'subCategory', 'childCategory', 'categories', 'sub_categories', 'child_categories'));
    }

    public function create()
    {
        $attribute_types = AttributeType::all();
        
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
                    'childcategories' => $childCategories->pluck('name', 'id')->toArray(),  
                ];
                
                $categoryData['subcategories'][] = $subcategoryData;
            }

            $data[] = $categoryData;
        }

        return view('admin.create_product_attribute', compact('attribute_types', 'data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'attribute_type' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'attribute_type.required' => trans('admin_validation.Attribute Type is required'),
        ];

        $this->validate($request, $rules,$customMessages);

        $attribute = new Attribute();
                
        $attribute->name = $request->name;
        $attribute->priority = $request->priority;
        if($request->is_required){
            $attribute->is_required = 1;
        }
        else{
            $attribute->is_required = 0;
        }
        $attribute->type = $request->attribute_type;

        if($request->has('allcats')){
            $attribute->categories = "ALL";
        }
        $attribute->save();
        
        $cat_save = 1;
        $categories = $request->category;
        if($categories)
        {
            foreach($categories as $category){
                $cat = Category::where('id', $category)->first();
                $cat_attr = new CategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
        
        $sub_categories = $request->sub_category;
        if($sub_categories)
        {
            foreach($sub_categories as $category){
                $cat = SubCategory::where('id', $category)->first();
                $cat_attr = new SubCategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
            
        $child_categories = $request->child_category;
        if($child_categories)
        {
            foreach($child_categories as $category){
                $cat = ChildCategory::where('id', $category)->first();
                $cat_attr = new ChildCategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
        

        $attribute_save = 1;
        $values = $request->attribute_value;
        if($values){
            $values = explode(',', $values);
            foreach($values as $value){
                $attribute_value = new AttributeValue();
                $attribute_value->attribute_id = $attribute->id;
                $attribute_value->value = $value;
                $attribute_value->save();
                if(!$attribute_value->save())
                {
                    $attribute_save = 0;
                }
            }
        }
        
        if($attribute_save == 0 || $cat_save == 0)
        {
            $attribute->delete();
        }
        
        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-attribute')->with($notification);
    }

    public function get_multi_entry()
    {
        $attribute_types = AttributeType::all();
        
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
                    'childcategories' => $childCategories->pluck('name', 'id')->toArray(),  
                ];
                
                $categoryData['subcategories'][] = $subcategoryData;
            }

            $data[] = $categoryData;
        }

        return view('admin.create_product_attribute_multi_secret', compact('attribute_types', 'data'));
    }

    public function multi_entry(Request $request)
    {
        $rules = [
            'names' => 'required',
            'attribute_type' => 'required',
        ];
        $customMessages = [
            'names.required' => trans('admin_validation.Name is required'),
            'attribute_type.required' => trans('admin_validation.Attribute Type is required'),
        ];

        $this->validate($request, $rules,$customMessages);

        $name = $request->names;
        $names = explode(',', $name);
        $priority = 1;
        foreach($names as $name){
            $attribute = new Attribute();
                    
            $attribute->name = $name;
            $attribute->priority = $priority;
            if($request->is_required){
                $attribute->is_required = 1;
            }
            else{
                $attribute->is_required = 0;
            }
            $attribute->type = $request->attribute_type;

            if($request->has('allcats')){
                $attribute->categories = "ALL";
            }
            $attribute->save();
            $priority = $priority + 1;
            
            $cat_save = 1;
            $categories = $request->category;
            if($categories)
            {
                foreach($categories as $category){
                    $cat = Category::where('id', $category)->first();
                    $cat_attr = new CategoryAttribute();
                    $cat_attr->attribute = $attribute->id;
                    $cat_attr->category = $cat->id;
                    $cat_attr->save();
                    if(!$cat_attr->save())
                    {
                        $cat_save = 0;
                    }
                }
            }
            
            $sub_categories = $request->sub_category;
            if($sub_categories)
            {
                foreach($sub_categories as $category){
                    $cat = SubCategory::where('id', $category)->first();
                    $cat_attr = new SubCategoryAttribute();
                    $cat_attr->attribute = $attribute->id;
                    $cat_attr->category = $cat->id;
                    $cat_attr->save();
                    if(!$cat_attr->save())
                    {
                        $cat_save = 0;
                    }
                }
            }
                
            $child_categories = $request->child_category;
            if($child_categories)
            {
                foreach($child_categories as $category){
                    $cat = ChildCategory::where('id', $category)->first();
                    $cat_attr = new ChildCategoryAttribute();
                    $cat_attr->attribute = $attribute->id;
                    $cat_attr->category = $cat->id;
                    $cat_attr->save();
                    if(!$cat_attr->save())
                    {
                        $cat_save = 0;
                    }
                }
            }
            
            if($cat_save == 0)
            {
                $attribute->delete();
                $priority = $priority - 1;
            }
        }
        $notification = trans('admin_validation.Secret Added Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-attribute')->with($notification);
    }

    public function edit($id)
    {
        $attribute = Attribute::find($id);

        $attribute_types = AttributeType::all();
        $attribute_values = AttributeValue::where('attribute_id', '=', $id)->pluck('value')->toArray();
        $escapedNames = array_map('htmlspecialchars', $attribute_values);
        // Prepare the data for JavaScript (consider JSON encoding for complex data structures)
        $attribute_values = json_encode($escapedNames);

        $data = [];

        // Fetch categories from the database
        $categories = Category::orderBy('priority', 'asc')->get();

        foreach ($categories as $category) {
            // Fetch subcategories for each category
            $subcategories = SubCategory::where('category_id', $category->id)->orderBy('priority', 'asc')->get();
            $cat = CategoryAttribute::where('category', $category->id)->where('attribute', $attribute->id)->first();
            if($cat)
                $check = 1;
            else    
                $check = 0;
            $categoryData = [
                'category' => $category->name,
                'category_id' => $category->id,
                'category_check' => $check,
                'subcategories' => [],
            ];

            foreach ($subcategories as $subcategory) {
                // Fetch child categories for each subcategory
                $childCategories = ChildCategory::where('sub_category_id', $subcategory->id)->orderBy('priority', 'asc')->get();
                $cat = SubCategoryAttribute::where('category', $category->id)->where('attribute', $attribute->id)->first();
                if($cat)
                    $check = 1;
                else    
                    $check = 0;
                $subcategoryData = [
                    'subcategory' => $subcategory->name,
                    'subcategory_id' => $subcategory->id,
                    'subcategory_check' => $check,
                    'childcategories' => $childCategories->pluck('name', 'id')->toArray(),  
                ];
                
                $categoryData['subcategories'][] = $subcategoryData;
            }

            $data[] = $categoryData;
        }

        return view('admin.edit_product_attribute', compact('attribute', 'attribute_values', 'attribute_types', 'data'));
    }


    public function update(Request $request, $id)
    {

        $attribute = Attribute::find($id);
                
        $attribute->name = $request->name;
        $attribute->priority = $request->priority;
        if($request->is_required){
            $attribute->is_required = 1;
        }
        else{
            $attribute->is_required = 0;
        }
        $attribute->type = $request->attribute_type;

        if($request->has('allcats')){
            $attribute->categories = "ALL";
        }
        $attribute->save();
        
        $deletedRows = CategoryAttribute::where('attribute', $id)->delete();
        $deletedRows = SubCategoryAttribute::where('attribute', $id)->delete();
        $deletedRows = ChildCategoryAttribute::where('attribute', $id)->delete();

        $cat_save = 1;
        $categories = $request->category;
        if($categories)
        {
            foreach($categories as $category){
                $cat = Category::where('id', $category)->first();
                $cat_attr = new CategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
        
        $sub_categories = $request->sub_category;
        if($sub_categories)
        {
            foreach($sub_categories as $category){
                $cat = SubCategory::where('id', $category)->first();
                $cat_attr = new SubCategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
            
        $child_categories = $request->child_category;
        if($child_categories)
        {
            foreach($child_categories as $category){
                $cat = ChildCategory::where('id', $category)->first();
                $cat_attr = new ChildCategoryAttribute();
                $cat_attr->attribute = $attribute->id;
                $cat_attr->category = $cat->id;
                $cat_attr->save();
                if(!$cat_attr->save())
                {
                    $cat_save = 0;
                }
            }
        }
        
        $deletedRows = AttributeValue::where('attribute_id', $id)->delete();
        $attribute_save = 1;
        $values = $request->attribute_value;
        if($values){
            $values = explode(',', $values);
            foreach($values as $value){
                $attribute_value = new AttributeValue();
                $attribute_value->attribute_id = $attribute->id;
                $attribute_value->value = $value;
                $attribute_value->save();
                if(!$attribute_value->save())
                {
                    $attribute_save = 0;
                }
            }
        }
        
        $notification = trans('admin_validation.Edited Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-attribute')->with($notification);
        
    }

    public function destroy($id)
    {
        $attribute = Attribute::find($id);
        AttributeValue::where('attribute_id', $attribute->id)->delete();
        $attribute->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-attribute')->with($notification);
    }

    public function changeStatus($id){
        $product = Product::find($id);
        if($product->status == 1){
            $product->status = 0;
            $product->save();
            $message = trans('admin_validation.InActive Successfully');
        }else{
            $product->status = 1;
            $product->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function productApproved($id){
        $product = Product::find($id);
        if($product->approve_by_admin == 1){
            $product->approve_by_admin = 0;
            $product->save();
            $message = trans('admin_validation.Reject Successfully');
        }else{
            $product->approve_by_admin = 1;
            $product->save();
            $message = trans('admin_validation.Approved Successfully');
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
        return view('admin.product_import_page');
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
