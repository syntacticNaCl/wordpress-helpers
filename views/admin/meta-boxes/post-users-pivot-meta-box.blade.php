<div id="{{ $options['elementId'] }}" class="wpbs post-users-pivot-metabox">
    @include('admin.meta-boxes.post-users-pivot.select-users')
    @include('admin.meta-boxes.post-users-pivot.selected-users')
    @include('admin.meta-boxes.post-users-pivot.view-controls')
</div>

<script>
    var vm;
    jQuery(document).ready(function() {
        vm = new PostUsersPivoterMetaBoxViewModel( {!! json_encode( $options ) !!} );
    });
</script>