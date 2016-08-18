<!-- ko if: 'connected' == importer.screen() -->

<h4 style="color: green">
    Connected to Remote
    <i class="fa fa-check"></i>
</h4>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Remote WordPress Instance</h3>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Remote URL</td>
                    <td data-bind="text: importer.remoteUrl"></td>
                </tr>
                <tr>
                    <td>Remote Security Key</td>
                    <td data-bind="text: importer.remoteSecurityKey"></td>
                </tr>
            </tbody>
        </table>

        <!-- ko if: ! importer.hasFetchedData() -->
        <i class="fa fa-spinner fa-spin"></i>
        Fetching remote WordPress instance data...
        <!-- /ko -->
    </div>
</div>

<!-- /ko -->