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
    parent: AttachmentMetaboxViewModel|MultipleAttachmentMetaboxViewModel;

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
    initialize() {
        ko.mapping.fromJS( this.preload, {}, this.collection );
    }

    add() {
        this.collection.push( new CustomInputAttachment('', '') );
    }

    remove(item) {
        this.collection.remove(item);
    }

    preload;
    multiple;
    elementId;

    constructor(parent, options: AttachmentOptionsInterface)
    {
        // Assign parent object.
        this.parent = parent;

        this.multiple = options.multiple;
        this.preload = options.preload ? options.preload : options.attachmentPreload;
        this.elementId = options.elementId ? options.elementId : options.key + '_tab_panel';

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