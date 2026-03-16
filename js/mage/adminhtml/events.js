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
 * Custom event bus for OpenMage admin.
 * Rewritten to vanilla JS — no Prototype.js dependency.
 *
 * @constructor
 */
function varienEvents() {
    this.arrEvents = {};
    this.eventPrefix = '';
}

varienEvents.prototype = {
    /**
     * Attach a handler to an event.
     * @param {string} eventName
     * @param {Function} handler
     * @param {boolean} [asynch=false]
     */
    attachEventHandler: function (eventName, handler) {
        if (typeof handler === 'undefined' || handler === null) {
            return;
        }
        eventName = eventName + this.eventPrefix;
        if (this.arrEvents[eventName] == null) {
            this.arrEvents[eventName] = [];
        }
        var asynchVar = arguments.length > 2 ? arguments[2] : false;
        this.arrEvents[eventName].push({
            method: handler,
            asynch: asynchVar
        });
    },

    /**
     * Remove a single handler from an event.
     * @param {string} eventName
     * @param {Function} handler
     */
    removeEventHandler: function (eventName, handler) {
        eventName = eventName + this.eventPrefix;
        if (this.arrEvents[eventName] != null) {
            this.arrEvents[eventName] = this.arrEvents[eventName].filter(function (obj) {
                return obj.method !== handler;
            });
        }
    },

    /**
     * Remove all handlers from a single event.
     * @param {string} eventName
     */
    clearEventHandlers: function (eventName) {
        eventName = eventName + this.eventPrefix;
        this.arrEvents[eventName] = null;
    },

    /**
     * Remove all handlers from ALL events.
     */
    clearAllEventHandlers: function () {
        this.arrEvents = {};
    },

    /**
     * Fire an event, executing all registered handlers.
     * @param {string} eventName
     * @param {*} [args] Passed to each handler
     * @return {Array} Results from synchronous handlers
     */
    fireEvent: function (eventName) {
        var evtName = eventName + this.eventPrefix;
        var results = [];
        var result;
        if (this.arrEvents[evtName] != null) {
            var len = this.arrEvents[evtName].length;
            for (var i = 0; i < len; i++) {
                try {
                    if (arguments.length > 1) {
                        if (this.arrEvents[evtName][i].asynch) {
                            var eventArgs = arguments[1];
                            var method = this.arrEvents[evtName][i].method.bind(this);
                            setTimeout(function () {
                                method(eventArgs);
                            }, 10);
                        } else {
                            result = this.arrEvents[evtName][i].method(arguments[1]);
                        }
                    } else {
                        if (this.arrEvents[evtName][i].asynch) {
                            var eventHandler = this.arrEvents[evtName][i].method;
                            setTimeout(eventHandler, 1);
                        } else if (this.arrEvents && this.arrEvents[evtName] && this.arrEvents[evtName][i] && this.arrEvents[evtName][i].method) {
                            result = this.arrEvents[evtName][i].method();
                        }
                    }
                    results.push(result);
                } catch (e) {
                    if (this.id) {
                        alert('error: error in ' + this.id + '.fireEvent():\n\nevent name: ' + eventName + '\n\nerror message: ' + e.message);
                    } else {
                        alert('error: error in [unknown object].fireEvent():\n\nevent name: ' + eventName + '\n\nerror message: ' + e.message);
                    }
                }
            }
        }
        return results;
    }
};

var varienGlobalEvents = new varienEvents();
