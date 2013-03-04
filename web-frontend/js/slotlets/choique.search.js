if (Choique == undefined)
{
  Choique = {};
}

var Choique = jQuery.extend(Choique, {
  placeholder: function(selector, placeholder) {
    // Test for HTML5 placeholder support
    if (this.supportsPlaceholder())
    {
      // Supported, let's use it!
      jQuery(selector).val('').attr('placeholder', placeholder);
    }
    else
    {
      // Not supported, let's use JS
      jQuery(selector)
        .focus(function() {
          if (jQuery(this).val() == placeholder)
          {
            jQuery(this).val('');
          }
        }).blur(function() {
          if (jQuery(this).val() == '')
          {
            jQuery(this).val(placeholder);
          }
        })
      ;
    }
  },

  supportsPlaceholder: function() {
    var input = document.createElement('input');
    
    return 'placeholder' in input;
  }
});
