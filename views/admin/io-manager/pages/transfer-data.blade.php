<h2>
    Transfer Data
    <div class="btn-group">
        <button type="button" class="btn btn-sm" data-bind="
            css: { 'btn-primary active': 'import' == mode() },
            click: function(){ mode('import'); }
        ">Import</button>
        <button type="button" class="btn btn-sm" data-bind="
            css: { 'btn-primary active': 'export' == mode() },
            click: function(){ mode('export'); }
        ">Export</button>
    </div>
</h2>

<hr>

<!-- ko if: 'import' == mode() -->
@include('admin.io-manager.pages.import-menu')
<!-- /ko -->

<!-- ko if: 'export' == mode() -->
export
<!-- /ko -->