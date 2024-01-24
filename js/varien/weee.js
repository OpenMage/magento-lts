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
 * @copyright   Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

function taxToggle(details, switcher, expandedClassName)
{
    var detailsElement = document.getElementById(details);
    var switcherElement = document.getElementById(switcher);

    if (detailsElement.style.display == 'none') {
        detailsElement.style.display = 'block';
        switcherElement.classList.add(expandedClassName);
    } else {
        detailsElement.style.display = 'none';
        switcherElement.classList.remove(expandedClassName);
    }
}
