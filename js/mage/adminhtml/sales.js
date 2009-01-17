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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var AdminOrder = new Class.create();
AdminOrder.prototype = {
    initialize : function(data){
        if(!data) data = {};
        this.loadBaseUrl    = false;
        this.customerId     = data.customer_id ? data.customer_id : false;
        this.storeId        = data.store_id ? data.store_id : false;
        this.currencyId     = false;
        this.addresses      = data.addresses ? data.addresses : $H({});
        this.shippingAsBilling = data.shippingAsBilling ? data.shippingAsBilling : false;
        this.gridProducts   = $H({});
        this.gridProductsGift = $H({});
        this.billingAddressContainer = '';
        this.shippingAddressContainer= '';
        this.isShippingMethodReseted = data.shipping_method_reseted ? data.shipping_method_reseted : false;
        this.overlayData = $H({});
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
            $(this.getAreaId('data')).callback = 'dataShow';
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

    selectAddress : function(el, container){
        id = el.value;
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
        var fields = $(container).select('input', 'select');
        for(var i=0;i<fields.length;i++){
            Event.observe(fields[i], 'change', this.changeAddressField.bind(this));
        }
    },

    changeAddressField : function(event){
        var field = Event.element(event);
        var re = /[^\[]*\[([^\]]*)_address\]\[([^\]]*)\](\[(\d)\])?/;
        var matchRes = field.name.match(re);
        var type = matchRes[1];
        var name = matchRes[2];

        var data;
        if(this.isBillingField(field.id)){
            data = this.serializeData(this.billingAddressContainer)
        }
        else{
            data = this.serializeData(this.shippingAddressContainer)
        }
        data = data.toObject();

        if(name == 'postcode' || name == 'country_id' || name == 'region_id'){
            if( (type == 'billing' && this.shippingAsBilling)
                || (type == 'shipping' && !this.shippingAsBilling) ) {
                data['reset_shipping'] = true;
            }
        }

        data['order['+type+'_address][customer_address_id]'] = $('order:'+type+'_address_customer_address_id').value;

        if (data['reset_shipping']) {
            this.resetShippingMethod(data);
        }
        else {
            this.saveData(data);
            // added for reloading of default sender and default recipient for giftmessages
            //this.loadArea(['giftmessage'], true, data);
        }
    },

    fillAddressFields : function(container, data){
        var regionIdElem = false;
        var regionIdElemValue = false;
        var fields = $(container).select('input', 'select');
        var re = /[^\[]*\[[^\]]*\]\[([^\]]*)\](\[(\d)\])?/;
        for(var i=0;i<fields.length;i++){
            var matchRes = fields[i].name.match(re);
            var name = matchRes[1];
            var index = matchRes[3];

            if(index){
                if(data[name]){
                    var values = data[name].split("\n");
                    fields[i].value = values[index] ? values[index] : '';
                }
                else{
                    fields[i].value = '';
                }
            }
            else{
                fields[i].value = data[name] ? data[name] : '';
            }

            if(fields[i].changeUpdater) fields[i].changeUpdater();
            if(name == 'region' && data['region_id'] && !data['region']){
                fields[i].value = data['region_id'];
            }
        }
    },

    disableShippingAddress : function(flag){
        this.shippingAsBilling = flag;
        if($('order:shipping_address_customer_address_id')) {
            $('order:shipping_address_customer_address_id').disabled=flag;
        }
        if($(this.shippingAddressContainer)){
            var dataFields = $(this.shippingAddressContainer).select('input', 'select');
            for(var i=0;i<dataFields.length;i++) dataFields[i].disabled = flag;
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
        this.loadArea(['shipping_method', 'billing_method', 'totals', 'giftmessage'], true, data);
    },

    loadShippingRates : function(){
        this.isShippingMethodReseted = false;
        this.loadArea(['shipping_method'], true, {collect_shipping_rates: 1});
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
        this.saveData(data);
    },

    setPaymentMethod : function(method){
        if (this.paymentMethod && $('payment_form_'+this.paymentMethod)) {
            var form = $('payment_form_'+this.paymentMethod);
            form.hide();
            var elements = form.select('input', 'select');
            for (var i=0; i<elements.length; i++) elements[i].disabled = true;
        }

        if(!this.paymentMethod || method){
            $('order:billing_method').select('input', 'select').each(function(elem){
                if(elem.type != 'radio') elem.disabled = true;
            })
        }

        if ($('payment_form_'+method)){
            this.paymentMethod = method;
            var form = $('payment_form_'+method);
            form.show();
            var elements = form.select('input', 'select');
            for (var i=0; i<elements.length; i++) {
                elements[i].disabled = false;
                if(!elements[i].bindChange){
                    elements[i].bindChange = true;
                    elements[i].paymentContainer = 'payment_form_'+method;
                    elements[i].observe('change', this.changePaymentData.bind(this))
                }
            }
        }
    },

    changePaymentData : function(event){
        var elem = Event.element(event);
        if(elem && elem.paymentContainer){
            var data = {};
            var fields = $(elem.paymentContainer).select('input', 'select');
            for(var i=0;i<fields.length;i++){
                data[fields[i].name] = fields[i].getValue();
            }
            if ((typeof data['payment[cc_type]']) != 'undefined' && (!data['payment[cc_type]'] || !data['payment[cc_number]'])) {
                return;
            }
            this.saveData(data);
        }
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
                inputs[i].checkboxElement = checkbox;
                if (this.gridProducts.get(checkbox.value) && this.gridProducts.get(checkbox.value)[inputs[i].name] && inputs[i].name != 'giftmessage') {
                    inputs[i].value = this.gridProducts.get(checkbox.value)[inputs[i].name];
                } else if (this.gridProducts.get(checkbox.value) && this.gridProducts.get(checkbox.value)[inputs[i].name]) {
                    inputs[i].checked = true;
                }
                inputs[i].disabled = !checkbox.checked;
                Event.observe(inputs[i],'keyup', this.productGridRowInputChange.bind(this));
                Event.observe(inputs[i],'change',this.productGridRowInputChange.bind(this));
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
        var isInput = Event.element(event).tagName == 'INPUT';
        if (trElement) {
            var checkbox = Element.select(trElement, 'input');
            if (checkbox[0]) {
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
    },

    productGridCheckboxCheck : function(grid, element, checked){
        if (checked) {
            if(element.inputElements) {
                this.gridProducts.set(element.value, {});
                for(var i = 0; i < element.inputElements.length; i++) {
                    element.inputElements[i].disabled = false;
                    if (element.inputElements[i].name == 'qty') {
                        if (!element.inputElements[i].value) {
                            element.inputElements[i].value = 1;
                        }
                    }
                    if (element.inputElements[i].name!='giftmessage' || element.inputElements[i].checked) {
                        this.gridProducts.get(element.value)[element.inputElements[i].name] = element.inputElements[i].value;
                    } else if (element.inputElements[i].name=='giftmessage' && this.gridProducts.get(element.value)[element.inputElements[i].name]) {
                        delete(this.gridProducts.get(element.value)[element.inputElements[i].name]);
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

    productGridAddSelected : function(){
        if(this.productGridShowButton) Element.show(this.productGridShowButton);
        var data = {};
        data['add_products'] = this.gridProducts.toJSON();
        data['reset_shipping'] = 1;
        this.gridProducts = $H({});
        this.hideArea('search');
        this.loadArea(['search', 'items', 'shipping_method', 'totals', 'giftmessage','billing_method'], true, data);
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

    sidebarApplyChanges : function(){
        if($(this.getAreaId('sidebar'))){
            var data  = {};
            var elems = $(this.getAreaId('sidebar')).select('input');
            for(var i=0; i<elems.length; i++){
                if(elems[i].getValue()){
                    data[elems[i].name] = elems[i].getValue();
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

    removeSidebarItem : function(id, from){
        this.loadArea(['sidebar_'+from], 'sidebar_data_'+from, {remove_item:id, from:from});
    },

    itemsUpdate : function(){
        var info = $('order:items_grid').select('input', 'select', 'textarea');
        var data = {};
        for(var i=0; i<info.length; i++){
            if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                data[info[i].name] = info[i].getValue();
            }
        }
        data.reset_shipping = true;
        data.update_items = true;
        this.orderItemChanged = false;
        this.loadArea(['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'], true, data);
    },

    itemsOnchangeBind : function(){
        var elems = $('order:items_grid').select('input', 'select', 'textarea');
        for(var i=0; i<elems.length; i++){
            if(!elems[i].bindOnchange){
                elems[i].bindOnchange = true;
                elems[i].observe('change', this.itemChange.bind(this))
            }
        }
    },

    itemChange : function(){
        this.orderItemChanged = true;
    },

    accountFieldsBind : function(container){
        if($(container)){
            var fields = $(container).select('input', 'select');
            for(var i=0; i<fields.length; i++){
                if(fields[i].id == 'group_id'){
                    fields[i].observe('change', this.accountGroupChange.bind(this))
                }
                else{
                    fields[i].observe('change', this.accountFieldChange.bind(this))
                }
            }
        }
    },

    accountGroupChange : function(){
        this.loadArea(['data'], true, this.serializeData('order:form_account').toObject());
    },

    accountFieldChange : function(){
        this.saveData(this.serializeData('order:form_account'));
    },

    commentFieldsBind : function(container){
        if($(container)){
            var fields = $(container).select('input', 'textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].observe('change', this.commentFieldChange.bind(this))
        }
    },

    commentFieldChange : function(){
        this.saveData(this.serializeData('order:comment'));
    },

    giftmessageFieldsBind : function(container){
        if($(container)){
            var fields = $(container).select('input', 'textarea');
            for(var i=0; i<fields.length; i++)
                fields[i].observe('change', this.giftmessageFieldChange.bind(this))
        }
    },

    giftmessageFieldChange : function(){
        this.saveData(this.serializeData('order:giftmessage'));
    },

    loadArea : function(area, indicator, params){
        var url = this.loadBaseUrl;
        if(area) url+= 'block/' + area
        if(indicator === true) indicator = 'html-body';
        params = this.prepareParams(params);
        params.json = true;
        if(!this.loadingAreas) this.loadingAreas = [];
        if (indicator) {
            this.loadingAreas = area;
            new Ajax.Request(url, {
                parameters:params,
                loaderArea: indicator,
                onSuccess: function(transport) {
                    var response = transport.responseText.evalJSON();
                    if(!this.loadingAreas){
                        this.loadingAreas = [];
                    }
                    if(typeof this.loadingAreas == 'string'){
                        this.loadingAreas = [this.loadingAreas];
                    }
                    if(this.loadingAreas.indexOf('messages'==-1)) this.loadingAreas.push('messages');
                    for(var i=0; i<this.loadingAreas.length; i++){
                        var id = this.loadingAreas[i];
                        if($(this.getAreaId(id))){
                            $(this.getAreaId(id)).update(response[id] ? response[id] : '');
                            if ($(this.getAreaId(id)).callback) {
                                this[$(this.getAreaId(id)).callback]();
                            }
                        }
                    }
                }.bind(this)
            });
        }
        else {
            new Ajax.Request(url, {parameters:params,loaderArea: indicator});
        }
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
        return 'order:'+area;
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
        var data = this.serializeData('order:billing_method');
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

    submit : function(){
        //editForm.submit();
        if(this.orderItemChanged){
            if(confirm('You have item changes')){
                //$('edit_form').submit();
                editForm.submit();
            }
            else{
                this.itemsUpdate();
            }
        }
        else{
            //$('edit_form').submit();
            editForm.submit();
        }
    },

    overlay : function(elId, show, observe)
    {
        if (typeof(show) == 'undefined') { show = true; }

        var orderObj = this;
        var obj = this.overlayData.get(elId)
        if (!obj) {
            obj = {
                show: show,
                el: elId,
                order: orderObj,
                fx: function(event) {
                    this.order.processOverlay(this.el, this.show);
                }
            }
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
        var parentEl = el.up(1);
        var parentPos = Element.cumulativeOffset(parentEl);
        if (show) {
            parentEl.removeClassName('ignore-validate');
        }
        else {
            parentEl.addClassName('ignore-validate');
        }

        if (Prototype.Browser.IE) {
            parentEl.select('select').each(function (elem) {
                show ? elem .show() : elem.hide();
            });
        }

        el.setStyle({
            display: show ? 'none' : '',
            position: 'absolute',
            backgroundColor: '#999999',
            opacity: 0.8,
            width: parentEl.getWidth() + 'px',
            height: parentEl.getHeight() + 'px',
            top: parentPos[1] + 'px',
            left: parentPos[0] + 'px'
        });
    }
}