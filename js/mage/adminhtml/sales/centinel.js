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
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var centinelValidator = new Class.create();
centinelValidator.prototype = {

    initialize : function(method, validationUrl, containerId){
        this.method = method;
        this.validationUrl = validationUrl;
        this.containerId = containerId;
    },

    validate : function(){
        if (order.paymentMethod != this.method) {
            return false;
        }
        var params = order.getPaymentData();
        params = order.prepareParams(params);
        params.json = true;

        new Ajax.Request(this.validationUrl, {
            parameters:params,
            method:'post',
            onSuccess: function(transport) {
            var response = transport.responseText.evalJSON();
                if (response.authenticationUrl) {
                    this.autenticationStart(response.authenticationUrl);
                }
                if (response.message) {
                    this.autenticationFinish(response.message);
                }
            }.bind(this)
        });
    },

    autenticationStart : function(url) {
        this.getContainer().src = url;
        this.getContainer().style.display = 'block';
    },

    autenticationFinish : function(message) {
        alert(message);
        this.getContainer().style.display = 'none';
    },

    getContainer : function() {
        return $(this.containerId);
    }

};
