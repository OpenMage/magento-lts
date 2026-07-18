/**
 * @copyright  For copyright and license information, read the README.md file.
 * @link       /README.md
 * @license    Academic Free License (AFL 3.0)
 * @package     base_default
 */

/**
 * Helper: evaluate <script> elements inside a container.
 */
function _reviewEvalScripts(container) {
    if (!container) return;
    var scripts = container.querySelectorAll('script');
    scripts.forEach(function(old) {
        var s = document.createElement('script');
        if (old.src) {
            s.src = old.src;
        } else {
            s.textContent = old.textContent;
        }
        old.parentNode.replaceChild(s, old);
    });
}

/**
 * Helper: enable an array/NodeList of form elements (replaces .invoke('enable')).
 */
function _reviewEnableElements(elements) {
    Array.from(elements).forEach(function(el) { el.disabled = false; });
}

/**
 * Helper: disable an array/NodeList of form elements (replaces .invoke('disable')).
 */
function _reviewDisableElements(elements) {
    Array.from(elements).forEach(function(el) { el.disabled = true; });
}

/**
 * Helper: fetch HTML and put it into a container, optionally evaluating scripts.
 */
function _reviewAjaxUpdater(container, url, options) {
    var el = typeof container === 'string' ? document.getElementById(container) : container;
    if (!el) return;
    var sep = url.indexOf('?') === -1 ? '?' : '&';
    if (options.parameters) {
        if (typeof options.parameters === 'string') {
            url += sep + options.parameters;
        } else {
            // Repeat keys for array values (billing[street][], multi-selects) —
            // URLSearchParams(object) would flatten them to "a,b"
            var usp = new URLSearchParams();
            Object.keys(options.parameters).forEach(function(key) {
                var value = options.parameters[key];
                if (Array.isArray(value)) {
                    value.forEach(function(item) { usp.append(key, item); });
                } else {
                    usp.append(key, value);
                }
            });
            url += sep + usp.toString();
        }
    }
    fetch(url).then(function(resp) {
        return resp.text().then(function(text) {
            return { ok: resp.ok, status: resp.status, text: text };
        });
    }).then(function(result) {
        var response = { responseText: result.text, status: result.status };
        // Only treat 2xx as success: inserting an error page into the review
        // area and reporting success would let the order proceed on a failed save
        if (result.ok) {
            el.innerHTML = result.text;
            if (options.evalScripts !== false) {
                _reviewEvalScripts(el);
            }
            if (options.onSuccess) options.onSuccess(response);
        } else if (options.onFailure) {
            options.onFailure(response);
        }
        if (options.onComplete) options.onComplete(response);
    }).catch(function() {
        if (options.onFailure) options.onFailure({ responseText: '', status: 0 });
        if (options.onComplete) options.onComplete({ responseText: '', status: 0 });
    });
}

/**
 * Helper: serialize form to a plain object (replaces Form.serialize(true)).
 */
function _reviewSerializeForm(form) {
    var data = {};
    // Accumulate repeated names (billing[street][]) into arrays instead of
    // letting later fields overwrite earlier ones
    function append(name, value) {
        if (data.hasOwnProperty(name)) {
            if (!Array.isArray(data[name])) data[name] = [data[name]];
            data[name].push(value);
        } else {
            data[name] = value;
        }
    }
    Array.from(form.elements).forEach(function(el) {
        if (!el.name || el.disabled) return;
        if (el.type === 'checkbox') {
            data[el.name] = el.checked ? el.value : 0;
        } else if (el.type === 'radio') {
            if (el.checked) data[el.name] = el.value;
        } else if (el.type === 'select-multiple') {
            // .value on a multi-select only yields the first selected option
            Array.from(el.options).forEach(function(opt) {
                if (opt.selected) append(el.name, opt.value);
            });
        } else if (el.type !== 'submit' && el.type !== 'button') {
            append(el.name, el.value);
        }
    });
    return data;
}

/**
 * Controller of order review form that may select shipping method
 */
OrderReviewController = function(orderForm, orderFormSubmit, shippingSelect, shippingSubmitForm, shippingResultId, shippingSubmit)
{
    if (!orderForm) {
        return;
    }
    this.form = orderForm;
    if (orderFormSubmit) {
        this.formSubmit = orderFormSubmit;
        orderFormSubmit.addEventListener('click', this._submitOrder.bind(this));
    }

    if (shippingSubmitForm) {
        this.reloadByShippingSelect = true;
        if (shippingSubmitForm && shippingSelect) {
            this.shippingSelect = shippingSelect;
            shippingSelect.addEventListener('change', this._submitShipping.bind(this, shippingSubmitForm.action, shippingResultId));
            this._updateOrderSubmit(false);
        } else {
            this._canSubmitOrder = true;
        }
    } else {
        Array.from(this.form.elements).forEach(this._bindElementChange.bind(this));

        if (shippingSelect && document.getElementById(shippingSelect)) {
            this.shippingSelect = document.getElementById(shippingSelect).id;
            this.shippingMethodsContainer = document.getElementById(this.shippingSelect).parentElement;
        } else {
            this.shippingSelect = shippingSelect;
        }
        this._updateOrderSubmit(false);
    }
};

OrderReviewController.prototype = {
    _canSubmitOrder : false,
    _pleaseWait : false,
    shippingSelect : false,
    reloadByShippingSelect : false,
    _copyElement : false,
    onSubmitShippingSuccess : false,
    shippingMethodsUpdateUrl : false,
    _updateShippingMethods: false,
    _ubpdateOrderButton : false,
    shippingMethodsContainer: false,
    _submitUpdateOrderUrl : false,
    _itemsGrid : false,

    addPleaseWait : function(element)
    {
        if (element) {
            this._pleaseWait = element;
        }
    },

    _submitShipping : function(url, resultId, event)
    {
        if (this.shippingSelect && url && resultId) {
            this._updateOrderSubmit(true);
            if (this._pleaseWait) {
                this._pleaseWait.style.display = '';
            }
            if ('' != this.shippingSelect.value) {
                var self = this;
                _reviewAjaxUpdater(resultId, url, {
                    parameters: {isAjax:true, shipping_method:this.shippingSelect.value},
                    evalScripts: true,
                    onComplete: function() {
                        if (self._pleaseWait) {
                            self._pleaseWait.style.display = 'none';
                        }
                    },
                    onSuccess: function() {
                        self._onSubmitShippingSuccess();
                    }
                });
            }
        }
    },

    setUpdateButton : function(element, url, resultId)
    {
        if (element) {
            this._ubpdateOrderButton = element;
            this._submitUpdateOrderUrl = url;
            this._itemsGrid = resultId;
            element.addEventListener('click', this._submitUpdateOrder.bind(this, url, resultId));
            if(this.shippingSelect) {
                this._updateShipping();
            }
            this._updateOrderSubmit(!this._validateForm());
            this.formValidator.reset();
            this._clearValidation('');
        }
    },

    setCopyElement : function(element)
    {
        if (element) {
            this._copyElement = element;
            element.addEventListener('click', this._copyShippingToBilling.bind(this));
            this._copyShippingToBilling();
        }
    },

    setShippingAddressContainer: function(element)
    {
        if (element) {
            var inputs = element.querySelectorAll('input, select, textarea');
            var self = this;
            Array.from(inputs).forEach(function(input) {
                if (input.type.toLowerCase() == 'radio' || input.type.toLowerCase() == 'checkbox') {
                    input.addEventListener('click', self._onShippingChange.bind(self));
                } else {
                    input.addEventListener('change', self._onShippingChange.bind(self));
                }
            });
        }
    },

    setShippingMethodContainer: function(element)
    {
        if (element) {
            this.shippingMethodsContainer = element;
        }
    },

    _copyElementValue: function(el)
    {
        var newId = el.id.replace('shipping:','billing:');
        var newEl = document.getElementById(newId);
        if (newId && newEl && newEl.type != 'hidden') {
            newEl.value = el.value;
            newEl.setAttribute('readOnly', 'readonly');
            newEl.classList.add('local-validation');
            newEl.style.opacity = .5;
            newEl.disabled = true;
        }
    },

    _copyShippingToBilling : function (event)
    {
        if (!this._copyElement) {
            return;
        }
        if (this._copyElement.checked) {
            this._copyElementValue(document.getElementById('shipping:country_id'));
            billingRegionUpdater.update();
            document.querySelectorAll('[id^="shipping:"]').forEach(this._copyElementValue.bind(this));
            this._clearValidation('billing');
        } else {
            _reviewEnableElements(document.querySelectorAll('[id^="billing:"]'));
            document.querySelectorAll('[id^="billing:"]').forEach(function(el){el.removeAttribute("readOnly");});
            document.querySelectorAll('[id^="billing:"]').forEach(function(el){el.classList.remove('local-validation');});
            document.querySelectorAll('[id^="billing:"]').forEach(function(el){el.style.opacity = 1;});
        }
        if (event) {
            this._updateOrderSubmit(true);
        }
    },

    _submitUpdateOrder : function(url, resultId, event)
    {
        this._copyShippingToBilling();
        if (url && resultId && this._validateForm()) {
            if (this._copyElement && this._copyElement.checked) {
                this._clearValidation('billing');
            }
            this._updateOrderSubmit(true);
            if (this._pleaseWait) {
                this._pleaseWait.style.display = '';
            }
            this._toggleButton(this._ubpdateOrderButton, true);

            _reviewEnableElements(document.querySelectorAll('[id^="billing:"]'));
            var formData = _reviewSerializeForm(this.form);
            if (this._copyElement && this._copyElement.checked) {
                _reviewDisableElements(document.querySelectorAll('[id^="billing:"]'));
                this._copyElement.disabled = false;
            }
            formData.isAjax = true;
            var self = this;
            _reviewAjaxUpdater(resultId, url, {
                parameters: formData,
                evalScripts: true,
                onComplete: function() {
                    if (self._pleaseWait && !self._updateShippingMethods) {
                        self._pleaseWait.style.display = 'none';
                    }
                    self._toggleButton(self._ubpdateOrderButton, false);
                },
                onSuccess: function() {
                    self._updateShippingMethodsElement();
                },
                onFailure: function() {
                    // don't leave the please-wait spinner up when the save failed
                    if (self._pleaseWait) {
                        self._pleaseWait.style.display = 'none';
                    }
                }
            });
        } else {
            if (this._copyElement && this._copyElement.checked) {
                this._clearValidation('billing');
            }
        }
    },

    _updateShippingMethodsElement : function (){
        var self = this;
        if (this._updateShippingMethods) {
            var containerEl = document.getElementById(this.shippingMethodsContainer);
            if (containerEl && containerEl.parentElement) {
                _reviewAjaxUpdater(containerEl.parentElement, this.shippingMethodsUpdateUrl, {
                    evalScripts: false,
                    onComplete: function() {
                        self._updateShipping();
                        self._onSubmitShippingSuccess();
                    }
                });
            }
        } else {
            this._onSubmitShippingSuccess();
        }
    },

    _updateShipping : function () {
        var shipSelect = document.getElementById(this.shippingSelect);
        if (shipSelect) {
            var newSelect = shipSelect.cloneNode(true);
            shipSelect.parentNode.replaceChild(newSelect, shipSelect);
            newSelect.disabled = false;

            this._bindElementChange(newSelect);
            var self = this;
            newSelect.addEventListener('change', function(e) {
                self._submitUpdateOrder(self._submitUpdateOrderUrl, self._itemsGrid, e);
            });

            var updateEl = document.getElementById(this.shippingSelect + '_update');
            if (updateEl) updateEl.style.display = 'none';
            newSelect.style.display = '';
        }
        this._updateShippingMethods = false;
        if (this._pleaseWait) {
            this._pleaseWait.style.display = 'none';
        }
    },

    _validateForm : function()
    {
        if (!this.form) {
            return false;
        }
        if (!this.formValidator) {
            this.formValidator = new Validation(this.form);
        }

        return this.formValidator.validate();
    },

    _onShippingChange : function(event){
        var element = event.target;
        var shipSelect = document.getElementById(this.shippingSelect);
        if (element != shipSelect && !(shipSelect && shipSelect.disabled)) {
            if (shipSelect) {
                shipSelect.disabled = true;
                shipSelect.style.display = 'none';
                var adviceEl = document.getElementById('advice-required-entry-' + this.shippingSelect);
                if (adviceEl) {
                    adviceEl.style.display = 'none';
                }
            }
            if (this.shippingMethodsContainer) {
                var containerEl = typeof this.shippingMethodsContainer === 'string'
                    ? document.getElementById(this.shippingMethodsContainer)
                    : this.shippingMethodsContainer;
                if (containerEl) containerEl.style.display = 'none';
            }

            if (this.shippingSelect) {
                var updateEl = document.getElementById(this.shippingSelect + '_update');
                if (updateEl) updateEl.style.display = '';
            }
            this._updateShippingMethods = true;
        }
    },

    _bindElementChange : function(input){
        input.addEventListener('change', this._onElementChange.bind(this));
    },

    _onElementChange : function(){
        this._updateOrderSubmit(true);
    },

    _clearValidation : function(idprefix)
    {
        var prefix = '';
        if (idprefix) {
            prefix = '[id*="' + idprefix + ':"]';
            document.querySelectorAll(prefix).forEach(function(el){
                var up = el.closest('.validation-failed, .validation-passed, .validation-error');
                if (up) {
                    up.classList.remove('validation-failed', 'validation-passed', 'validation-error');
                }
            });
        } else {
            this.formValidator.reset();
        }
        document.querySelectorAll('.validation-advice' + prefix).forEach(function(el){ el.remove(); });
        document.querySelectorAll('.validation-failed' + prefix).forEach(function(el){ el.classList.remove('validation-failed'); });
        document.querySelectorAll('.validation-passed' + prefix).forEach(function(el){ el.classList.remove('validation-passed'); });
        document.querySelectorAll('.validation-error' + prefix).forEach(function(el){ el.classList.remove('validation-error'); });
    },

    _submitOrder : function()
    {
        if (this._canSubmitOrder && (this.reloadByShippingSelect || this._validateForm())) {
            this.form.submit();
            this._updateOrderSubmit(true);
            if (this._ubpdateOrderButton) {
                this._ubpdateOrderButton.classList.add('no-checkout');
                this._ubpdateOrderButton.style.opacity = .5;
            }
            if (this._pleaseWait) {
                this._pleaseWait.style.display = '';
            }
            return;
        }
        this._updateOrderSubmit(true);
    },

    _onSubmitShippingSuccess : function()
    {
        this._updateOrderSubmit(false);
        if (this.onSubmitShippingSuccess) {
            this.onSubmitShippingSuccess();
        }
    },

    _updateOrderSubmit : function(shouldDisable)
    {
        var isDisabled = shouldDisable || (
            this.reloadByShippingSelect && (!this.shippingSelect || '' == this.shippingSelect.value)
        );
        this._canSubmitOrder = !isDisabled;
        if (this.formSubmit) {
            this._toggleButton(this.formSubmit, isDisabled);
        }
    },

    _toggleButton : function(button, disable)
    {
        button.disabled = disable;
        button.classList.remove('no-checkout');
        button.style.opacity = 1;
        if (disable) {
            button.classList.add('no-checkout');
            button.style.opacity = .5;
        }
    }
};
