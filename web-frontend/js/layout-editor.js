var Layout = {
  configuration: {
    options: [],
    rows: []
  },

  initialize: function(configuration) {
    if (configuration != undefined)
    {
      this.configuration = configuration;
    }

    return this;
  },

  fromDOM: function(root_selector, row_selector, col_selector, slotlet_selector, slotlet_class_selector, slotlet_index_selector, slotlet_option_selector, column_option_selector, row_option_selector) {
    var my_cnf = {options: [], rows: []};
    var root   = jQuery(root_selector);
    
    jQuery(row_selector, root).each(function(row, e) {
      my_cnf.rows[row] = {options: [], columns: []};

      var opts = {};
      
      jQuery(this).find(row_option_selector).each(function(opt, ele) {
        if (LayoutEditor.isCheckbox(this))
        {
          val = jQuery(this).attr('checked');
        }
        else
        {
          val = jQuery(this).val();
        }

        opts[jQuery(this).attr('name')] = val;
      });

      my_cnf.rows[row]['options'] = opts;

      jQuery(col_selector, jQuery(this)).each(function(col, el) {
        my_cnf.rows[row]['columns'][col] = {
          options: {},
          slotlets: []
        };

        var opts = {};
        
        jQuery(this).find(column_option_selector).each(function(opt, ele) {
          if (LayoutEditor.isCheckbox(this))
          {
            val = jQuery(this).attr('checked');
          }
          else
          {
            val = jQuery(this).val();
          }

          opts[jQuery(this).attr('name')] = val;
        });

        my_cnf.rows[row]['columns'][col]['options'] = opts;
        
        jQuery(this).find(slotlet_selector).each(function(slotlet, ele) {
          var opts = {};

          jQuery(this).find(slotlet_option_selector).each(function(opt, elem) {
            if (LayoutEditor.isCheckbox(this))
            {
              val = jQuery(this).attr('checked');
            }
            else
            {
              val = jQuery(this).val();
            }

            opts[jQuery(this).attr('name')] = val;
          });

          my_cnf.rows[row]['columns'][col]['slotlets'][slotlet] = {
            name: jQuery(this).find(slotlet_class_selector).val(),
            options: opts
          };
          
          jQuery(this).find(slotlet_index_selector).val(slotlet);
        });
      });
    });
    
    return this.initialize(my_cnf);
  },

  toDOM: function() {
    jQuery.each(this.configuration.rows, function(row, e) {
      var dom_row = LayoutEditor.addRow('last', e.options);
      jQuery.each(e.columns, function(col, el) {
        var dom_col = LayoutEditor.addColumn(dom_row, 'last', el.options);
        jQuery.each(el.slotlets, function(index, slotlet) {
          LayoutEditor.addSlotlet(dom_col, slotlet.name, slotlet.options);
        });
      });
    });

    return this;
  },

  addRow: function(content) {
    if (content == undefined)
    {
      content = [];
    }

    this.configuration.rows.push(content);

    return this;
  },

  removeRow: function(row) {
    if (row == undefined || row >= this.configuration.rows.length)
    {
      row = this.sizeOf(this.configuration.rows);
    }
    else if (row < 0)
    {
      row = 0;
    }

    this.configuration.rows.splice(row, 1);

    return this;
  },

  addColumn: function(row, content) {
    if (content == undefined)
    {
      content = [];
    }

    this.getRow(row)['columns'].push(content);

    return this;
  },
  
  removeColumn: function(row, col)
  {
    var whole_row = this.getRow(row)['columns'];

    if (col == undefined || col >= whole_row.length)
    {
      col = this.sizeOf(whole_row);
    }
    else if (col < 0)
    {
      col = 0;
    }
    
    whole_row.splice(col, 1);

    return this;
  },

  setOption: function(option, value) {
    this.configuration.options[option] = value;

    return this;
  },

  getRow: function(row) {
    return this.configuration.rows[row];
  },

  getColumn: function(row, col) {
    return this.getRow(row)['columns'][col];
  },

  addSlotlet: function(row, col, slotlet) {
    var column = this.getColumn(row, col);

    column.push(slotlet);

    return jQuery.inArray(slotlet, column);
  },

  removeSlotlet: function(row, col, index) {
    var slotlets = this.getColumn(row, col);

    if (0 >= index && index < slotlets.length)
    {
      slotlets.splice(index, 1);
    }

    return this;
  },

  getSlotlet: function(row, col, position) {
    var slotlets = this.getColumn(row, col);

    return slotlets[position];
  },

  toString: function() {
    var me = 'Layout:\n';

    if (this.configuration.rows.length == 0)
    {
      me += '\tNo rows have been added yet.\n';

      return me;
    }

    me += '\tRows:\n';
    
    for (row in this.configuration.rows)
    {
      var whole_row = this.getRow(row)['columns'];

      me += '\t\t' + row + ':\n';

      if (whole_row.length == 0)
      {
        me += '\t\t\t0 columns.\n';

        continue;
      }
      
      for (col in whole_row)
      {
        var column = this.getColumn(row, col);

        me += '\t\t\tColumn ' + col + ':\n';

        if (column.length == 0)
        {
          me += '\t\t\t\t0 slotlets.\n';

          continue;
        }
        
        for (index in column)
        {
          me += '\t\t\t\t' + index + ' => ' + this.printSlotlet(row, col, index) + '\n';
        }
      }
    }

    return me;
  },

  printSlotlet: function(row, col, index) {
    return this.dump(this.getSlotlet(row, col, index));
  },

  sizeOf: function(object) {
    var len = this.length ? --this.length : -1;

    for (var k in object)
    {
      len++;
    }
    
    return len;
  },

  dump: function (arr) {
    var dumped_text = '';

    // Array/Hashes/Objects
    if (typeof(arr) == 'object')
    {
      var i = 0;
      dumped_text += '{ ';

      for (var item in arr)
      {
        var value = arr[item];

        if (typeof(value) == 'object')
        {
          dumped_text += item + ': ' + this.dump(value);
        }
        else if (typeof(value) == 'function')
        {
          continue;
        }
        else
        {
          dumped_text += item + ' => "' + value + '"';
        }
        
        if (i < this.sizeOf(arr))
        {
          dumped_text += ', ';
        }

        i++;
      }
      
      dumped_text += ' }';
    }
    else
    {
      dumped_text = arr;
    }
    
    return dumped_text;
  },

  printOn: function(selector) {
    jQuery(selector).html(this.dump(this.configuration));
    
    return this;
  },

  saveOn: function(selector) {
    jQuery(selector).html(JSONstring.make(this.configuration));

    return this;
  },
  
  log: function() {
    console.log(this);

    return this;
  }
};

var LayoutEditor = {
  debug: false,

  unsaved: false,
  
  console: '#layout_configuration_console',

  saveInput: '#layout_configuration',

  initialize: function(configuration) {
    jQuery('#editor a, #editor input:not(.slotlet_option)').live('click', function() {
      return false;
    });

    if (configuration !== undefined)
    {
      this.dump(configuration);
    }
    
    this.register();

    return this;
  },

  register: function() {
    jQuery('#available_slotlets .slotlet-holder').draggable('destroy');
    jQuery('#available_slotlets .slotlet-holder').draggable({
      opacity: 0.8,
      connectToSortable: '#editor .slotlet_container',
      handle: '> *',
      helper: function(event) {
        var clone = jQuery(event.currentTarget).clone(false).prependTo(document.body).css('zIndex', 999999999);
        clone.children('.slotlet').css('display', 'block');

        return clone;
      },
      start: function(event) {
        LayoutEditor.closeSlotletsDialog();
      }
    });

    jQuery('#editor .slotlet_container').sortable('destroy');
    jQuery('#editor .slotlet_container').sortable({
      opacity: 0.8,
      forcePlaceholderSize: true,
      forceHelperSize: true,
      placeholder: 'slotlet_placeholder',
      connectWith: '#editor .slotlet_container, #available_slotlets .slotlet_container',
      handle: '> *',
      revert: true,
      items: '.slotlet-holder',
      update: function(event, ui) {
        LayoutEditor.update();
      },
      stop: function(event, ui) {
        jQuery('#editor .layout_container').each(function(i, e) {
          LayoutEditor.resizeColumns(jQuery(e));
        });
      }
    });
  },
  
  removeSlotlet: function(slotlet_holder) {
    this.resizeColumns(slotlet_holder.closest('.layout_container').first());

    slotlet_holder.remove();

    return this.update();
  },

  addRow: function(position, options) {
    var
      row     = this.createRow(),
      target  = '#editor .choique-layout',
      options = jQuery.extend({}, options);

    if ('first' == position)
    {
      row.prependTo(target);
    }
    else // = Last
    {
      row.appendTo(target);
    }

    jQuery.each(options, function(key, value) {
      var opt = row.find('.row_option[name="' + key + '"]').first();

      if (LayoutEditor.isCheckbox(opt))
      {
        opt.attr('checked', value ? 'checked' : '');
      }
      else
      {
        opt.val(value);
      }
    });

    this.update(true);

    return row;
  },

  createRow: function() {
    return this.insertControlsIntoRow(this.createEmptyDiv('layout_container'));
  },

  deleteRow: function(row) {
    row.remove();

    return this.update(true);
  },

  insertControlsIntoRow: function(row) {
    var container = this.createEmptyDiv('layout-editor-buttons');

    jQuery('<input type="text" class="row_option" name="class" value="" title="Clase CSS de la fila" placeholder="Clase CSS Fila" />').change(function(){ LayoutEditor.update(false); }).appendTo(container);
    this.createButton('Agregar columna al principio', "LayoutEditor.addColumn(jQuery(this).closest('.layout_container'), 'first')", 'add').appendTo(container);
    this.createButton('Agregar columna al final', "LayoutEditor.addColumn(jQuery(this).closest('.layout_container'), 'last')", 'add').appendTo(container);
    this.createButton('Agregar slotlet', "LayoutEditor.popSlotlets(jQuery(this).closest('.layout_container'))", 'add').appendTo(container);
    this.createButton('Eliminar fila', "LayoutEditor.deleteRow(jQuery(this).closest('.layout_container'))", 'cancel', '¿Está seguro que desea eliminar la fila?').appendTo(container);
    container.prependTo(row);

    jQuery('<div class="work-area" />').appendTo(row);
    jQuery('<div style="clear: both;" />').appendTo(row);

    return row;
  },

  addColumn: function(row, position, options) {
    var
      column  = this.createColumn(),
      target  = row.find('.work-area')
      options = jQuery.extend({}, options);

    if ('first' == position)
    {
      column.prependTo(target);
    }
    else // = Last
    {
      column.appendTo(target);
    }

    jQuery.each(options, function(key, value) {
      var opt = column.find('.column_option[name="' + key + '"]').first();
      
      if (LayoutEditor.isCheckbox(opt))
      {
        opt.attr('checked', value ? 'checked' : '');
      }
      else
      {
        opt.val(value);
      }
    });

    this
      .resizeColumns(row)
      .update(true)
    ;

    return column;
  },

  createColumn: function() {
    return this.insertControlsIntoColumn(this.createEmptyDiv('slotlet_container'));
  },

  deleteColumn: function(column) {
    var row = column.closest('.layout_container');
    
    column.remove();

    return this
      .resizeColumns(row)
      .update(true)
    ;
  },

  insertControlsIntoColumn: function(column) {
    var column_id = 'column_' + new Date().getTime();
    var container = this.createEmptyDiv('layout-editor-buttons').css('display', 'none').attr('id', column_id);
    var container_content = jQuery('<div />').appendTo(container);

    jQuery('<div>Ancho: <input type="text" name="width" class="column_option" value="auto" title="Ancho de la columna" placeholder="Ancho" /><select name="measure_unit" class="column_option" title="Unidad de medida del ancho de la columna"><option value="%">%</option><option value="px">px</option></select></div>').appendTo(container_content);
    jQuery('<div>Clase CSS: <input type="text" name="class" class="column_option" value="" title="Clase CSS de la columna" placeholder="Clase CSS" /></div>').appendTo(container_content);
    this.createButton('Guardar cambios', "LayoutEditor.update(false); jQuery('#" + column_id + "').slideUp(500);", 'save').appendTo(container_content);

    container.prependTo(column);

    var actions_container = jQuery('<div class="actions-container" />').prependTo(column);
    jQuery('<a href="#" title="Eliminar columna" class="layout-editor-button cancel" onclick="if (confirm(\'¿Está seguro que desea eliminar la columna?\')) { LayoutEditor.deleteColumn(jQuery(this).closest(\'.slotlet_container\')); } return false;">Eliminar</a>').prependTo(actions_container);
    jQuery('<a href="#" title="Editar propiedades de columna" class="layout-editor-button" onclick="jQuery(\'#' + column_id + '\').slideToggle(500); return false;">Editar columna</a>').prependTo(actions_container);
    
    return column;
  },

  resizeColumns: function(row) {
    var
      columns = row.find('.slotlet_container'),
      count   = columns.length,
      size    = (99.5 / count) - 1,
      height  = 40;

    columns.each(function() {
      $col = jQuery(this);

      height = height <= $col.height() ? $col.height() : height;
    });

    columns.css({
      width: size + '%',
      Float: 'left',
      height: 'auto',
      minHeight: height + 'px'
    });

    return this;
  },

  popSlotlets: function(target) {
    jQuery('#slotlets_toolbar').dialog('open');

    return this;
  },

  closeSlotletsDialog: function() {
    jQuery('#slotlets_toolbar').dialog('close');

    return this;
  },

  addSlotlet: function(column, slotlet, options) {
    var dom_slotlet = this.findSlotlet(slotlet, options).appendTo(column);
    
    this.resizeColumns(column.closest('.layout_container'));

    return dom_slotlet;
  },

  findSlotlet: function(slotlet_class, options) {
    var slotlet = jQuery('#available_slotlets').find('.slotlet_class_attr[value="' + slotlet_class + '"]').closest('.slotlet-holder').clone();

    jQuery.each(options, function(name, value) {
      var field = slotlet.find('.slotlet_option[name="'+name+'"]').first();

      if (LayoutEditor.isCheckbox(field))
      {
        field.attr('checked', value ? 'checked' : '');
      }
      else
      {
        field.val(value);
      }
    });

    return slotlet;
  },

  createEmptyDiv: function(css_class) {
    return jQuery('<div />').addClass(css_class);
  },

  createButton: function(text, action, type, confirm_msg) {
    if (undefined !== confirm_msg && false !== confirm_msg)
    {
      action = 'if (confirm(\'' + confirm_msg + '\')) { ' + action + ' }';
    }
    return jQuery('<a href="#" onclick="' + action + '; return false;" class="layout-editor-button ' + type + '">' + text + '</a>')
  },

  update: function(register) {
    jQuery('#layout-editor-save').attr('disabled', 'disabled').removeClass('save').addClass('cancel');
    
    Layout.fromDOM('#editor .choique-layout', '.layout_container', '.slotlet_container', '.slotlet-holder', '.slotlet_class_attr', '.slotlet_index_attr', '.slotlet_option', '.column_option', '.row_option');

    this.unsaved = true;

    if (true === register)
    {
      this.register();
    }

    Layout.saveOn(this.saveInput);

    if (this.debug)
    {
      Layout.printOn(this.console);
    }

    jQuery('#layout-editor-save').removeAttr('disabled').removeClass('cancel').addClass('save');

    return this;
  },

  dump: function(configuration) {
    Layout.initialize(JSONstring.toObject(configuration));
    Layout.toDOM();
    
    jQuery('#editor .layout-editor-refresh-button').click();

    this.unsaved = false;

    return this;
  },

  isCheckbox: function(element) {
    return jQuery(element).is(':checkbox');
  },

  refreshSlotlet: function(anchor) {
    var slotlet = jQuery(anchor).closest('.slotlet-holder').find('.update_slotlet_area').first();
    var loader  = jQuery(anchor).closest('.slotlet-holder').find('.ajax-loader').first();
    var opts    = {
      section_name: jQuery('#section_name_select').val()
    };

    jQuery(anchor)
      .closest('.slotlet-holder')
        .find('.slotlet_option')
          .each(function(k, v) {
            opts[jQuery(this).attr('name')] = LayoutEditor.isCheckbox(this) ? (jQuery(this).attr('checked') ? 1 : 0) : jQuery(this).val();
          });

    jQuery.ajax({
      url: anchor.href,
      data: {options: opts},
      success: function(data) {
        slotlet.html(data);
      },
      beforeSend: function() {
        loader.height(slotlet.height());
        loader.width(slotlet.width());
        loader.show();
      },
      complete: function() {
        loader.hide();
      }
    });
  }
  
};