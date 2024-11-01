function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    console.log(`Code matched = ${decodedText}`, decodedResult);
    $('#search').val(decodedText);
    $('#search-btn').trigger('click');
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
    $('#scanner').toggle();
    $('.scan-btn').on('click', function(){
       $('#scanner').toggle();
    });
});