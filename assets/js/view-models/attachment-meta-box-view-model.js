/**
 * The main Attachment Metabox View Model. This class provides an abstraction layer
 * through WordPressMediaAttachments or CustomInputAttachments.
 */
var AttachmentMetaboxViewModel = (function () {
    function AttachmentMetaboxViewModel(options, autoBind) {
        var _this = this;
        if (autoBind === void 0) { autoBind = true; }
        /**
         * The attachment meta box source type, defaults to 'wp'.
         * Available types: 'wp', 'url'
         * @type {KnockoutObservable<string>}
         */
        this.type = ko.observable('');
        /**
         * Returns the attachment meta box's calculated value.
         *
         * If the attachment type is 'wp', then we'll get a comma separated list of integers,
         * for example: "1,2,3" which corresponds to individual WordPress Post IDs.
         *
         *
         * @type {KnockoutComputed<string>}
         */
        this.valueString = ko.pureComputed(function () {
            return _this.types[_this.type()].value();
        });
        /**
         * Instantiate the available "types" of attachment meta box views.
         * @type {{wp: WordPressMediaAttachmentsViewModel, url: CustomUrlAttachmentsViewModel}}
         */
        this.types = {
            wp: WordPressMediaAttachmentsViewModel,
            url: CustomUrlAttachmentsViewModel
        };
        // Set options.
        this.options = options;
        // Set the type.
        this.type(options.type);
        this.types = {
            wp: new WordPressMediaAttachmentsViewModel(this, options),
            url: new CustomUrlAttachmentsViewModel(this, options)
        };
        // Setup the object.
        this.initialize();
        // Initialize knockout.
        if (autoBind) {
            ko.applyBindings(this, document.getElementById(options.elementId));
        }
    }
    /**
     * Initialize the attachment meta box type.
     */
    AttachmentMetaboxViewModel.prototype.initialize = function () {
        // Get the type.
        var type = this.type();
        // Initialize the type.
        this.types[type].initialize();
    };
    return AttachmentMetaboxViewModel;
}());
