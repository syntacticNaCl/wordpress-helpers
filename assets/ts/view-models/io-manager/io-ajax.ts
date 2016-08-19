class IOAjax
{
    static post(action, data, success, fail?)
    {
        let postData = {
            action: 'io_' + action,
        };

        // Merge data.
        _.each( data, (item, key) => {
            postData[key] = item;
        });

        jQuery.post(
            ajaxurl,
            postData,
            function(r)
            {
                if ( success ) {
                    success(r);
                }
            }
        ).fail((r) => {
            if ( fail )
            {
                fail(r.responseJSON);
            }
        });
    }
}