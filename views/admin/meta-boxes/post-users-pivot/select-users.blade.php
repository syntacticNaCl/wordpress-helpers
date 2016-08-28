<!-- ko if: 'users' == view() -->

    <h3 class="available-users title">
        Available Users
        <button class="btn btn-xs pull-right" type="button" data-bind="
            click: function(){ displaySearchForm( ! displaySearchForm() ); },
            css: { active: displaySearchForm() }
            ">
            <i class="fa fa-search"></i>
        </button>
    </h3>

    <!-- ko if: loadingUsers -->
    <h4 style="text-align: center;">
        Loading Users
        <i class="fa fa-spinner"></i>
    </h4>
    <!-- /ko -->

    <!-- ko if: displaySearchForm -->
    <ko-input params="value: searchInput, label: 'Search', placeholder: 'Login, name, email, ID'"></ko-input>
    <!-- /ko -->

    @include('admin.meta-boxes.post-users-pivot.select-users.large')
    @include('admin.meta-boxes.post-users-pivot.select-users.table')

<!-- /ko -->