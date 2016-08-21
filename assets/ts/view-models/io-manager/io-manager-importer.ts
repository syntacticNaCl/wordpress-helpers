interface IOImporterSessionOptions
{
    sessionId: string;
    remoteUrl: string;
    securityKey: string;
    createdAt: number;
    instanceData: {
        json: string[],
        postTypes: string[],
        siteData: {
            admin_email: string,
            description: string,
            name: string,
            url: string,
            wpurl: string,
        }
    }
}

interface IOManagerOptionsInterface {
    settingsNonce: string;
    securityKey: string;
}

class IOManagerImporter
{
    options: IOManagerImportOptions;

    main = new IOImporter(this);

    constructor(parent)
    {
        this.parent = parent;
        this.options = new IOManagerImportOptions;
    }

    screen = ko.observable('connect');
    parent: IOManagerViewModel;
    remoteUrl = ko.observable('http://projection.wp');
    remoteSecurityKey = ko.observable('051faf3cff5dbe2c26de0d416e69a9b1');
    busy = ko.observable(false);

    // Determines if the client can connect to remote.
    canConnect = ko.pureComputed(() =>
    {
        if ( false != this.validKey() )
        {
            return false;
        }

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

    // We set an initial state of false for validKey so that we can attempt to
    // connect first and verify our security key.
    validKey = ko.observable(false);
    validUrl = ko.observable(false);

    hasFetchedData = ko.observable(false);

    getInstanceData()
    {
        IOAjax.post( 'get_remote_data', {
            remoteUrl: this.validUrl(),
            remoteSecurityKey: this.remoteSecurityKey(),
            nonce: this.parent.nonce()
        }, (r) =>
        {
            // Assign session internally.
            //ko.mapping.fromJS( session );
            ko.merge.fromJS(this.session, r);

            this.hasFetchedData(true);
        });
    }

    session = {
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

    connect()
    {
        // No valid key yet, let's test it.
        if ( false == this.validKey() )
        {
            // Make a reference to the currently specified security key.
            let curKey = this.remoteSecurityKey(),
                curUrl = this.remoteUrl();

            // Set busy status.
            this.busy(true);

            let success = (r) =>
            {
                // Clear busy status.
                this.busy(false);

                // Set valid key.
                this.validKey(curKey);
                this.validUrl(curUrl);

                // Set screen to connected
                this.screen('connected');

                // Fetch instance data.
                this.getInstanceData();
            };

            let fail = (r) =>
            {
                this.busy(false);
                alert(r);
            };

            let postData = {
                remoteUrl: this.remoteUrl(),
                remoteSecurityKey: this.remoteSecurityKey(),
                nonce: this.parent.nonce()
            };

            // Perform request.
            IOAjax.post( 'can_connect_to_remote', postData, success, fail );
        }
    }
}