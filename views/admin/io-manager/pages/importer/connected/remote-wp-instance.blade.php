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
            <!-- ko if: importer.hasFetchedData() -->
            <tr>
                <td>Site Name</td>
                <td data-bind="text: importer.session.instanceData.siteData.name"></td>
            </tr>
            <tr>
                <td>Description</td>
                <td data-bind="text: importer.session.instanceData.siteData.description"></td>
            </tr>
            <tr>
                <td>Admin Email</td>
                <td data-bind="text: importer.session.instanceData.siteData.admin_email"></td>
            </tr>
            <tr>
                <td>Post Types</td>
                <td data-bind="text: importer.session.instanceData.postTypes().join(', ')"></td>
            </tr>
            <tr>
                <td>Post Counts by Post Type</td>
                <td>
                    <!-- ko foreach: importer.session.instanceData.postTypes() -->
                    <span data-bind="text: $data"></span>:
                    <span data-bind="text: $parent.importer.session.instanceData.postTypesCount()[$data]"></span>
                    <br>
                    <!-- /ko -->
                </td>
            </tr>
            <tr>
                <td>Users (<span data-bind="text: importer.session.instanceData.usersCount"></span>)</td>
                <td>
                    <!-- ko foreach: importer.session.instanceData.users() -->
                    <span data-bind="text: $data.login"></span> :
                    <span data-bind="text: $data.email"></span> :
                    <span data-bind="text: $data.id"></span>
                    <br>
                    <!-- /ko -->
                </td>
            </tr>
            <!-- /ko -->
            </tbody>
        </table>

        <!-- ko if: ! importer.hasFetchedData() -->
        <i class="fa fa-spinner fa-spin"></i>
        Fetching remote WordPress instance data...
        <!-- /ko -->
    </div>
</div>
