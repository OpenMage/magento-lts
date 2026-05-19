/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2022-2026 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class PayPalShortcut {
    constructor(config = {}) {
        this.config = config;
        this.container = null;
        this.sdkLoaded = false;
        this.abortController = new AbortController();

        this.init();
    }

    async init() {
        if (document.readyState === 'loading') {
            await new Promise(resolve => {
                document.addEventListener('DOMContentLoaded', resolve, { once: true });
            });
        }

        this.container = document.getElementById(this.config.containerId);
        if (!this.container) return;

        try {
            await this.loadPayPalSDK();
            await this.renderButton();
        } catch (error) {
            this.showError(error.message || this.config.errorMessage);
        }
    }

    async loadPayPalSDK() {
        if (typeof paypal !== 'undefined' && paypal.Buttons) {
            this.sdkLoaded = true;
            return;
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = this.config.sdkUrl;
            script.async = true;
            script.onload = () => {
                this.sdkLoaded = true;
                resolve();
            };
            script.onerror = () => reject(new Error('Failed to load PayPal SDK'));
            document.head.appendChild(script);
        });
    }

    async renderButton() {
        if (!this.sdkLoaded) {
            throw new Error('PayPal SDK not loaded');
        }

        await paypal.Buttons({
            style: {
                layout: this.config.buttonLayout || 'vertical',
                color: this.config.buttonColor || 'gold',
                shape: this.config.buttonShape || 'rect',
                label: this.config.buttonLabel || 'paypal',
                tagline: Boolean(this.config.buttonMessage),
                height: parseInt(this.config.buttonHeight, 10) || 40
            },
            createOrder: data => this.createOrder(data),
            onApprove: data => this.onApprove(data),
            onCancel: () => this.onCancel(),
            onError: error => this.onError(error)
        }).render(this.container);
    }

    async createOrder(data) {
        this.clearError();

        if (!this.validateSourceForm()) {
            this.showError(this.config.validationMessage);
            throw new Error('Form validation failed');
        }

        this.setLoading(true);
        try {
            const requestBody = this.buildRequestBody(data);
            const response = await fetch(this.config.startUrl, {
                method: 'POST',
                body: requestBody,
                signal: this.abortController.signal
            });
            const result = await response.json();

            if (result?.redirect) {
                window.location.href = result.redirect;
                throw new Error(result?.message || this.config.errorMessage);
            }

            if (!response.ok || !result?.success || !result?.id) {
                throw new Error(result?.message || result?.error || this.config.errorMessage);
            }

            return result.id;
        } catch (error) {
            this.showError(error.message || this.config.errorMessage);
            throw error;
        } finally {
            this.setLoading(false);
        }
    }

    buildRequestBody(data) {
        const form = this.getSourceForm();
        const requestBody = form ? new FormData(form) : new FormData();
        requestBody.set('form_key', this.config.formKey);
        requestBody.set('shortcut_context', this.config.context || 'cart');

        if (data?.paymentSource) {
            requestBody.set('funding_source', data.paymentSource);
        }

        return requestBody;
    }

    async onApprove(data) {
        const reviewUrl = new URL(this.config.reviewUrl, window.location.href);
        reviewUrl.searchParams.set('token', data.orderID);
        reviewUrl.searchParams.set('form_key', this.config.formKey);
        window.location.href = reviewUrl.toString();
    }

    onCancel() {
        this.setLoading(false);
        fetch(this.config.cancelUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                form_key: this.config.formKey
            }),
            signal: this.abortController.signal
        }).catch(error => console.error('PayPal shortcut cancel error:', error));
    }

    onError(error) {
        console.error('PayPal shortcut error:', error);
        this.showError(this.config.errorMessage);
    }

    validateSourceForm() {
        if (this.config.context !== 'product') {
            return true;
        }

        const form = this.getSourceForm();
        if (!form) {
            return false;
        }

        if (typeof productAddToCartForm !== 'undefined' && productAddToCartForm.validator) {
            return productAddToCartForm.validator.validate();
        }

        if (typeof Validation !== 'undefined') {
            return new Validation(form).validate();
        }

        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        return true;
    }

    getSourceForm() {
        if (!this.config.formId) {
            return null;
        }

        return document.getElementById(this.config.formId);
    }

    setLoading(isLoading) {
        if (!this.container) return;
        this.container.dataset.loading = isLoading ? 'true' : 'false';
    }

    showError(message) {
        if (!this.container) return;
        this.clearError();

        const error = document.createElement('div');
        error.id = `${this.config.containerId}-error`;
        error.className = 'paypal-error';
        error.textContent = message || this.config.errorMessage;
        this.container.after(error);
    }

    clearError() {
        document.getElementById(`${this.config.containerId}-error`)?.remove();
    }

    destroy() {
        this.abortController.abort();
        this.clearError();
    }
}

class PayPalExpressReview {
    constructor(config = {}) {
        this.config = config;
        this.form = null;
        this.abortController = new AbortController();

        this.init();
    }

    async init() {
        if (document.readyState === 'loading') {
            await new Promise(resolve => {
                document.addEventListener('DOMContentLoaded', resolve, { once: true });
            });
        }

        this.form = document.getElementById(this.config.formId);
        if (!this.form) return;

        this.bindShippingMethods();
        this.bindSubmit();
    }

    bindShippingMethods() {
        this.form.querySelectorAll('input[name="shipping_method"]').forEach(input => {
            input.addEventListener('change', () => {
                if (input.checked) {
                    this.saveShippingMethod(input.value);
                }
            }, { signal: this.abortController.signal });
        });
    }

    bindSubmit() {
        this.form.addEventListener('submit', event => {
            if (typeof Validation !== 'undefined' && !new Validation(this.form).validate()) {
                event.preventDefault();
                return;
            }

            const wait = document.getElementById('paypal-express-review-please-wait');
            if (wait) {
                wait.style.display = '';
            }
        }, { signal: this.abortController.signal });
    }

    async saveShippingMethod(method) {
        this.clearError();
        this.setLoading(true);

        try {
            const response = await fetch(this.config.saveShippingMethodUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    form_key: this.config.formKey,
                    token: this.config.token,
                    shipping_method: method
                }),
                signal: this.abortController.signal
            });
            const result = await response.json();
            if (!response.ok || !result?.success) {
                throw new Error(result?.message || 'Unable to save shipping method.');
            }

            const totals = document.getElementById(this.config.totalsContainerId);
            if (totals && result.totals_html) {
                totals.innerHTML = result.totals_html;
            }

            const grandTotal = document.getElementById(this.config.grandTotalInputId);
            if (grandTotal && result.grand_total) {
                grandTotal.value = result.grand_total;
            }
        } catch (error) {
            this.showError(error.message);
        } finally {
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        if (!this.form) return;
        this.form.dataset.loading = isLoading ? 'true' : 'false';
    }

    showError(message) {
        const error = document.getElementById(this.config.errorContainerId);
        if (!error) return;

        error.textContent = message;
        error.style.display = '';
    }

    clearError() {
        const error = document.getElementById(this.config.errorContainerId);
        if (!error) return;

        error.textContent = '';
        error.style.display = 'none';
    }

    destroy() {
        this.abortController.abort();
    }
}

if (typeof window !== 'undefined') {
    window.PayPalShortcut = PayPalShortcut;
    window.PayPalExpressReview = PayPalExpressReview;
}
