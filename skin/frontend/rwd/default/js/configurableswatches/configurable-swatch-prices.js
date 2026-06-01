/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

function ConfigurableSwatchPrices(config) {
    this.swatchesPrices = [];
    this.generalConfig = config.generalConfig;
    this.products = config.products;

    this.addObservers();
}

ConfigurableSwatchPrices.prototype = {
    addObservers: function() {
        document.addEventListener('click', this.onSwatchClick.bind(this));
    },

    onSwatchClick: function(e) {
        var element = e.target.closest('.swatch-link');
        if (!element) return;
        var swatchElement = element.closest('[data-product-id]');
        if (!swatchElement) return;
        var productId = parseInt(swatchElement.getAttribute('data-product-id'), 10);
        var swatchLabel = swatchElement.getAttribute('data-option-label');
        var optionsPrice = this.optionsPrice(productId);
        var swatchTarget = this.getSwatchPriceInfo(productId, swatchLabel);

        if (swatchTarget) {
            optionsPrice.changePrice('config', {price: swatchTarget.price, oldPrice: swatchTarget.oldPrice});
            optionsPrice.reload();
        }
    },

    getSwatchPriceInfo: function(productId, swatchLabel) {
        var productInfo = this.products[productId];
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
