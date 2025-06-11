<?php

/**
 * PayPal button block
 */
class Mage_Paypal_Block_Button extends Mage_Core_Block_Template
{
    /**
     * Get PayPal button configuration
     *
     * @return array
     */
    public function getButtonConfig()
    {
        return Mage::helper('paypal')->getButtonConfig();
    }

    /**
     * Get PayPal client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return Mage::helper('paypal')->getConfig()->getApiCredentials()['client_id'];
    }

    /**
     * Get PayPal SDK URL
     *
     * @return string
     */
    public function getSdkUrl()
    {
        $config = $this->getButtonConfig();
        $intent = Mage::getSingleton('paypal/config')->getPaymentAction();
    
        $params = [
            'client-id' => $this->getClientId(),
            'components' => 'buttons,messages',
            'intent' => $intent,
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()
        ];

        if ($config['message']) {
            $params['enable-funding'] = 'paylater';
        }

        return 'https://www.paypal.com/sdk/js?' . http_build_query($params);
    }

    /**
     * Get form key for CSRF protection
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }
    
    /**
     * Check if button should be rendered
     *
     * @return bool
     */
    public function shouldRender()
    {
        return Mage::helper('paypal')->isAvailable()
            && $this->getClientId();
    }

    /**
     * Get button container ID
     *
     * @return string
     */
    public function getContainerId()
    {
        return 'paypal-button-container-' . $this->getNameInLayout();
    }

    /**
     * Get button container attributes
     *
     * @return string
     */
    public function getContainerAttributes()
    {
        return sprintf(
            'id="%s" class="paypal-button-container" data-loading="false" style="min-height: 35px;"',
            $this->getContainerId()
        );
    }

    /**
     * Get button initialization script
     *
     * @return string
     */
    public function getButtonScript()
    {
        $config = $this->getButtonConfig();
        $containerId = $this->getContainerId();
        
        return sprintf(
            'window.paypalLoadPromise = window.paypalLoadPromise || new Promise(function(resolve) {
                var script = document.createElement("script");
                script.src = "%s";
                script.onload = resolve;
                document.body.appendChild(script);
            });

            window.paypalLoadPromise.then(function() {
                var container = document.getElementById("%s");
                container.setAttribute("data-loading", "true");
                
                try {
                    paypal.Buttons({
                        style: {
                            layout: "%s",
                            color: "%s",
                            shape: "%s",
                            label: "%s"
                        },
                        createOrder: function() {
                            container.setAttribute("data-loading", "true");
                            return fetch("%s", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                }
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                if (!data.success) {
                                    throw new Error(data.error || "Error creating PayPal order");
                                }
                                return data.order_id;
                            });
                        },
                        onApprove: function(data) {
                            container.setAttribute("data-loading", "true");
                            return fetch("%s", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({
                                    order_id: data.orderID
                                })
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                if (!data.success) {
                                    throw new Error(data.error || "Error processing payment");
                                }
                                window.location.href = "%s";
                            });
                        },
                        onError: function(err) {
                            console.error("PayPal Error:", err);
                            alert("There was an error with PayPal. Please try again.");
                            container.setAttribute("data-loading", "false");
                        }
                    }).render("#%s")
                    .catch(function(err) {
                        console.error("PayPal Render Error:", err);
                        container.innerHTML = "<div class=\'error\'>Could not load PayPal button</div>";
                        container.setAttribute("data-loading", "false");
                    });
                } catch (err) {
                    console.error("PayPal Init Error:", err);
                    container.innerHTML = "<div class=\'error\'>Could not initialize PayPal</div>";
                    container.setAttribute("data-loading", "false");
                }
            });',
            $this->getSdkUrl(),
            $containerId,
            $config['layout'],
            $config['color'],
            $config['shape'],
            $config['label'],
            $this->getUrl('paypal/payment/createOrder'),
            $this->getUrl('paypal/payment/capture'),
            $this->getUrl('checkout/onepage/success'),
            $containerId
        );
    }
}
