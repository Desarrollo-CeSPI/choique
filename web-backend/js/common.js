function changeLayout() {
	var link = $('_css_layout_');
	var size = ((link.getProperty('href')).test('800')) ? 1024 : 800;
	var cssfilename = '../css/layout' + size + '.css';
	link.setProperty('href', cssfilename);
}

function sugestName()  {
    var str = $('multimedia_title').value;
    $('multimedia_name').value = str.replace(/ /g, '_');
}


/*****************************************************************
 * Functions related to article actions (pdf, enlarge text, etc.)
 *
 *****************************************************************/
/**
 * Arguments:
 *      - String element: the element tho which apply the change
 *      - int enlargeBy: the pixels to be added to the actual size
 */

var enlargeTextTmp = 100;
function enlargeText(element, enlargeBy){
    enlargeTextTmp = enlargeTextTmp + enlargeBy;
    $$('#' + element + ' .body').each(
        function(e){
            e.style.fontSize = enlargeTextTmp + "%";
            if (enlargeBy > 0)
                e.style.lineHeight = e.style.lineHeight + 125 + "%";
            if (enlargeBy < 0)
                e.style.lineHeight = e.style.lineHeight - 125 + "%";
        }
    );
}


/*****************************************************************
 * Funtion that creates a new named window 
 *
 *****************************************************************/
/**
 * Arguments:
 *      - String url
 *      - String name of this window
 *      - int height
 *      - int width
 *      - String parameters to js window open. If null we assume
 *        resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no, status=no, menubar=no,copyhistory=no
 *           1. width=300
 *                Use this to define the width of the new window.
 *
 *            2. height=200
 *                Use this to define the height of the new window.
 *
 *            3. resizable=yes or no
 *                Use this to control whether or not you want the user to be able to resize the window.
 *
 *            4. scrollbars=yes or no
 *                This lets you decide whether or not to have scrollbars on the window.
 *
 *            5. toolbar=yes or no
 *                Whether or not the new window should have the browser navigation bar at the top (The back, foward, stop buttons..etc.).
 *
 *            6. location=yes or no
 *                Whether or not you wish to show the location box with the current url (The place to type http://address).
 *
 *            7. directories=yes or no
 *                Whether or not the window should show the extra buttons. (what's cool, personal buttons, etc...).
 *
 *            8. status=yes or no
 *                Whether or not to show the window status bar at the bottom of the window.
 *
 *            9. menubar=yes or no
 *                Whether or not to show the menus at the top of the window (File, Edit, etc...).
 *
 *            10. copyhistory=yes or no
 *                Whether or not to copy the old browser window's history list to the new window. 
 */
function popup_window(url,name, height, width, params)
{
  if (null==height)
    height=500;
  if (null==width)
    width=750;
  if (null==params)
    params='resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no, status=no, menubar=no,copyhistory=no';
  var top_corner=(screen.height-height)/2;
  var left_corner =(screen.width-width)/2;
  params+=',height='+height+',width='+width+',top='+top_corner+',left='+left_corner;
  window.open(url,name,params);
}
