/**
 * An extensible View Model with build-in validation support.
 */
var ValidatableViewModel = (function () {
    function ValidatableViewModel() {
        this.busy = ko.observable(false);
    }
    ValidatableViewModel.prototype.clearValidation = function () {
        var _this = this;
        _.each(this.rules, function (rules, key) {
            _this[key + '_validation'].removeAll();
        });
    };
    ValidatableViewModel.prototype.getData = function () {
        var _this = this;
        var data = {};
        _.each(this.rules, function (rules, key) {
            data[key] = _this[key]();
        });
        return data;
    };
    ValidatableViewModel.prototype.validate = function () {
        var _this = this;
        var data = {}, errors;
        // Loop through the validation rule keys, get current observable values.
        _.each(this.rules, function (rules, key) {
            data[key] = _this[key]();
        });
        // Clear validation rules.
        this.clearValidation();
        // Validate.
        errors = validate(data, this.rules);
        // If there are errors, push them to their respective observable array.
        if (errors) {
            _.each(errors, function (errors, key) {
                _.each(errors, function (error) {
                    _this[key + '_validation'].push(error);
                });
            });
            return false;
        }
        // All good.
        return true;
    };
    ValidatableViewModel.prototype.initialize = function () {
        var _this = this;
        // Loop through the view model's rules.
        _.each(this.rules, function (rules, key) {
            // Create an observable for this key which holds the input/form value.
            _this[key] = ko.observable('');
            // Create an observable array, which holds the validation rules.
            _this[key + '_validation'] = ko.observableArray([]);
        });
        // If a form trigger element is specified, bind a function to the viewModel
        // "keydown", so we can automatically trigger forms when the user hits enter.
        if (this.formTrigger) {
            this.keydown = function (self, event) {
                if (13 == event.keyCode) {
                    jQuery(_this.formTrigger).trigger('click');
                    return false;
                }
                return true;
            };
        }
    };
    return ValidatableViewModel;
}());
