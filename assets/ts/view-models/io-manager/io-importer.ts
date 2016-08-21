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

    /**
     * Executes an array of functions.
     * @param pool An array of functions.
     * @param done Callback function when chain is complete.
     */
    doFunctions(pool, done)
    {
        // Get the first function.
        let a = pool.shift();

        // Do the function.
        (() => {
            a();

            if ( 0 != pool.length ) {
                return this.doFunctions(pool, done);
            } else {
                return done();
            }
        })();
    }

    processPosts(postIds: string[], postType, onSuccess?, onFail?, done?)
    {
        // Extract a postID from the postIDs array.
        let postId = postIds.shift();

        let postData = {
            postId: postId,
            sessionId: this.parent.session.sessionId,
            key: this.parent.remoteSecurityKey,
            nonce: this.parent.parent.nonce(),
            postType: postType
        };

        let success = (r) =>
        {
            // If a download success callback is defined, run it.
            if ( onSuccess ) {
                onSuccess(r);
            }

            IOProgressBar.bump();

            if ( 0 == postIds.length )
            {

                if ( done )
                {
                    return done();
                } else {
                    return;
                }

            } else {
                return this.processPosts( postIds, postType, onSuccess, onFail, done );
            }
        };

        let fail = (r) =>
        {
            // If a download success callback is defined, run it.
            if ( onFail ) {
                onFail(r);
            }

            alert(`${r} Failed to process post.`);

            IOProgressBar.bump();
        };

        // Download
        IOAjax.post('import_post', postData, success, fail );
    }

    processPostTypes(type, then?)
    {
        let postData = {
            nonce: this.parent.parent.nonce(),
            sessionId: this.parent.session.sessionId(),
            postType: type
        };

        let success = (r) =>
        {
            let count = r.count,
                postIds = r.postIds,
                postType = r.postType;

            // Reset the progress bar.
            IOProgressBar.reset( count );
            IOProgressBar.message( `Processing ${type}s...` );

            let onSuccess = () => {

            };

            let onFail = () => {

            };

            let onDone = () => {
                IOProgressBar.logOutput(`${count} ${type}${count>1?'s':''} processed`);

                if ( then ) {
                    then();
                }
            };

            // Process attachments.
            this.processPosts( postIds, postType, onSuccess, onFail, onDone );
        };

        let fail = (r) => {

        };

        IOAjax.post( 'get_post_manifest', postData, success, fail );
    }

    processPostTypeSequence(sequence)
    {
        if ( 0 !== sequence.length )
        {
            // Get first in sequence.
            let postType = sequence.shift();

            this.processPostTypes( postType, () =>
            {
                if ( sequence.length > 0 ) {
                    return this.processPostTypeSequence( sequence );
                } else {
                    alert('Sequence complete!');
                }
            });
        }
    }

    processData()
    {
        // Function sequence.
        let functions = [];

        // Do attachments first.
        if ( -1 !== this.parent.options.selectedPostTypes().indexOf('attachment') )
        {
            functions.push( 'attachment' );
        }

        // Loop through remaining post types.
        _.each( this.parent.options.selectedPostTypes(), (postType) =>
        {
            // Skip attachments.
            if ( 'attachment' == postType ) {
                return;
            }

            functions.push( postType );
        });

        this.processPostTypeSequence(functions);
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
            IOProgressBar.logOutput(`${IOProgressBar.total} data files downloaded from remote server`);
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
                this.downloadJsonFiles(() =>
                {
                    // Run importer.
                    this.processData()
                });
            });
        });
    }
}