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