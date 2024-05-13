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
class Accordion {
    constructor(elem, clickableEntity, checkAllow) {
        this.container = document.getElementById(elem);
        this.checkAllow = checkAllow || false;
        this.disallowAccessToNextSections = false;
        this.sections = Array.from(document.querySelectorAll('#' + elem + ' .section'));
        this.currentSection = false;
        var headers = Array.from(document.querySelectorAll('#' + elem + ' .section ' + clickableEntity));
        headers.forEach(function(header) {
            header.addEventListener('click', this.sectionClicked.bind(this));
        }.bind(this));
    }

    sectionClicked(event) {
        this.openSection(event.target.closest('.section'));
        event.stopPropagation();
    }

    openSection(section) {
        if (typeof section == 'string') {
            section = document.getElementById(section);
        }

        if (this.checkAllow && section && !section.classList.contains('allow')){
            return;
        }

        if(section.id != this.currentSection) {
            this.closeExistingSection();
            this.currentSection = section.id;
            section.classList.add('active');
            var contents = section.querySelector('.a-item');
            contents.style.display = 'block';

            if (this.disallowAccessToNextSections) {
                var pastCurrentSection = false;
                for (var i=0; i<this.sections.length; i++) {
                    if (pastCurrentSection) {
                        this.sections[i].classList.remove('allow');
                    }
                    if (this.sections[i].id==section.id) {
                        pastCurrentSection = true;
                    }
                }
            }
        }
    }

    closeSection(section) {
        section.classList.remove('active');
        var contents = section.querySelector('.a-item');
        contents.style.display = 'none';
    }

    openNextSection(setAllow){
        for (let i = 0; i < this.sections.length; i++) {
            let nextIndex = i + 1;
            if (this.sections[i].id == this.currentSection && this.sections[nextIndex]){
                if (setAllow) {
                    this.sections[nextIndex].classList.add('allow');
                }
                this.openSection(this.sections[nextIndex]);
                return;
            }
        }
    }

    openPrevSection(setAllow){
        for (let i = 0; i < this.sections.length; i++) {
            let prevIndex = i - 1;
            if (this.sections[i].id == this.currentSection && this.sections[prevIndex]){
                if (setAllow) {
                    this.sections[prevIndex].classList.add('allow');
                }
                this.openSection(this.sections[prevIndex]);
                return;
            }
        }
    }

    closeExistingSection() {
        if(this.currentSection) {
            this.closeSection(document.getElementById(this.currentSection));
        }
    }
}
