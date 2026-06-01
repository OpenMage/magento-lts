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
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

/**
 * @classDescription simple Navigation with replacing old handlers
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */
var mainNav = function() {

    var main = {
        obj_nav: document.getElementById(arguments[0]) || document.getElementById('nav'),

        settings: {
            show_delay: 0,
            hide_delay: 0,
        },

        init: function(obj, level) {
            obj.lists = Array.from(obj.children);
            obj.lists.forEach(function(el) {
                main.handlNavElement(el);
            });
        },

        handlNavElement: function(list) {
            if (list !== undefined) {
                list.onmouseover = function() {
                    main.fireNavEvent(this, true);
                };
                list.onmouseout = function() {
                    main.fireNavEvent(this, false);
                };
                var subList = list.querySelector('ul');
                if (subList) {
                    main.init(subList, true);
                }
            }
        },

        fireNavEvent: function(elm, ev) {
            var children = Array.from(elm.children);
            if (ev) {
                elm.classList.add('over');
                var a = elm.querySelector('a');
                if (a) a.classList.add('over');
                if (children[1]) {
                    main.show(children[1]);
                }
            } else {
                elm.classList.remove('over');
                var a = elm.querySelector('a');
                if (a) a.classList.remove('over');
                if (children[1]) {
                    main.hide(children[1]);
                }
            }
        },

        show: function(sub_elm) {
            if (sub_elm.hide_time_id) {
                clearTimeout(sub_elm.hide_time_id);
            }
            sub_elm.show_time_id = setTimeout(function() {
                sub_elm.classList.add('shown-sub');
            }, main.settings.show_delay);
        },

        hide: function(sub_elm) {
            if (sub_elm.show_time_id) {
                clearTimeout(sub_elm.show_time_id);
            }
            sub_elm.hide_time_id = setTimeout(function() {
                sub_elm.classList.remove('shown-sub');
            }, main.settings.hide_delay);
        }
    };

    if (arguments[1]) {
        Object.assign(main.settings, arguments[1]);
    }
    if (main.obj_nav) {
        main.init(main.obj_nav, false);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    mainNav('nav', {'show_delay': '100', 'hide_delay': '100'});
});
