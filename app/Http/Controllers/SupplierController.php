<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class SupplierController extends Controller
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

        $query = Supplier::query();

        $startData = Carbon::now()->setTimezone('+7')->startOfDay();
        $endData = $startData->clone()->endOfDay();

        if (auth()->user()->hasRole(UserRole::GUARD)) {
            $query->whereBetween('created_at', [$startData->utc(), $endData->utc()]);
        }

        $suppliers = $query->filter(request(['search']))
            ->sortable()->paginate($row)
            ->appends(request()->query());

        return view('suppliers.index', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'nullable|email|max:50|unique:suppliers,email',
            'phone' => 'required|string|max:25',
            'shopname' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:25',
            'account_holder' => 'max:50',
            'account_number' => 'max:25',
            'bank_name' => 'max:25',
            'address' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'bien_so_xe' => 'required|string|max:40',
            'so_kien_giao' => 'nullable|integer',
            'note' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload an image
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/suppliers/';

            /**
             * Store an image to Storage.
             */
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
            $validatedData['created_by'] = auth()->user->id;
        }

        $supplier = Supplier::create($validatedData);

//        return Redirect::route('suppliers.downloadSupplier', ['supplier_id' => $supplier->id]);

        return Redirect::route('suppliers.index')->with('success', 'New supplier has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', [
            'supplier' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'nullable|email|max:50|unique:suppliers,email,'.$supplier->id,
            'phone' => 'required|string|max:25',
            'shopname' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:25',
            'account_holder' => 'max:50',
            'account_number' => 'max:25',
            'bank_name' => 'max:25',
            'address' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'bien_so_xe' => 'required|string|max:40',
            'so_kien_giao' => 'nullable|integer',
            'note' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/suppliers/';

            /**
             * Delete an image if exists.
             */
            if($supplier->photo){
                Storage::delete($path . $supplier->photo);
            }

            // Store an image to Storage
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Supplier::where('id', $supplier->id)->update($validatedData);

        return Redirect::route('suppliers.index')->with('success', 'Supplier has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        /**
         * Delete photo if exists.
         */
        if($supplier->photo){
            Storage::delete('public/suppliers/' . $supplier->photo);
        }

        Supplier::destroy($supplier->id);

        return Redirect::route('suppliers.index')->with('success', 'Supplier has been deleted!');
    }

    public function downloadSupplier(Int $supplierId)
    {
        $supplier = Supplier::where('id', $supplierId)->firstOrFail();

        return view('suppliers.print-supplier', [
            'supplier' => $supplier,
        ]);
    }
}
