interface IOManagerOptionsInterface {
    settingsNonce: string;
    securityKey: string;
}

class IOManagerImporter
{
    remoteUrl = ko.observable('');
    remoteSecurityKey = ko.observable('');

    // Determines if the client can connect to remote.
    canConnect = ko.pureComputed(() =>
    {
        if ( 32 != this.remoteSecurityKey().length )
        {
            return false;
        }

        if ( 0 == this.remoteUrl().trim().length )
        {
            return false;
        }

        return true;
    });
}

class IOManagerViewModel
{
    nonce = ko.observable('');
    securityKey = ko.observable('');
    mode = ko.observable('import');
    resetting = ko.observable(false);

    importer = new IOManagerImporter();

    updateSettings() {
        this.ajax('update_settings', {
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
        this.ajax('reset_security_key', {
            nonce: this.nonce(),
            settings: {
                securityKey: this.securityKey()
            }
        }, (r) => {
            this.resetting(false);
            this.securityKey(r);
        });
    }

    ajax(action, data, cb)
    {
        let postData = {
            action: 'io_' + action,
        };

        // Merge data.
        _.each( data, (item, key) => {
            postData[key] = item;
        });

        jQuery.post(
            ajaxurl,
            postData,
            function(r)
            {
                if ( cb ) {
                    cb(r);
                }
            }
        );
    }

    constructor(options: IOManagerOptionsInterface)
    {
        this.nonce( options.settingsNonce );
        this.securityKey( options.securityKey );

        this.ajax('update_settings',{},() => {});
    }
}