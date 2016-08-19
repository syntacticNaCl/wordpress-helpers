var IOAjax = (function () {
    function IOAjax() {
    }
    IOAjax.post = function (action, data, success, fail) {
        var postData = {
            action: 'io_' + action
        };
        // Merge data.
        _.each(data, function (item, key) {
            postData[key] = item;
        });
        jQuery.post(ajaxurl, postData, function (r) {
            if (success) {
                success(r);
            }
        }).fail(function (r) {
            if (fail) {
                fail(r.responseJSON);
            }
        });
    };
    return IOAjax;
}());
