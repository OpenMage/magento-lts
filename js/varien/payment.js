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
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

function paymentForm(formId) {
    this.formId = formId;
    this.currentMethod = null;
    this.validator = new Validation(this.formId);

    var form = document.getElementById(formId);
    var elements = form ? Array.from(form.elements) : [];
    var method = null;

    for (var i = 0; i < elements.length; i++) {
        if (elements[i].name === 'payment[method]' || elements[i].name === 'form_key') {
            if (elements[i].checked) {
                method = elements[i].value;
            }
        } else {
            if (elements[i].type && elements[i].type.toLowerCase() !== 'submit') {
                elements[i].disabled = true;
            }
        }
        elements[i].setAttribute('autocomplete', 'off');
    }

    if (method) this.switchMethod(method);
}

paymentForm.prototype = {
    switchMethod: function(method) {
        if (this.currentMethod) {
            var oldForm = document.getElementById('payment_form_' + this.currentMethod);
            if (oldForm) {
                oldForm.style.display = 'none';
                Array.from(oldForm.getElementsByTagName('input')).forEach(function(el) { el.disabled = true; });
                Array.from(oldForm.getElementsByTagName('select')).forEach(function(el) { el.disabled = true; });
            }
        }

        var newForm = document.getElementById('payment_form_' + method);
        if (newForm) {
            newForm.style.display = '';
            Array.from(newForm.getElementsByTagName('input')).forEach(function(el) { el.disabled = false; });
            Array.from(newForm.getElementsByTagName('select')).forEach(function(el) { el.disabled = false; });
            this.currentMethod = method;
        }
    }
};
