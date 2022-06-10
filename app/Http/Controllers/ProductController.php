<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    protected $stotage_disc;
    public function __construct()
    {
        $this->stotage_disc = 'public';
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return view('products',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formType='create';
        $categories = Category::all();
        return view('createProduct',compact('categories','formType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'product_image' => 'required',
        ]);
        try{
            $path = $request->file('product_image')->store('product_images', $this->stotage_disc);
            $request->merge(['image' => $path]);
            $product = new Product($request->all());
            $product->save();
            return redirect()->route('product.index');
        }catch (\Exception $e){
            Log::debug($e);
            return redirect()->back()->withErrors('Try Again Later');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $formType = 'edit';
        return view('createProduct', compact('product', 'categories','formType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required'
        ]);

        try{
            $product=Product::find($id);
            if($request->file('product_image')){
                $old_file = $product->image;
                $path = $request->file('product_image')->store('product_images', $this->stotage_disc);
                $request->merge(['image' => $path]);
                /*DELETE OLD FILE FROM STORAGE*/
                Storage::delete($old_file);
            }
            $product->fill($request->all());
            $product->save();
            return redirect()->route('product.index');
        }catch(\Exception $e){
            Log::debug($e);
            return redirect()->back()->withErrors('Try Again Later');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            Storage::disk($this->stotage_disc)->delete($product->image);
            return response()->json(['status' => '1', 'message' => 'deleted']);
        }catch (Exception $e){
            Log::debug($e);
            return response()->json(['status' => '0', 'message' => 'Try again later']);
        }
    }
}
