/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     base_default
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

if(typeof Product=='undefined') {
    var Product = {};
}

/**************************** BUNDLE PRODUCT **************************/
Product.Bundle = function (config) {
    this.config = config;

    // Set preconfigured values for correct price base calculation
    if (config.defaultValues) {
        for (var option in config.defaultValues) {
            if (this.config['options'][option].isMulti) {
                var selected = [];
                for (var i = 0; i < config.defaultValues[option].length; i++) {
                    selected.push(config.defaultValues[option][i]);
                }
                this.config.selected[option] = selected;
            } else {
                this.config.selected[option] = [config.defaultValues[option] + ""];
            }
        }
    }

    this.reloadPrice();
};

Product.Bundle.prototype = {
    changeSelection: function (selection) {
        var parts = selection.id.split('-');
        if (this.config['options'][parts[2]].isMulti) {
            var selected = [];
            if (selection.tagName == 'SELECT') {
                for (var i = 0; i < selection.options.length; i++) {
                    if (selection.options[i].selected && selection.options[i].value != '') {
                        selected.push(selection.options[i].value);
                    }
                }
            } else if (selection.tagName == 'INPUT') {
                var selector = parts[0] + '-' + parts[1] + '-' + parts[2];
                var selections = document.querySelectorAll('.' + selector);
                for (var i = 0; i < selections.length; i++) {
                    if (selections[i].checked && selections[i].value != '') {
                        selected.push(selections[i].value);
                    }
                }
            }
            this.config.selected[parts[2]] = selected;
        } else {
            if (selection.value != '') {
                this.config.selected[parts[2]] = [selection.value];
            } else {
                this.config.selected[parts[2]] = [];
            }
            this.populateQty(parts[2], selection.value);
            var tierPriceElement = document.getElementById('bundle-option-' + parts[2] + '-tier-prices');
            var tierPriceHtml = '';
            if (selection.value != '' && this.config.options[parts[2]].selections[selection.value].customQty == 1) {
                tierPriceHtml = this.config.options[parts[2]].selections[selection.value].tierPriceHtml;
            }
            tierPriceElement.innerHTML = tierPriceHtml;
        }
        this.reloadPrice();
    },

    reloadPrice: function () {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        var includeTaxPrice = 0;

        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i = 0; i < this.config.selected[option].length; i++) {
                    var prices = this.selectionPrice(option, this.config.selected[option][i]);
                    calculatedPrice += Number(prices[0]);
                    dispositionPrice += Number(prices[1]);
                    includeTaxPrice += Number(prices[2]);
                }
            }
        }

        if (taxCalcMethod == CACL_TOTAL_BASE) {
            var calculatedPriceFormatted = calculatedPrice.toFixed(10);
            var includeTaxPriceFormatted = includeTaxPrice.toFixed(10);
            var tax = includeTaxPriceFormatted - calculatedPriceFormatted;
            calculatedPrice = includeTaxPrice - Math.round(tax * 100) / 100;
        }

        if (this.config.priceType == '0') {
            calculatedPrice = Math.round(calculatedPrice * 100) / 100;
            dispositionPrice = Math.round(dispositionPrice * 100) / 100;
            includeTaxPrice = Math.round(includeTaxPrice * 100) / 100;
        }

        var eventDetail = {
            price: calculatedPrice,
            priceInclTax: includeTaxPrice,
            dispositionPrice: dispositionPrice,
            bundle: this,
            noReloadPrice: false
        };
        var event = new CustomEvent('bundle:reload-price', {bubbles: true, cancelable: true, detail: eventDetail});
        document.dispatchEvent(event);

        if (!eventDetail.noReloadPrice) {
            optionsPrice.specialTaxPrice = 'true';
            optionsPrice.changePrice('bundle', calculatedPrice);
            optionsPrice.changePrice('nontaxable', dispositionPrice);
            optionsPrice.changePrice('priceInclTax', includeTaxPrice);
            optionsPrice.reload();
        }

        return calculatedPrice;
    },

    selectionPrice: function (optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none' || typeof(this.config.options[optionId].selections[selectionId]) == 'undefined') {
            return 0;
        }
        var qty = null;
        var tierPriceInclTax, tierPriceExclTax;
        if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            var qtyInput = document.getElementById('bundle-option-' + optionId + '-qty-input');
            if (qtyInput) {
                qty = qtyInput.value;
            } else {
                qty = 1;
            }
        } else {
            qty = this.config.options[optionId].selections[selectionId].qty;
        }
        var price, tierPrice;
        if (this.config.priceType == '0') {
            price = this.config.options[optionId].selections[selectionId].price;
            tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

            for (var i = 0; i < tierPrice.length; i++) {
                if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
                    price = tierPrice[i].price;
                    tierPriceInclTax = tierPrice[i].priceInclTax;
                    tierPriceExclTax = tierPrice[i].priceExclTax;
                }
            }
        } else {
            var selection = this.config.options[optionId].selections[selectionId];
            if (selection.priceType == '0') {
                price = selection.priceValue;
            } else {
                price = (this.config.basePrice * selection.priceValue) / 100;
            }
        }

        var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
            this.config.options[optionId].selections[selectionId].minusDisposition;

        if (this.config.specialPrice) {
            var newPrice = (price * this.config.specialPrice) / 100;
            price = Math.min(newPrice, price);
        }

        var selection = this.config.options[optionId].selections[selectionId];
        var priceInclTax;
        if (tierPriceInclTax !== undefined && tierPriceExclTax !== undefined) {
            priceInclTax = tierPriceInclTax;
            price = tierPriceExclTax;
        } else if (selection.priceInclTax !== undefined) {
            priceInclTax = selection.priceInclTax;
            price = selection.priceExclTax !== undefined ? selection.priceExclTax : selection.price;
        } else {
            priceInclTax = price;
        }

        if (this.config.priceType == '1' || taxCalcMethod == CACL_TOTAL_BASE) {
            return [price * qty, disposition * qty, priceInclTax * qty];
        } else if (taxCalcMethod == CACL_UNIT_BASE) {
            price = (Math.round(price * 100) / 100).toString();
            disposition = (Math.round(disposition * 100) / 100).toString();
            priceInclTax = (Math.round(priceInclTax * 100) / 100).toString();
            return [price * qty, disposition * qty, priceInclTax * qty];
        } else {
            price = (Math.round(price * qty * 100) / 100).toString();
            disposition = (Math.round(disposition * qty * 100) / 100).toString();
            priceInclTax = (Math.round(priceInclTax * qty * 100) / 100).toString();
            return [price, disposition, priceInclTax];
        }
    },

    populateQty: function (optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            this.showQtyInput(optionId, '0', false);
            return;
        }
        if (this.config.options[optionId].selections[selectionId].customQty == 1) {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, true);
        } else {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, false);
        }
    },

    showQtyInput: function (optionId, value, canEdit) {
        var elem = document.getElementById('bundle-option-' + optionId + '-qty-input');
        elem.value = value;
        elem.disabled = !canEdit;
        if (canEdit) {
            elem.classList.remove('qty-disabled');
        } else {
            elem.classList.add('qty-disabled');
        }
    },

    changeOptionQty: function (element, event) {
        var checkQty = true;
        if (typeof(event) != 'undefined') {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) == 0 || isNaN(Number(element.value)))) {
            element.value = 1;
        }
        var parts = element.id.split('-');
        var optionId = parts[2];
        if (!this.config['options'][optionId].isMulti) {
            var selectionId = this.config.selected[optionId][0];
            this.config.options[optionId].selections[selectionId].qty = element.value * 1;
            this.reloadPrice();
        }
    },

    validationCallback: function (elmId, result) {
        var el = typeof elmId === 'string' ? document.getElementById(elmId) : elmId;
        if (!el) {
            return;
        }
        var container = el.closest('ul.options-list');
        if (container) {
            if (result == 'failed') {
                container.classList.remove('validation-passed');
                container.classList.add('validation-failed');
            } else {
                container.classList.remove('validation-failed');
                container.classList.add('validation-passed');
            }
        }
    }
};
