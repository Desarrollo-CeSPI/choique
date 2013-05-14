if (typeof(Choique) === 'undefined')
{
  Choique = {};
}

Choique.ordered_set = {
  current: null,
  
  elements: [],

  create: function() {
    this.elements = [];
    this.current  = null;

    return this;
  },

  includes: function(element) {
    return (-1 != jQuery.inArray(element, this.elements));
  },

  setAll: function(elements) {
    this.elements = jQuery.isArray(elements) ? elements : jQuery.makeArray(elements);
    this.reset();

    return this;
  },

  push: function(element) {
    if (!this.includes(element))
    {
      this.elements.push(element);
    }

    return this;
  },

  shift: function() {
    if (!this.empty())
    {
      return this.elements.shift();
    }

    return null;
  },

  pop: function() {
    if (!this.empty())
    {
      return this.elements.pop();
    }

    return null;
  },

  empty: function() {
    return (this.size() == 0);
  },

  atEnd: function() {
    return (this.current == this.size() - 1)
  },

  atBeginning: function() {
    return (this.current == 0);
  },
  
  size: function() {
    return this.elements.length;
  },

  reset: function() {
    this.current = 0;
    
    return this;
  },

  next: function() {
    if (this.atEnd())
    {
      this.reset();
    }
    else
    {
      this.current++;
    }

    return this.get();
  },

  previous: function() {
    if (this.atBeginning())
    {
      this.current = this.size() - 1;
    }
    else
    {
      this.current--;
    }

    return this.get();
  },
  
  get: function() {
    if (this.current !== null)
    {
      return this.elements[this.current];
    }
    
    return null;
  }
};

Choique.multimedia_gallery = {
  options: {
    effectDelay:          5000,      // Interval in milliseconds between cycles
    effectDuration:       500,       // Duration of the cycling effect
    autoStart:            false,     // Boolean value indicating if cycling should start on user interaction and/or init
    initialTabIndex:      0,         // Zero-based index for the tab to display at init
    indicatorSrc:         null       // Source url of the indicator image
  },

  set: Choique.ordered_set.create(),
  
  last: null,

  interval_id: null,

  overlay_div: null,

  create: function(selector, options) {
    this.options = jQuery.extend(
      this.options,
      options,
      { selector: selector }
    );

    this.overlay(false);
    this.registerTabs();
    this.showInitialTab();

    return this;
  },

  root: function() {
    return jQuery(this.options.selector);
  },

  selectedTab: function() {
    return jQuery('a.cq_selected_tab', this.root());
  },

  registerTabs: function() {
    var cmg = this;

    this.root().find('.cq_tabs a').click(function() {
      var tab = jQuery(this);

      if (!tab.hasClass('cq_selected_tab'))
      {
        cmg.showTab(tab);
      }

      return false;
    });

    return this;
  },

  showInitialTab: function() {
    if (false !== this.options.initialTabIndex)
    {
      this.showTab(this.root().find('.cq_tabs a:eq(' + this.options.initialTabIndex + ')'));
    }

    return this;
  },

  workingSet: function(elements) {
    this.set.setAll(elements);

    if (this.set.size() < 2)
    {
      this.previousButton().hide();
      this.nextButton().hide();
      this.playButton().hide();
      this.pauseButton().hide();
    }
    else
    {
      this.previousButton().show();
      this.nextButton().show();
      this.playButton().show();
      this.pauseButton().show();

      if (!this.previousButton().attr('cqpositioned'))
      {
        // WebKit-based browsers hack: they position the arrow wrong
        if (jQuery.browser.webkit)
        {
          var offset = '39 15';
        }
        else
        {
          var offset = '0 0';
        }

        this.previousButton().position({ my: 'left center', at: 'left center', of: this.options.selector, offset: offset }).attr('cqpositioned', '1');
      }

      if (!this.nextButton().attr('cqpositioned'))
      {
        // WebKit-based browsers hack: they position the arrow wrong
        if (jQuery.browser.webkit)
        {
          var offset = '7 15';
        }
        else
        {
          var offset = '0 0';
        }

        this.nextButton().position({ my: 'right center', at: 'right center', of: this.options.selector, offset: offset }).attr('cqpositioned', '1');
      }
    }

    return this;
  },

  previousButton: function() {
    return this.root().find('.cq_control_previous');
  },

  nextButton: function() {
    return this.root().find('.cq_control_next');
  },

  playButton: function() {
    return this.root().find('.cq_control_play');
  },

  pauseButton: function() {
    return this.root().find('.cq_control_pause');
  },

  elements: function() {
    return this.targetOf(this.selectedTab()).find('.cq_content .cq_gallery_item');
  },

  current: function() {
    return jQuery(this.set.get());
  },

  next: function() {
    this.last = this.current();
    
    return this.set.next();
  },

  previous: function() {
    this.last = this.current();

    return this.set.previous();
  },

  stop: function(leave_interval) {
    leave_interval = leave_interval || false;

    if (null !== this.current())
    {
      this.current().stop(true);
    }

    if (!leave_interval && this.interval_id || this.set.size() < 2)
    {
      clearInterval(this.interval_id);

      this.interval_id = null;

      this.indicator().hide();
    }

    return this;
  },

  show: function() {
    if (null !== this.last)
    {
      this.hideElement(this.last);

      this.last = null;
    }

    var cmg = this;

    this.current()
      .css('z-index','auto')
      .delay(this.options.effectDuration)
      .fadeTo(this.options.effectDuration, 1, function() {
        cmg.centerElement(cmg.current());
      })
    ;
    
    this.updateDescription();
    return this;
  },

  hideElement: function(element) {
    if (undefined === element)
    {
      element = this.current();
    }

    element.fadeTo(this.options.effectDuration, 0, function(){ 
      element.css('z-index',-1);
    });
    return this;
  },

  hide: function() {
    var cmg = this;

    jQuery('.cq_tabs a', this.root()).each(function() {
      var tab = jQuery(this).removeClass('cq_selected_tab');
      
      cmg.targetOf(tab).hide();
    });

    return this;
  },

  go: function(direction, leave_interval) {
    this.stop(leave_interval);


    if (direction == -1)
    {
      this.previous();
    }
    else if (direction == 1)
    {
      this.next();
    }

    if (!this.current().attr('cqpositioned'))
    {
      this.overlay();
    }

    this.show();

    return this;
  },

  targetOf: function(tab) {
    return jQuery(tab.attr('href'));
  },

  updateDescription: function() {
    var description = '<div class="cq_description_title">' + this.current().attr('title') + '</div><div class="cq_description_detail">' + this.current().attr('longdesc') + '</div>'

    this.root().find('.cq_description').html(description);

    return this;
  },

  select: function(tab) {
    this.targetOf(tab).show();

    tab.addClass('cq_selected_tab');

    return this;
  },

  centerElement: function(element) {
    if (!element.attr('cqpositioned'))
    {
      this.overlay();

      element
        .position({
          my: 'center',
          at: 'center',
          of: this.selectedTab().attr('href')
        })
        .attr('cqpositioned', '1')
      ;
    }

    return this;
  },

  overlay: function(timeout) {
    this.root().mask('Cargando...');

    if (false !== timeout)
    {
      setTimeout('Choique.multimedia_gallery.unoverlay();', 1000);
    }
  },

  unoverlay: function() {
    this.root().unmask();
  },

  showTab: function(tab) {
    this
      .stop()
      .hide()
      .select(tab)
      .workingSet(this.elements().hide())
      .show();

    if (this.options.autoStart)
    {
      this.play();
    }

    return this;
  },

  play: function() {
    if (this.set.size() >= 2)
    {
      this.options.autoStart = true;

      if (null === this.interval_id)
      {
        this.interval_id = setInterval('Choique.multimedia_gallery.cycle()', this.options.effectDelay);
      }

      this.indicator().show();
    }

    return this;
  },

  pause: function() {
    this.stop();

    this.options.autoStart = false;

    return this;
  },

  cycle: function() {
    this.go(1, true);

    return this;
  },

  indicator: function() {
    return this.root().find('.cq_playback_indicator');
  }
};
