interface AttachmentOptionsInterface
{
    postId: number;
    elementId: string;
    postType: string;
    multiple: boolean;
    attachmentType: string;
    attachmentButtonText: string;
    attachmentPreload: any[];
}

class AttachmentMetaboxViewModel
{
    frame;

    view = ko.observable('grid');

    options: AttachmentOptionsInterface;

    collection = ko.observableArray([]);

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

    attachmentIds = ko.observableArray([]);

    valueString = ko.pureComputed(() => {
        return this.attachmentIds().join(',');
    });

    removeModel(model) {
        this.collection.remove(model);
        this.attachmentIds.remove(model.id);
    }

    onSelect()
    {
        let models = this.frame.state().get('selection').toJSON();

        console.log(models);

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

    initializeFrame()
    {
        if ( ! this.frame )
        {
            let self = this;

            this.frame = new wp.media.view.MediaFrame.Select({
                // Modal title
                title: 'Select profile background',

                // Enable/disable multiple select
                multiple: self.options.multiple,

                // Library WordPress query arguments.
                library: {
                    order: 'ASC',

                    // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                    // 'id', 'post__in', 'menuOrder' ]
                    orderby: 'title',

                    // mime type. e.g. 'image', 'image/jpeg'
                    type: self.options.attachmentType,

                    // Searches the attachment title.
                    search: null,

                    // Attached to a specific post (ID).
                    uploadedTo: null
                },

                button: {
                    text: self.options.attachmentButtonText
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

    applySortables()
    {
        jQuery(`#${this.options.elementId} .attachment-models`).sortable(
        {
            // Placeholder class.
            placeholder: 'attachment-model-highlight',

            // The move item handle.
            handle: '.move-button',

            // Listen to update changes so that we can update the attachment ID order.
            update: (event, ui) =>
            {
                let divs = jQuery(`#${this.options.elementId} .attachment-model-container`),
                    models = [],
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

    constructor(options: AttachmentOptionsInterface)
    {
        // Set options.
        this.options = options;

        // Loop through preload items.
        _.each( options.attachmentPreload, (model) => {

            // If the model ID is not already in the attachment IDs array.
            if ( -1 == this.attachmentIds.indexOf(model.id) ) {
                this.collection.push(model);
                this.attachmentIds.push(model.id);
            }
        });

        // Initialize knockout.
        ko.applyBindings( this, document.getElementById( options.elementId ) );

        // Reapply sortables on view changes.
        this.view.subscribe(() => {
            this.applySortables();
        });

        this.applySortables();
    }
}