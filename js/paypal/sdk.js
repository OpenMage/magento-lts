/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2026 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Loads the PayPal JavaScript SDK v6 once per page and renders the eligible
 * PayPal, Pay Later and PayPal Credit buttons into a container.
 */
class PayPalSdk {
    static scriptPromise = null;

    static instancePromises = new Map();

    static BUTTONS = [
        { source: 'paypal', tag: 'paypal-button', session: 'createPayPalOneTimePaymentSession' },
        { source: 'paylater', tag: 'paypal-pay-later-button', session: 'createPayLaterOneTimePaymentSession', option: 'payLaterEnabled' },
        { source: 'credit', tag: 'paypal-credit-button', session: 'createPayPalCreditOneTimePaymentSession', option: 'creditEnabled' }
    ];

    static loadScript(url) {
        if (window.paypal?.createInstance) {
            return Promise.resolve();
        }

        if (!PayPalSdk.scriptPromise) {
            PayPalSdk.scriptPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = url;
                script.async = true;
                script.onload = () => resolve();
                script.onerror = () => {
                    // Let a later render retry instead of failing on the cached rejection forever.
                    PayPalSdk.scriptPromise = null;
                    script.remove();
                    reject(new Error('Failed to load PayPal SDK'));
                };
                document.head.appendChild(script);
            });
        }

        return PayPalSdk.scriptPromise;
    }

    /**
     * @param {object} config sdkUrl, clientId, pageType, locale
     */
    static getInstance(config) {
        const key = `${config.clientId}|${config.pageType}`;
        if (!PayPalSdk.instancePromises.has(key)) {
            const promise = PayPalSdk.loadScript(config.sdkUrl).then(() => {
                const options = {
                    clientId: config.clientId,
                    components: ['paypal-payments'],
                    pageType: config.pageType
                };
                if (config.locale) {
                    options.locale = config.locale;
                }

                return window.paypal.createInstance(options);
            });
            promise.catch(() => PayPalSdk.instancePromises.delete(key));
            PayPalSdk.instancePromises.set(key, promise);
        }

        return PayPalSdk.instancePromises.get(key);
    }

    /**
     * Render the buttons the buyer is eligible for.
     *
     * The click handler calls handlers.validate() synchronously and starts the session right away with the
     * pending handlers.createOrder() promise: awaiting anything first loses the click's user activation and
     * the browser blocks the PayPal window.
     *
     * @param {HTMLElement} container
     * @param {object} config sdkUrl, clientId, pageType, locale, currencyCode, amount, buttonType,
     *                        payLaterEnabled, creditEnabled
     * @param {object} handlers validate(), createOrder(source) => Promise<{orderId}>, onApprove(data),
     *                          onCancel(data), onError(error)
     * @returns {Promise<{destroy: Function}>}
     */
    static async renderButtons(container, config, handlers) {
        const instance = await PayPalSdk.getInstance(config);
        const eligibilityOptions = { currencyCode: config.currencyCode };
        if (config.amount) {
            eligibilityOptions.amount = config.amount;
        }
        const methods = await instance.findEligibleMethods(eligibilityOptions);

        const sessions = [];
        const elements = [];
        PayPalSdk.BUTTONS.forEach(button => {
            if ((button.option && !config[button.option]) || !methods.isEligible(button.source)) {
                return;
            }

            const element = document.createElement(button.tag);
            if (button.source === 'paypal' && config.buttonType) {
                element.type = config.buttonType;
            } else if (button.source !== 'paypal') {
                const details = methods.getDetails(button.source) || {};
                element.countryCode = details.countryCode;
                if (details.productCode) {
                    element.productCode = details.productCode;
                }
            }

            const session = instance[button.session]({
                onApprove: data => handlers.onApprove(data),
                onCancel: data => handlers.onCancel(data),
                onError: error => handlers.onError(error)
            });

            element.addEventListener('click', () => {
                if (!handlers.validate()) {
                    return;
                }

                session.start({ presentationMode: 'auto' }, handlers.createOrder(button.source))
                    .catch(error => {
                        if (!error?.handled) {
                            handlers.onError(error);
                        }
                    });
            });

            sessions.push(session);
            elements.push(element);
        });

        if (elements.length === 0) {
            throw new Error('PayPal is not available for this purchase.');
        }

        elements.forEach(element => container.appendChild(element));

        return {
            destroy() {
                sessions.forEach(session => session.destroy());
                elements.forEach(element => element.remove());
            }
        };
    }
}

if (typeof window !== 'undefined') {
    window.PayPalSdk = PayPalSdk;
}
