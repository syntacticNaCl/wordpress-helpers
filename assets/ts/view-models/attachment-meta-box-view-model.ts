class CustomInputAttachment
{
    url: KnockoutObservable<string>;
    label: KockoutObservable<string>;

    constructor(url = '', label? = '')
    {
        this.url = ko.observable(url);
        this.label = ko.observable(label);
    }
}

class CustomUrlAttachmentsViewModel
{
    parent: AttachmentMetaboxViewModel;

    collection = ko.observableArray([]);

    value = ko.pureComputed(() => {

        let output = [];

        // Exclude empty or garbage values.
        _.each( this.collection(), (model) => {
            if ( 0 != model.url().trim().length ) {
                output.push(model);
            }
        });

        return ko.mapping.toJSON( output );
    });

    // Initialize the custom url attachments view model
    initialize()
    {
        ko.mapping.fromJS( this.parent.options.attachmentPreload, {}, this.collection );
    }

    add() {
        this.collection.push( new CustomInputAttachment('', '') );
    }

    remove(item) {
        this.collection.remove(item);
    }

    constructor(parent: AttachmentMetaboxViewModel)
    {
        // Assign parent object.
        this.parent = parent;

        // Add a default input.
        this.collection.push( new CustomInputAttachment('', '') );

        // Listen to collection changes.
        this.collection.subscribe(() =>
        {
            // Always display at least 1 entry.
            if ( this.collection().length == 0 )
            {
                this.collection.push( new CustomInputAttachment('', '') );
            }
        })
    }
}

/**
 * WordPress Attachment Type.
 */
class WordPressMediaAttachmentsViewModel
{
    constructor(parent: AttachmentMetaboxViewModel)
    {
        // Link the parent node view model so we can share data.
        this.parent = parent;

        // Reapply sortables on 'view' changes.
        this.view.subscribe(() => {
            this.applySortables();
        });

        this.applySortables();
    }

    parent: AttachmentMetaboxViewModel;

    /** WordPress Media Frame */
    frame;

    /** Active View **/
    view = ko.observable('grid');

    /** WordPress Media Collection */
    collection = ko.observableArray([]);

    /**
     * Ordered/filtered collection.
     * @type {KnockoutComputed<Array>}
     */
    orderedCollection = ko.pureComputed(() =>
    {
        let output = [];

        _.each( this.attachmentIds(), (id) => {
            _.each( this.collection(), (model) => {
                if ( model.id == id ) {
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
    attachmentIds = ko.observableArray([]);

    /**
     * A map of available file extension icons/graphics by key.
     * @type {{aac: string, ai: string, aiff: string, asp: string, avi: string, bmp: string, c: string, cpp: string, css: string, dat: string, dmg: string, doc: string, docx: string, dot: string, dotx: string, dwg: string, dxf: string, eps: string, exe: string, flv: string, gif: string, h: string, html: string, ics: string, iso: string, java: string, jpg: string, key: string, m4v: string, mid: string, mov: string, mp3: string, mp4: string, mpg: string, odp: string, ods: string, odt: string, otp: string, ots: string, ott: string, pdf: string, php: string, png: string, pps: string, ppt: string, pptx: string, psd: string, pt: string, qt: string, rar: string, rb: string, rtf: string, sql: string, tga: string, tgz: string, tiff: string, txt: string, wav: string, xls: string, xlsx: string, xml: string, yml: string, zip: string}}
     */
    icons = {
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
        zip: 'zip',
    };

    /**
     * Prints the available thumbnail graphic or a file extension graphic.
     * @param model
     * @returns {string}
     */
    renderIcon(model)
    {
        let file = model.filename,
            index = file.lastIndexOf('.'),
            ext = file.substr(index + 1),
            path = this.icons[ext] ?
            wordpress_helpers.assets + 'img/file-type-icons/' + this.icons[ext] + '.png' :
            wordpress_helpers.assets + 'img/no-image-150.png';

        return `<img src="${path}">`;
    }

    /**
     * Detach a model from the collection.
     * @param model
     */
    removeModel(model) {
        this.collection.remove(model);
        this.attachmentIds.remove(model.id);
    }

    /**
     * WordPress media query select callback.
     */
    onSelect()
    {
        let models = this.frame.state().get('selection').toJSON();

        _.each(models, (model) =>
        {
            // If the model ID is not already in the attachment IDs array.
            if ( -1 == this.attachmentIds.indexOf(model.id) ) {
                this.collection.push(model);
                this.attachmentIds.push(model.id);
            }
        });

        this.applySortables();
    }

    /**
     * Initialize the WordPress media frame.
     */
    initializeFrame()
    {
        if ( ! this.frame )
        {
            let self = this;

            this.frame = new wp.media.view.MediaFrame.Select({
                // Modal title
                title: 'Select profile background',

                // Enable/disable multiple select
                multiple: self.parent.options.multiple,

                // Library WordPress query arguments.
                library: {
                    order: 'ASC',

                    // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                    // 'id', 'post__in', 'menuOrder' ]
                    orderby: 'title',

                    // mime type. e.g. 'image', 'image/jpeg'
                    type: self.parent.options.attachmentType,

                    // Searches the attachment title.
                    search: null,

                    // Attached to a specific post (ID).
                    uploadedTo: null
                },

                button: {
                    text: self.parent.options.attachmentButtonText
                }
            });

            // Fires when a user has selected attachment(s) and clicked the select button.
            // @see media.view.MediaFrame.Post.mainInsertToolbar()
            this.frame.on( 'select', function() {
                self.onSelect();
            } );
        }
    }

    selectAttachment() {

        // Verify that the media frame is initialized.
        this.initializeFrame();

        let frame = this.frame;

        // Get an object representing the current state.
        frame.state();

        // Get an object representing the previous state.
        frame.lastState();

        // Open the modal.
        frame.open();
    }

    /** Apply jQuery sortables. */
    applySortables()
    {
        jQuery(`#${this.parent.options.elementId} .attachment-models`).sortable(
        {
            // Placeholder class.
            placeholder: 'attachment-model-highlight',

            // The move item handle.
            handle: '.move-button',

            // Listen to update changes so that we can update the attachment ID order.
            update: (event, ui) =>
            {
                let divs = jQuery(`#${this.parent.options.elementId} .attachment-model-container`),
                    order = [];

                // Loop through the divs, extract data-id attributes.
                _.each(divs, (item) => {

                    // Get attachment ID.
                    let attachmentId = jQuery(item).data('id');

                    // Skip if already in array.
                    if ( -1 != order.indexOf( attachmentId ) ) {
                        return;
                    }

                    // Push to order.
                    order.push( attachmentId );
                });

                ko.mapping.fromJS( order, {}, this.attachmentIds );
            }
        });
    }

    /**
     * Initialize the WordPress
     */
    initialize()
    {
        // Loop through preload items.
        _.each( this.parent.options.attachmentPreload, (model) => {

            // If the model ID is not already in the attachment IDs array.
            if ( -1 == this.attachmentIds.indexOf(model.id) ) {
                this.collection.push(model);
                this.attachmentIds.push(model.id);
            }
        });
    }

    value = ko.pureComputed(() =>
    {
        return this.attachmentIds().join(',');
    })
}

/**
 * The options object via AttachmentMetaBox passed to the main class constructor.
 * See /views/admin/meta-boxes/attachment-meta-box.php.
 */
interface AttachmentOptionsInterface
{
    postId: number;
    elementId: string;
    postType: string;
    multiple: boolean;
    attachmentType: string;

    attachmentButtonText: string;

    /**
     * Data retrieved from DB.
     */
    attachmentPreload: any;

    /**
     * The "type" of attachment meta box ('wp' or 'url').
     */
    type: string;
}

/**
 * The main Attachment Metabox View Model. This class provides an abstraction layer
 * through WordPressMediaAttachments or CustomInputAttachments.
 */
class AttachmentMetaboxViewModel
{
    options: AttachmentOptionsInterface;

    /**
     * The attachment meta box source type, defaults to 'wp'.
     * Available types: 'wp', 'url'
     * @type {KnockoutObservable<string>}
     */
    type = ko.observable('');

    /**
     * Returns the attachment meta box's calculated value.
     *
     * If the attachment type is 'wp', then we'll get a comma separated list of integers,
     * for example: "1,2,3" which corresponds to individual WordPress Post IDs.
     *
     *
     * @type {KnockoutComputed<string>}
     */
    valueString = ko.pureComputed(() =>
    {
        return this.types[this.type()].value();
    });

    /**
     * Instantiate the available "types" of attachment meta box views.
     * @type {{wp: WordPressMediaAttachmentsViewModel, url: CustomUrlAttachmentsViewModel}}
     */
    types = {
        wp: WordPressMediaAttachmentsViewModel,
        url: CustomUrlAttachmentsViewModel
    };

    /**
     * Initialize the attachment meta box type.
     */
    initialize()
    {
        // Get the type.
        let type = this.type();

        // Initialize the type.
        this.types[type].initialize();
    }

    constructor(options: AttachmentOptionsInterface)
    {
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
        ko.applyBindings( this, document.getElementById( options.elementId ) );
    }
}