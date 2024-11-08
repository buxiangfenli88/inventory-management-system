<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStorageLocation;
use App\Models\User;
use App\Services\StorageLocationService;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $storageLocationService;

    public function __construct(StorageLocationService $storageLocationService)
    {
        $this->storageLocationService = $storageLocationService;
    }

    /**
     * Display a pending orders.
     */
    public function pendingOrders()
    {
        $row = (int)request('row', 10);

        if ($row < 1) {
            abort(400, 'The per-page parameter must be an integer.');
        }

        $orders = Order::where('order_status', 'pending')
            ->roleFilter(auth()->user())
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('orders.pending-orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display a pending orders.
     */
    public function completeOrders(Request $request)
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $user = $request->user();

        $orders = Order::where('order_status', 'complete')
            ->roleFilter($user)
//            ->whereHas('customer')
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('orders.complete-orders', [
            'orders' => $orders,
        ]);
    }

    public function dueOrders()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('due', '>', '0')
            ->roleFilter(auth()->user())
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('orders.due-orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display an order details.
     */
    public function dueOrderDetails(string $order_id)
    {
        $order = Order::where('id', $order_id)->roleFilter(auth()->user())->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id')
            ->get();

        return view('orders.details-due-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Display an order details.
     */
    public function orderDetails(string $order_id)
    {
        $order = Order::where('id', $order_id)->roleFilter(auth()->user())->firstOrFail();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id')
            ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Handle create new order
     */
    public function createOrder(Request $request)
    {
        $rules = [
            'customer_id' => 'required|numeric',
            'payment_type' => 'required|string',
            'pay' => 'required|numeric',
        ];

        $invoice_no = IdGenerator::generate([
            'table' => 'orders',
            'field' => 'invoice_no',
            'length' => 10,
            'prefix' => 'INV-',
        ]);

        $validatedData = $request->validate($rules);

        DB::transaction(function () use ($validatedData, $invoice_no) {
            $validatedData['order_date'] = Carbon::now()->format('Y-m-d');
            $validatedData['order_status'] = 'pending';
            $validatedData['total_products'] = Cart::count();
            $validatedData['sub_total'] = Cart::subtotal();
            $validatedData['vat'] = Cart::tax();
            $validatedData['invoice_no'] = $invoice_no;
            $validatedData['total'] = Cart::total();
            $validatedData['due'] = ((int)Cart::total() - (int)$validatedData['pay']);
            $validatedData['created_at'] = Carbon::now();
            $validatedData['created_by'] = auth()->user()->id;

            $order_id = Order::insertGetId($validatedData);

            // Create Order Details
            $carts = Cart::content();
            $oDetails = array();

            foreach ($carts as $cartItem) {
                /* @var ProductStorageLocation $productStorageLocation */
                $productStorageLocation = ProductStorageLocation::findOrFail($cartItem->id);
                if ($cartItem->qty > $productStorageLocation->quantity) {
                    throw ValidationException::withMessages(
                        [
                            'invalidStock' => '<strong>' . $cartItem->name . '</strong>'
                                . ' vượt quá số lượng khả dụng ở vị trí '
                                . '<strong>' . $cartItem->options['storage_location_name'] . '</strong>',
                        ]
                    )->redirectTo(Redirect::back()->getTargetUrl());
                }

                $oDetails['order_id'] = $order_id;
                $oDetails['product_id'] = $productStorageLocation->product_id;
                $oDetails['storage_location_id'] = $productStorageLocation->storage_location_id;
                $oDetails['storage_location_name'] = $productStorageLocation->storageLocation->name ?? null;
                $oDetails['quantity'] = $cartItem->qty;
                $oDetails['unitcost'] = $cartItem->price;
                $oDetails['total'] = $cartItem->subtotal;
                $oDetails['created_at'] = Carbon::now();

                OrderDetails::insert($oDetails);
            }

            // Delete Cart Sopping History
            Cart::destroy();
        });

        return Redirect::route('order.pendingOrders')->with('success', 'Order has been created!');
    }

    /**
     * Handle update a status order
     */
    public function updateOrder(Request $request)
    {
        $orderId = $request->id;
        /* @var Order $order */
        $order = Order::findOrFail($orderId);

        DB::transaction(function () use ($order, $orderId) {
            /* @var OrderDetails $orderDetail */
            foreach ($order->orderDetails as $orderDetail) {

                /* @var ProductStorageLocation $productStorageLocation */
                $productStorageLocation = ProductStorageLocation::query()
                    ->where('product_id', $orderDetail->product_id)
                    ->where('storage_location_id', $orderDetail->storage_location_id)
                    ->first();
                if ($orderDetail->quantity > $productStorageLocation->quantity) {
                    throw ValidationException::withMessages(
                        [
                            'invalidStock' => '<strong>' . $orderDetail->product->product_name . '</strong>'
                                . ' vượt quá số lượng khả dụng ở vị trí '
                                . '<strong>' . $orderDetail->storage_location_name . '</strong>',
                        ]
                    )->redirectTo(Redirect::back()->getTargetUrl());
                }

                Product::query()
                    ->where('id', $orderDetail->product_id)
                    ->decrement('stock', $orderDetail->quantity);

                // update storage location when export product
                $this->storageLocationService->exportStorageLocation(
                    $orderDetail->product_id,
                    $orderDetail->storage_location_id,
                    $orderDetail->quantity,
                );
            }

            Order::findOrFail($orderId)->update(['order_status' => 'complete']);
        });

        return Redirect::route('order.completeOrders')->with('success', 'Order has been completed!');
    }

    /**
     * Handle update a due pay order
     */
    public function updateDueOrder(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'pay' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);
        $order = Order::findOrFail($validatedData['id']);

        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paidDue = $mainDue - $validatedData['pay'];
        $paidPay = $mainPay + $validatedData['pay'];

        Order::findOrFail($validatedData['id'])->update([
            'due' => $paidDue,
            'pay' => $paidPay,
        ]);

        return Redirect::route('order.dueOrders')->with('success', 'Due amount has been updated!');
    }

    /**
     * Handle to print an invoice.
     */
    public function downloadInvoice(int $order_id)
    {
        $order = Order::with('customer')->where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('orders.print-invoice', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function getOrderReport()
    {
        return view('orders.report-order');
    }

    public function exportPurchaseReport(Request $request)
    {
        $user = $request->user();

        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        $purchases = $orders = Order::query()
            ->where('order_status', 'complete')
            ->roleFilter($user)
            ->whereBetween('orders.order_date', [$sDate, $eDate])
            ->filter(request(['search']))
            ->sortable()
            ->appends(request()->query())
            ->get();


        $purchase_array [] = array(
            'Date',
            'No Purchase',
            'Supplier',
            'Product Code',
            'Product',
            'Quantity',
//            'Unitcost',
//            'Total',
        );

        foreach ($purchases as $purchase) {
            $purchase_array[] = array(
                'Date' => $purchase->purchase_date,
                'No Purchase' => $purchase->purchase_no,
                'Supplier' => $purchase->supplier_id,
                'Product Code' => $purchase->product_code,
                'Product' => $purchase->product_name,
                'Quantity' => $purchase->quantity,
//                'Unitcost' => $purchase->unitcost,
//                'Total' => $purchase->total,
            );
        }

        $this->exportExcel($purchase_array);
    }
}
