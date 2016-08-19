class IOImporter
{
    parent: IOManagerImporter;

    constructor(parent)
    {
        this.parent = parent;
    }

    downloadResources(pool: string[], onSuccess?, onFail?, done?)
    {
        // Extract a URL from the array.
        let url = pool.pop();

        let postData = {
            url: url,
            sessionId: this.parent.session.sessionId,
            key: this.parent.remoteSecurityKey,
            nonce: this.parent.parent.nonce()
        };

        let success = (r) =>
        {
            // If a download success callback is defined, run it.
            if ( onSuccess ) {
                onSuccess(r, url);
            }

            IOProgressBar.bump();

            if ( 0 == pool.length && done ) {
                return done();
            } else {
                return this.downloadResources( pool, onSuccess, onFail, done );
            }
        };

        let fail = (r) =>
        {
            // If a download success callback is defined, run it.
            if ( onFail ) {
                onFail(r, url);
            }

            alert(`${r} Failed to download resource.`);

            IOProgressBar.bump();
        };

        // Download
        IOAjax.post('download_remote_resource', postData, success, fail );
    }

    processJsonFiles()
    {
        
    }

    downloadJsonFiles(then?)
    {
        let json = this.parent.session.instanceData.json(),
            remoteUrl = this.parent.validUrl();

        // Reset the progress bar.
        IOProgressBar.reset( json.length );

        // Show message.
        IOProgressBar.message(`Downloading remote data files from ${remoteUrl}`);

        let onSuccess = (r, url) => {

        };

        let onFail = (r, url) => {

        };

        let done = () => {
            IOProgressBar.logOutput(`${IOProgressBar.total} data files downloaded from remote server!.`);
            if ( then ) { then() };
        };

        // Download json resources
        this.downloadResources( json, onSuccess, onFail, done );
    }

    start() {

        let container = jQuery('#import-options-form'),
            importerScreen = jQuery('#importer-screen');

        container.fadeOut(() => {
            importerScreen.fadeIn(() =>
            {
                // Download JSON files, then...
                this.downloadJsonFiles(

                    // Process JSON files, then...
                    this.processJsonFiles
                );
            });
        });
    }
}