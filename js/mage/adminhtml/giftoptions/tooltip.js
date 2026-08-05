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
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Gift Options Tooltip Model
 * 
 * @category    Mage
 * @package     Mage_Adminhtml
 */
function GiftOptionsTooltip() {
    if (this.initialize) this.initialize.apply(this, arguments);
}
GiftOptionsTooltip.prototype = {
    _tooltipLines: [],
    _tooltipWindow: null,
    _tooltipWindowContent: null,
    _targetLinks: [],
    _eventMouseOver: null,
    _eventMouseOut: null,
    _styleOptions: null,
    _tooltipContentLoaderFunction: null,

    initialize: function() {
        this._styleOptions = {
            delta_x: 30,
            delta_y: 0,
            zindex: 1000
        };
        this._eventMouseOver = this.showTooltip.bind(this);
        this._eventMouseOut = this.hideTooltip.bind(this);
    },

    setTooltipWindow: function(windowId, contentId) {
        if (!document.getElementById(windowId) || !document.getElementById(contentId)) {
            return false;
        }
        this._tooltipWindow = document.getElementById(windowId);
        this._tooltipWindowContent = document.getElementById(contentId);
        this.hideTooltip();
        return true;
    },

    addTargetLink: function(linkId, itemId) {
        var el = document.getElementById(linkId);
        if (el) {
            this._targetLinks[linkId] = [];
            this._targetLinks[linkId]['object'] = el;
            this._targetLinks[linkId]['itemId'] = itemId;
            this._registerEvents(this._targetLinks[linkId]['object']);
            return true;
        }
        return false;
    },

    destroy: function() {
        for (var linkId in this._targetLinks) {
            this._targetLinks[linkId]['object'].removeEventListener('mouseover', this._eventMouseOver);
            this._targetLinks[linkId]['object'].removeEventListener('mouseout', this._eventMouseOut);
        }
    },

    _registerEvents: function(element) {
        element.addEventListener('mouseover', this._eventMouseOver);
        element.addEventListener('mouseout', this._eventMouseOut);
    },

    _moveTooltip: function(event) {
        this.setStyles(event.pageX, event.pageY);
    },

    showTooltip: function(event) {
        event.preventDefault();
        event.stopPropagation();
        if (this._tooltipWindow) {
            var link = event.currentTarget;
            var itemId = this._targetLinks[link.id]['itemId'];
            var tooltipContent = '';
            if (typeof this._tooltipContentLoaderFunction === 'function') {
                tooltipContent = this._tooltipContentLoaderFunction(itemId);
            }
            if (tooltipContent !== '') {
                this._updateTooltipWindowContent(tooltipContent);
                this._moveTooltip(event);
                this._tooltipWindow.style.display = '';
                return true;
            }
        }
        return false;
    },

    setStyles: function(x, y) {
        this._tooltipWindow.style.position = 'absolute';
        this._tooltipWindow.style.top = (y + this._styleOptions.delta_y) + 'px';
        this._tooltipWindow.style.left = (x + this._styleOptions.delta_x) + 'px';
        this._tooltipWindow.style.zIndex = this._styleOptions.zindex;
    },

    hideTooltip: function(event) {
        if (this._tooltipWindow) {
            this._tooltipWindow.style.display = 'none';
        }
    },

    setTooltipContentLoaderFunction: function(loaderFunction) {
        this._tooltipContentLoaderFunction = loaderFunction;
    },

    _evalScripts: function(container) {
        container.querySelectorAll('script').forEach(function(script) {
            var evaluatedScript = document.createElement('script');
            Array.prototype.forEach.call(script.attributes, function(attribute) {
                evaluatedScript.setAttribute(attribute.name, attribute.value);
            });
            evaluatedScript.textContent = script.textContent;
            document.head.appendChild(evaluatedScript).parentNode.removeChild(evaluatedScript);
        });
    },

    _updateTooltipWindowContent: function(content) {
        this._tooltipWindowContent.innerHTML = content;
        this._evalScripts(this._tooltipWindowContent);
    }
};

giftOptionsTooltip = new GiftOptionsTooltip();
