var IOImporter = (function () {
    function IOImporter(parent) {
        this.parent = parent;
    }
    IOImporter.prototype.downloadResources = function (pool, onSuccess, onFail, done) {
        var _this = this;
        // Extract a URL from the array.
        var url = pool.pop();
        var postData = {
            url: url,
            sessionId: this.parent.session.sessionId,
            key: this.parent.remoteSecurityKey,
            nonce: this.parent.parent.nonce()
        };
        var success = function (r) {
            // If a download success callback is defined, run it.
            if (onSuccess) {
                onSuccess(r, url);
            }
            IOProgressBar.bump();
            if (0 == pool.length && done) {
                return done();
            }
            else {
                return _this.downloadResources(pool, onSuccess, onFail, done);
            }
        };
        var fail = function (r) {
            // If a download success callback is defined, run it.
            if (onFail) {
                onFail(r, url);
            }
            alert(r + " Failed to download resource.");
            IOProgressBar.bump();
        };
        // Download
        IOAjax.post('download_remote_resource', postData, success, fail);
    };
    /**
     * Executes an array of functions.
     * @param pool An array of functions.
     * @param done Callback function when chain is complete.
     */
    IOImporter.prototype.doFunctions = function (pool, done) {
        var _this = this;
        // Get the first function.
        var a = pool.shift();
        // Do the function.
        (function () {
            a();
            if (0 != pool.length) {
                return _this.doFunctions(pool, done);
            }
            else {
                return done();
            }
        })();
    };
    IOImporter.prototype.processPosts = function (postIds, postType, onSuccess, onFail, done) {
        var _this = this;
        // Extract a postID from the postIDs array.
        var postId = postIds.shift();
        var postData = {
            postId: postId,
            sessionId: this.parent.session.sessionId,
            key: this.parent.remoteSecurityKey,
            nonce: this.parent.parent.nonce(),
            postType: postType
        };
        var success = function (r) {
            // If a download success callback is defined, run it.
            if (onSuccess) {
                onSuccess(r);
            }
            IOProgressBar.bump();
            if (0 == postIds.length) {
                if (done) {
                    return done();
                }
                else {
                    return;
                }
            }
            else {
                return _this.processPosts(postIds, postType, onSuccess, onFail, done);
            }
        };
        var fail = function (r) {
            // If a download success callback is defined, run it.
            if (onFail) {
                onFail(r);
            }
            console.log(r + " Failed to process post, unshifting element to try again.");
            postIds.unshift(postId);
            if (0 == postIds.length) {
                if (done) {
                    return done();
                }
                else {
                    return;
                }
            }
            else {
                return _this.processPosts(postIds, postType, onSuccess, onFail, done);
            }
        };
        // Download
        IOAjax.post('import_post', postData, success, fail);
    };
    IOImporter.prototype.processPostTypes = function (type, then) {
        var _this = this;
        var postData = {
            nonce: this.parent.parent.nonce(),
            sessionId: this.parent.session.sessionId(),
            postType: type
        };
        var success = function (r) {
            var count = r.count, postIds = r.postIds, postType = r.postType;
            // Reset the progress bar.
            IOProgressBar.reset(count);
            IOProgressBar.message("Processing " + type + "s...");
            var onSuccess = function () {
            };
            var onFail = function () {
            };
            var onDone = function () {
                IOProgressBar.logOutput(count + " " + type + (count > 1 ? 's' : '') + " processed");
                if (then) {
                    then();
                }
            };
            // Process attachments.
            _this.processPosts(postIds, postType, onSuccess, onFail, onDone);
        };
        var fail = function (r) {
        };
        IOAjax.post('get_post_manifest', postData, success, fail);
    };
    IOImporter.prototype.processPostTypeSequence = function (sequence) {
        var _this = this;
        if (0 !== sequence.length) {
            // Get first in sequence.
            var postType = sequence.shift();
            this.processPostTypes(postType, function () {
                if (sequence.length > 0) {
                    return _this.processPostTypeSequence(sequence);
                }
                else {
                    alert('Sequence complete!');
                }
            });
        }
    };
    IOImporter.prototype.processData = function () {
        // Function sequence.
        var functions = [];
        // Do attachments first.
        if (-1 !== this.parent.options.selectedPostTypes().indexOf('attachment')) {
            functions.push('attachment');
        }
        // Loop through remaining post types.
        _.each(this.parent.options.selectedPostTypes(), function (postType) {
            // Skip attachments.
            if ('attachment' == postType) {
                return;
            }
            functions.push(postType);
        });
        this.processPostTypeSequence(functions);
    };
    IOImporter.prototype.downloadJsonFiles = function (then) {
        var json = this.parent.session.instanceData.json(), remoteUrl = this.parent.validUrl();
        // Reset the progress bar.
        IOProgressBar.reset(json.length);
        // Show message.
        IOProgressBar.message("Downloading remote data files from " + remoteUrl);
        var onSuccess = function (r, url) {
        };
        var onFail = function (r, url) {
        };
        var done = function () {
            IOProgressBar.logOutput(IOProgressBar.total + " data files downloaded from remote server");
            if (then) {
                then();
            }
            ;
        };
        // Download json resources
        this.downloadResources(json, onSuccess, onFail, done);
    };
    IOImporter.prototype.start = function () {
        var _this = this;
        var container = jQuery('#import-options-form'), importerScreen = jQuery('#importer-screen');
        container.fadeOut(function () {
            importerScreen.fadeIn(function () {
                // Download JSON files, then...
                _this.downloadJsonFiles(function () {
                    // Run importer.
                    _this.processData();
                });
            });
        });
    };
    return IOImporter;
}());
