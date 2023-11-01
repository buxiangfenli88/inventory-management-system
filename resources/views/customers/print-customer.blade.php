<!DOCTYPE html>
<html lang="en">
<head>
    <title>Người Nhận</title>
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
                                    <h4 class="inv-title-1">Giờ Vào Kho:</h4>
                                    <p class="invo-addr-1">
                                        {{ $customer->created_at->timezone('+7')->format('d-m-Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-50">
                                <h4 class="inv-title-1">Người Giao:</h4>
                                <p class="inv-from-1">{{ $customer->name }}</p>
                                <p class="inv-from-1">{{ $customer->bien_so_xe }}</p>
{{--                                <p class="inv-from-1">{{ $order->customer->email }}</p>--}}
                                <p class="inv-from-2"><strong>Ghi chú: </strong></p>
                                <p class="inv-from-2">{{ $customer->note }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoice-btn-section clearfix d-print-none">
                    <a href="javascript:window.print()" class="btn btn-lg btn-print">
                        <i class="fa fa-print"></i> Print Invoice
                    </a>
                    <a id="invoice_download_btn" class="btn btn-lg btn-download">
                        <i class="fa fa-download"></i> Download Invoice
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
