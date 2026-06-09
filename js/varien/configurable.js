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
 * @copyright   Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

if (typeof Product == 'undefined') {
    var Product = {};
}

/**************************** CONFIGURABLE PRODUCT **************************/
Product.Config = function (config) {
    this.config = config;
    this.taxConfig = this.config.taxConfig;
    if (config.containerId) {
        this.settings = Array.from(document.querySelectorAll('#' + config.containerId + ' .super-attribute-select'));
    } else {
        this.settings = Array.from(document.querySelectorAll('.super-attribute-select'));
    }
    this.state = {};
    this.prices = config.prices;

    // Simple template: replace #{key} with obj[key]
    var tpl = this.config.template;
    this.priceTemplate = {
        evaluate: function (obj) {
            return tpl.replace(/#\{(\w+)\}/g, function (m, key) {
                return obj[key] !== undefined ? obj[key] : '';
            });
        }
    };

    // Set default values from config
    if (config.defaultValues) {
        this.values = config.defaultValues;
    }

    // Overwrite defaults by url hash
    var separatorIndex = window.location.href.indexOf('#');
    if (separatorIndex != -1) {
        var paramsStr = window.location.href.substr(separatorIndex + 1);
        var urlValues = {};
        paramsStr.split('&').forEach(function (pair) {
            var parts = pair.split('=');
            if (parts[0]) urlValues[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
        });
        if (!this.values) {
            this.values = {};
        }
        for (var i in urlValues) {
            this.values[i] = urlValues[i];
        }
    }

    // Overwrite defaults by inputs values if needed
    if (config.inputsInitialized) {
        this.values = {};
        var self = this;
        this.settings.forEach(function (element) {
            if (element.value) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                self.values[attributeId] = element.value;
            }
        });
    }

    // Put events to check select reloads
    var self = this;
    this.settings.forEach(function (element) {
        element.addEventListener('change', self.configure.bind(self));
    });

    // fill state
    this.settings.forEach(function (element) {
        var attributeId = element.id.replace(/[a-z]*/, '');
        if (attributeId && self.config.attributes[attributeId]) {
            element.config = self.config.attributes[attributeId];
            element.attributeId = attributeId;
            self.state[attributeId] = false;
        }
    });

    // Init settings dropdown
    var childSettings = [];
    for (var i = this.settings.length - 1; i >= 0; i--) {
        var prevSetting = this.settings[i - 1] ? this.settings[i - 1] : false;
        var nextSetting = this.settings[i + 1] ? this.settings[i + 1] : false;
        if (i == 0) {
            this.fillSelect(this.settings[i]);
        } else {
            this.settings[i].disabled = true;
        }
        this.settings[i].childSettings = childSettings.slice();
        this.settings[i].prevSetting = prevSetting;
        this.settings[i].nextSetting = nextSetting;
        childSettings.push(this.settings[i]);
    }

    // Set values to inputs
    this.configureForValues();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', this.configureForValues.bind(this));
    } else {
        this.configureForValues();
    }
};

Product.Config.prototype = {
    configureForValues: function () {
        if (this.values) {
            var self = this;
            this.settings.forEach(function (element) {
                var attributeId = element.attributeId;
                element.value = (typeof(self.values[attributeId]) == 'undefined') ? '' : self.values[attributeId];
                self.configureElement(element);
            });
        }
    },

    configure: function (event) {
        var element = event.target;
        this.configureElement(element);
    },

    configureElement: function (element) {
        this.reloadOptionLabels(element);
        if (element.value) {
            this.state[element.config.id] = element.value;
            if (element.nextSetting) {
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        } else {
            this.resetChildren(element);
        }
        this.reloadPrice();
    },

    reloadOptionLabels: function (element) {
        var selectedPrice;
        if (element.options[element.selectedIndex].config && !this.config.stablePrices) {
            selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
        } else {
            selectedPrice = 0;
        }
        for (var i = 0; i < element.options.length; i++) {
            if (element.options[i].config) {
                element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price - selectedPrice);
            }
        }
    },

    resetChildren: function (element) {
        if (element.childSettings) {
            for (var i = 0; i < element.childSettings.length; i++) {
                element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if (element.config) {
                    this.state[element.config.id] = false;
                }
            }
        }
    },

    fillSelect: function (element) {
        var attributeId = element.id.replace(/[a-z]*/, '');
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option('', '');
        element.options[0].innerHTML = this.config.chooseText;

        var prevConfig = false;
        if (element.prevSetting) {
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if (options) {
            var index = 1;
            for (var i = 0; i < options.length; i++) {
                var allowedProducts = [];
                if (prevConfig) {
                    for (var j = 0; j < options[i].products.length; j++) {
                        if (prevConfig.config && prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.slice();
                }

                if (allowedProducts.length > 0) {
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }
    },

    getOptionLabel: function (option, price) {
        var price = parseFloat(price);
        if (this.taxConfig.includeTax) {
            var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            var excl = price - tax;
            var incl = excl * (1 + (this.taxConfig.currentTax / 100));
        } else {
            var tax = price * (this.taxConfig.currentTax / 100);
            var excl = price;
            var incl = excl + tax;
        }

        if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
            price = incl;
        } else {
            price = excl;
        }

        var str = option.label;
        if (price) {
            if (this.taxConfig.showBothPrices) {
                str += ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str += ' ' + this.formatPrice(price, true);
            }
        }
        return str;
    },

    formatPrice: function (price, showSign) {
        var str = '';
        price = parseFloat(price);
        if (showSign) {
            if (price < 0) {
                str += '-';
                price = -price;
            } else {
                str += '+';
            }
        }

        var roundedPrice = (Math.round(price * 100) / 100).toString();

        if (this.prices && this.prices[roundedPrice]) {
            str += this.prices[roundedPrice];
        } else {
            str += this.priceTemplate.evaluate({price: price.toFixed(2)});
        }
        return str;
    },

    clearSelect: function (element) {
        for (var i = element.options.length - 1; i >= 0; i--) {
            element.remove(i);
        }
    },

    getAttributeOptions: function (attributeId) {
        if (this.config.attributes[attributeId]) {
            return this.config.attributes[attributeId].options;
        }
    },

    reloadPrice: function () {
        if (this.config.disablePriceReload) {
            return;
        }
        var price = 0;
        var oldPrice = 0;
        for (var i = this.settings.length - 1; i >= 0; i--) {
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if (selected.config) {
                price += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice.reload();

        return price;
    },

    reloadOldPrice: function () {
        if (this.config.disablePriceReload) {
            return;
        }
        var oldPriceEl = document.getElementById('old-price-' + this.config.productId);
        if (oldPriceEl) {
            var price = parseFloat(this.config.oldPrice);
            for (var i = this.settings.length - 1; i >= 0; i--) {
                var selected = this.settings[i].options[this.settings[i].selectedIndex];
                if (selected.config) {
                    price += parseFloat(selected.config.oldPrice);
                }
            }
            if (price < 0)
                price = 0;
            price = this.formatPrice(price);

            oldPriceEl.innerHTML = price;
        }
    }
};
