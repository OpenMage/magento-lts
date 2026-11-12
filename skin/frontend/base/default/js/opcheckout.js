/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     base_default
 */

/**
 * Helper: fetch wrapper mimicking Ajax.Request / Ajax.Updater callback style.
 * Callbacks receive a transport-like object with responseText and responseJSON.
 */
var _opcAjax = function(url, opts) {
    const method = (opts.method || 'get').toUpperCase();
    // Ajax.Request always sent X-Requested-With; the controllers read isAjax()
    const fetchOpts = { method: method, headers: { 'X-Requested-With': 'XMLHttpRequest' } };

    if (opts.parameters && method === 'POST') {
        if (typeof opts.parameters === 'string') {
            fetchOpts.body = opts.parameters;
            fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        } else if (opts.parameters instanceof FormData) {
            fetchOpts.body = opts.parameters;
        } else {
            fetchOpts.body = new URLSearchParams(opts.parameters).toString();
            fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
    } else if (opts.parameters && method === 'GET') {
        const sep = url.indexOf('?') === -1 ? '?' : '&';
        if (typeof opts.parameters === 'string') {
            url += sep + opts.parameters;
        } else {
            url += sep + new URLSearchParams(opts.parameters).toString();
        }
    }

    fetch(url, fetchOpts).then(function(resp) {
        return resp.text().then(function(text) {
            const transport = { responseText: text, responseJSON: null, status: resp.status };
            try { transport.responseJSON = JSON.parse(text); } catch(e) {}
            return { ok: resp.ok, transport: transport };
        });
    }).then(function(result) {
        // Callbacks run outside the fetch chain. Ajax.Request caught an exception
        // in onSuccess and still ran onComplete; it never treated it as a failed
        // request, so a callback bug must not trigger ajaxFailure's redirect.
        try {
            if (!result.ok) {
                if (opts.onFailure) opts.onFailure(result.transport);
            } else {
                if (opts.onSuccess) opts.onSuccess(result.transport);
            }
        } catch (e) {
            if (window.console) console.error(e);
        }
        if (opts.onComplete) opts.onComplete(result.transport);
    }, function() {
        const transport = { responseText: '', responseJSON: null, status: 0 };
        if (opts.onFailure) opts.onFailure(transport);
        if (opts.onComplete) opts.onComplete(transport);
    });
};

/**
 * Helper: fetch HTML and put it into a container element (replaces Ajax.Updater).
 */
var _opcAjaxUpdate = function(containerId, url, opts) {
    const wrappedSuccess = opts.onSuccess;
    const wrappedComplete = opts.onComplete;

    _opcAjax(url, Object.assign({}, opts, {
        onSuccess: function(transport) {
            const el = document.getElementById(containerId);
            if (el) {
                el.innerHTML = transport.responseText;
                // Ajax.Updater defaulted to evalScripts: false
                if (opts.evalScripts) {
                    Varien.evalScripts(el);
                }
            }
            if (wrappedSuccess) wrappedSuccess(transport);
        },
        onComplete: function(transport) {
            if (wrappedComplete) wrappedComplete(transport);
        }
    }));
};

/**
 * Helper: parse JSON from a transport-like object.
 */
var _opcParseResponse = function(transport) {
    if (transport.responseJSON) return transport.responseJSON;
    if (transport.responseText) {
        try { return JSON.parse(transport.responseText); } catch(e) {}
    }
    return {};
};

/**
 * Helper: get all form elements (replaces Form.getElements).
 */
var _opcFormElements = function(formId) {
    const form = typeof formId === 'string' ? document.getElementById(formId) : formId;
    if (!form) return [];
    return Array.from(form.elements);
};

/**
 * Helper: serialize a form (replaces Form.serialize).
 */
var _opcSerializeForm = function(formId) {
    const form = typeof formId === 'string' ? document.getElementById(formId) : formId;
    if (!form) return '';
    return new URLSearchParams(new FormData(form)).toString();
};

/**
 * Helper: strip HTML tags from a string.
 */
var _opcStripTags = function(str) {
    if (typeof str !== 'string') return String(str || '');
    const tpl = document.createElement('template');
    tpl.innerHTML = str;
    return tpl.content.textContent || '';
};

/**
 * Helper: iterate over object entries (replaces Hash.each).
 */
var _opcEachEntry = function(obj, fn) {
    const keys = Object.keys(obj);
    for (let i = 0; i < keys.length; i++) {
        fn({ key: keys[i], value: obj[keys[i]] });
    }
};

/**
 * Helper: dispatch a custom event on an element.
 */
var _opcFireEvent = function(el, eventName, data) {
    if (!el) return;
    const evt = new CustomEvent(eventName, { detail: data, bubbles: true });
    // Prototype-era observers read event.memo — keep both properties populated
    evt.memo = data || {};
    el.dispatchEvent(evt);
    // Prototype registers custom-event observers on dataavailable, which a native
    // CustomEvent never reaches. Fire through Prototype too while it loads.
    if (window.Event && typeof Event.fire === 'function') {
        Event.fire(el, eventName, data || {});
    }
};

// =====================================================================
// Checkout
// =====================================================================

var Checkout = function(accordion, urls) {
    this.initialize(accordion, urls);
};
Checkout.prototype = {
    initialize: function(accordion, urls) {
        this.accordion = accordion;
        this.progressUrl = urls.progress;
        this.reviewUrl = urls.review;
        this.saveMethodUrl = urls.saveMethod;
        this.failureUrl = urls.failure;
        this.billingForm = false;
        this.shippingForm = false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';
        this.loadWaiting = false;
        this.steps = ['login', 'billing', 'shipping', 'shipping_method', 'payment', 'review'];
        //We use billing as beginning step since progress bar tracks from billing
        this.currentStep = 'billing';

        const self = this;
        this.accordion.sections.forEach(function(section) {
            const sectionEl = typeof section === 'string' ? document.getElementById(section) : section;
            if (sectionEl) {
                const title = sectionEl.querySelector('.step-title');
                if (title) {
                    title.addEventListener('click', self._onSectionClick.bind(self));
                }
            }
        });

        this.accordion.disallowAccessToNextSections = true;
    },

    /**
     * Section header click handler
     *
     * @param event
     */
    _onSectionClick: function(event) {
        const section = event.target.closest('.section');
        if (section && section.classList.contains('allow')) {
            event.preventDefault();
            event.stopPropagation();
            this.gotoSection(section.getAttribute('id').replace('opc-', ''), false);
            return false;
        }
    },

    ajaxFailure: function() {
        location.href = encodeURI(this.failureUrl);
    },

    reloadProgressBlock: function(toStep) {
        this.reloadStep(toStep);
        if (this.syncBillingShipping) {
            this.syncBillingShipping = false;
            this.reloadStep('shipping');
        }
    },

    reloadStep: function(prevStep) {
        const self = this;
        _opcAjaxUpdate(prevStep + '-progress-opcheckout', this.progressUrl, {
            method: 'get',
            onFailure: this.ajaxFailure.bind(this),
            onComplete: function() {
                self.resetPreviousSteps();
            },
            parameters: prevStep ? { prevStep: prevStep } : null
        });
    },

    reloadReviewBlock: function() {
        _opcAjaxUpdate('checkout-review-load', this.reviewUrl, { method: 'get', onFailure: this.ajaxFailure.bind(this) });
    },

    _disableEnableAll: function(element, isDisabled) {
        const descendants = Array.from(element.querySelectorAll('*'));
        descendants.forEach(function(descendant) {
            descendant.disabled = isDisabled;
        });
        element.disabled = isDisabled;
    },

    setLoadWaiting: function(step, keepDisabled) {
        let container;
        if (step) {
            if (this.loadWaiting) {
                this.setLoadWaiting(false);
            }
            container = document.getElementById(step + '-buttons-container');
            container.classList.add('disabled');
            container.style.opacity = 0.5;
            this._disableEnableAll(container, true);
            const pleaseWait = document.getElementById(step + '-please-wait');
            if (pleaseWait) pleaseWait.style.display = '';
        } else {
            if (this.loadWaiting) {
                container = document.getElementById(this.loadWaiting + '-buttons-container');
                const isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.classList.remove('disabled');
                    container.style.opacity = 1;
                }
                this._disableEnableAll(container, isDisabled);
                const pleaseWaitEl = document.getElementById(this.loadWaiting + '-please-wait');
                if (pleaseWaitEl) pleaseWaitEl.style.display = 'none';
            }
        }
        this.loadWaiting = step;
    },

    gotoSection: function(section, reloadProgressBlock) {
        if (reloadProgressBlock) {
            this.reloadProgressBlock(this.currentStep);
        }
        this.currentStep = section;
        const sectionElement = document.getElementById('opc-' + section);
        sectionElement.classList.add('allow');
        this.accordion.openSection('opc-' + section);
        if (!reloadProgressBlock) {
            this.resetPreviousSteps();
        }
    },

    resetPreviousSteps: function() {
        const stepIndex = this.steps.indexOf(this.currentStep);

        //Clear other steps if already populated through javascript
        for (let i = stepIndex; i < this.steps.length; i++) {
            const nextStep = this.steps[i];
            const progressDiv = document.getElementById(nextStep + '-progress-opcheckout');
            if (progressDiv) {
                //Remove the link
                Array.from(progressDiv.querySelectorAll('.changelink')).forEach(function(el) { el.remove(); });
                Array.from(progressDiv.querySelectorAll('dt')).forEach(function(el) { el.classList.remove('complete'); });
                //Remove the content
                Array.from(progressDiv.querySelectorAll('dd.complete')).forEach(function(el) { el.remove(); });
            }
        }
    },

    changeSection: function(section) {
        const changeStep = section.replace('opc-', '');
        this.gotoSection(changeStep, false);
    },

    setMethod: function() {
        const loginGuest = document.getElementById('login:guest');
        const loginRegister = document.getElementById('login:register');
        if (loginGuest && loginGuest.checked) {
            this.method = 'guest';
            _opcAjax(
                this.saveMethodUrl,
                { method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: { method: 'guest' } }
            );
            const regPassword = document.getElementById('register-customer-password');
            if (regPassword) regPassword.style.display = 'none';
            this.gotoSection('billing', true);
        }
        else if (loginRegister && (loginRegister.checked || loginRegister.type == 'hidden')) {
            this.method = 'register';
            _opcAjax(
                this.saveMethodUrl,
                { method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: { method: 'register' } }
            );
            const regPasswordEl = document.getElementById('register-customer-password');
            if (regPasswordEl) regPasswordEl.style.display = '';
            this.gotoSection('billing', true);
        }
        else {
            alert(_opcStripTags(Translator.translate('Please choose to register or to checkout as a guest')));
            return false;
        }
        _opcFireEvent(document.body, 'login:setMethod', { method: this.method });
    },

    setBilling: function() {
        const useYes = document.getElementById('billing:use_for_shipping_yes');
        const useNo = document.getElementById('billing:use_for_shipping_no');
        const sameAsBilling = document.getElementById('shipping:same_as_billing');
        if (useYes && useYes.checked) {
            shipping.syncWithBilling();
            document.getElementById('opc-shipping').classList.add('allow');
            this.gotoSection('shipping_method', true);
        } else if (useNo && useNo.checked) {
            sameAsBilling.checked = false;
            this.gotoSection('shipping', true);
        } else {
            sameAsBilling.checked = true;
            this.gotoSection('shipping', true);
        }
    },

    setShipping: function() {
        this.gotoSection('shipping_method', true);
    },

    setShippingMethod: function() {
        this.gotoSection('payment', true);
    },

    setPayment: function() {
        this.gotoSection('review', true);
    },

    setReview: function() {
        this.reloadProgressBlock();
    },

    back: function() {
        if (this.loadWaiting) return;
        //Navigate back to the previous available step
        let stepIndex = this.steps.indexOf(this.currentStep);
        let section = this.steps[--stepIndex];
        let sectionElement = document.getElementById('opc-' + section);

        //Traverse back to find the available section. Ex Virtual product does not have shipping section
        while (sectionElement === null && stepIndex > 0) {
            --stepIndex;
            section = this.steps[stepIndex];
            sectionElement = document.getElementById('opc-' + section);
        }
        this.changeSection('opc-' + section);
    },

    setStepResponse: function(response) {
        if (response.update_section) {
            const updateEl = document.getElementById('checkout-' + response.update_section.name + '-load');
            if (updateEl) {
                updateEl.innerHTML = response.update_section.html;
                Varien.evalScripts(updateEl);
            }
        }
        if (response.allow_sections) {
            response.allow_sections.forEach(function(e) {
                const el = document.getElementById('opc-' + e);
                if (el) el.classList.add('allow');
            });
        }

        if (response.duplicateBillingInfo) {
            this.syncBillingShipping = true;
            shipping.setSameAsBilling(true);
        }

        if (response.goto_section) {
            this.gotoSection(response.goto_section, true);
            return true;
        }
        if (response.redirect) {
            location.href = encodeURI(response.redirect);
            return true;
        }
        return false;
    }
};
Varien.classCompat(Checkout);

// =====================================================================
// Billing
// =====================================================================

var Billing = function(form, addressUrl, saveUrl) {
    this.initialize(form, addressUrl, saveUrl);
};
Billing.prototype = {
    initialize: function(form, addressUrl, saveUrl) {
        this.form = form;
        const formEl = document.getElementById(this.form);
        if (formEl) {
            formEl.addEventListener('submit', function(event) { this.save(); event.preventDefault(); event.stopPropagation(); }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.onAddressLoad = this.fillForm.bind(this);
        this.onSave = this.nextStep.bind(this);
        this.onComplete = this.resetLoadWaiting.bind(this);
    },

    setAddress: function(addressId) {
        if (addressId) {
            _opcAjax(
                this.addressUrl + addressId,
                { method: 'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout) }
            );
        }
        else {
            this.fillForm(false);
        }
    },

    newAddress: function(isNew) {
        const newForm = document.getElementById('billing-new-address-form');
        if (isNew) {
            this.resetSelectedAddress();
            if (newForm) newForm.style.display = '';
        } else {
            if (newForm) newForm.style.display = 'none';
        }
    },

    resetSelectedAddress: function() {
        const selectElement = document.getElementById('billing-address-select');
        if (selectElement) {
            selectElement.value = '';
        }
    },

    fillForm: function(transport) {
        let elementValues = {};
        if (transport) {
            elementValues = _opcParseResponse(transport);
        }
        if (!transport && !Object.keys(elementValues).length) {
            this.resetSelectedAddress();
        }
        const arrElements = _opcFormElements(this.form);
        for (let i = 0; i < arrElements.length; i++) {
            if (arrElements[i].id) {
                const fieldName = arrElements[i].id.replace(/^billing:/, '');
                arrElements[i].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm) {
                    billingForm.elementChildLoad(arrElements[i]);
                }
            }
        }
    },

    setUseForShipping: function(flag) {
        document.getElementById('shipping:same_as_billing').checked = flag;
    },

    save: function() {
        if (checkout.loadWaiting != false) return;

        const validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('billing');

            _opcAjax(
                this.saveUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: _opcSerializeForm(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function(transport) {
        checkout.setLoadWaiting(false);
        _opcFireEvent(document.body, 'billing-request:completed', { transport: transport });
    },

    /**
     This method receives the AJAX response on success.
     There are 3 options: error, redirect or html with shipping options.
     */
    nextStep: function(transport) {
        const response = _opcParseResponse(transport);

        if (response.error) {
            if (typeof response.message === 'string') {
                alert(_opcStripTags(response.message));
            } else {
                if (window.billingRegionUpdater) {
                    billingRegionUpdater.update();
                }

                const msg = response.message;
                if (Array.isArray(msg)) {
                    alert(msg.join("\n"));
                }
                alert(_opcStripTags(String(msg)));
            }

            return false;
        }

        checkout.setStepResponse(response);
        if (payment) {
            payment.initWhatIsCvvListeners();
        }
    }
};
Varien.classCompat(Billing);

// =====================================================================
// Shipping
// =====================================================================

var Shipping = function(form, addressUrl, saveUrl, methodsUrl) {
    this.initialize(form, addressUrl, saveUrl, methodsUrl);
};
Shipping.prototype = {
    initialize: function(form, addressUrl, saveUrl, methodsUrl) {
        this.form = form;
        const formEl = document.getElementById(this.form);
        if (formEl) {
            formEl.addEventListener('submit', function(event) { this.save(); event.preventDefault(); event.stopPropagation(); }.bind(this));
            const countrySelect = formEl.querySelector('#shipping\\:country_id');
            if (countrySelect) {
                countrySelect.addEventListener('change', function() { if (window.shipping) shipping.setSameAsBilling(false); });
            }
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.methodsUrl = methodsUrl;
        this.onAddressLoad = this.fillForm.bind(this);
        this.onSave = this.nextStep.bind(this);
        this.onComplete = this.resetLoadWaiting.bind(this);
    },

    setAddress: function(addressId) {
        if (addressId) {
            _opcAjax(
                this.addressUrl + addressId,
                { method: 'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout) }
            );
        }
        else {
            this.fillForm(false);
        }
    },

    newAddress: function(isNew) {
        const newForm = document.getElementById('shipping-new-address-form');
        if (isNew) {
            this.resetSelectedAddress();
            if (newForm) newForm.style.display = '';
        } else {
            if (newForm) newForm.style.display = 'none';
        }
        shipping.setSameAsBilling(false);
    },

    resetSelectedAddress: function() {
        const selectElement = document.getElementById('shipping-address-select');
        if (selectElement) {
            selectElement.value = '';
        }
    },

    fillForm: function(transport) {
        let elementValues = {};
        if (transport) {
            elementValues = _opcParseResponse(transport);
        }
        if (!transport && !Object.keys(elementValues).length) {
            this.resetSelectedAddress();
        }
        const arrElements = _opcFormElements(this.form);
        for (let i = 0; i < arrElements.length; i++) {
            if (arrElements[i].id) {
                const fieldName = arrElements[i].id.replace(/^shipping:/, '');
                arrElements[i].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm) {
                    shippingForm.elementChildLoad(arrElements[i]);
                }
            }
        }
    },

    setSameAsBilling: function(flag) {
        document.getElementById('shipping:same_as_billing').checked = flag;
        if (flag) {
            this.syncWithBilling();
        }
    },

    syncWithBilling: function() {
        const billingSelect = document.getElementById('billing-address-select');
        if (billingSelect) this.newAddress(!billingSelect.value);
        document.getElementById('shipping:same_as_billing').checked = true;
        if (!billingSelect || !billingSelect.value) {
            const arrElements = _opcFormElements(this.form);
            for (let i = 0; i < arrElements.length; i++) {
                if (arrElements[i].id) {
                    const sourceField = document.getElementById(arrElements[i].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField) {
                        arrElements[i].value = sourceField.value;
                    }
                }
            }
            shippingRegionUpdater.update();
            document.getElementById('shipping:region_id').value = document.getElementById('billing:region_id').value;
            document.getElementById('shipping:region').value = document.getElementById('billing:region').value;
        } else {
            document.getElementById('shipping-address-select').value = billingSelect.value;
        }
    },

    setRegionValue: function() {
        document.getElementById('shipping:region').value = document.getElementById('billing:region').value;
    },

    save: function() {
        if (checkout.loadWaiting != false) return;
        const validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('shipping');
            _opcAjax(
                this.saveUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: _opcSerializeForm(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function() {
        checkout.setLoadWaiting(false);
    },

    nextStep: function(transport) {
        const response = _opcParseResponse(transport);

        if (response.error) {
            if (typeof response.message === 'string') {
                alert(_opcStripTags(response.message));
            } else {
                if (window.shippingRegionUpdater) {
                    shippingRegionUpdater.update();
                }
                const msg = response.message;
                if (Array.isArray(msg)) {
                    alert(msg.join("\n"));
                }
                alert(_opcStripTags(String(msg)));
            }

            return false;
        }

        checkout.setStepResponse(response);
    }
};
Varien.classCompat(Shipping);

// =====================================================================
// ShippingMethod
// =====================================================================

var ShippingMethod = function(form, saveUrl) {
    this.initialize(form, saveUrl);
};
ShippingMethod.prototype = {
    initialize: function(form, saveUrl) {
        this.form = form;
        const formEl = document.getElementById(this.form);
        if (formEl) {
            formEl.addEventListener('submit', function(event) { this.save(); event.preventDefault(); event.stopPropagation(); }.bind(this));
        }
        this.saveUrl = saveUrl;
        this.validator = new Validation(this.form);
        this.onSave = this.nextStep.bind(this);
        this.onComplete = this.resetLoadWaiting.bind(this);
    },

    validate: function() {
        const methods = document.getElementsByName('shipping_method');
        if (methods.length == 0) {
            alert(_opcStripTags(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.')));
            return false;
        }

        if (!this.validator.validate()) {
            return false;
        }

        for (let i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        alert(_opcStripTags(Translator.translate('Please specify shipping method.')));
        return false;
    },

    save: function() {
        if (checkout.loadWaiting != false) return;
        if (this.validate()) {
            checkout.setLoadWaiting('shipping-method');
            _opcAjax(
                this.saveUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: _opcSerializeForm(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function(transport) {
        checkout.setLoadWaiting(false);
    },

    nextStep: function(transport) {
        const response = _opcParseResponse(transport);

        if (response.error) {
            alert(_opcStripTags(String(response.message)));
            return false;
        }

        if (response.update_section) {
            const updateEl = document.getElementById('checkout-' + response.update_section.name + '-load');
            if (updateEl) {
                updateEl.innerHTML = response.update_section.html;
                Varien.evalScripts(updateEl);
            }
        }

        payment.initWhatIsCvvListeners();

        if (response.goto_section) {
            checkout.gotoSection(response.goto_section, true);
            checkout.reloadProgressBlock();
            return;
        }

        if (response.payment_methods_html) {
            const paymentLoad = document.getElementById('checkout-payment-method-load');
            if (paymentLoad) {
                paymentLoad.innerHTML = response.payment_methods_html;
                Varien.evalScripts(paymentLoad);
            }
        }

        checkout.setShippingMethod();
    }
};
Varien.classCompat(ShippingMethod);

// =====================================================================
// Payment
// =====================================================================

var Payment = function(form, saveUrl) {
    this.initialize(form, saveUrl);
};
Payment.prototype = {
    beforeInitFunc: {},
    afterInitFunc: {},
    beforeValidateFunc: {},
    afterValidateFunc: {},

    initialize: function(form, saveUrl) {
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bind(this);
        this.onComplete = this.resetLoadWaiting.bind(this);
    },

    addBeforeInitFunction: function(code, func) {
        this.beforeInitFunc[code] = func;
    },

    beforeInit: function() {
        const self = this;
        _opcEachEntry(this.beforeInitFunc, function(init) {
            (init.value)();
        });
    },

    init: function() {
        this.beforeInit();
        const elements = _opcFormElements(this.form);
        const formEl = document.getElementById(this.form);
        if (formEl) {
            formEl.addEventListener('submit', function(event) { this.save(); event.preventDefault(); event.stopPropagation(); }.bind(this));
        }
        let method = null;
        for (let i = 0; i < elements.length; i++) {
            if (elements[i].name == 'payment[method]' || elements[i].name == 'form_key') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete', 'off');
        }
        if (method) this.switchMethod(method);
        this.afterInit();
    },

    addAfterInitFunction: function(code, func) {
        this.afterInitFunc[code] = func;
    },

    afterInit: function() {
        _opcEachEntry(this.afterInitFunc, function(init) {
            (init.value)();
        });
    },

    switchMethod: function(method) {
        if (this.currentMethod && document.getElementById('payment_form_' + this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            _opcFireEvent(document.getElementById('payment_form_' + this.currentMethod), 'payment-method:switched-off', { method_code: this.currentMethod });
        }
        if (document.getElementById('payment_form_' + method)) {
            this.changeVisible(method, false);
            _opcFireEvent(document.getElementById('payment_form_' + method), 'payment-method:switched', { method_code: method });
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            _opcFireEvent(document.body, 'payment-method:switched', { method_code: method });
        }
        if (method == 'free' && quoteBaseGrandTotal > 0.0001
            && !((document.getElementById('use_reward_points') && document.getElementById('use_reward_points').checked) || (document.getElementById('use_customer_balance') && document.getElementById('use_customer_balance').checked))
        ) {
            const pMethod = document.getElementById('p_method_' + method);
            if (pMethod) {
                pMethod.checked = false;
                const dtMethod = document.getElementById('dt_method_' + method);
                if (dtMethod) {
                    dtMethod.style.display = 'none';
                }
                const ddMethod = document.getElementById('dd_method_' + method);
                if (ddMethod) {
                    ddMethod.style.display = 'none';
                }
            }
            method == '';
        }
        if (method) {
            this.lastUsedMethod = method;
        }
        this.currentMethod = method;
    },

    changeVisible: function(method, mode) {
        const block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].forEach(function(elId) {
            const element = document.getElementById(elId);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                Array.from(element.querySelectorAll('input, select, textarea, button')).forEach(function(field) {
                    field.disabled = mode;
                });
            }
        });
    },

    addBeforeValidateFunction: function(code, func) {
        this.beforeValidateFunc[code] = func;
    },

    beforeValidate: function() {
        let validateResult = true;
        let hasValidation = false;
        _opcEachEntry(this.beforeValidateFunc, function(validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        });
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    validate: function() {
        let result = this.beforeValidate();
        if (result) {
            return true;
        }
        const methods = document.getElementsByName('payment[method]');
        if (methods.length == 0) {
            alert(_opcStripTags(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.')));
            return false;
        }
        for (let i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        alert(_opcStripTags(Translator.translate('Please specify payment method.')));
        return false;
    },

    addAfterValidateFunction: function(code, func) {
        this.afterValidateFunc[code] = func;
    },

    afterValidate: function() {
        let validateResult = true;
        let hasValidation = false;
        _opcEachEntry(this.afterValidateFunc, function(validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        });
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    save: function() {
        if (checkout.loadWaiting != false) return;
        const validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            checkout.setLoadWaiting('payment');
            _opcAjax(
                this.saveUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: _opcSerializeForm(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function() {
        checkout.setLoadWaiting(false);
    },

    nextStep: function(transport) {
        const response = _opcParseResponse(transport);

        /*
         * if there is an error in payment, need to show error message
         */
        if (response.error) {
            if (response.fields) {
                const fields = response.fields.split(',');
                for (let i = 0; i < fields.length; i++) {
                    const field = document.getElementById(fields[i]);
                    if (field) {
                        Validation.ajaxError(field, response.error);
                    }
                }
                return;
            }
            if (typeof response.message === 'string') {
                alert(_opcStripTags(response.message));
            } else {
                alert(_opcStripTags(String(response.error)));
            }
            return;
        }

        checkout.setStepResponse(response);
    },

    initWhatIsCvvListeners: function() {
        Array.from(document.querySelectorAll('.cvv-what-is-this')).forEach(function(element) {
            element.addEventListener('click', toggleToolTip);
        });
    }
};
Varien.classCompat(Payment);

// =====================================================================
// Review
// =====================================================================

var Review = function(saveUrl, successUrl, agreementsForm) {
    this.initialize(saveUrl, successUrl, agreementsForm);
};
Review.prototype = {
    initialize: function(saveUrl, successUrl, agreementsForm) {
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.agreementsForm = agreementsForm;
        this.onSave = this.nextStep.bind(this);
        this.onComplete = this.resetLoadWaiting.bind(this);
    },

    save: function() {
        if (checkout.loadWaiting != false) return;
        checkout.setLoadWaiting('review');
        let params = _opcSerializeForm(payment.form);
        if (this.agreementsForm) {
            params += '&' + _opcSerializeForm(this.agreementsForm);
        }
        params.save = true;
        _opcAjax(
            this.saveUrl,
            {
                method: 'post',
                parameters: params,
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout)
            }
        );
    },

    resetLoadWaiting: function(transport) {
        checkout.setLoadWaiting(false, this.isSuccess);
    },

    nextStep: function(transport) {
        if (transport) {
            const response = _opcParseResponse(transport);

            if (response.redirect) {
                this.isSuccess = true;
                location.href = encodeURI(response.redirect);
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                location.href = encodeURI(this.successUrl);
            }
            else {
                let msg = response.error_messages;
                if (Array.isArray(msg)) {
                    msg = _opcStripTags(msg.join("\n"));
                }
                if (msg) {
                    alert(msg);
                }
            }

            if (response.update_section) {
                const updateEl = document.getElementById('checkout-' + response.update_section.name + '-load');
                if (updateEl) {
                    updateEl.innerHTML = response.update_section.html;
                    Varien.evalScripts(updateEl);
                }
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section, true);
            }
        }
    },

    isSuccess: false
};
Varien.classCompat(Review);
