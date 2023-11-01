@extends('dashboard.body.main')

@section('specificpagescripts')
<script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endsection

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-users"></i></div>
                        Add Supplier
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile picture card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <!-- Profile picture image -->
                        <img class="img-account-profile rounded-circle mb-2" src="{{ asset('assets/img/demo/user-placeholder.svg') }}" alt="" id="image-preview" />
                        <!-- Profile picture help block -->
                        <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 1 MB</div>
                        <!-- Profile picture input -->
                        <input class="form-control form-control-solid mb-2 @error('photo') is-invalid @enderror" type="file"  id="image" name="photo" accept="image/*" onchange="previewImage();">
                        @error('photo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <!-- BEGIN: Supplier Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Supplier Details
                    </div>
                    <div class="card-body">
                        <!-- Form Group (name) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="name">Tên</label>
                            <input class="form-control form-control-solid @error('name') is-invalid @enderror" id="name" name="name" type="text" placeholder="" value="{{ old('name') }}" />
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="small mb-1" for="name">Biển số xe <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('bien_so_xe') is-invalid @enderror" id="bien_so_xe" name="bien_so_xe" type="text" placeholder="" value="{{ old('bien_so_xe') }}" />
                            @error('bien_so_xe')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Form Group (email address) -->
{{--                        <div class="mb-3">--}}
{{--                            <label class="small mb-1" for="email">Email address <span class="text-danger">*</span></label>--}}
{{--                            <input class="form-control form-control-solid @error('email') is-invalid @enderror" id="email" name="email" type="text" placeholder="" value="{{ old('email') }}" />--}}
{{--                            @error('email')--}}
{{--                            <div class="invalid-feedback">--}}
{{--                                {{ $message }}--}}
{{--                            </div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
                        <!-- Form Group (shopname) -->
{{--                        <div class="mb-3">--}}
{{--                            <label class="small mb-1" for="shopname">Shopname <span class="text-danger">*</span></label>--}}
{{--                            <input class="form-control form-control-solid @error('shopname') is-invalid @enderror" id="shopname" name="shopname" type="text" placeholder="" value="{{ old('shopname') }}" />--}}
{{--                            @error('shopname')--}}
{{--                            <div class="invalid-feedback">--}}
{{--                                {{ $message }}--}}
{{--                            </div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
                        <!-- Form Group (phone number) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="phone">Nhà cung cấp<span class="text-danger">*</span></label>
                            <select class="form-select form-control-solid @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option selected="" disabled="">Chọn nhà cung cấp:</option>
                                @foreach (\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1" for="phone">Số Điện Thoại <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('phone') is-invalid @enderror" id="phone" name="phone" type="text" placeholder="" value="{{ old('phone') }}" />
                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1" for="phone">Số kiện giao</label>
                            <input class="form-control form-control-solid @error('so_kien_giao') is-invalid @enderror" id="so_kien_giao" name="so_kien_giao" type="text" placeholder="" value="{{ old('so_kien_giao') }}" />
                            @error('so_kien_giao')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Row -->
{{--                        <div class="row gx-3 mb-3">--}}
{{--                            <!-- Form Group (type of supplier) -->--}}
{{--                            <div class="col-md-6">--}}
{{--                                <label class="small mb-1" for="type">Type of supplier <span class="text-danger">*</span></label>--}}
{{--                                <select class="form-select form-control-solid @error('type') is-invalid @enderror" id="type" name="type">--}}
{{--                                    <option selected="" disabled="">Select a type:</option>--}}
{{--                                    <option value="Distributor" @if(old('type') == 'Distributor')selected="selected"@endif>Distributor</option>--}}
{{--                                    <option value="Whole Seller" @if(old('type') == 'Whole Seller')selected="selected"@endif>Whole Seller</option>--}}
{{--                                </select>--}}
{{--                                @error('type')--}}
{{--                                <div class="invalid-feedback">--}}
{{--                                    {{ $message }}--}}
{{--                                </div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                            <!-- Form Group (bank name) -->--}}
{{--                            <div class="col-md-6">--}}
{{--                                <label class="small mb-1" for="bank_name">Bank Name</label>--}}
{{--                                <select class="form-select form-control-solid @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name">--}}
{{--                                    <option selected="" disabled="">Select a bank:</option>--}}
{{--                                    <option value="BRI" @if(old('bank_name') == 'BRI')selected="selected"@endif>BRI</option>--}}
{{--                                    <option value="BNI" @if(old('bank_name') == 'BNI')selected="selected"@endif>BNI</option>--}}
{{--                                    <option value="BCA" @if(old('bank_name') == 'BCA')selected="selected"@endif>BCA</option>--}}
{{--                                    <option value="BSI" @if(old('bank_name') == 'BSI')selected="selected"@endif>BSI</option>--}}
{{--                                    <option value="Mandiri" @if(old('bank_name') == 'Mandiri')selected="selected"@endif>Mandiri</option>--}}
{{--                                </select>--}}
{{--                                @error('bank_name')--}}
{{--                                <div class="invalid-feedback">--}}
{{--                                    {{ $message }}--}}
{{--                                </div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <!-- Form Row -->
{{--                        <div class="row gx-3 mb-3">--}}
{{--                            <!-- Form Group (account holder) -->--}}
{{--                            <div class="col-md-6">--}}
{{--                                <label class="small mb-1" for="account_holder">Account holder</label>--}}
{{--                                <input class="form-control form-control-solid @error('account_holder') is-invalid @enderror" id="account_holder" name="account_holder" type="text" placeholder="" value="{{ old('account_holder') }}" />--}}
{{--                                @error('account_holder')--}}
{{--                                <div class="invalid-feedback">--}}
{{--                                    {{ $message }}--}}
{{--                                </div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                            <!-- Form Group (account_name) -->--}}
{{--                            <div class="col-md-6">--}}
{{--                                <label class="small mb-1" for="account_number">Account number</label>--}}
{{--                                <input class="form-control form-control-solid @error('account_number') is-invalid @enderror" id="account_number" name="account_number" type="text" placeholder="" value="{{ old('account_number') }}" />--}}
{{--                                @error('account_number')--}}
{{--                                <div class="invalid-feedback">--}}
{{--                                    {{ $message }}--}}
{{--                                </div>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <!-- Form Group (address) -->
{{--                        <div class="mb-3">--}}
{{--                                <label for="address">Address <span class="text-danger">*</span></label>--}}
{{--                                <textarea class="form-control form-control-solid @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>--}}
{{--                                @error('address')--}}
{{--                                <div class="invalid-feedback">--}}
{{--                                    {{ $message }}--}}
{{--                                </div>--}}
{{--                                @enderror--}}
{{--                        </div>--}}
                        <div class="mb-3">
                            <label for="address">Note</label>
                            <textarea class="form-control form-control-solid @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">In</button>
                        <a class="btn btn-danger" href="{{ route('suppliers.index') }}">Cancel</a>
                    </div>
                </div>
                <!-- END: Supplier Details -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection