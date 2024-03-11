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
class CentinelAuthenticate {
    constructor(blockId, iframeId) {
        this._isAuthenticationStarted = false;
        this._relatedBlocks = [];
        this.centinelBlockId = blockId;
        this.iframeId = iframeId;

        if (this._isCentinelBlocksLoaded()) {
            document.getElementById(this.centinelBlockId).style.display = 'none';
        }
    }

    isAuthenticationStarted() {
        return this._isAuthenticationStarted;
    }

    addRelatedBlock(blockId) {
        this._relatedBlocks.push(blockId);
    }

    _hideRelatedBlocks() {
        for (const blockId of this._relatedBlocks) {
            document.getElementById(blockId).style.display = 'none';
        }
    }

    _showRelatedBlocks() {
        for (const blockId of this._relatedBlocks) {
            document.getElementById(blockId).style.display = 'block';
        }
    }

    _isRelatedBlocksLoaded() {
        return this._relatedBlocks.every(blockId => document.getElementById(blockId));
    }

    _isCentinelBlocksLoaded() {
        return document.getElementById(this.centinelBlockId) && document.getElementById(this.iframeId);
    }

    start(authenticateUrl) {
        if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
            this._hideRelatedBlocks();
            document.getElementById(this.iframeId).src = authenticateUrl;
            document.getElementById(this.centinelBlockId).style.display = 'block';
            this._isAuthenticationStarted = true;
        }
    }

    success() {
        if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
            this._showRelatedBlocks();
            document.getElementById(this.centinelBlockId).style.display = 'none';
            this._isAuthenticationStarted = false;
        }
    }

    cancel() {
        if (this._isAuthenticationStarted) {
            if (this._isRelatedBlocksLoaded()) {
                this._showRelatedBlocks();
            }
            if (this._isCentinelBlocksLoaded()) {
                document.getElementById(this.centinelBlockId).style.display = 'none';
                document.getElementById(this.iframeId).src = '';
            }
            this._isAuthenticationStarted = false;
        }
    }
};