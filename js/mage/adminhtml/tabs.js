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
function varienTabs(containerId, destElementId, activeTabId, shadowTabs) {
    this.containerId = containerId;
    this.destElementId = destElementId;
    this.activeTab = null;

    this.tabOnClick = this.tabMouseClick.bind(this);

    this.tabs = Array.from(document.querySelectorAll('#' + this.containerId + ' li a.tab-item-link'));

    this.hideAllTabsContent();
    var destEl = document.getElementById(this.destElementId);
    for (var tab = 0; tab < this.tabs.length; tab++) {
        this.tabs[tab].addEventListener('click', this.tabOnClick);
        if (destEl) {
            var tabContentElement = document.getElementById(this.getTabContentElementId(this.tabs[tab]));
            if (tabContentElement && tabContentElement.parentNode.id != this.destElementId) {
                destEl.appendChild(tabContentElement);
                tabContentElement.container = this;
                tabContentElement.statusBar = this.tabs[tab];
                tabContentElement.tabObject = this.tabs[tab];
                this.tabs[tab].contentMoved = true;
                this.tabs[tab].container = this;
                this.tabs[tab].show = function () {
                    this.container.showTabContent(this);
                };
                if (typeof varienGlobalEvents !== 'undefined') {
                    varienGlobalEvents.fireEvent('moveTab', {tab: this.tabs[tab]});
                }
            }
        }
        if (this.tabs[tab].id && shadowTabs && shadowTabs[this.tabs[tab].id]) {
            this.tabs[tab].shadowTabs = shadowTabs[this.tabs[tab].id];
        }
    }

    this.displayFirst = activeTabId;
    window.addEventListener('load', this.moveTabContentInDest.bind(this));
}

varienTabs.prototype = {
    setSkipDisplayFirstTab: function () {
        this.displayFirst = null;
    },

    moveTabContentInDest: function () {
        var destEl = document.getElementById(this.destElementId);
        for (var tab = 0; tab < this.tabs.length; tab++) {
            if (destEl && !this.tabs[tab].contentMoved) {
                var tabContentElement = document.getElementById(this.getTabContentElementId(this.tabs[tab]));
                if (tabContentElement && tabContentElement.parentNode.id != this.destElementId) {
                    destEl.appendChild(tabContentElement);
                    tabContentElement.container = this;
                    tabContentElement.statusBar = this.tabs[tab];
                    tabContentElement.tabObject = this.tabs[tab];
                    this.tabs[tab].container = this;
                    this.tabs[tab].show = function () {
                        this.container.showTabContent(this);
                    };
                    if (typeof varienGlobalEvents !== 'undefined') {
                        varienGlobalEvents.fireEvent('moveTab', {tab: this.tabs[tab]});
                    }
                }
            }
        }
        if (this.displayFirst) {
            this.showTabContent(document.getElementById(this.displayFirst));
            this.displayFirst = null;
        }
    },

    getTabContentElementId: function (tab) {
        if (tab) {
            return tab.id + '_content';
        }
        return false;
    },

    tabMouseClick: function (event) {
        var tab = event.target.closest('a');
        if ((tab.href.indexOf('#') != tab.href.length - 1)
            && !(tab.classList.contains('ajax'))
        ) {
            location.href = tab.href;
        } else {
            this.showTabContent(tab);
        }
        event.preventDefault();
        event.stopPropagation();
    },

    hideAllTabsContent: function () {
        for (var tab in this.tabs) {
            this.hideTabContent(this.tabs[tab]);
        }
    },

    showTabContentImmediately: function (tab) {
        this.hideAllTabsContent();
        var tabContentElement = document.getElementById(this.getTabContentElementId(tab));
        if (tabContentElement) {
            tabContentElement.style.display = '';
            tab.classList.add('active');
            if (tab.shadowTabs && tab.shadowTabs.length) {
                for (var k in tab.shadowTabs) {
                    this.loadShadowTab(document.getElementById(tab.shadowTabs[k]));
                }
            }
            if (!tab.classList.contains('ajax') || !tab.classList.contains('only')) {
                tab.classList.remove('notloaded');
            }
            this.activeTab = tab;
        }
        if (typeof varienGlobalEvents !== 'undefined') {
            varienGlobalEvents.fireEvent('showTab', {tab: tab});
        }
    },

    showTabContent: function (tab) {
        var tabContentElement = document.getElementById(this.getTabContentElementId(tab));
        if (tabContentElement) {
            if (this.activeTab != tab) {
                if (typeof varienGlobalEvents !== 'undefined') {
                    var activeContent = this.activeTab ? document.getElementById(this.getTabContentElementId(this.activeTab)) : null;
                    if (varienGlobalEvents.fireEvent('tabChangeBefore', activeContent).indexOf('cannotchange') != -1) {
                        return;
                    }
                }
            }
            var isAjax = tab.classList.contains('ajax');
            var isEmpty = tabContentElement.innerHTML == '' && tab.href.indexOf('#') != tab.href.length - 1;
            var isNotLoaded = tab.classList.contains('notloaded');

            if (isAjax && (isEmpty || isNotLoaded)) {
                var self = this;
                fetch(tab.href, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'form_key=' + encodeURIComponent(FORM_KEY)
                })
                .then(function (resp) { return resp.text(); })
                .then(function (text) {
                    try {
                        var response = JSON.parse(text);
                        if (response.error) {
                            alert(response.message);
                        }
                        if (response.ajaxExpired && response.ajaxRedirect) {
                            setLocation(response.ajaxRedirect);
                        }
                    } catch (e) {
                        tabContentElement.innerHTML = text;
                        self.showTabContentImmediately(tab);
                    }
                });
            } else {
                this.showTabContentImmediately(tab);
            }
        }
    },

    loadShadowTab: function (tab) {
        var tabContentElement = document.getElementById(this.getTabContentElementId(tab));
        if (tabContentElement && tab.classList.contains('ajax') && tab.classList.contains('notloaded')) {
            fetch(tab.href, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'form_key=' + encodeURIComponent(FORM_KEY)
            })
            .then(function (resp) { return resp.text(); })
            .then(function (text) {
                try {
                    var response = JSON.parse(text);
                    if (response.error) {
                        alert(response.message);
                    }
                    if (response.ajaxExpired && response.ajaxRedirect) {
                        setLocation(response.ajaxRedirect);
                    }
                } catch (e) {
                    tabContentElement.innerHTML = text;
                    if (!tab.classList.contains('ajax') || !tab.classList.contains('only')) {
                        tab.classList.remove('notloaded');
                    }
                }
            });
        }
    },

    hideTabContent: function (tab) {
        var destEl = document.getElementById(this.destElementId);
        var tabContentElement = document.getElementById(this.getTabContentElementId(tab));
        if (destEl && tabContentElement) {
            tabContentElement.style.display = 'none';
            tab.classList.remove('active');
        }
        if (typeof varienGlobalEvents !== 'undefined') {
            varienGlobalEvents.fireEvent('hideTab', {tab: tab});
        }
    }
};
