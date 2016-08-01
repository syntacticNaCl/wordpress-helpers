<!-- ko if: 'url' == type() -->
<!-- ko with: types.url -->

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>URL</th>
                <th>Label</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <!-- ko foreach: collection() -->
            <tr>
                <td>
                    <input class="form-control" data-bind="textInput: url" placeholder="URL">
                </td>
                <td>
                    <input class="form-control" data-bind="textInput: label">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-circle-micro" data-bind="click: function(){ $parent.remove($data); }">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
            <!-- /ko -->
        </tbody>
    </table>

    <!-- ko if: $parent.options.multiple -->
    <button type="button" class="btn btn-success" data-bind="click: function() { add(); }">
        <i class="fa fa-plus"></i>
        Add URL
    </button>
    <!-- /ko -->

    <button type="button" class="btn btn-primary" data-bind="click: function() { $parent.type('wp'); }">
        <i class="fa fa-wordpress"></i>
        Choose WordPress Media
    </button>

<!-- /ko -->
<!-- /ko -->