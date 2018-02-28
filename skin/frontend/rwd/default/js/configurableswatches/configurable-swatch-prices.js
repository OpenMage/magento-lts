/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var ConfigurableSwatchPrices = Class.create({
    initialize: function(config) {
        this.swatchesPrices = [];
        this.generalConfig = config.generalConfig;
        this.products = config.products;

        this.addObservers();
    },

    addObservers: function() {
        $(document).on('click', '.swatch-link', this.onSwatchClick.bind(this));
    },

    onSwatchClick: function(e) {
        var element = Event.findElement(e);
        var swatchElement = element.up('[data-product-id]');
        var productId = parseInt(swatchElement.getAttribute('data-product-id'), 10);
        var swatchLabel = swatchElement.getAttribute('data-option-label');
        var optionsPrice = this.optionsPrice(productId);
        var swatchTarget = this.getSwatchPriceInfo(productId, swatchLabel);

        if(swatchTarget) {
            optionsPrice.changePrice('config', {price: swatchTarget.price, oldPrice: swatchTarget.oldPrice});
            optionsPrice.reload();
        }
    },

    getSwatchPriceInfo: function(productId, swatchLabel) {
        var productInfo = this.products[productId];
        if(productInfo && productInfo.swatchPrices[swatchLabel]) {
            return productInfo.swatchPrices[swatchLabel];
        }
        return 0;
    },

    optionsPrice: function(productId) {
        if(this.swatchesPrices[productId]) {
            return this.swatchesPrices[productId];
        }
        this.swatchesPrices[productId] = new Product.OptionsPrice(this.getProductConfig(productId));

        return this.swatchesPrices[productId];
    },

    getProductConfig: function(productId) {
        var generalConfigClone = Object.extend({}, this.generalConfig);

        return Object.extend(generalConfigClone, this.products[productId]);
    }
});
