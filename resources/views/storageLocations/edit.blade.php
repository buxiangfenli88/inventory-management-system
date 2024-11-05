@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-folder"></i></div>
                        Sửa vị trí
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('storage-locations.index') }}">Vị trí</a></li>
                    <li class="breadcrumb-item active">Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('storage-locations.update', $storageLocation->id) }}" method="POST">
        @csrf
        @method('put')
        <div class="row">

            <div class="col-xl-3"></div>

            <div class="col-xl-6">
                <!-- BEGIN: Category Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Vị trí
                    </div>
                    <div class="card-body">
                        <!-- Form Group (name) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="name">Tên vị trí<span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('name') is-invalid @enderror" id="name" name="name" type="text" placeholder="" value="{{ old('name', $storageLocation->name) }}" autocomplete="off" />
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (stock) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="stock">Số lượng khả dụng</label>
                            <input class="form-control form-control-solid @error('stock') is-invalid @enderror" id="stock" name="stock" type="text" placeholder="" value="{{ old('stock', $storageLocation->stock) }}" />
                            @error('stock')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">Update</button>
                        <a class="btn btn-danger" href="{{ route('storage-locations.index') }}">Cancel</a>
                    </div>
                </div>
                <!-- END: Category Details -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->

<script>
    // stock Generator
    // const title = document.querySelector("#name");
    // const stock = document.querySelector("#stock");
    // title.addEventListener("keyup", function() {
    //     let preslug = title.value;
    //     preslug = preslug.replace(/ /g,"-");
    //     stock.value = preslug.toLowerCase();
    // });
</script>
@endsection
