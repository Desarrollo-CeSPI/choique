var EventsCalendar = {
  current:    {},
  old:        {},
  make_visible: {},

  toggle: function (key, events_id) {
    if (this.hasCurrent(key) && this.exists(events_id))
    {
      this.get(this.getCurrent(key))
            .toggleClass('visible')
            .toggleClass('hidden');

      if (this.isCurrent(key, events_id))
      {
        this.setOld(key, '');
        this.setMakeVisible(key, false);
      }

      this.setCurrent(key, '');
    }

    if (!this.isOld(key, events_id) && this.getMakeVisible(key))
    {
      this.setCurrent(key, events_id);
  
      this.get(this.getCurrent(key))
            .toggleClass('visible')
            .toggleClass('hidden');
    }
  
    this.setMakeVisible(key, true);
  },

  toggleAll: function (key, events_class, anchor) {
    this.getAll(events_class).toggle();

    jQuery(anchor).html(jQuery(anchor).html() == 'Ocultar' ? 'Ver todos' : 'Ocultar');
  },

  get: function (dom_id) {
    return jQuery('#'+dom_id);
  },

  getAll: function (dom_id) {
    return jQuery('.'+dom_id);
  },

  hasCurrent: function (key) {
    return this.getCurrent(key) != '';
  },

  exists: function (dom_id) {
    return this.get(dom_id).size() > 0;
  },

  isCurrent: function (key, value) {
    return this.getCurrent(key) == value;
  },

  isOld: function (key, value) {
    return this.getOld(key) == value;
  },

  setCurrent: function (key, value) {
    this.current[key] = value;

    return this;
  },

  getCurrent: function (key) {
    if (undefined == this.current[key])
    {
      this.setCurrent(key, '');
    }

    return this.current[key];
  },

  setOld: function (key, value) {
    this.old[key] = value;

    return this;
  },

  getOld: function (key) {
    if (undefined == this.old[key])
    {
      this.setOld(key, '');
    }

    return this.old[key];
  },

  setMakeVisible: function (key, value) {
    this.make_visible[key] = value;

    return this;
  },

  getMakeVisible: function (key) {
    if (undefined == this.make_visible[key])
    {
      this.setMakeVisible(key, true);
    }

    return this.make_visible[key];
  }
};