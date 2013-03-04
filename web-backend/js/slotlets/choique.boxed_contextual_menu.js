if (Choique == undefined)
{
  Choique = {};
}

var Choique = jQuery.extend(Choique, {
  boxedContextualMenu: {
    contextualize: function(selector, options) {
      var
        root                = jQuery(selector),
        first_level_anchors = root.find('.boxed_item_level_1 > a');

      root.data('boxed.restored', true);
      root.mouseleave(function() {
        Choique.boxedContextualMenu.restore(jQuery(this));
      });

      first_level_anchors
        .data('boxed.root', root)
        .click(this.select);
    },

    select: function() {
      var
        anchor = jQuery(this),
        root   = anchor.data('boxed.root'),
        target = jQuery(anchor.attr('href'));

      root.find('.hidden_menu')
        .fadeOut(150, function() { target.fadeIn(150); });
      root.data('boxed.restored', false);
      
      return false;
    },

    restore: function(root) {
      if (root.data('boxed.restored'))
      {
        return false;
      }

      root.find('.boxed_contextual_menu_children_container')
        .fadeOut(150, function() { root.find('.hidden_menu').fadeIn(150); });
      root.data('boxed.restored', true);
    }
  }
});