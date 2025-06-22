/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class PayPalPayment {
    constructor(config = {}) {
        this.config = config;
        this.buttonInitialized = false;
        this.sdkLoaded = false;
        this.renderPromise = null;
        this.reviewContainerInterval = null;
        this.abortController = new AbortController();

        this.init();
    }

    async init() {
        if (document.readyState === 'loading') {
            await new Promise(resolve => {
                document.addEventListener('DOMContentLoaded', resolve, { once: true });
            });
        }

        this.setupEventListeners();
        this.checkAndInitialize();
    }

    setupEventListeners() {
        this.setupPaymentMethodHandling();
        this.setupPaymentButtonHandling();
    }

    setupPaymentButtonHandling() {
        const paymentButton = document.querySelector('button[onclick="payment.save()"]');
        if (!paymentButton) return;

        paymentButton.addEventListener('click', async () => {
            if (this.getCurrentPaymentMethod() === this.config.methodCode) {
                const container = await this.waitForReviewContainer();
                if (container) {
                    await this.initializePayPalButton();
                }
            }
        }, { signal: this.abortController.signal });
    }

    async waitForReviewContainer() {
        if (this.reviewContainerInterval) {
            clearInterval(this.reviewContainerInterval);
            this.reviewContainerInterval = null;
        }

        const maxAttempts = 100;
        let attempts = 0;

        return new Promise(resolve => {
            const checkForContainer = () => {
                attempts++;
                const reviewContainer = document.getElementById(this.config.reviewButtonContainerId);

                if (reviewContainer || attempts >= maxAttempts) {
                    if (this.reviewContainerInterval) {
                        clearInterval(this.reviewContainerInterval);
                        this.reviewContainerInterval = null;
                    }
                    resolve(reviewContainer);
                }
            };

            checkForContainer();

            if (!document.getElementById(this.config.reviewButtonContainerId)) {
                this.reviewContainerInterval = setInterval(checkForContainer, 100);
            }
        });
    }

    checkAndInitialize() {
        if (this.getCurrentPaymentMethod() === this.config.methodCode) {
            this.initializePayPalButton();
        }
    }

    getCurrentPaymentMethod() {
        const checkedInput = document.querySelector('input[name="payment[method]"]:checked');
        return checkedInput?.value ?? null;
    }

    async initializePayPalButton() {
        if (this.buttonInitialized) return;

        try {
            await this.loadPayPalSDK();
            await this.renderButton();
        } catch (error) {
            this.showError('Failed to load PayPal. Please try another payment method.');
        }
    }

    async loadPayPalSDK() {
        if (typeof paypal !== 'undefined') {
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

        const reviewContainer = document.getElementById(this.config.reviewButtonContainerId);
        if (!reviewContainer) {
            throw new Error('Review container not found');
        }

        this.removePayPalButton();

        const checkoutButton = reviewContainer.querySelector('button.btn-checkout');
        const paypalContainer = this.createPayPalContainer();

        if (!checkoutButton) {
            reviewContainer.prepend(paypalContainer);
        } else {
            checkoutButton.remove();
            const pleaseWaitSpan = reviewContainer.querySelector('span.please-wait');

            if (pleaseWaitSpan) {
                pleaseWaitSpan.before(paypalContainer);
            } else {
                reviewContainer.prepend(paypalContainer);
            }
        }

        try {
            await this.createPayPalButton(paypalContainer, reviewContainer);
        } catch (error) {
            this.handleRenderError(error, reviewContainer, paypalContainer);
            throw error;
        }
    }

    createPayPalContainer() {
        const container = document.createElement('div');
        container.id = this.config.containerId;
        container.className = 'paypal-button-container';
        container.style.cssText = 'margin: 0; min-height: 35px;';
        return container;
    }

    async createPayPalButton(paypalContainer, reviewContainer) {
        const buttonWrapper = document.createElement('div');
        buttonWrapper.className = 'paypal-button-wrapper';
        paypalContainer.appendChild(buttonWrapper);

        await paypal.Buttons({
            style: {
                layout: this.config.buttonLayout || 'vertical',
                color: this.config.buttonColor || 'gold',
                shape: this.config.buttonShape || 'rect',
                label: this.config.buttonLabel || 'paypal',
                height: parseInt(this.config.buttonHeight, 10) || 40
            },
            createOrder: () => this.createOrder(),
            onApprove: (data) => this.onApprove(data),
            onError: (error) => this.onButtonError(error),
            onCancel: () => this.onCancel()
        }).render(buttonWrapper);

        this.buttonInitialized = true;
    }

    handleRenderError(error, reviewContainer, paypalContainer) {
        paypalContainer?.remove();
        this.recreateCheckoutButton(reviewContainer);
        this.buttonInitialized = false;
    }

    recreateCheckoutButton(reviewContainer) {
        if (reviewContainer.querySelector('button.btn-checkout')) return;

        const checkoutButton = document.createElement('button');
        checkoutButton.type = 'submit';
        checkoutButton.title = 'Place Order';
        checkoutButton.className = 'button btn-checkout';
        checkoutButton.onclick = () => review.save();

        const span1 = document.createElement('span');
        const span2 = document.createElement('span');
        span2.textContent = 'Place Order';
        span1.appendChild(span2);
        checkoutButton.appendChild(span1);

        const pleaseWaitSpan = reviewContainer.querySelector('span.please-wait');
        if (pleaseWaitSpan) {
            pleaseWaitSpan.before(checkoutButton);
        } else {
            reviewContainer.prepend(checkoutButton);
        }
    }

    async createOrder() {
        this.showLoadingMask();

        try {
            if (!this.validateForm()) {
                throw new Error('Form validation failed');
            }

            const response = await fetch(this.config.createOrderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    form_key: this.config.formKey
                }),
                signal: this.abortController.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result?.success || !result?.id) {
                throw new Error(result?.message || 'Failed to create PayPal order');
            }

            return result.id;
        } catch (error) {
            console.error('Create order error:', error);
            throw error;
        } finally {
            this.hideLoadingMask();
        }
    }

    async onApprove(data) {
        this.showLoadingMask();

        try {
            const response = await fetch(this.config.captureUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    order_id: data.orderID,
                    form_key: this.config.formKey
                }),
                signal: this.abortController.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result?.success) {
                throw new Error(result?.message || 'Payment capture failed');
            }

            this.submitPlaceOrderForm(data.orderID);
            return result;
        } catch (error) {
            console.error('Payment approval error:', error);
            this.showError(error.message);
            throw error;
        }
    }

    submitPlaceOrderForm(orderId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.config.placeOrderUrl;
        form.style.display = 'none';

        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'id';
        orderIdInput.value = orderId;

        const formKeyInput = document.createElement('input');
        formKeyInput.type = 'hidden';
        formKeyInput.name = 'form_key';
        formKeyInput.value = this.config.formKey;

        form.append(orderIdInput, formKeyInput);
        document.body.appendChild(form);
        form.submit();
    }

    onButtonError(error) {
        console.error('PayPal button error:', error);
        this.showError('PayPal payment failed. Please try again or use another payment method.');
    }

    onCancel() {
        this.hideLoadingMask();
        console.log('PayPal payment cancelled by user');
    }

    validateForm() {
        const form = document.getElementById('firecheckout-form') ||
            document.getElementById('co-payment-form');

        if (!form) return false;

        const isValid = form.checkValidity();
        if (!isValid) {
            form.reportValidity();
        }

        if (typeof Validation !== 'undefined') {
            const validator = new Validation(form);
            return validator.validate() && isValid;
        }

        return isValid;
    }

    setupPaymentMethodHandling() {
        const paymentInputs = document.querySelectorAll('input[name="payment[method]"]');

        paymentInputs.forEach(input => {
            input.addEventListener('change', () => {
                this.handlePaymentMethodChange(input.value);
            }, { signal: this.abortController.signal });
        });

        document.addEventListener('payment-method:switched', (event) => {
            const methodCode = event.detail?.method_code;
            if (methodCode) {
                this.handlePaymentMethodChange(methodCode);
            }
        }, { signal: this.abortController.signal });

        const currentMethod = this.getCurrentPaymentMethod();
        if (currentMethod) {
            this.handlePaymentMethodChange(currentMethod);
        }
    }

    handlePaymentMethodChange(selectedMethod) {
        const reviewContainer = document.getElementById(this.config.reviewButtonContainerId);
        if (!reviewContainer) return;

        this.buttonInitialized = false;
        reviewContainer.remove();
    }

    removePayPalButton() {
        const container = document.getElementById(this.config.containerId);
        if (container) {
            container.remove();
            this.buttonInitialized = false;
        }
    }

    showLoadingMask() {
        let mask = document.getElementById('loading-mask');

        if (!mask) {
            mask = document.createElement('div');
            mask.id = 'loading-mask';
            mask.className = 'loading-mask';
            mask.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            `;

            const loader = document.createElement('div');
            loader.className = 'loading-mask-loader';
            loader.style.cssText = 'color: white; font-size: 18px;';
            loader.textContent = 'Processing...';

            mask.appendChild(loader);
            document.body.appendChild(mask);
        }

        mask.style.display = 'flex';
    }

    hideLoadingMask() {
        const mask = document.getElementById('loading-mask');
        if (mask) {
            mask.style.display = 'none';
        }
    }

    showError(message) {
        const errorId = `${this.config.containerId}-error`;
        const existingError = document.getElementById(errorId);
        existingError?.remove();

        const container = document.getElementById(this.config.containerId);
        if (!container) return;

        const errorDiv = document.createElement('div');
        errorDiv.id = errorId;
        errorDiv.className = 'paypal-error';
        errorDiv.style.cssText = `
            color: red;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid red;
            background: #ffe6e6;
            border-radius: 4px;
        `;
        errorDiv.textContent = message || this.config.errorMessage || 'An error occurred. Please try again later.';

        container.after(errorDiv);

        setTimeout(() => errorDiv?.remove(), 10000);
    }

    destroy() {
        this.abortController.abort();

        if (this.reviewContainerInterval) {
            clearInterval(this.reviewContainerInterval);
            this.reviewContainerInterval = null;
        }

        this.removePayPalButton();

        const mask = document.getElementById('loading-mask');
        mask?.remove();
    }
}

if (typeof window !== 'undefined') {
    window.PayPalPayment = PayPalPayment;
}