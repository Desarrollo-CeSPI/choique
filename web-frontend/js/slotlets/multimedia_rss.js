var current_index = 0;
jQuery(document).ready(
function ()
{
  jQuery(".main_image").addClass(jQuery(".image_thumb ul li:first").attr('class'));
  jQuery(".image_thumb ul li:first").addClass('active'); //Add the active class (highlights the very first list item by default)
  jQuery(".image_thumb ul li").hover(function(){
    jQuery(this).addClass('mouse_over'); //Add class "hover" on hover 
    selectTab(this);
  }, function() {
    jQuery(this).removeClass('hover'); //Remove class "hover" on hover out
    jQuery(this).removeClass('mouse_over'); //Remove class "hover" on hover out
  });
  jQuery(".main_image").hover(function(){
    jQuery(this).addClass('mouse_over'); 
  }, function(){
    jQuery(this).removeClass('mouse_over'); 
  });
  
  setInterval('rotate()', 4000);
});

function selectTab(tab)
{
    jQuery(tab).addClass('hover'); //Add class "hover" on hover 
    
    //Set Variables
    var imgAlt = jQuery(tab).find('img').attr("alt"); //Get Alt Tag of Image
    var imgTitle = jQuery(tab).find('a').attr("href"); //Get Main Image URL
    var imgDesc = jQuery(tab).find('.block').html();  //Get HTML of the "block" container
    var imgDescHeight = jQuery(".main_image").find('.block').height(); //Find the height of the "block"
    var link_to = jQuery(tab).find('a.feed_view_more').attr("href");

    if (jQuery(tab).is(".active")) {  //If the list item is active/selected, then...
        return false; // Don't click through - Prevents repetitive animations on active/selected list-item
    } else { //If not active then...
        //Animate the Description
        jQuery(".main_image .block").animate({ opacity: 0, marginBottom: -imgDescHeight }, 250 , function() { //Pull the block down (negative bottom margin of its own height)
            jQuery(".main_image .block").html(imgDesc).animate({ opacity: 0.75,  marginBottom: "0" }, 450 ); //swap the html of the block, then pull the block container back up and set opacity
            jQuery(".main_image img").attr({ src: imgTitle , alt: imgAlt}); //Switch the main image (URL + alt tag)
            jQuery(".main_image a").attr({href: link_to});
        });
    }
    //Show active list-item
    jQuery(".image_thumb ul li").removeClass('active'); //Remove class of 'active' on all list-items
    jQuery(".main_image").removeClass(jQuery(".main_image").attr('class')).addClass(jQuery(tab).attr('class')).addClass('main_image');
    jQuery(tab).addClass('active');  //Add class of 'active' on the selected list
    return false; 
}

function rotate()
{
  var ok = !jQuery(".main_image").is(".mouse_over");
  jQuery(".image_thumb ul li").each(function() {    
    if ((jQuery(this).is('.mouse_over')))
    {
      ok = false;
    }
  });

  if (ok == true)
  {
    var total = jQuery(".image_thumb ul li").size();
    var elements = jQuery(".image_thumb ul li");
    if(current_index == (total-1)){
      current_index = 0;
    }
    else{
      current_index = current_index + 1;
    }
    selectTab(elements[current_index]);
    
  }
}
