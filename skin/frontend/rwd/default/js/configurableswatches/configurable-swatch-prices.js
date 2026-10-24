/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

function ConfigurableSwatchPrices() { this.initialize(...arguments); }

ConfigurableSwatchPrices.prototype = {
    initialize: function(config) {
        this.swatchesPrices = [];
        this.generalConfig = config.generalConfig;
        this.products = config.products;

        this.addObservers();
    },

    addObservers: function() {
        document.addEventListener('click', this.onSwatchClick.bind(this));
    },

    onSwatchClick: function(e) {
        const element = e.target.closest('.swatch-link');
        if (!element) return;
        const swatchElement = element.closest('[data-product-id]');
        if (!swatchElement) return;
        const productId = parseInt(swatchElement.getAttribute('data-product-id'), 10);
        const swatchLabel = swatchElement.getAttribute('data-option-label');
        const optionsPrice = this.optionsPrice(productId);
        const swatchTarget = this.getSwatchPriceInfo(productId, swatchLabel);

        if (swatchTarget) {
            optionsPrice.changePrice('config', {price: swatchTarget.price, oldPrice: swatchTarget.oldPrice});
            optionsPrice.reload();
        }
    },

    getSwatchPriceInfo: function(productId, swatchLabel) {
        const productInfo = this.products[productId];
        if (productInfo && productInfo.swatchPrices[swatchLabel]) {
            return productInfo.swatchPrices[swatchLabel];
        }
        return 0;
    },

    optionsPrice: function(productId) {
        if (this.swatchesPrices[productId]) {
            return this.swatchesPrices[productId];
        }
        this.swatchesPrices[productId] = new Product.OptionsPrice(this.getProductConfig(productId));
        return this.swatchesPrices[productId];
    },

    getProductConfig: function(productId) {
        return Object.assign({}, this.generalConfig, this.products[productId]);
    }
};
Varien.classCompat(ConfigurableSwatchPrices);
