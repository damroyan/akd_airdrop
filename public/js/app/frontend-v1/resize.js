define([
    'jquery', 'app/core'
], function($, core) {
    console.log('[app/resize]', 'Init');

    var $body       = $('body'),
        _           = {},
        variable    = {
            windowWidth:    0,
            windowHeight:   0,
            documentHeight: 0
        };

    var obj = {
        getVar: function(key) {
            return key ? variable[key] : variable;
        },

        _resize: function(state) {
            var self =  this;

            variable.documentHeight     = parseInt($(document).height());
            variable.windowWidth        = parseInt($body.width());
            try {
                variable.windowHeight   = parseInt(window.innerHeight ? window.innerHeight : $(window).height())
            }
            catch(e) {
                variable.windowHeight   = parseInt($(window).height());
            }

            if(state) {
                self.update();
            }
            else {
                self._resize(true);
            }
        },

        destroy: function() {
            $(window).off('resize.app.resize');
        },

        update: function() {
            for(var id in _) {
                if(_.hasOwnProperty(id)) {
                    try {
                        _[id]();
                    }
                    catch(e) {
                        console.log('[app/resize]', e);
                    }
                }
            }
        },

        updateById: function(id) {
            if(id && typeof(_[id]) == 'function') {
                _[id]();
            }
        },

        clear: function() {
            //delete _;
            _ = [];
        },

        list: function() {
            return Object.keys(_);
        },

        remove: function(id) {
            if(_[id]) {
                delete _[id];
            }
        },

        add: function(func) {
            var id      = core.random();

            _[id] = func;
            _[id]();

            return id;
        }
    };

    $(window).on('resize.app.resize', function() {
        obj._resize(false);
    }).trigger('resize.app.resize');

    return obj;
});
