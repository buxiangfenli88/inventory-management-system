function onScanSuccess(decodedText, decodedResult) {
    $.ajax({
        url: "/products/find-by-code?product_code=" + decodedText,
        type: "GET",
        success: function (data) {
            if (!data.category) {
                alert('Không tìm thấy sản phẩm!');
                return;
            }

            $('#scan_product_id').val(data.id);

            $('#category_id').val(data.category.id).change();
        }
    })
}

function onScanFailure(error) {
    // alert(`Error ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "scanner",
    {
        fps: 10,
        qrbox: {width: 250, height: 250}
    }
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);