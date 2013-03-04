if (Choique == undefined)
{
  Choique = {};
}

var Choique = jQuery.extend(Choique, {
  contextualMenu: {
    open: '',
    
    settings: {
      slideUpArrow: false,
      slideDownArrow: false
    },

    toggle: function(selector, element, section) {
      var parent = element.closest('.first-level');

      if (this.open == section)
      {
        this.open = '';
        parent.removeClass('current-section');
        
        if (false !== this.settings.slideUpArrow)
        {
          element.children('img').attr('src', this.settings.slideUpArrow).attr('alt', '+');
        }
      }
      else
      {
        if (false !== this.settings.slideUpArrow)
        {
          jQuery(selector + ' .current-section .arrow img').attr('src', this.settings.slideUpArrow).attr('alt', '+');
        }

        this.open = section;
        jQuery(selector + ' .current-section').removeClass('current-section');
        parent.addClass('current-section');
        jQuery(selector + ' .first-level:not(.current-section) .second-level.collapseable').slideUp(500);
        
        if (false !== this.settings.slideDownArrow)
        {
          element.children('img').attr('src', this.settings.slideDownArrow).attr('alt', '-');
        }
      }

      jQuery(section + '-children').slideToggle(500);

    },

    contextualize: function(selector, options) {
      this.settings = jQuery.extend(this.settings, options);

      var root = jQuery(selector);

      root.find('.second-level.collapseable').hide();
      
      root.find('.arrow').click(function () {
        var me = jQuery(this), section = me.attr('href');

        Choique.contextualMenu.toggle(selector, me, section);

        return false;
      });
    }
  }
});