function spin_increase(id, amount, max) {
	var elmnt = $(id);
	var current_value = elmnt.getValue();
	
	if (current_value.strip() == '')
		current_value = 0;
	current_value = parseInt(current_value) + amount;
	
	if (current_value <= max) {
		elmnt.writeAttribute('value', current_value);
	}
}

function spin_decrease(id, amount, min) {
	var elmnt = $(id);
	var current_value = elmnt.getValue();
	
	if (current_value.strip() == '')
		current_value = 0;
	current_value = parseInt(current_value) - amount;
	
	if (current_value >= min) {
		elmnt.writeAttribute('value', current_value);
	}
}

function convert_input_to_spin(id, readonly, up_img, dn_img, step, min, max) {
	//Provisory IE bug solution: don't do anything!!
	if (navigator.appName.include("icrosoft"))
		return;
		
	if (readonly)
		$(id).writeAttribute('readonly', 'readonly');

	var img2 = new Element('img', { 'alt': '+', 'src': up_img, 
								   	'style': 'cursor: pointer; vertical-align: middle;',
								   	'onclick': 'spin_increase("' + id + '", ' + step + ', ' + max + ');' });
	var img1 = new Element('img', { 'alt': '-', 
							   		'src': dn_img,
							   		'style': 'cursor: pointer; vertical-align: middle;', 
							   		'onclick': 'spin_decrease("' + id + '", ' + step + ', ' + min + ');' });
	Element.insert($(id), { 'after': img1 });
	Element.insert($(id), { 'after': img2 });
}

function slide_right(id) {
	var field    = $('slider_' + id);
	var original = $(id);
	var selected = $(id + '_selected');
	var value    = parseInt(selected.getValue()) + 1;
	
	if (original.down(value)) {
		original.selectedIndex = value;
		selected.writeAttribute('value', value);
		field.update(original.down(value).innerHTML);
	}
}

function slide_left(id) {
	var field    = $('slider_' + id);
	var original = $(id);
	var selected = $(id + '_selected');
	var value    = parseInt(selected.getValue()) - 1;
	
	if (original.down(value)) {
		original.selectedIndex = value;
		selected.writeAttribute('value', value);
		field.update(original.down(value).innerHTML);
	}
}

function convert_select_to_slider(id, left_img, right_img) {
	//Provisory IE bug solution: don't do anything!!
	if (navigator.appName.include("icrosoft"))
		return;
		
	//Provisory IE bug solution: don't do anything!!
	if (navigator.appName.include("icrosoft"))
		return;
		
	var original = $(id);
	original.hide();
	
	var l_img  = new Element('img',   { 'src': left_img,
									    'alt': '<',
									    'onclick': 'slide_left(\"' + id + '\");',
									    'style': 'cursor: pointer; vertical-align: middle; float: left;' });
	var r_img  = new Element('img',   { 'src': right_img,
									    'alt': '>',
									    'onclick': 'slide_right(\"' + id + '\");',
									    'style': 'cursor: pointer; vertical-align: middle; float: right;' });
	var field  = new Element('span',  { 'id': 'slider_' + id,
									    'style': 'text-align: center; margin-left: 5px;' });
	var hidden = new Element('input', { 'type': 'hidden',
										'id': id + '_selected',
										'value': 0 });
	var div    = new Element('div',   { 'style': 'width: 24em; padding: 3px; text-align: center;' });
										
	var value = (typeof(parseInt(original.getValue())) == 'number') ? parseInt(original.getValue()) : 'NaN';
	var index = 0;
	
	if (value != 'NaN') {
		original.descendants().each(function(e,i) {
			if (Element.readAttribute(e, 'value') == value) {
				index = i;
			}
		});
	}
	
	hidden.writeAttribute('value', index);
	
	field.update(original.down(index).innerHTML);
	
	Element.insert(original, {'before': div });
	Element.insert(div, field);
	Element.insert(original.up('form'), hidden);
	Element.insert(field, { 'before': l_img });
	Element.insert(field, { 'before': r_img });
}
