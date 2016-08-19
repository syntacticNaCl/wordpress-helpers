class IOManagerImportOptions
{
    // Set defaults to 'post', 'page', and 'attachment'
    selectedPostTypes = ko.observable(['post','page','attachment']);

    importMedia = ko.observable(true);

    localizeInlineUrls = ko.observable(true);
}