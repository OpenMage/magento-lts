/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var Packaging = function (params) {
    this.initialize(params);
};
Packaging.prototype = {
    /**
     * Initialize object
     */
    initialize: function(params) {
        this.packageIncrement = 0;
        this.packages = [];
        this.itemsAll = [];
        this.createLabelUrl = params.createLabelUrl ? params.createLabelUrl : null;
        this.itemsGridUrl = params.itemsGridUrl ? params.itemsGridUrl : null;
        this.errorQtyOverLimit = params.errorQtyOverLimit;
        this.titleDisabledSaveBtn = params.titleDisabledSaveBtn;
        this.window = document.getElementById('packaging_window');
        this.windowMask = document.getElementById('popup-window-mask');
        this.messages = this.window.querySelectorAll('.messages')[0];
        this.packagesContent = document.getElementById('packages_content');
        this.template = document.getElementById('package_template');
        this.paramsCreateLabelRequest = {};
        this.validationErrorMsg = params.validationErrorMsg;

        this.defaultItemsQty            = params.shipmentItemsQty ? params.shipmentItemsQty : null;
        this.defaultItemsPrice          = params.shipmentItemsPrice ? params.shipmentItemsPrice : null;
        this.defaultItemsName           = params.shipmentItemsName ? params.shipmentItemsName : null;
        this.defaultItemsWeight         = params.shipmentItemsWeight ? params.shipmentItemsWeight : null;
        this.defaultItemsProductId      = params.shipmentItemsProductId ? params.shipmentItemsProductId : null;
        this.defaultItemsOrderItemId    = params.shipmentItemsOrderItemId ? params.shipmentItemsOrderItemId : null;

        this.shippingInformation= params.shippingInformation ? params.shippingInformation : null;
        this.thisPage           = params.thisPage ? params.thisPage : null;
        this.customizableContainers = params.customizable ? params.customizable : [];

        this.eps = .000001;
    },

//******************** Setters **********************************//
    setLabelCreatedCallback: function(callback) {
        this.labelCreatedCallback = callback;
    },
    setCancelCallback: function(callback) {
        this.cancelCallback = callback;
    },
    setConfirmPackagingCallback: function(callback) {
        this.confirmPackagingCallback = callback;
    },
    setItemQtyCallback: function(callback) {
        this.itemQtyCallback = callback;
    },
    setCreateLabelUrl: function(url) {
        this.createLabelUrl = url;
    },
    setParamsCreateLabelRequest: function(params) {
        Object.assign(this.paramsCreateLabelRequest, params);
    },
//******************** End Setters *******************************//

    showWindow: function() {
        if (!this.packagesContent.children.length) {
            this.newPackage();
        }
        this.window.style.display = '';
        this.window.style.marginLeft = -this.window.offsetWidth / 2 + 'px';
        var htmlBody = document.getElementById('html-body');
        this.windowMask.style.height = htmlBody.scrollHeight + 'px';
        this.windowMask.style.display = '';
    },

    cancelPackaging: function() {
        packaging.window.style.display = 'none';
        packaging.windowMask.style.display = 'none';
        if (typeof this.cancelCallback === 'function') {
            this.cancelCallback();
        }
    },

    confirmPackaging: function(params) {
        if (typeof this.confirmPackagingCallback === 'function') {
            this.confirmPackagingCallback();
        }
    },

    checkAllItems: function(headCheckbox) {
        var el = headCheckbox;
        while (el && el.tagName !== 'TABLE') {
            el = el.parentNode;
        }
        var checkboxes = el ? Array.from(el.querySelectorAll('tbody input[type="checkbox"]')) : [];
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = headCheckbox.checked;
            this._observeQty.call(checkbox);
        }.bind(this));
    },

    cleanPackages: function() {
        this.packagesContent.innerHTML = '';
        this.packages = [];
        this.itemsAll = [];
        this.packageIncrement = 0;
        this._setAllItemsPackedState();
        this.messages.style.display = 'none';
        this.messages.innerHTML = '';
    },

    sendCreateLabelRequest: function() {
        var self = this;
        if (!this.validate()) {
            this.messages.style.display = '';
            this.messages.innerHTML = this.validationErrorMsg;
            return;
        } else {
            this.messages.style.display = 'none';
            this.messages.innerHTML = '';
        }
        if (this.createLabelUrl) {
            var weight, length, width, height = null;
            var packagesParams = [];
            Array.from(this.packagesContent.children).forEach(function(pack) {
                var packageId = pack.id.match(/\d+$/)[0];
                weight = parseFloat(pack.querySelector('input[name="container_weight"]').value);
                length = parseFloat(pack.querySelector('input[name="container_length"]').value);
                width = parseFloat(pack.querySelector('input[name="container_width"]').value);
                height = parseFloat(pack.querySelector('input[name="container_height"]').value);
                packagesParams[packageId] = {
                    container:                  pack.querySelector('select[name="package_container"]').value,
                    customs_value:              parseFloat(pack.querySelector('input[name="package_customs_value"]').value, 10),
                    weight:                     isNaN(weight) ? '' : weight,
                    length:                     isNaN(length) ? '' : length,
                    width:                      isNaN(width) ? '' : width,
                    height:                     isNaN(height) ? '' : height,
                    weight_units:               pack.querySelector('select[name="container_weight_units"]').value,
                    dimension_units:            pack.querySelector('select[name="container_dimension_units"]').value
                };
                if (isNaN(packagesParams[packageId]['customs_value'])) {
                    packagesParams[packageId]['customs_value'] = 0;
                }
                var packageSizeEl = pack.querySelector('select[name="package_size"]');
                if (typeof packageSizeEl !== 'undefined' && packageSizeEl !== null) {
                    if ('' != packageSizeEl.value) {
                        packagesParams[packageId]['size'] = packageSizeEl.value;
                    }
                }
                var containerGirthEl = pack.querySelector('input[name="container_girth"]');
                if (typeof containerGirthEl !== 'undefined' && containerGirthEl !== null) {
                    if ('' != containerGirthEl.value) {
                        packagesParams[packageId]['girth'] = containerGirthEl.value;
                        packagesParams[packageId]['girth_dimension_units'] = pack.querySelector('select[name="container_girth_dimension_units"]').value;
                    }
                }
                var contentTypeEl = pack.querySelector('select[name="content_type"]');
                var contentTypeOtherEl = pack.querySelector('input[name="content_type_other"]');
                if (contentTypeEl !== null && contentTypeOtherEl !== null) {
                    packagesParams[packageId]['content_type'] = contentTypeEl.value;
                    packagesParams[packageId]['content_type_other'] = contentTypeOtherEl.value;
                } else {
                    packagesParams[packageId]['content_type'] = '';
                    packagesParams[packageId]['content_type_other'] = '';
                }
                var deliveryConfirmation = pack.querySelectorAll('select[name="delivery_confirmation_types"]');
                if (deliveryConfirmation.length) {
                     packagesParams[packageId]['delivery_confirmation'] = deliveryConfirmation[0].value;
                }
            }.bind(this));
            for (var packageId in this.packages) {
                 if (!isNaN(packageId)) {
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[container]']              = packagesParams[packageId]['container'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[weight]']                 = packagesParams[packageId]['weight'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[customs_value]']          = packagesParams[packageId]['customs_value'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[length]']                 = packagesParams[packageId]['length'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[width]']                  = packagesParams[packageId]['width'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[height]']                 = packagesParams[packageId]['height'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[weight_units]']           = packagesParams[packageId]['weight_units'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[dimension_units]']        = packagesParams[packageId]['dimension_units'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[content_type]']           = packagesParams[packageId]['content_type'];
                     this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[content_type_other]']     = packagesParams[packageId]['content_type_other'];

                     if ('undefined' != typeof packagesParams[packageId]['size']) {
                         this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[size]'] = packagesParams[packageId]['size'];
                     }

                     if ('undefined' != typeof packagesParams[packageId]['girth']) {
                         this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[girth]'] = packagesParams[packageId]['girth'];
                         this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[girth_dimension_units]'] = packagesParams[packageId]['girth_dimension_units'];
                     }

                     if ('undefined' != typeof packagesParams[packageId]['delivery_confirmation']) {
                         this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[delivery_confirmation]']  = packagesParams[packageId]['delivery_confirmation'];
                     }
                     for (var packedItemId in this.packages[packageId]['items']) {
                         if (!isNaN(packedItemId)) {
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][qty]']           = this.packages[packageId]['items'][packedItemId]['qty'];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][customs_value]'] = this.packages[packageId]['items'][packedItemId]['customs_value'];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][price]']         = self.defaultItemsPrice[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][name]']          = self.defaultItemsName[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][weight]']        = self.defaultItemsWeight[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][product_id]']    = self.defaultItemsProductId[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][order_item_id]'] = self.defaultItemsOrderItemId[packedItemId];
                         }
                     }
                 }
            }

            var requestParams = new URLSearchParams();
            for (var key in this.paramsCreateLabelRequest) {
                if (this.paramsCreateLabelRequest.hasOwnProperty(key)) {
                    requestParams.append(key, this.paramsCreateLabelRequest[key]);
                }
            }
            requestParams.append('form_key', window.FORM_KEY);
            fetch(this.createLabelUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: requestParams.toString()
            }).then(function(resp) {
                return resp.text();
            }).then(function(responseText) {
                try {
                    var response = JSON.parse(responseText);
                    if (response.error) {
                        this.messages.style.display = '';
                        this.messages.innerHTML = response.message;
                    } else if (response.ok && typeof this.labelCreatedCallback === 'function') {
                        this.labelCreatedCallback(response);
                    }
                } catch(e) {
                    // response was not JSON, ignore
                }
            }.bind(this));

            if (this.paramsCreateLabelRequest['code']
                && this.paramsCreateLabelRequest['carrier_title']
                && this.paramsCreateLabelRequest['method_title']
                && this.paramsCreateLabelRequest['price']
            ) {
                var a = this.paramsCreateLabelRequest['code'];
                var b = this.paramsCreateLabelRequest['carrier_title'];
                var c = this.paramsCreateLabelRequest['method_title'];
                var d = this.paramsCreateLabelRequest['price'];

                this.paramsCreateLabelRequest = {};
                this.paramsCreateLabelRequest['code']           = a;
                this.paramsCreateLabelRequest['carrier_title']  = b;
                this.paramsCreateLabelRequest['method_title']   = c;
                this.paramsCreateLabelRequest['price']          = d;
            } else {
                this.paramsCreateLabelRequest = {};
            }
        }
    },

    validate: function() {
        var dimensionElements = Array.from(document.getElementById('packaging_window').querySelectorAll(
            'input[name=container_length],input[name=container_width],input[name=container_height]'
        ));
        var callback = null;
        if (dimensionElements.some(function(element) { return !!element.value; })) {
            callback = function(element) { element.classList.add('required-entry'); };
        } else {
            callback = function(element) { element.classList.remove('required-entry'); };
        }
        dimensionElements.forEach(callback);

        return result = Array.from(document.querySelectorAll('[id^="package_block_"] input')).map(function (element) {
            return this.validateElement(element);
        }, this).every(function(v) { return v; });
    },

    validateElement: function(elm) {
        var cn = elm.className.split(/\s+/).filter(function(s) { return s.length > 0; });
        return result = cn.every(function(value) {
            var v = Validation.get(value);
            if (Validation.isVisible(elm) && !v.test(elm.value, elm)) {
                elm.classList.add('validation-failed');
                return false;
            } else {
                elm.classList.remove('validation-failed');
                return true;
            }
        });
    },

    validateCustomsValue: function() {
        var items = [];
        var isValid = true;
        var itemsPrepare = [];
        var itemsPacked = [];

        Array.from(this.packagesContent.children).forEach(function(pack) {
            itemsPrepare = pack.querySelectorAll('.package_prapare')[0];
            if (itemsPrepare) {
                items = items.concat(Array.from(itemsPrepare.querySelectorAll('.grid tbody tr')));
            }
            itemsPacked = pack.querySelectorAll('.package_items')[0];
            if (itemsPacked) {
                items = items.concat(Array.from(itemsPacked.querySelectorAll('.grid tbody tr')));
            }
        }.bind(this));

        items.forEach(function(item) {
            var itemCustomsValue = item.querySelector('[name="customs_value"]');
            if (!this.validateElement(itemCustomsValue)) {
                isValid = false;
            }
        }.bind(this));

        if (isValid) {
            this.messages.style.display = 'none';
            this.messages.innerHTML = '';
        } else {
            this.messages.style.display = '';
            this.messages.innerHTML = this.validationErrorMsg;
        }
        return isValid;
    },

    newPackage: function() {
        var pack = this.template.cloneNode(true);
        pack.id = 'package_block_' + ++this.packageIncrement;
        pack.classList.add('package-block');
        pack.querySelector('.package-number span').innerHTML = this.packageIncrement;
        this.packagesContent.insertAdjacentElement('afterbegin', pack);
        pack.querySelector('.AddSelectedBtn').style.display = 'none';
        pack.style.display = '';
    },

    deletePackage: function(obj) {
        var pack = obj.closest('div[id^="package_block"]');
        var packItems = pack.querySelectorAll('.package_items')[0];
        var packageId = pack.id.match(/\d$/)[0];

        delete this.packages[packageId];
        pack.remove();
        this.messages.style.display = 'none';
        this.messages.innerHTML = '';
        this._setAllItemsPackedState();
    },

    deleteItem: function(obj) {
        var item = obj.closest('tr');
        var itemId = item.querySelector('[type="checkbox"]').value;
        var pack = obj.closest('div[id^="package_block"]');
        var packItems = pack.querySelectorAll('.package_items')[0];
        var packageId = pack.id.match(/\d$/)[0];

        delete this.packages[packageId]['items'][itemId];
        if (item.offsetParent.rows.length <= 2) { /* head + this last row */
            packItems.style.display = 'none';
        }
        item.remove();
        this.messages.style.display = 'none';
        this.messages.innerHTML = '';
        this._recalcContainerWeightAndCustomsValue(packItems);
        this._setAllItemsPackedState();
    },

    recalcContainerWeightAndCustomsValue: function(obj) {
        var pack = obj.closest('div[id^="package_block"]');
        var packItems = pack.querySelectorAll('.package_items')[0];
        if (packItems) {
            if (!this.validateCustomsValue()) {
                return;
            }
            this._recalcContainerWeightAndCustomsValue(packItems);
        }
    },

    getItemsForPack: function(obj) {
        if (this.itemsGridUrl) {
            var packageBlock = obj.closest('[id^="package_block"]');
            var packagePrapare = packageBlock.querySelectorAll('.package_prapare')[0];
            var packagePrapareGrid = packagePrapare.querySelectorAll('.grid_prepare')[0];

            var requestParams = new URLSearchParams();
            requestParams.append('shipment_id', this.shipmentId);
            requestParams.append('form_key', window.FORM_KEY);

            fetch(this.itemsGridUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: requestParams.toString()
            }).then(function(resp) {
                return resp.text();
            }).then(function(response) {
                if (response) {
                    packagePrapareGrid.innerHTML = response;
                    this._processPackagePrapare(packagePrapareGrid);
                    if (packagePrapareGrid.querySelectorAll('.grid tbody tr').length) {
                        packageBlock.querySelector('.AddItemsBtn').style.display = 'none';
                        packageBlock.querySelector('.AddSelectedBtn').style.display = '';
                        packagePrapare.style.display = '';
                    } else {
                        packagePrapareGrid.innerHTML = '';
                    }
                }
            }.bind(this));
        }
    },

    getPackedItemsQty: function() {
        var items = [];
        for (var packageId in this.packages) {
             if (!isNaN(packageId)) {
                 for (var packedItemId in this.packages[packageId]['items']) {
                     if (!isNaN(packedItemId)) {
                         if (items[packedItemId]) {
                             items[packedItemId] += this.packages[packageId]['items'][packedItemId]['qty'];
                         } else {
                             items[packedItemId] = this.packages[packageId]['items'][packedItemId]['qty'];
                         }
                     }
                 }
             }
        }
        return items;
    },

    _parseQty: function(obj) {
        var qty = obj.classList.contains('qty-decimal') ? parseFloat(obj.value) : parseInt(obj.value);
        if (isNaN(qty) || qty <= 0) {
            qty = 1;
        }
        return qty;
    },

    packItems: function(obj) {
        var anySelected = false;
        var packageBlock = obj.closest('[id^="package_block"]');
        var packageId = packageBlock.id.match(/\d$/)[0];
        var packagePrepare = packageBlock.querySelectorAll('.package_prapare')[0];
        var packagePrepareGrid = packagePrepare.querySelectorAll('.grid_prepare')[0];

        // check for exceeds the total shipped quantity
        var checkExceedsQty = false;
        this.messages.style.display = 'none';
        this.messages.innerHTML = '';
        Array.from(packagePrepareGrid.querySelectorAll('.grid tbody tr')).forEach(function(item) {
            var checkbox = item.querySelector('[type="checkbox"]');
            var itemId = checkbox.value;
            var qtyValue  = this._parseQty(item.querySelector('[name="qty"]'));
            item.querySelector('[name="qty"]').value = qtyValue;
            if (checkbox.checked && this._checkExceedsQty(itemId, qtyValue)) {
                this.messages.style.display = '';
                this.messages.innerHTML = this.errorQtyOverLimit;
                checkExceedsQty = true;
            }
        }.bind(this));
        if (checkExceedsQty) {
            return;
        }

        if (!this.validateCustomsValue()) {
            return;
        }

        // prepare items for packing
        Array.from(packagePrepareGrid.querySelectorAll('.grid tbody tr')).forEach(function(item) {
            var checkbox = item.querySelector('[type="checkbox"]');
            if (checkbox.checked) {
                var qty  = item.querySelector('[name="qty"]');
                var qtyValue  = this._parseQty(qty);
                item.querySelector('[name="qty"]').value = qtyValue;
                anySelected = true;
                qty.disabled = true;
                checkbox.closest('td').style.display = 'none';
                packagePrepareGrid.querySelector('.grid th [type="checkbox"]').closest('th').style.display = 'none';
                item.querySelector('.delete').style.display = '';
            } else {
                item.remove();
            }
        }.bind(this));

        // packing items
        if (anySelected) {
            var packItems = packageBlock.querySelectorAll('.package_items')[0];
            if (!packItems) {
                var newGridPrepare = document.createElement('div');
                newGridPrepare.className = 'grid_prepare';
                packagePrepare.appendChild(newGridPrepare);
                packagePrepare.parentNode.insertBefore(packagePrepareGrid, packagePrepare.nextSibling);
                packagePrepareGrid.classList.remove('grid_prepare');
                packagePrepareGrid.classList.add('package_items');
                packItems = packagePrepareGrid;
                Array.from(packItems.querySelectorAll('.grid tbody tr')).forEach(function(item) {
                    var itemId = item.querySelector('[type="checkbox"]').value;
                    var qtyValue  = parseFloat(item.querySelector('[name="qty"]').value);
                    qtyValue = (qtyValue <= 0) ? 1 : qtyValue;

                    if ('undefined' == typeof this.packages[packageId]) {
                        this.packages[packageId] = {'items': [], 'params': {}};
                    }
                    if ('undefined' == typeof this.packages[packageId]['items'][itemId]) {
                        this.packages[packageId]['items'][itemId] = {};
                        this.packages[packageId]['items'][itemId]['qty'] = qtyValue;
                    } else {
                        this.packages[packageId]['items'][itemId]['qty'] += qtyValue;
                    }
                }.bind(this));
            } else {
                Array.from(packagePrepareGrid.querySelectorAll('.grid tbody tr')).forEach(function(item) {
                    var itemId = item.querySelector('[type="checkbox"]').value;
                    var qtyValue  = parseFloat(item.querySelector('[name="qty"]').value);
                    qtyValue = (qtyValue <= 0) ? 1 : qtyValue;

                    if ('undefined' == typeof this.packages[packageId]['items'][itemId]) {
                        this.packages[packageId]['items'][itemId] = {};
                        this.packages[packageId]['items'][itemId]['qty'] = qtyValue;
                        packItems.querySelector('.grid tbody').appendChild(item);
                    } else {
                        this.packages[packageId]['items'][itemId]['qty'] += qtyValue;
                        var packItem = packItems.querySelector('[type="checkbox"][value="'+itemId+'"]').closest('tr').querySelector('[name="qty"]');
                        packItem.value = this.packages[packageId]['items'][itemId]['qty'];
                    }
                }.bind(this));
                packagePrepareGrid.innerHTML = '';
            }
            packItems.style.display = '';
            this._recalcContainerWeightAndCustomsValue(packItems);
        } else {
            packagePrepareGrid.innerHTML = '';
        }

        // show/hide disable/enable
        packagePrepare.style.display = 'none';
        packageBlock.querySelector('.AddSelectedBtn').style.display = 'none';
        packageBlock.querySelector('.AddItemsBtn').style.display = '';
        this._setAllItemsPackedState();
    },

    validateItemQty: function (itemId, qty) {
        return (this.defaultItemsQty[itemId] < qty) ? this.defaultItemsQty[itemId] : qty;
    },

    changeMeasures: function(obj) {
        var incr = 0;
        var incrSelected = 0;
        Array.from(obj.children).forEach(function(option) {
            if (option.selected) {
                incrSelected = incr;
            }
            incr++;
        });

        var packageBlock = obj.closest('[id^="package_block"]');
        Array.from(packageBlock.querySelectorAll('.measures')).forEach(function(item){
            if (item.name != obj.name) {
                var incr = 0;
                Array.from(item.querySelectorAll('option')).forEach(function(option){
                    if (incr == incrSelected) {
                        item.value = option.value;
                    }
                    incr++;
                });
            }
        });

    },

    checkSizeAndGirthParameter: function(obj, enabled) {
        if (enabled == 0) {
            return;
        }
        var currentNode = obj;

        while (currentNode.nodeName != 'TBODY') {
            currentNode = currentNode.parentNode;
        }
        if (!currentNode) {
            return;
        }

        var packageSize = currentNode.querySelectorAll('select[name=package_size]');
        var packageContainer = currentNode.querySelectorAll('select[name=package_container]');
        var packageGirth = currentNode.querySelectorAll('input[name=container_girth]');
        var packageGirthDimensionUnits = currentNode.querySelectorAll('select[name=container_girth_dimension_units]');

        if (packageSize.length <= 0) {
            return;
        }

        var girthEnabled = (packageSize[0].value == 'LARGE' && (packageContainer[0].value == 'NONRECTANGULAR'
            || packageContainer[0].value == 'VARIABLE' ));

        if (!girthEnabled) {
            packageGirth[0].value='';
            packageGirth[0].disabled = true;
            packageGirth[0].classList.add('disabled');
            packageGirthDimensionUnits[0].disabled = true;
            packageGirthDimensionUnits[0].classList.add('disabled');
        } else {
            packageGirth[0].disabled = false;
            packageGirth[0].classList.remove('disabled');
            packageGirthDimensionUnits[0].disabled = false;
            packageGirthDimensionUnits[0].classList.remove('disabled');
        }

        var sizeEnabled = (packageContainer[0].value == 'NONRECTANGULAR' || packageContainer[0].value == 'RECTANGULAR'
            || packageContainer[0].value == 'VARIABLE');

        if (!sizeEnabled) {
            option = document.createElement('OPTION');
            option.value = '';
            option.text = '';
            packageSize[0].options.add(option);
            packageSize[0].value = '';
            packageSize[0].disabled = true;
            packageSize[0].classList.add('disabled');
        } else {
            for (i = 0; i < packageSize[0].length; i ++) {
                if (packageSize[0].options[i].value == '') {
                    packageSize[0].removeChild(packageSize[0].options[i]);
                }
            }
            packageSize[0].disabled = false;
            packageSize[0].classList.remove('disabled');
        }
    },

    changeContainerType: function(obj) {
        if (this.customizableContainers.length <= 0) {
            return;
        }

        var disable = true;
        for (var i in this.customizableContainers) {
            if (this.customizableContainers[i] == obj.value) {
                disable = false;
                break;
            }
        }

        var currentNode = obj;
        while (currentNode.nodeName != 'TBODY') {
            currentNode = currentNode.parentNode;
        }
        if (!currentNode) {
            return;
        }

        Array.from(currentNode.querySelectorAll(
            'input[name=container_length],input[name=container_width],input[name=container_height],select[name=container_dimension_units]'
        )).forEach(function(inputElement) {
            if (disable) {
                inputElement.disabled = true;
                inputElement.classList.add('disabled');
                if (inputElement.nodeName == 'INPUT') {
                    inputElement.value = '';
                }
            } else {
                inputElement.disabled = false;
                inputElement.classList.remove('disabled');
            }
        });
    },

    changeContentTypes: function(obj) {
        var packageBlock = obj.closest('[id^="package_block"]');
        var contentType = packageBlock.querySelector('[name=content_type]');
        var contentTypeOther = packageBlock.querySelector('[name=content_type_other]');
        if (contentType.value == 'OTHER') {
            contentTypeOther.disabled = false;
            contentTypeOther.classList.remove('disabled');
        } else {
            contentTypeOther.disabled = true;
            contentTypeOther.classList.add('disabled');
        }

    },

//******************** Private functions **********************************//
    _getItemsCount: function(items) {
        var count = 0;
        items.forEach(function(itemCount) {
            if (!isNaN(itemCount)) {
                count += parseFloat(itemCount);
            }
        });
        return count;
    },

    /**
     * Show/hide disable/enable buttons in case of all items packed state
     */
    _setAllItemsPackedState: function() {
        var addPackageBtn = this.window.querySelectorAll('.AddPackageBtn')[0];
        var savePackagesBtn = this.window.querySelectorAll('.SavePackagesBtn')[0];
        if (this._getItemsCount(this.itemsAll) > 0
                && (this._checkExceedsQtyFinal(this._getItemsCount(this.getPackedItemsQty()),this._getItemsCount(this.itemsAll)))
        ) {
            Array.from(this.packagesContent.querySelectorAll('.AddItemsBtn')).forEach(function(button){
                button.disabled = true;
                button.classList.add('disabled');
            });
            addPackageBtn.classList.add('disabled');
            addPackageBtn.disabled = true;
            savePackagesBtn.classList.remove('disabled');
            savePackagesBtn.disabled = false;
            savePackagesBtn.title = '';

            // package number recalculation
            var packagesRecalc = [];
            Array.from(this.packagesContent.children).forEach(function(pack) {
                if (!pack.querySelectorAll('.package_items .grid tbody tr').length) {
                    pack.remove();
                }
            }.bind(this));
            var packagesCount = this.packagesContent.children.length;
            this.packageIncrement = packagesCount;
            Array.from(this.packagesContent.children).forEach(function(pack) {
                var packageId = pack.id.match(/\d$/)[0];
                pack.id = 'package_block_' + packagesCount;
                pack.querySelector('.package-number span').innerHTML = packagesCount;
                packagesRecalc[packagesCount] = this.packages[packageId];
                --packagesCount;
            }.bind(this));
            this.packages = packagesRecalc;

        } else {
            Array.from(this.packagesContent.querySelectorAll('.AddItemsBtn')).forEach(function(button){
                button.classList.remove('disabled');
                button.disabled = false;
            });
            addPackageBtn.classList.remove('disabled');
            addPackageBtn.disabled = false;
            savePackagesBtn.classList.add('disabled');
            savePackagesBtn.disabled = true;
            savePackagesBtn.title = this.titleDisabledSaveBtn;
        }
    },

    _processPackagePrapare: function(packagePrapare) {
        var itemsAll = [];
        Array.from(packagePrapare.querySelectorAll('.grid tbody tr')).forEach(function(item) {
            var qty  = item.querySelector('[name="qty"]');
            var itemId = item.querySelector('[type="checkbox"]').value;
            var qtyValue = 0;
            if (typeof this.itemQtyCallback === 'function') {
                var value = this.itemQtyCallback(itemId);
                qtyValue = ((typeof value == 'string') && (value.length == 0)) ? 0 : parseFloat(value);
                if (isNaN(qtyValue) || qtyValue < 0) {
                    qtyValue = 1;
                }
                qtyValue = this.validateItemQty(itemId, qtyValue);
                qty.value = qtyValue;
            } else {
                var value = item.querySelector('[name="qty"]').value;
                qtyValue = ((typeof value == 'string') && (value.length == 0)) ? 0 : parseFloat(value);
                if (isNaN(qtyValue) || qtyValue < 0) {
                    qtyValue = 1;
                }
            }
            if (qtyValue == 0) {
                item.remove();
                return;
            }
            var packedItems = this.getPackedItemsQty();
            itemsAll[itemId] = qtyValue;
            for (var packedItemId in packedItems) {
                if (!isNaN(packedItemId)) {
                    var packedQty = packedItems[packedItemId];
                    if (itemId == packedItemId) {
                        if (qtyValue == packedQty || qtyValue <= packedQty) {
                            item.remove();
                        } else if (qtyValue > packedQty) {
                            /* fix float number precision */
                            qty.value = Number((qtyValue - packedQty).toFixed(4));
                        }
                    }
                }
            }
        }.bind(this));
        if (!this.itemsAll.length) {
            this.itemsAll = itemsAll;
        }

        Array.from(packagePrapare.querySelectorAll('tbody input[type="checkbox"]')).forEach(function(item){
            item.addEventListener('change', this._observeQty);
            this._observeQty.call(item);
        }.bind(this));
    },

    _observeQty: function() {
        /** this = input[type="checkbox"] */
        var tr  = this.parentNode.parentNode,
            qty = tr.cells[tr.cells.length - 1].querySelector('input[name="qty"]');

        if (qty.disabled = !this.checked) {
            qty.classList.add('disabled');
        } else {
            qty.classList.remove('disabled');
        }
    },

    _checkExceedsQty: function(itemId, qty) {
        var packedItemQty = this.getPackedItemsQty()[itemId] ? this.getPackedItemsQty()[itemId] : 0;
        var allItemQty = this.itemsAll[itemId];
        return (qty * (1 - this.eps) > (allItemQty *  (1 + this.eps)  - packedItemQty * (1 - this.eps)));
    },

    _checkExceedsQtyFinal: function(checkOne, defQty) {
        return checkOne * (1 + this.eps) >= defQty * (1 - this.eps);
    },

    _recalcContainerWeightAndCustomsValue: function(container) {
        var packageBlock = container.closest('[id^="package_block"]');
        var packageId = packageBlock.id.match(/\d$/)[0];
        var containerWeight = packageBlock.querySelector('[name="container_weight"]');
        var containerCustomsValue = packageBlock.querySelector('[name="package_customs_value"]');
        containerWeight.value = 0;
        containerCustomsValue.value = 0;
        Array.from(container.querySelectorAll('.grid tbody tr')).forEach(function(item) {
            var itemId = item.querySelector('[type="checkbox"]').value;
            var qtyValue  = parseFloat(item.querySelector('[name="qty"]').value);
            if (isNaN(qtyValue) || qtyValue <= 0) {
                qtyValue = 1;
                item.querySelector('[name="qty"]').value = qtyValue;
            }
            var itemWeight = parseFloat(this._getElementText(item.querySelectorAll('.weight')[0]));
            containerWeight.value = parseFloat(containerWeight.value) + (itemWeight * qtyValue);
            var itemCustomsValue = parseFloat(item.querySelector('[name="customs_value"]').value) || 0;
            containerCustomsValue.value = parseFloat(containerCustomsValue.value) + itemCustomsValue * qtyValue;
            this.packages[packageId]['items'][itemId]['customs_value'] = itemCustomsValue;
        }.bind(this));
        containerWeight.value = parseFloat(parseFloat(containerWeight.value).toFixed(4));
        containerCustomsValue.value = parseFloat(containerCustomsValue.value).toFixed(2);
        if (containerCustomsValue.value == 0) {
            containerCustomsValue.value = '';
        }
    },

    _getElementText: function(el) {
        if ('string' == typeof el.textContent) {
            return el.textContent;
        }
        if ('string' == typeof el.innerText) {
            return el.innerText;
        }
        return el.innerHTML.replace(/<[^>]*>/g,'');
    }
//******************** End Private functions ******************************//
};
