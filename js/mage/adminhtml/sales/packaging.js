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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var Packaging = Class.create();
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
        this.window = $('packaging_window');
        this.windowMask = $('popup-window-mask');
        this.messages = this.window.select('.messages')[0];
        this.packagesContent = $('packages_content');
        this.template = $('package_template');
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
        Object.extend(this.paramsCreateLabelRequest, params);
    },
//******************** End Setters *******************************//

    showWindow: function() {
        if (this.packagesContent.childElements().length == 0) {
            this.newPackage();
        }
        this.window.show().setStyle({
            'marginLeft': -this.window.getDimensions().width/2 + 'px'
        });
        this.windowMask.setStyle({
            height: $('html-body').getHeight() + 'px'
        }).show();
    },

    cancelPackaging: function() {
        packaging.window.hide();
        packaging.windowMask.hide();
        if (Object.isFunction(this.cancelCallback)) {
            this.cancelCallback();
        }
    },

    confirmPackaging: function(params) {
        if (Object.isFunction(this.confirmPackagingCallback)) {
            this.confirmPackagingCallback();
        }
    },

    checkAllItems: function(headCheckbox) {
        $(headCheckbox).up('table').select('tbody input[type="checkbox"]').each(function(checkbox){
            checkbox.checked = headCheckbox.checked;
            this._observeQty.call(checkbox);
        }.bind(this));
    },

    cleanPackages: function() {
        this.packagesContent.update();
        this.packages = [];
        this.itemsAll = [];
        this.packageIncrement = 0;
        this._setAllItemsPackedState();
        this.messages.hide().update();
    },

    sendCreateLabelRequest: function() {
        var package = this;
        if (!this.validate()) {
            this.messages.show().update(this.validationErrorMsg);
            return;
        } else {
            this.messages.hide().update();
        }
        if (this.createLabelUrl) {
            var weight, length, width, height = null;
            var packagesParams = [];
            this.packagesContent.childElements().each(function(pack) {
                var packageId = pack.id.match(/\d+$/)[0];
                weight = parseFloat(pack.select('input[name="container_weight"]')[0].value);
                length = parseFloat(pack.select('input[name="container_length"]')[0].value);
                width = parseFloat(pack.select('input[name="container_width"]')[0].value);
                height = parseFloat(pack.select('input[name="container_height"]')[0].value);
                packagesParams[packageId] = {
                    container:                  pack.select('select[name="package_container"]')[0].value,
                    customs_value:              parseFloat(pack.select('input[name="package_customs_value"]')[0].value, 10),
                    weight:                     isNaN(weight) ? '' : weight,
                    length:                     isNaN(length) ? '' : length,
                    width:                      isNaN(width) ? '' : width,
                    height:                     isNaN(height) ? '' : height,
                    weight_units:               pack.select('select[name="container_weight_units"]')[0].value,
                    dimension_units:            pack.select('select[name="container_dimension_units"]')[0].value
                };
                if (isNaN(packagesParams[packageId]['customs_value'])) {
                    packagesParams[packageId]['customs_value'] = 0;
                }
                if ('undefined' != typeof pack.select('select[name="package_size"]')[0]) {
                    if ('' != pack.select('select[name="package_size"]')[0].value) {
                        packagesParams[packageId]['size'] = pack.select('select[name="package_size"]')[0].value;
                    }
                }
                if ('undefined' != typeof pack.select('input[name="container_girth"]')[0]) {
                    if ('' != pack.select('input[name="container_girth"]')[0].value) {
                        packagesParams[packageId]['girth'] = pack.select('input[name="container_girth"]')[0].value;
                        packagesParams[packageId]['girth_dimension_units'] = pack.select('select[name="container_girth_dimension_units"]')[0].value;
                    }
                }
                if ('undefined' != typeof pack.select('select[name="content_type"]')[0] && 'undefined' != typeof pack.select('input[name="content_type_other"]')[0]) {
                    packagesParams[packageId]['content_type'] = pack.select('select[name="content_type"]')[0].value;
                    packagesParams[packageId]['content_type_other'] = pack.select('input[name="content_type_other"]')[0].value;
                } else {
                    packagesParams[packageId]['content_type'] = '';
                    packagesParams[packageId]['content_type_other'] = '';
                }
                var deliveryConfirmation = pack.select('select[name="delivery_confirmation_types"]');
                if (deliveryConfirmation.length) {
                     packagesParams[packageId]['delivery_confirmation'] =  deliveryConfirmation[0].value;
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
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][price]']         = package.defaultItemsPrice[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][name]']          = package.defaultItemsName[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][weight]']        = package.defaultItemsWeight[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][product_id]']    = package.defaultItemsProductId[packedItemId];
                             this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][order_item_id]'] = package.defaultItemsOrderItemId[packedItemId];
                         }
                     }
                 }
            }

            new Ajax.Request(this.createLabelUrl, {
                parameters: this.paramsCreateLabelRequest,
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response.isJSON()) {
                        response = response.evalJSON();
                        if (response.error) {
                            this.messages.show().innerHTML = response.message;
                        } else if (response.ok && Object.isFunction(this.labelCreatedCallback)) {
                            this.labelCreatedCallback(response);
                        }
                    }
                }.bind(this)
            });
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
        var dimensionElements = $("packaging_window").select(
            'input[name=container_length],input[name=container_width],input[name=container_height]'
        );
        var callback = null;
        if ( dimensionElements.any(function(element) { return !!element.value; })) {
            callback = function(element) { $(element).addClassName('required-entry'); };
        } else {
            callback = function(element) { $(element).removeClassName('required-entry'); };
        }
        dimensionElements.each(callback);

        return result = $$('[id^="package_block_"] input').collect(function (element) {
            return this.validateElement(element);
        }, this).all();
    },

    validateElement: function(elm) {
        var cn = $w(elm.className);
        return result = cn.all(function(value) {
            var v = Validation.get(value);
            if (Validation.isVisible(elm) && !v.test($F(elm), elm)) {
                $(elm).addClassName('validation-failed');
                return false;
            } else {
                $(elm).removeClassName('validation-failed');
                return true;
            }
        });
    },

    validateCustomsValue: function() {
        var items = [];
        var isValid = true;
        var itemsPrepare = [];
        var itemsPacked = [];

        this.packagesContent.childElements().each(function(pack) {
            itemsPrepare = pack.select('.package_prapare')[0];
            if (itemsPrepare) {
                items = items.concat(itemsPrepare.select('.grid tbody tr'));
            }
            itemsPacked = pack.select('.package_items')[0];
            if (itemsPacked) {
                items = items.concat(itemsPacked.select('.grid tbody tr'));
            }
        }.bind(this));

        items.each(function(item) {
            var itemCustomsValue = item.select('[name="customs_value"]')[0];
            if (!this.validateElement(itemCustomsValue)) {
                isValid = false;
            }
        }.bind(this));

        if (isValid) {
            this.messages.hide().update();
        } else {
            this.messages.show().update(this.validationErrorMsg);
        }
        return isValid;
    },

    newPackage: function() {
        var pack = this.template.cloneNode(true);
        pack.id = 'package_block_' + ++this.packageIncrement;
        pack.addClassName('package-block');
        pack.select('.package-number span')[0].update(this.packageIncrement);
        this.packagesContent.insert({top: pack});
        pack.select('.AddSelectedBtn')[0].hide();
        pack.show();
    },

    deletePackage: function(obj) {
        var pack = $(obj).up('div[id^="package_block"]');
        var packItems = pack.select('.package_items')[0];
        var packageId = pack.id.match(/\d$/)[0];

        delete this.packages[packageId];
        pack.remove();
        this.messages.hide().update();
        this._setAllItemsPackedState();
    },

    deleteItem: function(obj) {
        var item = $(obj).up('tr');
        var itemId = item.select('[type="checkbox"]')[0].value;
        var pack = $(obj).up('div[id^="package_block"]');
        var packItems = pack.select('.package_items')[0];
        var packageId = pack.id.match(/\d$/)[0];

        delete this.packages[packageId]['items'][itemId];
        if (item.offsetParent.rows.length <= 2) { /* head + this last row */
            $(packItems).hide();
        }
        item.remove();
        this.messages.hide().update();
        this._recalcContainerWeightAndCustomsValue(packItems);
        this._setAllItemsPackedState();
    },

    recalcContainerWeightAndCustomsValue: function(obj) {
        var pack = $(obj).up('div[id^="package_block"]');
        var packItems = pack.select('.package_items')[0];
        if (packItems) {
            if (!this.validateCustomsValue()) {
                return;
            }
            this._recalcContainerWeightAndCustomsValue(packItems);
        }
    },

    getItemsForPack: function(obj) {
        if (this.itemsGridUrl) {
            var parameters = $H({'shipment_id': this.shipmentId});
            var packageBlock = $(obj).up('[id^="package_block"]');
            var packagePrapare = packageBlock.select('.package_prapare')[0];
            var packagePrapareGrid = packagePrapare.select('.grid_prepare')[0];
            new Ajax.Request(this.itemsGridUrl, {
                parameters: parameters,
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response) {
                        packagePrapareGrid.update(response);
                        this._processPackagePrapare(packagePrapareGrid);
                        if (packagePrapareGrid.select('.grid tbody tr').length) {
                            packageBlock.select('.AddItemsBtn')[0].hide();
                            packageBlock.select('.AddSelectedBtn')[0].show();
                            packagePrapare.show();
                        } else {
                            packagePrapareGrid.update();
                        }
                    }
                }.bind(this)
            });
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
        var qty = $(obj).hasClassName('qty-decimal') ? parseFloat(obj.value) : parseInt(obj.value);
        if (isNaN(qty) || qty <= 0) {
            qty = 1;
        }
        return qty;
    },

    packItems: function(obj) {
        var anySelected = false;
        var packageBlock = $(obj).up('[id^="package_block"]');
        var packageId = packageBlock.id.match(/\d$/)[0];
        var packagePrepare = packageBlock.select('.package_prapare')[0];
        var packagePrepareGrid = packagePrepare.select('.grid_prepare')[0];

        // check for exceeds the total shipped quantity
        var checkExceedsQty = false;
        this.messages.hide().update();
        packagePrepareGrid.select('.grid tbody tr').each(function(item) {
            var checkbox = item.select('[type="checkbox"]')[0];
            var itemId = checkbox.value;
            var qtyValue  = this._parseQty(item.select('[name="qty"]')[0]);
            item.select('[name="qty"]')[0].value = qtyValue;
            if (checkbox.checked && this._checkExceedsQty(itemId, qtyValue)) {
                this.messages.show().update(this.errorQtyOverLimit);
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
        packagePrepareGrid.select('.grid tbody tr').each(function(item) {
            var checkbox = item.select('[type="checkbox"]')[0];
            if (checkbox.checked) {
                var qty  = item.select('[name="qty"]')[0];
                var qtyValue  = this._parseQty(qty);
                item.select('[name="qty"]')[0].value = qtyValue;
                anySelected = true;
                qty.disabled = 'disabled';
                checkbox.up('td').hide();
                packagePrepareGrid.select('.grid th [type="checkbox"]')[0].up('th').hide();
                item.select('.delete')[0].show();
            } else {
                item.remove();
            }
        }.bind(this));

        // packing items
        if (anySelected) {
            var packItems = packageBlock.select('.package_items')[0];
            if (!packItems) {
                packagePrepare.insert(new Element('div').addClassName('grid_prepare'));
                packagePrepare.insert({after: packagePrepareGrid});
                packItems = packagePrepareGrid.removeClassName('grid_prepare').addClassName('package_items');
                packItems.select('.grid tbody tr').each(function(item) {
                    var itemId = item.select('[type="checkbox"]')[0].value;
                    var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
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
                packagePrepareGrid.select('.grid tbody tr').each(function(item) {
                    var itemId = item.select('[type="checkbox"]')[0].value;
                    var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
                    qtyValue = (qtyValue <= 0) ? 1 : qtyValue;

                    if ('undefined' == typeof this.packages[packageId]['items'][itemId]) {
                        this.packages[packageId]['items'][itemId] = {};
                        this.packages[packageId]['items'][itemId]['qty'] = qtyValue;
                        packItems.select('.grid tbody')[0].insert(item);
                    } else {
                        this.packages[packageId]['items'][itemId]['qty'] += qtyValue;
                        var packItem = packItems.select('[type="checkbox"][value="'+itemId+'"]')[0].up('tr').select('[name="qty"]')[0];
                        packItem.value = this.packages[packageId]['items'][itemId]['qty'];
                    }
                }.bind(this));
                packagePrepareGrid.update();
            }
            $(packItems).show();
            this._recalcContainerWeightAndCustomsValue(packItems);
        } else {
            packagePrepareGrid.update();
        }

        // show/hide disable/enable
        packagePrepare.hide();
        packageBlock.select('.AddSelectedBtn')[0].hide();
        packageBlock.select('.AddItemsBtn')[0].show();
        this._setAllItemsPackedState();
    },

    validateItemQty: function (itemId, qty) {
        return (this.defaultItemsQty[itemId] < qty) ? this.defaultItemsQty[itemId] : qty;
    },

    changeMeasures: function(obj) {
        var incr = 0;
        var incrSelected = 0;
        obj.childElements().each(function(option) {
            if (option.selected) {
                incrSelected = incr;
            }
            incr++;
        }.bind(this));

        var packageBlock = $(obj).up('[id^="package_block"]');
        packageBlock.select('.measures').each(function(item){
            if (item.name != obj.name) {
                var incr = 0;
                item.select('option').each(function(option){
                    if (incr == incrSelected) {
                        item.value = option.value;
                        //option.selected = true
                    }
                    incr++;
                }.bind(this));
            }
        }.bind(this));

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

        var packageSize = currentNode.select('select[name=package_size]');
        var packageContainer = currentNode.select('select[name=package_container]');
        var packageGirth = currentNode.select('input[name=container_girth]');
        var packageGirthDimensionUnits = currentNode.select('select[name=container_girth_dimension_units]');

        if (packageSize.length <= 0) {
            return;
        }

        var girthEnabled = (packageSize[0].value == 'LARGE' && (packageContainer[0].value == 'NONRECTANGULAR'
            || packageContainer[0].value == 'VARIABLE' ));

        if (!girthEnabled) {
            packageGirth[0].value='';
            packageGirth[0].disable();
            packageGirth[0].addClassName('disabled');
            packageGirthDimensionUnits[0].disable();
            packageGirthDimensionUnits[0].addClassName('disabled');
        } else {
            packageGirth[0].enable();
            packageGirth[0].removeClassName('disabled');
            packageGirthDimensionUnits[0].enable();
            packageGirthDimensionUnits[0].removeClassName('disabled');
        }

        var sizeEnabled = (packageContainer[0].value == 'NONRECTANGULAR' || packageContainer[0].value == 'RECTANGULAR'
            || packageContainer[0].value == 'VARIABLE');

        if (!sizeEnabled) {
            option = document.createElement('OPTION');
            option.value = '';
            option.text = '';
            packageSize[0].options.add(option);
            packageSize[0].value = '';
            packageSize[0].disable();
            packageSize[0].addClassName('disabled');
        } else {
            for (i = 0; i < packageSize[0].length; i ++) {
                if (packageSize[0].options[i].value == '') {
                    packageSize[0].removeChild(packageSize[0].options[i]);
                }
            }
            packageSize[0].enable();
            packageSize[0].removeClassName('disabled');
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

        $(currentNode).select(
            'input[name=container_length],input[name=container_width],input[name=container_height],select[name=container_dimension_units]'
        ).each(function(inputElement) {
            if (disable) {
                Form.Element.disable(inputElement);
                inputElement.addClassName('disabled');
                if (inputElement.nodeName == 'INPUT') {
                    $(inputElement).value = '';
                }
            } else {
                Form.Element.enable(inputElement);
                inputElement.removeClassName('disabled');
            }
        });
    },

    changeContentTypes: function(obj) {
        var packageBlock = $(obj).up('[id^="package_block"]');
        var contentType = packageBlock.select('[name=content_type]')[0];
        var contentTypeOther = packageBlock.select('[name=content_type_other]')[0];
        if (contentType.value == 'OTHER') {
            Form.Element.enable(contentTypeOther);
            contentTypeOther.removeClassName('disabled');
        } else {
            Form.Element.disable(contentTypeOther);
            contentTypeOther.addClassName('disabled');
        }

    },

//******************** Private functions **********************************//
    _getItemsCount: function(items) {
        var count = 0;
        items.each(function(itemCount) {
            if (!isNaN(itemCount)) {
                count += parseFloat(itemCount);
            }
        }.bind(this));
        return count;
    },

    /**
     * Show/hide disable/enable buttons in case of all items packed state
     */
    _setAllItemsPackedState: function() {
        var addPackageBtn = this.window.select('.AddPackageBtn')[0];
        var savePackagesBtn = this.window.select('.SavePackagesBtn')[0];
        if (this._getItemsCount(this.itemsAll) > 0
                && (this._checkExceedsQtyFinal(this._getItemsCount(this.getPackedItemsQty()),this._getItemsCount(this.itemsAll)))
        ) {
            this.packagesContent.select('.AddItemsBtn').each(function(button){
                button.disabled = 'disabled';
                button.addClassName('disabled');
            });
            addPackageBtn.addClassName('disabled');
            Form.Element.disable(addPackageBtn);
            savePackagesBtn.removeClassName('disabled');
            Form.Element.enable(savePackagesBtn);
            savePackagesBtn.title = '';

            // package number recalculation
            var packagesRecalc = [];
            this.packagesContent.childElements().each(function(pack) {
                if (!pack.select('.package_items .grid tbody tr').length) {
                    pack.remove();
                }
            }.bind(this));
            var packagesCount = this.packagesContent.childElements().length;
            this.packageIncrement = packagesCount;
            this.packagesContent.childElements().each(function(pack) {
                var packageId = pack.id.match(/\d$/)[0];
                pack.id = 'package_block_' + packagesCount;
                pack.select('.package-number span')[0].update(packagesCount);
                packagesRecalc[packagesCount] = this.packages[packageId];
                --packagesCount;
            }.bind(this));
            this.packages = packagesRecalc;

        } else {
            this.packagesContent.select('.AddItemsBtn').each(function(button){
                button.removeClassName('disabled');
                Form.Element.enable(button);
            });
            addPackageBtn.removeClassName('disabled');
            Form.Element.enable(addPackageBtn);
            savePackagesBtn.addClassName('disabled');
            Form.Element.disable(savePackagesBtn);
            savePackagesBtn.title = this.titleDisabledSaveBtn;
        }
    },

    _processPackagePrapare: function(packagePrapare) {
        var itemsAll = [];
        packagePrapare.select('.grid tbody tr').each(function(item) {
            var qty  = item.select('[name="qty"]')[0];
            var itemId = item.select('[type="checkbox"]')[0].value;
            var qtyValue = 0;
            if (Object.isFunction(this.itemQtyCallback)) {
                var value = this.itemQtyCallback(itemId);
                qtyValue = ((typeof value == 'string') && (value.length == 0)) ? 0 : parseFloat(value);
                if (isNaN(qtyValue) || qtyValue < 0) {
                    qtyValue = 1;
                }
                qtyValue = this.validateItemQty(itemId, qtyValue);
                qty.value = qtyValue;
            } else {
                var value = item.select('[name="qty"]')[0].value;
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

        packagePrapare.select('tbody input[type="checkbox"]').each(function(item){
            $(item).observe('change', this._observeQty);
            this._observeQty.call(item);
        }.bind(this));
    },

    _observeQty: function() {
        /** this = input[type="checkbox"] */
        var tr  = this.parentNode.parentNode,
            qty = $(tr.cells[tr.cells.length - 1]).select('input[name="qty"]')[0];

        if (qty.disabled = !this.checked) {
            $(qty).addClassName('disabled');
        } else {
            $(qty).removeClassName('disabled');
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
        var packageBlock = container.up('[id^="package_block"]');
        var packageId = packageBlock.id.match(/\d$/)[0];
        var containerWeight = packageBlock.select('[name="container_weight"]')[0];
        var containerCustomsValue = packageBlock.select('[name="package_customs_value"]')[0];
        containerWeight.value = 0;
        containerCustomsValue.value = 0;
        container.select('.grid tbody tr').each(function(item) {
            var itemId = item.select('[type="checkbox"]')[0].value;
            var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
            if (isNaN(qtyValue) || qtyValue <= 0) {
                qtyValue = 1;
                item.select('[name="qty"]')[0].value = qtyValue;
            }
            var itemWeight = parseFloat(this._getElementText(item.select('.weight')[0]));
            containerWeight.value = parseFloat(containerWeight.value) + (itemWeight * qtyValue);
            var itemCustomsValue = parseFloat(item.select('[name="customs_value"]')[0].value) || 0;
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
