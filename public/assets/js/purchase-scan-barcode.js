function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    console.log(`Code matched = ${decodedText}`, decodedResult);

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
    alert(`Error ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "scanner",
    {
        fps: 10,
        qrbox: {width: 50, height: 50}
    },
    false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);

$(function () {

});