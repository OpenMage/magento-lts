/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Lightweight vanilla autocomplete (replaces Scriptaculous Ajax.Autocompleter).
 * Works in all prototype_mode values (full/shim/none) — uses fetch, no Prototype.
 * The server is expected to return an HTML <ul> with <li> entries.
 *
 * Self-contained: load this file wherever a search/autocomplete field exists.
 * Detach it (and swap in another implementation) by removing it from layout —
 * the only consumers are Varien.searchForm.initAutocomplete (storefront search)
 * and the admin global search (page/header.phtml).
 */
if (!window.Varien) {
    window.Varien = {};
}

/**
 * @constructor
 * @param {string|HTMLElement} element - the input field (id or node)
 * @param {string|HTMLElement} update  - the results container (id or node)
 * @param {string} url
 * @param {object} options - paramName, minChars, method, frequency, indicator,
 *                           updateElement, afterUpdateElement, onShow, onHide, parameters
 */
Varien.Autocomplete = function(element, update, url, options) {
    this.element = typeof element === 'string' ? document.getElementById(element) : element;
    this.update  = typeof update  === 'string' ? document.getElementById(update)  : update;
    this.url = url;
    this.options = Object.assign({
        paramName: 'query',
        minChars: 1,
        method: 'get',
        frequency: 0.4,
        indicator: null,
        updateElement: null,
        afterUpdateElement: null,
        onShow: null,
        onHide: null,
        parameters: {}
    }, options || {});

    this.active = false;
    this.hasFocus = false;
    this.index = -1;
    this.entryCount = 0;
    this.observer = null;

    if (!this.element || !this.update) {
        return;
    }

    this.oldValue = this.element.value;
    this.update.style.display = 'none';
    this.element.setAttribute('autocomplete', 'off');

    var self = this;
    this.element.addEventListener('keydown', function(e) { self.onKeyDown(e); });
    this.element.addEventListener('focus', function() { self.onFocus(); });
    this.element.addEventListener('blur', function() { self.onBlur(); });
};

Varien.Autocomplete.prototype = {
    onKeyDown: function(evt) {
        switch (evt.keyCode) {
            case 38: // up
                evt.preventDefault();
                this.markPrevious();
                return;
            case 40: // down
                evt.preventDefault();
                this.markNext();
                return;
            case 13: // return
                if (this.active && this.index >= 0) {
                    evt.preventDefault();
                    this.selectEntry();
                }
                return;
            case 27: // esc
                this.hide();
                this.active = false;
                return;
            case 9: // tab
                if (this.active && this.index >= 0) {
                    this.selectEntry();
                }
                return;
        }
    },

    onFocus: function() {
        this.hasFocus = true;
        this.startObserving();
    },

    onBlur: function() {
        var self = this;
        this.hasFocus = false;
        // Delay so a click on a dropdown entry can register first
        setTimeout(function() {
            if (!self.hasFocus) {
                self.hide();
                self.stopObserving();
            }
        }, 250);
    },

    startObserving: function() {
        if (this.observer) {
            return;
        }
        var self = this;
        this.observer = setInterval(function() { self.onObserverEvent(); }, this.options.frequency * 1000);
    },

    stopObserving: function() {
        if (this.observer) {
            clearInterval(this.observer);
            this.observer = null;
        }
    },

    onObserverEvent: function() {
        var val = this.element.value;
        if (val === this.oldValue) {
            return;
        }
        this.oldValue = val;
        if (val.length >= this.options.minChars) {
            this.getUpdatedChoices();
        } else {
            this.hide();
            this.active = false;
        }
    },

    getUpdatedChoices: function() {
        var params = {};
        params[this.options.paramName] = this.element.value;
        var extra = this.options.parameters || {};
        Object.keys(extra).forEach(function(k) { params[k] = extra[k]; });

        var indicator = this.options.indicator
            ? (typeof this.options.indicator === 'string' ? document.getElementById(this.options.indicator) : this.options.indicator)
            : null;
        if (indicator) indicator.style.display = '';

        var self = this;
        var method = (this.options.method || 'get').toUpperCase();
        var fetchUrl = this.url;
        var fetchOpts = { method: method, headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        var qs = new URLSearchParams(params).toString();
        if (method === 'GET') {
            fetchUrl += (fetchUrl.indexOf('?') === -1 ? '?' : '&') + qs;
        } else {
            fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            fetchOpts.body = qs;
        }

        fetch(fetchUrl, fetchOpts)
            .then(function(resp) { return resp.text(); })
            .then(function(text) {
                if (indicator) indicator.style.display = 'none';
                self.updateChoices(text);
            })
            .catch(function() {
                if (indicator) indicator.style.display = 'none';
            });
    },

    updateChoices: function(html) {
        this.update.innerHTML = html;
        var entries = this.update.querySelectorAll('li');
        this.entryCount = entries.length;
        this.index = -1;

        var self = this;
        for (var i = 0; i < entries.length; i++) {
            (function(idx) {
                entries[idx].addEventListener('click', function(e) {
                    e.preventDefault();
                    self.index = idx;
                    self.selectEntry();
                });
                entries[idx].addEventListener('mouseover', function() { self.setActive(idx); });
            })(i);
        }

        if (this.entryCount > 0) {
            this.show();
            this.active = true;
        } else {
            this.hide();
            this.active = false;
        }
    },

    show: function() {
        if (typeof this.options.onShow === 'function') {
            this.options.onShow(this.element, this.update);
        } else {
            this.update.style.display = '';
        }
    },

    hide: function() {
        // Note: do not stop the polling observer here — it must keep running while
        // the field has focus so that typing past minChars is detected. The observer
        // is stopped on blur (see onBlur).
        if (typeof this.options.onHide === 'function') {
            this.options.onHide(this.element, this.update);
        } else {
            this.update.style.display = 'none';
        }
    },

    setActive: function(idx) {
        var entries = this.update.querySelectorAll('li');
        for (var i = 0; i < entries.length; i++) {
            entries[i].className = (i === idx) ? 'selected' : '';
        }
        this.index = idx;
    },

    markPrevious: function() {
        if (this.index > 0) {
            this.setActive(this.index - 1);
        } else {
            this.setActive(this.entryCount - 1);
        }
    },

    markNext: function() {
        if (this.index < this.entryCount - 1) {
            this.setActive(this.index + 1);
        } else {
            this.setActive(0);
        }
    },

    selectEntry: function() {
        this.active = false;
        var entries = this.update.querySelectorAll('li');
        var selected = entries[this.index];
        if (!selected) {
            return;
        }
        this.updateElement(selected);
        this.hide();
    },

    updateElement: function(selected) {
        if (typeof this.options.updateElement === 'function') {
            this.options.updateElement(selected);
        } else {
            var value = (selected.textContent || '').replace(/^\s+|\s+$/g, '');
            this.element.value = value;
            this.oldValue = value;
            this.element.focus();
        }
        if (typeof this.options.afterUpdateElement === 'function') {
            this.options.afterUpdateElement(this.element, selected);
        }
    }
};
