/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Translate {
    constructor(data) {
        this.data = new Map(Object.entries(data));
    }

    translate(text) {
        if(this.data.has(text)) {
            return this.data.get(text);
        }
        return text;
    }

    add(keyOrObject, value) {
        if (arguments.length > 1) {
            this.data.set(keyOrObject, value);
        } else if (typeof keyOrObject == 'object') {
            Object.entries(keyOrObject).forEach(([key, value]) => {
                this.data.set(key, value);
            });
        }
    }
}
