function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    // console.log(`Code matched = ${decodedText}`, decodedResult);
    $('#search').val(decodedText);
    $('#search-btn').trigger('click');

    html5QrcodeScanner.clear();
}

function onScanFailure(error) {
    // alert(`Error ${error}`);
}

const formatsToSupport = [
    Html5QrcodeSupportedFormats.CODE_128
];

let html5QrcodeScanner = new Html5QrcodeScanner(
    "scanner",
    {
        fps: 10,
        qrbox: {width: 400, height: 150},
        rememberLastUsedCamera: true,
        useBarCodeDetectorIfSupported: true,
        formatsToSupport: formatsToSupport
    }
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);

$(function () {
    $('#scanner').toggle();
    $('.scan-btn').on('click', function(){
       $('#scanner').toggle();
    });
});