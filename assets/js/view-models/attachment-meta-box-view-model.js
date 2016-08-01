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
    function CustomUrlAttachmentsViewModel(parent) {
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
        ko.mapping.fromJS(this.parent.options.attachmentPreload, {}, this.collection);
    };
    CustomUrlAttachmentsViewModel.prototype.add = function () {
        this.collection.push(new CustomInputAttachment('', ''));
    };
    CustomUrlAttachmentsViewModel.prototype.remove = function (item) {
        this.collection.remove(item);
    };
    return CustomUrlAttachmentsViewModel;
}());
/**
 * WordPress Attachment Type.
 */
var WordPressMediaAttachmentsViewModel = (function () {
    function WordPressMediaAttachmentsViewModel(parent) {
        var _this = this;
        /** Active View **/
        this.view = ko.observable('grid');
        /** WordPress Media Collection */
        this.collection = ko.observableArray([]);
        /**
         * Ordered/filtered collection.
         * @type {KnockoutComputed<Array>}
         */
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
        /**
         * An array of WordPress Post IDs.
         * @type {KnockoutObservableArray<number>}
         */
        this.attachmentIds = ko.observableArray([]);
        /**
         * A map of available file extension icons/graphics by key.
         * @type {{aac: string, ai: string, aiff: string, asp: string, avi: string, bmp: string, c: string, cpp: string, css: string, dat: string, dmg: string, doc: string, docx: string, dot: string, dotx: string, dwg: string, dxf: string, eps: string, exe: string, flv: string, gif: string, h: string, html: string, ics: string, iso: string, java: string, jpg: string, key: string, m4v: string, mid: string, mov: string, mp3: string, mp4: string, mpg: string, odp: string, ods: string, odt: string, otp: string, ots: string, ott: string, pdf: string, php: string, png: string, pps: string, ppt: string, pptx: string, psd: string, pt: string, qt: string, rar: string, rb: string, rtf: string, sql: string, tga: string, tgz: string, tiff: string, txt: string, wav: string, xls: string, xlsx: string, xml: string, yml: string, zip: string}}
         */
        this.icons = {
            aac: 'aac',
            ai: 'ai',
            aiff: 'aiff',
            asp: 'asp',
            avi: 'avi',
            bmp: 'bmp',
            c: 'c',
            cpp: 'cpp',
            css: 'css',
            dat: 'dat',
            dmg: 'dmg',
            doc: 'doc',
            docx: 'docx',
            dot: 'dot',
            dotx: 'dotx',
            dwg: 'dwg',
            dxf: 'dxf',
            eps: 'eps',
            exe: 'exe',
            flv: 'flv',
            gif: 'gif',
            h: 'h',
            html: 'html',
            ics: 'ics',
            iso: 'iso',
            java: 'java',
            jpg: 'jpg',
            key: 'key',
            m4v: 'm4v',
            mid: 'mid',
            mov: 'mov',
            mp3: 'mp3',
            mp4: 'mp4',
            mpg: 'mpg',
            odp: 'odp',
            ods: 'ods',
            odt: 'odt',
            otp: 'otp',
            ots: 'ots',
            ott: 'ott',
            pdf: 'pdf',
            php: 'php',
            png: 'png',
            pps: 'pps',
            ppt: 'ppt',
            pptx: 'ppt',
            psd: 'psd',
            pt: 'pt',
            qt: 'qt',
            rar: 'rar',
            rb: 'rb',
            rtf: 'rtf',
            sql: 'sql',
            tga: 'tga',
            tgz: 'tgz',
            tiff: 'tiff',
            txt: 'txt',
            wav: 'wav',
            xls: 'xls',
            xlsx: 'xlsx',
            xml: 'xml',
            yml: 'yml',
            zip: 'zip'
        };
        this.value = ko.pureComputed(function () {
            return _this.attachmentIds().join(',');
        });
        // Link the parent node view model so we can share data.
        this.parent = parent;
        // Reapply sortables on 'view' changes.
        this.view.subscribe(function () {
            _this.applySortables();
        });
        this.applySortables();
    }
    /**
     * Prints the available thumbnail graphic or a file extension graphic.
     * @param model
     * @returns {string}
     */
    WordPressMediaAttachmentsViewModel.prototype.renderIcon = function (model) {
        var file = model.filename, index = file.lastIndexOf('.'), ext = file.substr(index + 1), path = this.icons[ext] ?
            wordpress_helpers.assets + 'img/file-type-icons/' + this.icons[ext] + '.png' :
            wordpress_helpers.assets + 'img/no-image-150.png';
        return "<img src=\"" + path + "\">";
    };
    /**
     * Detach a model from the collection.
     * @param model
     */
    WordPressMediaAttachmentsViewModel.prototype.removeModel = function (model) {
        this.collection.remove(model);
        this.attachmentIds.remove(model.id);
    };
    /**
     * WordPress media query select callback.
     */
    WordPressMediaAttachmentsViewModel.prototype.onSelect = function () {
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
    /**
     * Initialize the WordPress media frame.
     */
    WordPressMediaAttachmentsViewModel.prototype.initializeFrame = function () {
        if (!this.frame) {
            var self_1 = this;
            this.frame = new wp.media.view.MediaFrame.Select({
                // Modal title
                title: 'Select profile background',
                // Enable/disable multiple select
                multiple: self_1.parent.options.multiple,
                // Library WordPress query arguments.
                library: {
                    order: 'ASC',
                    // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                    // 'id', 'post__in', 'menuOrder' ]
                    orderby: 'title',
                    // mime type. e.g. 'image', 'image/jpeg'
                    type: self_1.parent.options.attachmentType,
                    // Searches the attachment title.
                    search: null,
                    // Attached to a specific post (ID).
                    uploadedTo: null
                },
                button: {
                    text: self_1.parent.options.attachmentButtonText
                }
            });
            // Fires when a user has selected attachment(s) and clicked the select button.
            // @see media.view.MediaFrame.Post.mainInsertToolbar()
            this.frame.on('select', function () {
                self_1.onSelect();
            });
        }
    };
    WordPressMediaAttachmentsViewModel.prototype.selectAttachment = function () {
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
    /** Apply jQuery sortables. */
    WordPressMediaAttachmentsViewModel.prototype.applySortables = function () {
        var _this = this;
        jQuery("#" + this.parent.options.elementId + " .attachment-models").sortable({
            // Placeholder class.
            placeholder: 'attachment-model-highlight',
            // The move item handle.
            handle: '.move-button',
            // Listen to update changes so that we can update the attachment ID order.
            update: function (event, ui) {
                var divs = jQuery("#" + _this.parent.options.elementId + " .attachment-model-container"), order = [];
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
    /**
     * Initialize the WordPress
     */
    WordPressMediaAttachmentsViewModel.prototype.initialize = function () {
        var _this = this;
        // Loop through preload items.
        _.each(this.parent.options.attachmentPreload, function (model) {
            // If the model ID is not already in the attachment IDs array.
            if (-1 == _this.attachmentIds.indexOf(model.id)) {
                _this.collection.push(model);
                _this.attachmentIds.push(model.id);
            }
        });
    };
    return WordPressMediaAttachmentsViewModel;
}());
/**
 * The main Attachment Metabox View Model. This class provides an abstraction layer
 * through WordPressMediaAttachments or CustomInputAttachments.
 */
var AttachmentMetaboxViewModel = (function () {
    function AttachmentMetaboxViewModel(options) {
        var _this = this;
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
            wp: new WordPressMediaAttachmentsViewModel(this),
            url: new CustomUrlAttachmentsViewModel(this)
        };
        // Setup the object.
        this.initialize();
        // Initialize knockout.
        ko.applyBindings(this, document.getElementById(options.elementId));
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
