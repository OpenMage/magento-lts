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
class GiftOptionsTooltip
{
    constructor() {
        this._tooltipWindow = null;
        this._tooltipWindowContent = null;
        this._targetLinks = [];
        this._eventMouseOver = this.showTooltip.bind(this);
        this._eventMouseOut = this.hideTooltip.bind(this);
        this._styleOptions = null;
        this._tooltipContentLoaderFunction = null;

        this.initialize();
    }

    initialize() {
        let options = {
            delta_x: 30,
            delta_y: 0,
            zindex: 1000
        };
        this._styleOptions = options;
    }

    setTooltipWindow(windowId, contentId) {
        if (!document.getElementById(windowId) || !document.getElementById(contentId)) {
            return false;
        }
        this._tooltipWindow = document.getElementById(windowId);
        this._tooltipWindowContent = document.getElementById(contentId);
        this.hideTooltip();
        return true;
    }

    // Add tooltip to specified link
    addTargetLink(linkId, itemId) {
        let link = document.getElementById(linkId);
        if (link) {
            this._targetLinks[linkId] = {
                object: link,
                itemId: itemId
            };
            this._registerEvents(this._targetLinks[linkId].object);
            return true;
        }
        return false;
    }

    // Detach event listeners from target links when tooltip is destroyed
    destroy() {
        for (const linkId in this._targetLinks) {
            this._targetLinks[linkId].object.removeEventListener('mouseover', this._eventMouseOver);
            this._targetLinks[linkId].object.removeEventListener('mouseout', this._eventMouseOut);
        }
    }

    _registerEvents(element) {
        element.addEventListener('mouseover', this._eventMouseOver);
        element.addEventListener('mouseout', this._eventMouseOut);
    }

    // Move tooltip to mouse position
    _moveTooltip(event) {
        event.preventDefault();
        const mouseX = event.clientX;
        const mouseY = event.clientY;
        this.setStyles(mouseX, mouseY);
    }

    showTooltip(event) {
        event.preventDefault();
        if (this._tooltipWindow) {
            const link = event.target;
            const itemId = this._targetLinks[link.id].itemId;
            let tooltipContent = '';
            if (typeof this._tooltipContentLoaderFunction === 'function') {
                tooltipContent = this._tooltipContentLoaderFunction(itemId);
            }
            if (tooltipContent !== '') {
                this._updateTooltipWindowContent(tooltipContent);
                this._moveTooltip(event);
                this._tooltipWindow.style.display = 'block';
                return true;
            }
        }
        return false;
    }

    // Set tooltip window styles
    setStyles(x, y) {
        this._tooltipWindow.style.position = 'absolute';
        this._tooltipWindow.style.top = y + this._styleOptions.delta_y + 'px';
        this._tooltipWindow.style.left = x + this._styleOptions.delta_x + 'px';
        this._tooltipWindow.style.zIndex = this._styleOptions.zindex;
    }

    hideTooltip() {
        if (this._tooltipWindow) {
            this._tooltipWindow.style.display = 'none';
        }
    }

    // Set gift options tooltip content loader function
    setTooltipContentLoaderFunction(loaderFunction) {
        this._tooltipContentLoaderFunction = loaderFunction;
    }

    // Update tooltip window content
    _updateTooltipWindowContent(content) {
        this._tooltipWindowContent.innerHTML = content;
    }
}

let giftOptionsTooltip = new GiftOptionsTooltip();
