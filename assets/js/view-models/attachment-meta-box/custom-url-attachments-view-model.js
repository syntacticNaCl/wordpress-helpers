var CustomInputAttachment = (function () {
    function CustomInputAttachment(url, label) {
        if (url === void 0) { url = ''; }
        if (label === void 0) { label = ''; }
        this.url = ko.observable(url);
        this.label = ko.observable(label);
    }
    return CustomInputAttachment;
}());
var CustomUrlAttachmentsViewModel = (function () {
    function CustomUrlAttachmentsViewModel(parent, options) {
        var _this = this;
        this.collection = ko.observableArray([]);
        this.value = ko.pureComputed(function () {
            var output = [];
            // Exclude empty or garbage values.
            _.each(_this.collection(), function (model) {
                if (0 != model.url().trim().length) {
                    output.push(model);
                }
            });
            return ko.mapping.toJSON(output);
        });
        // Assign parent object.
        this.parent = parent;
        this.multiple = options.multiple;
        this.preload = options.preload ? options.preload : options.attachmentPreload;
        this.elementId = options.elementId ? options.elementId : options.key + '_tab_panel';
        // Add a default input.
        this.collection.push(new CustomInputAttachment('', ''));
        // Listen to collection changes.
        this.collection.subscribe(function () {
            // Always display at least 1 entry.
            if (_this.collection().length == 0) {
                _this.collection.push(new CustomInputAttachment('', ''));
            }
        });
    }
    // Initialize the custom url attachments view model
    CustomUrlAttachmentsViewModel.prototype.initialize = function () {
        ko.mapping.fromJS(this.preload, {}, this.collection);
    };
    CustomUrlAttachmentsViewModel.prototype.add = function () {
        this.collection.push(new CustomInputAttachment('', ''));
    };
    CustomUrlAttachmentsViewModel.prototype.remove = function (item) {
        this.collection.remove(item);
    };
    return CustomUrlAttachmentsViewModel;
}());
