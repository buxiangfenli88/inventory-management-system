<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductStorageLocation;
use App\Models\StorageLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int)request('row', 10);

        if ($row < 1) {
            abort(400, 'The per-page parameter must be an integer.');
        }

        $products = Product::with(['category', 'unit'])
            ->join('product_storage_locations', 'product_storage_locations.product_id', '=', 'products.id')
            ->leftJoin('storage_locations', 'product_storage_locations.storage_location_id', '=', 'storage_locations.id')
            ->select('products.*')
            ->addSelect(DB::raw('product_storage_locations.quantity as stock_available'))
            ->addSelect(['product_storage_locations.id as product_storage_location_id'])
            ->addSelect(['storage_locations.name as storage_location_name'])
            ->filter(request(['search']))
            ->when(request('sort'), function ($query, $sort) {
                $query->orderBy($sort, request('direction', 'desc'));
            })
            ->where('product_storage_locations.quantity', '>', 0)
            ->where('products.stock', '>', 0)
            ->paginate($row)
            ->appends(request()->query());

        $customers = Customer::all()->sortBy('name');

        $carts = Cart::content();

        return view('pos.index', [
            'products' => $products,
            'customers' => $customers,
            'carts' => $carts,
        ]);
    }

    /**
     * Handle add product to cart.
     */
    public function addCartItem(Request $request)
    {
        $rules = [
            'id' => 'required|numeric|exists:product_storage_locations,id',
            'name' => 'required|string',
            'price' => 'nullable|numeric',
        ];

        $validatedData = $request->validate($rules);

        /* @var ProductStorageLocation $productStorageLocation */
        $productStorageLocation = ProductStorageLocation::findOrFail($validatedData['id']);
        /* @var StorageLocation $storageLocation */
        $storageLocation = StorageLocation::findOrFail($productStorageLocation->storage_location_id);

        Cart::add([
            'id' => $validatedData['id'], // this is product_storage_locations.id
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'] ?? 0,
            'options' => [
                'storage_location_name' => $storageLocation->name,
            ],
        ]);

        return Redirect::back()->with('success', 'Product has been added to cart!');
    }

    /**
     * Handle update product in cart.
     */
    public function updateCartItem(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Product has been updated from cart!');
    }

    /**
     * Handle delete product from cart.
     */
    public function deleteCartItem(string $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Product has been deleted from cart!');
    }

    /**
     * Handle create an invoice.
     * @throws ValidationException
     */
    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required|string',
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();

        $carts = Cart::content();

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
        }

        return view('pos.create', [
            'customer' => $customer,
            'carts' => $carts,
        ]);
    }
}
