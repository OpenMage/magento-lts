/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var VarienRulesForm = function (parent, newChildUrl) {
    this.initialize(parent, newChildUrl);
};

VarienRulesForm.prototype = {

    _escapeHTML: function (str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    },

    _evalScripts: function (container) {
        container.querySelectorAll('script').forEach(function (s) {
            var ns = document.createElement('script');
            if (s.src) {
                ns.src = s.src;
            } else {
                ns.textContent = s.textContent;
            }
            document.head.appendChild(ns);
        });
    },

    _isJSON: function (str) {
        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    },

    initialize: function (parent, newChildUrl) {
        this.parent = document.getElementById(parent);
        this.newChildUrl = newChildUrl;
        this.shownElement = null;
        this.updateElement = null;
        this.chooserSelectedItems = {};
        this.readOnly = false;

        var elems = this.parent.getElementsByClassName('rule-param');
        for (var i = 0; i < elems.length; i++) {
            this.initParam(elems[i]);
        }
    },

    setReadonly: function (readonly) {
        this.readOnly = readonly;
        var elems = this.parent.getElementsByClassName('rule-param-remove');
        for (var i = 0; i < elems.length; i++) {
            var element = elems[i];
            if (this.readOnly) {
                element.style.display = 'none';
            } else {
                element.style.display = '';
            }
        }

        var elems = this.parent.getElementsByClassName('rule-param-new-child');
        for (var i = 0; i < elems.length; i++) {
            var element = elems[i];
            if (this.readOnly) {
                element.style.display = 'none';
            } else {
                element.style.display = '';
            }
        }

        var elems = this.parent.getElementsByClassName('rule-param');
        for (var i = 0; i < elems.length; i++) {
            var container = elems[i];
            var label = container.querySelector('.label');
            if (label) {
                if (this.readOnly) {
                    label.classList.add('label-disabled');
                } else {
                    label.classList.remove('label-disabled');
                }
            }
        }
    },

    initParam: function (container) {
        container.rulesObject = this;
        var label = container.querySelector('.label');
        if (label) {
            label.addEventListener('click', this.showParamInputField.bind(this, container));
        }

        var elem = container.querySelector('.element');
        if (elem) {
            var trig = elem.querySelector('.rule-chooser-trigger');
            if (trig) {
                trig.addEventListener('click', this.toggleChooser.bind(this, container));
            }

            var apply = elem.querySelector('.rule-param-apply');
            if (apply) {
                apply.addEventListener('click', this.hideParamInputField.bind(this, container));
            } else {
                elem = elem.querySelector('.element-value-changer');
                elem.container = container;
                if (!elem.multiple) {
                    elem.addEventListener('change', this.hideParamInputField.bind(this, container));
                }
                elem.addEventListener('blur', this.hideParamInputField.bind(this, container));
            }
        }

        var remove = container.querySelector('.rule-param-remove');
        if (remove) {
            remove.addEventListener('click', this.removeRuleEntry.bind(this, container));
        }
    },

    showChooserElement: function (chooser) {
        this.chooserSelectedItems = {};
        if (chooser.classList.contains('no-split')) {
            this.chooserSelectedItems[this.updateElement.value] = 1;
        } else {
            var values = this.updateElement.value.split(','), s = '';
            for (var i = 0; i < values.length; i++) {
                s = values[i].trim();
                if (s != '') {
                    this.chooserSelectedItems[s] = 1;
                }
            }
        }

        var self = this;
        var params = new URLSearchParams();
        params.append('form_key', FORM_KEY);
        Object.keys(this.chooserSelectedItems).forEach(function (key) {
            params.append('selected[]', key);
        });

        fetch(chooser.getAttribute('url'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        }).then(function (response) {
            if (!response.ok) {
                self._processFailure();
                return;
            }
            return response.text();
        }).then(function (responseText) {
            if (responseText === undefined) return;
            if (self._processSuccess(responseText)) {
                chooser.innerHTML = responseText;
                self._evalScripts(chooser);
                self.showChooserLoaded(chooser);
            }
        }).catch(function () {
            self._processFailure();
        });
    },

    showChooserLoaded: function (chooser) {
        chooser.style.display = 'block';
    },

    showChooser: function (container, event) {
        var chooser = container.closest('li');
        if (!chooser) {
            return;
        }
        chooser = chooser.querySelector('.rule-chooser');
        if (!chooser) {
            return;
        }
        this.showChooserElement(chooser);
    },

    hideChooser: function (container, event) {
        var chooser = container.closest('li');
        if (!chooser) {
            return;
        }
        chooser = chooser.querySelector('.rule-chooser');
        if (!chooser) {
            return;
        }
        chooser.style.display = 'none';
    },

    toggleChooser: function (container, event) {
        if (this.readOnly) {
            return false;
        }

        var chooser = container.closest('li').querySelector('.rule-chooser');
        if (!chooser) {
            return;
        }
        if (chooser.style.display == 'block') {
            chooser.style.display = 'none';
            this.cleanChooser(container, event);
        } else {
            this.showChooserElement(chooser);
        }
    },

    cleanChooser: function (container, event) {
        var chooser = container.closest('li').querySelector('.rule-chooser');
        if (!chooser) {
            return;
        }
        chooser.innerHTML = '';
    },

    showParamInputField: function (container, event) {
        if (this.readOnly) {
            return false;
        }

        if (this.shownElement) {
            this.hideParamInputField(this.shownElement, event);
        }

        container.classList.add('rule-param-edit');
        var elemContainer = container.querySelector('.element');

        var elem = elemContainer.querySelector('input.input-text');
        if (elem) {
            elem.focus();
            if (elem && elem.id && elem.id.match(/__value$/)) {
                this.updateElement = elem;
                //this.showChooser(container, event);
            }
        }

        var elem = elemContainer.querySelector('.element-value-changer');
        if (elem) {
            elem.focus();
        }

        this.shownElement = container;
    },

    hideParamInputField: function (container, event) {
        container.classList.remove('rule-param-edit');
        var label = container.querySelector('.label'), elem;

        if (!container.classList.contains('rule-param-new-child')) {
            elem = container.querySelector('.element-value-changer');
            if (elem && elem.options) {
                var selectedOptions = [];
                for (var i = 0; i < elem.options.length; i++) {
                    if (elem.options[i].selected) {
                        selectedOptions.push(elem.options[i].text);
                    }
                }

                var str = selectedOptions.join(', ');
                label.innerHTML = str != '' ? str : '...';
            }

            elem = container.querySelector('input.input-text');
            if (elem) {
                var str = elem.value.replace(/(^\s+|\s+$)/g, '');
                elem.value = str;
                if (str == '') {
                    str = '...';
                } else if (str.length > 100) {
                    str = str.substr(0, 100) + '...';
                }
                label.innerHTML = this._escapeHTML(str);
            }
        } else {
            elem = container.querySelector('.element-value-changer');
            if (elem.value) {
                this.addRuleNewChild(elem);
            }
            elem.value = '';
        }

        if (elem && elem.id && elem.id.match(/__value$/)) {
            this.hideChooser(container, event);
            this.updateElement = null;
        }

        this.shownElement = null;
    },

    addRuleNewChild: function (elem) {
        var parent_id = elem.id.replace(/^.*__(.*)__.*$/, '$1');
        var children_ul = document.getElementById(elem.id.replace(/__/g, ':').replace(/[^:]*$/, 'children').replace(/:/g, '__'));
        var max_id = 0, i;
        var children_inputs = children_ul.querySelectorAll('input.hidden');
        if (children_inputs.length) {
            children_inputs.forEach(function (el) {
                if (el.id.match(/__type$/)) {
                    i = 1 * el.id.replace(/^.*__.*?([0-9]+)__.*$/, '$1');
                    max_id = i > max_id ? i : max_id;
                }
            });
        }
        var new_id = parent_id + '--' + (max_id + 1);
        var new_type = elem.value;
        var new_elem = document.createElement('LI');
        new_elem.className = 'rule-param-wait';
        new_elem.innerHTML = Translator.translate('Please wait, loading...');
        children_ul.insertBefore(new_elem, elem.closest('li'));

        var self = this;
        var params = new URLSearchParams();
        params.append('form_key', FORM_KEY);
        params.append('type', new_type.replace('/', '-'));
        params.append('id', new_id);

        fetch(this.newChildUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        }).then(function (response) {
            if (!response.ok) {
                self._processFailure();
                return;
            }
            return response.text();
        }).then(function (responseText) {
            if (responseText === undefined) return;
            if (self._processSuccess(responseText)) {
                new_elem.innerHTML = responseText;
                self._evalScripts(new_elem);
            }
            self.onAddNewChildComplete(new_elem);
        }).catch(function () {
            self._processFailure();
        });
    },

    _processSuccess: function (responseText) {
        if (this._isJSON(responseText)) {
            var response = JSON.parse(responseText);
            if (response.error) {
                alert(response.message);
            }
            if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            }
            return false;
        }
        return true;
    },

    _processFailure: function () {
        location.href = BASE_URL;
    },

    onAddNewChildComplete: function (new_elem) {
        if (this.readOnly) {
            return false;
        }

        new_elem.classList.remove('rule-param-wait');
        var elems = new_elem.getElementsByClassName('rule-param');
        for (var i = 0; i < elems.length; i++) {
            this.initParam(elems[i]);
        }
    },

    removeRuleEntry: function (container, event) {
        var li = container.closest('li');
        li.parentNode.removeChild(li);
    },

    chooserGridInit: function (grid) {
        //grid.reloadParams = {'selected[]':Object.keys(this.chooserSelectedItems)};
    },

    chooserGridRowInit: function (grid, row) {
        if (!grid.reloadParams) {
            grid.reloadParams = { 'selected[]': Object.keys(this.chooserSelectedItems) };
        }
    },

    chooserGridRowClick: function (grid, event) {
        var trElement = event.target.closest('tr');
        var isInput = event.target.tagName == 'INPUT';
        if (trElement) {
            var checkbox = trElement.querySelectorAll('input');
            if (checkbox[0]) {
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
    },

    chooserGridCheckboxCheck: function (grid, element, checked) {
        if (checked) {
            if (!element.closest('th')) {
                this.chooserSelectedItems[element.value] = 1;
            }
        } else {
            delete this.chooserSelectedItems[element.value];
        }
        grid.reloadParams = { 'selected[]': Object.keys(this.chooserSelectedItems) };
        this.updateElement.value = Object.keys(this.chooserSelectedItems).join(', ');
    }
};
