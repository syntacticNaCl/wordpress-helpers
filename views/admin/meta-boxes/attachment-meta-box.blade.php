<div id="{{ $options['elementId'] }}" class="wpbs">

    <input type="hidden" name="{{ $metaKey }}" data-bind="value: valueString">
    <input type="hidden" name="{{ $metaKey }}_type" data-bind="value: type">

    @include('admin.meta-boxes.types.url-attachments')
    @include('admin.meta-boxes.types.wp-media-media-attachments')
</div>

<script>
    jQuery(document).ready(function()
    {
        // Instantiate the PostsPivoterViewModel
        new AttachmentMetaboxViewModel( {!! json_encode( $options ) !!} );
    });
</script>