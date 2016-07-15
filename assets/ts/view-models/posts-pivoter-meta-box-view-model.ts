interface PostsPivoterOptionsInterface
{
    postId: number;
    elementId: string;
    postType: string;
    relatedType: string;
    relatedPostsCreator: boolean;
}

class CreateRelatedPostFormViewModel
{
    constructor(parent: PostsPivoterViewModel) {
        this.parent = parent;
    }

    parent: PostsPivoterViewModel;

    post_title = ko.observable('');
    post_content = ko.observable('');

    submit(btn)
    {
        // Set busy state to true.
        btn.busy(true);

        // Do ajax.
        this.parent.ajax(
            'create_related',
            {
                postId: this.parent.options.postId,
                relatedType: this.parent.options.relatedType,
                newPost: {
                    post_title: this.post_title(),
                    post_content: this.post_content()
                }
            },
            (r) => {
                // Loop through the posts returned by server.
                _.each(r.posts, (post) => {
                    // Add the current item to collection.
                    this.parent.collection.push( ko.mapping.fromJS(post) );
                    // Add the current post ID to related IDs.
                    this.parent.relatedIds.push(post.id);
                });

                // Done.
                btn.busy(false);
            }
        );
    }
}

class PostsPivoterViewModel
{
    creator: CreateRelatedPostFormViewModel;

    /**
     * Options passed to views which instantiate PostPivoterViewModel instances.
     */
    options: PostsPivoterOptionsInterface;

    /**
     * The currently views screen: 'all', 'related'
     * @type {KnockoutObservable<string>}
     */
    screen = ko.observable('all');

    /**
     * Represents a busy state when doing ajax calls.
     * @type {KnockoutObservable<boolean>}
     */
    loading = ko.observable(true);

    /**
     * A collection of post models.
     * @type {KnockoutObservableArray<object>}
     */
    collection = ko.observableArray([]);

    /**
     * Search input string.
     * @type {KnockoutObservable<string>}
     */
    search = ko.observable('');

    /**
     * Show the search form?
     * @type {KnockoutObservable<boolean>}
     */
    showSearch = ko.observable(false);

    /**
     * Show the related posts creator form?
     * @type {KnockoutObservable<boolean>}
     */
    showRelatedPostsCreator = ko.observable(false);

    filteredCollection = ko.pureComputed(() =>
    {
        // Declare an array for filtered output.
        let filtered = [],
            search = this.search().trim().toLowerCase();

        // Loop through items.
        _.each( this.filteredViewCollection(), (model) =>
        {
            // Search the string.
            if ( '' != search && this.showSearch() ) {
                if ( -1 != model.title().toLowerCase().search(search) ) {
                    filtered.push(model);
                }
            } else {
                filtered.push(model);
            }
        });

        return filtered;
    });

    /**
     * Filter models by the current view, ie: 'all', 'related'
     * @type {KnockoutComputed<Array>}
     */
    filteredViewCollection = ko.pureComputed(() =>
    {
        // Declare an array for filtered output.
        let filtered = [];

        // Loop through items.
        _.each( this.collection(), (model) => {

            // Are we viewing only related models?
            if ( 'related' == this.screen() ) {

                // Check that model.id() exists in this.relatedIds
                if ( -1 != this.relatedIds.indexOf( model.id() ) ) {
                    filtered.push(model);
                }
                return;
            }

            if ( 'all' == this.screen() ) {
                filtered.push(model);
                return;
            }
        });

        return filtered;
    });

    // A list of related post IDs.
    relatedIds = ko.observableArray([]);

    ajax(endpoint, data, success, fail?)
    {
        // Declare the ajax action.
        data.action = 'posts_pivot_' + endpoint;

        let callback = function(r) {
            success(r);
        };

        jQuery
            .ajax(ajaxurl, {
                method: 'POST',
                data: data
            })
            .done(callback)
            .fail(fail);
    }

    all() {
        let data = this.options;

        this.ajax('all', data, (r) =>
        {
            ko.mapping.fromJS( r, {}, this.collection );
            this.loading(false);
        });
    }

    get() {
        let data = this.options;

        this.ajax('get', data, (r) =>
        {
            ko.mapping.fromJS( r, {}, this.relatedIds );
            this.loading(false);
        });
    }

    attach(model, button)
    {
        button.busy(true);

        let data = this.options;

        data.relatedId = model.id;

        let success = (r) =>
        {
            // If the model id is not already present in the relatedIds array, push it.
            if ( -1 == this.relatedIds.indexOf(model.id()) )
            {
                this.relatedIds.push( model.id() );
            }

            button.busy(false);
        };

        this.ajax('attach', data, success);
    }

    detach(model, button)
    {
        button.busy(true);

        let data = this.options;

        data.relatedId = model.id;

        let success = (r) =>
        {
            // If the model id is not already present in the relatedIds array, push it.
            if ( -1 != this.relatedIds.indexOf(model.id()) )
            {
                this.relatedIds.remove( model.id() );
            }

            button.busy(false);
        };

        this.ajax('detach', data, success);
    }

    constructor(options)
    {
        // Inject options on instantiation.
        this.options = options;

        this.all();
        this.get();

        // Instantiate related posts creator.
        this.creator = new CreateRelatedPostFormViewModel(this);

        // Apply knockout bindings.
        ko.applyBindings( this, document.getElementById(options.elementId) );
    }
}