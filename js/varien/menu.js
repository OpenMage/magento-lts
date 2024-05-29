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
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * @classDescription simple Navigation with replacing old handlers
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */
var mainNav = function () {
    var main = {
        obj_nav: document.getElementById('nav'),

        settings: {
            show_delay: 0,
            hide_delay: 0,
        },

        init: function (obj, level) {
            obj.lists = Array.from(obj.children);
            obj.lists.forEach(function (el, ind) {
                main.handlNavElement(el);
            });
        },

        handlNavElement: function (list) {
            if (list !== undefined) {
                list.addEventListener('mouseover', function () {
                    main.fireNavEvent(this, true);
                });
                list.addEventListener('mouseout', function () {
                    main.fireNavEvent(this, false);
                });

                let nestedUl = list.querySelector('ul');
                if (nestedUl) {
                    main.init(nestedUl, true);
                }
            }
        },

        fireNavEvent: function (elm, ev) {
            if (ev) {
                elm.classList.add("over");
                elm.querySelector("a").classList.add("over");
                let secondChild = elm.children[1];
                if (secondChild) {
                    main.show(secondChild);
                }
            } else {
                elm.classList.remove("over");
                elm.querySelector("a").classList.remove("over");
                let secondChild = elm.children[1];
                if (secondChild) {
                    main.hide(secondChild);
                }
            }
        },

        show: function (sub_elm) {
            if (sub_elm.hide_time_id) {
                clearTimeout(sub_elm.hide_time_id);
            }
            sub_elm.show_time_id = setTimeout(() => {
                if (!sub_elm.classList.contains("shown-sub")) {
                    sub_elm.classList.add("shown-sub");
                }
            }, main.settings.show_delay);
        },

        hide: function (sub_elm) {
            if (sub_elm.show_time_id) {
                clearTimeout(sub_elm.show_time_id);
            }
            sub_elm.hide_time_id = setTimeout(function () {
                if (sub_elm.classList.contains("shown-sub")) {
                    sub_elm.classList.remove("shown-sub");
                }
            }, main.settings.hide_delay);
        }

    };
    if (arguments[1]) {
        main.settings = {...main.settings, ...arguments[1]};
    }
    if (main.obj_nav) {
        main.init(main.obj_nav, false);
    }
};

document.addEventListener("DOMContentLoaded", function () {
    mainNav("nav", {"show_delay": "100", "hide_delay": "100"});
});
