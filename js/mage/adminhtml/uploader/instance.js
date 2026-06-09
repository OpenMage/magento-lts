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

(function(window, document) {
'use strict';

    /**
     * @name Uploader
     *
     * @param {JSON} config
     *
     * @constructor
     */
    function Uploader(config) {
        this.elementsIds = config.elementIds;
        this.elements = this.getElements(this.elementsIds);

        this.uploaderConfig = config.uploaderConfig;
        this.browseConfig = config.browseConfig;
        this.miscConfig = config.miscConfig;
        this.uploader = new Flow(this.uploaderConfig);

        this.attachEvents();

        /**
         * Bridging functions to retain functionality of existing modules
         */
        this.formatSize = this._getPluralSize.bind(this);
        this.upload = this.onUploadClick.bind(this);
        this.onContainerHideBefore = this.onTabChange.bind(this);
    }

    /**
     * @type {Boolean} Are we in debug mode?
     */
    Uploader.prototype.debug = false;

    /**
     * @constant
     * @type {RegExp} templatePattern
     */
    Uploader.prototype.templatePattern = /\{\{(\w+)\}\}/g;

    /**
     * @type {JSON} Array of elements ids to instantiate DOM collection
     */
    Uploader.prototype.elementsIds = [];

    /**
     * @type {Array.<HTMLElement>} List of elements ids across all uploader functionality
     */
    Uploader.prototype.elements = [];

    /**
     * @type {(Flow)} Uploader object instance
     */
    Uploader.prototype.uploader = {};

    /**
     * @type {JSON} General Uploader config
     */
    Uploader.prototype.uploaderConfig = {};

    /**
     * @type {JSON} browseConfig General Uploader config
     */
    Uploader.prototype.browseConfig = {};

    /**
     * @type {JSON} Misc settings to manipulate Uploader
     */
    Uploader.prototype.miscConfig = {};

    /**
     * @type {Array.<String>} Sizes in plural
     */
    Uploader.prototype.sizesPlural = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    /**
     * @type {Number} Precision of calculation during convetion to human readable size format
     */
    Uploader.prototype.sizePrecisionDefault = 3;

    /**
     * @type {Number} Unit type conversion kib or kb, etc
     */
    Uploader.prototype.sizeUnitType = 1024;

    /**
     * @type {String} Default delete button selector
     */
    Uploader.prototype.deleteButtonSelector = '.delete';

    /**
     * @type {Number} Timeout of completion handler
     */
    Uploader.prototype.onCompleteTimeout = 1000;

    /**
     * @type {(null|Array.<FlowFile>)} Files array stored for success event
     */
    Uploader.prototype.files = null;

    /**
     * Array of strings containing elements ids
     *
     * @param {JSON.<string, Array.<string>>} ids as JSON map,
     *      {<type> => ['id1', 'id2'...], <type2>...}
     * @returns {Array.<HTMLElement>} An array of DOM elements
     */
    Uploader.prototype.getElements = function (ids) {
        var result = {};
        var keys = Object.keys(ids);
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            result[key] = this.getElementsByIds(ids[key]);
        }
        return result;
    };

    /**
     * Get HTMLElement from hash values
     *
     * @param {(Array|String)}ids
     * @returns {(Array.<HTMLElement>|HTMLElement)}
     */
    Uploader.prototype.getElementsByIds = function (ids) {
        var result = [];
        if (ids && Array.isArray(ids)) {
            ids.forEach(function(fromId) {
                var DOMElement = document.getElementById(fromId);

                if (DOMElement) {
                    // Add it only if it's valid HTMLElement, otherwise skip.
                    result.push(DOMElement);
                }
            });
        } else {
            result = document.getElementById(ids);
        }

        return result;
    };

    /**
     * Attach all types of events
     */
    Uploader.prototype.attachEvents = function() {
        this.assignBrowse();

        this.uploader.on('filesSubmitted', this.onFilesSubmitted.bind(this));

        this.uploader.on('uploadStart', this.onUploadStart.bind(this));

        this.uploader.on('fileSuccess', this.onFileSuccess.bind(this));
        this.uploader.on('complete', this.onSuccess.bind(this));

        if (this.elements.container && !this.elements.delete) {
            this.elements.container.addEventListener('click', function(e) {
                if (e.target.matches(this.deleteButtonSelector) || e.target.closest(this.deleteButtonSelector)) {
                    this.onDeleteClick(e);
                }
            }.bind(this));
        } else {
            if (this.elements.delete) {
                this.elements.delete.addEventListener('click', function() {
                    document.dispatchEvent(new CustomEvent('upload:simulateDelete', {
                        bubbles: true,
                        detail: { containerId: this.elementsIds.container }
                    }));
                }.bind(this));
            }
        }
        if (this.elements.upload) {
            this.elements.upload.forEach(function(el) {
                el.addEventListener('click', this.onUploadClick.bind(this));
            }.bind(this));
        }
        if (this.debug) {
            this.uploader.on('catchAll', this.onCatchAll.bind(this));
        }
    };

    Uploader.prototype.onTabChange = function (successFunc) {
        if (this.uploader.files.length && !Array.isArray(this.files)) {
            if (confirm(
                    this._translate('There are files that were selected but not uploaded yet. After switching to another tab your selections will be lost. Do you wish to continue ?')
               )
            ) {
                if (typeof successFunc === 'function') {
                    successFunc();
                } else {
                    this._handleDelete(this.uploader.files);
                    document.dispatchEvent(new CustomEvent('uploader:fileError', {
                        bubbles: true,
                        detail: { containerId: this.elementsIds.container }
                    }));
                }
            } else {
                return 'cannotchange';
            }
        }
    };

    /**
     * Assign browse buttons to appropriate targets
     */
    Uploader.prototype.assignBrowse = function() {
        if (this.elements.browse && this.elements.browse.length) {
            this.uploader.assignBrowse(
                this.elements.browse,
                this.browseConfig.isDirectory || false,
                this.browseConfig.singleFile || false,
                this.browseConfig.attributes || {}
            );
        }
    };

    /**
     * @event
     * @param {Array.<FlowFile>} files
     */
    Uploader.prototype.onFilesSubmitted = function (files) {
        files.filter(function (file) {
            if (this._checkFileSize(file)) {
                alert(
                    this._translate('Maximum allowed file size for upload is') +
                    " " + this.miscConfig.maxSizePlural + "\n" +
                    this._translate('Please check your server PHP settings.')
                );
                file.cancel();
                return false;
            }
            return true;
        }.bind(this)).forEach(function (file) {
            this._handleUpdateFile(file);
        }.bind(this));
    };

    Uploader.prototype._handleUpdateFile = function (file) {
        var replaceBrowseWithRemove = this.miscConfig.replaceBrowseWithRemove;
        if (replaceBrowseWithRemove) {
            document.dispatchEvent(new CustomEvent('uploader:simulateNewUpload', {
                bubbles: true,
                detail: { containerId: this.elementsIds.container }
            }));
        }
        var html = this._renderFromTemplate(
            this.elements.templateFile,
            {
                name: file.name,
                size: file.size ? '(' + this._getPluralSize(file.size) + ')' : '',
                id: file.uniqueIdentifier
            }
        );
        if (replaceBrowseWithRemove) {
            this.elements.container.innerHTML = html;
        } else {
            this.elements.container.insertAdjacentHTML('beforeend', html);
        }
    };

    /**
     * Upload button is being pressed
     *
     * @event
     */
    Uploader.prototype.onUploadStart = function () {
        var files = this.uploader.files;

        files.forEach(function (file) {
            var id = file.uniqueIdentifier;
            var container = this._getFileContainerById(id);

            container.classList.remove('new');
            container.classList.remove('error');
            container.classList.add('progress');
            this._getProgressTextById(id).innerHTML = this._translate('Uploading...');

            var deleteButton = this._getDeleteButtonById(id);
            if (deleteButton) {
                deleteButton.style.display = 'none';
            }
        }.bind(this));

        this.files = this.uploader.files;
    };

    /**
     * Get file-line container by id
     *
     * @param {String} id
     * @returns {HTMLElement}
     * @private
     */
    Uploader.prototype._getFileContainerById = function (id) {
        return document.getElementById(id + '-container');
    };

    /**
     * Get text update container
     *
     * @param id
     * @returns {*}
     * @private
     */
    Uploader.prototype._getProgressTextById = function (id) {
        return this._getFileContainerById(id).querySelector('.progress-text');
    };

    Uploader.prototype._getDeleteButtonById = function(id) {
        return this._getFileContainerById(id).querySelector('.delete');
    };

    /**
     * Handle delete button click
     *
     * @event
     * @param {Event} e
     */
    Uploader.prototype.onDeleteClick = function (e) {
        var element = e.target;
        var id = element.id;
        if (!id) {
            id = element.closest(this.deleteButtonSelector).id;
        }
        this._handleDelete([this.uploader.getFromUniqueIdentifier(id)]);
    };

    /**
     * Complete handler of uploading process
     *
     * @event
     */
    Uploader.prototype.onSuccess = function () {
        document.dispatchEvent(new CustomEvent('uploader:success', {
            bubbles: true,
            detail: { files: this.files }
        }));
        this.files = null;
    };

    /**
     * Successfully uploaded file, notify about that other components, handle deletion from queue
     *
     * @param {FlowFile} file
     * @param {JSON} response
     */
    Uploader.prototype.onFileSuccess = function (file, response) {
        response = JSON.parse(response);
        var id = file.uniqueIdentifier;
        var error = response.error;
        var container = this._getFileContainerById(id);
        container.classList.remove('progress');
        container.classList.add(error ? 'error' : 'complete');

        this._getProgressTextById(id).innerHTML = this._translate(
            error ? this._XSSFilter(error) : 'Complete'
        );

        setTimeout(function() {
            if (!error) {
                document.dispatchEvent(new CustomEvent('uploader:fileSuccess', {
                    bubbles: true,
                    detail: {
                        response: JSON.stringify(response),
                        containerId: this.elementsIds.container
                    }
                }));
            } else {
                document.dispatchEvent(new CustomEvent('uploader:fileError', {
                    bubbles: true,
                    detail: { containerId: this.elementsIds.container }
                }));
            }
            this._handleDelete([file]);
        }.bind(this), !error ? this.onCompleteTimeout : this.onCompleteTimeout * 3);
    };

    /**
     * Upload button click event
     *
     * @event
     */
    Uploader.prototype.onUploadClick = function () {
        try {
            this.uploader.upload();
        } catch(e) {
            if (console) {
                console.error(e);
            }
        }
    };

    /**
     * Event for debugging purposes
     *
     * @event
     */
    Uploader.prototype.onCatchAll = function () {
        if (console.group && console.groupEnd && console.trace) {
            var args = [].splice.call(arguments, 1);
            console.group();
                console.info(arguments[0]);
                console.log("Uploader Instance:", this);
                console.log("Event Arguments:", args);
                console.trace();
            console.groupEnd();
        } else {
            console.log(this, arguments);
        }
    };

    /**
     * Handle deletition of files
     * @param {Array.<FlowFile>} files
     * @private
     */
    Uploader.prototype._handleDelete = function (files) {
        files.forEach(function (file) {
            file.cancel();
            var container = document.getElementById(file.uniqueIdentifier + '-container');
            if (container) {
                container.remove();
            }
        });
    };

    /**
     * Check whenever file size exceeded permitted amount
     *
     * @param {FlowFile} file
     * @returns {boolean}
     * @private
     */
    Uploader.prototype._checkFileSize = function (file) {
        return this.miscConfig.maxSizeInBytes && file.size > this.miscConfig.maxSizeInBytes;
    };

    /**
     * Make a translation of string
     *
     * @param {String} text
     * @returns {String}
     * @private
     */
    Uploader.prototype._translate = function (text) {
        try {
            return Translator.translate(text);
        }
        catch(e) {
            return text;
        }
    };

    /**
     * Render from given template and given variables to assign
     *
     * @param {HTMLElement} template
     * @param {JSON} vars
     * @returns {String}
     * @private
     */
    Uploader.prototype._renderFromTemplate = function (template, vars) {
        var templateStr = this._XSSFilter(template.innerHTML);
        return templateStr.replace(this.templatePattern, function(match, key) {
            return vars[key] !== undefined ? vars[key] : '';
        });
    };

    /**
     * Format size with precision
     *
     * @param {Number} sizeInBytes
     * @param {Number} [precision]
     * @returns {String}
     * @private
     */
    Uploader.prototype._getPluralSize = function (sizeInBytes, precision) {
        if (sizeInBytes == 0) {
            return 0 + this.sizesPlural[0];
        }
        var dm = (precision || this.sizePrecisionDefault) + 1;
        var i = Math.floor(Math.log(sizeInBytes) / Math.log(this.sizeUnitType));

        return (sizeInBytes / Math.pow(this.sizeUnitType, i)).toPrecision(dm) + ' ' + this.sizesPlural[i];
    };

    /**
     * Purify template string to prevent XSS attacks
     *
     * @param {String} str
     * @returns {String}
     * @private
     */
    Uploader.prototype._XSSFilter = function (str) {
        return str
            .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
            // Remove inline event handlers like onclick, onload, etc
            .replace(/(on[a-z]+=["][^"]+["])(?=[^>]*>)/img, '')
            .replace(/(on[a-z]+=['][^']+['])(?=[^>]*>)/img, '')
        ;
    };

    window.Uploader = Uploader;

})(window, document);
