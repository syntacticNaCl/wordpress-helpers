/**
 * The Post Users Pivoter meta box view model.
 */
var PostUsersPivoterMetaBoxViewModel = (function () {
    function PostUsersPivoterMetaBoxViewModel(options) {
        var _this = this;
        // Active view
        this.view = ko.observable('selected');
        this.selectionType = ko.observable('large');
        this.displaySearchForm = ko.observable(false);
        this.searchInput = ko.observable('');
        // Loading states.
        this.loadingUsers = ko.observable(false);
        this.loadingAttachedUsers = ko.observable(false);
        // Available users.
        this.usersCollection = ko.observableArray([]);
        /** An array of attached user IDs. */
        this.attachedUserIds = ko.observableArray([]);
        this.attachedUsers = ko.pureComputed(function () {
            var users = [];
            _.each(_this.attachedUserIds(), function (id) {
                _.each(_this.usersCollection(), function (user) {
                    if (user.userId() == id) {
                        users.push(user);
                    }
                });
            });
            return users;
        });
        this.filteredUsers = ko.pureComputed(function () {
            var output = [], users = _this.usersCollection(), input = _this.searchInput().trim(), caseInsensitive = true;
            // If there's no search input, return the entire collection.
            if ('' == input) {
                return users;
            }
            // If the filter is case insensitive, make lower case.
            if (caseInsensitive) {
                input = input.toLowerCase();
            }
            // Loop through users, apply filters.
            _.each(users, function (user) {
                // If a number is supplied as search input, filter user IDs.
                if (!isNaN(input)) {
                    if (input == user.userId()) {
                        output.push(user);
                    }
                    return;
                }
                // Get string filterables.
                var email = user.email(), login = user.login(), match = false;
                if (caseInsensitive) {
                    email = email.toLowerCase();
                    login = login.toLowerCase();
                }
                // Combine strings in an array so we can iterate through them for filter tests.
                var strings = [email, login];
                // Apply string tests.
                _.each(strings, function (string) {
                    if (-1 !== string.search(input)) {
                        match = true;
                    }
                });
                // If a match was found, push to output.
                if (match) {
                    output.push(user);
                }
            });
            return output;
        });
        // Assign options internally to the class.
        this.options = options;
        // Initialize knockout bindings.
        ko.applyBindings(this, document.getElementById(this.options.elementId));
        // Get users from server.
        this.getAllUsers();
        this.getAttachedUserIds();
        // Bind observable callbacks.
        this.listenToObservables();
    }
    PostUsersPivoterMetaBoxViewModel.prototype.ajax = function (endpoint, data, success, fail) {
        // Declare the ajax action.
        data.action = 'post_users_pivot_' + endpoint;
        var callback = function (r) {
            success(r);
        };
        jQuery
            .ajax(ajaxurl, {
            method: 'POST',
            data: data
        })
            .done(callback)
            .fail(fail);
    };
    PostUsersPivoterMetaBoxViewModel.prototype.getAllUsers = function () {
        var _this = this;
        this.loadingUsers(true);
        var prepUsers = function (users) {
            _.each(users, function (user) {
                user.selected = false;
                user.busy = false;
            });
            return users;
        };
        var success = function (r) {
            var models = prepUsers(r);
            ko.mapping.fromJS(models, {}, _this.usersCollection);
            _this.loadingUsers(false);
        };
        var fail = function () {
            _this.loadingUsers(false);
        };
        var postData = {};
        // Get all users.
        this.ajax('all', postData, success, fail);
    };
    PostUsersPivoterMetaBoxViewModel.prototype.getAttachedUserIds = function () {
        var _this = this;
        this.loadingAttachedUsers(true);
        var success = function (r) {
            _this.loadingAttachedUsers(false);
            // Loop through the IDs returned by server.
            _.each(r, function (id) {
                // Push to collection.
                _this.attachedUserIds.push(id);
            });
        };
        var fail = function () {
            _this.loadingAttachedUsers(false);
        };
        var postData = {
            postId: this.options.postId
        };
        // Get get attached user IDs.
        this.ajax('get', postData, success, fail);
    };
    PostUsersPivoterMetaBoxViewModel.prototype.attachUser = function (user) {
        var _this = this;
        if (!this.canAttach()) {
            alert('Cannot attach user.');
            return;
        }
        user.busy(true);
        var success = function (r) {
            user.busy(false);
            user.selected(true);
            _this.attachedUserIds.push(user.userId());
            // If this is a single selection, go ahead and switch back to selected view.
            if (!_this.options.multipleUsers) {
                _this.view('selected');
            }
        };
        var fail = function () {
            user.busy(false);
            _this.loadingUsers(false);
        };
        var postData = {
            userId: user.userId(),
            postId: this.options.postId
        };
        // Get all users.
        this.ajax('attach', postData, success, fail);
    };
    PostUsersPivoterMetaBoxViewModel.prototype.detachUser = function (user) {
        var _this = this;
        user.busy(true);
        var success = function (r) {
            user.busy(false);
            user.selected(false);
            _this.attachedUserIds.remove(user.userId());
        };
        var fail = function () {
            user.busy(false);
            _this.loadingUsers(false);
        };
        var postData = {
            userId: user.userId(),
            postId: this.options.postId
        };
        // Get all users.
        this.ajax('detach', postData, success, fail);
    };
    PostUsersPivoterMetaBoxViewModel.prototype.canAttach = function () {
        var count = this.attachedUserIds().length, multiple = this.options.multipleUsers;
        // If multiple users are not allowed and there's already an
        // attached user, then return false.
        if (!multiple && count >= 1) {
            return false;
        }
        return true;
    };
    ;
    PostUsersPivoterMetaBoxViewModel.prototype.toggleUserAttachment = function (user) {
        if (user.selected()) {
            this.detachUser(user);
        }
        else {
            this.attachUser(user);
        }
    };
    PostUsersPivoterMetaBoxViewModel.prototype.listenToObservables = function () {
        var _this = this;
        // When userCollection changes.
        this.usersCollection.subscribe(function (user) {
            // Users collection still in preload state, so do nothing.
            if ('undefined' == typeof (user.userId)) {
                return;
            }
            // Get the user ID.
            var id = user.userId();
            // If the user ID exists in attachUserIds, set selected to true.
            if (-1 !== _this.attachedUserIds.indexOf(id)) {
                user.selected(true);
            }
        });
        // Listen to changes in attached user IDs.
        this.attachedUserIds.subscribe(function (id) {
            // Loop through the users collection.
            _.each(_this.usersCollection(), function (user) {
                if (user.userId() == id) {
                    user.selected(true);
                }
            });
        });
    };
    return PostUsersPivoterMetaBoxViewModel;
}());
