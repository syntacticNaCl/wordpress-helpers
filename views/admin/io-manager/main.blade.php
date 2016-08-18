<div class="wrap">
    <h2>IO Manager</h2>

    <hr>

    <div class="wpbs" id="io-settings-wrapper">

        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#io-transfer" role="tab" data-toggle="tab">Transfer Data</a></li>
            <li><a href="#io-settings" role="tab" data-toggle="tab">Settings</a></li>
        </ul>

        <div class="tab-content">
            <div class="active tab-pane fade in" id="io-transfer">
                @include('admin.io-manager.pages.transfer-data')
            </div>

            <div class="tab-pane fade" id="io-settings">
                @include('admin.io-manager.pages.settings')
            </div>
        </div>

    </div>

</div>

<script>
    // Print preload data.
    var ioManagerPreload = {!! json_encode( $options ) !!};

    // On jQuery ready.
    jQuery(document).ready(function()
    {
        // Bind knockout.
        ko.applyBindings( new IOManagerViewModel( ioManagerPreload ), document.getElementById( 'io-settings-wrapper' ) );
    });
</script>
