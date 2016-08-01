<div id="{{ $options['elementId'] }}" class="wpbs">

    <div class="btn-group">
        <button type="button" class="btn btn-default" data-bind="
            css: { active: 'all' == screen() },
            click: function(){ screen('all'); }">
            All
            <span class="badge" data-bind="text: collection().length"></span>
        </button>

        <button type="button" class="btn btn-default" data-bind="
            css: { active: 'related' == screen() },
            click: function(){ screen('related'); }">
            Related
            <span class="badge" data-bind="text: relatedIds().length"></span>
        </button>

        <button type="button" class="btn btn-default" data-bind="
            css: { active: showSearch() },
            click: function(){ showSearch( ! showSearch() ); }">
            <i class="fa fa-search"></i>
            Search
        </button>

        <!-- ko if: options.relatedPostsCreator -->
        <button type="button" class="btn btn-primary btn-default" data-bind="
            css: { active: showRelatedPostsCreator() },
            click: function(){ showRelatedPostsCreator( ! showRelatedPostsCreator() ); }">
            <i class="fa fa-plus"></i>
            Create {{ $labels['related_post_singular'] }}
        </button>
        <!-- /ko -->
    </div>

    <!-- ko if: options.relatedPostsCreator && showRelatedPostsCreator() -->
    <div class="form-group">
        {!! $relatedPostsForm !!}
    </div>
    <!-- /ko -->

    <div data-bind="visible: showSearch" class="form-group">
        <ko-input params="value: search, label: 'Search', placeholder: 'Search by title'"></ko-input>
    </div>

    <div data-bind="col-xs-12">
        <h2 data-bind="if: loading">
            Loading...
            <i class="fa fa-spinner fa-spin"></i>
        </h2>

        <div data-bind="if: ! loading()" class="posts-pivot-meta-box">

            <div data-bind="if: 0 == collection().length">
                There are no models.
            </div>

            <div class="list-group" style="max-height: 200px; overflow-x: hidden;">

                <div data-bind="foreach: filteredCollection">

                    <div class="list-group-item" style="padding: 0;" data-bind="css: { attached: -1 != $parent.relatedIds.indexOf( $data.id() ) }">

                        <h4 class="list-group-item-heading" style="padding: 0; margin: 0">

                            <!-- ko if: false == $data.thumbnail() -->
                            <img src="{{ WORDPRESS_HELPERS_URL . 'assets/img/no-image-150.png' }}" width="60">
                            <!-- /ko -->

                            <!-- ko if: false != $data.thumbnail() -->
                            <img src="{{ WORDPRESS_HELPERS_URL . 'assets/img/no-image-150.png' }}" data-bind="attr: { src: $data.thumbnail }"  width="60">
                            <!-- /ko -->

                            <!-- Title -->
                            <a target="_blank" data-bind="attr: { href: '{{ admin_url() . 'post.php?post=' }}' + $data.id() + '&action=edit' }, text: $data.title"></a>

                            <!-- Right Side Button Group -->
                            <div class="pull-right">
                                <div class="btn-group" style="position: relative; right: 10px; top: 12px;">

                                    <!-- ko if: -1 == $parent.relatedIds.indexOf( $data.id() ) -->
                                    <ko-button params="
                                        text: 'Attach',
                                        busyText: 'Attaching...',
                                        class: 'btn-primary',
                                        click: function(){ $parent.attach($data, this); }"></ko-button>
                                    <!-- /ko -->

                                    <!-- ko if: -1 != $parent.relatedIds.indexOf( $data.id() ) -->
                                    <ko-button params="
                                        text: 'Detach',
                                        busyText: 'Detaching...',
                                        class: 'btn-danger',
                                        click: function(){ $parent.detach($data, this); }"></ko-button>
                                    <!-- /ko -->
                                </div>
                            </div>
                        </h4>
                    </div>
                </div>

                <div data-bind="if: 0 == filteredCollection().length">
                    <p>There are no <span data-bind="text: 'all' == screen() ? 'available' : 'related'"></span> models.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    jQuery(document).ready(function()
    {
        // Instantiate the PostsPivoterViewModel
        new PostsPivoterViewModel( {!! json_encode( $options ) !!} );
    });
</script>