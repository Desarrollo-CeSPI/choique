var current_index=0;
jQuery(document).ready(function()
{
  jQuery(".article_multimedia_gallery .main_image").addClass(jQuery(".article_multimedia_gallery .image_thumb ul.articles_list li:first").attr('class'));
  jQuery(".article_multimedia_gallery .image_thumb ul.articles_list li:first").addClass('active'); //Add the active class (highlights the very first list item by default)
  jQuery(".article_multimedia_gallery .image_thumb ul.articles_list li").hover(function(){
    jQuery(this).addClass('mouse_over'); //Add class "hover" on hover
    selectTab(this);
  }, function() {
    jQuery(this).removeClass('hover'); //Remove class "hover" on hover out
    jQuery(this).removeClass('mouse_over'); //Remove class "hover" on hover out
  });
  jQuery(".article_multimedia_gallery .main_image").hover(function(){
    jQuery(this).addClass('mouse_over');
  }, function(){
    jQuery(this).removeClass('mouse_over');
  });
  setInterval('rotate()', 4000);
});

function selectTab(galery, tab, index)
{
  var galery = jQuery(galery);  
  var tab    = jQuery(tab);
  
  jQuery(galery).find('.articles_number_list_container .articles_number_list .articles_number_list_element').removeClass('active');

  var box = jQuery(galery).find('.articles_number_list_container .articles_number_list .articles_number_list_element_' + index).addClass('active');
  
  tab.addClass('hover'); //Add class "hover" on hover

  //Set Variables
  var imgAlt = tab.find('img').attr("alt"); //Get Alt Tag of Image
  var imgTitle = tab.find('a').attr("href"); //Get Main Image URL
  var imgDesc = tab.find('.block').html();  //Get HTML of the "block" container
  var imgDescHeight = jQuery(galery).find(".main_image").find('.block').height(); //Find the height of the "block"
  var link_to = tab.find('a.rm_article_gallery_view_more').attr("href");

  if (tab.is(".active"))
  {
    //If the list item is active/selected, then...
    return false; // Don't click through - Prevents repetitive animations on active/selected list-item
  }
  else
  {
    //If not active then...
    //Animate the Description
    jQuery(galery).find(".main_image .block").animate({ opacity: 0, width: '100%' }, 250 , function() { //Pull the block down (negative bottom margin of its own height)
      jQuery(galery).find(".main_image .block").html(imgDesc).animate({ opacity: 1,  marginBottom: "0" }, 450 ); //swap the html of the block, then pull the block container back up and set opacity
      jQuery(galery).find(".main_image .block").attr('style', jQuery(tab).find('.block').attr('style'));
      jQuery(galery).find(".main_image img").attr({ src: imgTitle , alt: imgAlt}); //Switch the main image (URL + alt tag)
      jQuery(galery).find(".main_image a").attr('href', link_to);
    });
  }

  //Show active list-item
  jQuery(galery).find(".image_thumb ul.articles_list li").removeClass('active'); //Remove class of 'active' on all list-items
  jQuery(galery).find(".main_image").removeClass(jQuery(galery).find(".main_image").attr('class')).addClass(jQuery(tab).attr('class')).addClass('main_image');
  tab.addClass('active');  //Add class of 'active' on the selected list

  return false;
}

function rotate()
{
  jQuery(".article_multimedia_gallery ").each(function (index,element) {
       
        var ok = !jQuery(element).find(".main_image").is(".mouse_over");

        jQuery(element).find(".image_thumb ul.articles_list li").each(function() {
 
          if ((jQuery(this).is('.mouse_over')))
          {
            ok = false;
          }
        });

        if (ok == true)
        {
          var elements = jQuery(element).find(".image_thumb ul.articles_list li");
          var total    = elements.size();

          var_name=element.id+'_index';
          eval("var_value = "+var_name);
          if ( var_value == (total-1))
          {
            eval(var_name + " =0 ");
          }
          else
          {
            eval(var_name + " ++ ");
          }
          eval("var_value = "+var_name);
    
          selectTab(element, elements[var_value], var_value + 1);
        }
  });
}

function selectTabByNumber(galery_id, element_number)
{
  galery = jQuery('#'+galery_id);
  var tab = jQuery(galery).find('.image_thumb ul.articles_list li.article_gallery_element_' + (element_number-1))[0];
  selectTab(galery, tab, element_number);
}


