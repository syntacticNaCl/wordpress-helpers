/**
 * WordPress Attachment Type.
 */
class WordPressMediaAttachmentsViewModel
{
    constructor(parent, options: AttachmentOptionsInterface)
    {
        // Link the parent node view model so we can share data.
        this.parent = parent;

        // Set options.
        this.multiple = options.multiple;
        this.preload = options.preload ? options.preload : options.attachmentPreload;
        this.elementId = options.elementId ? options.elementId : options.key + '_tab_panel';

        // Reapply sortables on 'view' changes.
        this.view.subscribe(() => {
            this.applySortables();
        });

        this.applySortables();
    }
    
    elementId: string;
    preload = [];
    multiple = true;

    /**
     * Initialize the WordPress
     */
    initialize()
    {
        // Loop through preload items.
        _.each( this.preload, (model) => {

            // If the model ID is not already in the attachment IDs array.
            if ( -1 == this.attachmentIds.indexOf(model.id) ) {
                this.collection.push(model);
                this.attachmentIds.push(model.id);
            }
        });
    }

    parent: AttachmentMetaboxViewModel|MultipleAttachmentMetaboxViewModel;

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
                multiple: this.multiple,

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
        jQuery(`#${this.elementId} .attachment-models`).sortable(
            {
                // Placeholder class.
                placeholder: 'attachment-model-highlight',

                // The move item handle.
                handle: '.move-button',

                // Listen to update changes so that we can update the attachment ID order.
                update: (event, ui) =>
                {
                    let divs = jQuery(`#${this.elementId} .attachment-model-container`),
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

    value = ko.pureComputed(() =>
    {
        return this.attachmentIds().join(',');
    })
}