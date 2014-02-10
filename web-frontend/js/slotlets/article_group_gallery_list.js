jQuery(function($){
    //when a link in the filters div is clicked...
    $('.article_group_gallery_filters a').click(function(e){
        var container = $(this).closest('.article_group_gallery_container');

        //prevent the default behaviour of the link
        e.preventDefault();

        //get the id of the clicked link(which is equal to classes of our content
        var filter = $(this).attr('id');

        //show all the list items(this is needed to get the hidden ones shown)
        container.find('li').hide();

        /*using the :not attribute and the filter class in it we are selecting
         only the list items that don't have that class and hide them '*/
        container.find('li.' + filter).show();

        container.find('.selected').removeClass('selected');
        $(this).addClass('selected');

    }).first().trigger('click');
});
