<div id="{{ $options['elementId'] }}" class="wpbs">

    <input type="hidden" name="{{ $metaKey }}" data-bind="value: valueString">

    <!-- ko if: attachmentIds().length == 0 -->
    <p>No attachments.</p>
    <!-- /ko -->

    <div class="attachment-models">
    <!-- ko foreach: collection -->
    <div class="attachment-model-container" data-bind="attr: { 'data-id': $data.id }">

        <div class="attached-model">
            <img data-bind="attr: { src: $data.sizes.thumbnail.url }" class="img-thumbnail">

            <div class="img-controls">

                <ko-button params="
                    text: '',
                    icon: 'fa-times',
                    class: 'btn-circle-micro btn-danger delete-button',
                    click: function(){ $parent.removeModel($data); }"></ko-button>

                <br>

                <a href="#" class="btn btn-circle-micro btn-default move-button">
                    <i class="fa fa-arrows"></i>
                </a>

            </div>
        </div>
    </div>
    <!-- /ko -->
    </div>

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