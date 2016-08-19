<!-- ko if: importer.hasFetchedData() -->

<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title">Import Data</h3>
    </div>

    <div class="panel-body">

        <div id="import-options-form">

            <p>Select the content you wish to import into this WordPress instance.</p>

            <hr>

            <h4>Post Types</h4>
            <!-- ko foreach: importer.session.instanceData.postTypes() -->
            <div class="checkbox">
                <label>
                    <input type="checkbox" data-bind="checked: $parent.importer.options.selectedPostTypes, attr: { value: $data }">
                    <!-- ko text: $data --><!-- /ko -->
                    (<!-- ko text: $parent.importer.session.instanceData.postTypesCount()[$data] --><!-- /ko -->)
                </label>
            </div>
            <!-- /ko -->

            <hr>

            <h4>Importer Options</h4>

            <div class="checkbox">
                <label>
                    <input type="checkbox" data-bind="checked: importer.options.importMedia">
                    Import Remote Files and Attachments
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" data-bind="checked: importer.options.localizeInlineUrls">
                    Localize URLs
                </label>
            </div>

            <hr>

            <button type="button" class="btn btn-primary btn-sm" data-bind="
                click: function() {
                    importer.main.start();
                }
                ">
                Start Importer
            </button>

        </div>

        <div id="importer-screen" style="display: none;">

            <div class="progress">
                <div id="io-progress" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="0"
                     aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    <span class="sr-only">0% Complete</span>
                </div>
            </div>
            <div id="io-progress-message"></div>
            <div id="io-progress-counter"></div>

            <hr>
            <div id="io-output"></div>
        </div>

    </div>
</div>

<!-- /ko -->
