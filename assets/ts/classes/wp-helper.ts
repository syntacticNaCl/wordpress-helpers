class WpHelper
{
    /**
     * Generates an edit post url via a post ID.
     * @param postId
     * @returns {string}
     */
    static getEditPostUrl(postId: number)
    {
        return ajaxurl.replace('admin-ajax.php','') + 'post.php?post=' + postId + '&action=edit';
    }
}