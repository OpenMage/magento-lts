/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

function VarienForm(formId, firstFieldFocus) {
    this.form = document.getElementById(formId);
    if (!this.form) {
        return;
    }
    this.cache = [];
    this.currLoader = false;
    this.currDataIndex = false;
    if (typeof Validation === 'function') {
        this.validator = new Validation(this.form);
    }
    this.elementFocus = this.elementOnFocus.bind(this);
    this.elementBlur = this.elementOnBlur.bind(this);
    this.childLoader = this.onChangeChildLoad.bind(this);
    this.highlightClass = 'highlight';
    this.extraChildParams = '';
    this.firstFieldFocus = firstFieldFocus || false;
    this.bindElements();
    if (this.firstFieldFocus) {
        try {
            var elements = this.form.elements;
            for (var i = 0; i < elements.length; i++) {
                var el = elements[i];
                if (!el.disabled && el.type !== 'hidden' && el.offsetParent !== null) {
                    el.focus();
                    break;
                }
            }
        } catch (e) {}
    }
}

VarienForm.prototype = {
    submit: function (url) {
        if (this.validator && this.validator.validate()) {
            this.form.submit();
        }
        return false;
    },

    bindElements: function () {
        var elements = Array.prototype.slice.call(this.form.elements);
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].id) {
                elements[i].addEventListener('focus', this.elementFocus);
                elements[i].addEventListener('blur', this.elementBlur);
            }
        }
    },

    elementOnFocus: function (event) {
        var element = event.target.closest('fieldset');
        if (element) {
            element.classList.add(this.highlightClass);
        }
    },

    elementOnBlur: function (event) {
        var element = event.target.closest('fieldset');
        if (element) {
            element.classList.remove(this.highlightClass);
        }
    },

    setElementsRelation: function (parent, child, dataUrl, first) {
        if (typeof parent === 'string') {
            parent = document.getElementById(parent);
        }
        if (parent) {
            if (!this.cache[parent.id]) {
                this.cache[parent.id] = [];
                this.cache[parent.id]['child'] = child;
                this.cache[parent.id]['dataUrl'] = dataUrl;
                this.cache[parent.id]['data'] = [];
                this.cache[parent.id]['first'] = first || false;
            }
            parent.addEventListener('change', this.childLoader);
        }
    },

    onChangeChildLoad: function (event) {
        var element = event.target;
        this.elementChildLoad(element);
    },

    elementChildLoad: function (element, callback) {
        this.callback = callback || false;
        if (element.value) {
            this.currLoader = element.id;
            this.currDataIndex = element.value;
            if (this.cache[element.id]['data'][element.value]) {
                this.setDataToChild(this.cache[element.id]['data'][element.value]);
            } else {
                var self = this;
                var formData = new FormData();
                formData.append('parent', element.value);
                fetch(this.cache[this.currLoader]['dataUrl'], {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (resp) { return resp.json(); })
                .then(function (data) {
                    self.cache[self.currLoader]['data'][self.currDataIndex] = data;
                    self.setDataToChild(data);
                });
            }
        }
    },

    reloadChildren: function (transport) {
        var data;
        try {
            data = typeof transport.responseJSON !== 'undefined' ? transport.responseJSON : JSON.parse(transport.responseText);
        } catch (e) {
            data = {};
        }
        this.cache[this.currLoader]['data'][this.currDataIndex] = data;
        this.setDataToChild(data);
    },

    setDataToChild: function (data) {
        var child = document.getElementById(this.cache[this.currLoader]['child']);
        if (!child) {
            return;
        }
        var el;
        if (data.length) {
            el = document.createElement('select');
            if (this.cache[this.currLoader]['first']) {
                var first = document.createElement('option');
                first.value = '';
                first.text = this.cache[this.currLoader]['first'];
                el.appendChild(first);
            }
            for (var i in data) {
                if (data[i].value) {
                    var option = document.createElement('option');
                    option.value = data[i].value;
                    option.text = data[i].label;
                    if (child.value && (child.value == data[i].value || child.value == data[i].label)) {
                        option.selected = true;
                    }
                    el.appendChild(option);
                }
            }
        } else {
            el = document.createElement('input');
            el.type = 'text';
        }
        el.name = child.name;
        el.id = child.id;
        el.className = child.className;
        el.title = child.title;
        this._applyExtraChildParams(el);
        child.parentNode.replaceChild(el, child);

        this.bindElements();
        if (this.callback) {
            this.callback();
        }
    },

    /**
     * extraChildParams is a template-supplied raw attribute string
     * (e.g. ' onchange="shipping.setSameAsBilling(false);"'). Parse it on an
     * inert template element and copy the attributes over, so the replacement
     * child keeps supporting it without string-built HTML.
     */
    _applyExtraChildParams: function (el) {
        if (!this.extraChildParams) {
            return;
        }
        var tpl = document.createElement('template');
        tpl.innerHTML = '<div ' + this.extraChildParams + '></div>';
        var probe = tpl.content.firstElementChild;
        if (!probe) {
            return;
        }
        Array.prototype.forEach.call(probe.attributes, function (attr) {
            el.setAttribute(attr.name, attr.value);
        });
    }
};

function RegionUpdater(countryEl, regionTextEl, regionSelectEl, regions, disableAction, zipEl) {
    this.countryEl = document.getElementById(countryEl);
    this.regionTextEl = document.getElementById(regionTextEl);
    this.regionSelectEl = document.getElementById(regionSelectEl);
    this.zipEl = document.getElementById(zipEl);
    this.config = regions['config'];
    delete regions.config;
    this.regions = regions;

    this.disableAction = (typeof disableAction == 'undefined') ? 'hide' : disableAction;
    this.zipOptions = (typeof zipOptions == 'undefined') ? false : zipOptions;

    if (this.regionSelectEl.options.length <= 1) {
        this.update();
    }

    this.countryEl.addEventListener('change', this.update.bind(this));
}

RegionUpdater.prototype = {
    _checkRegionRequired: function () {
        var that = this;
        var elements = [this.regionTextEl, this.regionSelectEl];
        if (typeof this.config == 'undefined') {
            return;
        }
        var regionRequired = this.config.regions_required.indexOf(this.countryEl.value) >= 0;

        elements.forEach(function (currentElement) {
            if (typeof Validation !== 'undefined') {
                Validation.reset(currentElement);
            }
            var label = document.querySelector('label[for="' + currentElement.id + '"]');
            if (label) {
                var wildCard = label.querySelector('em') || label.querySelector('span.required');
                if (!wildCard) {
                    label.insertAdjacentHTML('beforeend', ' <span class="required">*</span>');
                    wildCard = label.querySelector('span.required');
                }
                if (!that.config.show_all_regions) {
                    if (regionRequired) {
                        label.parentNode.style.display = '';
                    } else {
                        label.parentNode.style.display = 'none';
                    }
                }

                if (wildCard) {
                    if (!regionRequired) {
                        wildCard.style.display = 'none';
                        label.classList.remove('required');
                    } else {
                        wildCard.style.display = '';
                        label.classList.add('required');
                    }
                }
            }

            if (!regionRequired) {
                currentElement.classList.remove('required-entry');
                if ('select' == currentElement.tagName.toLowerCase()) {
                    currentElement.classList.remove('validate-select');
                }
            } else {
                currentElement.classList.add('required-entry');
                if ('select' == currentElement.tagName.toLowerCase()) {
                    currentElement.classList.add('validate-select');
                }
            }
        });
    },

    update: function () {
        if (this.regions[this.countryEl.value]) {
            var i, option, region, def;

            def = this.regionSelectEl.getAttribute('defaultValue');
            if (this.regionTextEl) {
                if (!def) {
                    def = this.regionTextEl.value.toLowerCase();
                }
                this.regionTextEl.value = '';
            }

            this.regionSelectEl.options.length = 1;
            for (var regionId in this.regions[this.countryEl.value]) {
                region = this.regions[this.countryEl.value][regionId];

                option = document.createElement('OPTION');
                option.value = regionId;
                // option.text is rendered as plain text, never parsed as HTML
                option.text = region.name;
                option.title = region.name;

                if (this.regionSelectEl.options.add) {
                    this.regionSelectEl.options.add(option);
                } else {
                    this.regionSelectEl.appendChild(option);
                }

                if (regionId == def || (region.name && region.name.toLowerCase() == def)
                    || (region.name && region.code.toLowerCase() == def)
                ) {
                    this.regionSelectEl.value = regionId;
                }
            }
            this.sortSelect();
            if (this.disableAction == 'hide') {
                if (this.regionTextEl) {
                    this.regionTextEl.style.display = 'none';
                }
                this.regionSelectEl.style.display = '';
            } else if (this.disableAction == 'disable') {
                if (this.regionTextEl) {
                    this.regionTextEl.disabled = true;
                }
                this.regionSelectEl.disabled = false;
            }
            this.setMarkDisplay(this.regionSelectEl, true);
        } else {
            this.regionSelectEl.options.length = 1;
            this.sortSelect();
            if (this.disableAction == 'hide') {
                if (this.regionTextEl) {
                    this.regionTextEl.style.display = '';
                }
                this.regionSelectEl.style.display = 'none';
                if (typeof Validation !== 'undefined') {
                    Validation.reset(this.regionSelectEl);
                }
            } else if (this.disableAction == 'disable') {
                if (this.regionTextEl) {
                    this.regionTextEl.disabled = false;
                }
                this.regionSelectEl.disabled = true;
            } else if (this.disableAction == 'nullify') {
                this.regionSelectEl.options.length = 1;
                this.regionSelectEl.value = '';
                this.regionSelectEl.selectedIndex = 0;
                this.lastCountryId = '';
            }
            this.setMarkDisplay(this.regionSelectEl, false);
        }

        this._checkRegionRequired();
        var zipUpdater = new ZipUpdater(this.countryEl.value, this.zipEl);
        zipUpdater.update();
    },

    setMarkDisplay: function (elem, display) {
        if (typeof elem === 'string') {
            elem = document.getElementById(elem);
        }
        if (!elem) return;
        var parent0 = elem.parentNode;
        var parent1 = parent0 ? parent0.parentNode : null;
        var labelElement = (parent0 ? parent0.querySelector('label > span.required') : null) ||
                           (parent1 ? parent1.querySelector('label > span.required') : null) ||
                           (parent0 ? parent0.querySelector('label.required > em') : null) ||
                           (parent1 ? parent1.querySelector('label.required > em') : null);
        if (labelElement) {
            var labelParent = labelElement.parentNode;
            var inputElement = labelParent ? labelParent.nextElementSibling : null;
            if (inputElement && inputElement.tagName !== 'INPUT') inputElement = null;
            if (display) {
                labelElement.style.display = '';
                if (inputElement) {
                    inputElement.classList.add('required-entry');
                }
            } else {
                labelElement.style.display = 'none';
                if (inputElement) {
                    inputElement.classList.remove('required-entry');
                }
            }
        }
    },

    sortSelect: function () {
        var elem = this.regionSelectEl;
        var tmpArray = [];
        var currentVal = elem.value;
        for (var i = 1; i < elem.options.length; i++) {
            tmpArray.push([elem.options[i].text, elem.options[i].value]);
        }
        tmpArray.sort(function (a, b) {
            return a[0].localeCompare(b[0]);
        });
        for (var i = 0; i < tmpArray.length; i++) {
            elem.options[i + 1] = new Option(tmpArray[i][0], tmpArray[i][1]);
        }
        elem.value = currentVal;
    }
};

function ZipUpdater(country, zipElement) {
    this.country = country;
    this.zipElement = typeof zipElement === 'string' ? document.getElementById(zipElement) : zipElement;
}

ZipUpdater.prototype = {
    update: function () {
        if (typeof optionalZipCountries == 'undefined') {
            return false;
        }

        if (this.zipElement != undefined) {
            if (typeof Validation !== 'undefined') {
                Validation.reset(this.zipElement);
            }
            this._setPostcodeOptional();
        } else {
            window.addEventListener('load', this._setPostcodeOptional.bind(this));
        }
    },

    _setPostcodeOptional: function () {
        if (typeof this.zipElement === 'string') {
            this.zipElement = document.getElementById(this.zipElement);
        }
        if (this.zipElement === undefined || this.zipElement === null) {
            return false;
        }

        var label = document.querySelector('label[for="' + this.zipElement.id + '"]');
        var wildCard;
        if (label !== undefined && label !== null) {
            wildCard = label.querySelector('em') || label.querySelector('span.required');
            if (!wildCard) {
                label.insertAdjacentHTML('beforeend', ' <span class="required">*</span>');
                wildCard = label.querySelector('span.required');
            }
        }

        if (optionalZipCountries.indexOf(this.country) != -1) {
            if (label) {
                label.classList.remove('required');
            }
            this.zipElement.classList.remove('required-entry');
            if (wildCard) {
                wildCard.style.display = 'none';
            }
        } else {
            if (label) {
                label.classList.add('required');
            }
            this.zipElement.classList.add('required-entry');
            if (wildCard) {
                wildCard.style.display = '';
            }
        }
    }
};
