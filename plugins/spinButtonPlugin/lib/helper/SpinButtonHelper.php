<?php
/**
 *	Converts any input field with the provided Id(s) to a
 *	spin button kind of field (it adds an increase and a decrease button
 *	next to it, which obviously increase and decrease the value of the
 *	input field.
 *
 *		$fieldIds might be either a single string or an array of strings.
 *		$options  should be an associative array holding the options passed
 *				  to the helper's function.
 *
 *	The function returns a string with the html that should be echoed
 *	in an html document.
 *
 *	Configurable options:
 *		. min		:  number  : minimum value allowed
 *		. max		:  number  : maximum value allowed
 *		. step		:  number  : amount of units to increment/decrement.
 *		. up_img	:  string  : name of the increase's image file
 *		. down_img 	:  string  : name of the decrease's image file
 *		. readonly	:  boolean : whether the input fields should be turned
 *								 readonly or left as are. (currently disabled)
 *
 *	@return string
 */
function convert_spin_buttons($fieldIds, $options = array())
{
	sfLoader::loadHelpers(array('Javascript', 'Asset'));
	
	sfContext::getInstance()->getResponse()->addJavascript('/spinButtonPlugin/js/spinbutton/functions');
	//sfContext::getInstance()->getResponse()->addJavascript('/spinButtonPlugin/js/prototype-1.6');
	
	$min	= (isset($options['min'])) ? $options['min'] : 0;
	$max	= (isset($options['max'])) ? $options['max'] : 100;
	$step	= (isset($options['step'])) ? $options['step'] : 1;
	$up_arr = (isset($options['up_img'])) ? $options['up_img'] : '/spinButtonPlugin/images/spinbutton/arrow_up.png';
	$dn_arr = (isset($options['down_img'])) ? $options['down_img'] : '/spinButtonPlugin/images/spinbutton/arrow_down.png';
//	$ro		= (isset($options['readonly'])) ? $options['readonly'] : true;
	$ro		= true;
	
	$up_arr_path = image_path($up_arr);
	$dn_arr_path = image_path($dn_arr);
	$time = time();

	if (!is_array($fieldIds))
		$fieldIds = array($fieldIds);
	$code = "function convert$time() {\nclearInterval(intvl);\n";
	foreach ($fieldIds as $fieldId) {
		$code .= "\tconvert_input_to_spin('$fieldId', $ro, '$up_arr_path', '$dn_arr_path', $step, $min, $max);\n";
	}
	$code .= "\n}\n\nif(document.readyState == 'complete') {\n\tconvert$time();\n} else {\n\tintvl = setInterval('convert$time()', 1000);\n}";

	return javascript_tag($code);
}

/**
 *	Converts any form select field with the provided Id(s) to a
 *	slider button (it adds a left and a right button next to it, which
 *	simulate transitions through a sequence of items).
 *
 *		$selectIds might be either a single string or an array of strings.
 *		$options   should be an associative array holding the options passed
 *				   to the helper's function.
 *
 *	The function returns a string with the html that should be echoed
 *	in an html document.
 *
 *	Configurable options:
 *		. left_img	:  string  : name of the increase's image file
 *		. right_img :  string  : name of the decrease's image file
 *
 *	@return string
 */
function convert_sliders($selectIds, $options = array())
{
	sfLoader::loadHelpers(array('Javascript', 'Asset'));
	
	sfContext::getInstance()->getResponse()->addJavascript('/spinButtonPlugin/js/spinbutton/functions');
	sfContext::getInstance()->getResponse()->addJavascript('/spinButtonPlugin/js/prototype-1.6');
	
	$left_arr  = (isset($options['left_img'])) ? $options['left_img'] : '/spinButtonPlugin/images/spinbutton/arrow_left.png';
	$right_arr = (isset($options['right_img'])) ? $options['right_img'] : '/spinButtonPlugin/images/spinbutton/arrow_right.png';
	
	$left_arr_path  = image_path($left_arr);
	$right_arr_path = image_path($right_arr);
	$time = time();
	
	if (!is_array($selectIds))
		$selectIds = array($selectIds);
	$code = "function convert$time() {\nclearInterval(intvl);\n";
	foreach ($selectIds as $selectId) {
		$code .= "\tconvert_select_to_slider('$selectId', '$left_arr_path', '$right_arr_path');\n";
	}
	$code .= "\n}\n\nif(document.readyState == 'complete') {\n\tconvert$time();\n} else {\n\tintvl = setInterval('convert$time()', 1000);\n}";

	return javascript_tag($code);
}
