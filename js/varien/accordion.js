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
 *
 * @constructor
 * @param {string} elem - container element ID
 * @param {string} clickableEntity - CSS selector for clickable headers within sections
 * @param {boolean} [checkAllow=false]
 */
function Accordion() {
    this.initialize(...arguments);
}

Accordion.prototype = {
    initialize: function(elem, clickableEntity, checkAllow) {
        this.container = document.getElementById(elem);
        this.checkAllow = checkAllow || false;
        this.disallowAccessToNextSections = false;
        this.sections = Array.prototype.slice.call(document.querySelectorAll('#' + elem + ' .section'));
        this.currentSection = false;

        const headers = document.querySelectorAll('#' + elem + ' .section ' + clickableEntity);
        const self = this;
        headers.forEach(function (header) {
            header.addEventListener('click', function (event) {
                self.sectionClicked(event);
            });
        });
    },

    sectionClicked: function (event) {
        const target = event.target;
        const section = target.closest('.section');
        if (section) {
            this.openSection(section);
        }
        event.preventDefault();
        event.stopPropagation();
    },

    openSection: function (section) {
        if (typeof section === 'string') {
            section = document.getElementById(section);
        }
        if (!section) {
            return;
        }

        if (this.checkAllow && !section.classList.contains('allow')) {
            return;
        }

        if (section.id !== this.currentSection) {
            this.closeExistingSection();
            this.currentSection = section.id;
            document.getElementById(this.currentSection).classList.add('active');
            const contents = section.querySelectorAll('.a-item');
            if (contents[0]) {
                contents[0].style.display = '';
            }

            if (this.disallowAccessToNextSections) {
                let pastCurrentSection = false;
                for (let i = 0; i < this.sections.length; i++) {
                    if (pastCurrentSection) {
                        this.sections[i].classList.remove('allow');
                    }
                    if (this.sections[i].id === section.id) {
                        pastCurrentSection = true;
                    }
                }
            }
        }
    },

    closeSection: function (section) {
        if (typeof section === 'string') {
            section = document.getElementById(section);
        }
        if (!section) {
            return;
        }
        section.classList.remove('active');
        const contents = section.querySelectorAll('.a-item');
        if (contents[0]) {
            contents[0].style.display = 'none';
        }
    },

    openNextSection: function (setAllow) {
        for (let i = 0; i < this.sections.length; i++) {
            const nextIndex = i + 1;
            if (this.sections[i].id === this.currentSection && this.sections[nextIndex]) {
                if (setAllow) {
                    this.sections[nextIndex].classList.add('allow');
                }
                this.openSection(this.sections[nextIndex]);
                return;
            }
        }
    },

    openPrevSection: function (setAllow) {
        for (let i = 0; i < this.sections.length; i++) {
            const prevIndex = i - 1;
            if (this.sections[i].id === this.currentSection && this.sections[prevIndex]) {
                if (setAllow) {
                    this.sections[prevIndex].classList.add('allow');
                }
                this.openSection(this.sections[prevIndex]);
                return;
            }
        }
    },

    closeExistingSection: function () {
        if (this.currentSection) {
            this.closeSection(this.currentSection);
        }
    }
};
Varien.classCompat(Accordion);
