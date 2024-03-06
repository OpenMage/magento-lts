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

class DirectPost {
    constructor(methodCode, iframeId, controller, orderSaveUrl, cgiUrl, nativeAction) {
        this.iframeId = iframeId;
        this.controller = controller;
        this.orderSaveUrl = orderSaveUrl;
        this.nativeAction = nativeAction;
        this.cgiUrl = cgiUrl;
        this.code = methodCode;
        this.inputs = ['cc_type', 'cc_number', 'expiration', 'expiration_yr', 'cc_cid'];
        this.headers = [];
        this.isValid = true;
        this.paymentRequestSent = false;
        this.isResponse = false;
        this.orderIncrementId = false;
        this.successUrl = false;
        this.hasError = false;
        this.tmpForm = false;

        this.onSaveOnepageOrderSuccess = this.saveOnepageOrderSuccess.bind(this);
        this.onLoadIframe = this.loadIframe.bind(this);
        this.onLoadOrderIframe = this.loadOrderIframe.bind(this);
        this.onSubmitAdminOrder = this.submitAdminOrder.bind(this);

        this.preparePayment();
    }

    validate() {
        this.isValid = true;
        this.inputs.forEach(elemIndex => {
            let element = document.getElementById(`${this.code}_${elemIndex}`);
            if (element && !Validation.validate(element)) {
                this.isValid = false;
            }
        });

        return this.isValid;
    }

    changeInputOptions(param, value) {
        this.inputs.forEach(elemIndex => {
            let element = document.getElementById(`${this.code}_${elemIndex}`);
            if (element) {
                element.setAttribute(param, value);
            }
        });
    }

    preparePayment() {
        this.changeInputOptions('autocomplete', 'off');
        if (document.getElementById(this.iframeId)) {
            switch (this.controller) {
                case 'onepage':
                    this.headers = [...document.querySelectorAll(`#${checkout.accordion.container.getAttribute('id')} .section`)];
                    let button = document.querySelector('#review-buttons-container button');
                    button.setAttribute('onclick', '');
                    button.removeEventListener('click', null);
                    button.addEventListener('click', () => {
                        if (document.getElementById(this.iframeId)) {
                            if (this.validate()) {
                                this.saveOnepageOrder();
                            }
                        } else {
                            review.save();
                        }
                    });
                    break;
                case 'sales_order_create':
                case 'sales_order_edit':
                    let buttons = document.getElementsByClassName('scalable save');
                    for (let i = 0; i < buttons.length; i++) {
                        buttons[i].setAttribute('onclick', '');
                        buttons[i].addEventListener('click', this.onSubmitAdminOrder);
                    }
                    document.getElementById(`order-${this.iframeId}`).addEventListener('load', this.onLoadOrderIframe);
                    break;
            }

            document.getElementById(this.iframeId).addEventListener('load', this.onLoadIframe);
        }
    }

    loadIframe() {
        if (this.paymentRequestSent) {
            switch (this.controller) {
                case 'onepage':
                    this.paymentRequestSent = false;
                    if (!this.hasError) {
                        this.returnQuote();
                    }
                    break;
                case 'sales_order_edit':
                case 'sales_order_create':
                    if (!this.orderRequestSent) {
                        this.paymentRequestSent = false;
                        if (!this.hasError) {
                            this.returnQuote();
                        } else {
                            this.changeInputOptions('disabled', false);
                            hideLoader();
                            enableElements('save');
                        }
                    }
                    break;
            }
            if (this.tmpForm) {
                document.body.removeChild(this.tmpForm);
            }
        }
    }

    loadOrderIframe() {
        if (this.orderRequestSent) {
            document.getElementById(this.iframeId).style.display = 'none';
            let data = document.getElementById(`order-${this.iframeId}`).contentWindow.document.body.innerHTML;
            this.saveAdminOrderSuccess(data);
            this.orderRequestSent = false;
        }
    }

    showError(msg) {
        this.hasError = true;
        if (this.controller === 'onepage') {
            document.getElementById(this.iframeId).style.display = 'none';
            this.resetLoadWaiting();
        }
        alert(msg.replace(/<\/?[^>]+(>|$)/g, ''));
    }

    returnQuote() {
        let url = this.orderSaveUrl.replace('place', 'returnQuote');
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error_message) {
                    alert(data.error_message.replace(/<\/?[^>]+(>|$)/g, ''));
                }
                document.getElementById(this.iframeId).style.display = 'block';
                switch (this.controller) {
                    case 'onepage':
                        this.resetLoadWaiting();
                        break;
                    case 'sales_order_edit':
                    case 'sales_order_create':
                        this.changeInputOptions('disabled', false);
                        hideLoader();
                        enableElements('save');
                        break;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    setLoadWaiting() {
        this.headers.forEach(header => {
            header.classList.remove('allow');
        });
        checkout.setLoadWaiting('review');
    }

    resetLoadWaiting() {
        this.headers.forEach(header => {
            header.classList.add('allow');
        });
        checkout.setLoadWaiting(false);
    }

    saveOnepageOrder() {
        this.hasError = false;
        this.setLoadWaiting();
        let params = `${new URLSearchParams(new FormData(payment.form)).toString()}&controller=${this.controller}`;
        if (review.agreementsForm) {
            params += `&${new URLSearchParams(new FormData(review.agreementsForm)).toString()}`;
        }

        fetch(this.orderSaveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params
        })
            .then(response => response.json())
            .then(this.onSaveOnepageOrderSuccess)
            .catch(error => {
                this.resetLoadWaiting();
                if (error.status === 403) {
                    checkout.ajaxFailure();
                }
            });
    }

    saveOnepageOrderSuccess(response) {
        if (response.status === 403) {
            checkout.ajaxFailure();
        }

        if (response.success && response.directpost) {
            this.orderIncrementId = response.directpost.fields.x_invoice_num;
            let paymentData = Object.fromEntries(
                Object.entries(response.directpost.fields).map(([key, value]) => [key, value])
            );
            let preparedData = this.preparePaymentRequest(paymentData);
            this.sendPaymentRequest(preparedData);
        } else {
            let msg = response.error_messages;
            if (Array.isArray(msg)) {
                msg = msg.join("\n");
            }
            if (msg) {
                alert(msg.replace(/<\/?[^>]+(>|$)/g, ''));
            }

            if (response.update_section) {
                document.getElementById(`checkout-${response.update_section.name}-load`).innerHTML = response.update_section.html;
                response.update_section.html.evalScripts();
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section);
                checkout.reloadProgressBlock();
            }
        }
    }

    submitAdminOrder() {
        if (editForm.validate()) {
            let paymentMethodEl = [...editForm.formId.querySelectorAll('input[name="payment[method]"]')].find(radio => radio.checked);
            this.hasError = false;
            if (paymentMethodEl.value === this.code) {
                showLoader();
                this.changeInputOptions('disabled', 'disabled');
                this.paymentRequestSent = true;
                this.orderRequestSent = true;
                editForm.formId.setAttribute('action', this.orderSaveUrl);
                editForm.formId.setAttribute('target', document.getElementById(`order-${this.iframeId}`).getAttribute('name'));
                editForm.formId.appendChild(this.createHiddenElement('controller', this.controller));
                disableElements('save');
                editForm.formId.submit();
            } else {
                editForm.formId.setAttribute('action', this.nativeAction);
                editForm.formId.setAttribute('target', '_top');
                disableElements('save');
                editForm.formId.submit();
            }
        }
    }

    recollectQuote() {
        let area = ['sidebar', 'items', 'shipping_method', 'billing_method', 'totals', 'giftmessage'];
        area = order.prepareArea(area);
        const url = `${order.loadBaseUrl}block/${area}`;
        const info = [...document.getElementById('order-items_grid').querySelectorAll('input, select, textarea')];
        const data = new FormData();
        for (let i = 0; i < info.length; i++) {
            if (!info[i].disabled && (info[i].type !== 'checkbox' || info[i].checked)) {
                data.append(info[i].name, info[i].value);
            }
        }
        data.append('reset_shipping', true);
        data.append('update_items', true);
        if (document.getElementById('coupons:code') && document.getElementById('coupons:code').value) {
            data.append('order[coupon][code]', document.getElementById('coupons:code').value);
        }
        data.append('json', true);

        fetch(url, {
            method: 'POST',
            body: data
        })
            .then(() => {
                editForm.formId.submit();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    saveAdminOrderSuccess(data) {
        let response = JSON.parse(data) || {};

        if (response.directpost) {
            this.orderIncrementId = response.directpost.fields.x_invoice_num;
            let paymentData = Object.fromEntries(
                Object.entries(response.directpost.fields).map(([key, value]) => [key, value])
            );
            let preparedData = this.preparePaymentRequest(paymentData);
            this.sendPaymentRequest(preparedData);
        } else {
            if (response.redirect) {
                window.location = response.redirect;
            }
            if (response.error_messages) {
                let msg = response.error_messages;
                if (Array.isArray(msg)) {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg.replace(/<\/?[^>]+(>|$)/g, ''));
                }
            }
        }
    }

    preparePaymentRequest(data) {
        if (document.getElementById(`${this.code}_cc_cid`)) {
            data.x_card_code = document.getElementById(`${this.code}_cc_cid`).value;
        }
        let year = document.getElementById(`${this.code}_expiration_yr`).value;
        if (year.length > 2) {
            year = year.slice(2);
        }
        let month = parseInt(document.getElementById(`${this.code}_expiration`).value, 10);
        if (month < 10) {
            month = `0${month}`;
        }

        data.x_exp_date = `${month}/${year}`;
        data.x_card_num = document.getElementById(`${this.code}_cc_number`).value;

        return data;
    }

    sendPaymentRequest(preparedData) {
        this.recreateIframe();
        this.tmpForm = document.createElement('form');
        this.tmpForm.style.display = 'none';
        this.tmpForm.enctype = 'application/x-www-form-urlencoded';
        this.tmpForm.method = 'POST';
        document.body.appendChild(this.tmpForm);
        this.tmpForm.action = this.cgiUrl;
        this.tmpForm.target = document.getElementById(this.iframeId).getAttribute('name');
        this.tmpForm.setAttribute('target', document.getElementById(this.iframeId).getAttribute('name'));

        for (const [param, value] of Object.entries(preparedData)) {
            this.tmpForm.appendChild(this.createHiddenElement(param, value));
        }

        this.paymentRequestSent = true;
        this.tmpForm.submit();
    }

    createHiddenElement(name, value) {
        let field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        return field;
    }

    recreateIframe() {
        if (document.getElementById(this.iframeId)) {
            let nextElement = document.getElementById(this.iframeId).nextElementSibling;
            let src = document.getElementById(this.iframeId).getAttribute('src');
            let name = document.getElementById(this.iframeId).getAttribute('name');
            document.getElementById(this.iframeId).removeEventListener('load', this.onLoadIframe);
            document.getElementById(this.iframeId).remove();
            let iframe = `<iframe id="${this.iframeId}" allowtransparency="true" frameborder="0" name="${name}" style="display:none;width:100%;background-color:transparent" src="${src}"></iframe>`;
            nextElement.insertAdjacentHTML('beforebegin', iframe);
            document.getElementById(this.iframeId).addEventListener('load', this.onLoadIframe);
        }
    }
}
