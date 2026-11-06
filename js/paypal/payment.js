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

class PayPalPayment {
    constructor(config = {}) {
        this.config = config;
        this.buttonInitialized = false;
        this.buttons = null;
        this.renderPromise = null;
        this.reviewContainerInterval = null;
        this.abortController = new AbortController();

        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.start(), { once: true });
            return;
        }

        this.start();
    }

    start() {
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
                this.handlePaymentMethodChange(this.config.methodCode);
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
            this.initializePayPalButton().catch(error => console.error('PayPal button error:', error));
        }
    }

    getCurrentPaymentMethod() {
        const checkedInput = document.querySelector('input[name="payment[method]"]:checked');
        return checkedInput?.value ?? null;
    }

    async initializePayPalButton() {
        if (this.buttonInitialized) return;

        try {
            await this.renderButton();
        } catch (error) {
            this.showError('Failed to load PayPal. Please try another payment method.');
        }
    }

    async renderButton() {
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
        // Right-align the stacked buttons so they line up with the totals
        // column instead of floating mid-page.
        container.style.cssText = 'display: flex; flex-direction: column; gap: 10px; margin: 0 0 0 auto; min-height: 35px; max-width: 750px;';
        return container;
    }

    async createPayPalButton(paypalContainer, reviewContainer) {
        this.buttons = await PayPalSdk.renderButtons(paypalContainer, this.config.sdk, {
            validate: () => this.validateForm(),
            createOrder: (source) => this.createOrder(source),
            onApprove: (data) => this.onApprove(data),
            onCancel: () => this.onCancel(),
            onError: (error) => this.onButtonError(error)
        });

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

    /**
     * @param {string} source the clicked button: paypal, paylater or credit
     * @returns {Promise<{orderId: string}>}
     */
    async createOrder(source) {
        this.showLoadingMask();

        try {
            const requestBody = new URLSearchParams({
                form_key: this.config.formKey,
                funding_source: source
            });

            this.collectAgreementParams().forEach(([name, value]) => {
                requestBody.append(name, value);
            });

            const response = await fetch(this.config.createOrderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: requestBody,
                signal: this.abortController.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            // The cart was already paid for and its order has now been placed: go there instead of paying again.
            if (result?.redirect_url) {
                window.location.href = result.redirect_url;
                return new Promise(() => {});
            }

            if (!result?.success || !result?.id) {
                throw new Error(result?.message || 'Failed to create PayPal order');
            }

            return { orderId: result.id };
        } catch (error) {
            console.error('Create order error:', error);
            this.showError(error.message);
            this.hideLoadingMask();
            error.handled = true;
            throw error;
        } finally {
            this.hideLoadingMask();
        }
    }

    async onApprove(data) {
        this.showLoadingMask();

        try {
            const requestBody = new URLSearchParams({
                order_id: data.orderId,
                form_key: this.config.formKey
            });

            this.collectAgreementParams().forEach(([name, value]) => {
                requestBody.append(name, value);
            });

            const response = await fetch(this.config.captureUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: requestBody,
                signal: this.abortController.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            // The server has charged the buyer and placed the order in that one request; all that is left
            // is to show the result.
            if (!result?.success || !result?.redirect_url) {
                throw new Error(result?.message || 'Payment capture failed');
            }

            window.location.href = result.redirect_url;
        } catch (error) {
            // Not rethrown: the SDK would report it through onError, which cancels and replaces this message.
            console.error('Payment approval error:', error);
            this.showError(error.message);
            this.hideLoadingMask();
        }
    }

    /**
     * Collect accepted checkout agreement fields so they can be carried into
     * the PayPal-only create-order and capture requests, which bypass the
     * normal review.save() submission.
     *
     * @returns {Array<[string, string]>}
     */
    collectAgreementParams() {
        const params = [];
        document.querySelectorAll('input[name^="agreement["]').forEach(input => {
            if ((input.type === 'checkbox' || input.type === 'radio') && !input.checked) {
                return;
            }
            params.push([input.name, input.value]);
        });
        return params;
    }

    onButtonError(error) {
        console.error('PayPal button error:', error);
        this.onCancel();
        this.showError(error?.message);
    }

    onCancel() {
        this.hideLoadingMask();
        fetch(this.config.cancelUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                form_key: this.config.formKey
            }),
            signal: this.abortController.signal
        }).catch(err => console.error('Cancel order error:', err));
        this.showError('PayPal payment cancelled by user.');
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

        if (selectedMethod !== this.config.methodCode) {
            this.removePayPalButton();
            this.recreateCheckoutButton(reviewContainer);
            return;
        }

        this.buttonInitialized = false;
        reviewContainer.remove();
    }

    removePayPalButton() {
        this.buttons?.destroy();
        this.buttons = null;

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
