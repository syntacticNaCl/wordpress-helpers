<div id="{{ $options['elementId'] }}" class="wpbs">

    <input type="hidden" name="{{ $metaKey }}" data-bind="value: valueString">
    <input type="hidden" name="{{ $metaKey }}_type" data-bind="value: type">

    <!-- ko if: 'url' == type() -->
    <!-- ko with: types.url -->
    @include('admin.meta-boxes.types.url-attachments')
    <!-- /ko -->
    <!-- /ko -->

    <!-- ko if: 'wp' == type() -->
    <!-- ko with: types.wp -->
    @include('admin.meta-boxes.types.wp-media-media-attachments')
    <!-- /ko -->
    <!-- /ko -->
</div>

<script>
    jQuery(document).ready(function()
    {
        // Instantiate the PostsPivoterViewModel
        new AttachmentMetaboxViewModel( {!! json_encode( $options ) !!} );
    });
</script>