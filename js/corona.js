jQuery(document).ready(function () {
    console.log("loaded")
    var cellWidth = jQuery((".divCell").css("width"));
    var tableWidth = jQuery((".divRow").css("width"));
    jQuery((".headerRow").css({
        "width": tableWidth,
        "position": "fixed"
    }).find(".divCell").css("width", cellWidth));
});

function defer(method) {
    if (window.jQuery) {
        method();
    } else {
        setTimeout(function() { defer(method) }, 50);
    }
}