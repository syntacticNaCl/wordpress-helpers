var IOAjax = (function () {
    function IOAjax() {
    }
    IOAjax.post = function (action, data, success, fail) {
        var postData = {
            action: 'io_' + action
        };
        // Merge data.
        _.each(data, function (item, key) {
            postData[key] = item;
        });
        jQuery.post(ajaxurl, postData, function (r) {
            if (success) {
                success(r);
            }
        }).fail(function (r) {
            if (fail) {
                fail(r.responseJSON);
            }
        });
    };
    return IOAjax;
}());
var IOManagerImporter = (function () {
    function IOManagerImporter(parent) {
        var _this = this;
        this.screen = ko.observable('connect');
        this.remoteUrl = ko.observable('http://wordpress-helpers.wp');
        this.remoteSecurityKey = ko.observable('84c5a635a68e9d0a4487787f7261ae4e');
        this.busy = ko.observable(false);
        // Determines if the client can connect to remote.
        this.canConnect = ko.pureComputed(function () {
            if (false != _this.validKey()) {
            }
            if (32 != _this.remoteSecurityKey().length) {
                return false;
            }
            if (0 == _this.remoteUrl().trim().length) {
                return false;
            }
            return true;
        });
        // We set an initial state of false for validKey so that we can attempt to
        // connect first and verify our security key.
        this.validKey = ko.observable(false);
        this.validUrl = ko.observable(false);
        this.hasFetchedData = ko.observable(false);
        this.session = {
            active: ko.observable(false),
            sessionId: ko.observable(false),
            remoteUrl: ko.observable(false),
            securityKey: ko.observable(false),
            createdAt: ko.observable(false),
            instanceData: {
                json: ko.observable(false),
                postTypes: ko.observable(false),
                postTypesCount: ko.observable(false),
                usersCount: ko.observable(false),
                users: ko.observable(false),
                siteData: {
                    admin_email: ko.observable(false),
                    description: ko.observable(false),
                    name: ko.observable(false),
                    url: ko.observable(false),
                    wpurl: ko.observable(false)
                }
            }
        };
        this.parent = parent;
    }
    IOManagerImporter.prototype.getInstanceData = function () {
        var _this = this;
        IOAjax.post('get_remote_data', {
            remoteUrl: this.validUrl(),
            remoteSecurityKey: this.remoteSecurityKey(),
            nonce: this.parent.nonce()
        }, function (r) {
            // Assign session internally.
            //ko.mapping.fromJS( session );
            ko.merge.fromJS(_this.session, r);
            _this.hasFetchedData(true);
        });
    };
    IOManagerImporter.prototype.connect = function () {
        var _this = this;
        // No valid key yet, let's test it.
        if (false == this.validKey()) {
            // Make a reference to the currently specified security key.
            var curKey_1 = this.remoteSecurityKey(), curUrl_1 = this.remoteUrl();
            // Set busy status.
            this.busy(true);
            var success = function (r) {
                // Clear busy status.
                _this.busy(false);
                // Set valid key.
                _this.validKey(curKey_1);
                _this.validUrl(curUrl_1);
                // Set screen to connected
                _this.screen('connected');
                // Fetch instance data.
                _this.getInstanceData();
            };
            var fail = function (r) {
                _this.busy(false);
                alert(r);
            };
            var postData = {
                remoteUrl: this.remoteUrl(),
                remoteSecurityKey: this.remoteSecurityKey(),
                nonce: this.parent.nonce()
            };
            // Perform request.
            IOAjax.post('can_connect_to_remote', postData, success, fail);
        }
    };
    return IOManagerImporter;
}());
var IOManagerViewModel = (function () {
    function IOManagerViewModel(options) {
        this.nonce = ko.observable('');
        this.securityKey = ko.observable('');
        this.mode = ko.observable('import');
        this.resetting = ko.observable(false);
        this.importer = new IOManagerImporter(this);
        this.nonce(options.settingsNonce);
        this.securityKey(options.securityKey);
    }
    IOManagerViewModel.prototype.updateSettings = function () {
        var _this = this;
        IOAjax.post('update_settings', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, function (r) {
            _this.securityKey(r);
        });
    };
    IOManagerViewModel.prototype.resetKey = function () {
        var _this = this;
        this.resetting(true);
        this.securityKey('Resetting...');
        IOAjax.post('reset_security_key', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, function (r) {
            _this.resetting(false);
            _this.securityKey(r);
        });
    };
    return IOManagerViewModel;
}());
