interface AttachmentOptionsInterface
{
    key: string;
    label: string;
    multiple: boolean;
    type: string;
    preload: any;
    sourceType: string;
}

interface MultipleAttachmentOptionsInterface
{
    postId: number;
    elementId: string;
    options: AttachmentOptionsInterface[];
}

class Attachment
{
    constructor(parent, options: AttachmentOptionsInterface)
    {
        this.parent = parent;
        this.key = options.key;
        this.label = options.label;
        this.type( options.sourceType || options.type );
        this.types = {
            wp: new WordPressMediaAttachmentsViewModel(parent, options),
            url: new CustomUrlAttachmentsViewModel(parent, options)
        };

        alert( this.type() );

        this.types[ this.type() ].initialize();
    }

    key;
    label;
    parent;
    types;
    type = ko.observable('');

    valueString = ko.pureComputed(() => {
        return this.types[this.type()].value();
    });
}

class MultipleAttachmentMetaboxViewModel
{
    data: MultipleAttachmentOptionsInterface;

    options = ko.observableArray([]);

    // The currently active meta key.
    activeKey  = ko.observable('');

    initialize()
    {
        // Reference the options passed to constructor.
        let options = this.data.options;

        _.each( options, (option) =>
        {
            this.options.push(new Attachment(this, option));
        });

        // Set active key.
        this.activeKey( options[0].key );
    }

    constructor(data: MultipleAttachmentOptionsInterface)
    {
        // Set options.
        this.data = data;

        // Initialize metabox.
        this.initialize();

        // Initialize knockout.
        ko.applyBindings( this, document.getElementById( data.elementId ) );
    }
}