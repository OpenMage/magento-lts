/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class gridDoubleScroll {
    oldCallbacks = {};
    _scrolling = false;

    constructor(containerId, grid) {
        this.containerId = containerId;
        this.grid = grid;
        this.setOldCallback('init', grid.initCallback);
        this.grid.initCallback = this.onGridInit.bind(this);

        this.handlerSyncWrapperScrollBar = this.syncWrapperScrollBar.bind(this);
        this.handlerSyncScrollBarTop = this.syncScrollBarTop.bind(this);
        this.handlerUpdateDoubleScrollWidth = this.updateDoubleScrollWidth.bind(this);
        
        this.initDoubleScroll();
    }

    initDoubleScroll() {
        this.wrapperScrollBar = document.getElementById(this.containerId).querySelector('.hor-scroll');
        if (this.wrapperScrollBar) {
            let scrollbarTop = this.wrapperScrollBar.parentNode.querySelector('.hor-scroll-top');
            if (!scrollbarTop){
                this.createDoubleScroll(this.wrapperScrollBar);
                this.scrollbarTop.addEventListener('scroll', this.handlerSyncWrapperScrollBar, false);
                this.wrapperScrollBar.addEventListener('scroll', this.handlerSyncScrollBarTop, false);
                const observer = new MutationObserver( this.handlerUpdateDoubleScrollWidth );
                observer.observe(this.wrapperScrollBar, { childList: true, subtree: true });
            }
            this.updateDoubleScrollWidth();
        }
    }

    // ensure varienGrid events propagation
    getOldCallback(callbackName) {
        return this.oldCallbacks[callbackName] ? this.oldCallbacks[callbackName] : Prototype.emptyFunction;
    }

    setOldCallback(callbackName, callback) {
        this.oldCallbacks[callbackName] = callback;
    }

    onGridInit(grid) {
        this.initDoubleScroll();
        this.getOldCallback('init')(grid);
    }

    createDoubleScroll() {
        let scrollbarTop = document.createElement('div');
        scrollbarTop.classList.add('hor-scroll-top');
        scrollbarTop.appendChild(document.createElement('div'));
        scrollbarTop.style.overflow = 'auto';
        scrollbarTop.style.overflowY = 'hidden';
        scrollbarTop.firstChild.style.height = '0';
        scrollbarTop.firstChild.style.paddingTop = '1px';
        scrollbarTop.firstChild.appendChild(document.createTextNode('\xA0'));
        this.wrapperScrollBar.parentNode.insertBefore(scrollbarTop, this.wrapperScrollBar);

        this.scrollbarTop = scrollbarTop;
    }

    syncWrapperScrollBar() {
        if(this._scrolling) {
            this._scrolling = false;
            return;
        }
        this._scrolling = true;
        this.wrapperScrollBar.scrollLeft = this.scrollbarTop.scrollLeft;
    }

    syncScrollBarTop() {
        if(this._scrolling) {
            this._scrolling = false;
            return;
        }
        this._scrolling = true;
        this.scrollbarTop.scrollLeft = this.wrapperScrollBar.scrollLeft;
    };

    updateDoubleScrollWidth() {
        if (this.scrollbarTop.firstChild.style.width != this.wrapperScrollBar.scrollWidth) {
            this.scrollbarTop.firstChild.style.width = this.wrapperScrollBar.scrollWidth + 'px';
        }
    }
};
