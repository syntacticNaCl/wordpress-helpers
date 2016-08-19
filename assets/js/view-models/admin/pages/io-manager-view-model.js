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
