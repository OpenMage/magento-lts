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
 * @copyright   Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Helper: get value from a form element (replaces $F / Form.Element.getValue / el.getValue)
 */
function _getFieldValue(el) {
    if (!el) return '';
    if (el.tagName === 'SELECT') {
        if (el.multiple) {
            var vals = [];
            for (var i = 0; i < el.options.length; i++) {
                if (el.options[i].selected) vals.push(el.options[i].value);
            }
            return vals;
        }
        return el.value;
    }
    if (el.tagName === 'INPUT' && (el.type === 'checkbox' || el.type === 'radio')) {
        return el.checked ? el.value : '';
    }
    return el.value;
}

/**
 * Helper: set value on a form element (replaces el.setValue from Prototype)
 */
function _setFieldValue(el, value) {
    if (!el) return;
    if (el.tagName === 'SELECT' && el.multiple) {
        var vals = Array.isArray(value) ? value : [value];
        for (var i = 0; i < el.options.length; i++) {
            el.options[i].selected = vals.indexOf(el.options[i].value) !== -1;
        }
    } else if (el.tagName === 'INPUT' && (el.type === 'checkbox' || el.type === 'radio')) {
        el.checked = !!value;
    } else {
        el.value = (value != null) ? value : '';
    }
}

/**
 * Helper: serialize form elements to an object (replaces Form.serializeElements)
 */
function _serializeElements(fields) {
    var data = {};
    for (var i = 0; i < fields.length; i++) {
        var el = fields[i];
        if (el.disabled || !el.name) continue;
        var val = _getFieldValue(el);
        data[el.name] = val;
    }
    return data;
}

/**
 * Helper: wrap a function so original can be called via proceed()
 * Replaces fn.wrap(wrapper) pattern from Prototype
 */
function _wrapFunction(original, wrapper) {
    return function() {
        var args = [original.bind(this)].concat(Array.prototype.slice.call(arguments));
        return wrapper.apply(this, args);
    };
}

function AdminOrder(data) {
    if (!data) data = {};
    this.loadBaseUrl    = false;
    this.customerId     = data.customer_id ? data.customer_id : false;
    this.isGuest        = data.is_guest ? true : false;
    this.storeId        = data.store_id ? data.store_id : false;
    this.currencyId     = false;
    this.currencySymbol = data.currency_symbol ? data.currency_symbol : '';
    this.addresses      = data.addresses ? data.addresses : {};
    this.shippingAsBilling = data.shippingAsBilling ? data.shippingAsBilling : false;
    this.gridProducts   = {};
    this.gridProductsGift = {};
    this.billingAddressContainer = '';
    this.shippingAddressContainer= '';
    this.isShippingMethodReseted = data.shipping_method_reseted ? data.shipping_method_reseted : false;
    this.overlayData = {};
    this.giftMessageDataChanged = false;
    this.productConfigureAddFields = {};
    this.productPriceBase = {};
    this.collectElementsValue = true;
    window.addEventListener('load', (function(){
        this.dataArea = new OrderFormArea('data', document.getElementById(this.getAreaId('data')), this);
        this.itemsArea = Object.assign(new OrderFormArea('items', document.getElementById(this.getAreaId('items')), this), {
            addControlButton: function(button){
                var controlButtonArea = this.node.querySelector('.form-buttons');
                if (typeof controlButtonArea != 'undefined' && controlButtonArea) {
                    var buttons = Array.from(controlButtonArea.children);
                    for (var i = 0; i < buttons.length; i++) {
                        if (buttons[i].innerHTML.indexOf(button.label) !== -1) {
                            return ;
                        }
                    }
                    button.insertIn(controlButtonArea, 'top');
                }
            }
        });

        var searchButton = new ControlButton(Translator.translate('Add Products')),
            searchAreaId = this.getAreaId('search');
        searchButton.onClick = function() {
            document.getElementById(searchAreaId).style.display = '';
            var el = this;
            window.setTimeout(function () {
                el.remove();
            }, 10);
        };

        this.dataArea.onLoad = _wrapFunction(this.dataArea.onLoad, function(proceed) {
            proceed();
            this._parent.itemsArea.setNode(document.getElementById(this._parent.getAreaId('items')));
            this._parent.itemsArea.onLoad();
        }.bind(this.dataArea));

        this.itemsArea.onLoad = _wrapFunction(this.itemsArea.onLoad, function(proceed) {
            proceed();
            var searchEl = document.getElementById(searchAreaId);
            if (searchEl && searchEl.style.display === 'none') {
                this.addControlButton(searchButton);
            }
        }.bind(this.itemsArea));
        this.areasLoaded();
        this.itemsArea.onLoad();
    }).bind(this));
}

AdminOrder.prototype = {
    areasLoaded: function(){
    },

    itemsLoaded: function(){
    },

    dataLoaded: function(){
        this.dataShow();
    },

    setLoadBaseUrl : function(url){
        this.loadBaseUrl = url;
    },

    setAddresses : function(addresses){
        this.addresses = addresses;
    },

    setCustomerIsGuest : function(){
        this.isGuest = true;
        this.setCustomerId(false);
    },

    setCustomerId : function(id){
        this.customerId = id;
        this.loadArea('header', true);
        document.getElementById(this.getAreaId('header')).callback = 'setCustomerAfter';
        document.getElementById('back_order_top_button').style.display = 'none';
        document.getElementById('reset_order_top_button').style.display = '';
    },

    setCustomerAfter : function () {
        this.customerSelectorHide();
        if (this.storeId) {
            document.getElementById(this.getAreaId('data')).callback = 'dataLoaded';
            this.loadArea(['data'], true);
        }
        else {
            this.storeSelectorShow();
        }
    },

    setStoreId : function(id){
        this.storeId = id;
        this.storeSelectorHide();
        this.sidebarShow();
        //this.loadArea(['header', 'sidebar','data'], true);
        this.dataShow();
        this.loadArea(['header', 'data'], true);
    },

    setCurrencyId : function(id){
        this.currencyId = id;
        //this.loadArea(['sidebar', 'data'], true);
        this.loadArea(['data'], true);
    },

    setCurrencySymbol : function(symbol){
        this.currencySymbol = symbol;
    },

    selectAddress : function(el, container){
        id = el.value;
        if (id.length == 0) {
            id = '0';
        }
        if(this.addresses[id]){
            this.fillAddressFields(container, this.addresses[id]);
        }
        else{
            this.fillAddressFields(container, {});
        }

        var data = this.serializeData(container);
        data[el.name] = id;
        if(this.isShippingField(container) && !this.isShippingMethodReseted){
            this.resetShippingMethod(data);
        }
        else{
            this.saveData(data);
        }
    },

    isShippingField : function(fieldId){
        if(this.shippingAsBilling){
            return fieldId.indexOf('billing') !== -1;
        }
        return fieldId.indexOf('shipping') !== -1;
    },

    isBillingField : function(fieldId){
        return fieldId.indexOf('billing') !== -1;
    },

    bindAddressFields : function(container) {
        var fields = document.getElementById(container).querySelectorAll('input, select, textarea');
        for(var i=0;i<fields.length;i++){
            fields[i].addEventListener('change', this.changeAddressField.bind(this));
        }
    },

    changeAddressField : function(event){
        var field = event.target;
        var re = /[^\[]*\[([^\]]*)_address\]\[([^\]]*)\](\[(\d)\])?/;
        var matchRes = field.name.match(re);

        if (!matchRes) {
            return;
        }

        var type = matchRes[1];
        var name = matchRes[2];
        var data;

        if(this.isBillingField(field.id)){
            data = this.serializeData(this.billingAddressContainer);
        }
        else{
            data = this.serializeData(this.shippingAddressContainer);
        }

        if( (type == 'billing' && this.shippingAsBilling && !this.isShippingMethodReseted)
            || (type == 'shipping' && !this.shippingAsBilling && !this.isShippingMethodReseted) ) {
            data['reset_shipping'] = true;
        }

        data['order['+type+'_address][customer_address_id]'] = document.getElementById('order-'+type+'_address_customer_address_id').value;

        if (type == 'billing' && this.shippingAsBilling) {
            this.copyDataFromBillingToShipping(field);
        }

        if (data['reset_shipping']) {
            this.resetShippingMethod(data);
        } else {
            this.saveData(data);
            if (!this.isShippingMethodReseted && (name == 'country_id' || name == 'customer_address_id')) {
                this.loadArea(['shipping_method', 'billing_method', 'totals', 'items'], true, data);
            }
        }
    },

    copyDataFromBillingToShipping : function(field) {
        var shippingId = field.id.replace('-billing_', '-shipping_');
        var inputField = document.getElementById(shippingId);
        if (inputField) {
            _setFieldValue(inputField, _getFieldValue(field));
            if (inputField.changeUpdater) {
                inputField.changeUpdater();
            }
            var selects = document.getElementById(this.shippingAddressContainer).querySelectorAll('select');
            selects.forEach(function(el){
                el.disabled = true;
            });
        }
    },

    fillAddressFields : function(container, data){
        var regionIdElem = false;
        var regionIdElemValue = false;

        var fields = document.getElementById(container).querySelectorAll('input, select, textarea');
        var re = /[^\[]*\[[^\]]*\]\[([^\]]*)\](\[(\d)\])?/;
        for(var i=0;i<fields.length;i++){
            // skip input type file @Security error code: 1000
            if (fields[i].tagName.toLowerCase() == 'input' && fields[i].type.toLowerCase() == 'file') {
                continue;
            }
            var matchRes = fields[i].name.match(re);
            if (matchRes === null) {
                continue;
            }
            var name = matchRes[1];
            var index = matchRes[3];

            if (index){
                // multiply line
                if (data[name]){
                    var values = data[name].split("\n");
                    fields[i].value = values[index] ? values[index] : '';
                } else {
                    fields[i].value = '';
                }
            } else if (fields[i].tagName.toLowerCase() == 'select' && fields[i].multiple) {
                // multiselect
                if (data[name]) {
                    values = [''];
                    if (typeof data[name] === 'string') {
                        values = data[name].split(',');
                    } else if (Array.isArray(data[name])) {
                        values = data[name];
                    }
                    _setFieldValue(fields[i], values);
                }
            } else {
                _setFieldValue(fields[i], data[name] ? data[name] : '');
            }

            if (fields[i].changeUpdater) fields[i].changeUpdater();
            if (name == 'region' && data['region_id'] > 0 && !data['region']){
                fields[i].value = data['region_id'];
            }
        }
    },

    disableShippingAddress : function(flag) {
        this.shippingAsBilling = flag;
        if (document.getElementById('order-shipping_address_customer_address_id')) {
            document.getElementById('order-shipping_address_customer_address_id').disabled = flag;
        }

        var shippingContainer = document.getElementById(this.shippingAddressContainer);
        if (shippingContainer) {
            var dataFields = shippingContainer.querySelectorAll('input, select, textarea');
            for (var i = 0; i < dataFields.length; i++) {
                dataFields[i].disabled = flag;
            }
            var buttons = shippingContainer.querySelectorAll('button');
            // Add corresponding class to buttons while disabling them
            for (i = 0; i < buttons.length; i++) {
                buttons[i].disabled = flag;
                if (flag) {
                    buttons[i].classList.add('disabled');
                } else {
                    buttons[i].classList.remove('disabled');
                }
            }
        }
    },

    turnOffShippingFields : function() {
        var shippingContainer = document.getElementById(this.shippingAddressContainer);
        if (shippingContainer) {
            var dataFields = shippingContainer.querySelectorAll('input, select, textarea, button');
            for (var i = 0; i < dataFields.length; i++) {
                dataFields[i].removeAttribute('name');
                dataFields[i].removeAttribute('id');
                dataFields[i].readOnly = true;
            }
        }
    },

    setShippingAsBilling : function(flag){
        this.disableShippingAddress(flag);
        if(flag){
            var data = this.serializeData(this.billingAddressContainer);
        }
        else{
            var data = this.serializeData(this.shippingAddressContainer);
        }
        data['shipping_as_billing'] = flag ? 1 : 0;
        data['reset_shipping'] = 1;
        this.loadArea(['shipping_method', 'billing_method', 'shipping_address', 'totals', 'giftmessage'], true, data);
    },

    resetShippingMethod : function(data){
        data['reset_shipping'] = 1;
        this.isShippingMethodReseted = true;
        this.loadArea(['shipping_method', 'billing_method', 'totals', 'giftmessage', 'items'], true, data);
    },

    loadShippingRates : function(){
        this.isShippingMethodReseted = false;
        this.loadArea(['shipping_method', 'totals'], true, {collect_shipping_rates: 1});
    },

    setShippingMethod : function(method){
        var data = {};
        data['order[shipping_method]'] = method;
        data['shipping_as_billing'] = this.shippingAsBilling ? 1 : 0;
        this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
    },

    switchPaymentMethod : function(method){
        this.setPaymentMethod(method);
        var data = {};
        data['order[payment_method]'] = method;
        this.loadArea(['card_validation'], true, data);
    },

    setPaymentMethod : function(method){
        if (this.paymentMethod && document.getElementById('payment_form_'+this.paymentMethod)) {
            var form = 'payment_form_'+this.paymentMethod;
            [form + '_before', form, form + '_after'].forEach(function(el) {
                var block = document.getElementById(el);
                if (block) {
                    block.style.display = 'none';
                    block.querySelectorAll('input, select, textarea').forEach(function(field) {
                        field.disabled = true;
                    });
                }
            });
        }

        if(!this.paymentMethod || method){
            document.getElementById('order-billing_method_form').querySelectorAll('input, select, textarea').forEach(function(elem){
                if(elem.type != 'radio') elem.disabled = true;
            });
        }

        if (document.getElementById('payment_form_'+method)){
            this.paymentMethod = method;
            var form = 'payment_form_'+method;
            [form + '_before', form, form + '_after'].forEach(function(el) {
                var block = document.getElementById(el);
                if (block) {
                   block.style.display = '';
                   block.querySelectorAll('input, select, textarea').forEach(function(field) {
                       field.disabled = false;
                       if (el.indexOf('_before') === -1 && el.indexOf('_after') === -1 && !field.bindChange) {
                           field.bindChange = true;
                           field.paymentContainer = form; /** @deprecated after 1.4.0.0-rc1 */
                           field.method = method;
                           field.addEventListener('change', this.changePaymentData.bind(this));
                        }
                    }.bind(this));
                }
            }.bind(this));
        }
    },

    changePaymentData : function(event){
        var elem = event.target;
        if(elem && elem.method){
            var data = this.getPaymentData(elem.method);
            if (data) {
                 this.loadArea(['card_validation'], true, data);
            } else {
                return;
            }
        }
    },

    getPaymentData : function(currentMethod){
        if (typeof(currentMethod) == 'undefined') {
            if (this.paymentMethod) {
                currentMethod = this.paymentMethod;
            } else {
                return false;
            }
        }
        var data = {};
        var fields = document.getElementById('payment_form_' + currentMethod).querySelectorAll('input, select');
        for(var i=0;i<fields.length;i++){
            data[fields[i].name] = _getFieldValue(fields[i]);
        }
        if ((typeof data['payment[cc_type]']) != 'undefined' && (!data['payment[cc_type]'] || !data['payment[cc_number]'])) {
            return false;
        }
        return data;
    },

    applyCoupon : function(code){
        this.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {'order[coupon][code]':code, reset_shipping: true});
    },

    addProduct : function(id){
        this.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {add_product:id, reset_shipping: true});
    },

    removeQuoteItem : function(id){
        this.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true,
            {remove_item:id, from:'quote',reset_shipping: true});
    },

    moveQuoteItem : function(id, to){
        this.loadArea(['sidebar_'+to, 'items', 'shipping_method', 'totals', 'billing_method'], this.getAreaId('items'),
            {move_item:id, to:to, reset_shipping: true});
    },

    productGridShow : function(buttonElement){
        this.productGridShowButton = buttonElement;
        buttonElement.style.display = 'none';
        this.showArea('search');
    },

    productGridRowInit : function(grid, row){
        var checkbox = row.querySelector('.checkbox');
        var inputs = Array.from(row.querySelectorAll('.input-text'));
        if (checkbox && inputs.length > 0) {
            checkbox.inputElements = inputs;
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                input.checkboxElement = checkbox;

                var product = this.gridProducts[checkbox.value];
                if (product) {
                    var defaultValue = product[input.name];
                    if (defaultValue) {
                        if (input.name == 'giftmessage') {
                            input.checked = true;
                        } else {
                            input.value = defaultValue;
                        }
                    }
                }

                input.disabled = !checkbox.checked || input.classList.contains('input-inactive');

                input.addEventListener('keyup', this.productGridRowInputChange.bind(this));
                input.addEventListener('change',this.productGridRowInputChange.bind(this));
            }
        }
    },

    productGridRowInputChange : function(event){
        var element = event.target;
        if (element && element.checkboxElement && element.checkboxElement.checked){
            if (element.name!='giftmessage' || element.checked) {
                this.gridProducts[element.checkboxElement.value][element.name] = element.value;
            } else if (element.name=='giftmessage' && this.gridProducts[element.checkboxElement.value][element.name]) {
                delete(this.gridProducts[element.checkboxElement.value][element.name]);
            }
        }
    },

    productGridRowClick : function(grid, event){
        var trElement = event.target.closest('tr');
        var qtyElement = trElement.querySelector('input[name="qty"]');
        var eventElement = event.target;
        var isInputCheckbox = eventElement.tagName == 'INPUT' && eventElement.type == 'checkbox';
        var isInputQty = eventElement.tagName == 'INPUT' && eventElement.name == 'qty';
        if (trElement && !isInputQty) {
            var checkbox = trElement.querySelector('input[type="checkbox"]');
            var confLink = trElement.querySelector('a');
            var priceColl = trElement.querySelector('.price');
            if (checkbox) {
                // processing non composite product
                if (confLink.getAttribute('disabled')) {
                    var checked = isInputCheckbox ? checkbox.checked : !checkbox.checked;
                    grid.setCheckboxChecked(checkbox, checked);
                // processing composite product
                } else if (isInputCheckbox && !checkbox.checked) {
                    grid.setCheckboxChecked(checkbox, false);
                // processing composite product
                } else if (!isInputCheckbox || (isInputCheckbox && checkbox.checked)) {
                    var listType = confLink.getAttribute('list_type');
                    var productId = confLink.getAttribute('product_id');
                    if (typeof this.productPriceBase[productId] == 'undefined') {
                        var priceBase = priceColl.innerHTML.match(/.*?([\d,]+\.?\d*)/);
                        if (!priceBase) {
                            this.productPriceBase[productId] = 0;
                        } else {
                            this.productPriceBase[productId] = parseFloat(priceBase[1].replace(/,/g,''));
                        }
                    }
                    productConfigure.setConfirmCallback(listType, function() {
                        // sync qty of popup and qty of grid
                        var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
                        if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                            qtyElement.value = confirmedCurrentQty.value;
                        }
                        // calc and set product price
                        var productPrice = parseFloat(this._calcProductPrice() + this.productPriceBase[productId]);
                        priceColl.innerHTML = this.currencySymbol + productPrice.toFixed(2);
                        // and set checkbox checked
                        grid.setCheckboxChecked(checkbox, true);
                    }.bind(this));
                    productConfigure.setCancelCallback(listType, function() {
                        var confirmedEl = document.getElementById(productConfigure.confirmedCurrentId);
                        if (!confirmedEl || !confirmedEl.innerHTML) {
                            grid.setCheckboxChecked(checkbox, false);
                        }
                    });
                    productConfigure.setShowWindowCallback(listType, function() {
                        // sync qty of grid and qty of popup
                        var formCurrentQty = productConfigure.getCurrentFormQtyElement();
                        if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                            formCurrentQty.value = qtyElement.value;
                        }
                    }.bind(this));
                    productConfigure.showItemConfiguration(listType, productId);
                }
            }
        }
    },

    /**
     * Calc product price through its options
     */
    _calcProductPrice: function () {
        var productPrice = 0;
        var getPriceFields = function (elms) {
            var productPrice = 0;
            var getPrice = function (elm) {
                var optQty = 1;
                if (elm.hasAttribute('qtyId')) {
                    var qtyEl = document.getElementById(elm.getAttribute('qtyId'));
                    if (!qtyEl || !qtyEl.value) {
                        return 0;
                    } else {
                        optQty = parseFloat(qtyEl.value);
                    }
                }
                if (elm.hasAttribute('price') && !elm.disabled) {
                    return parseFloat(elm.getAttribute('price')) * optQty;
                }
                return 0;
            };
            for(var i = 0; i < elms.length; i++) {
                if (elms[i].type == 'select-one' || elms[i].type == 'select-multiple') {
                    for(var ii = 0; ii < elms[i].options.length; ii++) {
                        if (elms[i].options[ii].selected) {
                            productPrice += getPrice(elms[i].options[ii]);
                        }
                    }
                }
                else if (((elms[i].type == 'checkbox' || elms[i].type == 'radio') && elms[i].checked)
                        || ((elms[i].type == 'file' || elms[i].type == 'text' || elms[i].type == 'textarea' || elms[i].type == 'hidden')
                            && _getFieldValue(elms[i]))
                ) {
                    productPrice += getPrice(elms[i]);
                }
            }
            return productPrice;
        }.bind(this);
        var confirmedEl = document.getElementById(productConfigure.confirmedCurrentId);
        productPrice += getPriceFields(confirmedEl.getElementsByTagName('input'));
        productPrice += getPriceFields(confirmedEl.getElementsByTagName('select'));
        productPrice += getPriceFields(confirmedEl.getElementsByTagName('textarea'));
        return productPrice;
    },

    productGridCheckboxCheck : function(grid, element, checked){
        if (checked) {
            if(element.inputElements) {
                this.gridProducts[element.value] = {};
                var product = this.gridProducts[element.value];
                for (var i = 0; i < element.inputElements.length; i++) {
                    var input = element.inputElements[i];
                    if (!input.classList.contains('input-inactive')) {
                        input.disabled = false;
                        if (input.name == 'qty' && !input.value) {
                            input.value = 1;
                        }
                    }

                    if (input.checked || input.name != 'giftmessage') {
                        product[input.name] = input.value;
                    } else if (product[input.name]) {
                        delete(product[input.name]);
                    }
                }
            }
        } else {
            if(element.inputElements){
                for(var i = 0; i < element.inputElements.length; i++) {
                    element.inputElements[i].disabled = true;
                }
            }
            delete this.gridProducts[element.value];
        }
        grid.reloadParams = {'products[]':Object.keys(this.gridProducts)};
    },

    /**
     * Submit configured products to quote
     */
    productGridAddSelected : function(){
        if(this.productGridShowButton) this.productGridShowButton.style.display = '';
        var area = ['search', 'items', 'shipping_method', 'totals', 'giftmessage','billing_method'];
        // prepare additional fields and filtered items of products
        var fieldsPrepare = {};
        var itemsFilter = [];
        var products = this.gridProducts;
        for (var productId in products) {
            itemsFilter.push(productId);
            var paramKey = 'item['+productId+']';
            for (var productParamKey in products[productId]) {
                paramKey += '['+productParamKey+']';
                fieldsPrepare[paramKey] = products[productId][productParamKey];
            }
        }
        this.productConfigureSubmit('product_to_add', area, fieldsPrepare, itemsFilter);
        productConfigure.clean('quote_items');
        this.hideArea('search');
        this.gridProducts = {};
    },

    selectCustomer : function(grid, event){
        var element = event.target.closest('tr');
        if (element.title){
            this.setCustomerId(element.title);
        }
    },

    customerSelectorHide : function(){
        this.hideArea('customer-selector');
    },

    customerSelectorShow : function(){
        this.showArea('customer-selector');
    },

    storeSelectorHide : function(){
        this.hideArea('store-selector');
    },

    storeSelectorShow : function(){
        this.showArea('store-selector');
    },

    dataHide : function(){
        this.hideArea('data');
    },

    dataShow : function(){
        var submitBtn = document.getElementById('submit_order_top_button');
        if (submitBtn) {
            submitBtn.style.display = '';
        }
        this.showArea('data');
    },

    clearShoppingCart : function(confirmMessage){
        if (confirm(confirmMessage)) {
            this.collectElementsValue = false;
            order.sidebarApplyChanges({'sidebar[empty_customer_cart]': 1});
        }
    },

    sidebarApplyChanges : function(auxiliaryParams) {
        var sidebarEl = document.getElementById(this.getAreaId('sidebar'));
        if (sidebarEl) {
            var data = {};
            if (this.collectElementsValue) {
                var elems = sidebarEl.querySelectorAll('input');
                for (var i=0; i < elems.length; i++) {
                    if (_getFieldValue(elems[i])) {
                        data[elems[i].name] = _getFieldValue(elems[i]);
                    }
                }
            }
            if (auxiliaryParams instanceof Object) {
                for (var paramName in auxiliaryParams) {
                    data[paramName] = String(auxiliaryParams[paramName]);
                }
            }
            data.reset_shipping = true;
            this.loadArea(['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'], true, data);
        }
    },

    sidebarHide : function(){
        if(this.storeId === false && document.getElementById('page:left') && document.getElementById('page:container')){
            document.getElementById('page:left').style.display = 'none';
            document.getElementById('page:container').classList.remove('container');
            document.getElementById('page:container').classList.add('container-collapsed');
        }
    },

    sidebarShow : function(){
        if(document.getElementById('page:left') && document.getElementById('page:container')){
            document.getElementById('page:left').style.display = '';
            document.getElementById('page:container').classList.remove('container-collapsed');
            document.getElementById('page:container').classList.add('container');
        }
    },

    /**
     * Show configuration of product and add handlers on submit form
     *
     * @param productId
     */
    sidebarConfigureProduct: function (listType, productId, itemId) {
        // create additional fields
        var params = {};
        params.reset_shipping = true;
        params.add_product = productId;
        this.prepareParams(params);
        for (var i in params) {
            if (params[i] === null) {
                unset(params[i]);
            } else if (typeof(params[i]) == 'boolean') {
                params[i] = params[i] ? 1 : 0;
            }
        }
        var fields = [];
        for (var name in params) {
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = name;
            hiddenInput.value = params[name];
            fields.push(hiddenInput);
        }
        // add additional fields before triggered submit
        productConfigure.setBeforeSubmitCallback(listType, function() {
            productConfigure.addFields(fields);
        }.bind(this));
        // response handler
        productConfigure.setOnLoadIFrameCallback(listType, function(response) {
            if (!response.ok) {
                return;
            }
            this.loadArea(['items', 'shipping_method', 'billing_method','totals', 'giftmessage'], true);
        }.bind(this));
        // show item configuration
        itemId = itemId ? itemId : productId;
        productConfigure.showItemConfiguration(listType, itemId);
        return false;
    },

    removeSidebarItem : function(id, from){
        this.loadArea(['sidebar_'+from], 'sidebar_data_'+from, {remove_item:id, from:from});
    },

    itemsUpdate : function(){
        var area = ['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'];
        // prepare additional fields
        var fieldsPrepare = {update_items: 1};
        var info = document.getElementById('order-items_grid').querySelectorAll('input, select, textarea');
        for(var i=0; i<info.length; i++){
            if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                fieldsPrepare[info[i].name] = _getFieldValue(info[i]);
            }
        }
        fieldsPrepare = Object.assign(fieldsPrepare, this.productConfigureAddFields);
        this.productConfigureSubmit('quote_items', area, fieldsPrepare);
        this.orderItemChanged = false;
    },

    itemsOnchangeBind : function(){
        var elems = document.getElementById('order-items_grid').querySelectorAll('input, select, textarea');
        for(var i=0; i<elems.length; i++){
            if(!elems[i].bindOnchange){
                elems[i].bindOnchange = true;
                elems[i].addEventListener('change', this.itemChange.bind(this));
            }
        }
    },

    itemChange : function(event){
        this.giftmessageOnItemChange(event);
        this.orderItemChanged = true;
    },

    /**
     * Submit batch of configured products
     *
     * @param listType
     * @param area
     * @param fieldsPrepare
     * @param itemsFilter
     */
    productConfigureSubmit : function(listType, area, fieldsPrepare, itemsFilter) {
        // prepare loading areas and build url
        area = this.prepareArea(area);
        this.loadingAreas = area;
        var url = this.loadBaseUrl + 'block/' + area + '?isAjax=true';

        // prepare additional fields
        fieldsPrepare = this.prepareParams(fieldsPrepare);
        fieldsPrepare.reset_shipping = 1;
        fieldsPrepare.json = 1;

        // create fields
        var fields = [];
        for (var name in fieldsPrepare) {
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = name;
            hiddenInput.value = fieldsPrepare[name];
            fields.push(hiddenInput);
        }
        productConfigure.addFields(fields);

        // filter items
        if (itemsFilter) {
            productConfigure.addItemsFilter(listType, itemsFilter);
        }

        // prepare and do submit
        productConfigure.addListType(listType, {urlSubmit: url});
        productConfigure.setOnLoadIFrameCallback(listType, function(response){
            this.loadAreaResponseHandler(response);
        }.bind(this));
        productConfigure.submit(listType);
        // clean
        this.productConfigureAddFields = {};
    },

    /**
     * Show configuration of quote item
     *
     * @param itemId
     */
    showQuoteItemConfiguration: function(itemId){
        var listType = 'quote_items';
        var qtyElement = document.getElementById('order-items_grid').querySelector('input[name="item\\['+itemId+'\\]\\[qty\\]"]');
        productConfigure.setConfirmCallback(listType, function() {
            // sync qty of popup and qty of grid
            var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
            if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                qtyElement.value = confirmedCurrentQty.value;
            }
            this.productConfigureAddFields['item['+itemId+'][configured]'] = 1;

        }.bind(this));
        productConfigure.setShowWindowCallback(listType, function() {
            // sync qty of grid and qty of popup
            var formCurrentQty = productConfigure.getCurrentFormQtyElement();
            if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                formCurrentQty.value = qtyElement.value;
            }
        }.bind(this));
        productConfigure.showItemConfiguration(listType, itemId);
    },

    accountFieldsBind : function(container){
        var containerEl = document.getElementById(container);
        if(containerEl){
            var fields = containerEl.querySelectorAll('input, select, textarea');
            for(var i=0; i<fields.length; i++){
                if(fields[i].id == 'group_id'){
                    fields[i].addEventListener('change', this.accountGroupChange.bind(this));
                }
                else{
                    fields[i].addEventListener('change', this.accountFieldChange.bind(this));
                }
            }
        }
    },

    accountGroupChange : function(){
        this.loadArea(['data'], true, this.serializeData('order-form_account'));
    },

    accountFieldChange : function(){
        this.saveData(this.serializeData('order-form_account'));
    },

    commentFieldsBind : function(container){
        var containerEl = document.getElementById(container);
        if(containerEl){
            var fields = containerEl.querySelectorAll('input, textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].addEventListener('change', this.commentFieldChange.bind(this));
        }
    },

    commentFieldChange : function(){
        this.saveData(this.serializeData('order-comment'));
    },

    giftmessageFieldsBind : function(container){
        var containerEl = document.getElementById(container);
        if(containerEl){
            var fields = containerEl.querySelectorAll('input, textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].addEventListener('change', this.giftmessageFieldChange.bind(this));
        }
    },

    giftmessageFieldChange : function(){
        this.giftMessageDataChanged = true;
    },

    giftmessageOnItemChange : function(event) {
        var element = event.target;
        if(element.name.indexOf("giftmessage") != -1 && element.type == "checkbox" && !element.checked) {
            var messages = document.getElementById("order-giftmessage").querySelectorAll('textarea');
            var name;
            for(var i=0; i<messages.length; i++) {
                name = messages[i].id.split("_");
                if(name.length < 2) continue;
                if (element.name.indexOf("[" + name[1] + "]") != -1 && messages[i].value != "") {
                    alert("First, clean the Message field in Gift Message form");
                    element.checked = true;
                }
            }
        }
    },

    loadArea : function(area, indicator, params){
        var url = this.loadBaseUrl;
        if (area) {
            area = this.prepareArea(area);
            url += 'block/' + area;
        }
        if (indicator === true) indicator = 'html-body';
        params = this.prepareParams(params);
        params.json = true;
        if (!this.loadingAreas) this.loadingAreas = [];
        if (indicator) {
            this.loadingAreas = area;
            var self = this;
            fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams(params).toString()
            }).then(function(response) {
                return response.text();
            }).then(function(text) {
                var response;
                try {
                    response = JSON.parse(text);
                } catch(e) {
                    response = {};
                }
                self.loadAreaResponseHandler(response);
            });
        }
        else {
            fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams(params).toString()
            });
        }
        if (typeof productConfigure != 'undefined' && area instanceof Array && area.indexOf('items') != -1) {
            productConfigure.clean('quote_items');
        }
    },

    loadAreaResponseHandler : function (response){
        if (response.error) {
            alert(response.message);
        }
        if(response.ajaxExpired && response.ajaxRedirect) {
            setLocation(response.ajaxRedirect);
        }
        if(!this.loadingAreas){
            this.loadingAreas = [];
        }
        if(typeof this.loadingAreas == 'string'){
            this.loadingAreas = [this.loadingAreas];
        }
        if(this.loadingAreas.indexOf('message') == -1) {
            this.loadingAreas.push('message');
        }

        for(var i=0; i<this.loadingAreas.length; i++){
            var id = this.loadingAreas[i];
            var areaEl = document.getElementById(this.getAreaId(id));
            if(areaEl){
                if ('message' != id || response[id]) {
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = response[id] ? response[id] : '';
                    areaEl.innerHTML = '';
                    areaEl.appendChild(wrapper);
                }
                if (areaEl.callback) {
                    this[areaEl.callback]();
                }
            }
        }
    },

    prepareArea : function(area){
        if (this.giftMessageDataChanged) {
            return area.filter(function(item) { return item !== 'giftmessage'; });
        }
        return area;
    },

    saveData : function(data){
        this.loadArea(false, false, data);
    },

    showArea : function(area){
        var id = this.getAreaId(area);
        var el = document.getElementById(id);
        if(el) {
            el.style.display = '';
            this.areaOverlay();
        }
    },

    hideArea : function(area){
        var id = this.getAreaId(area);
        var el = document.getElementById(id);
        if(el) {
            el.style.display = 'none';
            this.areaOverlay();
        }
    },

    areaOverlay : function()
    {
        var overlayData = this.overlayData;
        Object.keys(overlayData).forEach(function(key){
            overlayData[key].fx();
        });
    },

    getAreaId : function(area){
        return 'order-'+area;
    },

    prepareParams : function(params){
        if (!params) {
            params = {};
        }
        if (!params.customer_id) {
            params.customer_id = this.customerId;
        }
        if (!params.customer_is_guest) {
            params.customer_is_guest = this.isGuest ? 1 : 0;
        }
        if (!params.store_id) {
            params.store_id = this.storeId;
        }
        if (!params.currency_id) {
            params.currency_id = this.currencyId;
        }
        if (!params.form_key) {
            params.form_key = FORM_KEY;
        }
        var data = this.serializeData('order-billing_method');
        if (data) {
            Object.keys(data).forEach(function(key) {
                params[key] = data[key];
            });
        }
        return params;
    },

    serializeData : function(container){
        var containerEl = document.getElementById(container);
        if (!containerEl) return {};
        var fields = containerEl.querySelectorAll('input, select, textarea');
        return _serializeElements(fields);
    },

    toggleCustomPrice: function(checkbox, elemId, tierBlock) {
        var elem = document.getElementById(elemId);
        var tierEl = document.getElementById(tierBlock);
        if (checkbox.checked) {
            elem.disabled = false;
            elem.style.display = '';
            if(tierEl) tierEl.style.display = 'none';
        }
        else {
            elem.disabled = true;
            elem.style.display = 'none';
            if(tierEl) tierEl.style.display = '';
        }
    },

    submit : function()
    {
        if (this.orderItemChanged) {
            if (confirm('You have item changes')) {
                if (editForm.submit()) {
                    disableElements('save');
                }
            } else {
                this.itemsUpdate();
            }
        } else {
            if (editForm.submit()) {
                disableElements('save');
            }
        }
    },

    overlay : function(elId, show, observe)
    {
        if (typeof(show) == 'undefined') { show = true; }

        var orderObj = this;
        var obj = this.overlayData[elId];
        if (!obj) {
            obj = {
                show: show,
                el: elId,
                order: orderObj,
                fx: function(event) {
                    this.order.processOverlay(this.el, this.show);
                }
            };
            obj.bfx = obj.fx.bind(obj);
            this.overlayData[elId] = obj;
        }
        else {
            obj.show = show;
            window.removeEventListener('resize', obj.bfx);
        }

        window.addEventListener('resize', obj.bfx);

        this.processOverlay(elId, show);
    },

    processOverlay : function(elId, show)
    {
        var el = document.getElementById(elId);

        if (!el) {
            return false;
        }

        var parentEl = el.parentElement ? el.parentElement.parentElement : null;
        if (!parentEl) return false;

        if (show) {
            parentEl.classList.remove('ignore-validate');
        }
        else {
            parentEl.classList.add('ignore-validate');
        }

        parentEl.style.position = 'relative';
        Object.assign(el.style, {
            display: show ? 'none' : '',
            position: 'absolute',
            backgroundColor: '#999999',
            opacity: '0.8',
            width: parentEl.offsetWidth + 'px',
            height: parentEl.offsetHeight + 'px',
            top: '0',
            left: '0'
        });
    },

    validateVat: function(parameters)
    {
        var params = {
            country: document.getElementById(parameters.countryElementId).value,
            vat: document.getElementById(parameters.vatElementId).value
        };

        if (this.storeId !== false) {
            params.store_id = this.storeId;
        }

        var currentCustomerGroupId = document.getElementById(parameters.groupIdHtmlId).value;
        var self = this;

        fetch(parameters.validateUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: new URLSearchParams(params).toString()
        }).then(function(response) {
            return response.text();
        }).then(function(text) {
            var message = '';
            var groupChangeRequired = false;
            try {
                var response = JSON.parse(text);

                if (true === response.valid) {
                    message = parameters.vatValidMessage;
                    if (currentCustomerGroupId != response.group) {
                        message = parameters.vatValidAndGroupChangeMessage;
                        groupChangeRequired = true;
                    }
                } else if (response.success) {
                    message = parameters.vatInvalidMessage.replace(/%s/, params.vat);
                    groupChangeRequired = true;
                } else {
                    message = parameters.vatValidationFailedMessage;
                    groupChangeRequired = true;
                }

            } catch (e) {
                message = parameters.vatErrorMessage;
            }
            if (!groupChangeRequired) {
                alert(message);
            }
            else {
                self.processCustomerGroupChange(parameters.groupIdHtmlId, message, response.group);
            }
        });
    },

    processCustomerGroupChange: function(groupIdHtmlId, message, groupId)
    {
        var currentCustomerGroupId = document.getElementById(groupIdHtmlId).value;
        var currentCustomerGroupTitle = document.querySelector('#' + groupIdHtmlId + ' > option[value="' + currentCustomerGroupId + '"]').text;
        var customerGroupOption = document.querySelector('#' + groupIdHtmlId + ' > option[value="' + groupId + '"]');
        var confirmText = message.replace(/%s/, customerGroupOption.text);
        confirmText = confirmText.replace(/%s/, currentCustomerGroupTitle);
        if (confirm(confirmText)) {
            document.querySelectorAll('#' + groupIdHtmlId + ' option').forEach(function(o) {
                o.selected = o.getAttribute('value') == groupId;
            });
            this.accountGroupChange();
        }
    }
};

function OrderFormArea(name, node, parent) {
    this._name = name;
    this._parent = parent;
    this._callbackName = node.callback;
    if (typeof this._callbackName == 'undefined') {
        this._callbackName = name + 'Loaded';
        node.callback = this._callbackName;
    }
    parent[this._callbackName] = _wrapFunction(parent[this._callbackName], (function (proceed){
        proceed();
        this.onLoad();
    }).bind(this));

    this.setNode(node);
}

OrderFormArea.prototype = {
    _name: null,
    _node: null,
    _parent: null,
    _callbackName: null,

    setNode: function(node){
        if (!node.callback) {
            node.callback = this._callbackName;
        }
        this.node = node;
    },

    onLoad: function(){
    }
};

function ControlButton(label) {
    this._label = label;
    this._node = document.createElement('button');
    this._node.className = 'scalable add';
    this._node.type = 'button';
}

ControlButton.prototype = {
    _label: '',
    _node: null,

    onClick: function(){
    },

    insertIn: function(element, position){
        var node = this._node;
        node.addEventListener('click', this.onClick);
        node.innerHTML = '<span>' + this._label + '</span>';
        if (position === 'top') {
            element.insertBefore(node, element.firstChild);
        } else {
            element.appendChild(node);
        }
    }
};
