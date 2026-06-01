/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

var windowLoaded = false;
window.addEventListener('load', function() { windowLoaded = true; });

// rewrite the fillselect method from /js/varien/configurable.js
Product.Config.prototype.fillSelect = function (element) {
    return;
};
// rewrite the resetChildren method from /js/varien/configurable.js; it would reset the third attribute when selecting a swatch in the first attribute
Product.Config.prototype.resetChildren = function (element) {
    return;
};
// rewrite the configureForValues method from /js/varien/configurable.js; it tries to select the options when a product has been selected (e.g. editing product from cart page), but we have our own method for that
// @see: Product.ConfigurableSwatches.run()
Product.Config.prototype.configureForValues = function(){
    return;
};

// Wrap the Product.Config constructor to initialize configureObservers and load swatch options.
// The vanilla JS configurable.js uses a plain constructor function, not Class.create(), so
// prototype.initialize is never called — we must wrap the constructor itself.
(function() {
    var _OrigConfig = Product.Config;
    Product.Config = function(config) {
        this.configureObservers = [];
        _OrigConfig.call(this, config);
        this.loadOptions();
    };
    Product.Config.prototype = _OrigConfig.prototype;
    Product.Config.prototype.constructor = Product.Config;
})();

Product.Config.prototype.handleSelectChange = function(element) {
    this.configureElement(element);
    this.configureObservers.forEach(function(funct) {
        funct(element);
    });
};

Product.Config.prototype.origConfigure = Product.Config.prototype.configure;
Product.Config.prototype.configure = function(event) {
    this.origConfigure(event);
    var element = event.target;
    this.configureObservers.forEach(function(funct) {
        funct(element);
    });
};

Product.Config.prototype.configureSubscribe = function(funct)
{
    this.configureObservers.push(funct);
};

/**
 *
 * Load ALL the options into the selects
 * Uses global var spConfig declared in template/configurableswatches/catalog/product/view/type/configurable.phtml
 **/
Product.Config.prototype.loadOptions = function() {
    var self = this;
    this.settings.forEach(function(element){
        element.disabled = false;
        element.options[0] = new Option(self.config.chooseText, '');
        var attributeId = element.id.replace(/[a-z]*/, '');
        var options = self.getAttributeOptions(attributeId);
        if(options) {
            var index = 1;
            for(var i=0;i<options.length;i++){
                options[i].allowedProducts = options[i].products.slice(0);
                element.options[index] = new Option(self.getOptionLabel(options[i], options[i].price), options[i].id);
                if (typeof options[i].price != 'undefined') {
                    element.options[index].setAttribute('price', options[i].price);
                }
                element.options[index].setAttribute('data-label', options[i].label.toLowerCase());
                element.options[index].config = options[i];
                index++;
            }
        }
        self.reloadOptionLabels(element);
    });
};


Product.ConfigurableSwatches = (function() {
    function ProductConfigurableSwatches(productConfig, config) {
        if (config && typeof(config) == 'object') {
            this.setConfig(config);
        }
        this.productConfig = productConfig;
        var attributes = [];
        for (var i in productConfig.config.attributes) {
            attributes.push(productConfig.config.attributes[i]);
        }
        this.configurableAttributes = attributes;
        this.run();
        return this;
    }

    ProductConfigurableSwatches.prototype = {
        productConfig: false,
        configurableAttributes: {},
        // Options
        _O: {
            selectFirstOption: false
        },
        // Flags
        _F: {
            currentAction: false,
            firstOptionSelected: false,
            nativeSelectChange: true
        },
        // Namespaces
        _N: {
            resetTimeout: false
        },
        // Elements
        _E: {
            cartBtn: {
                btn: false,
                txt: ['Add to Cart'],
                onclick: function() { return false; }
            },
            availability: false,
            optionOver: false,
            optionOut: false,
            _last: {
                optionOver: false
            },
            activeConfigurableOptions: [],
            allConfigurableOptions: []
        },

        setConfig: function(config) {
            Object.assign(this._O, config);
        },

        /**
         *
         * Sets the stage for configurable swatches, including attaching all the data and events needed in the process to all attributes and options
         **/
        run: function() {
            var self = this;
            this._F.hasPresetValues = (typeof spConfig != "undefined" && typeof spConfig.values != "undefined");

            this.setStockData();

            this.configurableAttributes.forEach(function(attr, i){
                self.setAttrData(attr, i);
                attr.options.forEach(function(opt, j){
                    self.setOptData(opt, attr, j);
                    self._E.allConfigurableOptions.push( opt );
                    self.attachOptEvents(opt);
                });
            });

            this.productConfig.configureSubscribe(this.onSelectChange.bind(this));

            if (this._F.hasPresetValues) {
                this.values = spConfig.values;
                this.configurableAttributes.forEach(function(attr){
                    var optId = self.values[attr.id];
                    var matched = attr.options.find(function(opt) {
                        return optId == opt.id;
                    });
                    if (matched) {
                        self.selectOption(matched);
                    }
                });
                this._F.presetValuesSelected = true;
            } else if (this._O.selectFirstOption) {
                this.selectFirstOption();
            }
            return this;
        },

        /**
         *
         * Enables/Disables the add to cart button to prevent the user from selecting an out of stock item.
         * This also makes the necessary visual cues to show in stock/out of stock.
         **/
        setStockData: function() {
            var self = this;
            var cartBtn = Array.from(document.querySelectorAll('.add-to-cart button.button'));
            this._E.cartBtn = {
                btn: cartBtn,
                txt: cartBtn.map(function(el) { return el.getAttribute('title'); }),
                onclick: cartBtn.length ? cartBtn[0].getAttribute('onclick') : ''
            };
            this._E.availability = Array.from(document.querySelectorAll('p.availability'));
            cartBtn.forEach(function(el) {
                el.parentElement.addEventListener('mouseenter', function() {
                    clearTimeout(self._N.resetTimeout);
                    self.resetAvailableOptions();
                });
            });
        },

        /**
         *
         * Sets the necessary flags on the attribute and stores the DOM elements related to the attribute
         *
         * @var attr - an object with options
         * @var i - index of attr in `configurableAttributes`
         **/
        setAttrData: function(attr, i) {
            var optionSelect = document.getElementById('attribute' + attr.id);
            attr._f = {};
            attr._f.isCustomOption = false;
            attr._f.isSwatch = optionSelect.classList.contains('swatch-select');
            attr._e = {
                optionSelect: optionSelect,
                attrLabel: this._u.getAttrLabelElement( attr.code ),
                selectedOption: false,
                _last: {
                    selectedOption: false
                }
            };
            attr._e.optionSelect.attr = attr;
            if (attr._f.isSwatch) {
                attr._e.ul = document.getElementById('configurable_swatch_' + attr.code);
            }
            return attr;
        },

        /**
         *
         * Set necessary flags and related DOM elements at an option level
         *
         * @var opt - object being looped through
         * @var attr - the object from which the `opt` came from
         * @var j - index of `opt` in `attr`
         **/
        setOptData: function(opt, attr, j) {
            opt.attr = attr;
            opt._f = {
                isSwatch: attr._f.isSwatch,
                enabled: true,
                active: false
            };
            opt._e = {
                option: this._u.getOptionElement(opt, attr, j)
            };
            opt._e.option.opt = opt;
            if (attr._f.isSwatch) {
                opt._e.a = document.getElementById('swatch'+opt.id);
                opt._e.li = document.getElementById('option'+opt.id);
                opt._e.ul = attr._e.ul;
            }
            return opt;
        },

        /**
         *
         * Attach click, mouseenter, and mouseleave events for each option/swatch
         **/
        attachOptEvents: function(opt) {
            var self = this;
            var attr = opt.attr;
            if (opt._f.isSwatch) {
                opt._e.a.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    self._F.currentAction = "click";
                    attr._e._last.selectedOption = attr._e.selectedOption;
                    attr._e.selectedOption = opt;
                    self.onOptionClick( attr );
                    return false;
                });
                opt._e.a.addEventListener('mouseenter', function(){
                    self._F.currentAction = "over-swatch";
                    self._E.optionOver = opt;
                    self.onOptionOver();
                    self._E._last.optionOver = self._E.optionOver;
                });
                opt._e.a.addEventListener('mouseleave', function(){
                    self._F.currentAction = "out-swatch";
                    self._E.optionOut = opt;
                    self.onOptionOut();
                });
            }
        },

        /**
         *
         * An optional method to select the first option on page load
         **/
        selectFirstOption: function() {
            if (this.configurableAttributes.length) {
                var attr = this.configurableAttributes[0];
                if (attr.options.length) {
                    var opt = attr.options[0];
                    this.selectOption(opt);
                }
            }
        },

        /**
         *
         * Initialize the selecting of an option: set necessary flags,
         * store active options, and remove last active options
         * Send to onOptionClick method
         **/
        selectOption: function(opt) {
            var attr = opt.attr;
            this._F.currentAction = "click";
            attr._e._last.selectedOption = attr._e.selectedOption;
            attr._e.selectedOption = opt;
            this.onOptionClick( attr );
        },

        onSelectChange: function(select)
        {
            var attr = select.attr;

            if (this._F.nativeSelectChange) {
                this._F.currentAction = 'change';
                var option = select.options[select.selectedIndex];
                if (option.opt) {
                    attr._e._last.selectedOption = attr._e.selectedOption;
                    attr._e.selectedOption = option.opt;

                    if (attr._e._last.selectedOption) attr._e._last.selectedOption._f.active = false;
                    option.opt._f.active = true;

                    var pos = this._E.activeConfigurableOptions.indexOf( attr._e._last.selectedOption );
                    if (pos !== -1) this._E.activeConfigurableOptions.splice(pos, 1);

                    this._E.activeConfigurableOptions.push( option.opt );

                } else {
                    var pos = this._E.activeConfigurableOptions.indexOf( attr._e._last.selectedOption );
                    if (pos !== -1) this._E.activeConfigurableOptions.splice(pos, 1);
                    if (attr._e._last.selectedOption) attr._e._last.selectedOption._f.active = false;
                }
                this.setAvailableOptions();
                this.checkStockStatus();
            }
        },

        /**
         *
         * Run everything that needs to happen (visually and functionally) when an option is clicked
         **/
        onOptionClick: function(attr) {
            var self = this;
            var opt = attr._e.selectedOption;
            if (opt) {
                if (opt != attr._e._last.selectedOption) {
                    attr._e.attrLabel.innerHTML = this.getOptionLabel(opt);

                    if (opt._f.isSwatch) {
                        opt._e.ul.querySelectorAll('li').forEach(function(li) {
                            li.classList.remove('selected');
                        });
                        opt._e.li.classList.add('selected');
                        var inputBox = attr._e.optionSelect.parentElement;
                        if (inputBox.classList.contains('validation-error')) {
                            inputBox.classList.remove('validation-error');
                            var advice = inputBox.querySelector('.validation-advice');
                            if (advice) advice.remove();
                        }
                    }

                    if (attr._e._last.selectedOption) attr._e._last.selectedOption._f.active = false;
                    opt._f.active = true;

                    var pos = this._E.activeConfigurableOptions.indexOf( attr._e._last.selectedOption );
                    if (pos !== -1) this._E.activeConfigurableOptions.splice(pos, 1);

                    this._E.activeConfigurableOptions.push( opt );

                    this.setAvailableOptions();
                    if (opt._f.isSwatch && !attr._f.isCustomOption && this._F.firstOptionSelected) {
                        this.previewAvailableOptions();
                    }
                }
            } else {
                var pos = this._E.activeConfigurableOptions.indexOf( attr._e._last.selectedOption );
                if (pos !== -1) this._E.activeConfigurableOptions.splice(pos, 1);
                if (attr._e._last.selectedOption) attr._e._last.selectedOption._f.active = false;
                this.setAvailableOptions();
            }
            this.checkStockStatus();

            this._E.activeConfigurableOptions.forEach(function(selectedOpt){
                var oldDisabledValue = selectedOpt._e.option.disabled;
                selectedOpt._e.option.disabled = false;
                selectedOpt._e.option.selected = true;
                selectedOpt._e.option.disabled = oldDisabledValue;
            });

            if ((this._O.selectFirstOption && !this._F.firstOptionSelected) ||
                (this._F.hasPresetValues && !this._F.presetValuesSelected) ||
                (!windowLoaded)) {
                window.addEventListener('load', function() {
                    window.setTimeout(function() {
                        self.updateSelect( attr );
                        self._F.firstOptionSelected = true;
                    }, 200);
                });
            } else {
                this.updateSelect(attr);
                this._F.firstOptionSelected = true;
            }
        },

        /**
         *
         * Visual cues if you were to click on the option/swatch you're hovering over
         * - Show enabled/disabled state of other options/swatches
         * - Preview label of hovered swatch
         * - Preview the stock status
         **/
        onOptionOver: function() {
            if(PointerManager.getPointer() == PointerManager.TOUCH_POINTER_TYPE) {
                return;
            }

            var opt = this._E.optionOver;
            var attr = opt.attr;
            var lastOpt = this._E._last.optionOver;

            clearTimeout(this._N.resetTimeout);

            if (lastOpt && lastOpt._f.isSwatch) {
                lastOpt._e.li.classList.remove('hover');
            }
            if (opt._f.isSwatch) {
                opt._e.li.classList.add('hover');
            }

            attr._e.attrLabel.innerHTML = this.getOptionLabel(opt);

            this.setAvailableOptions();
            if(lastOpt && lastOpt.attr.id != opt.attr.id) {
                lastOpt.attr._e.attrLabel.innerHTML = lastOpt.attr._e.selectedOption ? this.getOptionLabel(lastOpt.attr._e.selectedOption) : '';
            }

            if (!attr._f.isCustomOption) {
                this.previewAvailableOptions();

                var stockCheckOptions = this._E.activeConfigurableOptions;
                if (!opt._f.active) {
                    stockCheckOptions = stockCheckOptions.filter(function(o) { return o !== attr._e.selectedOption; });
                    stockCheckOptions.push(opt);
                }
                this.checkStockStatus( stockCheckOptions );
            }
        },

        /**
         *
         * Reset all visual cues from onOptionOver
         **/
        onOptionOut: function() {
            if (PointerManager.getPointer() == PointerManager.TOUCH_POINTER_TYPE) {
                return;
            }

            var self = this;
            var opt = this._E.optionOver;

            this._N.resetTimeout = setTimeout(function(){
                self.resetAvailableOptions();
            }, 300);

            if (opt && opt._f.isSwatch) {
                opt._e.li.classList.remove('hover');
            }
        },

        /**
         *
         * Loop through each option across all attributes to set them as available or not
         * and set necessary flags as such
         **/
        setAvailableOptions: function() {
            var self = this;
            var args = arguments;
            var loopThroughOptions = args.length ? args[0] : this._E.allConfigurableOptions;
            loopThroughOptions.forEach( function(loopingOption) {
                var productArrays = [ loopingOption.products ];
                if (loopingOption.attr._e.selectedOption) {
                    self._E.activeConfigurableOptions
                        .filter(function(o) { return o !== loopingOption.attr._e.selectedOption; })
                        .forEach(function(selectedOpt) {
                            productArrays.push( selectedOpt.products );
                        });
                } else {
                    self._E.activeConfigurableOptions.forEach(function(selectedOpt){
                        productArrays.push( selectedOpt.products );
                    });
                }
                var result = self._u.intersectAll( productArrays );
                self.setOptionStatus(loopingOption, result.length);
            });
        },

        /**
         *
         * Loop though each option across all attributes to preview their availability if the
         * option being hovered were to be selected
         **/
        previewAvailableOptions: function() {
            var self = this;
            var opt = this._E.optionOver;
            if (!opt) {
                return;
            }

            var attr = opt.attr;

            this._E.allConfigurableOptions.forEach( function(loopingOption, i) {
                var productArrays = [ loopingOption.products, opt.products ];

                if (attr.id == loopingOption.attr.id) {
                    return;
                }
                if (!loopingOption.attr._e.selectedOption) {
                    self._E.activeConfigurableOptions.forEach(function(selectedOpt){
                        if (selectedOpt.attr.id != opt.attr.id) {
                            productArrays.push( selectedOpt.products );
                        }
                    });
                }
                var result = self._u.intersectAll( productArrays );
                self.setOptionStatus(loopingOption, result.length);
            });
        },

        /**
         *
         * Reset all the options and their availability, the attribute labels, and the stock status
         **/
        resetAvailableOptions: function() {
            var opt = this._E.optionOver;

            if (opt) {
                var attr = opt.attr;

                attr._e.attrLabel.innerHTML = attr._e.selectedOption ? this.getOptionLabel(attr._e.selectedOption) : '';
                this._F.currentAction = false;

                if (!attr._f.isCustomOption) {
                    this.setAvailableOptions();
                    this.checkStockStatus();
                }

                this._E._last.optionOver = false;
            }
        },

        /**
         *
         * Run a check though all the selected options and set the stock status if any are disabled
         **/
        checkStockStatus: function() {
            var checkOptions = arguments.length ? arguments[0] : this._E.activeConfigurableOptions;
            var inStock = !checkOptions.some(function(selectedOpt) {
                return !selectedOpt._f.enabled;
            });
            this.setStockStatus( inStock );
        },

        /**
         *
         * Do all the visual changes and enable/disable add to cart button depending on the stock status
         *
         * @var inStock - boolean
         **/
        setStockStatus: function(inStock) {
            var self = this;
            if (inStock) {
                this._E.availability.forEach(function(el) {
                    el.classList.add('in-stock');
                    el.classList.remove('out-of-stock');
                    el.querySelectorAll('span').forEach(function(span) {
                        span.innerHTML = Translator.translate('In Stock');
                    });
                });

                this._E.cartBtn.btn.forEach(function(el, index) {
                    el.disabled = false;
                    el.classList.remove('out-of-stock');
                    if (self._E.cartBtn.onclick) {
                        el.setAttribute('onclick', self._E.cartBtn.onclick);
                    } else {
                        el.removeAttribute('onclick');
                    }
                    el.title = '' + Translator.translate(self._E.cartBtn.txt[index]);
                    el.querySelectorAll('span span').forEach(function(span) {
                        span.innerHTML = Translator.translate(self._E.cartBtn.txt[index]);
                    });
                });
            } else {
                this._E.availability.forEach(function(el) {
                    el.classList.add('out-of-stock');
                    el.classList.remove('in-stock');
                    el.querySelectorAll('span').forEach(function(span) {
                        span.innerHTML = Translator.translate('Out of Stock');
                    });
                });
                this._E.cartBtn.btn.forEach(function(el) {
                    el.classList.add('out-of-stock');
                    el.disabled = true;
                    el.removeAttribute('onclick');
                    el.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    });
                    el.setAttribute('title', Translator.translate('Out of Stock'));
                    el.querySelectorAll('span span').forEach(function(span) {
                        span.innerHTML = Translator.translate('Out of Stock');
                    });
                });
            }
        },

        /**
         *
         * Enable/disable a specific option
         **/
        setOptionStatus: function(opt, enabled) {
            enabled = enabled > 0;
            opt._f.enabled = enabled;
            if (opt._f.isSwatch) {
                if (enabled) {
                    opt._e.li.classList.remove('not-available');
                } else {
                    opt._e.li.classList.add('not-available');
                }
            } else if (this._F.currentAction == "click" || this._F.currentAction == "change") {
                if (enabled) {
                    opt._e.option.removeAttribute('disabled');
                } else {
                    opt._e.option.setAttribute('disabled', 'disabled');
                }
            }
            return enabled;
        },

        /**
         *
         * Make sure all events related to the select being updated are fired appropriately
         **/
        updateSelect: function(attr) {
            if (attr._e.selectedOption !== false && attr._e.optionSelect) {
                this._F.nativeSelectChange = false;
                ConfigurableMediaImages.updateImage(attr._e.optionSelect);
                this.productConfig.handleSelectChange(attr._e.optionSelect);
                this._F.nativeSelectChange = true;
            }
        },

        /**
         * Return text that should be displayed in attribute label for a certain option
         *
         * @param {object} option
         * return {string}
         */
        getOptionLabel: function(option) {
            return this.productConfig.getOptionLabel(option, option.price);
        },

        /**
         * Utility methods - none of these require more information than what is sent to them in the params or any outside methods
         */
        _u: {
            /**
             *
             * Find (or else, make) the attribute's label
             **/
            getAttrLabelElement: function(attrCode) {
                var spanLabel = document.querySelectorAll('#select_label_'+attrCode);
                if (spanLabel.length) {
                    return spanLabel[0];
                } else {
                    var labels = document.querySelectorAll('#'+attrCode+'_label');
                    if (labels.length) {
                        labels[0].insertAdjacentHTML('beforeend', ' <span id="select_label_'+attrCode+'" class="select-label"></span>');
                        return labels[0].querySelector('span.select-label');
                    }
                }
                return false;
            },

            /**
             *
             * Find the DOM element option relating to the option object in configurableAttributes
             **/
            getOptionElement: function(opt, attr, idx) {
                var indexedOption = attr._e.optionSelect.options[idx+1];
                if (indexedOption && indexedOption.value == opt.id) {
                    return indexedOption;
                }
                var optionElement = false;
                var optionsLen = attr._e.optionSelect.options.length;
                for (var i=0; i<optionsLen; i++) {
                    var option = attr._e.optionSelect.options[i];
                    if (option.value == opt.id) {
                        optionElement = option;
                        break;
                    }
                }
                return optionElement;
            },

            /**
             *
             * Find intersecting items from an array of arrays
             *
             * @var lists - array
             * Example: intersectAll([ [1,2,3], [2,3,4] ]); returns [2,3]
             **/
            intersectAll: function(lists) {
                if (lists.length == 0) return [];
                else if (lists.length == 1) return lists[0];

                var result = lists[0];
                for (var i = 1; i < lists.length; i++) {
                    if (!result.length) break;
                    var other = lists[i];
                    result = result.filter(function(item) { return other.indexOf(item) !== -1; });
                }
                return result;
            }
        }
    };

    return ProductConfigurableSwatches;
})();
