/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

var Product = Product || {};

/**************************** PRICE RELOADER ********************************/
Product.OptionsPrice = function (config) {
    this.productId          = config.productId;
    this.priceFormat        = config.priceFormat;
    this.includeTax         = config.includeTax;
    this.defaultTax         = config.defaultTax;
    this.currentTax         = config.currentTax;
    this.productPrice       = config.productPrice;
    this.showIncludeTax     = config.showIncludeTax;
    this.showBothPrices     = config.showBothPrices;
    this.productOldPrice    = config.productOldPrice;
    this.priceInclTax       = config.priceInclTax;
    this.priceExclTax       = config.priceExclTax;
    this.skipCalculate      = config.skipCalculate; /** @deprecated after 1.5.1.0 */
    this.duplicateIdSuffix  = config.idSuffix;
    this.specialTaxPrice    = config.specialTaxPrice;
    this.tierPrices         = config.tierPrices;
    this.tierPricesInclTax  = config.tierPricesInclTax;

    this.oldPlusDisposition  = config.oldPlusDisposition;
    this.plusDisposition     = config.plusDisposition;
    this.plusDispositionTax  = config.plusDispositionTax;

    this.oldMinusDisposition = config.oldMinusDisposition;
    this.minusDisposition    = config.minusDisposition;

    this.exclDisposition     = config.exclDisposition;

    this.optionPrices   = {};
    this.customPrices   = {};
    this.containers     = {};

    this.displayZeroPrice = true;

    this.initPrices();
};

Product.OptionsPrice.prototype = {
    setDuplicateIdSuffix: function (idSuffix) {
        this.duplicateIdSuffix = idSuffix;
    },

    initPrices: function () {
        this.containers[0] = 'product-price-' + this.productId;
        this.containers[1] = 'bundle-price-' + this.productId;
        this.containers[2] = 'price-including-tax-' + this.productId;
        this.containers[3] = 'price-excluding-tax-' + this.productId;
        this.containers[4] = 'old-price-' + this.productId;
    },

    changePrice: function (key, price) {
        this.optionPrices[key] = price;
    },

    addCustomPrices: function (key, price) {
        this.customPrices[key] = price;
    },

    getOptionPrices: function () {
        var price = 0;
        var nonTaxable = 0;
        var oldPrice = 0;
        var priceInclTax = 0;
        var currentTax = this.currentTax;
        var optionPrices = this.optionPrices;

        Object.keys(optionPrices).forEach(function (key) {
            var value = optionPrices[key];
            if ('undefined' != typeof(value.price) && 'undefined' != typeof(value.oldPrice)) {
                price += parseFloat(value.price);
                oldPrice += parseFloat(value.oldPrice);
            } else if (key == 'nontaxable') {
                nonTaxable = value;
            } else if (key == 'priceInclTax') {
                priceInclTax += value;
            } else if (key == 'optionsPriceInclTax') {
                priceInclTax += value * (100 + currentTax) / 100;
            } else {
                price += parseFloat(value);
                oldPrice += parseFloat(value);
            }
        });

        return [price, nonTaxable, oldPrice, priceInclTax];
    },

    reload: function () {
        var self = this;
        var price;
        var formattedPrice;
        var optionPrices = this.getOptionPrices();
        var nonTaxable = optionPrices[1];
        var optionOldPrice = optionPrices[2];
        var priceInclTax = optionPrices[3];
        optionPrices = optionPrices[0];

        Object.keys(this.containers).forEach(function (key) {
            var containerId = self.containers[key];
            var _productPrice;
            var _plusDisposition;
            var _minusDisposition;
            var _priceInclTax;
            var excl;
            var incl;
            var tax;

            if (!document.getElementById(containerId)) {
                containerId = 'product-price-weee-' + self.productId;
            }

            var el = document.getElementById(containerId);
            if (el) {
                if (containerId == 'old-price-' + self.productId && self.productOldPrice != self.productPrice) {
                    _productPrice = self.productOldPrice;
                    _plusDisposition = self.oldPlusDisposition;
                    _minusDisposition = self.oldMinusDisposition;
                } else {
                    _productPrice = self.productPrice;
                    _plusDisposition = self.plusDisposition;
                    _minusDisposition = self.minusDisposition;
                }
                _priceInclTax = priceInclTax;

                if (containerId == 'old-price-' + self.productId && optionOldPrice !== undefined) {
                    price = optionOldPrice + parseFloat(_productPrice);
                } else if (self.specialTaxPrice == 'true' && self.priceInclTax !== undefined && self.priceExclTax !== undefined) {
                    price = optionPrices + parseFloat(self.priceExclTax);
                    _priceInclTax += self.priceInclTax;
                } else {
                    price = optionPrices + parseFloat(_productPrice);
                    _priceInclTax += parseFloat(_productPrice) * (100 + self.currentTax) / 100;
                }

                if (self.specialTaxPrice == 'true') {
                    excl = price;
                    incl = _priceInclTax;
                } else if (self.includeTax == 'true') {
                    // tax included into product price by admin
                    tax = price / (100 + self.defaultTax) * self.defaultTax;
                    excl = price - tax;
                    incl = excl * (1 + (self.currentTax / 100));
                } else {
                    tax = price * (self.currentTax / 100);
                    excl = price;
                    incl = excl + tax;
                }

                var subPrice = 0;
                var subPriceincludeTax = 0;
                Object.values(self.customPrices).forEach(function (el) {
                    if (el.excludeTax && el.includeTax) {
                        subPrice += parseFloat(el.excludeTax);
                        subPriceincludeTax += parseFloat(el.includeTax);
                    } else {
                        subPrice += parseFloat(el.price);
                        subPriceincludeTax += parseFloat(el.price);
                    }
                });
                excl += subPrice;
                incl += subPriceincludeTax;

                if (typeof self.exclDisposition == 'undefined') {
                    excl += parseFloat(_plusDisposition);
                }

                incl += parseFloat(_plusDisposition) + parseFloat(self.plusDispositionTax);
                excl -= parseFloat(_minusDisposition);
                incl -= parseFloat(_minusDisposition);

                // adding nontaxable part of options
                excl += parseFloat(nonTaxable);
                incl += parseFloat(nonTaxable);

                if (containerId == 'price-including-tax-' + self.productId) {
                    price = incl;
                } else if (containerId == 'price-excluding-tax-' + self.productId) {
                    price = excl;
                } else if (containerId == 'old-price-' + self.productId) {
                    price = (self.showIncludeTax || self.showBothPrices) ? incl : excl;
                } else {
                    price = self.showIncludeTax ? incl : excl;
                }

                if (price < 0) price = 0;

                formattedPrice = (price > 0 || self.displayZeroPrice) ? self.formatPrice(price) : '';

                var priceEl = el.querySelector('.price');
                if (priceEl) {
                    priceEl.innerHTML = formattedPrice;
                    var dupEl = document.getElementById(containerId + self.duplicateIdSuffix);
                    if (dupEl) {
                        var dupPriceEl = dupEl.querySelector('.price');
                        if (dupPriceEl) dupPriceEl.innerHTML = formattedPrice;
                    }
                } else {
                    el.innerHTML = formattedPrice;
                    var dupEl = document.getElementById(containerId + self.duplicateIdSuffix);
                    if (dupEl) dupEl.innerHTML = formattedPrice;
                }
            }
        });

        if (typeof(skipTierPricePercentUpdate) === 'undefined' && typeof(this.tierPrices) !== 'undefined') {
            for (var i = 0; i < this.tierPrices.length; i++) {
                document.querySelectorAll('.benefit').forEach(function (benefitEl) {
                    var parsePrice = function (html) {
                        var format = self.priceFormat;
                        var decimalSymbol = format.decimalSymbol === undefined ? ',' : format.decimalSymbol;
                        var regexStr = '[^0-9-' + decimalSymbol + ']';
                        html = html.replace(new RegExp(regexStr, 'g'), '');
                        html = html.replace(decimalSymbol, '.');
                        return parseFloat(html);
                    };

                    var updateTierPriceInfo = function (priceEl, tierPriceDiff, tierPriceEl, el) {
                        if (typeof(tierPriceEl) === 'undefined') {
                            return;
                        }
                        var price = parsePrice(priceEl.innerHTML);
                        var tierPrice = price + tierPriceDiff;

                        tierPriceEl.innerHTML = self.formatPrice(tierPrice);

                        el.querySelectorAll('.percent.tier-' + i).forEach(function (percentEl) {
                            percentEl.innerHTML = Math.ceil(100 - ((100 / price) * tierPrice));
                        });
                    };

                    var tierPriceElArray = document.querySelectorAll('.tier-price.tier-' + i + ' .price');
                    if (self.showBothPrices) {
                        var containerExclTax = document.getElementById(self.containers[3]);
                        var tierPriceExclTaxDiff = self.tierPrices[i];
                        var tierPriceExclTaxEl = tierPriceElArray[0];
                        updateTierPriceInfo(containerExclTax, tierPriceExclTaxDiff, tierPriceExclTaxEl, benefitEl);
                        var containerInclTax = document.getElementById(self.containers[2]);
                        var tierPriceInclTaxDiff = self.tierPricesInclTax[i];
                        var tierPriceInclTaxEl = tierPriceElArray[1];
                        updateTierPriceInfo(containerInclTax, tierPriceInclTaxDiff, tierPriceInclTaxEl, benefitEl);
                    } else if (self.showIncludeTax) {
                        var container = document.getElementById(self.containers[0]);
                        var tierPriceInclTaxDiff = self.tierPricesInclTax[i];
                        var tierPriceInclTaxEl = tierPriceElArray[0];
                        updateTierPriceInfo(container, tierPriceInclTaxDiff, tierPriceInclTaxEl, benefitEl);
                    } else {
                        var container = document.getElementById(self.containers[0]);
                        var tierPriceExclTaxDiff = self.tierPrices[i];
                        var tierPriceExclTaxEl = tierPriceElArray[0];
                        updateTierPriceInfo(container, tierPriceExclTaxDiff, tierPriceExclTaxEl, benefitEl);
                    }
                });
            }
        }
    },

    formatPrice: function (price) {
        return formatCurrency(price, this.priceFormat);
    }
};
