<?php
  /**
   *
   *  CarouselComponentHelper.php
   *
   *  Carousel Component properties (item(default value): description)
   *    firstVisible(1):                            sets wich item should be the first
   *                                                visible item in the carousel.
   *    scrollBeforeAmount(0):                      Normally set to 0. How much you are
   *                                                allowed to scroll below the first item.
   *                                                Setting it to 2 allows you to scroll to
   *                                                the -1 position. However, the load handlers
   *                                                will not be asked to load anything below 1.
   *    scrollAfterAmount(0):                       Normally set to 0. How much you are allowed
   *                                                to scroll past the end (size). Setting it
   *                                                to 2 allows you to scroll to the
   *                                                size+scrollAfterAmount position. However,
   *                                                the load handlers will not be asked to
   *                                                load anything beyond size.
   *    numVisible(3):                              The number of items that will be visible.
   *    scrollInc(3):                               The number of items to scroll by.
   *    size(1000000):                              The upper limit for scrolling in the 'next'
   *                                                set of content. Set to a large value by
   *                                                default (this means unlimited scrolling.)
   *                                                Normally scrolling be limited by the size property.
   *                                                If wrap is set to true, the carousel will wrap
   *                                                back to start when the end is pushed past the
   *                                                size value.
   *    wrap(false):                                Specifies whether to wrap when at the end of
   *                                                scrolled content. Only has effect when the
   *                                                size attribute is set.
   *    revealAmount(0):                            How many pixels to reveal of the before & after
   *                                                items in the list. For example, setting it to 20,
   *                                                will ensure that 20 pixels of the before
   *                                                object are shown and 20 pixels of the after
   *                                                object will be shown.
   *    orientation('horizontal'):                  Either "horizontal" or "vertical". Changes carousel
   *                                                from a left/right style carousel to a up/down style carousel.
   *    navMargin(0):                               The margin space for the navigation controls. This is only
   *                                                useful for horizontal carousels in which you have
   *                                                embedded navigation controls. The navMargin allocates
   *                                                space between the left and right margins (each navMargin wide)
   *                                                giving space for the navigation controls.
   *    animationSpeed(0.25):                       The time (in seconds) it takes to complete the scroll
   *                                                animation. If set to 0, animated transitions are turned
   *                                                off and the new page of content is moved immdediately into place.
   *    animationMethod(YAHOO.util.Easing.easeOut): The YAHOO.util.Easing method.
   *    animationCompleteHandler(null):             JavaScript function that is called when the Carousel
   *                                                finishes animation after a next or previous nagivation.
   *                                                Only invoked if animationSpeed > 0. Two parameters
   *                                                are passed: type (set to 'onAnimationComplete') and
   *                                                args array (args[0] = direction [either: 'next' or 'previous']).
   *    autoPlay(0):                                Specifies how many milliseconds to periodically auto scroll
   *                                                the content. If set to 0 (default) then autoPlay is turned
   *                                                off. If the user interacts by clicking left or right
   *                                                navigation, autoPlay is turned off. You can restart autoPlay
   *                                                by calling the startAutoPlay(). If you externally control
   *                                                navigation (with your own event handlers) then you can turn
   *                                                off the autoPlay by callingstopAutoPlay()
   *    disableSelection(true):                     Specifies whether to turn off browser text selection within
   *                                                the carousel. Default is true.
   *    loadOnStart(true):                          If true, will call loadInitHandler on startup. If false,
   *                                                will not. Useful for delaying the initialization of
   *                                                the carousel for a later time after creation.
   *    loadInitHandler(null):                      JavaScript function that is called when the Carousel needs
   *                                                to load the initial set of visible items.
   *                                                Two parameters are passed: type (set to 'onLoadInit') and
   *                                                args array. The args array contains 3 values;
   *                                                args[0] = start index, args[1] = last index,
   *                                                args[2] = alreadyCached flag [items in range already created].
   *                                                If revealAmount is non-zero the carousel will reveal
   *                                                a portion of the item just before and/or the item just
   *                                                after. In this case args[0] and args[1] will be
   *                                                augmented as necessary. 
   *    loadNextHandler(null):                      JavaScript function that is called when the Carousel needs
   *                                                to load a next set of visible items.
   *                                                Two parameters are passed: type (set to 'onLoadInit') and
   *                                                args array. The args array contains 3 values;
   *                                                args[0] = start index, args[1] = last index,
   *                                                args[2] = alreadyCached flag [items in range already created].
   *                                                If revealAmount is non-zero the carousel will reveal a portion
   *                                                of the item just before and/or the item just after.
   *                                                In this case args[0] and args[1] will be augmented as necessary.
   *    loadPrevHandler(null):                      JavaScript function that is called when the Carousel needs
   *                                                to load a previous set of visible items.
   *                                                Two parameters are passed: type (set to 'onLoadInit')
   *                                                and args array. The args array contains 3 values;
   *                                                args[0] = start index, args[1] = last index,
   *                                                args[2] = alreadyCached flag [items in range already created].
   *                                                If revealAmount is non-zero the carousel will reveal a portion
   *                                                of the item just before and/or the item just after.
   *                                                In this case args[0] and args[1] will be augmented as necessary.
   *    prevElement(null):                          The element ID (string ID) or element object of the HTML
   *                                                element that will provide the previous navigation control.
   *                                                Can be a list (array) of element IDs or element objects.
   *    nextElement(null):                          The element ID (string ID) or element object of the HTML
   *                                                element that will provide the previous navigation control.
   *                                                Can be a list (array) of element IDs or element objects.
   *    prevButtonStateHandler(null):               JavaScript function that is called when the enabled state
   *                                                of the 'previous' control is changing. The responsibility
   *                                                of this method is to enable or disable the 'previous'
   *                                                control. Two parameters are passed to this method:
   *                                                type (which is set to "onPrevButtonStateChange") and args,
   *                                                an array that contains two values. The parameter args[0]
   *                                                is a flag denoting whether the 'previous' control is
   *                                                being enabled or disabled. The parameter args[1] is the
   *                                                element object derived from the prevElement parameter.
   *    nextButtonStatehandler(null):               JavaScript function that is called when the enabled state
   *                                                of the 'next' control is changing. The responsibility of
   *                                                this method is to enable or disable the 'next' control.
   *                                                Two parameters are passed to this method: type
   *                                                (which is set to "onPrevButtonStateChange") and args,
   *                                                an array that contains two values. The parameter args[0]
   *                                                is a flag denoting whether the 'next' control is being
   *                                                enabled or disabled. The parameter args[1] is the element
   *                                                object derived from the nextElement parameter.
   *
   *  @author Patricio Mac Adden <pmacadden@desarrollo.cespi.unlp.edu.ar>
   *  @link http://billwscott.com/carousel
   *  @version 0.0.1
   *
   */

  /**
   *
   *  Adds needed javascript and css resources for runnning Carousel Component.
   *
   */
  function _add_resources_cc()
  {
    $response = sfContext::getInstance()->getResponse();

    $response->addJavascript('/sfYUIPlugin/js/yui/yahoo/yahoo.js');
    $response->addJavascript('/sfYUIPlugin/js/yui/event/event.js');
    $response->addJavascript('/sfYUIPlugin/js/yui/container/container_core.js');
    $response->addJavascript('/sfYUIPlugin/js/yui/dom/dom.js');
    $response->addJavascript('/sfYUIPlugin/js/yui/animation/animation.js');
    $response->addJavascript('/pmCarouselComponentPlugin/js/carousel.js');
  }

  /**
   *
   *  Defines handlePrevButtonState and handleNextButtonState variables used
   *  for handling carousel's buttons.
   *  @return string javascript code defining handlePrevButtonState and handleNextButtonState
   *  variables
   *
   */
  function _horizontal_vars()
  {
    sfLoader::loadHelpers(array('Javascript'));

    return javascript_tag('var handlePrevButtonState = function(type, args) {
                             var enabling = args[0];
                             var leftImage = args[1];
                             if(enabling) {
                               leftImage.src = "'.image_path('/pmCarouselComponentPlugin/images/left-enabled.gif').'";
                             } else {
                               leftImage.src = "'.image_path('/pmCarouselComponentPlugin/images/left-disabled.gif').'";
                             }
                           };
                           var handleNextButtonState = function(type, args) {
                             var enabling = args[0];
                             var rightImage = args[1];
                             if(enabling) {
                               rightImage.src = "'.image_path('/pmCarouselComponentPlugin/images/right-enabled.gif').'";
                             } else {
                               rightImage.src = "'.image_path('/pmCarouselComponentPlugin/images/right-disabled.gif').'";
                             }
                           };');
  }

  /**
   *
   *  Defines handleUpButtonState and handleDownButtonState variables used
   *  for handling carousel's buttons.
   *  @return string javascript code defining handleUpButtonState and handleDownButtonState
   *  variables
   *
   */
  function _vertical_vars()
  {
    sfLoader::loadHelpers(array('Javascript'));

    return javascript_tag('var handleUpButtonState = function(type, args) {
                             var enabling = args[0];
                             var upImage = args[1];
                             if(enabling) {
                               upImage.src = "'.image_path('/pmCarouselComponentPlugin/images/up-enabled.gif').'";
                             } else {
                               upImage.src = "'.image_path('/pmCarouselComponentPlugin/images/up-disabled.gif').'";
                             }
                           };
                           var handleDownButtonState = function(type, args) {
                             var enabling = args[0];
                             var downImage = args[1];
                             if(enabling) {
                               downImage.src = "'.image_path('/pmCarouselComponentPlugin/images/down-enabled.gif').'";
                             } else {
                               downImage.src = "'.image_path('/pmCarouselComponentPlugin/images/down-disabled.gif').'";
                             }
                           };');
  }

  function _parse_options_cc($options)
  {
    $keys = array_keys($options);
    $str = '';
    foreach ($keys as $key):
      $str .= $key.': '.$options[$key].', ';
    endforeach;
    return substr($str, 0, strlen($str) - 2);
  }

  /**
   *
   *  Returns an horizontal Carousel Component instance.
   *  @param string $name a name for the carousel component instance.
   *  @param array $hrefs an array of images paths.
   *  @param array $options an array of options for Carousel Component.
   *  @return string a Carousel Component instance.
   *
   */
  function carousel_component_horizontal($name, $hrefs, $options = array())
  {
    _add_resources_cc();
    $response = sfContext::getInstance()->getResponse();
    $response->addStylesheet('/pmCarouselComponentPlugin/css/carousel-component-horizontal');

    sfLoader::loadHelpers(array('Javascript', 'CmsEscaping'));

    $str = _horizontal_vars();

    $str .= tag('div', array('id' => escape_string($name), 'class' => 'carousel-component-horizontal'), true);
    $str .= tag('div', array('class' => 'carousel-prev'), true);
    $str .= image_tag('/pmCarouselComponentPlugin/images/left-disabled.gif', array('id' => 'prev-arrow-'.escape_string($name), 'class' => 'left-button-image', 'alt' => 'Previous button'));
    $str .= tag('/div', array(), true);
    $str .= tag('div', array('class' => 'carousel-next'), true);
    $str .= image_tag('/pmCarouselComponentPlugin/images/right-enabled.gif', array('id' => 'next-arrow-'.escape_string($name), 'class' => 'right-button-image', 'alt' => 'Next button'));
    $str .= tag('/div', array(), true);
    $str .= tag('div', array('class' => 'carousel-clip-region'), true);
    $str .= tag('ul', array('class' => 'carousel-list'), true);

    for ($i = 1; $i <= count($hrefs); $i++) {
      $str .= tag('li', array('id' => escape_string($name)."-item-$i"), true);
      $str .= $hrefs[$i-1];
      $str .= tag('/li', array(), true);
    }

    $str .= tag('/ul', array(), true);
    $str .= tag('/div', array(), true);
    $str .= content_tag('div', $name, array('class' => 'name'));
    $str .= tag('/div', array(), true);

    $prevButtonStateHandler = (!array_key_exists('prevButtonStateHandler', $options))?'prevButtonStateHandler: handlePrevButtonState, ':'';
    $nextButtonStateHandler = (!array_key_exists('nextButtonStateHandler', $options))?'nextButtonStateHandler: handleNextButtonState, ':'';
    $prevElement = (!array_key_exists('prevElement', $options))?'prevElement: "prev-arrow-'.escape_string($name).'", ':'';
    $nextElement = (!array_key_exists('nextElement', $options))?'nextElement: "next-arrow-'.escape_string($name).'", ':'';

    $str .= javascript_tag("var carousel = new YAHOO.extension.Carousel(\"".escape_string($name)."\", { $prevButtonStateHandler$nextButtonStateHandler$prevElement$nextElement
                                                                                     size: ".count($hrefs).", "._parse_options_cc($options)."});");

    return $str;
  }

  /**
   *
   *  Returns a vertical Carousel Component instance.
   *  @param string $name a name for the carousel component instance.
   *  @param array $hrefs an array of images paths.
   *  @param array $options an array of options for Carousel Component.
   *  @return string a Carousel Component instance.
   *
   */
  function carousel_component_vertical($name, $hrefs, $options = array())
  {
    _add_resources_cc();
    $response = sfContext::getInstance()->getResponse();
    $response->addStylesheet('/pmCarouselComponentPlugin/css/carousel-component-vertical');

    sfLoader::loadHelpers(array('Javascript', 'CmsEscaping'));

    $str = _vertical_vars();

    $str .= tag('div', array(), true);
    $str .= image_tag('/pmCarouselComponentPlugin/images/up-disabled.gif', array('id' => 'up-arrow-'.escape_string($name), 'class' => 'left-button-image', 'alt' => 'Previous button'));
    $str .= tag('/div', array(), true);
    $str .= tag('div', array('id' => escape_string($name), 'class' => 'carousel-component-vertical'), true);
    $str .= tag('div', array('class' => 'carousel-clip-region'), true);
    $str .= tag('ul', array('class' => 'carousel-list'), true);

    for ($i = 1; $i <= count($hrefs); $i++) {
      $str .= tag('li', array('id' => escape_string($name)."-item-$i"), true);
      $str .= $hrefs[$i-1];
      $str .= tag('/li', array(), true);
    }

    $str .= tag('/ul', array(), true);
    $str .= tag('/div', array(), true);
    $str .= tag('/div', array(), true);
    $str .= tag('div', array(), true);
    $str .= image_tag('/pmCarouselComponentPlugin/images/down-enabled.gif', array('id' => 'down-arrow-'.escape_string($name), 'class' => 'right-button-image', 'alt' => 'Next button'));
    $str .= tag('/div', array(), true);

    $prevButtonStateHandler = (!array_key_exists('prevButtonStateHandler', $options))?'prevButtonStateHandler: handleUpButtonState, ':'';
    $nextButtonStateHandler = (!array_key_exists('nextButtonStateHandler', $options))?'nextButtonStateHandler: handleDownButtonState, ':'';
    $prevElement = (!array_key_exists('prevElement', $options))?'prevElement: "up-arrow-'.escape_string($name).'", ':'';
    $nextElement = (!array_key_exists('nextElement', $options))?'nextElement: "down-arrow-'.escape_string($name).'", ':'';

    $str .= javascript_tag("var carousel = new YAHOO.extension.Carousel(\"".escape_string($name)."\", { $prevButtonStateHandler$nextButtonStateHandler$prevElement$nextElement
                                                                                     size: ".count($hrefs).", orientation: 'vertical', "._parse_options_cc($options)."});");

    return $str;
  }

?>
