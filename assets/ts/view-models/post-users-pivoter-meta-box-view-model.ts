interface PostUsersPivoterMetaBoxOptions
{
    elementId: string;
    postId: number;
    multipleUsers: boolean;
}

/**
 * The Post Users Pivoter meta box view model.
 */
class PostUsersPivoterMetaBoxViewModel
{
    // Active view
    view = ko.observable('selected');
    selectionType = ko.observable('large');

    // View model options.
    options: PostUsersPivoterMetaBoxOptions;

    displaySearchForm = ko.observable('');
    searchInput = ko.observable('');

    // Loading states.
    loadingUsers = ko.observable(false);
    loadingAttachedUsers = ko.observable(false);

    // Available users.
    usersCollection = ko.observableArray([]);

    /** An array of attached user IDs. */
    attachedUserIds = ko.observableArray([]);

    attachedUsers = ko.pureComputed(() => {
        let users = [];
        _.each(this.attachedUserIds(), (id) => {
            _.each(this.usersCollection(), (user) => {
                if ( user.userId() == id ) {
                    users.push(user);
                }
            });
        });
        return users;
    });

    filteredUsers = ko.pureComputed(() =>
    {
        let output = [],
            users = this.usersCollection(),
            input = this.searchInput().trim(),
            caseInsensitive = true;

        // If there's no search input, return the entire collection.
        if ( '' == input ) {
            return users;
        }

        // If the filter is case insensitive, make lower case.
        if ( caseInsensitive ) {
            input = input.toLowerCase();
        }

        // Loop through users, apply filters.
        _.each( users, (user) => {

            // If a number is supplied as search input, filter user IDs.
            if ( ! isNaN( input ) ) {
                if ( input == user.userId() ) {
                    output.push( user );
                }
                return;
            }

            // Get string filterables.
            let email = user.email(),
                login = user.login(),
                match = false;

            if ( caseInsensitive ) {
                email = email.toLowerCase();
                login = login.toLowerCase();
            }

            // Combine strings in an array so we can iterate through them for filter tests.
            let strings = [email, login];

            // Apply string tests.
            _.each( strings, (string) => {
                if ( -1 !== string.search(input) ) {
                    match = true;
                }
            });

            // If a match was found, push to output.
            if ( match ) {
                output.push(user);
            }
        });

        return output;
    });

    ajax(endpoint, data, success, fail?)
    {
        // Declare the ajax action.
        data.action = 'post_users_pivot_' + endpoint;

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

    getAllUsers()
    {
        this.loadingUsers(true);

        let prepUsers = (users) => {
            _.each(users, (user) => {
                user.selected = false;
                user.busy = false;
            });
            return users;
        };

        let success = (r) => {
            let models = prepUsers(r);
            ko.mapping.fromJS( models, {}, this.usersCollection );
            this.loadingUsers(false);
        };

        let fail = () => {
            this.loadingUsers(false);
        };

        let postData = {};

        // Get all users.
        this.ajax( 'all', postData, success, fail );
    }

    getAttachedUserIds()
    {
        this.loadingAttachedUsers(true);

        let success = (r) => {
            this.loadingAttachedUsers(false);
            // Loop through the IDs returned by server.
            _.each(r, (id) => {
                // Push to collection.
                this.attachedUserIds.push( id );
            });
        };

        let fail = () => {
            this.loadingAttachedUsers(false);
        };

        let postData = {
            postId: this.options.postId
        };

        // Get get attached user IDs.
        this.ajax( 'get', postData, success, fail );
    }

    attachUser(user)
    {
        if ( ! this.canAttach() ) {
            alert('Cannot attach user.');
            return;
        }

        user.busy(true);

        let success = (r) => {
            user.busy(false);
            user.selected(true);
            this.attachedUserIds.push( user.userId() );

            // If this is a single selection, go ahead and switch back to selected view.
            if ( ! this.options.multipleUsers ) {
                this.view('selected');
            }
        };

        let fail = () => {
            user.busy(false);
            this.loadingUsers(false);
        };

        let postData = {
            userId: user.userId(),
            postId: this.options.postId
        };

        // Get all users.
        this.ajax( 'attach', postData, success, fail );
    }

    detachUser(user)
    {
        user.busy(true);

        let success = (r) => {
            user.busy(false);
            user.selected(false);
            this.attachedUserIds.remove( user.userId() );
        };

        let fail = () => {
            user.busy(false);
            this.loadingUsers(false);
        };

        let postData = {
            userId: user.userId(),
            postId: this.options.postId
        };

        // Get all users.
        this.ajax( 'detach', postData, success, fail );
    }

    canAttach()
    {
        let count = this.attachedUserIds().length,
            multiple = this.options.multipleUsers;

        // If multiple users are not allowed and there's already an
        // attached user, then return false.
        if ( ! multiple && count >= 1 ) {
            return false;
        }

        return true;
    };

    toggleUserAttachment(user)
    {
        if ( user.selected() ) {
            this.detachUser(user);
        } else {
            this.attachUser(user);
        }
    }

    listenToObservables()
    {
        // When userCollection changes.
        this.usersCollection.subscribe((user) =>
        {
            // Users collection still in preload state, so do nothing.
            if ( 'undefined' == typeof(user.userId) ) {
                return;
            }

            // Get the user ID.
            let id = user.userId();

            // If the user ID exists in attachUserIds, set selected to true.
            if ( -1 !== this.attachedUserIds.indexOf(id) ) {
                user.selected(true);
            }
        });

        // Listen to changes in attached user IDs.
        this.attachedUserIds.subscribe((id) =>
        {
            // Loop through the users collection.
            _.each( this.usersCollection(), (user) => {
                if ( user.userId() == id ) {
                    user.selected(true);
                }
            });
        });
    }

    constructor(options: PostUsersPivoterMetaBoxOptions)
    {
        // Assign options internally to the class.
        this.options = options;

        // Initialize knockout bindings.
        ko.applyBindings( this, document.getElementById( this.options.elementId ) );

        // Get users from server.
        this.getAllUsers();
        this.getAttachedUserIds();

        // Bind observable callbacks.
        this.listenToObservables();
    }
}