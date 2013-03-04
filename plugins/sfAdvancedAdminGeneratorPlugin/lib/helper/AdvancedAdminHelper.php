<?php
/**
 * Returns a html map control.
 *
 * @param object An object.
 * @param string An object column.
 * @param array Date options.
 * @param bool Date default value.
 *
 * @return string An html string which represents a date control.
 *
 */
function object_select_map_tag($object, $method, $options = array(), $default_value = null) {
	$map = (isset($options['map'])?$options['map']:array());
	unset($options['map']);
	return select_tag(_convert_method_to_name($method, $options), options_for_select($map), $options);
}

function object_input_auto_complete_tag($object, $method, $options = array(), $default_value = null) {
	$peer_table = _get_option($options, 'peer_table');
	$peer_field = _get_option($options, 'peer_field');
	$input_name = _convert_method_to_name($method, $options);
  $current_value = ($object) ? $object->$method() : null;
  
  $peer_class = sfInflector::camelize($peer_table).'Peer';
  $current_value = call_user_func(array($peer_class, 'retrieveByPk'), $current_value);
  
	echo input_auto_complete_tag("${peer_table}_${peer_field}_search", $current_value,
	    sfContext::getInstance()->getModuleName()."/autocomplete?table=$peer_table&field=$peer_field",
	    array('autocomplete' => 'off'),
	    array(
	    	'use_style'            => true,
	    	'after_update_element' => "function (inputField, selectedItem) { $('".get_id_from_name($input_name)."').value = selectedItem.id; }",
	    	'method' => 'get'
	    )
	);
	echo input_hidden_tag($input_name);
}
?>
