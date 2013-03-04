var Layout = {};

jQuery(function() {
  Layout = {
    root: jQuery('#layout_container > tbody'),

    highlight: function(element) {
      jQuery(element).effect('highlight', {
        color: '#ffe77d'
      }, 1000);
    },

    checkChanges: function() {
      var answer = true;

      if (this.initial != this.encode(this.fromDOM()))
      {
        answer = confirm('Se han realizado cambios en la distribución que se perderán si continúa.\n¿Está seguro que desea descartar los cambios realizados?');
      }

      return answer;
    },

    read: function(curtainize) {
      curtainize = undefined == curtainize ? true : curtainize;

      if (curtainize)
      {
        jQuery('#curtain').show();
      }

      jQuery('#configuration_holder').val(this.encode(this.fromDOM()));

      if (curtainize)
      {
        jQuery('#curtain').hide();
      }
    },

    encode: function(object) {
      return JSONstring.make(object);
    },

    fromDOM: function() {
      var my_cnf = {
        options: [],
        rows: []
      };

      var column_option_selector = '.column_option';

      Layout.row.all().each(function(row, e) {
        var $row = jQuery(this);

        my_cnf.rows[row] = {
          options: [],
          columns: []
        };

        var opts = {};

        $row.find('.row_option').each(function() {
          var $this = jQuery(this);

          opts[$this.attr('name')] = $this.is(':checkbox') ? $this.attr('checked') : $this.val();
        });

        my_cnf.rows[row]['options'] = opts;

        $row.find('.column').each(function(col, el) {
          var $column = jQuery(this);

          my_cnf.rows[row]['columns'][col] = {
            options: {},
            slotlets: []
          };

          var opts = {};

          $column.find('.column_option').each(function() {
            var $this = jQuery(this);

            opts[$this.attr('name')] = $this.is(':checkbox') ? $this.attr('checked') : $this.val();
          });

          my_cnf.rows[row]['columns'][col]['options'] = opts;

          $column.find('.slotlet').each(function(slotlet, ele) {
            var $slotlet = jQuery(this);
            var opts = {};

            $slotlet.find('.slotlet_option').each(function() {
              var $this = jQuery(this);

              opts[$this.attr('name')] = $this.is(':checkbox') ? $this.attr('checked') : $this.val();
            });

            my_cnf.rows[row]['columns'][col]['slotlets'][slotlet] = {
              name: $slotlet.find('.slotlet_class').val(),
              options: opts
            };

            //$slotlet.find(slotlet_index_selector).val(slotlet);
          });
        });
      });

      return my_cnf;
    }
  }

  Layout.row = {
    mock: jQuery('.mock .row'),

    all: function() {
      return Layout.root.find('.row');
    },

    add: function(position) {
      var root = Layout.root;
      var row = this.mock.clone();

      if (position == 'first')
      {
        row.prependTo(root);
      }
      else
      {
        row.appendTo(root);
      }

      Layout.highlight(row.find('.column'));
    },

    swap: function(pivot, direction) {
      var row = this.find(pivot);

      switch (direction)
      {
        case 'up':
          row.insertBefore(row.prevAll('.row:first'))
          break;
        case 'down':
          row.insertAfter(row.nextAll('.row:first'))
          break;
        case 'top':
          row.insertBefore(row.prevAll('.row:last'));
          break;
        case 'bottom':
          row.insertAfter(row.nextAll('.row:last'))
          break;
      }

      Layout.highlight(row.find('.column'));
    },

    edit: function(pivot) {
      this.find(pivot).find('.row_controls .form').toggle();
    },

    stopEdition: function(pivot) {
      jQuery(pivot).closest('.form').hide();
    },

    remove: function(pivot) {
      var row = this.find(pivot);

      row.addClass('highlighted');

      if (!confirm('¿Está seguro que desea eliminar esta fila?'))
      {
        row.removeClass('highlighted');

        return;
      }

      row.fadeOut(500, function() {
        jQuery(this).remove();
      });
    },

    find: function(pivot) {
      return jQuery(pivot).closest('.row');
    }
  };

  Layout.column = {
    mock: jQuery('.mock .column'),
    
    current: null,

    colspan: 0,

    add: function(pivot, position) {
      var row = Layout.row.find(pivot);
      var column = this.mock.clone();

      if (position == 'first')
      {
        column.insertBefore(row.find('.column:first'));
      }
      else
      {
        column.insertAfter(row.find('.column:last'));
      }

      this.makeDroppable(column);

      Layout.highlight(column);
    },

    swap: function(pivot, direction) {
      var column = this.find(pivot);

      switch (direction)
      {
        case 'left':
          column.insertBefore(column.prev('.column'))
          break;
        case 'right':
          column.insertAfter(column.next('.column'))
          break;
        case 'first':
          column.insertBefore(column.prevAll('.column:last'));
          break;
        case 'last':
          column.insertAfter(column.nextAll('.column:last'));
          break;
      }

      Layout.highlight(column);
    },

    edit: function(pivot) {
      this.find(pivot).find('.column_controls .form').toggle();
    },

    stopEdition: function(pivot) {
      jQuery(pivot).closest('.form').hide();
    },

    makeDroppable: function(column) {
      column.droppable({
        addClasses: false,
        hoverClass: 'ui-state-hover',
        accept: '.slotlet',
        tolerance: 'pointer',
        drop: function(event, ui) {
          var neighbor = Layout.column.find(ui.draggable);

          ui.draggable.appendTo(this);

          jQuery(this).removeClass('empty');

          if (neighbor.find('.slotlet').length == 1)
          {
            neighbor.addClass('empty');
          }
        }
      });
    },

    remove: function(pivot) {
      var column = this.find(pivot);

      if (Layout.row.find(pivot).find('.column').length == 1)
      {
        alert('No se puede eliminar la última columna de la fila.\nIntente eliminar la fila completa.');

        return false;
      }

      column.addClass('highlighted');

      if (!confirm('¿Está seguro que desea eliminar esta columna?'))
      {
        column.removeClass('highlighted');

        return;
      }

      column.hide(500, function() {
        jQuery(this).remove();
      });

      this.updateColspan();
    },

    addSlotlet: function(pivot) {
      Layout.column.current = this.find(pivot);
      
      jQuery('#available_slotlets_section').dialog({
        width: '50%',
        modal: true
      });
    },

    find: function(pivot) {
      return jQuery(pivot).closest('.column');
    }
  };

  Layout.slotlet = {
    mock: jQuery('.mock .slotlet'),

    add: function(pivot) {
      var column = Layout.column.current;
      var slotlet = this.findAvailable(pivot);

      column.append(this.create(slotlet));

      jQuery('#available_slotlets_section').dialog('close');
      Layout.column.current = null;

      column.removeClass('empty');

      this.makeDraggable(slotlet);

      Layout.highlight(slotlet);
    },

    edit: function(pivot) {
      var slotlet = this.find(pivot);

      slotlet.find('tbody').toggle();
    },

    find: function(pivot) {
      return jQuery(pivot).closest('.slotlet');
    },

    findAvailable: function(pivot) {
      return jQuery(pivot).closest('.available_slotlet');
    },

    create: function(slotlet) {
      var mock = this.mock.clone();
      var slotlet_class = slotlet.find('.class').html();

      mock.find('.slotlet_name').html(slotlet.find('.name').html());
      mock.find('.slotlet_class').val(slotlet_class);

      jQuery.get(Layout.url, {
        'class': slotlet_class
      }, function(data) {
        mock.find('.form > .form_content').html(data);
      });

      return mock;
    },

    makeDraggable: function(slotlet) {
      slotlet.draggable({
        opacity: 0.8,
        handle: '.name',
        revert: 'invalid',
        zIndex: 30,
        addClasses: false,
        cursor: 'move',
        cursorAt: {left: 50, top: 10},
        helper: function(event) {
          return jQuery(event.currentTarget).clone().width(300);
        }
      });
    },

    remove: function(pivot) {
      var slotlet = this.find(pivot);
      slotlet.addClass('highlighted');

      if (!confirm('¿Está seguro que desea eliminar este slotlet?'))
      {
        slotlet.removeClass('highlighted');

        return;
      }

      var column = Layout.column.find(pivot);

      slotlet.slideUp(500, function() {
        jQuery(this).remove();

        if (column.find('.slotlet').length == 0)
        {
          column.addClass('empty');
        }
      });
    },

    swap: function(pivot, direction) {
      var slotlet = this.find(pivot);

      switch (direction)
      {
        case 'up':
          slotlet.insertBefore(slotlet.prev('.slotlet'))
          break;
        case 'down':
          slotlet.insertAfter(slotlet.next('.slotlet'))
          break;
        case 'top':
          slotlet.insertBefore(slotlet.prevAll('.slotlet:last'));
          break;
        case 'bottom':
          slotlet.insertAfter(slotlet.nextAll('.slotlet:last'));
          break;
      }

      Layout.highlight(slotlet);
    }
  };

  Layout.read(false);

  Layout.initial = jQuery('#configuration_holder').val();
});