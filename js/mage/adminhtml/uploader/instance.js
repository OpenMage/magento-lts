/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function(flowFactory, window, document) {
'use strict';
    window.Uploader = Class.create({

        /**
         * @type {Boolean} Are we in debug mode?
         */
        debug: false,

        /**
         * @constant
         * @type {String} templatePattern
         */
        templatePattern: /(^|.|\r|\n)({{(\w+)}})/,

        /**
         * @type {JSON} Array of elements ids to instantiate DOM collection
         */
        elementsIds: [],

        /**
         * @type {Array.<HTMLElement>} List of elements ids across all uploader functionality
         */
        elements: [],

        /**
         * @type {(FustyFlow|Flow)} Uploader object instance
         */
        uploader: {},

        /**
         * @type {JSON} General Uploader config
         */
        uploaderConfig: {},

        /**
         * @type {JSON} browseConfig General Uploader config
         */
        browseConfig: {},

        /**
         * @type {JSON} Misc settings to manipulate Uploader
         */
        miscConfig: {},

        /**
         * @type {Array.<String>} Sizes in plural
         */
        sizesPlural: ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],

        /**
         * @type {Number} Precision of calculation during convetion to human readable size format
         */
        sizePrecisionDefault: 3,

        /**
         * @type {Number} Unit type conversion kib or kb, etc
         */
        sizeUnitType: 1024,

        /**
         * @type {String} Default delete button selector
         */
        deleteButtonSelector: '.delete',

        /**
         * @type {Number} Timeout of completion handler
         */
        onCompleteTimeout: 1000,

        /**
         * @type {(null|Array.<FlowFile>)} Files array stored for success event
         */
        files: null,


        /**
         * @name Uploader
         *
         * @param {JSON} config
         *
         * @constructor
         */
        initialize: function(config) {
            this.elementsIds = config.elementIds;
            this.elements = this.getElements(this.elementsIds);

            this.uploaderConfig = config.uploaderConfig;
            this.browseConfig = config.browseConfig;
            this.miscConfig =  config.miscConfig;

            this.uploader = flowFactory(this.uploaderConfig);

            this.attachEvents();

            /**
             * Bridging functions to retain functionality of existing modules
             */
            this.formatSize = this._getPluralSize.bind(this);
            this.upload = this.onUploadClick.bind(this);
            this.onContainerHideBefore = this.onTabChange.bind(this);
        },

        /**
         * Array of strings containing elements ids
         *
         * @param {JSON.<string, Array.<string>>} ids as JSON map,
         *      {<type> => ['id1', 'id2'...], <type2>...}
         * @returns {Array.<HTMLElement>} An array of DOM elements
         */
        getElements: function (ids) {
            /** @type {Hash} idsHash */
            var idsHash = $H(ids);

            idsHash.each(function (id) {
                var result = this.getElementsByIds(id.value);

                idsHash.set(id.key, result);
            }.bind(this));

            return idsHash.toObject();
        },

        /**
         * Get HTMLElement from hash values
         *
         * @param {(Array|String)}ids
         * @returns {(Array.<HTMLElement>|HTMLElement)}
         */
        getElementsByIds: function (ids) {
            var result = [];
            if(ids && Object.isArray(ids)) {
                ids.each(function(fromId) {
                    var DOMElement = $(fromId);

                    if (DOMElement) {
                        // Add it only if it's valid HTMLElement, otherwise skip.
                        result.push(DOMElement);
                    }
                });
            } else {
                result = $(ids);
            }

            return result;
        },

        /**
         * Attach all types of events
         */
        attachEvents: function() {
            this.assignBrowse();

            this.uploader.on('filesSubmitted', this.onFilesSubmitted.bind(this));

            this.uploader.on('uploadStart', this.onUploadStart.bind(this));

            this.uploader.on('fileSuccess', this.onFileSuccess.bind(this));
            this.uploader.on('complete', this.onSuccess.bind(this));

            if(this.elements.container && !this.elements.delete) {
                this.elements.container.on('click', this.deleteButtonSelector, this.onDeleteClick.bind(this));
            } else {
                if(this.elements.delete) {
                    this.elements.delete.on('click', Event.fire.bind(this, document, 'upload:simulateDelete', {
                        containerId: this.elementsIds.container
                    }));
                }
            }
            if(this.elements.upload) {
                this.elements.upload.invoke('on', 'click', this.onUploadClick.bind(this));
            }
            if(this.debug) {
                this.uploader.on('catchAll', this.onCatchAll.bind(this));
            }
        },

        onTabChange: function (successFunc) {
            if(this.uploader.files.length && !Object.isArray(this.files)) {
                if(confirm(
                        this._translate('There are files that were selected but not uploaded yet. After switching to another tab your selections will be lost. Do you wish to continue ?')
                   )
                ) {
                    if(Object.isFunction(successFunc)) {
                        successFunc();
                    } else {
                        this._handleDelete(this.uploader.files);
                        document.fire('uploader:fileError', {
                            containerId: this.elementsIds.container
                        });
                    }
                } else {
                    return 'cannotchange';
                }
            }
        },

        /**
         * Assign browse buttons to appropriate targets
         */
        assignBrowse: function() {
            if (this.elements.browse && this.elements.browse.length) {
                this.uploader.assignBrowse(
                    this.elements.browse,
                    this.browseConfig.isDirectory || false,
                    this.browseConfig.singleFile || false,
                    this.browseConfig.attributes || {}
                );
            }
        },

        /**
         * @event
         * @param {Array.<FlowFile>} files
         */
        onFilesSubmitted: function (files) {
            files.filter(function (file) {
                if(this._checkFileSize(file)) {
                    alert(
                        this._translate('Maximum allowed file size for upload is') +
                        " " + this.miscConfig.maxSizePlural + "\n" +
                        this._translate('Please check your server PHP settings.')
                    );
                    file.cancel();
                    return false;
                }
                return true;
            }.bind(this)).each(function (file) {
                this._handleUpdateFile(file);
            }.bind(this));
        },

        _handleUpdateFile: function (file) {
            var replaceBrowseWithRemove = this.miscConfig.replaceBrowseWithRemove;
            if(replaceBrowseWithRemove) {
                document.fire('uploader:simulateNewUpload', { containerId: this.elementsIds.container });
            }
            this.elements.container
                [replaceBrowseWithRemove ? 'update':'insert'](this._renderFromTemplate(
                    this.elements.templateFile,
                    {
                        name: file.name,
                        size: file.size ? '(' + this._getPluralSize(file.size) + ')' : '',
                        id: file.uniqueIdentifier
                    }
                )
            );
        },

        /**
         * Upload button is being pressed
         *
         * @event
         */
        onUploadStart: function () {
            var files = this.uploader.files;

            files.each(function (file) {
                var id = file.uniqueIdentifier;

                this._getFileContainerById(id)
                    .removeClassName('new')
                    .removeClassName('error')
                    .addClassName('progress');
                this._getProgressTextById(id).update(this._translate('Uploading...'));

                var deleteButton = this._getDeleteButtonById(id);
                if(deleteButton) {
                    this._getDeleteButtonById(id).hide();
                }
            }.bind(this));

            this.files = this.uploader.files;
        },

        /**
         * Get file-line container by id
         *
         * @param {String} id
         * @returns {HTMLElement}
         * @private
         */
        _getFileContainerById: function (id) {
            return $(id + '-container');
        },

        /**
         * Get text update container
         *
         * @param id
         * @returns {*}
         * @private
         */
        _getProgressTextById: function (id) {
            return this._getFileContainerById(id).down('.progress-text');
        },

        _getDeleteButtonById: function(id) {
            return this._getFileContainerById(id).down('.delete');
        },

        /**
         * Handle delete button click
         *
         * @event
         * @param {Event} e
         */
        onDeleteClick: function (e) {
            var element = Event.findElement(e);
            var id = element.id;
            if(!id) {
                id = element.up(this.deleteButtonSelector).id;
            }
            this._handleDelete([this.uploader.getFromUniqueIdentifier(id)]);
        },

        /**
         * Complete handler of uploading process
         *
         * @event
         */
        onSuccess: function () {
            document.fire('uploader:success', { files: this.files });
            this.files = null;
        },

        /**
         * Successfully uploaded file, notify about that other components, handle deletion from queue
         *
         * @param {FlowFile} file
         * @param {JSON} response
         */
        onFileSuccess: function (file, response) {
            response = response.evalJSON();
            var id = file.uniqueIdentifier;
            var error = response.error;
            this._getFileContainerById(id)
                .removeClassName('progress')
                .addClassName(error ? 'error': 'complete')
            ;
            this._getProgressTextById(id).update(this._translate(
                error ? this._XSSFilter(error) :'Complete'
            ));

            setTimeout(function() {
                if(!error) {
                    document.fire('uploader:fileSuccess', {
                        response: Object.toJSON(response),
                        containerId: this.elementsIds.container
                    });
                } else {
                    document.fire('uploader:fileError', {
                        containerId: this.elementsIds.container
                    });
                }
                this._handleDelete([file]);
            }.bind(this) , !error ? this.onCompleteTimeout: this.onCompleteTimeout * 3);
        },

        /**
         * Upload button click event
         *
         * @event
         */
        onUploadClick: function () {
            try {
                this.uploader.upload();
            } catch(e) {
                if(console) {
                    console.error(e);
                }
            }
        },

        /**
         * Event for debugging purposes
         *
         * @event
         */
        onCatchAll: function () {
            if(console.group && console.groupEnd && console.trace) {
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
        },

        /**
         * Handle deletition of files
         * @param {Array.<FlowFile>} files
         * @private
         */
        _handleDelete: function (files) {
            files.each(function (file) {
                file.cancel();
                var container = $(file.uniqueIdentifier + '-container');
                if(container) {
                    container.remove();
                }
            }.bind(this));
        },

        /**
         * Check whenever file size exceeded permitted amount
         *
         * @param {FlowFile} file
         * @returns {boolean}
         * @private
         */
        _checkFileSize: function (file) {
            return file.size > this.miscConfig.maxSizeInBytes;
        },

        /**
         * Make a translation of string
         *
         * @param {String} text
         * @returns {String}
         * @private
         */
        _translate: function (text) {
            try {
                return Translator.translate(text);
            }
            catch(e){
                return text;
            }
        },

        /**
         * Render from given template and given variables to assign
         *
         * @param {HTMLElement} template
         * @param {JSON} vars
         * @returns {String}
         * @private
         */
        _renderFromTemplate: function (template, vars) {
            var t = new Template(this._XSSFilter(template.innerHTML), this.templatePattern);
            return t.evaluate(vars);
        },

        /**
         * Format size with precision
         *
         * @param {Number} sizeInBytes
         * @param {Number} [precision]
         * @returns {String}
         * @private
         */
        _getPluralSize: function (sizeInBytes, precision) {
                if(sizeInBytes == 0) {
                    return 0 + this.sizesPlural[0];
                }
                var dm = (precision || this.sizePrecisionDefault) + 1;
                var i = Math.floor(Math.log(sizeInBytes) / Math.log(this.sizeUnitType));

                return (sizeInBytes / Math.pow(this.sizeUnitType, i)).toPrecision(dm) + ' ' + this.sizesPlural[i];
        },

        /**
         * Purify template string to prevent XSS attacks
         *
         * @param {String} str
         * @returns {String}
         * @private
         */
        _XSSFilter: function (str) {
            return str
                .stripScripts()
                // Remove inline event handlers like onclick, onload, etc
                .replace(/(on[a-z]+=["][^"]+["])(?=[^>]*>)/img, '')
                .replace(/(on[a-z]+=['][^']+['])(?=[^>]*>)/img, '')
            ;
        }
    });
})(fustyFlowFactory, window, document);
