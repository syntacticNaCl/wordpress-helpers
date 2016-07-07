var KnockoutComponent = (function () {
    function KnockoutComponent() {
    }
    KnockoutComponent.prototype.register = function () {
        // register the component
        ko.components.register(this.tag, {
            viewModel: this.viewModel,
            template: this.template
        });
    };
    return KnockoutComponent;
}());

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoButtonViewModel = (function () {
    function KoButtonViewModel(args) {
        // The default button text state.
        this.text = 'Set Text';
        this.id = '';
        this.icon = false;
        // The text when the button is busy.
        this.busyText = 'Set Busy Text';
        // The action related to the button is being performed, ie AJAX.
        this.busy = ko.observable(false);
        // The class attribute on the button.
        this.class = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    // Default click function.
    KoButtonViewModel.prototype.click = function () {
        alert('Pass a click function to the button!');
    };
    // Generates the button's classes.
    KoButtonViewModel.prototype.getClass = function () {
        return "btn " + this.class;
    };
    KoButtonViewModel.prototype.getIcon = function () {
        return this.icon;
    };
    return KoButtonViewModel;
}());
// ko-button Template
var KoButtonTemplate = "\n    <button class=\"btn\" data-bind=\"disable: busy(), click: click, attr: { class: getClass(), id: id }\">\n        <i class=\"fa fa-spinner fa-spin\" data-bind=\"visible: busy()\"></i>\n        <span data-bind=\"text: busy() ? busyText : text\"></span>\n        <!-- ko if: false != icon -->\n        <i class=\"fa\" data-bind=\"css: getIcon()\"></i>\n        <!-- /ko -->\n    </button>\n";
var KnockoutButtonComponent = (function (_super) {
    __extends(KnockoutButtonComponent, _super);
    function KnockoutButtonComponent() {
        _super.call(this);
        this.tag = 'ko-button';
        this.template = KoButtonTemplate;
        this.viewModel = KoButtonViewModel;
        this.register();
    }
    return KnockoutButtonComponent;
}(KnockoutComponent));
new KnockoutButtonComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoCheckboxViewModel = (function () {
    function KoCheckboxViewModel(args) {
        this.checked = ko.observable(false);
        this.label = '';
        this.name = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    return KoCheckboxViewModel;
}());
// ko-button Template
var KoCheckboxTemplate = "\n<div class=\"checkbox\">\n\t<label>\n\t\t<input type=\"checkbox\" data-bind=\"attr: { name: name }, checked: checked\">\n\t\t<!-- ko text: label --><!-- /ko -->\n\t</label>\n</div>\n";
var KnockoutCheckboxComponent = (function (_super) {
    __extends(KnockoutCheckboxComponent, _super);
    function KnockoutCheckboxComponent() {
        _super.call(this);
        this.tag = 'ko-checkbox';
        this.template = KoCheckboxTemplate;
        this.viewModel = KoCheckboxViewModel;
        this.register();
    }
    return KnockoutCheckboxComponent;
}(KnockoutComponent));
new KnockoutCheckboxComponent();

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoInputDateViewModel = (function () {
    function KoInputDateViewModel(args) {
        var _this = this;
        this.value = ko.observable('');
        // A text list of months.
        this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        // A list of day counts.
        this.dayCount = {
            29: ['February'],
            30: ['April', 'June', 'September', 'November'],
            31: ['January', 'March', 'May', 'July', 'August', 'October', 'December']
        };
        /**
         * Returns the day range limit for a given month.
         * @type {KnockoutComputed<Array>}
         */
        this.dayRange = ko.pureComputed(function () {
            var range = 31, month = _this.selectedMonth(), i = 1, options = [];
            _.each(_this.dayCount, function (months, dayCount) {
                if (-1 != months.indexOf(month)) {
                    range = dayCount;
                }
            });
            while (i <= range) {
                options.push(i);
                i++;
            }
            return options;
        });
        this.yearRange = _.range(1930, 2016);
        this.selectedMonth = ko.observable('');
        this.selectedDay = ko.observable('');
        this.selectedYear = ko.observable('');
        this.dateString = ko.pureComputed(function () {
            var m = _this.selectedMonth(), d = _this.selectedDay(), y = _this.selectedYear();
            // Convert month name to number.
            m = _this.months.indexOf(m) + 1;
            // Prepend zeros, if necessary.
            d = (d < 10 ? "0" + d : d);
            m = (m < 10 ? "0" + m : m);
            return y + "-" + m + "-" + d;
        });
        // Validation properties.
        this.isValidMonth = ko.observable(true);
        this.isValidDay = ko.observable(true);
        this.isValidYear = ko.observable(true);
        this.validator = ko.observableArray([]);
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
        // Listen to changes in the validator observableArray passed to the component.
        this.validator.subscribe(function () {
            _this.validate();
        });
        // When the computed dateString is generated, update the value observable passed
        // to the component.
        this.dateString.subscribe(function (dateString) {
            _this.value(dateString);
        });
        this.setInitialDate();
    }
    KoInputDateViewModel.prototype.setInitialDate = function () {
        var date = this.value().split('-');
        if (date.length == 3) {
            this.selectedMonth(this.months[new Number(date[1]) - 1]);
            this.selectedDay(new Number(date[2]));
            this.selectedYear(new Number(date[0]));
        }
    };
    // Validate the date properties.
    KoInputDateViewModel.prototype.validate = function () {
        this.isValidDay(this.selectedDay() != undefined);
        this.isValidMonth(this.selectedMonth() != undefined);
        this.isValidYear(this.selectedYear() != undefined);
    };
    return KoInputDateViewModel;
}());
// ko-button Template
var KoInputDateTemplate = "\n<div class=\"row\">\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidMonth() }\">\n            <label class=\"control-label\">Month</label>\n            <select class=\"form-control\" data-bind=\"options: months, value: selectedMonth, optionsCaption: 'Month'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidMonth()\">Select a month</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidDay() }\">\n            <label class=\"control-label\">Day</label>\n            <select class=\"form-control\" data-bind=\"options: dayRange, value: selectedDay, optionsCaption: 'Day'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidDay()\">Select a day</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidYear() }\">\n            <label class=\"control-label\">Year</label>\n            <select class=\"form-control\" data-bind=\"options: yearRange, value: selectedYear, optionsCaption: 'Year'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidYear()\">Select a year</span>\n        </div>\n    </div>\n</div>\n";
var KnockoutDateComponent = (function (_super) {
    __extends(KnockoutDateComponent, _super);
    function KnockoutDateComponent() {
        _super.call(this);
        this.tag = 'ko-input-date';
        this.template = KoInputDateTemplate;
        this.viewModel = KoInputDateViewModel;
        this.register();
    }
    return KnockoutDateComponent;
}(KnockoutComponent));
new KnockoutDateComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoInputDatetimeViewModel = (function () {
    function KoInputDatetimeViewModel(args) {
        var _this = this;
        this.value = ko.observable('');
        // A text list of months.
        this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        // A list of day counts.
        this.dayCount = {
            29: ['February'],
            30: ['April', 'June', 'September', 'November'],
            31: ['January', 'March', 'May', 'July', 'August', 'October', 'December']
        };
        this.options = {
            hours: ['12', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'],
            minutes: [
                '00', '01', '02', '03', '04', '05', '06', '07', '08', '09',
                '10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
                '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
                '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
                '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
                '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
            ],
            ampm: ['AM', 'PM']
        };
        /**
         * Returns the day range limit for a given month.
         * @type {KnockoutComputed<Array>}
         */
        this.dayRange = ko.pureComputed(function () {
            var range = 31, month = _this.selectedMonth(), i = 1, options = [];
            _.each(_this.dayCount, function (months, dayCount) {
                if (-1 != months.indexOf(month)) {
                    range = dayCount;
                }
            });
            while (i <= range) {
                options.push(i);
                i++;
            }
            return options;
        });
        this.yearRange = _.range(1930, 2017);
        this.selectedMonth = ko.observable('');
        this.selectedDay = ko.observable('');
        this.selectedYear = ko.observable('');
        this.selectedHour = ko.observable('');
        this.selectedMinute = ko.observable('');
        this.selectedAMPM = ko.observable('');
        /**
         * Compute date string: YYYY-MM-DD
         * @type {KnockoutComputed<string>}
         */
        this.dateString = ko.pureComputed(function () {
            var m = _this.selectedMonth(), d = _this.selectedDay(), y = _this.selectedYear();
            // Convert month name to number.
            m = _this.months.indexOf(m) + 1;
            // Prepend zeros, if necessary.
            d = (d < 10 ? "0" + d : d);
            m = (m < 10 ? "0" + m : m);
            return y + "-" + m + "-" + d;
        });
        this.timeString = ko.pureComputed(function () {
            var hour = _this.selectedHour(), minute = _this.selectedMinute(), ampm = _this.selectedAMPM();
            hour = ampm == 'PM' ? Number(hour) + 12 : hour;
            return hour + ":" + minute + ":00";
        });
        // Validation properties.
        this.isValidMonth = ko.observable(true);
        this.isValidDay = ko.observable(true);
        this.isValidYear = ko.observable(true);
        this.isValidHour = ko.observable(true);
        this.isValidMinute = ko.observable(true);
        this.isValidAMPM = ko.observable(true);
        this.validator = ko.observableArray([]);
        this.computedString = ko.pureComputed(function () {
            return _this.dateString() + " " + _this.timeString();
        });
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
        // Listen to changes in the validator observableArray passed to the component.
        this.validator.subscribe(function () {
            _this.validate();
        });
        // When the computed dateString is generated, update the value observable passed
        // to the component.
        this.computedString.subscribe(function (dateString) {
            _this.value(dateString);
        });
        this.setInitialDatetime();
    }
    KoInputDatetimeViewModel.prototype.setInitialDatetime = function () {
        var inputString = this.value().split(' '), date = inputString[0].split('-'), time = inputString[1].split(':');
        // Set date
        this.selectedMonth(this.months[Number(date[1]) - 1]);
        this.selectedDay(Number(date[2]));
        this.selectedYear(Number(date[0]));
        // Get time.
        var hour = Number(time[0]) > 12 ? Number(time[0]) - 12 : Number(time[0]), minute = time[1], ampm = Number(time[0]) > 12 ? 'PM' : 'AM';
        // Set time.
        this.selectedHour(hour);
        this.selectedMinute(minute);
        this.selectedAMPM(ampm);
    };
    // Validate the date properties.
    KoInputDatetimeViewModel.prototype.validate = function () {
        this.isValidDay(this.selectedDay() != undefined);
        this.isValidMonth(this.selectedMonth() != undefined);
        this.isValidYear(this.selectedYear() != undefined);
        this.isValidHour(this.selectedHour() != undefined);
        this.isValidMinute(this.selectedMinute() != undefined);
        this.isValidAMPM(this.selectedAMPM() != undefined);
    };
    return KoInputDatetimeViewModel;
}());
// ko-button Template
var KoInputDatetimeTemplate = "\n<!--\n<div class=\"form-group\">\n    <div class=\"btn-group\">\n        <button type=\"button\" class=\"btn btn-default btn-sm\">Set <i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i></button>\n        <button type=\"button\" class=\"btn btn-default btn-sm dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">\n            <span class=\"caret\"></span>\n            <span class=\"sr-only\">Toggle Dropdown</span>\n        </button>\n        <ul class=\"dropdown-menu\">\n            <li><a href=\"#\">Action</a></li>\n            <li><a href=\"#\">Another action</a></li>\n            <li><a href=\"#\">Something else here</a></li>\n            <li role=\"separator\" class=\"divider\"></li>\n            <li><a href=\"#\">Separated link</a></li>\n        </ul>\n    </div>\n</div>\n-->\n<div class=\"row\">\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidMonth() }\">\n            <label class=\"control-label\">Month</label>\n            <select class=\"form-control\" data-bind=\"options: months, value: selectedMonth, optionsCaption: 'Month'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidMonth()\">Select a month</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidDay() }\">\n            <label class=\"control-label\">Day</label>\n            <select class=\"form-control\" data-bind=\"options: dayRange, value: selectedDay, optionsCaption: 'Day'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidDay()\">Select a day</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidYear() }\">\n            <label class=\"control-label\">Year</label>\n            <select class=\"form-control\" data-bind=\"options: yearRange, value: selectedYear, optionsCaption: 'Year'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidYear()\">Select a year</span>\n        </div>\n    </div>\n</div>\n<div class=\"row\">\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidHour() }\">\n            <label class=\"control-label\">Hour</label>\n            <select class=\"form-control\" data-bind=\"options: options.hours, value: selectedHour, optionsCaption: 'Hour'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidHour()\">Select an hour</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidMinute() }\">\n            <label class=\"control-label\">Minute</label>\n            <select class=\"form-control\" data-bind=\"options: options.minutes, value: selectedMinute, optionsCaption: 'Minute'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidMinute()\">Select a minute</span>\n        </div>\n    </div>\n    <div class=\"col-xs-4\">\n        <div class=\"form-group\" data-bind=\"css: { 'has-error': ! isValidAMPM() }\">\n            <label class=\"control-label\">AM/PM</label>\n            <select class=\"form-control\" data-bind=\"options: options.ampm, value: selectedAMPM, optionsCaption: 'AM/PM'\"></select>\n            <span class=\"help-block\" data-bind=\"if: !isValidAMPM()\">Select am/pm</span>\n        </div>\n    </div>\n</div>\n";
var KnockoutDatetimeComponent = (function (_super) {
    __extends(KnockoutDatetimeComponent, _super);
    function KnockoutDatetimeComponent() {
        _super.call(this);
        this.tag = 'ko-datetime';
        this.template = KoInputDatetimeTemplate;
        this.viewModel = KoInputDatetimeViewModel;
        this.register();
    }
    return KnockoutDatetimeComponent;
}(KnockoutComponent));
new KnockoutDatetimeComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoInputViewModel = (function () {
    function KoInputViewModel(args) {
        // If the eye icon is being hovered.
        this.beingHovered = ko.observable(false);
        // The password value, should be passed via params.
        this.value = ko.observable('');
        this.validator = ko.observableArray([]);
        this.icon = false;
        this.type = 'input';
        this.placeholder = '';
        this.label = '';
        this.name = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    KoInputViewModel.prototype._in = function () {
        this.beingHovered(true);
    };
    KoInputViewModel.prototype._out = function () {
        this.beingHovered(false);
    };
    KoInputViewModel.prototype.css = function () {
        var css = {};
        css[this.icon] = true;
        return css;
    };
    return KoInputViewModel;
}());
// ko-button Template
var KoInputTemplate = "\n<div class=\"form-group\" data-bind=\"css: { 'has-error': validator().length > 0 }\">\n    <label class=\"control-label\" data-bind=\"text: label\"></label>\n    <div class=\"\" data-bind=\"css: { 'input-group': false != icon }\">\n        <input class=\"form-control\" data-bind=\"attr: { type: beingHovered() ? 'text' : 'password', placeholder: placeholder, type: type, name: name }, textInput: value\">\n        <span class=\"input-group-addon\" data-bind=\"visible: false != icon, event: { mouseover: _in, mouseout: _out }\">\n            <i class=\"fa\" data-bind=\"css: css()\"></i>\n        </span>\n    </div>\n    <div data-bind=\"if: validator().length > 0\">\n        <span class=\"help-block\" data-bind=\"foreach: validator\">\n            <p data-bind=\"text: $data\"></p>\n        </span>\n    </div>\n</div>\n";
var KnockoutInputComponent = (function (_super) {
    __extends(KnockoutInputComponent, _super);
    function KnockoutInputComponent() {
        _super.call(this);
        this.tag = 'ko-input';
        this.template = KoInputTemplate;
        this.viewModel = KoInputViewModel;
        this.register();
    }
    return KnockoutInputComponent;
}(KnockoutComponent));
new KnockoutInputComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoPasswordViewModel = (function () {
    function KoPasswordViewModel(args) {
        // If the eye icon is being hovered.
        this.beingHovered = ko.observable(false);
        // The password value, should be passed via params.
        this.value = ko.observable('');
        this.validator = ko.observableArray([]);
        this.label = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    KoPasswordViewModel.prototype._in = function () {
        this.beingHovered(true);
    };
    KoPasswordViewModel.prototype._out = function () {
        this.beingHovered(false);
    };
    return KoPasswordViewModel;
}());
// ko-button Template
var KoPasswordTemplate = "\n<div class=\"form-group\" data-bind=\"css: { 'has-error': validator().length > 0 }\">\n    <label class=\"control-label\" data-bind=\"text: label\"></label>\n    <div class=\"input-group\">\n        <input class=\"form-control\" placeholder=\"Password\" data-bind=\"attr: { type: beingHovered() ? 'text' : 'password' }, textInput: value\">\n        <span class=\"input-group-addon\" data-bind=\"event: { mouseover: _in, mouseout: _out }\">\n            <i class=\"fa\" data-bind=\"css: { 'fa-eye-slash': !beingHovered(), 'fa-eye': beingHovered() }\"></i>\n        </span>\n    </div>\n    <div data-bind=\"if: validator().length > 0\">\n        <span class=\"help-block\" data-bind=\"foreach: validator\">\n            <p data-bind=\"text: $data\"></p>\n        </span>\n    </div>\n</div>\n";
var KnockoutPasswordComponent = (function (_super) {
    __extends(KnockoutPasswordComponent, _super);
    function KnockoutPasswordComponent() {
        _super.call(this);
        this.tag = 'ko-password';
        this.template = KoPasswordTemplate;
        this.viewModel = KoPasswordViewModel;
        this.register();
    }
    return KnockoutPasswordComponent;
}(KnockoutComponent));
new KnockoutPasswordComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoSelectViewModel = (function () {
    function KoSelectViewModel(args) {
        // The password value, should be passed via params.
        this.value = ko.observable('');
        this.validator = ko.observableArray([]);
        this.label = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    return KoSelectViewModel;
}());
// ko-button Template
var KoSelectTemplate = "\n<div class=\"form-group\" data-bind=\"css: { 'has-error': validator().length > 0 }\">\n    <label class=\"control-label\" data-bind=\"text: label\"></label>\n    <select class=\"form-control\" data-bind=\"options: options, value: value\"></select>\n    <div data-bind=\"if: validator().length > 0\">\n        <span class=\"help-block\" data-bind=\"foreach: validator\">\n            <p data-bind=\"text: $data\"></p>\n        </span>\n    </div>\n</div>\n";
var KnockoutSelectComponent = (function (_super) {
    __extends(KnockoutSelectComponent, _super);
    function KnockoutSelectComponent() {
        _super.call(this);
        this.tag = 'ko-select';
        this.template = KoSelectTemplate;
        this.viewModel = KoSelectViewModel;
        this.register();
    }
    return KnockoutSelectComponent;
}(KnockoutComponent));
new KnockoutSelectComponent;

var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
// ko-button ViewModel
var KoTextareaViewModel = (function () {
    function KoTextareaViewModel(args) {
        this.rows = 3;
        // If the eye icon is being hovered.
        this.beingHovered = ko.observable(false);
        // The password value, should be passed via params.
        this.value = ko.observable('');
        this.validator = ko.observableArray([]);
        this.type = 'input';
        this.placeholder = '';
        this.label = '';
        var object = this;
        _.each(args, function (value, key) {
            object[key] = args[key] ? args[key] : object[key];
        });
    }
    KoTextareaViewModel.prototype._in = function () {
        this.beingHovered(true);
    };
    KoTextareaViewModel.prototype._out = function () {
        this.beingHovered(false);
    };
    return KoTextareaViewModel;
}());
// ko-button Template
var KoTextareaTemplate = "\n<div class=\"form-group\" data-bind=\"css: { 'has-error': validator().length > 0 }\">\n    <label class=\"control-label\" data-bind=\"text: label\"></label>\n    <textarea class=\"form-control\" data-bind=\"textInput: value, placeholder: placeholder, attr: { rows: rows ? rows : 3 }\"></textarea>\n    <div data-bind=\"if: validator().length > 0\">\n        <span class=\"help-block\" data-bind=\"foreach: validator\">\n            <p data-bind=\"text: $data\"></p>\n        </span>\n    </div>\n</div>\n";
var KnockoutTextareaComponent = (function (_super) {
    __extends(KnockoutTextareaComponent, _super);
    function KnockoutTextareaComponent() {
        _super.call(this);
        this.tag = 'ko-textarea';
        this.template = KoTextareaTemplate;
        this.viewModel = KoTextareaViewModel;
        this.register();
    }
    return KnockoutTextareaComponent;
}(KnockoutComponent));
new KnockoutTextareaComponent;

//# sourceMappingURL=zawntech-knockout-components.js.map
