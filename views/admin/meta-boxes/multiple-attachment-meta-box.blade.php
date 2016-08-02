<div id="{{ $options['elementId'] }}" class="wpbs">

    {{-- Tab Labels --}}

    <div>
        <ul class="nav nav-tabs" role="tablist">
            <!-- ko foreach: options -->
            <li role="presentation" data-bind="css: { active: $parent.activeKey() == $data.label }">
                <a href="#" role="tab" data-toggle="tab" data-bind="
                    text: $data.label,
                    click: function() { $parent.activeKey( $data.key ); },
                    attr: { href: '#' + $data.key + '_tab_panel' }
                "></a>
            </li>
            <!-- /ko -->
        </ul>

        <div class="tab-content">
            <!-- ko foreach: options -->
            <div role="tabpanel" class="tab-pane fade" data-bind="
                attr: { id: $data.key + '_tab_panel' },
                css: { 'in active': $parent.activeKey() == $data.key }">

                <input type="hidden" data-bind="attr: { name: $data.key }, value: $data.valueString">
                <input type="hidden" data-bind="value: $data.type, attr: { name: $data.key + '_type' }">

                <!-- ko if: 'url' == $data.type() -->
                <!-- ko with: $data.types.url -->
                @include('admin.meta-boxes.types.url-attachments')
                <!-- /ko -->
                <!-- /ko -->

                <!-- ko if: 'wp' == $data.type() -->
                <!-- ko with: $data.types.wp -->
                @include('admin.meta-boxes.types.wp-media-media-attachments')
                <!-- /ko -->
                <!-- /ko -->
            </div>
            <!-- /ko -->
        </div>
    </div>

</div>

<script>
    jQuery(document).ready(function()
    {
        var data = {!! json_encode( $options ) !!};

        new MultipleAttachmentMetaboxViewModel( data );

        console.log( data );
    });
</script>