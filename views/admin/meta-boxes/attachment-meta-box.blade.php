<div id="{{ $options['elementId'] }}" class="wpbs">

    <input type="hidden" name="{{ $metaKey }}" data-bind="value: valueString">

    <!-- ko if: attachmentIds().length == 0 -->
    <p>No attachments.</p>
    <!-- /ko -->

    <!-- ko if: attachmentIds().length > 0 -->
    <!-- ko foreach: collection -->
    <div class="attached-model">
        <img data-bind="attr: { src: $data.sizes.thumbnail.url }" class="img-thumbnail">
        <ko-button params="text: 'Remove', class: 'btn-danger btn-sm', click: function(){ $parent.removeModel($data); }"></ko-button>
    </div>
    <!-- /ko -->
    <!-- /ko -->

    <hr>

    <button class="btn btn-default" type="button" data-bind="
        click: function() { selectAttachment(); }
    ">Select File</button>
</div>

<script>
    jQuery(document).ready(function()
    {
        // Instantiate the PostsPivoterViewModel
        new AttachmentMetaboxViewModel( {!! json_encode( $options ) !!} );
    });
</script>