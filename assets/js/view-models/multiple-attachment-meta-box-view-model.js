var Attachment = (function () {
    function Attachment(parent, options) {
        var _this = this;
        this.type = ko.observable('');
        this.valueString = ko.pureComputed(function () {
            return _this.types[_this.type()].value();
        });
        this.parent = parent;
        this.key = options.key;
        this.label = options.label;
        this.type(options.sourceType || options.type);
        this.types = {
            wp: new WordPressMediaAttachmentsViewModel(parent, options),
            url: new CustomUrlAttachmentsViewModel(parent, options)
        };
        this.types[this.type()].initialize();
    }
    return Attachment;
}());
var MultipleAttachmentMetaboxViewModel = (function () {
    function MultipleAttachmentMetaboxViewModel(data, autoBind) {
        if (autoBind === void 0) { autoBind = true; }
        this.options = ko.observableArray([]);
        // The currently active meta key.
        this.activeKey = ko.observable('');
        // Set options.
        this.data = data;
        // Initialize metabox.
        this.initialize();
        if (autoBind) {
            // Initialize knockout.
            ko.applyBindings(this, document.getElementById(data.elementId));
        }
    }
    MultipleAttachmentMetaboxViewModel.prototype.initialize = function () {
        var _this = this;
        // Reference the options passed to constructor.
        var options = this.data.options;
        _.each(options, function (option) {
            _this.options.push(new Attachment(_this, option));
        });
        // Set active key.
        this.activeKey(options[0].key);
    };
    return MultipleAttachmentMetaboxViewModel;
}());
