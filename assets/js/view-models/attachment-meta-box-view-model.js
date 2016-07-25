var AttachmentMetaboxViewModel = (function () {
    function AttachmentMetaboxViewModel(options) {
        var _this = this;
        this.view = ko.observable('grid');
        this.collection = ko.observableArray([]);
        this.orderedCollection = ko.pureComputed(function () {
            var output = [];
            _.each(_this.attachmentIds(), function (id) {
                _.each(_this.collection(), function (model) {
                    if (model.id == id) {
                        output.push(model);
                    }
                });
            });
            return output;
        });
        this.attachmentIds = ko.observableArray([]);
        this.valueString = ko.pureComputed(function () {
            return _this.attachmentIds().join(',');
        });
        // Set options.
        this.options = options;
        // Loop through preload items.
        _.each(options.attachmentPreload, function (model) {
            // If the model ID is not already in the attachment IDs array.
            if (-1 == _this.attachmentIds.indexOf(model.id)) {
                _this.collection.push(model);
                _this.attachmentIds.push(model.id);
            }
        });
        // Initialize knockout.
        ko.applyBindings(this, document.getElementById(options.elementId));
        // Reapply sortables on view changes.
        this.view.subscribe(function () {
            _this.applySortables();
        });
        this.applySortables();
    }
    AttachmentMetaboxViewModel.prototype.removeModel = function (model) {
        this.collection.remove(model);
        this.attachmentIds.remove(model.id);
    };
    AttachmentMetaboxViewModel.prototype.onSelect = function () {
        var _this = this;
        var models = this.frame.state().get('selection').toJSON();
        _.each(models, function (model) {
            // If the model ID is not already in the attachment IDs array.
            if (-1 == _this.attachmentIds.indexOf(model.id)) {
                _this.collection.push(model);
                _this.attachmentIds.push(model.id);
            }
        });
        this.applySortables();
    };
    AttachmentMetaboxViewModel.prototype.initializeFrame = function () {
        if (!this.frame) {
            var self_1 = this;
            this.frame = new wp.media.view.MediaFrame.Select({
                // Modal title
                title: 'Select profile background',
                // Enable/disable multiple select
                multiple: self_1.options.multiple,
                // Library WordPress query arguments.
                library: {
                    order: 'ASC',
                    // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                    // 'id', 'post__in', 'menuOrder' ]
                    orderby: 'title',
                    // mime type. e.g. 'image', 'image/jpeg'
                    type: self_1.options.attachmentType,
                    // Searches the attachment title.
                    search: null,
                    // Attached to a specific post (ID).
                    uploadedTo: null
                },
                button: {
                    text: self_1.options.attachmentButtonText
                }
            });
            // Fires when a user has selected attachment(s) and clicked the select button.
            // @see media.view.MediaFrame.Post.mainInsertToolbar()
            this.frame.on('select', function () {
                self_1.onSelect();
            });
        }
    };
    AttachmentMetaboxViewModel.prototype.selectAttachment = function () {
        // Verify that the media frame is initialized.
        this.initializeFrame();
        var frame = this.frame;
        // Get an object representing the current state.
        frame.state();
        // Get an object representing the previous state.
        frame.lastState();
        // Open the modal.
        frame.open();
    };
    AttachmentMetaboxViewModel.prototype.applySortables = function () {
        var _this = this;
        jQuery("#" + this.options.elementId + " .attachment-models").sortable({
            // Placeholder class.
            placeholder: 'attachment-model-highlight',
            // The move item handle.
            handle: '.move-button',
            // Listen to update changes so that we can update the attachment ID order.
            update: function (event, ui) {
                var divs = jQuery("#" + _this.options.elementId + " .attachment-model-container"), models = [], order = [];
                // Loop through the divs, extract data-id attributes.
                _.each(divs, function (item) {
                    // Get attachment ID.
                    var attachmentId = jQuery(item).data('id');
                    // Skip if already in array.
                    if (-1 != order.indexOf(attachmentId)) {
                        return;
                    }
                    // Push to order.
                    order.push(attachmentId);
                });
                ko.mapping.fromJS(order, {}, _this.attachmentIds);
            }
        });
    };
    return AttachmentMetaboxViewModel;
}());
