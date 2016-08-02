<!-- ko if: attachmentIds().length == 0 -->
<p>No attachments.</p>
<!-- /ko -->

<!-- ko if: 'grid' == view() -->
<div class="attachment-models">
    <!-- ko foreach: orderedCollection -->
    <div class="attachment-model-container" data-bind="attr: { 'data-id': $data.id }">

        <div class="attached-model">

            <!-- ko if: $data.sizes -->
            <img data-bind="attr: { src: $data.sizes.thumbnail.url }" class="img-thumbnail">
            <!-- /ko -->

            <!-- ko if: ! $data.sizes -->
            <div data-bind="html: $parent.renderIcon($data)" style="text-align: center;"></div>
            <!-- /ko -->

            <div class="img-controls">

                <ko-button params="
                    text: '',
                    icon: 'fa-times',
                    class: 'btn-circle-micro btn-danger delete-button',
                    click: function(){ $parent.removeModel($data); }"></ko-button>

                <br>

                <a href="#" class="btn btn-circle-micro btn-default move-button">
                    <i class="fa fa-arrows"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /ko -->
</div>
<!-- /ko -->

<!-- ko if: 'list' == view() -->
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Preview</th>
        <th>Meta Data</th>
        <th>Remove</th>
    </tr>
    </thead>
    <tbody data-bind="foreach: orderedCollection">
    <tr>
        <td>
            <!-- ko if: $data.sizes -->
            <img data-bind="attr: { src: $data.sizes.thumbnail.url }" class="img-thumbnail">
            <!-- /ko -->

            <!-- ko if: ! $data.sizes -->
            <div data-bind="html: $parent.renderIcon($data)"></div>
            <!-- /ko -->
        </td>
        <td>
            <span data-bind="text: $data.title"></span>
        </td>
        <td>
            <ko-button params="
                text: '',
                icon: 'fa-times',
                class: 'btn-circle-micro btn-danger delete-button',
                click: function(){ $parent.removeModel($data); }"></ko-button>
        </td>
    </tr>
    </tbody>
</table>
<!-- /ko -->

<hr>

<button class="btn btn-primary" type="button" data-bind="
    click: function() { selectAttachment(); }">
    <i class="fa fa-plus"></i>
    <span data-bind="text: multiple ? 'Select Files' : 'Select File'"></span>
</button>

<button class="btn btn-primary" type="button" data-bind="
    click: function() { $parent.type('url') }">
    <i class="fa fa-link"></i>
    Custom URL
</button>

<div class="pull-right">
    <div class="btn-group">
        <button type="button" class="btn" data-bind="css: { active: 'grid' == view() }, click: function(){ view('grid'); }">
            <i class="fa fa-th-large"></i>
        </button>
        <button type="button" class="btn" data-bind="css: { active: 'list' == view() }, click: function(){ view('list'); }">
            <i class="fa fa-th-list"></i>
        </button>
    </div>
</div>

<!-- /ko -->