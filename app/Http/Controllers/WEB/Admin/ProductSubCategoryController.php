<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PopularCategory;
use App\Models\ThreeColumnCategory;
use App\Models\MegaMenuSubCategory;
use Image;
use File;
use Str;

class ProductSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $subCategories=SubCategory::with('category','childCategories','products')->get();

        return view('admin.product_sub_category',compact('subCategories'));
    }


    public function create()
    {
        $categories=Category::all();
        return view('admin.create_product_sub_category',compact('categories'));
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required',
            'priority' => 'required',
            'commission' => 'required_without:child-checkbox',
            // 'field1' is required if 'field2' is not present
            'child-checkbox' => 'required_without:commission'
            // 'field2' is required if 'field1' is not present
        ];

        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'priority.required' => trans('admin_validation.Priority is required'),
            'commission.required' => trans('admin_validation.Commission is required if there is no Child Category'),
            'category.required' => trans('admin_validation.Category is required')
        ];

        $this->validate($request, $rules, $customMessages);

        if ($request->has('child-checkbox')) {
            $has_child = 1;
        } else {
            // Checkbox is not checked
            $has_child = 0;
        }

        $subCategory = new SubCategory();
        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->priority = $request->priority;
        $subCategory->has_child = $has_child;
        $subCategory->commission = $request->commission;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;

        if($request->image){
            $old_logo = $subCategory->image;
            $extention = $request->image->getClientOriginalExtension();
            $logo_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $logo_name = 'uploads/custom-images/'.$logo_name;
            Image::make($request->image)
                ->save($logo_name);
            $subCategory->image=$logo_name;
            $subCategory->save();

            if($old_logo){
                if(File::exists($old_logo))unlink($old_logo);
            }
        }

        $subCategory->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-sub-category.index')->with($notification);
    }

    public function show($id){
        $subCategory = SubCategory::find($id);
        return response()->json(['subCategory' => $subCategory],200);
    }

    public function edit($id)
    {
        $subCategory = SubCategory::find($id);
        $categories=Category::all();
        return view('admin.edit_product_sub_category',compact('subCategory','categories'));
    }


    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        $rules = [
            'name'=>'required',
            'slug'=>'required|unique:sub_categories,slug,'.$subCategory->id,
            'category'=>'required',
            'status'=>'required',
            'priority' => 'required',
            'commission' => 'required_without:child-checkbox',
            // 'field1' is required if 'field2' is not present
            'child-checkbox' => 'required_without:commission'
            // 'field2' is required if 'field1' is not present
        ];

        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'priority.required' => trans('admin_validaton.Priority is required'),
            'commission.required' => trans('admin_validation.Commission is required if there is no Child Category'),
            'category.required' => trans('admin_validation.Category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if ($request->has('child-checkbox')) {
            $has_child = 1;
        } else {
            // Checkbox is not checked
            $has_child = 0;
        }

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->has_child = $has_child;
        $subCategory->priority = $request->priority;
        $subCategory->commission = $request->commission;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;

        if($request->image){
            $old_logo = $subCategory->image;
            $extention = $request->image->getClientOriginalExtension();
            $logo_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $logo_name = 'uploads/custom-images/'.$logo_name;
            Image::make($request->image)
                ->save($logo_name);
            $subCategory->image=$logo_name;
            $subCategory->save();

            if($old_logo){
                if(File::exists($old_logo))unlink($old_logo);
            }
        }
        
        $subCategory->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-sub-category.index')->with($notification);
    }


    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);
        $subCategory->delete();
        MegaMenuSubCategory::where('sub_category_id',$id)->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-sub-category.index')->with($notification);
    }

    public function changeStatus($id){
        $subCategory = SubCategory::find($id);
        if($subCategory->status==1){
            $subCategory->status=0;
            $subCategory->save();
            $message = trans('admin_validation.InActive Successfully');
        }else{
            $subCategory->status=1;
            $subCategory->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

}
