/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function Captcha(url, formId) {
    this.url = url;
    this.formId = formId;
}

Captcha.prototype.refresh = function(elem) {
    var formId = this.formId;
    if (elem) elem.classList.add('refreshing');
    var params = new URLSearchParams();
    params.append('formId', this.formId);
    fetch(this.url, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: params
    }).then(function(response) {
        return response.text();
    }).then(function(text) {
        var json;
        try {
            json = JSON.parse(text);
        } catch (e) {
            if (elem) elem.classList.remove('refreshing');
            return;
        }
        if (!json.error && json.imgSrc) {
            document.getElementById(formId).setAttribute('src', json.imgSrc);
        }
        if (elem) elem.classList.remove('refreshing');
    }).catch(function() {
        if (elem) elem.classList.remove('refreshing');
    });
};

document.addEventListener('billing-request:completed', function(event) {
    if (typeof window.checkout != 'undefined') {
        if (window.checkout.method == 'guest' && document.getElementById('guest_checkout')){
            document.getElementById('guest_checkout').captcha.refresh();
        }
        if (window.checkout.method == 'register' && document.getElementById('register_during_checkout')){
            document.getElementById('register_during_checkout').captcha.refresh();
        }
    }
});

document.addEventListener('login:setMethod', function(event) {
    var switchCaptchaElement = function(shown, hidden) {
        var inputPrefix = 'captcha-input-box-', imagePrefix = 'captcha-image-box-';
        if (document.getElementById(inputPrefix + hidden)) {
            document.getElementById(inputPrefix + hidden).style.display = 'none';
            document.getElementById(imagePrefix + hidden).style.display = 'none';
        }
        if (document.getElementById(inputPrefix + shown)) {
            document.getElementById(inputPrefix + shown).style.display = '';
            document.getElementById(imagePrefix + shown).style.display = '';
        }
    };

    switch (event.detail.method) {
        case 'guest':
            switchCaptchaElement('guest_checkout', 'register_during_checkout');
            break;
        case 'register':
            switchCaptchaElement('register_during_checkout', 'guest_checkout');
            break;
    }
});
