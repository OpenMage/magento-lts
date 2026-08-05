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
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function CentinelAuthenticate(blockId, iframeId) {
    this._isAuthenticationStarted = false;
    this._relatedBlocks = [];
    this.centinelBlockId = blockId;
    this.iframeId = iframeId;
    if (this._isCentinelBlocksLoaded()) {
        document.getElementById(this.centinelBlockId).style.display = 'none';
    }
}

CentinelAuthenticate.prototype.isAuthenticationStarted = function() {
    return this._isAuthenticationStarted;
};

CentinelAuthenticate.prototype.addRelatedBlock = function(blockId) {
    this._relatedBlocks[this._relatedBlocks.length] = blockId;
};

CentinelAuthenticate.prototype._hideRelatedBlocks = function() {
    for (var i = 0; i < this._relatedBlocks.length; i++) {
        document.getElementById(this._relatedBlocks[i]).style.display = 'none';
    }
};

CentinelAuthenticate.prototype._showRelatedBlocks = function() {
    for (var i = 0; i < this._relatedBlocks.length; i++) {
        document.getElementById(this._relatedBlocks[i]).style.display = '';
    }
};

CentinelAuthenticate.prototype._isRelatedBlocksLoaded = function() {
    for (var i = 0; i < this._relatedBlocks.length; i++) {
        if (!document.getElementById(this._relatedBlocks[i])) {
            return false;
        }
    }
    return true;
};

CentinelAuthenticate.prototype._isCentinelBlocksLoaded = function() {
    if (!document.getElementById(this.centinelBlockId) || !document.getElementById(this.iframeId)) {
        return false;
    }
    return true;
};

CentinelAuthenticate.prototype.start = function(authenticateUrl) {
    if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
        this._hideRelatedBlocks();
        document.getElementById(this.iframeId).src = authenticateUrl;
        document.getElementById(this.centinelBlockId).style.display = '';
        this._isAuthenticationStarted = true;
    }
};

CentinelAuthenticate.prototype.success = function() {
    if (this._isRelatedBlocksLoaded() && this._isCentinelBlocksLoaded()) {
        this._showRelatedBlocks();
        document.getElementById(this.centinelBlockId).style.display = 'none';
        this._isAuthenticationStarted = false;
    }
};

CentinelAuthenticate.prototype.cancel = function() {
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
};
