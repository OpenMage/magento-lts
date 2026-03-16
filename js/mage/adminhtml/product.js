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
 * @copyright   Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var Product = {};

Product.Gallery = function (containerId, imageTypes) {
    this.images = [];
    this.file2id = { 'no_selection': 0 };
    this.idIncrement = 1;
    this.containerId = '';
    this.container = null;
    this.imageTypes = {};
    this.initialize(containerId, imageTypes);
};

Product.Gallery.prototype = {
    initialize: function (containerId, imageTypes) {
        this.containerId = containerId;
        this.container = document.getElementById(this.containerId);
        this.imageTypes = imageTypes;

        document.addEventListener('uploader:fileSuccess', (function (event) {
            var memo = event.detail;
            if (memo && this._checkCurrentContainer(memo.containerId)) {
                this.handleUploadComplete([{response: memo.response}]);
            }
        }).bind(this));

        this.images = JSON.parse(this.getElement('save').value);
        this.imagesValues = JSON.parse(this.getElement('save_image').value);

        var templateRegex = new RegExp('(^|.|\\r|\\n)(__([a-zA-Z0-9_]+)__)', '');
        var templateHtml = '<tr id="__id__" class="preview">' + this.getElement('template').innerHTML + '</tr>';
        this._templateHtml = templateHtml;
        this._templateRegex = templateRegex;

        this.template = {
            evaluate: function (vars) {
                return templateHtml.replace(/__([a-zA-Z0-9_]+)__/g, function (match, key) {
                    return (vars[key] !== undefined) ? vars[key] : match;
                });
            }
        };

        this.fixParentTable();
        this.updateImages();
        varienGlobalEvents.attachEventHandler('moveTab', this.onImageTabMove.bind(this));
    },
    _checkCurrentContainer: function (child) {
        return document.getElementById(this.containerId).querySelector('#' + child);
    },
    onImageTabMove: function (event) {
        var imagesTab = false;
        var el = this.container;
        while (el && el.parentElement) {
            el = el.parentElement;
            if (el.tabObject) {
                imagesTab = el.tabObject;
                break;
            }
        }

        if (imagesTab && event.tab && event.tab.name && imagesTab.name == event.tab.name) {
            this.container.querySelectorAll('input[type="radio"]').forEach((function (radio) {
                radio.addEventListener('change', this.onChangeRadio);
            }).bind(this));
            this.updateImages();
        }
    },
    fixParentTable: function () {
        var el = this.container;
        while (el && el.parentElement) {
            el = el.parentElement;
            if (el.tagName.toLowerCase() == 'td') {
                el.style.width = '100%';
            }
            if (el.tagName.toLowerCase() == 'table') {
                el.style.width = '100%';
                break;
            }
        }
    },
    getElement: function (name) {
        return document.getElementById(this.containerId + '_' + name);
    },
    showUploader: function () {
        this.getElement('add_images_button').style.display = 'none';
        this.getElement('uploader').style.display = '';
    },
    handleUploadComplete: function (files) {
        files.forEach((function (item) {
            var isJSON;
            try {
                JSON.parse(item.response);
                isJSON = true;
            } catch (e) {
                isJSON = false;
            }
            if (!isJSON) {
                try {
                    console.log(item.response);
                } catch (e2) {
                    alert(item.response);
                }
                return;
            }
            var response = JSON.parse(item.response);
            if (response.error) {
                return;
            }
            var newImage = {};
            newImage.url = response.url;
            newImage.file = response.file;
            newImage.label = '';
            newImage.position = this.getNextPosition();
            newImage.disabled = 0;
            newImage.removed = 0;
            this.images.push(newImage);
        }).bind(this));
        this.container.setHasChanges();
        this.updateImages();
    },
    updateImages: function () {
        this.getElement('save').value = JSON.stringify(this.images);
        var imageTypes = this.imageTypes;
        Object.keys(imageTypes).forEach((function (key) {
            this.getFileElement('no_selection', 'cell-' + key + ' input').checked = true;
        }).bind(this));
        this.images.forEach((function (row) {
            if (!document.getElementById(this.prepareId(row.file))) {
                this.createImageRow(row);
            }
            this.updateVisualisation(row.file);
        }).bind(this));
        this.updateUseDefault(false);
    },
    onChangeRadio: function (evt) {
        var element = evt.target;
        element.setHasChanges();
    },
    createImageRow: function (image) {
        var vars = Object.assign({}, image);
        vars.id = this.prepareId(image.file);
        var html = this.template.evaluate(vars);
        this.getElement('list').insertAdjacentHTML('beforeend', html);

        document.getElementById(vars.id).querySelectorAll('input[type="radio"]').forEach((function (radio) {
            radio.addEventListener('change', this.onChangeRadio);
        }).bind(this));
    },
    prepareId: function (file) {
        if (typeof this.file2id[file] == 'undefined') {
            this.file2id[file] = this.idIncrement++;
        }
        return this.containerId + '-image-' + this.file2id[file];
    },
    getNextPosition: function () {
        var maxPosition = 0;
        this.images.forEach(function (item) {
            if (parseInt(item.position) > maxPosition) {
                maxPosition = parseInt(item.position);
            }
        });
        return maxPosition + 1;
    },
    updateImage: function (file) {
        var index = this.getIndexByFile(file);

        var use_default_label = document.getElementById("use_default_label");
        if (use_default_label && use_default_label.checked) {
            this.images[index].label = null;
            this.images[index].label_use_default = true;
        } else {
            this.images[index].label = this.getFileElement(file, 'cell-label input').value;
            this.images[index].label_use_default = false;
        }

        var use_default_position = document.getElementById("use_default_position");
        if (use_default_position && use_default_position.checked) {
            this.images[index].position = null;
            this.images[index].position_use_default = true;
        } else {
            this.images[index].position = this.getFileElement(file, 'cell-position input').value;
            this.images[index].position_use_default = false;
        }

        this.images[index].removed = (this.getFileElement(file, 'cell-remove input').checked ? 1 : 0);
        this.images[index].disabled = (this.getFileElement(file, 'cell-disable input').checked ? 1 : 0);
        this.getElement('save').value = JSON.stringify(this.images);
        this.updateState(file);
        this.container.setHasChanges();
    },
    loadImage: function (file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-image img').src = image.url;
        this.getFileElement(file, 'cell-image img').style.display = '';
        this.getFileElement(file, 'cell-image .place-holder').style.display = 'none';
    },
    setProductImages: function (file) {
        var imageTypes = this.imageTypes;
        Object.keys(imageTypes).forEach((function (key) {
            if (this.getFileElement(file, 'cell-' + key + ' input').checked) {
                this.imagesValues[key] = (file == 'no_selection' ? null : file);
            }
        }).bind(this));

        this.getElement('save_image').value = JSON.stringify(this.imagesValues);
    },
    updateVisualisation: function (file) {
        var image = this.getImageByFile(file);

        var use_default_label = document.getElementById("use_default_label");
        if (use_default_label && use_default_label.checked) {
            this.getFileElement(file, 'cell-label input').value = image.label_default;
        } else {
            this.getFileElement(file, 'cell-label input').value = image.label;
        }

        var use_default_position = document.getElementById("use_default_position");
        if (use_default_position && use_default_position.checked) {
            this.getFileElement(file, 'cell-position input').value = image.position_default;
        } else {
            this.getFileElement(file, 'cell-position input').value = image.position;
        }

        this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
        this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
        var imageTypes = this.imageTypes;
        Object.keys(imageTypes).forEach((function (key) {
            if (this.imagesValues[key] == file) {
                this.getFileElement(file, 'cell-' + key + ' input').checked = true;
            }
        }).bind(this));
        this.updateState(file);
    },
    updateState: function (file) {
        // deprecated
    },
    getFileElement: function (file, element) {
        var selector = '#' + this.prepareId(file) + ' .' + element;
        var elems = document.querySelectorAll(selector);
        if (!elems[0]) {
            try {
                console.log(selector);
            } catch (e2) {
                alert(selector);
            }
        }

        return elems[0];
    },
    getImageByFile: function (file) {
        if (this.getIndexByFile(file) === null) {
            return false;
        }

        return this.images[this.getIndexByFile(file)];
    },
    getIndexByFile: function (file) {
        var index;
        this.images.forEach(function (item, i) {
            if (item.file == file) {
                index = i;
            }
        });
        return index;
    },
    updateUseDefault: function (el) {
        var inputs = document.querySelectorAll('#' + this.containerId + '_default td input');
        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            var radios = document.querySelectorAll('#' + this.containerId + '_list .preview .cell-' + input.value + ' input');
            for (var j = 0; j < radios.length; j++) {
                var radio = radios[j];
                radio.disabled = input.checked;
            }
        }

        if (typeof el == "object" && el.id) {
            this.images.forEach((function (row) {
                this.updateImage(row.file);
            }).bind(this));
        }

        if (arguments.length == 0) {
            this.container.setHasChanges();
        }
    },
    handleUploadProgress: function (file) {
    },
    handleUploadError: function (fileId) {
    }
};

Product.AttributesBridge = {
    tabsObject: false,
    bindTabs2Attributes: {},
    bind: function (tabId, attributesObject) {
        this.bindTabs2Attributes[tabId] = attributesObject;
    },
    getAttributes: function (tabId) {
        return this.bindTabs2Attributes[tabId];
    },
    setTabsObject: function (tabs) {
        this.tabsObject = tabs;
    },
    getTabsObject: function () {
        return this.tabsObject;
    },
    addAttributeRow: function (data) {
        var self = this;
        Object.keys(data).forEach(function (key) {
            var value = data[key];
            if (self.getTabsObject().activeTab.name != key) {
                self.getTabsObject().showTabContent(document.getElementById(key));
            }
            self.getAttributes(key).addRow(value);
        });
    }
};

Product.Attributes = function (containerId) {
    this.config = {};
    this.containerId = null;
    this.initialize(containerId);
};

Product.Attributes.prototype = {
    initialize: function (containerId) {
        this.containerId = containerId;
    },
    setConfig: function (config) {
        this.config = config;
        Product.AttributesBridge.bind(this.getConfig().tab_id, this);
    },
    getConfig: function () {
        return this.config;
    },
    create: function () {
        var win = window.open(this.getConfig().url, 'new_attribute',
            'width=900,height=600,resizable=1,scrollbars=1');
        win.focus();
    },
    addRow: function (html) {
        var attributesContainer = document.querySelector('#group_fields' + this.getConfig().group_id + ' .form-list tbody');
        attributesContainer.insertAdjacentHTML('beforeend', html);

        var childs = Array.from(attributesContainer.children);
        var element = childs[childs.length - 1].querySelector('input, select, textarea');
        if (element) {
            var rect = element.getBoundingClientRect();
            window.scrollTo(0, rect.top + window.pageYOffset + element.offsetHeight);
        }
    }
};

Product.Configurable = function (attributes, links, idPrefix, grid, readonly) {
    this.templatesSyntax = new RegExp('(^|.|\\r|\\n)(\'{{\\s*(\\w+)\\s*}}\')', "");
    this.attributes = attributes;
    this.idPrefix = idPrefix;
    this.links = {};
    Object.keys(links).forEach(function (k) { this.links[k] = links[k]; }.bind(this));
    this.newProducts = [];
    this.readonly = readonly;
    this.valueAutoIndex = 0;

    /* Helper to evaluate templates: replaces '{{key}}' with value from object */
    function makeTemplate(html, syntax) {
        return {
            evaluate: function (vars) {
                return html.replace(/'?\{\{\s*(\w+)\s*\}\}'?/g, function (match, key) {
                    return (vars[key] !== undefined) ? vars[key] : match;
                });
            }
        };
    }

    var attrTemplateEl = document.getElementById(idPrefix + 'attribute_template');
    this.addAttributeTemplate = makeTemplate(
        attrTemplateEl.innerHTML.replace(/__id__/g, "'{{html_id}}'").replace(/ template no-display/g, ''),
        this.templatesSyntax);

    var valTemplateEl = document.getElementById(idPrefix + 'value_template');
    this.addValueTemplate = makeTemplate(
        valTemplateEl.innerHTML.replace(/__id__/g, "'{{html_id}}'").replace(/ template no-display/g, ''),
        this.templatesSyntax);

    var simplePricingEl = document.getElementById(idPrefix + 'simple_pricing');
    this.pricingValueTemplate = makeTemplate(simplePricingEl.innerHTML, this.templatesSyntax);

    var simplePricingViewEl = document.getElementById(idPrefix + 'simple_pricing_view');
    this.pricingValueViewTemplate = makeTemplate(simplePricingViewEl.innerHTML, this.templatesSyntax);

    this.container = document.getElementById(idPrefix + 'attributes');

    /* Listeners */
    this.onLabelUpdate = this.updateLabel.bind(this);
    this.onValuePriceUpdate = this.updateValuePrice.bind(this);
    this.onValueTypeUpdate = this.updateValueType.bind(this);
    this.onValueDefaultUpdate = this.updateValueUseDefault.bind(this);

    /* Grid initialization and attributes initialization */
    this.createAttributes();

    this.grid = grid;
    this.grid.rowClickCallback = this.rowClick.bind(this);
    this.grid.initRowCallback = this.rowInit.bind(this);
    this.grid.checkboxCheckCallback = this.registerProduct.bind(this);

    Array.from(this.grid.rows).forEach((function (row) {
        this.rowInit(this.grid, row);
    }).bind(this));
};

Product.Configurable.prototype = {
    /* Helper: get keys of links object */
    _linksKeys: function () {
        return Object.keys(this.links);
    },
    /* Helper: iterate links */
    _linksEach: function (fn) {
        var self = this;
        Object.keys(this.links).forEach(function (key) {
            fn({ key: key, value: self.links[key] });
        });
    },
    createAttributes: function () {
        this.attributes.forEach((function (attribute, index) {
            var li = document.createElement('LI');
            li.className = 'attribute';
            li.id = this.idPrefix + '_attribute_' + index;
            attribute.html_id = li.id;
            if (attribute && attribute.label && attribute.label.trim() === '') {
                attribute.label = '&nbsp;';
            }
            var label_readonly = '';
            var use_default_checked = '';
            if (attribute.use_default == '1' || attribute.id == null) {
                use_default_checked = ' checked="checked"';
                label_readonly = ' readonly="readonly"';
            }

            var template = this.addAttributeTemplate.evaluate(attribute);
            template = template.replace(new RegExp(' readonly="label"', 'ig'), label_readonly);
            template = template.replace(new RegExp(' checked="use_default"', 'ig'), use_default_checked);
            li.innerHTML = template;
            li.attributeObject = attribute;

            this.container.appendChild(li);
            li.attributeValues = li.querySelector('.attribute-values');

            if (attribute.values) {
                attribute.values.forEach((function (value) {
                    this.createValueRow(li, value);
                }).bind(this));
            }

            /* Observe label change */
            var labelEl = li.querySelector('.attribute-label');
            labelEl.addEventListener('change', this.onLabelUpdate);
            labelEl.addEventListener('keyup', this.onLabelUpdate);
            li.querySelector('.attribute-use-default-label').addEventListener('change', this.onLabelUpdate);
        }).bind(this));

        if (!this.readonly) {
            // Creation of sortable for attributes sorting
            Sortable.create(this.container, {
                handle: 'attribute-name-container',
                onUpdate: this.updatePositions.bind(this)
            });
        }
        this.updateSaveInput();
    },

    updateLabel: function (event) {
        var li = event.target.closest('LI');
        var labelEl = li.querySelector('.attribute-label');
        var defEl = li.querySelector('.attribute-use-default-label');

        li.attributeObject.label = labelEl.value;
        if (defEl.checked) {
            labelEl.readOnly = true;
            li.attributeObject.use_default = 1;
        } else {
            labelEl.readOnly = false;
            li.attributeObject.use_default = 0;
        }

        this.updateSaveInput();
    },
    updatePositions: function (param) {
        Array.from(this.container.children).forEach(function (row, index) {
            row.attributeObject.position = index;
        });
        this.updateSaveInput();
    },
    addNewProduct: function (productId, attributes) {
        if (this.checkAttributes(attributes)) {
            this.links[productId] = this.cloneAttributes(attributes);
        } else {
            this.newProducts.push(productId);
        }

        this.updateGrid();
        this.updateValues();
        this.grid.reload(null);
    },
    createEmptyProduct: function () {
        this.createPopup(this.createEmptyUrl);
    },
    createNewProduct: function () {
        this.createPopup(this.createNormalUrl);
    },
    createPopup: function (url) {
        if (this.win && !this.win.closed) {
            this.win.close();
        }

        this.win = window.open(url, '',
            'width=1000,height=700,resizable=1,scrollbars=1');
        this.win.focus();
    },
    registerProduct: function (grid, element, checked) {
        if (checked) {
            if (element.linkAttributes) {
                this.links[element.value] = element.linkAttributes;
            }
        } else {
            delete this.links[element.value];
        }
        this.updateGrid();
        Array.from(this.grid.rows).forEach((function (row) {
            this.revalidateRow(this.grid, row);
        }).bind(this));
        this.updateValues();
    },
    updateProduct: function (productId, attributes) {
        var isAssociated = false;

        if (typeof this.links[productId] != 'undefined') {
            isAssociated = true;
            delete this.links[productId];
        }

        if (isAssociated && this.checkAttributes(attributes)) {
            this.links[productId] = this.cloneAttributes(attributes);
        } else if (isAssociated) {
            this.newProducts.push(productId);
        }

        this.updateGrid();
        this.updateValues();
        this.grid.reload(null);
    },
    cloneAttributes: function (attributes) {
        var newObj = [];
        for (var i = 0, length = attributes.length; i < length; i++) {
            newObj[i] = Object.assign({}, attributes[i]);
        }
        return newObj;
    },
    rowClick: function (grid, event) {
        var trElement = event.target.closest('tr');
        var isInput = event.target.tagName.toUpperCase() == 'INPUT';

        var td = event.target.closest('td');
        if (td && td.querySelector('a')) {
            return;
        }

        if (trElement) {
            var checkbox = trElement.querySelector('input');
            if (checkbox && !checkbox.disabled) {
                var checked = isInput ? checkbox.checked : !checkbox.checked;
                grid.setCheckboxChecked(checkbox, checked);
            }
        }
    },
    rowInit: function (grid, row) {
        var checkbox = row.querySelector('.checkbox');
        var input = row.querySelector('.value-json');
        if (checkbox && input) {
            checkbox.linkAttributes = JSON.parse(input.value);
            if (!checkbox.checked) {
                if (!this.checkAttributes(checkbox.linkAttributes)) {
                    row.classList.add('invalid');
                    checkbox.disabled = true;
                } else {
                    row.classList.remove('invalid');
                    checkbox.disabled = false;
                }
            }
        }
    },
    revalidateRow: function (grid, row) {
        var checkbox = row.querySelector('.checkbox');
        if (checkbox) {
            if (!checkbox.checked) {
                if (!this.checkAttributes(checkbox.linkAttributes)) {
                    row.classList.add('invalid');
                    checkbox.disabled = true;
                } else {
                    row.classList.remove('invalid');
                    checkbox.disabled = false;
                }
            }
        }
    },
    checkAttributes: function (attributes) {
        var result = true;
        var self = this;
        Object.keys(this.links).forEach(function (key) {
            var pair = { key: key, value: self.links[key] };
            var fail = false;
            for (var i = 0; i < pair.value.length && !fail; i++) {
                for (var j = 0; j < attributes.length && !fail; j++) {
                    if (pair.value[i].attribute_id == attributes[j].attribute_id
                        && pair.value[i].value_index != attributes[j].value_index) {
                        fail = true;
                    }
                }
            }
            if (!fail) {
                result = false;
            }
        });
        return result;
    },
    updateGrid: function () {
        var keys = this._linksKeys();
        this.grid.reloadParams = {
            'products[]': keys.length ? keys : [0],
            'new_products[]': this.newProducts
        };
    },
    updateValues: function () {
        var uniqueAttributeValues = {};
        var self = this;
        /* Collect unique attributes */
        this._linksEach(function (pair) {
            for (var i = 0, length = pair.value.length; i < length; i++) {
                var attribute = pair.value[i];
                if (Object.keys(uniqueAttributeValues).indexOf(attribute.attribute_id) == -1) {
                    uniqueAttributeValues[attribute.attribute_id] = {};
                }
                uniqueAttributeValues[attribute.attribute_id][attribute.value_index] = attribute;
            }
        });
        /* Updating attributes value container */
        Array.from(this.container.children).forEach((function (row) {
            var attribute = row.attributeObject;
            for (var i = 0, length = attribute.values.length; i < length; i++) {
                if (Object.keys(uniqueAttributeValues).indexOf(attribute.attribute_id) == -1
                    || Object.keys(uniqueAttributeValues[attribute.attribute_id] || {}).indexOf(attribute.values[i].value_index) == -1) {
                    Array.from(row.attributeValues.children).forEach(function (elem) {
                        if (elem.valueObject.value_index == attribute.values[i].value_index) {
                            elem.remove();
                        }
                    });
                    attribute.values[i] = undefined;
                } else {
                    delete uniqueAttributeValues[attribute.attribute_id][attribute.values[i].value_index];
                }
            }
            attribute.values = attribute.values.filter(function (v) { return v !== undefined && v !== null; });
            if (uniqueAttributeValues[attribute.attribute_id]) {
                var attrVals = uniqueAttributeValues[attribute.attribute_id];
                Object.keys(attrVals).forEach((function (valKey) {
                    attribute.values.push(attrVals[valKey]);
                    this.createValueRow(row, attrVals[valKey]);
                }).bind(this));
            }
        }).bind(this));
        this.updateSaveInput();
        this.updateSimpleForm();
    },
    createValueRow: function (container, value) {
        var templateVariables = {};
        if (!this.valueAutoIndex) {
            this.valueAutoIndex = 1;
        }
        templateVariables['html_id'] = container.id + '_' + this.valueAutoIndex;
        Object.assign(templateVariables, value);
        var pricingValue = parseFloat(templateVariables['pricing_value']);
        if (!isNaN(pricingValue)) {
            templateVariables['pricing_value'] = pricingValue;
        } else {
            delete templateVariables['pricing_value'];
        }
        this.valueAutoIndex++;

        var li = document.createElement('LI');
        li.className = 'attribute-value';
        li.id = templateVariables['html_id'];
        li.innerHTML = this.addValueTemplate.evaluate(templateVariables);
        li.valueObject = value;
        if (typeof li.valueObject.is_percent == 'undefined') {
            li.valueObject.is_percent = 0;
        }

        if (typeof li.valueObject.pricing_value == 'undefined') {
            li.valueObject.pricing_value = '';
        }

        container.attributeValues.appendChild(li);

        var priceField = li.querySelector('.attribute-price');
        var priceTypeField = li.querySelector('.attribute-price-type');

        if (priceTypeField != undefined && priceTypeField.options != undefined) {
            if (parseInt(value.is_percent)) {
                priceTypeField.options[1].selected = !(priceTypeField.options[0].selected = false);
            } else {
                priceTypeField.options[1].selected = !(priceTypeField.options[0].selected = true);
            }
        }

        priceField.addEventListener('keyup', this.onValuePriceUpdate);
        priceField.addEventListener('change', this.onValuePriceUpdate);
        priceTypeField.addEventListener('change', this.onValueTypeUpdate);
        var useDefaultEl = li.querySelector('.attribute-use-default-value');
        if (useDefaultEl) {
            if (li.valueObject.use_default_value) {
                useDefaultEl.checked = true;
                this.updateUseDefaultRow(useDefaultEl, li);
            }
            useDefaultEl.addEventListener('change', this.onValueDefaultUpdate);
        }
    },
    updateValuePrice: function (event) {
        var li = event.target.closest('LI');
        li.valueObject.pricing_value = (event.target.value.trim() === '' ? null : event.target.value);
        this.updateSimpleForm();
        this.updateSaveInput();
    },
    updateValueType: function (event) {
        var li = event.target.closest('LI');
        li.valueObject.is_percent = (event.target.value.trim() === '' ? null : event.target.value);
        this.updateSimpleForm();
        this.updateSaveInput();
    },
    updateValueUseDefault: function (event) {
        var li = event.target.closest('LI');
        var useDefaultEl = event.target;
        li.valueObject.use_default_value = useDefaultEl.checked;
        this.updateUseDefaultRow(useDefaultEl, li);
    },
    updateUseDefaultRow: function (useDefaultEl, li) {
        var priceField = li.querySelector('.attribute-price');
        var priceTypeField = li.querySelector('.attribute-price-type');
        if (useDefaultEl.checked) {
            priceField.disabled = true;
            priceTypeField.disabled = true;
        } else {
            priceField.disabled = false;
            priceTypeField.disabled = false;
        }
        this.updateSimpleForm();
        this.updateSaveInput();
    },
    updateSaveInput: function () {
        var saveAttrsEl = document.getElementById(this.idPrefix + 'save_attributes');
        var saveLinksEl = document.getElementById(this.idPrefix + 'save_links');
        var oldSaveAttributesValue = saveAttrsEl.value;
        var oldSaveLinksValue = saveLinksEl.value;
        var newSaveAttributesValue = JSON.stringify(this.attributes);
        var newSaveLinksValue = JSON.stringify(this.links);
        saveAttrsEl.value = newSaveAttributesValue;
        saveLinksEl.value = newSaveLinksValue;
        if (oldSaveAttributesValue != newSaveAttributesValue || oldSaveLinksValue != newSaveLinksValue) {
            try {
                document.getElementById('configurable_save_attributes').setHasChanges();
            } catch (e) {}
        }
    },
    initializeAdvicesForSimpleForm: function () {
        var simpleForm = document.getElementById(this.idPrefix + 'simple_form');
        if (simpleForm.advicesInited) {
            return;
        }

        simpleForm.querySelectorAll('td.value').forEach(function (td) {
            var adviceContainer = document.createElement('div');
            td.appendChild(adviceContainer);
            td.querySelectorAll('input, select').forEach(function (element) {
                element.advaiceContainer = adviceContainer;
            });
        });
        simpleForm.advicesInited = true;
    },
    quickCreateNewProduct: function () {
        this.initializeAdvicesForSimpleForm();
        var simpleForm = document.getElementById(this.idPrefix + 'simple_form');
        simpleForm.classList.remove('ignore-validate');
        var validationResult = Array.from(simpleForm.querySelectorAll('input, select, textarea')).map(function (elm) {
            return Validation.validate(elm, {
                useTitle: false,
                onElementValidate: function () {
                }
            });
        }).every(function (v) { return v; });
        simpleForm.classList.add('ignore-validate');

        if (!validationResult) {
            return;
        }

        var formElements = simpleForm.querySelectorAll('input, select, textarea');
        var params = new URLSearchParams();
        formElements.forEach(function (el) {
            if (el.name && !el.disabled) {
                if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
                    return;
                }
                params.append(el.name, el.value);
            }
        });
        params.append('form_key', FORM_KEY);

        document.getElementById('messages').innerHTML = '';

        fetch(this.createQuickUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        }).then((function (response) {
            return response.text();
        }).bind(this)).then((function (responseText) {
            this.quickCreateNewProductComplete(responseText);
        }).bind(this));
    },
    quickCreateNewProductComplete: function (responseText) {
        var result = JSON.parse(responseText);

        if (result.error) {
            if (result.error.fields) {
                var simpleForm = document.getElementById(this.idPrefix + 'simple_form');
                simpleForm.classList.remove('ignore-validate');
                Object.keys(result.error.fields).forEach(function (key) {
                    var value = result.error.fields[key];
                    document.getElementById('simple_product_' + key).value = value;
                    document.getElementById('simple_product_' + key + '_autogenerate').checked = false;
                    var autoGenEl = document.getElementById('simple_product_' + key + '_autogenerate');
                    toggleValueElements(autoGenEl, autoGenEl.parentNode);
                    Validation.ajaxError(document.getElementById('simple_product_' + key), result.error.message);
                });
                simpleForm.classList.add('ignore-validate');
            } else {
                if (result.error.message) {
                    alert(result.error.message);
                } else {
                    alert(result.error);
                }
            }
            return;
        } else if (result.messages) {
            document.getElementById('messages').innerHTML = result.messages;
        }

        result.attributes.forEach((function (attribute) {
            var attr = this.getAttributeById(attribute.attribute_id);
            if (!this.getValueByIndex(attr, attribute.value_index)
                && result.pricing
                && result.pricing[attr.attribute_code]) {

                attribute.is_percent = result.pricing[attr.attribute_code].is_percent;
                attribute.pricing_value = (result.pricing[attr.attribute_code].value == null ? ''
                    : result.pricing[attr.attribute_code].value);
            }
        }).bind(this));

        this.attributes.forEach((function (attribute) {
            var el = document.getElementById('simple_product_' + attribute.attribute_code);
            if (el) {
                el.value = '';
            }
        }).bind(this));

        this.links[result.product_id] = result.attributes;
        this.updateGrid();
        this.updateValues();
        this.grid.reload();
    },
    checkCreationUniqueAttributes: function () {
        var attributes = [];
        this.attributes.forEach((function (attribute) {
            attributes.push({
                attribute_id: attribute.attribute_id,
                value_index: document.getElementById('simple_product_' + attribute.attribute_code).value
            });
        }).bind(this));

        return this.checkAttributes(attributes);
    },
    getAttributeByCode: function (attributeCode) {
        var attribute = null;
        for (var i = 0; i < this.attributes.length; i++) {
            if (this.attributes[i].attribute_code == attributeCode) {
                attribute = this.attributes[i];
                break;
            }
        }
        return attribute;
    },
    getAttributeById: function (attributeId) {
        var attribute = null;
        for (var i = 0; i < this.attributes.length; i++) {
            if (this.attributes[i].attribute_id == attributeId) {
                attribute = this.attributes[i];
                break;
            }
        }
        return attribute;
    },
    getValueByIndex: function (attribute, valueIndex) {
        var result = null;
        for (var i = 0; i < attribute.values.length; i++) {
            if (attribute.values[i].value_index == valueIndex) {
                result = attribute.values[i];
                break;
            }
        }
        return result;
    },
    showPricing: function (select, attributeCode) {
        var attribute = this.getAttributeByCode(attributeCode);
        if (!attribute) {
            return;
        }

        if (typeof select === 'string') {
            select = document.getElementById(select);
        }
        if (select.value && !document.getElementById('simple_product_' + attributeCode + '_pricing_container')) {
            select.insertAdjacentHTML('afterend',
                '<div class="left"></div> <div id="simple_product_' + attributeCode + '_pricing_container" class="left"></div>');
            var newContainer = select.nextElementSibling;
            select.parentNode.removeChild(select);
            newContainer.appendChild(select);
            // Fix visualization bug
            document.getElementById(this.idPrefix + 'simple_form').querySelector('.form-list').style.width = '100%';
        }

        var container = document.getElementById('simple_product_' + attributeCode + '_pricing_container');

        if (select.value) {
            var value = this.getValueByIndex(attribute, select.value);
            if (!value) {
                if (!container.querySelector('.attribute-price')) {
                    if (value == null) {
                        value = {};
                    }
                    container.innerHTML = this.pricingValueTemplate.evaluate(value);
                    var priceValueField = container.querySelector('.attribute-price');
                    var priceTypeField = container.querySelector('.attribute-price-type');

                    priceValueField.attributeCode = attributeCode;
                    priceValueField.priceField = priceValueField;
                    priceValueField.typeField = priceTypeField;

                    priceTypeField.attributeCode = attributeCode;
                    priceTypeField.priceField = priceValueField;
                    priceTypeField.typeField = priceTypeField;

                    priceValueField.addEventListener('change', this.updateSimplePricing.bind(this));
                    priceValueField.addEventListener('keyup', this.updateSimplePricing.bind(this));
                    priceTypeField.addEventListener('change', this.updateSimplePricing.bind(this));

                    document.getElementById('simple_product_' + attributeCode + '_pricing_value').value = null;
                    document.getElementById('simple_product_' + attributeCode + '_pricing_type').value = null;
                }
            } else if (!isNaN(parseFloat(value.pricing_value))) {
                container.innerHTML = this.pricingValueViewTemplate.evaluate({
                    'value': (parseFloat(value.pricing_value) > 0 ? '+' : '')
                        + parseFloat(value.pricing_value)
                        + (parseInt(value.is_percent) > 0 ? '%' : '')
                });
                document.getElementById('simple_product_' + attributeCode + '_pricing_value').value = value.pricing_value;
                document.getElementById('simple_product_' + attributeCode + '_pricing_type').value = value.is_percent;
            } else {
                container.innerHTML = '';
                document.getElementById('simple_product_' + attributeCode + '_pricing_value').value = null;
                document.getElementById('simple_product_' + attributeCode + '_pricing_type').value = null;
            }
        } else if (container) {
            container.innerHTML = '';
            document.getElementById('simple_product_' + attributeCode + '_pricing_value').value = null;
            document.getElementById('simple_product_' + attributeCode + '_pricing_type').value = null;
        }
    },
    updateSimplePricing: function (evt) {
        var element = evt.target;
        if (element.priceField.value.trim() !== '') {
            document.getElementById('simple_product_' + element.attributeCode + '_pricing_value').value = element.priceField.value;
            document.getElementById('simple_product_' + element.attributeCode + '_pricing_type').value = element.typeField.value;
        } else {
            document.getElementById('simple_product_' + element.attributeCode + '_pricing_value').value = null;
            document.getElementById('simple_product_' + element.attributeCode + '_pricing_type').value = null;
        }
    },
    updateSimpleForm: function () {
        this.attributes.forEach((function (attribute) {
            var el = document.getElementById('simple_product_' + attribute.attribute_code);
            if (el) {
                this.showPricing(el, attribute.attribute_code);
            }
        }).bind(this));
    },
    showNoticeMessage: function () {
        document.getElementById('assign_product_warrning').style.display = '';
    }
};

var onInitDisableFieldsList = [];

function toogleFieldEditMode(toogleIdentifier, fieldContainer) {
    if (document.getElementById(toogleIdentifier).checked) {
        enableFieldEditMode(fieldContainer);
    } else {
        disableFieldEditMode(fieldContainer);
    }
}

function disableFieldEditMode(fieldContainer) {
    var el = document.getElementById(fieldContainer);
    if (el) {
        el.disabled = true;
    }
    var hidden = document.getElementById(fieldContainer + '_hidden');
    if (hidden) {
        hidden.disabled = true;
    }
}

function enableFieldEditMode(fieldContainer) {
    var el = document.getElementById(fieldContainer);
    if (el) {
        el.disabled = false;
    }
    var hidden = document.getElementById(fieldContainer + '_hidden');
    if (hidden) {
        hidden.disabled = false;
    }
}

function initDisableFields(fieldContainer) {
    onInitDisableFieldsList.push(fieldContainer);
}

function onCompleteDisableInited() {
    onInitDisableFieldsList.forEach(function (item) {
        disableFieldEditMode(item);
    });
}

function onUrlkeyChanged(urlKey) {
    urlKey = document.getElementById(urlKey);
    var hidden = urlKey.parentElement.querySelector('input[type=hidden]');
    if (!hidden) {
        var sibling = urlKey.nextElementSibling;
        while (sibling) {
            if (sibling.matches && sibling.matches('input[type=hidden]')) { hidden = sibling; break; }
            sibling = sibling.nextElementSibling;
        }
    }
    var chbx = null;
    var sibling2 = urlKey.nextElementSibling;
    while (sibling2) {
        if (sibling2.matches && sibling2.matches('input[type=checkbox]')) { chbx = sibling2; break; }
        sibling2 = sibling2.nextElementSibling;
    }
    var oldValue = chbx.value;
    chbx.disabled = (oldValue == urlKey.value);
    hidden.disabled = chbx.disabled;
}

function onCustomUseParentChanged(element) {
    var useParent = (element.value == 1) ? true : false;
    /* Navigate up 2 levels */
    var ancestor = element.parentElement;
    if (ancestor && ancestor.parentElement) {
        ancestor = ancestor.parentElement;
    }
    ancestor.querySelectorAll('input, select, textarea').forEach(function (el) {
        if (element.id != el.id) {
            el.disabled = useParent;
        }
    });
    ancestor.querySelectorAll('img').forEach(function (el) {
        if (useParent) {
            el.style.display = 'none';
        } else {
            el.style.display = '';
        }
    });
}

window.addEventListener('load', onCompleteDisableInited);
