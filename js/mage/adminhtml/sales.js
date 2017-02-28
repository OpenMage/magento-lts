/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var AdminOrder = new Class.create();
AdminOrder.prototype = {
    initialize : function(data){
        if(!data) data = {};
        this.loadBaseUrl    = false;
        this.customerId     = data.customer_id ? data.customer_id : false;
        this.storeId        = data.store_id ? data.store_id : false;
        this.currencyId     = false;
        this.currencySymbol = data.currency_symbol ? data.currency_symbol : '';
        this.addresses      = data.addresses ? data.addresses : $H({});
        this.shippingAsBilling = data.shippingAsBilling ? data.shippingAsBilling : false;
        this.gridProducts   = $H({});
        this.gridProductsGift = $H({});
        this.billingAddressContainer = '';
        this.shippingAddressContainer= '';
        this.isShippingMethodReseted = data.shipping_method_reseted ? data.shipping_method_reseted : false;
        this.overlayData = $H({});
        this.giftMessageDataChanged = false;
        this.productConfigureAddFields = {};
        this.productPriceBase = {};
        this.collectElementsValue = true;
        Event.observe(window, 'load',  (function(){
            this.dataArea = new OrderFormArea('data', $(this.getAreaId('data')), this);
            this.itemsArea = Object.extend(new OrderFormArea('items', $(this.getAreaId('items')), this), {
                addControlButton: function(button){
                    var controlButtonArea = $(this.node).select('.form-buttons')[0];
                    if (typeof controlButtonArea != 'undefined') {
                        var buttons = controlButtonArea.childElements();
                        for (var i = 0; i < buttons.length; i++) {
                            if (buttons[i].innerHTML.include(button.label)) {
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
                $(searchAreaId).show();
                var el = this;
                window.setTimeout(function () {
                    el.remove();
                }, 10);
            };

            this.dataArea.onLoad = this.dataArea.onLoad.wrap(function(proceed) {
                proceed();
                this._parent.itemsArea.setNode($(this._parent.getAreaId('items')));
                this._parent.itemsArea.onLoad();
            });

            this.itemsArea.onLoad = this.itemsArea.onLoad.wrap(function(proceed) {
                proceed();
                if (!$(searchAreaId).visible()) {
                    this.addControlButton(searchButton);
                }
            });
            this.areasLoaded();
            this.itemsArea.onLoad();
        }).bind(this));
    },

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

    setCustomerId : function(id){
        this.customerId = id;
        this.loadArea('header', true);
        $(this.getAreaId('header')).callback = 'setCustomerAfter';
        $('back_order_top_button').hide();
        $('reset_order_top_button').show();
    },

    setCustomerAfter : function () {
        this.customerSelectorHide();
        if (this.storeId) {
            $(this.getAreaId('data')).callback = 'dataLoaded';
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
            return fieldId.include('billing');
        }
        return fieldId.include('shipping');
    },

    isBillingField : function(fieldId){
        return fieldId.include('billing');
    },

    bindAddressFields : function(container) {
        var fields = $(container).select('input', 'select', 'textarea');
        for(var i=0;i<fields.length;i++){
            Event.observe(fields[i], 'change', this.changeAddressField.bind(this));
        }
    },

    changeAddressField : function(event){
        var field = Event.element(event);
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
        data = data.toObject();

        if( (type == 'billing' && this.shippingAsBilling && !this.isShippingMethodReseted)
            || (type == 'shipping' && !this.shippingAsBilling && !this.isShippingMethodReseted) ) {
            data['reset_shipping'] = true;
        }

        data['order['+type+'_address][customer_address_id]'] = $('order-'+type+'_address_customer_address_id').value;

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
        var shippingId = $(field).identify().replace('-billing_', '-shipping_');
        var inputField = $(shippingId);
        if (inputField) {
            inputField.setValue($(field).getValue());
            if (inputField.changeUpdater) {
                inputField.changeUpdater();
            }
            $(this.shippingAddressContainer).select('select').each(function(el){
                el.disable();
            });
        }
    },

    fillAddressFields : function(container, data){
        var regionIdElem = false;
        var regionIdElemValue = false;

        var fields = $(container).select('input', 'select', 'textarea');
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
                    if (Object.isString(data[name])) {
                        values = data[name].split(',');
                    } else if (Object.isArray(data[name])) {
                        values = data[name];
                    }
                    fields[i].setValue(values);
                }
            } else {
                fields[i].setValue(data[name] ? data[name] : '');
            }

            if (fields[i].changeUpdater) fields[i].changeUpdater();
            if (name == 'region' && data['region_id'] > 0 && !data['region']){
                fields[i].value = data['region_id'];
            }
        }
    },

    disableShippingAddress : function(flag) {
        this.shippingAsBilling = flag;
        if ($('order-shipping_address_customer_address_id')) {
            $('order-shipping_address_customer_address_id').disabled = flag;
        }

        if ($(this.shippingAddressContainer)) {
            var dataFields = $(this.shippingAddressContainer).select('input', 'select', 'textarea');
            for (var i = 0; i < dataFields.length; i++) {
                dataFields[i].disabled = flag;
            }
            var buttons = $(this.shippingAddressContainer).select('button');
            // Add corresponding class to buttons while disabling them
            for (i = 0; i < buttons.length; i++) {
                buttons[i].disabled = flag;
                if (flag) {
                    buttons[i].addClassName('disabled');
                } else {
                    buttons[i].removeClassName('disabled');
                }
            }
        }
    },

    turnOffShippingFields : function() {
        if ($(this.shippingAddressContainer)) {
            var dataFields = $(this.shippingAddressContainer).select('input', 'select', 'textarea', 'button');
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
        data = data.toObject();
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
        this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
    },

    switchPaymentMethod : function(method){
        this.setPaymentMethod(method);
        var data = {};
        data['order[payment_method]'] = method;
        this.loadArea(['card_validation'], true, data);
    },

    setPaymentMethod : function(method){
        if (this.paymentMethod && $('payment_form_'+this.paymentMethod)) {
            var form = 'payment_form_'+this.paymentMethod;
            [form + '_before', form, form + '_after'].each(function(el) {
                var block = $(el);
                if (block) {
                    block.hide();
                    block.select('input', 'select', 'textarea').each(function(field) {
                        field.disabled = true;
                    });
                }
            });
        }

        if(!this.paymentMethod || method){
            $('order-billing_method_form').select('input', 'select', 'textarea').each(function(elem){
                if(elem.type != 'radio') elem.disabled = true;
            });
        }

        if ($('payment_form_'+method)){
            this.paymentMethod = method;
            var form = 'payment_form_'+method;
            [form + '_before', form, form + '_after'].each(function(el) {
                var block = $(el);
                if (block) {
                   block.show();
                   block.select('input', 'select', 'textarea').each(function(field) {
                       field.disabled = false;
                       if (!el.include('_before') && !el.include('_after') && !field.bindChange) {
                           field.bindChange = true;
                           field.paymentContainer = form; /** @deprecated after 1.4.0.0-rc1 */
                           field.method = method;
                           field.observe('change', this.changePaymentData.bind(this));
                        }
                    },this);
                }
            },this);
        }
    },

    changePaymentData : function(event){
        var elem = Event.element(event);
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
        var fields = $('payment_form_' + currentMethod).select('input', 'select');
        for(var i=0;i<fields.length;i++){
            data[fields[i].name] = fields[i].getValue();
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
        Element.hide(buttonElement);
        this.showArea('search');
    },

    productGridRowInit : function(grid, row){
        var checkbox = $(row).select('.checkbox')[0];
        var inputs = $(row).select('.input-text');
        if (checkbox && inputs.length > 0) {
            checkbox.inputElements = inputs;
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                input.checkboxElement = checkbox;

                var product = this.gridProducts.get(checkbox.value);
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

                input.disabled = !checkbox.checked || input.hasClassName('input-inactive');

                Event.observe(input,'keyup', this.productGridRowInputChange.bind(this));
                Event.observe(input,'change',this.productGridRowInputChange.bind(this));
            }
        }
    },

    productGridRowInputChange : function(event){
        var element = Event.element(event);
        if (element && element.checkboxElement && element.checkboxElement.checked){
            if (element.name!='giftmessage' || element.checked) {
                this.gridProducts.get(element.checkboxElement.value)[element.name] = element.value;
            } else if (element.name=='giftmessage' && this.gridProducts.get(element.checkboxElement.value)[element.name]) {
                delete(this.gridProducts.get(element.checkboxElement.value)[element.name]);
            }
        }
    },

    productGridRowClick : function(grid, event){
        var trElement = Event.findElement(event, 'tr');
        var qtyElement = trElement.select('input[name="qty"]')[0];
        var eventElement = Event.element(event);
        var isInputCheckbox = eventElement.tagName == 'INPUT' && eventElement.type == 'checkbox';
        var isInputQty = eventElement.tagName == 'INPUT' && eventElement.name == 'qty';
        if (trElement && !isInputQty) {
            var checkbox = Element.select(trElement, 'input[type="checkbox"]')[0];
            var confLink = Element.select(trElement, 'a')[0];
            var priceColl = Element.select(trElement, '.price')[0];
            if (checkbox) {
                // processing non composite product
                if (confLink.readAttribute('disabled')) {
                    var checked = isInputCheckbox ? checkbox.checked : !checkbox.checked;
                    grid.setCheckboxChecked(checkbox, checked);
                // processing composite product
                } else if (isInputCheckbox && !checkbox.checked) {
                    grid.setCheckboxChecked(checkbox, false);
                // processing composite product
                } else if (!isInputCheckbox || (isInputCheckbox && checkbox.checked)) {
                    var listType = confLink.readAttribute('list_type');
                    var productId = confLink.readAttribute('product_id');
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
                        if (!$(productConfigure.confirmedCurrentId) || !$(productConfigure.confirmedCurrentId).innerHTML) {
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
                    if (!$(elm.getAttribute('qtyId')).value) {
                        return 0;
                    } else {
                        optQty = parseFloat($(elm.getAttribute('qtyId')).value);
                    }
                }
                if (elm.hasAttribute('price') && !elm.disabled) {
                    return parseFloat(elm.readAttribute('price')) * optQty;
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
                            && Form.Element.getValue(elms[i]))
                ) {
                    productPrice += getPrice(elms[i]);
                }
            }
            return productPrice;
        }.bind(this);
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('input'));
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('select'));
        productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('textarea'));
        return productPrice;
    },

    productGridCheckboxCheck : function(grid, element, checked){
        if (checked) {
            if(element.inputElements) {
                this.gridProducts.set(element.value, {});
                var product = this.gridProducts.get(element.value);
                for (var i = 0; i < element.inputElements.length; i++) {
                    var input = element.inputElements[i];
                    if (!input.hasClassName('input-inactive')) {
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
            this.gridProducts.unset(element.value);
        }
        grid.reloadParams = {'products[]':this.gridProducts.keys()};
    },

    /**
     * Submit configured products to quote
     */
    productGridAddSelected : function(){
        if(this.productGridShowButton) Element.show(this.productGridShowButton);
        var area = ['search', 'items', 'shipping_method', 'totals', 'giftmessage','billing_method'];
        // prepare additional fields and filtered items of products
        var fieldsPrepare = {};
        var itemsFilter = [];
        var products = this.gridProducts.toObject();
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
        this.gridProducts = $H({});
    },

    selectCustomer : function(grid, event){
        var element = Event.findElement(event, 'tr');
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
        if ($('submit_order_top_button')) {
            $('submit_order_top_button').show();
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
        if ($(this.getAreaId('sidebar'))) {
            var data = {};
            if (this.collectElementsValue) {
                var elems = $(this.getAreaId('sidebar')).select('input');
                for (var i=0; i < elems.length; i++) {
                    if (elems[i].getValue()) {
                        data[elems[i].name] = elems[i].getValue();
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
        if(this.storeId === false && $('page:left') && $('page:container')){
            $('page:left').hide();
            $('page:container').removeClassName('container');
            $('page:container').addClassName('container-collapsed');
        }
    },

    sidebarShow : function(){
        if($('page:left') && $('page:container')){
            $('page:left').show();
            $('page:container').removeClassName('container-collapsed');
            $('page:container').addClassName('container');
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
            fields.push(new Element('input', {type: 'hidden', name: name, value: params[name]}));
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
        var info = $('order-items_grid').select('input', 'select', 'textarea');
        for(var i=0; i<info.length; i++){
            if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                fieldsPrepare[info[i].name] = info[i].getValue();
            }
        }
        fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
        this.productConfigureSubmit('quote_items', area, fieldsPrepare);
        this.orderItemChanged = false;
    },

    itemsOnchangeBind : function(){
        var elems = $('order-items_grid').select('input', 'select', 'textarea');
        for(var i=0; i<elems.length; i++){
            if(!elems[i].bindOnchange){
                elems[i].bindOnchange = true;
                elems[i].observe('change', this.itemChange.bind(this));
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
            fields.push(new Element('input', {type: 'hidden', name: name, value: fieldsPrepare[name]}));
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
        var qtyElement = $('order-items_grid').select('input[name="item\['+itemId+'\]\[qty\]"]')[0];
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
        if($(container)){
            var fields = $(container).select('input', 'select', 'textarea');
            for(var i=0; i<fields.length; i++){
                if(fields[i].id == 'group_id'){
                    fields[i].observe('change', this.accountGroupChange.bind(this));
                }
                else{
                    fields[i].observe('change', this.accountFieldChange.bind(this));
                }
            }
        }
    },

    accountGroupChange : function(){
        this.loadArea(['data'], true, this.serializeData('order-form_account').toObject());
    },

    accountFieldChange : function(){
        this.saveData(this.serializeData('order-form_account'));
    },

    commentFieldsBind : function(container){
        if($(container)){
            var fields = $(container).select('input', 'textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].observe('change', this.commentFieldChange.bind(this));
        }
    },

    commentFieldChange : function(){
        this.saveData(this.serializeData('order-comment'));
    },

    giftmessageFieldsBind : function(container){
        if($(container)){
            var fields = $(container).select('input', 'textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].observe('change', this.giftmessageFieldChange.bind(this));
        }
    },

    giftmessageFieldChange : function(){
        this.giftMessageDataChanged = true;
    },

    giftmessageOnItemChange : function(event) {
        var element = Event.element(event);
        if(element.name.indexOf("giftmessage") != -1 && element.type == "checkbox" && !element.checked) {
            var messages = $("order-giftmessage").select('textarea');
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
            new Ajax.Request(url, {
                parameters:params,
                loaderArea: indicator,
                onSuccess: function(transport) {
                    var response = transport.responseText.evalJSON();
                    this.loadAreaResponseHandler(response);
                }.bind(this)
            });
        }
        else {
            new Ajax.Request(url, {parameters:params,loaderArea: indicator});
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
            if($(this.getAreaId(id))){
                if ('message' != id || response[id]) {
                    var wrapper = new Element('div');
                    wrapper.update(response[id] ? response[id] : '');
                    $(this.getAreaId(id)).update(Prototype.Browser.IE ? wrapper.outerHTML : wrapper);
                }
                if ($(this.getAreaId(id)).callback) {
                    this[$(this.getAreaId(id)).callback]();
                }
            }
        }
    },

    prepareArea : function(area){
        if (this.giftMessageDataChanged) {
            return area.without('giftmessage');
        }
        return area;
    },

    saveData : function(data){
        this.loadArea(false, false, data);
    },

    showArea : function(area){
        var id = this.getAreaId(area);
        if($(id)) {
            $(id).show();
            this.areaOverlay();
        }
    },

    hideArea : function(area){
        var id = this.getAreaId(area);
        if($(id)) {
            $(id).hide();
            this.areaOverlay();
        }
    },

    areaOverlay : function()
    {
        $H(order.overlayData).each(function(e){
            e.value.fx();
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
            data.each(function(value) {
                params[value[0]] = value[1];
            });
        }
        return params;
    },

    serializeData : function(container){
        var fields = $(container).select('input', 'select', 'textarea');
        var data = Form.serializeElements(fields, true);

        return $H(data);
    },

    toggleCustomPrice: function(checkbox, elemId, tierBlock) {
        if (checkbox.checked) {
            $(elemId).disabled = false;
            $(elemId).show();
            if($(tierBlock)) $(tierBlock).hide();
        }
        else {
            $(elemId).disabled = true;
            $(elemId).hide();
            if($(tierBlock)) $(tierBlock).show();
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
        var obj = this.overlayData.get(elId);
        if (!obj) {
            obj = {
                show: show,
                el: elId,
                order: orderObj,
                fx: function(event) {
                    this.order.processOverlay(this.el, this.show);
                }
            };
            obj.bfx = obj.fx.bindAsEventListener(obj);
            this.overlayData.set(elId, obj);
        }
        else {
            obj.show = show;
            Event.stopObserving(window, 'resize', obj.bfx);
        }

        Event.observe(window, 'resize', obj.bfx);

        this.processOverlay(elId, show);
    },

    processOverlay : function(elId, show)
    {
        var el = $(elId);

        if (!el) {
            return false;
        }

        var parentEl = el.up(1);
        if (show) {
            parentEl.removeClassName('ignore-validate');
        }
        else {
            parentEl.addClassName('ignore-validate');
        }

        if (Prototype.Browser.IE) {
            parentEl.select('select').each(function (elem) {
                if (show) {
                    elem.needShowOnSuccess = false;
                    elem.style.visibility = '';
                } else {
                    elem.style.visibility = 'hidden';
                    elem.needShowOnSuccess = true;
                }
            });
        }

        parentEl.setStyle({position: 'relative'});
        el.setStyle({
            display: show ? 'none' : '',
            position: 'absolute',
            backgroundColor: '#999999',
            opacity: 0.8,
            width: parentEl.getWidth() + 'px',
            height: parentEl.getHeight() + 'px',
            top: 0,
            left: 0
        });
    },

    validateVat: function(parameters)
    {
        var params = {
            country: $(parameters.countryElementId).value,
            vat: $(parameters.vatElementId).value
        };

        if (this.storeId !== false) {
            params.store_id = this.storeId;
        }

        var currentCustomerGroupId = $(parameters.groupIdHtmlId).value;

        new Ajax.Request(parameters.validateUrl, {
            parameters: params,
            onSuccess: function(response) {
                var message = '';
                var groupChangeRequired = false;
                try {
                    response = response.responseText.evalJSON();

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
                    this.processCustomerGroupChange(parameters.groupIdHtmlId, message, response.group);
                }
            }.bind(this)
        });
    },

    processCustomerGroupChange: function(groupIdHtmlId, message, groupId)
    {
        var currentCustomerGroupId = $(groupIdHtmlId).value;
        var currentCustomerGroupTitle = $$('#' + groupIdHtmlId + ' > option[value=' + currentCustomerGroupId + ']')[0].text;
        var customerGroupOption = $$('#' + groupIdHtmlId + ' > option[value=' + groupId + ']')[0];
        var confirmText = message.replace(/%s/, customerGroupOption.text);
        confirmText = confirmText.replace(/%s/, currentCustomerGroupTitle);
        if (confirm(confirmText)) {
            $$('#' + groupIdHtmlId + ' option').each(function(o) {
                o.selected = o.readAttribute('value') == groupId;
            });
            this.accountGroupChange();
        }
    }
};

var OrderFormArea = Class.create();
OrderFormArea.prototype = {
    _name: null,
    _node: null,
    _parent: null,
    _callbackName: null,

    initialize: function(name, node, parent){
        this._name = name;
        this._parent = parent;
        this._callbackName = node.callback;
        if (typeof this._callbackName == 'undefined') {
            this._callbackName = name + 'Loaded';
            node.callback = this._callbackName;
        }
        parent[this._callbackName] = parent[this._callbackName].wrap((function (proceed){
            proceed();
            this.onLoad();
        }).bind(this));

        this.setNode(node);
    },

    setNode: function(node){
        if (!node.callback) {
            node.callback = this._callbackName;
        }
        this.node = node;
    },

    onLoad: function(){
    }
};

var ControlButton = Class.create();
ControlButton.prototype = {
    _label: '',
    _node: null,

    initialize: function(label){
        this._label = label;
        this._node = new Element('button', {
            'class': 'scalable add',
            'type':  'button'
        });
    },

    onClick: function(){
    },

    insertIn: function(element, position){
        var node = Object.extend(this._node),
            content = {};
        node.observe('click', this.onClick);
        node.update('<span>' + this._label + '</span>');
        content[position] = node;
        Element.insert(element, content);
    }
};
