<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = Product::with(['category', 'unit'])
                ->filter(request(['search']))
                ->sortable()
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
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'nullable|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'] ?? 0
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
    public function deleteCartItem(String $rowId)
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
            'customer_id' => 'required|string'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();

        $contents = Cart::content();

        foreach ($contents as $content) {
            $product = Product::findOrFail($content->id);
            if ($content->qty > $product->stock) {
                throw ValidationException::withMessages(['invalidStock' => $product->product_name . ' vượt quá số lượng khả dụng'])->redirectTo(Redirect::back()->getTargetUrl());
            }
        }

        return view('pos.create', [
            'customer' => $customer,
            'carts' => $contents
        ]);
    }
}
