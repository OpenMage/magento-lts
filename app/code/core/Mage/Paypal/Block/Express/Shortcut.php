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
     * Settings for rendering the PayPal SDK v6 buttons; the label fits the page instead of the configured one.
     *
     * @return array<string, bool|string>
     */
    public function getSdkConfig(): array
    {
        $isProduct = $this->isProductContext();
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        return Mage::helper('paypal')->getConfig()->getSdkConfig(
            $isProduct ? Mage_Paypal_Model_Config::PAGE_TYPE_PRODUCT : Mage_Paypal_Model_Config::PAGE_TYPE_CART,
            Mage::app()->getStore()->getCurrentCurrencyCode(),
            $isProduct ? null : (float) $quote->getGrandTotal(),
            $isProduct ? Mage_Paypal_Model_Config::BUTTON_LABEL_BUYNOW : Mage_Paypal_Model_Config::BUTTON_LABEL_CHECKOUT,
        );
    }

    /**
     * JavaScript configuration for PayPalShortcut.
     *
     * @return array<string, mixed>
     */
    public function getShortcutConfig(): array
    {
        return [
            'context' => $this->getShortcutContext(),
            'containerId' => $this->getContainerId(),
            'formId' => $this->isProductContext() ? 'product_addtocart_form' : null,
            'formKey' => $this->getFormKey(),
            'sdk' => $this->getSdkConfig(),
            'startUrl' => $this->getUrl('paypal/express/start'),
            'reviewUrl' => $this->getUrl('paypal/express/review'),
            'cancelUrl' => $this->getUrl('paypal/express/cancel'),
            'errorMessage' => $this->__('An error occurred while processing your PayPal checkout. Please try again.'),
            'validationMessage' => $this->__('Please specify the required product options.'),
        ];
    }
}
