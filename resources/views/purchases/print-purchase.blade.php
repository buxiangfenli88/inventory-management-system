<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
</head>
<body>

<!-- BEGIN: Invoice -->
<div class="invoice-16 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner-9" id="invoice_wrapper">
                    <div class="invoice-top">
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="logo">
                                    <h1>TTC Bình Dương</h1>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="invoice">
                                    <h1>Phiếu Nhập # <span>{{ $purchase->purchase_no }}</span></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-info">
                        <div class="row">
                            <h4 class="inv-title-1">Nhân viên:</h4>
                            <p class="invo-addr-1">
                                {{ \Illuminate\Support\Facades\Auth::user()->name }}
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-50">
                                <div class="invoice-number">
                                    <h4 class="inv-title-1">Invoice date:</h4>
                                    <p class="invo-addr-1">
                                        {{ \Carbon\Carbon::now()->setTimezone('GMT+7') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-50">
                                <h4 class="inv-title-1">Customer</h4>
                                <p class="inv-from-1">{{ $purchase->supplier->name }}</p>
                                <p class="inv-from-1">{{ $purchase->supplier->phone }}</p>
{{--                                <p class="inv-from-1"><strong>Biển số xe:</strong> {{ $purchase->supplier->bien_so_xe }}</p>--}}
                                <p class="inv-from-2"><strong>Ghi chú:</strong> {{ $purchase->note }}</p>
                            </div>
                            <div class="col-sm-6 text-end mb-50">
                                <h4 class="inv-title-1">Store</h4>
                                <p class="inv-from-1">TTC Bình Dương</p>
{{--                                <p class="inv-from-1">(+62) 123 123 123</p>--}}
{{--                                <p class="inv-from-1">email@example.com</p>--}}
{{--                                <p class="inv-from-2">Cirebon, Jawa Barat, Indonesia</p>--}}
                            </div>
                        </div>
                    </div>
                    <div class="order-summary">
                        <div class="table-outer">
                            <table class="default-table invoice-table">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach ($purchaseDetails as $item)
                                <tr>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                                @endforeach

{{--                                <tr>--}}
{{--                                    <td><strong>Subtotal</strong></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td><strong>{{ $purchase->sub_total }}</strong></td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td><strong>Tax</strong></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td><strong>{{ $purchase->vat }}</strong></td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td><strong>Total</strong></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td></td>--}}
{{--                                    <td><strong>{{ $purchase->total }}</strong></td>--}}
{{--                                </tr>--}}
                                </tbody>
                            </table>
                        </div>
                    </div>
{{--                    <div class="invoice-informeshon-footer">--}}
{{--                        <ul>--}}
{{--                            <li><a href="#">www.website.com</a></li>--}}
{{--                            <li><a href="mailto:sales@hotelempire.com">info@example.com</a></li>--}}
{{--                            <li><a href="tel:+088-01737-133959">+62 123 123 123</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
                </div>
                <div class="invoice-btn-section clearfix d-print-none">
                    <a href="javascript:window.print()" class="btn btn-lg btn-print">
                        <i class="fa fa-print"></i> Print Invoice
                    </a>
                    <a id="invoice_download_btn" class="btn btn-lg btn-download">
                        <i class="fa fa-download"></i> Download Purchase
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Invoice -->

<script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
<script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
<script src="{{ asset('assets/invoice/js/app.js') }}"></script>

</body>
</html>
