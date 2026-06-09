/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function TranslateInline(trigEl, ajaxUrl, area) {
    this.ajaxUrl = ajaxUrl;
    this.area = area;

    this.trigTimer = null;
    this.trigContentEl = null;
    this.trigEl = document.getElementById(trigEl);
    this.trigEl.addEventListener('click', this.formShow.bind(this));

    document.body.addEventListener('mousemove', function(e) {
        var target = e.target;
        if (!target.matches('*[data-translate]')) {
            target = target.closest('*[data-translate]');
        }

        if (target && target.matches('*[data-translate]')) {
            this.trigShow(target, e);
        } else {
            if (e.target.matches('#' + trigEl)) {
                this.trigHideClear();
            } else {
                this.trigHideDelayed();
            }
        }
    }.bind(this));

    this.helperDiv = document.createElement('div');
}

TranslateInline.prototype.initializeElement = function(el) {
    if (!el.initializedTranslate) {
        el.classList.add('translate-inline');
        el.initializedTranslate = true;
    }
};

TranslateInline.prototype.reinitElements = function(el) {
    var self = this;
    document.querySelectorAll('*[data-translate]').forEach(function(el) {
        self.initializeElement(el);
    });
};

TranslateInline.prototype.trigShow = function(el, event) {
    if (this.trigContentEl != el) {
        this.trigHideClear();
        this.trigContentEl = el;
        var left = 0, top = 0, current = el;
        while (current) {
            left += current.offsetLeft;
            top += current.offsetTop;
            current = current.offsetParent;
        }

        this.trigEl.style.left = left + 'px';
        this.trigEl.style.top = top + 'px';
        this.trigEl.style.display = 'block';

        event.preventDefault();
        event.stopPropagation();
    }
};

TranslateInline.prototype.trigHide = function() {
    this.trigEl.style.display = 'none';
    this.trigContentEl = null;
};

TranslateInline.prototype.trigHideDelayed = function() {
    if (this.trigTimer === null) {
        this.trigTimer = window.setTimeout(this.trigHide.bind(this), 2000);
    }
};

TranslateInline.prototype.trigHideClear = function() {
    clearInterval(this.trigTimer);
    this.trigTimer = null;
};

TranslateInline.prototype.formShow = function() {
    if (this.formIsShown) {
        return;
    }
    this.formIsShown = true;

    var el = this.trigContentEl;
    if (!el) {
        return;
    }
    this.trigHideClear();
    eval('var data = ' + el.getAttribute('data-translate'));

    var content = '<form id="translate-inline-form">';
    var tpl =
        '<div class="magento_table_container"><table cellspacing="0">' +
            '<tr><th class="label">Location:</th><td class="value">#{location}</td></tr>' +
            '<tr><th class="label">Scope:</th><td class="value">#{scope}</td></tr>' +
            '<tr><th class="label">Shown:</th><td class="value">#{shown_escape}</td></tr>' +
            '<tr><th class="label">Original:</th><td class="value">#{original_escape}</td></tr>' +
            '<tr><th class="label">Translated:</th><td class="value">#{translated_escape}</td></tr>' +
            '<tr><th class="label"><label for="perstore_#{i}">Store View Specific:</label></th><td class="value">' +
                '<input id="perstore_#{i}" name="translate[#{i}][perstore]" type="checkbox" value="1"/>' +
            '</td></tr>' +
            '<tr><th class="label"><label for="custom_#{i}">Custom:</label></th><td class="value">' +
                '<input name="translate[#{i}][original]" type="hidden" value="#{scope}::#{original_escape}"/>' +
                '<input id="custom_#{i}" name="translate[#{i}][custom]" class="input-text" value="#{translated_escape}" />' +
            '</td></tr>' +
        '</table></div>';
    for (var i = 0; i < data.length; i++) {
        data[i]['i'] = i;
        data[i]['shown_escape'] = this.escapeHTML(data[i]['shown']);
        data[i]['translated_escape'] = this.escapeHTML(data[i]['translated']);
        data[i]['original_escape'] = this.escapeHTML(data[i]['original']);
        content += tpl.replace(/#\{(\w+)\}/g, function(m, k) {
            return data[i][k] !== undefined ? data[i][k] : '';
        });
    }
    content += '</form><p class="a-center accent">Please refresh the page to see your changes after submitting this form.</p>';

    if (typeof Windows !== 'undefined' && typeof Dialog !== 'undefined') {
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration: 0};
        Windows.overlayHideEffectOptions = {duration: 0};

        Dialog.confirm(content, {
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            title: "Translation",
            width: 650,
            height: 470,
            zIndex: 2100,
            recenterAuto: false,
            hideEffect: function(el) { el.style.display = 'none'; },
            showEffect: function(el) { el.style.display = ''; },
            id: "translate-inline",
            buttonClass: "form-button button",
            okLabel: "Submit",
            ok: this.formOk.bind(this),
            cancel: this.formClose.bind(this),
            onClose: this.formClose.bind(this)
        });
    }
    this.trigHide();
};

TranslateInline.prototype.formOk = function(win) {
    if (this.formIsSubmitted) {
        return;
    }
    this.formIsSubmitted = true;

    var inputs = Array.from(document.getElementById('translate-inline-form').querySelectorAll('input')), parameters = {};
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            if (inputs[i].checked) {
                parameters[inputs[i].name] = inputs[i].value;
            }
        }
        else {
            parameters[inputs[i].name] = inputs[i].value;
        }
    }
    parameters['area'] = this.area;

    var body = new URLSearchParams();
    for (var key in parameters) {
        if (parameters.hasOwnProperty(key)) {
            body.append(key, parameters[key]);
        }
    }

    var self = this;
    fetch(this.ajaxUrl, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: body
    }).then(function() {
        win.close();
        self.formClose(win);
    });

    this.formIsSubmitted = false;
};

TranslateInline.prototype.ajaxComplete = function(win, transport) {
    win.close();
    this.formClose(win);
};

TranslateInline.prototype.formClose = function(win) {
    if (typeof Windows !== 'undefined') {
        Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
        Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
    }
    this.formIsShown = false;
};

TranslateInline.prototype.escapeHTML = function(str) {
    this.helperDiv.innerHTML = '';
    var text = document.createTextNode(str);
    this.helperDiv.appendChild(text);
    var escaped = this.helperDiv.innerHTML;
    escaped = escaped.replace(/"/g, '&quot;');
    return escaped;
};
