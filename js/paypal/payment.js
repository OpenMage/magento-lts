/**
 * Optimized PayPal payment integration
 */
var PayPalPayment = Class.create();
PayPalPayment.prototype = {
    initialize: function (config) {
        this.config = config || {};
        this.buttonInitialized = false;
        this.sdkLoaded = false;
        this.renderPromise = null;
        this.reviewContainerInterval = null;

        // Initialize on DOM ready
        document.readyState === 'loading'
            ? document.observe('dom:loaded', this.onDOMReady.bind(this))
            : this.onDOMReady();
    },

    onDOMReady: function () {
        this.setupPaymentMethodHandling();
        this.setupPaymentButtonHandling();
        this.checkAndInitialize();
    },

    setupPaymentButtonHandling: function () {
        const paymentButton = $$('button[onclick="payment.save()"]')[0];
        if (paymentButton) {
            paymentButton.observe('click', () => {
                if (this.getCurrentPaymentMethod() === this.config.methodCode) {
                    this.waitForReviewContainer().then(() => this.init());
                }
            });
        }
    },

    waitForReviewContainer: function () {
        return new Promise(resolve => {
            // Clear existing interval
            if (this.reviewContainerInterval) {
                clearInterval(this.reviewContainerInterval);
                this.reviewContainerInterval = null;
            }

            const maxAttempts = 20; // 10 seconds max
            let attempts = 0;

            const checkForContainer = () => {
                attempts++;
                const reviewContainer = $(this.config.reviewButtonContainerId);

                if (reviewContainer || attempts >= maxAttempts) {
                    if (this.reviewContainerInterval) {
                        clearInterval(this.reviewContainerInterval);
                        this.reviewContainerInterval = null;
                    }
                    resolve(reviewContainer);
                }
            };

            // Initial check
            checkForContainer();

            // Set interval if not found
            if (!$(this.config.reviewButtonContainerId)) {
                this.reviewContainerInterval = setInterval(checkForContainer, 500);
            }
        });
    },

    checkAndInitialize: function () {
        this.getCurrentPaymentMethod() === this.config.methodCode && this.init();
    },

    getCurrentPaymentMethod: function () {
        const checkedInput = $$('input[name="payment[method]"]:checked')[0];
        return checkedInput ? checkedInput.value : null;
    },

    init: function () {
        if (this.buttonInitialized) return Promise.resolve();

        return this.loadPayPalSDK()
            .then(() => this.renderButton())
            .catch(() => { });
    },

    debugCheckEnvironment: function () {
        // Removed debug info
    },

    loadPayPalSDK: function () {
        return new Promise((resolve, reject) => {
            // Check if SDK is already loaded
            if (typeof paypal !== 'undefined') {
                this.sdkLoaded = true;
                return resolve();
            }

            const script = document.createElement('script');
            script.src = this.config.sdkUrl;
            script.async = true;
            script.onload = () => {
                this.sdkLoaded = true;
                resolve();
            };
            script.onerror = reject;
            document.body.appendChild(script);
        });
    },

    renderButton: function () {
        // Reset previous render operation
        this.renderPromise = null;

        if (!this.sdkLoaded) {
            return Promise.reject(new Error('SDK not loaded'));
        }

        const reviewContainer = $(this.config.reviewButtonContainerId);
        if (!reviewContainer) {
            return Promise.reject(new Error('Review container not found'));
        }

        // Remove existing PayPal button
        this.removePayPalButton();

        // Find the existing checkout button
        const checkoutButton = reviewContainer.down('button.btn-checkout');

        // Create PayPal button container
        const paypalContainer = new Element('div', {
            'id': this.config.containerId,
            'class': 'paypal-button-container',
            'style': 'margin: 0; min-height: 35px;'
        });

        if (!checkoutButton) {
            // Insert at the beginning of the container
            reviewContainer.insert({ top: paypalContainer });
        } else {
            // Remove the original checkout button
            checkoutButton.remove();

            // Insert PayPal container in the same position
            const pleaseWaitSpan = reviewContainer.down('span.please-wait');
            pleaseWaitSpan
                ? pleaseWaitSpan.insert({ before: paypalContainer })
                : reviewContainer.insert({ top: paypalContainer });
        }

        return this.createPayPalButton(paypalContainer, reviewContainer);
    },

    createPayPalButton: function (paypalContainer, reviewContainer) {
        this.renderPromise = new Promise((resolve, reject) => {
            try {
                // Create button wrapper
                const buttonWrapper = new Element('div', { 'class': 'paypal-button-wrapper' });
                paypalContainer.insert(buttonWrapper);

                // Render PayPal button
                paypal.Buttons({
                    style: {
                        layout: this.config.buttonLayout || 'vertical',
                        color: this.config.buttonColor || 'gold',
                        shape: this.config.buttonShape || 'rect',
                        label: this.config.buttonLabel || 'paypal',
                        height: this.config.buttonHeight || 40
                    },
                    createOrder: () => this.createOrder(),
                    onApprove: data => this.onApprove(data),
                    onError: error => this.onButtonError(error),
                    onCancel: data => this.onCancel(data)
                }).render(buttonWrapper)
                    .then(() => {
                        this.buttonInitialized = true;
                        resolve();
                    })
                    .catch(error => {
                        this.handleRenderError(error, reviewContainer, paypalContainer);
                        reject(error);
                    });
            } catch (error) {
                this.handleRenderError(error, reviewContainer, paypalContainer);
                reject(error);
            }
        });

        return this.renderPromise;
    },

    handleRenderError: function (error, reviewContainer, paypalContainer) {
        // Clean up PayPal container
        paypalContainer?.parentNode && paypalContainer.remove();

        // Recreate the original checkout button
        this.recreateCheckoutButton(reviewContainer);
        this.buttonInitialized = false;
    },

    recreateCheckoutButton: function (reviewContainer) {
        // Skip if button already exists
        if (reviewContainer.down('button.btn-checkout')) return;

        // Create checkout button
        const checkoutButton = new Element('button', {
            'type': 'submit',
            'title': 'Place Order',
            'class': 'button btn-checkout',
            'onclick': 'review.save();'
        }).insert(
            new Element('span').insert(
                new Element('span').update('Place Order')
            )
        );

        // Insert button
        const pleaseWaitSpan = reviewContainer.down('span.please-wait');
        pleaseWaitSpan
            ? pleaseWaitSpan.insert({ before: checkoutButton })
            : reviewContainer.insert({ top: checkoutButton });
    },

    createOrder: function () {
        this.showLoadingMask();

        return new Promise((resolve, reject) => {
            if (!this.validateForm()) {
                this.hideLoadingMask();
                return reject(new Error('Form validation failed'));
            }
            new Ajax.Request(this.config.createOrderUrl, {
                method: 'post',
                parameters: { form_key: this.config.formKey },
                onSuccess: response => {
                    const result = response.responseJSON;
                    result?.success && result.id
                        ? resolve(result.id)
                        : reject(new Error(result?.message || 'Failed to create PayPal order'));
                },
                onFailure: () => reject(new Error('Failed to create PayPal order')),
                onComplete: () => this.hideLoadingMask()
            });
        });
    },

    onApprove: function (data) {
        this.showLoadingMask();
        return new Promise((resolve, reject) => {
            new Ajax.Request(this.config.captureUrl, {
                method: 'post',
                parameters: { order_id: data.orderID, form_key: this.config.formKey },
                onSuccess: response => {
                    const result = response.responseJSON;
                    if (result?.success) {
                        this.submitPlaceOrderForm(data.orderID);
                        resolve(result);
                    } else {
                        this.showError(result?.message);
                        this.hideLoadingMask();
                        reject(new Error('Payment capture failed'));
                    }
                },
                onFailure: () => {
                    this.showError();
                    this.hideLoadingMask();
                    reject(new Error('Payment capture failed'));
                }
            });
        });
    },

    submitPlaceOrderForm: function (orderId) {
        const form = new Element('form', {
            method: 'post',
            action: this.config.placeOrderUrl,
            style: 'display: none'
        });

        form.insert(new Element('input', {
            type: 'hidden',
            name: 'id',
            value: orderId
        }));

        form.insert(new Element('input', {
            type: 'hidden',
            name: 'form_key',
            value: this.config.formKey
        }));

        document.body.appendChild(form);
        form.submit();
    },

    onButtonError: function () {
        this.showError();
    },

    onCancel: function () {
        this.hideLoadingMask();
    },

    validateForm: function () {
        const form = $('firecheckout-form') || $('co-payment-form');
        if (!form) return false;

        const validator = new Validation(form);
        return validator.validate();
    },

    setupPaymentMethodHandling: function () {
        // Handle payment method selection
        $$('input[name="payment[method]"]').each(input => {
            input.observe('click', () => this.handlePaymentMethodChange(input.value));
        });

        // Handle review section updates
        document.observe('payment-method:switched', event => {
            const methodCode = event.memo?.method_code;
            methodCode && this.handlePaymentMethodChange(methodCode);
        });

        // Check initial payment method
        const currentMethod = this.getCurrentPaymentMethod();
        currentMethod && this.handlePaymentMethodChange(currentMethod);
    },

    handlePaymentMethodChange: function (selectedMethod) {
        const reviewContainer = $(this.config.reviewButtonContainerId);
        if (!reviewContainer) return;

        this.buttonInitialized = false;
        reviewContainer.remove();
    },

    removePayPalButton: function () {
        const container = $(this.config.containerId);
        if (container) {
            container.remove();
            this.buttonInitialized = false;
        }
    },

    showLoadingMask: function () {
        let mask = $('loading-mask');
        if (!mask) {
            mask = new Element('div', {
                'id': 'loading-mask',
                'class': 'loading-mask',
                'style': 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;'
            }).insert(
                new Element('div', {
                    'class': 'loading-mask-loader',
                    'style': 'color: white; font-size: 18px;'
                }).update('Processing...')
            );
            document.body.appendChild(mask);
        }
        mask.show();
    },

    hideLoadingMask: function () {
        $('loading-mask')?.hide();
    },

    showError: function (message) {
        // Remove existing error
        const errorId = this.config.containerId + '-error';
        $(errorId)?.remove();

        // Create new error element
        const container = $(this.config.containerId);
        if (container) {
            const errorDiv = new Element('div', {
                'id': errorId,
                'class': 'paypal-error',
                'style': 'color: red; margin: 10px 0; padding: 10px; border: 1px solid red; background: #ffe6e6;'
            });
            container.insert({ after: errorDiv });
            errorDiv.update(message || this.config.errorMessage || 'An error occurred. Please try again later.');
        }
    },

    log: function () {
        // Removed logging
    },

    destroy: function () {
        // Remove event listeners
        $$('input[name="payment[method]"]').each(input => {
            input.stopObserving('click');
        });

        // Remove payment button observer
        const paymentButton = $$('button[onclick="payment.save()"]')[0];
        paymentButton?.stopObserving('click');

        // Clear interval
        if (this.reviewContainerInterval) {
            clearInterval(this.reviewContainerInterval);
            this.reviewContainerInterval = null;
        }

        // Clean up containers
        this.removePayPalButton();
    }
};