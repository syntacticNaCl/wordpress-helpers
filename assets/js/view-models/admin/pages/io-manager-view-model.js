var IOManagerImporter = (function () {
    function IOManagerImporter() {
        var _this = this;
        this.remoteUrl = ko.observable('');
        this.remoteSecurityKey = ko.observable('');
        // Determines if the client can connect to remote.
        this.canConnect = ko.pureComputed(function () {
            if (32 != _this.remoteSecurityKey().length) {
                return false;
            }
            if (0 == _this.remoteUrl().trim().length) {
                return false;
            }
            return true;
        });
    }
    return IOManagerImporter;
}());
var IOManagerViewModel = (function () {
    function IOManagerViewModel(options) {
        this.nonce = ko.observable('');
        this.securityKey = ko.observable('');
        this.mode = ko.observable('import');
        this.resetting = ko.observable(false);
        this.importer = new IOManagerImporter();
        this.nonce(options.settingsNonce);
        this.securityKey(options.securityKey);
        this.ajax('update_settings', {}, function () { });
    }
    IOManagerViewModel.prototype.updateSettings = function () {
        var _this = this;
        this.ajax('update_settings', {
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
        this.ajax('reset_security_key', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, function (r) {
            _this.resetting(false);
            _this.securityKey(r);
        });
    };
    IOManagerViewModel.prototype.ajax = function (action, data, cb) {
        var postData = {
            action: 'io_' + action
        };
        // Merge data.
        _.each(data, function (item, key) {
            postData[key] = item;
        });
        jQuery.post(ajaxurl, postData, function (r) {
            if (cb) {
                cb(r);
            }
        });
    };
    return IOManagerViewModel;
}());
