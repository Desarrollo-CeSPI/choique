if (Choique == undefined)
{
  Choique = {};
}

Choique.side_multimedia_gallery = {
  create: function(selector) {
    var root = jQuery(selector);

    this.adjustSize(root);

    this.registerListItems(root);

    return this;
  },

  adjustSize: function(root) {
    var height = root.height();
    
    root.find('.cqs_preview').each(function(i, e) {
      if (height < (h = jQuery(this).height()))
      {
        height = h;
      }
    });

    if (height != root.height())
    {
      height += 16;
    }

// TODO: Y esto? No anda bien
//    root.height(height + root.find('.cqs_title').height());

    return this;
  },

  registerListItems: function(root) {
    root.find('.cqs_list_item').click(function() {
      var $this = jQuery(this);

      Choique.side_multimedia_gallery.change($this.closest('.cqs_gallery'), $this);

      return false;
    });

    this.selected(root).click();
    
    return this;
  },

  selected: function(root) {
    return root.find('.cqs_selected_item');
  },

  description: function(root) {
    return root.find('.cqs_description');
  },

  change: function(root, anchor) {
    var target = jQuery(anchor.attr('href'), root);

    root.find('.cqs_preview').hide();
    target.show();
    this.updateDescription(root, anchor.attr('title'));

    if (!anchor.hasClass('cqs_selected_item'))
    {
      root.find('.cqs_selected_item').removeClass('cqs_selected_item');
      anchor.addClass('cqs_selected_item');
    }
    
    return this;
  },

  updateDescription: function(root, description) {
    this.description(root).html(description);

    return this;
  }
  
};
