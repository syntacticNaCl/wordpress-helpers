class IOManagerImportOptions
{
    // Set defaults to 'post', 'page', and 'attachment'
    selectedPostTypes = ko.observable(['attachment','post','page',]);

    importMedia = ko.observable(true);

    localizeInlineUrls = ko.observable(true);
}