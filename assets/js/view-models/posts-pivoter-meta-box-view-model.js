var PostsPivoterViewModel = (function () {
    function PostsPivoterViewModel(options) {
        var _this = this;
        /**
         * The currently views screen: 'all', 'related'
         * @type {KnockoutObservable<string>}
         */
        this.screen = ko.observable('all');
        /**
         * Represents a busy state when doing ajax calls.
         * @type {KnockoutObservable<boolean>}
         */
        this.loading = ko.observable(true);
        /**
         * A collection of post models.
         * @type {KnockoutObservableArray<object>}
         */
        this.collection = ko.observableArray([]);
        /**
         * Search input string.
         * @type {KnockoutObservable<string>}
         */
        this.search = ko.observable('');
        /**
         * Show the search form?
         * @type {KnockoutObservable<boolean>}
         */
        this.showSearch = ko.observable(false);
        this.filteredCollection = ko.pureComputed(function () {
            // Declare an array for filtered output.
            var filtered = [], search = _this.search().trim().toLowerCase();
            // Loop through items.
            _.each(_this.filteredViewCollection(), function (model) {
                // Search the string.
                if ('' != search && _this.showSearch()) {
                    if (-1 != model.title().toLowerCase().search(search)) {
                        filtered.push(model);
                    }
                }
                else {
                    filtered.push(model);
                }
            });
            return filtered;
        });
        /**
         * Filter models by the current view, ie: 'all', 'related'
         * @type {KnockoutComputed<Array>}
         */
        this.filteredViewCollection = ko.pureComputed(function () {
            // Declare an array for filtered output.
            var filtered = [];
            // Loop through items.
            _.each(_this.collection(), function (model) {
                // Are we viewing only related models?
                if ('related' == _this.screen()) {
                    // Check that model.id() exists in this.relatedIds
                    if (-1 != _this.relatedIds.indexOf(model.id())) {
                        filtered.push(model);
                    }
                    return;
                }
                if ('all' == _this.screen()) {
                    filtered.push(model);
                    return;
                }
            });
            return filtered;
        });
        // A list of related post IDs.
        this.relatedIds = ko.observableArray([]);
        // Inject options on instantiation.
        this.options = options;
        this.all();
        this.get();
        // Apply knockout bindings.
        ko.applyBindings(this, document.getElementById(options.elementId));
    }
    PostsPivoterViewModel.prototype.ajax = function (endpoint, data, success, fail) {
        // Declare the ajax action.
        data.action = 'posts_pivot_' + endpoint;
        var callback = function (r) {
            success(r);
        };
        jQuery
            .ajax(ajaxurl, {
            method: 'POST',
            data: data
        })
            .done(callback)
            .fail(fail);
    };
    PostsPivoterViewModel.prototype.all = function () {
        var _this = this;
        var data = this.options;
        this.ajax('all', data, function (r) {
            ko.mapping.fromJS(r, {}, _this.collection);
            _this.loading(false);
        });
    };
    PostsPivoterViewModel.prototype.get = function () {
        var _this = this;
        var data = this.options;
        this.ajax('get', data, function (r) {
            ko.mapping.fromJS(r, {}, _this.relatedIds);
            _this.loading(false);
        });
    };
    PostsPivoterViewModel.prototype.attach = function (model, button) {
        var _this = this;
        button.busy(true);
        var data = this.options;
        data.relatedId = model.id;
        var success = function (r) {
            // If the model id is not already present in the relatedIds array, push it.
            if (-1 == _this.relatedIds.indexOf(model.id())) {
                _this.relatedIds.push(model.id());
            }
            button.busy(false);
        };
        this.ajax('attach', data, success);
    };
    PostsPivoterViewModel.prototype.detach = function (model, button) {
        var _this = this;
        button.busy(true);
        var data = this.options;
        data.relatedId = model.id;
        var success = function (r) {
            // If the model id is not already present in the relatedIds array, push it.
            if (-1 != _this.relatedIds.indexOf(model.id())) {
                _this.relatedIds.remove(model.id());
            }
            button.busy(false);
        };
        this.ajax('detach', data, success);
    };
    return PostsPivoterViewModel;
}());
