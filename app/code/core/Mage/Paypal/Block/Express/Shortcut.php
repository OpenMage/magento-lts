<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Express shortcut button block.
 */
class Mage_Paypal_Block_Express_Shortcut extends Mage_Core_Block_Template
{
    private const CONTEXT_PRODUCT = 'product';

    private const CONTEXT_CART = 'cart';

    /**
     * Set the default shortcut template.
     */
    #[Override]
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/express/shortcut.phtml');
    }

    /**
     * Render only when the context-specific config and method availability allow it.
     */
    #[Override]
    protected function _toHtml(): string
    {
        if (!$this->isVisible()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Checks whether this shortcut should render.
     */
    public function isVisible(): bool
    {
        if ($this->isProductContext()) {
            $product = Mage::registry('current_product');
            return $product instanceof Mage_Catalog_Model_Product
                && $product->isSaleable()
                && Mage::helper('paypal')->isShortcutVisibleOnProduct();
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        return $quote->hasItems()
            && !$quote->getHasError()
            && Mage::helper('paypal')->isShortcutVisibleOnCart();
    }

    /**
     * Configure product or cart context from layout XML.
     */
    public function setShortcutContext(string $context): self
    {
        $this->setData('shortcut_context', $context);
        return $this;
    }

    /**
     * Return the shortcut context.
     */
    public function getShortcutContext(): string
    {
        return $this->getData('shortcut_context') === self::CONTEXT_PRODUCT
            ? self::CONTEXT_PRODUCT
            : self::CONTEXT_CART;
    }

    /**
     * Checks if this block renders on a product page.
     */
    public function isProductContext(): bool
    {
        return $this->getShortcutContext() === self::CONTEXT_PRODUCT;
    }

    /**
     * Return a stable DOM container id.
     */
    public function getContainerId(): string
    {
        return 'paypal-express-shortcut-' . $this->getShortcutContext();
    }

    /**
     * Retrieves the PayPal JavaScript SDK URL.
     */
    public function getSdkUrl(): string
    {
        $intent = Mage::getSingleton('paypal/config')->getPaymentAction();
        $params = [
            'client-id' => Mage::helper('paypal')->getConfig()->getApiCredentials()['client_id'],
            'components' => 'buttons',
            'intent' => (string) $intent,
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
        ];
        if (Mage::getSingleton('paypal/config')->isDebugEnabled()) {
            $params['debug'] = 'true';
        }

        $baseUrl = Mage::helper('paypal')->getConfig()->getEndpoint();
        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }

        return $baseUrl . 'sdk/js?' . http_build_query($params);
    }

    /**
     * Retrieves the shortcut button style configuration.
     *
     * @return array<string, bool|string>
     */
    public function getButtonConfig(): array
    {
        $config = Mage::helper('paypal')->getButtonConfig();
        $config['label'] = $this->isProductContext()
            ? Mage_Paypal_Model_Config::BUTTON_LABEL_BUYNOW
            : Mage_Paypal_Model_Config::BUTTON_LABEL_CHECKOUT;

        return $config;
    }

    /**
     * JavaScript configuration for PayPalShortcut.
     *
     * @return array<string, mixed>
     */
    public function getShortcutConfig(): array
    {
        $buttonConfig = $this->getButtonConfig();

        return [
            'context' => $this->getShortcutContext(),
            'containerId' => $this->getContainerId(),
            'formId' => $this->isProductContext() ? 'product_addtocart_form' : null,
            'formKey' => $this->getFormKey(),
            'sdkUrl' => $this->getSdkUrl(),
            'startUrl' => $this->getUrl('paypal/express/start'),
            'reviewUrl' => $this->getUrl('paypal/express/review'),
            'cancelUrl' => $this->getUrl('paypal/express/cancel'),
            'buttonLayout' => $buttonConfig['layout'],
            'buttonColor' => $buttonConfig['color'],
            'buttonShape' => $buttonConfig['shape'],
            'buttonLabel' => $buttonConfig['label'],
            'buttonMessage' => (bool) $buttonConfig['message'],
            'errorMessage' => $this->__('An error occurred while processing your PayPal checkout. Please try again.'),
            'validationMessage' => $this->__('Please specify the required product options.'),
        ];
    }
}
