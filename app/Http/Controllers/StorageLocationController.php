<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per_page parameter must be an integer between 1 and 100.');
        }

        $storageLocations = StorageLocation::filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('storageLocations.index', [
            'storageLocations' => $storageLocations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('storageLocations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
            'stock' => 'required|numeric|min:0',
            'stock_remain' => 'required|numeric|min:0|lte:stock',
        ];

        $validatedData = $request->validate($rules);

        StorageLocation::create($validatedData);

        return Redirect::route('storage-locations.index')->with('success', 'Tạo vị trí thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StorageLocation $storageLocation)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StorageLocation $storageLocation)
    {
        return view('storageLocations.edit', [
            'storageLocation' => $storageLocation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StorageLocation $storageLocation)
    {
        $rules = [
            'name' => 'required|unique:categories,name,' . $storageLocation->id,
            'stock' => 'required|numeric|min:0',
            'stock_remain' => 'required|numeric|min:0|lte:stock',
        ];

        $validatedData = $request->validate($rules);

        StorageLocation::where('id', $storageLocation->id)->update($validatedData);

        return Redirect::route('storage-locations.index')->with('success', 'Update vị trí thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StorageLocation $storageLocation)
    {
        StorageLocation::destroy($storageLocation->id);

        return Redirect::route('storage-locations.index')->with('success', 'Xoá vị trí thành công!');
    }
}
