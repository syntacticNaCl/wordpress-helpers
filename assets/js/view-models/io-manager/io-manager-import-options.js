var IOManagerImportOptions = (function () {
    function IOManagerImportOptions() {
        // Set defaults to 'post', 'page', and 'attachment'
        this.selectedPostTypes = ko.observable(['attachment', 'post', 'page',]);
        this.importMedia = ko.observable(true);
        this.localizeInlineUrls = ko.observable(true);
    }
    return IOManagerImportOptions;
}());
