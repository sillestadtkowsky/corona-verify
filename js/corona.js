jQuery(document).ready(function () {
    console.log("loaded")
});

function defer(method) {
    if (window.jQuery) {
        method();
    } else {
        setTimeout(function () { defer(method) }, 50);
    }
}