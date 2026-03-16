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

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 * @constructor
 */
function varienAccordion(containerId, activeOnlyOne) {
    this.containerId = containerId;
    this.activeOnlyOne = activeOnlyOne || false;
    this.container = document.getElementById(this.containerId);
    this.items = Array.from(document.querySelectorAll('#' + this.containerId + ' dt'));
    this.loader = new varienLoader(true);

    var links = Array.from(document.querySelectorAll('#' + this.containerId + ' dt a'));
    var self = this;
    for (var i = 0; i < links.length; i++) {
        if (links[i].href) {
            links[i].addEventListener('click', self.clickItem.bind(self));
            if (this.items[i]) {
                this.items[i].dd = this.items[i].nextElementSibling;
                while (this.items[i].dd && this.items[i].dd.tagName !== 'DD') {
                    this.items[i].dd = this.items[i].dd.nextElementSibling;
                }
                this.items[i].link = links[i];
            }
        }
    }

    this.initFromCookie();
}

varienAccordion.prototype = {
    initFromCookie: function () {
        var activeItemId, visibility;
        if (this.activeOnlyOne &&
            (activeItemId = Cookie.read(this.cookiePrefix() + 'active-item')) !== null) {
            this.hideAllItems();
            this.showItem(this.getItemById(activeItemId));
        } else if (!this.activeOnlyOne) {
            var self = this;
            this.items.forEach(function (item) {
                if ((visibility = Cookie.read(self.cookiePrefix() + item.id)) !== null) {
                    if (visibility == 0) {
                        self.hideItem(item);
                    } else {
                        self.showItem(item);
                    }
                }
            });
        }
    },
    cookiePrefix: function () {
        return 'accordion-' + this.containerId + '-';
    },
    getItemById: function (itemId) {
        for (var i = 0; i < this.items.length; i++) {
            if (this.items[i].id == itemId) {
                return this.items[i];
            }
        }
        return null;
    },
    clickItem: function (event) {
        var item = event.target.closest('dt');
        if (this.activeOnlyOne) {
            this.hideAllItems();
            this.showItem(item);
            Cookie.write(this.cookiePrefix() + 'active-item', item.id, 30*24*60*60);
        } else {
            if (this.isItemVisible(item)) {
                this.hideItem(item);
                Cookie.write(this.cookiePrefix() + item.id, 0, 30*24*60*60);
            } else {
                this.showItem(item);
                Cookie.write(this.cookiePrefix() + item.id, 1, 30*24*60*60);
            }
        }
        event.preventDefault();
        event.stopPropagation();
    },
    showItem: function (item) {
        if (item && item.link) {
            if (item.link.href) {
                this.loadContent(item);
            }
            item.classList.add('open');
            if (item.dd) item.dd.classList.add('open');
        }
    },
    hideItem: function (item) {
        item.classList.remove('open');
        if (item.dd) item.dd.classList.remove('open');
    },
    isItemVisible: function (item) {
        return item.classList.contains('open');
    },
    loadContent: function (item) {
        if (item.link.href.indexOf('#') == item.link.href.length - 1) {
            return;
        }
        if (item.link.classList.contains('ajax')) {
            this.loadingItem = item;
            this.loader.load(item.link.href, {updaterId: this.loadingItem.dd.id}, this.setItemContent.bind(this));
            return;
        }
        location.href = item.link.href;
    },
    setItemContent: function (content) {
        try {
            if (JSON.parse(content)) return;
        } catch (e) {}
        this.loadingItem.dd.innerHTML = content;
    },
    hideAllItems: function () {
        for (var i = 0; i < this.items.length; i++) {
            if (this.items[i].id) {
                this.items[i].classList.remove('open');
                if (this.items[i].dd) this.items[i].dd.classList.remove('open');
            }
        }
    }
};
