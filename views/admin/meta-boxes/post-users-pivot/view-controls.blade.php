<!-- View controls for Post Users Pivot meta box -->

<div class="btn-group">
    <button type="button" class="btn btn-xs" data-bind="
        click: function() { view('selected'); },
        css: { active: 'selected' == view() }
        ">
        <i class="fa fa-user"></i>
    </button>
    <button type="button" class="btn btn-xs" data-bind="
        click: function() { view('users'); },
        css: { active: 'users' == view() }
        ">
        <i class="fa fa-users"></i>
    </button>
</div>

<!-- ko if: 'users' == view() -->
<div class="btn-group">
    <button type="button" class="btn btn-xs" data-bind="
        click: function() { selectionType('table'); },
        css: { active: 'table' == selectionType() }
        ">
        <i class="fa fa-list-ul"></i>
    </button>
    <button type="button" class="btn btn-xs" data-bind="
        click: function() { selectionType('large'); },
        css: { active: 'large' == selectionType() }
        ">
        <i class="fa fa-th-large"></i>
    </button>
</div>
<!-- /ko -->

<div class="pull-right">
    <small class="user-type" data-bind="text: options.multipleUsers ? 'Multiple Users' : 'Single User'"></small>
</div>