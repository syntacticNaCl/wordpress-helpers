<!-- ko if: 'large' == selectionType() -->

    <!-- ko foreach: { data: filteredUsers, as: 'user' } -->
    <div class="user" data-bind="css: { 'user-selected' : user.selected() }">
        <img data-bind="attr: { 'src': user.thumbnail }" class="img-circle">
        <p data-bind="text: user.login"></p>
        <p data-bind="text: user.email"></p>

        <button type="button" class="btn btn-sm" data-bind="
            css: { 'btn-danger' : user.selected() },
            click: function() { $parent.toggleUserAttachment(user); },
            disable: user.busy
            ">

            <!-- ko if: user.busy() -->
            <i class="fa fa-spinner"></i>
            <span data-bind="text: user.selected() ? 'Detaching...' : 'Attaching...'"></span>
            <!-- /ko -->

            <!-- ko if: ! user.busy() -->
            <!-- ko if: ! user.selected() -->
            <i class="fa fa-user-plus"></i> Attach
            <!-- /ko -->

            <!-- ko if: user.selected() -->
            <i class="fa fa-user-times"></i> Detach
            <!-- /ko -->
            <!-- /ko -->
        </button>

    </div>

    <hr>
    <!-- /ko -->

<!-- /ko -->