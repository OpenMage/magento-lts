/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

/**************************** WEEE STUFF ********************************/
function taxToggle(details, switcher, expandedClassName) {
    // Templates pass either an id or an element (e.g. `this`), like $() accepted
    const detailsEl  = typeof details === 'string' ? document.getElementById(details) : details;
    const switcherEl = typeof switcher === 'string' ? document.getElementById(switcher) : switcher;
    if (!detailsEl || !switcherEl) {
        return;
    }

    if (detailsEl.style.display === 'none') {
        detailsEl.style.display  = '';
        switcherEl.classList.add(expandedClassName);
    } else {
        detailsEl.style.display  = 'none';
        switcherEl.classList.remove(expandedClassName);
    }
}
