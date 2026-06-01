/**
 * @copyright  For copyright and license information, read the README.md file.
 * @link       /README.md
 * @license    Academic Free License (AFL 3.0)
 * @package     base_default
 */

var GiftMessage = function(buttonId) {
    GiftMessageStack.addObject(this);
    this.buttonId = buttonId;
    this.initListeners();
};

GiftMessage.prototype = {
    uniqueId: 0,
    editGiftMessage: function (evt) {
        var popUpUrl = this.url + '?uniqueId=' + this.uniqueId;
        this.popUp = window.open(popUpUrl, 'giftMessage', 'width=350,height=400,resizable=yes,scrollbars=yes');
        this.popUp.focus();
        evt.preventDefault();
        evt.stopPropagation();
    },
    initListeners: function () {
        var items = document.getElementById(this.buttonId).querySelectorAll('.listen-for-click');
        var self = this;
        items.forEach(function(item) {
           item.addEventListener('click', self.editGiftMessage.bind(self));
           item.controller = self;
        });
    },
    reloadContainer: function (url) {
        var self = this;
        fetch(url).then(function(resp) { return resp.text(); }).then(function(html) {
            document.getElementById(self.buttonId).innerHTML = html;
            self.initListeners();
        });
    },
    initWindow: function (windowObject) {
        this.windowObj = windowObject;
    }
};

var GiftMessageStack = {
    _stack: [],
    _nextUniqueId: 0,
    addObject: function(giftMessageObject) {
       giftMessageObject.uniqueId = this.uniqueId();
       this._stack.push(giftMessageObject);
       return this;
    },
    uniqueId: function() {
        return 'objectStack' + (this._nextUniqueId++);
    },
    getObjectById: function(id) {
        var giftMessageObject = false;
        this._stack.forEach(function(item){
           if(item.uniqueId == id) {
               giftMessageObject = item;
           }
        });
        return giftMessageObject;
    }
};

var GiftMessageWindow = function(uniqueId, formId, removeUrl) {
    this.uniqueId = uniqueId;
    this.removeUrl = removeUrl;
    if(window.opener) {
        this.parentObject = window.opener.GiftMessageStack.getObjectById(this.uniqueId);
        this.parentObject.initWindow(this);
    }
    if(formId) {
        this.form = new VarienForm(formId, true);
        this.formElement = document.getElementById(formId);
        this.initListeners();
    }
};

GiftMessageWindow.prototype = {
    initListeners: function() {
        var self = this;
        var removeButtons = this.formElement.querySelectorAll('.listen-remove');
        removeButtons.forEach(function(item){
            item.addEventListener('click', self.remove.bind(self));
        });

        var cancelButtons = this.formElement.querySelectorAll('.listen-cancel');
        cancelButtons.forEach(function(item){
            item.addEventListener('click', self.cancel.bind(self));
        });
    },
    cancel: function(evt)  {
        evt.preventDefault();
        evt.stopPropagation();
        window.opener.focus();
        window.close();
    },
    close: function()  {
        window.opener.focus();
        window.close();
    },
    remove: function(evt)  {
        evt.preventDefault();
        evt.stopPropagation();
        if(this.confirmMessage && !window.confirm(this.confirmMessage)) {
            return;
        }
        window.location.href = this.removeUrl;
    },
    updateParent: function (url, buttonUrl) {
        if(this.parentObject) {
            this.parentObject.url = url;
            this.parentObject.reloadContainer(buttonUrl);
        }
        setTimeout(function(){
            window.opener.focus();
            window.close();
        }, 3000);
    }
};
