<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\sections;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = products::all();
        $sections = sections::all();

        return view("products.products", compact("products","sections"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate=$request->validate([
            'Product_name'=>'required|unique:products,Product_name|max:255',
        ],[
            'Product_name.required'=>"يرجى ادخال اسم المنتج",
            'Product_name.unique'=>"المنتج موجود بالفعل",
        ]);
        products::create([
            'Product_name'=>$request->Product_name,
            'section_id' => $request->section_id,
            'description'=>$request->description,
        ]);
        session()->flash("add","تم اضافة المنتج بنجاح");
        return redirect("/home/products");
    }

    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validate=$request->validate([
            'Product_name'=>'required|max:255|unique:products,Product_name,'.$request->pro_id,
        ],[
            'Product_name.required'=>"يرجى ادخال اسم المنتج",
            'Product_name.unique'=>"المنتج موجود بالفعل",
        ]);


        $id = sections::where('section_name', $request->section_name)->first()->id;

        $Products = Products::findOrFail($request->pro_id);

        $Products->update([
        'Product_name' => $request->Product_name,
        'description' => $request->description,
        'section_id' => $id,
        ]);

        session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $Products = Products::findOrFail($request->pro_id);
        $Products->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}

