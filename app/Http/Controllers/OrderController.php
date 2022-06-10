<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();
        return view('orders',compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formType='create';
        $products = Product::all();
        return view('createOrder',compact('formType','products'));
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
            'customer_name' => 'required',
            'phone' => 'required',
        ]);


        try {


            $order_products_data = [];
            $net_amount = 0;
            foreach ($request->products as $pr) {
                $product = Product::findOrFail($request['item'][$pr]['id']);
                $coast = $product->price * $request['item'][$pr]['qty'];
                $net_amount = $net_amount + $coast;

                $temp = [];
                $temp['product_id'] = $request['item'][$pr]['id'];
                $temp['quantity'] = $request['item'][$pr]['qty'];
                $order_products_data[] = $temp;
            }

            DB::beginTransaction();
            $RandomNumber = time() . rand(1111, 9999);
            $orderid = 'OEX' . $RandomNumber;
            $order_data['orderid'] = $orderid;
            $order_data['customer_name'] = $request->customer_name;
            $order_data['phone'] = $request->phone;
            $order_data['net_amount'] = $net_amount;
            $order = new Order($order_data);
            $order->save();


            $order_products_data = array_map(function ($item) use ($order) {
                $item['order_id'] = $order->id;
                return $item;
            }, $order_products_data);

            OrderProduct::insert($order_products_data);
            DB::commit();

            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return redirect()->back()->withErrors('Try Again Later');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $products = OrderProduct::where('order_id', $order->id)->with('product')->get()->keyBy('product_id');
        $order->products = $products;
        $formType = 'edit';
        $products = Product::all();
        return view('createOrder', compact('order', 'products', 'formType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required',
            'phone' => 'required',
        ]);


        try {
            $order = Order::findOrFail($id);

            $order_products_data = [];
            $net_amount = 0;
            foreach ($request->products as $pr) {
                $product = Product::findOrFail($request['item'][$pr]['id']);
                $coast = $product->price * $request['item'][$pr]['qty'];
                $net_amount = $net_amount + $coast;

                $temp = [];
                $temp['product_id'] = $request['item'][$pr]['id'];
                $temp['quantity'] = $request['item'][$pr]['qty'];
                $order_products_data[] = $temp;
            }

            DB::beginTransaction();
            $RandomNumber = time() . rand(1111, 9999);
            $orderid = 'OEX' . $RandomNumber;
            $order_data['orderid'] = $orderid;
            $order_data['customer_name'] = $request->customer_name;
            $order_data['phone'] = $request->phone;
            $order_data['net_amount'] = $net_amount;

            $order->fill($order_data);
            $order->save();

            $order_products_data = array_map(function ($item) use ($order) {
                $item['order_id'] = $order->id;
                return $item;
            }, $order_products_data);
            foreach ($order_products_data as $prd_data) {
                OrderProduct::updateOrCreate(
                    ['order_id' => $prd_data['order_id'], 'product_id' => $prd_data['product_id']],
                    ['quantity' => $prd_data['quantity']]
                );
            }

            DB::commit();

            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return redirect()->back()->withErrors('Try Again Later');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $order = Order::find($id);
            $order->delete();
            return response()->json(['status' => '1', 'message' => 'deleted']);
        }catch (\Exception $e){
            Log::debug($e);
            return response()->json(['status' => '0', 'message' => 'Try again later']);
        }
    }


    public function getDataForInvoice($id)
    {
        $order = Order::find($id);
        $products = OrderProduct::where('order_id', $order->id)->with('product')->get();
        $order->products = $products;
        return view('formats.invoice', ['data' => $order]);
    }
}
