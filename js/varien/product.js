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
 * @copyright   Copyright (c) 2017-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
}

/********************* IMAGE ZOOMER ***********************/

Product.Zoom = function(imageEl, trackEl, handleEl, zoomInEl, zoomOutEl, hintEl) {
    this.initialize(imageEl, trackEl, handleEl, zoomInEl, zoomOutEl, hintEl);
};
Product.Zoom.prototype = {
    initialize: function(imageEl, trackEl, handleEl, zoomInEl, zoomOutEl, hintEl){
        this.containerEl = document.getElementById(imageEl).parentNode;
        this.imageEl = document.getElementById(imageEl);
        this.handleEl = document.getElementById(handleEl);
        this.trackEl = document.getElementById(trackEl);
        this.hintEl = document.getElementById(hintEl);

        this.containerDim = {width: this.containerEl.offsetWidth, height: this.containerEl.offsetHeight};
        this.imageDim = {width: this.imageEl.offsetWidth, height: this.imageEl.offsetHeight};

        this.imageDim.ratio = this.imageDim.width/this.imageDim.height;

        this.floorZoom = 1;

        if (this.imageDim.width > this.imageDim.height) {
            this.ceilingZoom = this.imageDim.width / this.containerDim.width;
        } else {
            this.ceilingZoom = this.imageDim.height / this.containerDim.height;
        }

        if (this.imageDim.width <= this.containerDim.width
            && this.imageDim.height <= this.containerDim.height) {
            this.trackEl.parentNode.style.display = 'none';
            this.hintEl.style.display = 'none';
            this.containerEl.classList.remove('product-image-zoom');
            return;
        }

        this.imageX = 0;
        this.imageY = 0;
        this.imageZoom = 1;

        this.sliderSpeed = 0;
        this.sliderAccel = 0;
        this.zoomBtnPressed = false;

        this.showFull = false;

        this.selects = document.getElementsByTagName('select');

        if (typeof Draggable !== 'undefined') {
            this.draggable = new Draggable(imageEl, {
                starteffect:false,
                reverteffect:false,
                endeffect:false,
                snap:this.contain.bind(this)
            });
        } else {
            this.draggable = { element: this.imageEl };
        }

        if (typeof Control !== 'undefined' && typeof Control.Slider !== 'undefined') {
            this.slider = new Control.Slider(handleEl, trackEl, {
                axis:'horizontal',
                minimum:0,
                maximum:this.trackEl.offsetWidth,
                alignX:0,
                increment:1,
                sliderValue:0,
                onSlide:this.scale.bind(this),
                onChange:this.scale.bind(this)
            });
        } else {
            this.slider = { value: 0, disabled: false, setValue: function(v) { this.value = v; }, setDisabled: function() { this.disabled = true; } };
        }

        this.scale(0);

        this.imageEl.addEventListener('dblclick', this.toggleFull.bind(this));

        const zoomInElNode = document.getElementById(zoomInEl);
        zoomInElNode.addEventListener('mousedown', this.startZoomIn.bind(this));
        zoomInElNode.addEventListener('mouseup', this.stopZooming.bind(this));
        zoomInElNode.addEventListener('mouseout', this.stopZooming.bind(this));

        const zoomOutElNode = document.getElementById(zoomOutEl);
        zoomOutElNode.addEventListener('mousedown', this.startZoomOut.bind(this));
        zoomOutElNode.addEventListener('mouseup', this.stopZooming.bind(this));
        zoomOutElNode.addEventListener('mouseout', this.stopZooming.bind(this));
    },

    toggleFull: function () {
        this.showFull = !this.showFull;

        const val_scale = !this.showFull ? this.slider.value : 1;
        this.scale(val_scale);

        this.trackEl.style.visibility = this.showFull ? 'hidden' : 'visible';
        this.containerEl.style.overflow = this.showFull ? 'visible' : 'hidden';
        this.containerEl.style.zIndex = this.showFull ? '1000' : '9';

        return this;
    },

    scale: function (v) {
        const centerX  = (this.containerDim.width*(1-this.imageZoom)/2-this.imageX)/this.imageZoom;
        const centerY  = (this.containerDim.height*(1-this.imageZoom)/2-this.imageY)/this.imageZoom;
        const overSize = (this.imageDim.width > this.containerDim.width || this.imageDim.height > this.containerDim.height);

        this.imageZoom = this.floorZoom+(v*(this.ceilingZoom-this.floorZoom));

        if (overSize) {
            if (this.imageDim.width > this.imageDim.height) {
                this.imageEl.style.width = (this.imageZoom*this.containerDim.width)+'px';
            } else {
                this.imageEl.style.height = (this.imageZoom*this.containerDim.height)+'px';
            }
            if (this.containerDim.ratio) {
                if (this.imageDim.width > this.imageDim.height) {
                    this.imageEl.style.height = (this.imageZoom*this.containerDim.width*this.containerDim.ratio)+'px'; // for safari
                } else {
                    this.imageEl.style.width = (this.imageZoom*this.containerDim.height*this.containerDim.ratio)+'px'; // for safari
                }
            }
        } else {
            this.slider.setDisabled();
        }

        this.imageX = this.containerDim.width*(1-this.imageZoom)/2-centerX*this.imageZoom;
        this.imageY = this.containerDim.height*(1-this.imageZoom)/2-centerY*this.imageZoom;

        this.contain(this.imageX, this.imageY, this.draggable);

        return true;
    },

    startZoomIn: function()
    {
        if (!this.slider.disabled) {
            this.zoomBtnPressed = true;
            this.sliderAccel = .002;
            this.periodicalZoom();
            this.zoomer = setInterval(this.periodicalZoom.bind(this), 50);
        }
        return this;
    },

    startZoomOut: function()
    {
        if (!this.slider.disabled) {
            this.zoomBtnPressed = true;
            this.sliderAccel = -.002;
            this.periodicalZoom();
            this.zoomer = setInterval(this.periodicalZoom.bind(this), 50);
        }
        return this;
    },

    stopZooming: function()
    {
        if (!this.zoomer || this.sliderSpeed==0) {
            return;
        }
        this.zoomBtnPressed = false;
        this.sliderAccel = 0;
    },

    periodicalZoom: function()
    {
        if (!this.zoomer) {
            return this;
        }

        if (this.zoomBtnPressed) {
            this.sliderSpeed += this.sliderAccel;
        } else {
            this.sliderSpeed /= 1.5;
            if (Math.abs(this.sliderSpeed)<.001) {
                this.sliderSpeed = 0;
                clearInterval(this.zoomer);
                this.zoomer = null;
            }
        }
        this.slider.value += this.sliderSpeed;

        this.slider.setValue(this.slider.value);
        this.scale(this.slider.value);

        return this;
    },

    contain: function (x,y,draggable) {

        const dim = {width: draggable.element.offsetWidth, height: draggable.element.offsetHeight};

        const xMin = 0, xMax = this.containerDim.width-dim.width;
        const yMin = 0, yMax = this.containerDim.height-dim.height;

        x = x>xMin ? xMin : x;
        x = x<xMax ? xMax : x;
        y = y>yMin ? yMin : y;
        y = y<yMax ? yMax : y;

        if (this.containerDim.width > dim.width) {
            x = (this.containerDim.width/2) - (dim.width/2);
        }

        if (this.containerDim.height > dim.height) {
            y = (this.containerDim.height/2) - (dim.height/2);
        }

        this.imageX = x;
        this.imageY = y;

        this.imageEl.style.left = this.imageX+'px';
        this.imageEl.style.top = this.imageY+'px';

        return [x,y];
    }
};
Varien.classCompat(Product.Zoom);

/**************************** CONFIGURABLE PRODUCT **************************/
Product.Config = function(config) {
    this.initialize(config);
};
Product.Config.prototype = {
    initialize: function(config){
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        this.settings   = Array.from(document.querySelectorAll('.super-attribute-select'));
        this.state      = {};
        // Simple template: replace #{key} with obj[key]
        const tpl = this.config.template;
        this.priceTemplate = {
            evaluate: function (obj) {
                return tpl.replace(/#\{(\w+)\}/g, function (m, key) {
                    return obj[key] !== undefined ? obj[key] : '';
                });
            }
        };
        this.prices     = config.prices;

        this.settings.forEach(function(element){
            element.addEventListener('change', this.configure.bind(this));
        }.bind(this));

        // fill state
        this.settings.forEach(function(element){
            const attributeId = element.id.replace(/[a-z]*/, '');
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this));

        // Init settings dropdown
        const childSettings = [];
        for(var i=this.settings.length-1;i>=0;i--){
            const prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            const nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
            if(i==0){
                this.fillSelect(this.settings[i]);
            }
            else {
                this.settings[i].disabled=true;
            }
            this.settings[i].childSettings = childSettings.slice();
            this.settings[i].prevSetting   = prevSetting;
            this.settings[i].nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }

        // Set default values - from config and overwrite them by url values
        if (config.defaultValues) {
            this.values = config.defaultValues;
        }

        const separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex != -1) {
            const paramsStr = window.location.href.substr(separatorIndex+1);
            const urlValues = {};
            const searchParams = new URLSearchParams(paramsStr);
            searchParams.forEach(function(value, key) {
                urlValues[key] = value;
            });
            if (!this.values) {
                this.values = {};
            }
            for (var i in urlValues) {
                this.values[i] = urlValues[i];
            }
        }

        this.configureForValues();
        document.addEventListener('DOMContentLoaded', this.configureForValues.bind(this));
    },

    configureForValues: function () {
        if (this.values) {
            this.settings.forEach(function(element){
                const attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                this.configureElement(element);
            }.bind(this));
        }
    },

    configure: function(event){
        const element = event.target;
        this.configureElement(element);
    },

    configureElement : function(element) {
        this.reloadOptionLabels(element);
        if(element.value){
            this.state[element.config.id] = element.value;
            if(element.nextSetting){
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        }
        else {
            this.resetChildren(element);
        }
        this.reloadPrice();
//      Calculator.updatePrice();
    },

    reloadOptionLabels: function(element){
        let selectedPrice;
        if(element.options[element.selectedIndex].config){
            selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
        }
        else{
            selectedPrice = 0;
        }
        for(let i=0;i<element.options.length;i++){
            if(element.options[i].config){
                element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
            }
        }
    },

    resetChildren : function(element){
        if(element.childSettings) {
            for(let i=0;i<element.childSettings.length;i++){
                element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if(element.config){
                    this.state[element.config.id] = false;
                }
            }
        }
    },

    fillSelect: function(element){
        const attributeId = element.id.replace(/[a-z]*/, '');
        const options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option('', '');
        element.options[0].innerHTML = this.config.chooseText;

        let prevConfig = false;
        if(element.prevSetting){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if(options) {
            let index = 1;
            for(let i=0;i<options.length;i++){
                let allowedProducts = [];
                if(prevConfig) {
                    for(let j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.slice();
                }

                if(allowedProducts.length>0){
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }
    },

    getOptionLabel: function(option, price){
        var price = parseFloat(price);
        if (this.taxConfig.includeTax) {
            var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            var excl = price - tax;
            var incl = excl*(1+(this.taxConfig.currentTax/100));
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

        let str = option.label;
        if(price){
            if (this.taxConfig.showBothPrices) {
                str+= ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str+= ' ' + this.formatPrice(price, true);
            }
        }
        return str;
    },

    formatPrice: function(price, showSign){
        let str = '';
        price = parseFloat(price);
        if(showSign){
            if(price<0){
                str+= '-';
                price = -price;
            }
            else{
                str+= '+';
            }
        }

        const roundedPrice = (Math.round(price*100)/100).toString();

        if (this.prices && this.prices[roundedPrice]) {
            str+= this.prices[roundedPrice];
        }
        else {
            str+= this.priceTemplate.evaluate({price: price.toFixed(2)});
        }
        return str;
    },

    clearSelect: function(element){
        for(let i=element.options.length-1;i>=0;i--){
            element.remove(i);
        }
    },

    getAttributeOptions: function(attributeId){
        if(this.config.attributes[attributeId]){
            return this.config.attributes[attributeId].options;
        }
    },

    reloadPrice: function(){
        let price    = 0;
        let oldPrice = 0;
        for(let i=this.settings.length-1;i>=0;i--){
            const selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice.reload();

        return price;
    },

    reloadOldPrice: function(){
        if (document.getElementById('old-price-'+this.config.productId)) {

            let price = parseFloat(this.config.oldPrice);
            for(let i=this.settings.length-1;i>=0;i--){
                const selected = this.settings[i].options[this.settings[i].selectedIndex];
                if(selected.config){
                    const parsedOldPrice = parseFloat(selected.config.oldPrice);
                    price += isNaN(parsedOldPrice) ? 0 : parsedOldPrice;
                }
            }
            if (price < 0)
                price = 0;
            price = this.formatPrice(price);

            if(document.getElementById('old-price-'+this.config.productId)){
                document.getElementById('old-price-'+this.config.productId).innerHTML = price;
            }

        }
    }
};
Varien.classCompat(Product.Config);


/**************************** SUPER PRODUCTS ********************************/

Product.Super = {};
Product.Super.Configurable = function(container, observeCss, updateUrl, updatePriceUrl, priceContainerId) {
    this.initialize(container, observeCss, updateUrl, updatePriceUrl, priceContainerId);
};

Product.Super.Configurable.prototype = {
    initialize: function(container, observeCss, updateUrl, updatePriceUrl, priceContainerId) {
        this.container = document.getElementById(container);
        this.observeCss = observeCss;
        this.updateUrl = updateUrl;
        this.updatePriceUrl = updatePriceUrl;
        this.priceContainerId = priceContainerId;
        this.registerObservers();
    },
    registerObservers: function() {
        const elements = this.container.getElementsByClassName(this.observeCss);
        Array.from(elements).forEach(function(element){
            element.addEventListener('change', this.update.bind(this));
        }.bind(this));
        return this;
    },
    update: function(event) {
        const elements = this.container.getElementsByClassName(this.observeCss);
        // Form.serializeElements skipped disabled fields and unchecked boxes,
        // and sent every selected option of a multi-select
        const params = new URLSearchParams();
        Array.from(elements).forEach(function(element) {
            if (!element.name || element.disabled) return;
            if ((element.type === 'checkbox' || element.type === 'radio') && !element.checked) return;
            if (element.type === 'select-multiple') {
                Array.from(element.options).forEach(function(opt) {
                    if (opt.selected) params.append(element.name, opt.value);
                });
                return;
            }
            params.append(element.name, element.value);
        });
        const paramString = params.toString();

        const self = this;
        fetch(this.updateUrl + '?ajax=1', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: paramString
        }).then(function(response) {
            // Ajax.Updater only replaced the container on a 2xx status
            return response.ok ? response.text() : null;
        }).then(function(html) {
            if (html !== null) {
                self.container.innerHTML = html;
            }
            self.registerObservers();
        });

        const priceContainer = document.getElementById(this.priceContainerId);
        if(priceContainer) {
            fetch(this.updatePriceUrl + '?ajax=1', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: paramString
            }).then(function(response) {
                return response.ok ? response.text() : null;
            }).then(function(html) {
                if (html !== null) {
                    priceContainer.innerHTML = html;
                }
            });
        }
    }
};
Varien.classCompat(Product.Super.Configurable);
