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
 * @copyright   Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Captcha {
    constructor(url, formId) {
        this.url = url;
        this.formId = formId;
    }

    refresh(elem) {
        const formId = this.formId;
        if (elem) {
            elem.classList.add('refreshing');
        }

        let formData = new FormData();
        formData.append('formId', this.formId);

        fetch(this.url, {
            method: 'post',
            body: formData,
        })
            .then(response => response.json())
            .then(json => {
                if (!json.error && json.imgSrc) {
                    document.getElementById(formId).setAttribute('src', json.imgSrc);
                    if (elem) {
                        elem.classList.remove('refreshing');
                    }
                } else {
                    if (elem) {
                        elem.classList.remove('refreshing');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('billing-request:completed', function(event) {
    if (typeof window.checkout !== 'undefined') {
        if (window.checkout.method === 'guest' && document.getElementById('guest_checkout')) {
            document.getElementById('guest_checkout').captcha.refresh();
        }
        if (window.checkout.method === 'register' && document.getElementById('register_during_checkout')) {
            document.getElementById('register_during_checkout').captcha.refresh();
        }
    }
});

document.addEventListener('login:setMethod', function(event) {
    const switchCaptchaElement = function(shown, hidden) {
        const inputPrefix = 'captcha-input-box-', imagePrefix = 'captcha-image-box-';
        const hiddenInput = document.getElementById(inputPrefix + hidden);
        const shownInput = document.getElementById(inputPrefix + shown);
        const hiddenImage = document.getElementById(imagePrefix + hidden);
        const shownImage = document.getElementById(imagePrefix + shown);

        if (hiddenInput) {
            hiddenInput.style.display = 'none';
            hiddenImage.style.display = 'none';
        }
        if (shownInput) {
            shownInput.style.display = 'block';
            shownImage.style.display = 'block';
        }
    };

    switch (event.memo.method) {
        case 'guest':
            switchCaptchaElement('guest_checkout', 'register_during_checkout');
            break;
        case 'register':
            switchCaptchaElement('register_during_checkout', 'guest_checkout');
            break;
    }
});
