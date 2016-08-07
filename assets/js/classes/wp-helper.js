var WpHelper = (function () {
    function WpHelper() {
    }
    /**
     * Generates an edit post url via a post ID.
     * @param postId
     * @returns {string}
     */
    WpHelper.getEditPostUrl = function (postId) {
        return ajaxurl.replace('admin-ajax.php', '') + 'post.php?post=' + postId + '&action=edit';
    };
    return WpHelper;
}());
