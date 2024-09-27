<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\get;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new OrderCollection(Order::where('complete', 0)->with(['user', 'products'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'total' => ['required', 'numeric'],
            'products' => ['required', 'array', 'min:1'],
        ], [
            'total.required' => 'El Total es Requerido',
            'total.numeric' => 'El Total es Invalido',
            'products.required' => 'Los Productos son Requeridos',
            'products.array' => 'Los Productos son Invalidos',
            'products.min' => 'Por Lo menos se necesita un producto',
        ]);

        $order = new Order();

        $order->complete = 0;
        $order->total = $data['total'];
        $order->user_id = Auth::user()->id;
        $order->save();

        $order_products = [];

        foreach($data['products'] as $product) {
            $order_products[] = [
                'quantity' => $product['quantity'],
                'product_id' => $product['product_id'],
                'order_id' => $order->getKey(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        OrderProduct::insert($order_products);

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {        
        $order->complete = 1;

        $order->save();

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
