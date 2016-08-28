<!-- ko if: 'selected' == view() -->

<h3 class="available-users title">
    Attached User<span data-bind="text: options.multipleUsers ? 's' : ''"></span>
</h3>

<!-- ko if: loadingAttachedUsers -->
<h4 style="text-align: center;">
    Loading Users
    <i class="fa fa-spinner"></i>
</h4>
<!-- /ko -->

<!-- ko if: 0 == attachedUsers().length -->
<p>
    No user<span data-bind="text: options.multipleUsers ? 's' : ''"></span> attached.
    <a href="#" data-bind="click: function(){ view('users'); }">Select...</a>
</p>
<!-- /ko -->

<!-- ko foreach: { data: attachedUsers, as: 'user' } -->
<div class="user" data-bind="css: { 'user-selected' : user.selected() }">
    <img data-bind="attr: { 'src': user.thumbnail }" class="img-circle">
    <p data-bind="text: user.login"></p>
    <p data-bind="text: user.email"></p>

    <button type="button" class="btn btn-sm" data-bind="
        css: { 'btn-danger' : user.selected() },
        click: function() { $parent.detachUser(user); },
        disable: user.busy
        ">

        <!-- ko if: user.busy() -->
        <i class="fa fa-spinner"></i>
        <span data-bind="text: user.selected() ? 'Detaching...' : 'Attaching...'"></span>
        <!-- /ko -->

        <!-- ko if: ! user.busy() -->
        <i class="fa fa-user-times"></i> Detach
        <!-- /ko -->
    </button>

</div>

<hr>
<!-- /ko -->

<!-- /ko -->