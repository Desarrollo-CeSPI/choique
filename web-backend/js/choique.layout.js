if (Choique == undefined)
{
  Choique = {};
}

var Choique = jQuery.extend(Choique, {
  layout: {
    make: function(selectors, options) {
      var settings = jQuery.extend({
        opacity: 0.8,
        forcePlaceholderSize: true,
        forceHelperSize: true,
        placeholder: 'slotlet_placeholder',
        connectWith: '.slotlet_container',
        handle: '> *',
        revert: true,
        items: '.slotlet',
        cursor: 'move',
        update: function() {
          Choique.layout.set(selectors);
        }
      }, options);

      jQuery.each(selectors.split(','), function() {
        jQuery(this.toString()).sortable(settings);

        jQuery(settings.handle, this.toString()).css('cursor', 'move');
      });

      return this;
    },

    destroy: function(selectors) {
      jQuery.each(selectors.split(','), function() {
        jQuery(this.toString()).sortable('destroy');
      });
               
    },

    get: function() {
      if (jQuery.cookie('choique.layout') == null)
      {
        return [[]];
      }

      return this.unserialize(jQuery.cookie('choique.layout'));
    },
    
    set: function(selectors) {
      var groups = [];

      jQuery.each(selectors.split(','), function() {
        groups.push(jQuery(this.toString()).sortable('toArray'));
      });

      jQuery.cookie('choique.layout', this.serialize(groups));

      return this;
    },

    unserialize: function(serialized_layout) {
      var unserialized_layout = [];

      jQuery.each(serialized_layout.split('|'), function() {
        var group    = [];

        jQuery.each(this.split(','), function() {
          group.push(this.toString());
        });

        unserialized_layout.push(group);
      });

      return unserialized_layout;
    },

    serialize: function(unserialized_layout)
    {
      if (!jQuery.isArray(unserialized_layout) || unserialized_layout.length == 0)
      {
        return '';
      }

      var serialized_layout = [];
      var index             = 0;

      jQuery.each(unserialized_layout, function() {
        serialized_layout.push(this.join(','));
      });

      return serialized_layout.join('|');
    },

    rearrange: function(selectors) {
      var groups = jQuery(selectors);

      jQuery.each(this.get(), function(i, e) {
        var group = groups[i];

        jQuery.each(this, function() { 
          jQuery('#' + this).appendTo(group);
        });
      });

      return this;
    }
  }
});
