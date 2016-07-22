var AttachmentMetaboxViewModel = (function () {
    function AttachmentMetaboxViewModel(options) {
        var _this = this;
        this.collection = ko.observableArray([]);
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
    return AttachmentMetaboxViewModel;
}());
