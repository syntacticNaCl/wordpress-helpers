var IOProgressBar = (function () {
    function IOProgressBar() {
    }
    IOProgressBar.logOutput = function (message) {
        return jQuery('#io-output').append("<li>" + message + "</li>");
    };
    IOProgressBar.bar = function () {
        return jQuery('div#io-progress');
    };
    IOProgressBar.message = function (message) {
        return jQuery('#io-progress-message').html("<b>" + message + "</b>");
    };
    IOProgressBar.updateCounter = function () {
        var total = IOProgressBar.total, i = IOProgressBar.iteration;
        jQuery('#io-progress-counter').html(i + " of " + total);
    };
    IOProgressBar.updateWidth = function () {
        var i = IOProgressBar.iteration, total = IOProgressBar.total, percentage = (i / total) * 100;
        // If total is zero, override.
        if (0 == total) {
            percentage = 0;
        }
        IOProgressBar.bar().css('width', percentage + '%');
        IOProgressBar.updateCounter();
    };
    IOProgressBar.bump = function () {
        IOProgressBar.iteration++;
        IOProgressBar.updateWidth();
    };
    IOProgressBar.reset = function (newTotal) {
        if (newTotal === void 0) { newTotal = 0; }
        IOProgressBar.total = newTotal;
        IOProgressBar.iteration = 0;
        IOProgressBar.updateWidth();
    };
    IOProgressBar.total = 0;
    IOProgressBar.iteration = 0;
    return IOProgressBar;
}());
