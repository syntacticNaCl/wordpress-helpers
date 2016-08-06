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

    clear() {
        this.types.url.collection.removeAll();
        this.types.wp.collection.removeAll();
        this.types.wp.attachmentIds.removeAll();

    }

    constructor(options: AttachmentOptionsInterface, autoBind = true)
    {
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
        if ( autoBind )
        {
            ko.applyBindings( this, document.getElementById( options.elementId ) );
        }
    }
}