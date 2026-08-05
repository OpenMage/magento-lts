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
 * @copyright   Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Lightweight drag-and-drop sortable for list containers.
 * Replaces Scriptaculous Sortable.create — no Prototype.js dependency.
 *
 * Usage:
 *   makeSortable(containerEl, {
 *     handle:   'css-class-of-drag-handle',  // child element used to initiate drag
 *     onUpdate: function() { ... }           // called when order changes after drop
 *   });
 */
(function () {
    'use strict';

    /**
     * @param {HTMLElement} container  Parent element whose direct children are sortable
     * @param {Object}      options
     * @param {string}      options.handle    CSS class of the drag-handle element inside each item
     * @param {Function}    [options.onUpdate] Called after a successful reorder
     */
    window.makeSortable = function (container, options) {
        var handleClass = options.handle   || '';
        var onUpdate    = options.onUpdate || null;

        var dragging    = null;   // the <li> being dragged
        var orderBefore = [];     // snapshot of item ids before drag starts

        function itemIds() {
            return Array.from(container.children).map(function (el) { return el.id; });
        }

        function attach(item) {
            var handle = handleClass ? item.querySelector('.' + handleClass) : item;
            if (!handle) return;

            handle.style.cursor = 'grab';

            // Only make the item draggable when the user presses down on the handle.
            // This prevents accidental drags when clicking inputs/buttons inside the row.
            handle.addEventListener('mousedown', function () {
                item.setAttribute('draggable', 'true');
                orderBefore = itemIds();
            });

            item.addEventListener('dragstart', function (e) {
                dragging = item;
                // Required by Firefox to allow drag
                e.dataTransfer.setData('text/plain', '');
                e.dataTransfer.effectAllowed = 'move';
                // Defer opacity so the ghost image is captured before style change
                setTimeout(function () { item.classList.add('sortable-dragging'); }, 0);
            });

            item.addEventListener('dragend', function () {
                item.classList.remove('sortable-dragging');
                item.removeAttribute('draggable');

                var changed = orderBefore.join(',') !== itemIds().join(',');
                dragging = null;

                if (changed && onUpdate) {
                    onUpdate();
                }
            });

            // Real-time reorder: move the dragged element as the cursor enters each sibling
            item.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (!dragging || dragging === item) return;

                var rect = item.getBoundingClientRect();
                var insertBefore = e.clientY < rect.top + rect.height / 2;

                if (insertBefore) {
                    container.insertBefore(dragging, item);
                } else if (item.nextSibling !== dragging) {
                    container.insertBefore(dragging, item.nextSibling);
                }
            });
        }

        // Make the container itself a valid drop target so drops on the gap between
        // items (or below the last item) don't cancel the drag.
        container.addEventListener('dragover', function (e) { e.preventDefault(); });

        Array.from(container.children).forEach(attach);
    };
}());
