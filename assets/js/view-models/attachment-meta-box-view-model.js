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
            if ('wp' == _this.attachmentSource()) {
                return _this.attachmentIds().join(',');
            }
            if ('url' == _this.attachmentSource()) {
                return _this.customInput();
            }
        });
        /**
         * The attachment meta box source, defaults to 'wp'.
         * @type {KnockoutObservable<string>}
         */
        this.attachmentSource = ko.observable('');
        this.customInput = ko.observable('');
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
        // Set options.
        this.options = options;
        this.attachmentSource(options.attachmentSource);
        // Load preload data.
        // If this is a URL, simply set the value.
        if ('url' == options.attachmentSource) {
            this.customInput(options.attachmentPreload);
        }
        else {
            // Loop through preload items.
            _.each(options.attachmentPreload, function (model) {
                // If the model ID is not already in the attachment IDs array.
                if (-1 == _this.attachmentIds.indexOf(model.id)) {
                    _this.collection.push(model);
                    _this.attachmentIds.push(model.id);
                }
            });
        }
        // Initialize knockout.
        ko.applyBindings(this, document.getElementById(options.elementId));
        // Reapply sortables on view changes.
        this.view.subscribe(function () {
            _this.applySortables();
        });
        this.applySortables();
    }
    AttachmentMetaboxViewModel.prototype.renderIcon = function (model) {
        var file = model.filename, index = file.lastIndexOf('.'), ext = file.substr(index + 1), path = this.icons[ext] ?
            wordpress_helpers.assets + 'img/file-type-icons/' + this.icons[ext] + '.png' :
            wordpress_helpers.assets + 'img/no-image-150.png';
        return "<img src=\"" + path + "\">";
    };
    AttachmentMetaboxViewModel.prototype.removeModel = function (model) {
        this.collection.remove(model);
        this.attachmentIds.remove(model.id);
    };
    AttachmentMetaboxViewModel.prototype.onSelect = function () {
        var _this = this;
        var models = this.frame.state().get('selection').toJSON();
        console.log(models);
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
