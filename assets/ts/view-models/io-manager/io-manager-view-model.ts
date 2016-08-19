



class IOManagerViewModel
{
    nonce = ko.observable('');
    securityKey = ko.observable('');
    mode = ko.observable('import');
    resetting = ko.observable(false);

    importer = new IOManagerImporter(this);

    updateSettings() {
        IOAjax.post('update_settings', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, (r) => {
            this.securityKey(r);
        });
    }

    resetKey() {
        this.resetting(true);
        this.securityKey('Resetting...');
        IOAjax.post('reset_security_key', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, (r) => {
            this.resetting(false);
            this.securityKey(r);
        });
    }

    constructor(options: IOManagerOptionsInterface)
    {
        this.nonce( options.settingsNonce );
        this.securityKey( options.securityKey );
    }
}