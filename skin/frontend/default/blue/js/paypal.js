/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var PaypalExpress = Class.create();
PaypalExpress.prototype = {
    initialize: function(form, saveShippingMethodUrl, saveOrderUrl, successUrl){
        this.form = form;
        this.loadWaiting = false;
        this.saveShippingMethodUrl = saveShippingMethodUrl;
        this.saveOrderUrl = saveOrderUrl;
        this.successUrl = successUrl;

    },

    setLoadWaiting: function(step) {
        if (step) {
            if (this.loadWaiting) {
                this.setLoadWaiting(false);
            }
            $(step+'-buttons-container').setStyle({opacity:.5});
            Element.show(step+'-please-wait');
        } else {
            if (this.loadWaiting) {
                $(this.loadWaiting+'-buttons-container').setStyle({opacity:1});
                Element.hide(this.loadWaiting+'-please-wait');
            }
        }
        this.loadWaiting = step;
    },

    validateShippingMethod: function() {
    	var methods = document.getElementsByName('shipping_method');
    	if (methods.length==0) {
    		alert('Your order can not be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.');
    		return false;
    	}
    	for (var i=0; i<methods.length; i++) {
    		if (methods[i].checked) {
    			return true;
    		}
    	}
    	alert('Please specify shipping method.');
    	return false;
    },

    saveShippingMethod: function() {
    	if (this.loadWaiting!=false) return;

        if (this.validateShippingMethod()) {
            this.setLoadWaiting('shipping-method');
            var request = new Ajax.Request(
                this.saveShippingMethodUrl,
                {
                    method:'post',
                    onComplete: this.resetLoadWaiting.bind(this),
                    onSuccess: this.getShippingMethodResult.bind(this),
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function(transport){
        this.setLoadWaiting(false);
    },

    getShippingMethodResult: function(transport){
    	if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        if (response.progress_html) {
            $$('.col-right')[0].innerHTML = response.progress_html;
        }
        if (response.shipping_methods_html) {
        	$('checkout-shipping-method-load').innerHTML = response.shipping_methods_html;
        }
    },

    saveOrder: function() {
    	if (this.loadWaiting!=false) return;
        this.setLoadWaiting('review');
        var request = new Ajax.Request(
            this.saveOrderUrl,
            {
                method:'post',
                parameters:{save:true},
                onComplete: this.resetLoadWaiting.bind(this),
                onSuccess: this.getSaveOrderResult.bind(this),
            }
        );
    },

    getSaveOrderResult: function(transport) {
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
            if (response.success) {
                window.location=this.successUrl;
            }
            else{
                alert(response.error_messages.join("\n"));
            }
        }
    }
}