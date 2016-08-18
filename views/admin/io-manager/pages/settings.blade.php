<h3>Settings</h3>

<div>
    <ko-input params="
        label: 'Local Security Key',
        value: securityKey,
    "></ko-input>

    <small>
        Use this security key to pull or push data to this WordPress instance.

        <!-- ko if: ! resetting() -->
        [<a href="#" data-bind="click: function(){ resetKey(); }">Reset Key</a>]
        <!-- /ko -->

        <!-- ko if: resetting() -->
        [<i>Resetting...</i>]
        <!-- /ko -->
    </small>
</div>

<br>

<button class="btn btn-primary btn-sm" type="button" data-bind="click: function() { updateSettings(); }">Save Settings</button>