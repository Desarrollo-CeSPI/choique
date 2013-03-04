function toggleMenuContent(section)
{
  jQuery('.dropdown-menu:visible').hide();//("slide", {direction: "up"}, 500);
  //jQuery('.dropdown-menu.' + section + '_content').show("slide", {direction: "down"}, 500);
  jQuery('.dropdown-menu.' + section + '_content').show();
  return false;
}

function hideShowChildrens(link)
{
  var elem = jQuery(link);
  if(elem.hasClass('show'))
  {
    elem.removeClass('show');
    elem.addClass('hide');
    elem.parent().next('ul').show();
  }
  else
  {
    elem.removeClass('hide');
    elem.addClass('show');
    elem.parent().next('ul').hide(); 
  }

  return false;
}