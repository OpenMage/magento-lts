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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Gift Options PopUp Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
GiftOptions = Class.create();
GiftOptions.prototype = {
    currentItem: $H({}),
    _giftOptionsWindow: null,
    _giftOptionsWindowMask: null,
    _giftoptionsWindowFieldPrefix: null,
    _giftOptionsWindowHeight: null,
    _giftOptionsGroups: [],
    _fieldSuffixes: [],
    _cancelCallback: null,
    _okCallback: null,
    _externalFieldsLoaderFunction: null,
    _contentLoaderFunction: null,
    _giftOptionsWindowTitleLoaderFunction: null,
    _giftOptionsWindowTitleElement: null,
    _giftOptionsForm: null,

    /**
     * Initialize gift options object
     */
    initialize: function () {},

    /**
     * Set gift options window
     *
     * @param string windowId
     * @param string maskId
     *
     * @return boolean success
     */
    setGiftOptionsWindow: function (windowId, maskId)
    {
        this._giftOptionsWindow = $(windowId);
        if (maskId) {
            this._giftOptionsWindowMask = $(maskId);
        }
        if (!this._giftOptionsWindow) {
            return false;
        }
        return true;
    },

    /**
     * Set gift options form
     *
     * @param varienForm form
     */
    setGiftOptionsForm: function (form)
    {
        this._giftOptionsForm = form;
    },

    /**
     * Set gift options window field prefix
     *
     * @param string fieldPrefix
     * 
     * @return boolean
     */
    setGiftOptionsWindowFieldPrefix: function (fieldPrefix)
    {
        this._giftoptionsWindowFieldPrefix = fieldPrefix;
        return true;
    },

    /**
     * Add new gift options group to display
     *
     * @param string groupFieldPrefix
     * @param array groupFieldSuffixes
     *
     * @return boolean success
     */
    addGiftOptionsGroup: function (groupFieldPrefix, groupFieldSuffixes)
    {
        if (!groupFieldPrefix || !groupFieldSuffixes) {
            return false;
        }
        // groupFieldPrefix is used as group identifier too
        this._giftOptionsGroups.push(groupFieldPrefix);
        this._fieldSuffixes[groupFieldPrefix] = groupFieldSuffixes;
        return true;
    },

    /**
     * Set cancel button callback
     *
     * @param function callback
     * @param boolean bind
     */
    setCancelCallback: function (callback)
    {
        this._cancelCallback = callback;
        if (bind) {
            this._cancelCallback.bind(this);
        }
    },

    /**
     * Set ok button callback
     *
     * @param function callback
     * @param boolean bind
     */
    setOkCallback: function (callback, bind)
    {
        this._okCallback = callback;
        if (bind) {
            this._okCallback.bind(this);
        }
    },

    /**
     * Set function that will load external gift options fields
     * This function should accept at least one parameter that will serve as a callback
     *
     * @param function loaderFunction
     */
    setExternalFieldsloaderFunction: function (loaderFunction)
    {
        this._externalFieldsLoaderFunction = loaderFunction;
    },

    /**
     * Show item gift options
     *
     * @param int itemId
     */
    showItemGiftOptions: function (itemId)
    {
        if (!itemId) {
            return false;
        }
        this.currentItem.itemId = itemId;
        if (Object.isFunction(this._contentLoaderFunction)) {
            if (!this._contentLoaderFunction(this.currentItem.itemId)) {
                if (Object.isFunction(this._externalFieldsLoaderFunction)) {
                    this._externalFieldsLoaderFunction(this._externalFieldsLoaderSuccessCallback.bind(this), this.currentItem.itemId);
                }
            } else {
                if (Object.isFunction(this._giftOptionsWindowTitleLoaderFunction) && this._giftOptionsWindowTitleElement) {
                    this._giftOptionsWindowTitleElement.update(this._giftOptionsWindowTitleLoaderFunction(this.currentItem.itemId));
                }
                this._showGiftOptionsWindow();
            }
        }
    },

    /**
     * Set gift options window content loader function
     * This function should accept at least one parameter that will serve as an item ID
     *
     * @param Function loaderFunction loader function
     * @param boolean bind
     */
    setContentLoaderFunction: function (loaderFunction, bind)
    {
        this._contentLoaderFunction = loaderFunction;
        if (bind) {
            this._contentLoaderFunction.bind(this);
        }
    },

    /**
     * Show gift options window
     */
    _showGiftOptionsWindow: function ()
    {
        if (this._giftOptionsWindowMask) {
            toggleSelectsUnderBlock(this._giftOptionsWindowMask, false);
            if (!this._giftOptionsWindowHeight) {
                this._giftOptionsWindowHeight = $('html-body').getHeight();
            }
            this._giftOptionsWindowMask.setStyle({'height': this._giftOptionsWindowHeight+'px'}).show();
        }
        this._giftOptionsWindow.setStyle({'marginTop': -this._giftOptionsWindow.getHeight()/2 + 'px', 'display': 'block'});
    },

    /**
     * Close gift options window
     */
    _closeWindow: function ()
    {
        if (this._giftOptionsWindowMask) {
            toggleSelectsUnderBlock(this._giftOptionsWindowMask, true);
            this._giftOptionsWindowMask.style.display = 'none';
        }
        this._giftOptionsWindow.style.display = 'none';
    },

    /**
     * Load gift options values from external fields to gift options window fields
     *
     * @param string itemId
     *
     * @return boolean success
     */
    loadGiftOptionsValues: function (itemId)
    {
        var groupFieldPrefix = null;
        var groupFieldSuffix = null;
        var externalFieldName = null;
        var externalFieldNameParts = null;
        var giftOptionsWindowFieldName = null;

        for (var i = 0; i < this._giftOptionsGroups.length; i++) {
            groupFieldPrefix = this._giftOptionsGroups[i];
            for (var j = 0; j < this._fieldSuffixes[groupFieldPrefix].length; j++) {
                groupFieldSuffix = this._fieldSuffixes[groupFieldPrefix][j];
                // Composing of gift options window field name
                giftOptionsWindowFieldName = this._giftoptionsWindowFieldPrefix + '_' + groupFieldSuffix;
                // Composing of external field name
                externalFieldNameParts = [];
                externalFieldNameParts.push(groupFieldPrefix);
                if (itemId) {
                    externalFieldNameParts.push(itemId)
                }
                externalFieldNameParts.push(groupFieldSuffix);
                externalFieldName = externalFieldNameParts.join('_');
                // Loading of data
                if (!this._loadGiftOptionValue(giftOptionsWindowFieldName, externalFieldName)) {
                    return false;
                }
            }
        }
        return true;
    },

    /**
     * Save gift options values from gift options window fields to external fields
     *
     * @param string itemId
     *
     * @return boolean success
     */
    saveGiftOptionsValues: function (itemId)
    {
        var groupFieldPrefix = null;
        var groupFieldSuffix = null;
        var externalFieldName = null;
        var externalFieldNameParts = null;
        var giftOptionsWindowFieldName = null;

        for (var i = 0; i < this._giftOptionsGroups.length; i++) {
            groupFieldPrefix = this._giftOptionsGroups[i];
            for (var j = 0; j < this._fieldSuffixes[groupFieldPrefix].length; j++) {
                groupFieldSuffix = this._fieldSuffixes[groupFieldPrefix][j];
                // Composing of gift options window field name
                giftOptionsWindowFieldName = this._giftoptionsWindowFieldPrefix + '_' + groupFieldSuffix;
                // Composing of external field name
                externalFieldNameParts = [];
                externalFieldNameParts.push(groupFieldPrefix);
                if (itemId) {
                    externalFieldNameParts.push(itemId)
                }
                externalFieldNameParts.push(groupFieldSuffix);
                externalFieldName = externalFieldNameParts.join('_');
                // Loading of data
                if (!this._loadGiftOptionValue(externalFieldName, giftOptionsWindowFieldName)) {
                    return false;
                }
            }
        }
        return true;
    },

    /**
     * Load gift option value from source field to destination field
     *
     * @param string destinationFieldId
     * @param string sourceFieldId
     *
     * @return boolean success
     */
    _loadGiftOptionValue: function (destinationFieldId, sourceFieldId)
    {
        var destinationField = $(destinationFieldId);
        var sourceField = $(sourceFieldId);
        try {
            destinationField.setValue(sourceField.getValue());
        } catch (exception) {
            return false;
        }
        // Handle 'change' event of the element if exists
        if (Object.isFunction(destinationField.onchange)) {
            destinationField.onchange();
        }
        return true;
    },

    /**
     * Click handler for Ok button
     *
     * @return boolean success
     */
    onOkButtonClickHandler: function ()
    {
        if (this._giftOptionsForm && Object.isFunction(this._giftOptionsForm.validate)) {
            this._giftOptionsForm.canShowError = true;
            if (!this._giftOptionsForm.validate()) {
                return false;
            }
            this._giftOptionsForm.validator.reset();
        }
        this._closeWindow();
        if (Object.isFunction(this._okCallback)) {
            this._okCallback(this.currentItem.itemId);
        }
        return true;
    },

    /**
     * Click handler for Cancel button
     */
    onCancelButtonClickHandler: function ()
    {
        this._closeWindow()
        if (Object.isFunction(this._cancelCallback)) {
            this._cancelCallback(this.currentItem.itemId);
        }
    },

    /**
     * External fields loader success callback
     * Function shows gift options window if exrternal fields were successfully loaded to page
     */
    _externalFieldsLoaderSuccessCallback: function ()
    {
        if(this.loadGiftOptionsValues(this.currentItem.itemId)) {
            if (Object.isFunction(this._giftOptionsWindowTitleLoaderFunction) && this._giftOptionsWindowTitleElement) {
                this._giftOptionsWindowTitleElement.update(this._giftOptionsWindowTitleLoaderFunction(this.currentItem.itemId));
            }
            this._showGiftOptionsWindow();
        }
    },

    /**
     * Set gift options window title loader function
     * This function should accept at least one parameter that will serve as an item ID
     *
     * @param Function loaderFunction
     */
    setGiftOptionsWindowTitleLoaderFunction: function (loaderFunction)
    {
        this._giftOptionsWindowTitleLoaderFunction = loaderFunction;
    },

    /**
     * Set gift options window title element
     *
     * @param string elementId
     *
     * @return boolean success
     */
    setGiftOptionsWindowTitleElement: function (elementId)
    {
        if ($(elementId)) {
            this._giftOptionsWindowTitleElement = $(elementId);
            return true;
        }
        return false;
    }
}

giftOptions = new GiftOptions();
