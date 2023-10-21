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
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if(!window.Flex) {
    alert('Flex library not loaded');
} else {
    Flex.ImageEditor = Class.create();
    Flex.ImageEditor.prototype = {
        flex: null,
        filters:null,
        containerId:null,
        flexContainerId:null,
        container:null,
        initialize: function(containerId, movieSrc, config) {
            this.containerId = containerId;
            this.container   = $(containerId);

            this.container.controller = this;

            this.config = config;
            this.flexContainerId = this.containerId + '-flash';
            Element.insert(this.container, {bottom: '<div id="'+this.flexContainerId+'"></div>'});

            this.flex = new Flex.Object({
                width:  "1024",
                height: "786",
                src:    movieSrc,
                wmode: 'transparent'
            });


            this.flex.onBridgeInit = this.handleBridgeInit.bind(this);
            this.flex.apply(this.flexContainerId);
        },
        getInnerElement: function(elementName) {
            return $(this.containerId + '-' + elementName);
        },
        handleBridgeInit: function() {
            this.flex.getBridge().addEventListener('image_loaded', this.handleImageLoad.bind(this));
            this.flex.getBridge().setImage(this.config.image);


        },
        handleImageLoad: function(event) {
            alert('image_loaded:' + this.config.image);
            this.hangleImageResize();
        },
        hangleImageResize: function() {
            var size = this.flex.getBridge().getSize();
            this.getInnerElement('width').value = size.width;
            this.getInnerElement('height').value = size.height;

        },
        rotateCw: function() {
            this.flex.getBridge().rotateFw();
            this.hangleImageResize();
        },
        rotateCCw: function() {
            this.flex.getBridge().rotateBw();
            this.hangleImageResize();
        },
        resize: function() {
            this.flex.getBridge().resize(parseFloat(this.getInnerElement('width').value), parseFloat(this.getInnerElement('height').value));
        },
        getImage: function() {
            this.getInnerElement('b64').value = this.flex.getBridge().getBase64Image();
        }
    };
}
