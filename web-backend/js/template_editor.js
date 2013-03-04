
/**
 *	Template edition
 */

var data = Array();
var defaultCols = 2;
var duplicate;

function genTables()
{
	data.each(function(e, i) {
				appendRow(e);
			 });
	setDnD();
}

function appendRow(cols)
{
	cols 		= (cols == null) ? defaultCols : cols;
	var index 	= data.length;
	var holder 	= $('tables');

	var line 	= new Element('div', { 'id': 'line' + index })
					.inject(holder);
	var table 	= new Element('table', { 'id': 'table' + index, 'class': 'line' })
					.inject(line);
	var tbody	= new Element('tbody')
					.inject(table);
	var tr 		= new Element('tr')
					.inject(tbody);

	var opt 	= new Element('option')
					.setProperty('name', index)
					.setText(index)
					.inject($('row'));
					
	data.push(0);
	for (var j=0; j < cols; j++)
		appendCell(index);
}

function removeRow()
{
	var index = data.length - 1;

	if (index > 0) {
		$('line' + index).remove();
		data.pop();
		$("row").getLast().remove();
	}
}

function removeCell(row)
{
	if (data[row] > 1) {
		var id = 'line' + row;
		data[row]--;
		$(id).getFirst()
			 .getFirst()
			 .getFirst()
			 .getLast()
			 .remove();
		 updateConstraints(row);
	}
}

function appendCell(row)
{
	var id		= 'cell' + row + '-' + data[row];
	var parent	= $('line' + row).getFirst().getFirst().getFirst();
								 
	if (parent.hasChild()) {
		var td	= parent.getLast().clone().inject(parent);
	} else {
		var td	= new Element('td', { 'id': id,
									  'class': 'cell' }).inject(parent);
		var div	= new Element('div')
						.setStyles({ 'display': 'none' })
						.inject(td);
		div		= new Element('div')
						.inject(td)
						.setText(" ");
	}
	
	data[row]++;
	updateConstraints(row);
	setDnD();
}

function setDnD() {
	var targets = $$('.cell');
	var articles = $$('.article');
	
	articles.each(function(element) {
		element.addEvent('mousedown', function(event) {
			event = new Event(event).stop();
			this.setProperty('class', 'dragged');
			var original = this;
			var url = original.getFirst().getText();

			duplicate = new Element('div', { 'class': 'ghost' })
				.setStyles({
					'top': event.client.y - 15,
					'left': event.client.x - 15,
					'width': '250px'
				})
				.addEvent('emptydrop', function() {
					this.remove();
					targets.each(function(target) {
						target.removeEvents();
					});
					original.setProperty('class', 'article');
				})
				.inject($('ghosts'));
			new Ajax(url, {
				method: 'get',
				update: duplicate 
			}).request();
			
			targets.each(function(target) {
				target.addEvents({
					'drop': function() {
						targets.each(function(target) {
							target.removeEvents();
						});
						this.getFirst().setText(url);
						new Ajax(url, {
							method: 'get',
							update: this.getLast()
						}).request();
						$('ghosts').empty();
						this.setStyles({ 'background-color': '#ffffff' });
					},
					'over': function() {
						this.setStyles({ 'background-color': '#ddeeff' });
					},
					'leave': function() {
						this.setStyles({ 'background-color': '#ffffff' });
					}
				});
			});
			
			var drag = duplicate.makeDraggable({
				droppables: targets
			});
			
			drag.start(event);
		});
	});
}

function saveStructure() {
	var form = $('templateForm');
	new Element('input', { 	'id': 'lines',
							'name': 'lines',
							'type': 'hidden',
							'value': data.length }).inject(form);
	var row = 0;
	data.each(function(e, i) {
				var line = new Element('input', { 	'id': 'line' + row,
													'name': 'line' + row,
													'type': 'hidden',
													'value': e });
				var col = 0;
				for (var j = 0; j < e; j++) {
					var url = $('cell' + i + '-' + j).getFirst().getText();
					if (url != '') {
						new Element('input', {	'id': 'cell' + row + '-' + col,
												'name': 'cell' + row + '-' + col,
												'type': 'hidden',
												'value': url }).inject(form);
						col++;
					}
				}
				if (col > 0 ) {
					line.inject(form)
					row++;
				}
	});
}

function getSelectedOption(list) {
	list = $(list);
	var kids = list.getChildren();
	var selected = null;
	kids.each(
		function(e, i) {
			if (e.getProperty("selected"))
				selected = e;
		});
		
	return selected.getText();
}

function updateConstraints(row) {
	var id 		= $('line' + row);
	var width	= (id.getSize()['size']['x'] - 1) / data[row];
	var kids 	= id.getFirst()
					.getFirst()
					.getFirst()
					.getChildren();
	
	kids.each(function(e, i) {
		e.setStyle('width', width);
	});
}

function loadFromHTML() {
	var divs = $('tables').getChildren();
	divs.each(function(e, i) {
		var indx = e.getFirst()
					.getFirst()
					.getFirst()
					.getChildren().length;
		data[i] = indx;
		var opt = new Element('option')
				.setProperty('name', i)
				.setText(i)
				.inject($('row'));
		updateConstraints(i);
	});
	setDnD();
}
