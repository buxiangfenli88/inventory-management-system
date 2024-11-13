<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStorageLocation;
use App\Models\StorageLocation;
use App\Services\StorageLocationService;
use Exception;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PurchaseController extends Controller
{
    protected $storageLocationService;

    public function __construct(StorageLocationService $storageLocationService)
    {
        $this->storageLocationService = $storageLocationService;
    }

    /**
     * Display an all purchases.
     */
    public function allPurchases()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $purchases = Purchase::with(['supplier'])
            ->roleFilter(auth()->user())
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('purchases.purchases', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Display an all approved purchases.
     */
    public function approvedPurchases()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $purchases = Purchase::with(['supplier'])
            ->roleFilter(auth()->user())
            ->where('purchase_status', 1) // 1 = approved
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('purchases.approved-purchases', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Display a purchase details.
     */
    public function purchaseDetails(string $purchase_id)
    {
        $purchase = Purchase::with(['supplier', 'user_created', 'user_updated'])
            ->roleFilter(auth()->user())
            ->where('id', $purchase_id)
            ->firstOrFail();

        $purchaseDetails = PurchaseDetails::with('product')
            ->where('purchase_id', $purchase_id)
            ->orderBy('id')
            ->get();

        return view('purchases.details-purchase', [
            'purchase' => $purchase,
            'purchaseDetails' => $purchaseDetails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createPurchase()
    {
        return view('purchases.create-purchase', [
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
            'storageLocations' => StorageLocation::query()->where('stock_remain', '>', 0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function storePurchase(Request $request)
    {
        $rules = [
            'supplier_id' => 'required|string',
            'purchase_date' => 'required|string',
            'total_amount' => 'required|numeric',
        ];

        $purchase_no = IdGenerator::generate([
            'table' => 'purchases',
            'field' => 'purchase_no',
            'length' => 10,
            'prefix' => 'PRS-',
        ]);

        $validatedData = $request->validate($rules);

        $validatedData['purchase_status'] = 0; // 0 = pending, 1 = approved
        $validatedData['purchase_no'] = $purchase_no;
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['created_at'] = Carbon::now();

        try {
            DB::transaction(function () use ($validatedData, $request) {
                $purchase_id = Purchase::insertGetId($validatedData);

                // Create Purchase Details
                $pDetails = array();
                $products = count($request->product_id);

                for ($i = 0; $i < $products; $i++) {
                    $pDetails['purchase_id'] = $purchase_id;
                    $pDetails['product_id'] = $request->product_id[$i];
                    $pDetails['quantity'] = $request->quantity[$i];
                    $pDetails['unitcost'] = $request->unitcost[$i];
                    $pDetails['total'] = $request->total[$i];
                    $pDetails['storage_location_id'] = $request->storage_location_id[$i];
                    $pDetails['created_at'] = Carbon::now();

                    /* @var StorageLocation $storageLocation */
                    $storageLocation = StorageLocation::findOrFail($request->storage_location_id[$i]);

                    if ((int)$pDetails['quantity'] > $storageLocation->stock_remain) {
                        throw ValidationException::withMessages(['errorMessage' => $storageLocation->name . ' không đủ só lượng chứa hàng']);
                    }

//                    // update storage location when input product
//                    $this->storageLocationService->inputStorageLocation(
//                        $pDetails['product_id'],
//                        $storageLocation->id,
//                        $pDetails['quantity']
//                    );

                    PurchaseDetails::insert($pDetails);
                }
            });
        } catch (ValidationException $e) {
            return Redirect::back()->withInput($request->all())->withErrors($e->errors());
        }

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been created!');
    }

    /**
     * Handle update a status purchase
     */
    public function updatePurchase(Request $request)
    {
        $purchase_id = $request->id;
        /* @var Purchase $purchase */
        $purchase = Purchase::findOrFail($purchase_id);

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->purchaseDetails as $purchaseDetail) {

                /* @var StorageLocation $storageLocation */
                $storageLocation = StorageLocation::findOrFail($purchaseDetail->storage_location_id);

                if ($purchaseDetail->quantity > $storageLocation->stock_remain) {
                    throw ValidationException::withMessages(
                        [
                            'invalidStock' => '<strong>' . $purchaseDetail->product->product_name . '</strong>'
                                . ' vượt quá số lượng khả dụng ở vị trí '
                                . '<strong>' . $purchaseDetail->storageLocation->name . '</strong>',
                        ]
                    )->redirectTo(Redirect::back()->getTargetUrl());
                }

                Product::query()
                    ->where('id', $purchaseDetail->product_id)
                    ->increment('stock', $purchaseDetail->quantity);

                // update storage location when input product
                $this->storageLocationService->inputStorageLocation(
                    $purchaseDetail->product_id,
                    $purchaseDetail->storage_location_id,
                    $purchaseDetail->quantity
                );
            }

            Purchase::findOrFail($purchase->id)
                ->update([
                    'purchase_status' => 1,
                    'updated_by' => auth()->user()->id,
                ]); // 1 = approved, 0 = pending
        });

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been approved!');
    }

    /**
     * Handle delete a purchase
     */
    public function deletePurchase(string $purchaseId)
    {
        DB::transaction(function () use ($purchaseId) {
            PurchaseDetails::where('purchase_id', $purchaseId)->delete();

            Purchase::where([
                'id' => $purchaseId,
                'purchase_status' => '0',
            ])->delete();
        });

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been deleted!');
    }

    /**
     * Display an all purchases.
     */
    public function dailyPurchaseReport()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $purchases = Purchase::with(['supplier'])
            ->roleFilter(auth()->user())
            ->filter(request(['search']))
            ->where('purchase_date', Carbon::now()->format('Y-m-d')) // 1 = approved
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('purchases.purchases', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Show the form input date for purchase report.
     */
    public function getPurchaseReport()
    {
        return view('purchases.report-purchase');
    }

    /**
     * Handle request to get purchase report
     */
    public function exportPurchaseReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        // $purchaseDetails = DB::table('purchases')
        //     ->whereBetween('purchases.purchase_date',[$sDate,$eDate])
        //     ->where('purchases.purchase_status','1')
        //     ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
        //     ->get();

        $purchaseDetails = PurchaseDetails::query()->with(['product', 'purchase', 'storageLocation'])
            ->join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->whereBetween('purchases.purchase_date', [$sDate, $eDate])
            ->where('purchases.purchase_status', '1')
            ->get();


        $purchase_array [] = array(
            'Ngày',
            'No Purchase',
            'Người giao',
            'Product Code',
            'Tên sản phẩm',
            'Số lượng',
            'Vị trí',
//            'Unitcost',
//            'Total',
        );

        /* @var PurchaseDetails $purchaseDetail */
        foreach ($purchaseDetails as $purchaseDetail) {
            $purchase_array[] = array(
                'Date' => $purchaseDetail->purchase->purchase_date,
                'No Purchase' => $purchaseDetail->purchase->purchase_no,
                'Người giao' => $purchaseDetail->purchase->supplier->name,
                'Product Code' => $purchaseDetail->product->product_code,
                'Tên sản phẩm' => $purchaseDetail->product->product_name,
                'Số lượng' => $purchaseDetail->quantity,
                'Vị trí' => $purchaseDetail->storageLocation->name,
//                'Unitcost' => $purchase->unitcost,
//                'Total' => $purchase->total,
            );
        }

        $this->exportExcel($purchase_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xlsx($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="purchase-report.xlsx"');
            header('Cache-Control: max-age=0');
//            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function downloadPurchases(int $purchase_id)
    {
        $purchase = Purchase::with('supplier')->where('id', $purchase_id)->first();
        $purchaseDetails = PurchaseDetails::with('product')
            ->where('purchase_id', $purchase_id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('purchases.print-purchase', [
            'purchase' => $purchase,
            'purchaseDetails' => $purchaseDetails,
        ]);
    }
}
