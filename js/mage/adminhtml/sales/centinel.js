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
function centinelValidator(method, validationUrl, containerId) {
    this.method = method;
    this.validationUrl = validationUrl;
    this.containerId = containerId;
}

centinelValidator.prototype.validate = function() {
    if (order.paymentMethod != this.method) {
        return false;
    }
    var params = order.getPaymentData();
    params = order.prepareParams(params);
    params.json = true;

    var body = new URLSearchParams();
    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            body.append(key, params[key]);
        }
    }

    fetch(this.validationUrl, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: body
    }).then(function(response) {
        return response.text();
    }).then(function(text) {
        var response = JSON.parse(text);
        if (response.authenticationUrl) {
            this.autenticationStart(response.authenticationUrl);
        }
        if (response.message) {
            this.autenticationFinish(response.message);
        }
    }.bind(this));
};

centinelValidator.prototype.autenticationStart = function(url) {
    this.getContainer().src = url;
    this.getContainer().style.display = 'block';
};

centinelValidator.prototype.autenticationFinish = function(message) {
    alert(message);
    this.getContainer().style.display = 'none';
};

centinelValidator.prototype.getContainer = function() {
    return document.getElementById(this.containerId);
};
