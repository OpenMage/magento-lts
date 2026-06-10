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
 * @copyright   Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

function popWin(url,win,para) {
    var win = window.open(url,win,para);
    win.focus();
}

function setLocation(url){
    window.location.href = encodeURI(url);
}

function setPLocation(url, setFocus){
    if( setFocus ) {
        window.opener.focus();
    }
    window.opener.location.href = encodeURI(url);
}

/**
 * Add classes to specified elements.
 * Supported classes are: 'odd', 'even', 'first', 'last'
 *
 * @param elements - array of elements to be decorated
 * [@param decorateParams] - array of classes to be set. If omitted, all available will be used
 * @deprecated
 */
function decorateGeneric(elements, decorateParams)
{
    var allSupportedParams = ['odd', 'even', 'first', 'last'];
    var _decorateParams = {};
    var total = elements.length;

    if (total) {
        if (typeof(decorateParams) == 'undefined') {
            decorateParams = allSupportedParams;
        }
        if (!decorateParams.length) {
            return;
        }
        for (var k in allSupportedParams) {
            _decorateParams[allSupportedParams[k]] = false;
        }
        for (var k in decorateParams) {
            _decorateParams[decorateParams[k]] = true;
        }

        if (_decorateParams.first) {
            elements[0].classList.add('first');
        }
        if (_decorateParams.last) {
            elements[total-1].classList.add('last');
        }
        for (var i = 0; i < total; i++) {
            if ((i + 1) % 2 == 0) {
                if (_decorateParams.even) {
                    elements[i].classList.add('even');
                }
            }
            else {
                if (_decorateParams.odd) {
                    elements[i].classList.add('odd');
                }
            }
        }
    }
}

/**
 * Decorate table rows and cells, tbody etc
 * @see decorateGeneric()
 * @deprecated
 */
function decorateTable(table, options) {
    if (typeof table === 'string') {
        table = document.getElementById(table);
    }
    if (table) {
        var _options = {
            'tbody'    : false,
            'tbody tr' : ['odd', 'even', 'first', 'last'],
            'thead tr' : ['first', 'last'],
            'tfoot tr' : ['first', 'last'],
            'tr td'    : ['last']
        };
        if (typeof(options) != 'undefined') {
            for (var k in options) {
                _options[k] = options[k];
            }
        }
        if (_options['tbody']) {
            decorateGeneric(table.querySelectorAll('tbody'), _options['tbody']);
        }
        if (_options['tbody tr']) {
            decorateGeneric(table.querySelectorAll('tbody tr'), _options['tbody tr']);
        }
        if (_options['thead tr']) {
            decorateGeneric(table.querySelectorAll('thead tr'), _options['thead tr']);
        }
        if (_options['tfoot tr']) {
            decorateGeneric(table.querySelectorAll('tfoot tr'), _options['tfoot tr']);
        }
        if (_options['tr td']) {
            var allRows = table.querySelectorAll('tr');
            if (allRows.length) {
                for (var i = 0; i < allRows.length; i++) {
                    decorateGeneric(allRows[i].getElementsByTagName('TD'), _options['tr td']);
                }
            }
        }
    }
}

/**
 * Set "odd", "even" and "last" CSS classes for list items
 * @see decorateGeneric()
 * @deprecated
 */
function decorateList(list, nonRecursive) {
    if (typeof list === 'string') {
        list = document.getElementById(list);
    }
    if (list) {
        var items;
        if (typeof(nonRecursive) == 'undefined') {
            items = list.querySelectorAll('li');
        } else {
            items = Array.prototype.slice.call(list.children);
        }
        decorateGeneric(items, ['odd', 'even', 'last']);
    }
}

/**
 * Set "odd", "even" and "last" CSS classes for list items
 * @see decorateGeneric()
 * @deprecated
 */
function decorateDataList(list) {
    if (typeof list === 'string') {
        list = document.getElementById(list);
    }
    if (list) {
        decorateGeneric(list.querySelectorAll('dt'), ['odd', 'even', 'last']);
        decorateGeneric(list.querySelectorAll('dd'), ['odd', 'even', 'last']);
    }
}

/**
 * Parse SID and produces the correct URL
 */
function parseSidUrl(baseUrl, urlExt) {
    var sidPos = baseUrl.indexOf('?');
    var sid = '';
    urlExt = (urlExt != undefined) ? urlExt : '';

    if (sidPos > -1) {
        sid = baseUrl.substring(sidPos);
        baseUrl = baseUrl.substring(0, sidPos);
    }

    return baseUrl+urlExt+sid;
}

/**
 * Formats currency using patern
 * format - JSON (pattern, decimal, decimalsDelimeter, groupsDelimeter)
 * showPlus - true (always show '+'or '-'),
 *      false (never show '-' even if number is negative)
 *      null (show '-' if number is negative)
 */

function formatCurrency(price, format, showPlus){
    var precision = isNaN(format.precision = Math.abs(format.precision)) ? 2 : format.precision;
    var requiredPrecision = isNaN(format.requiredPrecision = Math.abs(format.requiredPrecision)) ? 2 : format.requiredPrecision;

    //precision = (precision > requiredPrecision) ? precision : requiredPrecision;
    //for now we don't need this difference so precision is requiredPrecision
    precision = requiredPrecision;

    var integerRequired = isNaN(format.integerRequired = Math.abs(format.integerRequired)) ? 1 : format.integerRequired;

    var decimalSymbol = format.decimalSymbol == undefined ? "," : format.decimalSymbol;
    var groupSymbol = format.groupSymbol == undefined ? "." : format.groupSymbol;
    var groupLength = format.groupLength == undefined ? 3 : format.groupLength;

    var s = '';

    if (showPlus == undefined || showPlus == true) {
        s = price < 0 ? "-" : ( showPlus ? "+" : "");
    } else if (showPlus == false) {
        s = '';
    }

    var i = parseInt(price = Math.abs(+price || 0).toFixed(precision)) + "";
    var pad = (i.length < integerRequired) ? (integerRequired - i.length) : 0;
    while (pad) { i = '0' + i; pad--; }
    j = (j = i.length) > groupLength ? j % groupLength : 0;
    re = new RegExp("(\\d{" + groupLength + "})(?=\\d)", "g");

    /**
     * replace(/-/, 0) is only for fixing Safari bug which appears
     * when Math.abs(0).toFixed() executed on "0" number.
     * Result is "0.-0" :(
     */
    var r = (j ? i.substr(0, j) + groupSymbol : "") + i.substr(j).replace(re, "$1" + groupSymbol) + (precision ? decimalSymbol + Math.abs(price - i).toFixed(precision).replace(/-/, 0).slice(2) : "");
    var pattern = '';
    if (format.pattern.indexOf('{sign}') == -1) {
        pattern = s + format.pattern;
    } else {
        pattern = format.pattern.replace('{sign}', s);
    }

    return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function expandDetails(el, childClass) {
    if (typeof el === 'string') {
        el = document.getElementById(el);
    }
    if (el.classList.contains('show-details')) {
        document.querySelectorAll(childClass).forEach(function(item){
            item.style.display = 'none';
        });
        el.classList.remove('show-details');
    }
    else {
        document.querySelectorAll(childClass).forEach(function(item){
            item.style.display = '';
        });
        el.classList.add('show-details');
    }
}

/** @deprecated since 20.0.19 */
var isIE = false;

if (!window.Varien)
    var Varien = {};

Varien.showLoading = function(){
    var loader = document.getElementById('loading-process');
    if (loader) loader.style.display = '';
};
Varien.hideLoading = function(){
    var loader = document.getElementById('loading-process');
    if (loader) loader.style.display = 'none';
};
Varien.GlobalHandlers = {
    onCreate: function() {
        Varien.showLoading();
    },

    onComplete: function() {
        if(typeof Ajax !== 'undefined' && Ajax.activeRequestCount == 0) {
            Varien.hideLoading();
        }
    }
};

if (typeof Ajax !== 'undefined' && typeof Ajax.Responders !== 'undefined') {
    Ajax.Responders.register(Varien.GlobalHandlers);
}

/**
 * Quick Search form client model
 * @constructor
 */
Varien.searchForm = function(form, field, emptyText) {
    this.form = typeof form === 'string' ? document.getElementById(form) : form;
    this.field = typeof field === 'string' ? document.getElementById(field) : field;
    this.emptyText = emptyText;

    this.form.addEventListener('submit', this.submit.bind(this));
    this.field.addEventListener('focus', this.focus.bind(this));
    this.field.addEventListener('blur', this.blur.bind(this));
    this.blur();
};

Varien.searchForm.prototype = {
    submit: function(event){
        if (this.field.value == this.emptyText || this.field.value == ''){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
        return true;
    },

    focus: function(event){
        if(this.field.value==this.emptyText){
            this.field.value='';
        }
    },

    blur: function(event){
        if(this.field.value==''){
            this.field.value=this.emptyText;
        }
    },

    initAutocomplete: function(url, destinationElement){
        // Ajax.Autocompleter requires Scriptaculous controls.js
        // If available (shim or full), use it; otherwise no-op
        if (typeof Ajax !== 'undefined' && typeof Ajax.Autocompleter !== 'undefined') {
            new Ajax.Autocompleter(
                this.field,
                destinationElement,
                url,
                {
                    paramName: this.field.name,
                    method: 'get',
                    minChars: 2,
                    updateElement: this._selectAutocompleteItem.bind(this),
                    onShow: function(element, update) {
                        if(!update.style.position || update.style.position=='absolute') {
                            update.style.position = 'absolute';
                            if (typeof Position !== 'undefined') {
                                Position.clone(element, update, {
                                    setHeight: false,
                                    offsetTop: element.offsetHeight
                                });
                            }
                        }
                        update.style.display = '';
                    }
                }
            );
        }
    },

    _selectAutocompleteItem: function(element){
        if(element.title){
            this.field.value = element.title;
        }
        this.form.submit();
    }
};

/**
 * Tabs widget
 * @constructor
 */
Varien.Tabs = function(selector) {
    var links = document.querySelectorAll(selector + ' a');
    var self = this;
    links.forEach(function(el) { self.initTab(el); });
};

Varien.Tabs.prototype = {
    initTab: function(el) {
        el.href = 'javascript:void(0)';
        if (el.parentNode.classList.contains('active')) {
            this.showContent(el);
        }
        var self = this;
        el.addEventListener('click', function() { self.showContent(el); });
    },

    showContent: function(a) {
        var li = a.parentNode;
        var ul = li.parentNode;
        var items = ul.querySelectorAll('li, ol');
        items.forEach(function(el){
            var contents = document.getElementById(el.id + '_contents');
            if (el === li) {
                el.classList.add('active');
                if (contents) contents.style.display = '';
            } else {
                el.classList.remove('active');
                if (contents) contents.style.display = 'none';
            }
        });
    }
};

/**
 * Date element with validation
 * @constructor
 */
Varien.DateElement = function(type, content, required, format) {
    if (type == 'id') {
        this.day    = document.getElementById(content + 'day');
        this.month  = document.getElementById(content + 'month');
        this.year   = document.getElementById(content + 'year');
        this.full   = document.getElementById(content + 'full');
        this.advice = document.getElementById(content + 'date-advice');
    } else if (type == 'container') {
        this.day    = content.day;
        this.month  = content.month;
        this.year   = content.year;
        this.full   = content.full;
        this.advice = content.advice;
    } else {
        return;
    }

    this.required = required;
    this.format   = format;

    this.day.classList.add('validate-custom');
    this.day.validate = this.validate.bind(this);
    this.month.classList.add('validate-custom');
    this.month.validate = this.validate.bind(this);
    this.year.classList.add('validate-custom');
    this.year.validate = this.validate.bind(this);

    this.setDateRange(false, false);
    this.year.setAttribute('autocomplete','off');

    this.advice.style.display = 'none';

    var date = new Date;
    this.curyear = date.getFullYear();
};

Varien.DateElement.prototype = {
    validate: function() {
        var error = false,
            day   = parseInt(this.day.value, 10)   || 0,
            month = parseInt(this.month.value, 10) || 0,
            year  = parseInt(this.year.value, 10)  || 0;
        if (this.day.value.trim().length === 0
            && this.month.value.trim().length === 0
            && this.year.value.trim().length === 0
        ) {
            if (this.required) {
                error = 'This date is a required value.';
            } else {
                this.full.value = '';
            }
        } else if (!day || !month || !year) {
            error = 'Please enter a valid full date';
        } else {
            var date = new Date, countDaysInMonth = 0, errorType = null;
            date.setYear(year);date.setMonth(month-1);date.setDate(32);
            countDaysInMonth = 32 - date.getDate();
            if(!countDaysInMonth || countDaysInMonth>31) countDaysInMonth = 31;
            if(year < 1900) error = this.errorTextModifier(this.validateDataErrorText);

            if (day<1 || day>countDaysInMonth) {
                errorType = 'day';
                error = 'Please enter a valid day (1-%d).';
            } else if (month<1 || month>12) {
                errorType = 'month';
                error = 'Please enter a valid month (1-12).';
            } else {
                if(day % 10 == day) this.day.value = '0'+day;
                if(month % 10 == month) this.month.value = '0'+month;
                this.full.value = this.format.replace(/%[mb]/i, this.month.value).replace(/%[de]/i, this.day.value).replace(/%y/i, this.year.value);
                var testFull = this.month.value + '/' + this.day.value + '/'+ this.year.value;
                var test = new Date(testFull);
                if (isNaN(test)) {
                    error = 'Please enter a valid date.';
                } else {
                    this.setFullDate(test);
                }
            }
            var valueError = false;
            if (!error && !this.validateData()){
                errorType = this.validateDataErrorType;
                valueError = this.validateDataErrorText;
                error = valueError;
            }
        }

        if (error !== false) {
            try {
                error = Translator.translate(error);
            }
            catch (e) {}
            if (!valueError) {
                this.advice.innerHTML = error.replace('%d', countDaysInMonth);
            } else {
                this.advice.innerHTML = this.errorTextModifier(error);
            }
            this.advice.style.display = '';
            return false;
        }

        this.day.classList.remove('validation-failed');
        this.month.classList.remove('validation-failed');
        this.year.classList.remove('validation-failed');

        this.advice.style.display = 'none';
        return true;
    },
    validateData: function() {
        var year = this.fullDate.getFullYear();
        return (year>=1900 && year<=this.curyear);
    },
    validateDataErrorType: 'year',
    validateDataErrorText: 'Please enter a valid year (1900-%d).',
    errorTextModifier: function(text) {
        text = Translator.translate(text);
        return text.replace('%d', this.curyear);
    },
    setDateRange: function(minDate, maxDate) {
        this.minDate = minDate;
        this.maxDate = maxDate;
    },
    setFullDate: function(date) {
        this.fullDate = date;
    }
};

/**
 * DOB (Date of Birth) widget
 * @constructor
 */
Varien.DOB = function(selector, required, format) {
    var el = document.querySelector(selector);
    var container       = {};
    container.day       = el.querySelector('.dob-day input');
    container.month     = el.querySelector('.dob-month input');
    container.year      = el.querySelector('.dob-year input');
    container.full      = el.querySelector('.dob-full input');
    container.advice    = el.querySelector('.validation-advice');

    new Varien.DateElement('container', container, required, format);
};

/**
 * Date range validator
 * @constructor
 */
Varien.dateRangeDate = function() {};
Varien.dateRangeDate.prototype = Object.assign(Object.create(Varien.DateElement.prototype), {
    validateData: function() {
        var validate = true;
        if (this.minDate || this.maxValue) {
            if (this.minDate) {
                this.minDate = new Date(this.minDate);
                this.minDate.setHours(0);
                if (isNaN(this.minDate)) {
                    this.minDate = new Date('1/1/1900');
                }
                validate = validate && (this.fullDate >= this.minDate);
            }
            if (this.maxDate) {
                this.maxDate = new Date(this.maxDate);
                this.minDate.setHours(0);
                if (isNaN(this.maxDate)) {
                    this.maxDate = new Date();
                }
                validate = validate && (this.fullDate <= this.maxDate);
            }
            if (this.maxDate && this.minDate) {
                this.validateDataErrorText = 'Please enter a valid date between %s and %s';
            } else if (this.maxDate) {
                this.validateDataErrorText = 'Please enter a valid date less than or equal to %s';
            } else if (this.minDate) {
                this.validateDataErrorText = 'Please enter a valid date equal to or greater than %s';
            } else {
                this.validateDataErrorText = '';
            }
        }
        return validate;
    },
    validateDataErrorText: 'Date should be between %s and %s',
    errorTextModifier: function(text) {
        if (this.minDate) {
            text = text.replace('%s', this.dateFormat(this.minDate));
        }
        if (this.maxDate) {
            text = text.replace('%s', this.dateFormat(this.maxDate));
        }
        return text;
    },
    dateFormat: function(date) {
        return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
    }
});

/**
 * File upload element
 * @constructor
 */
Varien.FileElement = function(id) {
    this.fileElement = document.getElementById(id);
    this.hiddenElement = document.getElementById(id + '_value');

    this.fileElement.addEventListener('change', this.selectFile.bind(this));
};

Varien.FileElement.prototype = {
    selectFile: function(event) {
        this.hiddenElement.value = this.fileElement.value;
    }
};

if (typeof Validation !== 'undefined') {
    Validation.addAllThese([
        ['validate-custom', ' ', function(v,elm) {
            return elm.validate();
        }]
    ]);
}

function truncateOptions() {
    document.querySelectorAll('.truncated').forEach(function(element){
        element.addEventListener('mouseover', function(){
            var fullValue = element.querySelector('div.truncated_full_value');
            if (fullValue) {
                fullValue.classList.add('show');
            }
        });
        element.addEventListener('mouseout', function(){
            var fullValue = element.querySelector('div.truncated_full_value');
            if (fullValue) {
                fullValue.classList.remove('show');
            }
        });
    });
}
window.addEventListener('load', function(){
   truncateOptions();
});

/**
 * getInnerText — added to HTMLElement.prototype for backward compat
 */
if (typeof HTMLElement !== 'undefined' && !HTMLElement.prototype.getInnerText) {
    HTMLElement.prototype.getInnerText = function() {
        if (this.innerText) {
            return this.innerText;
        }
        // Strip scripts, decode entities, normalize whitespace
        var html = this.innerHTML.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        return (tmp.textContent || '').replace(/[\n\r\s]+/g, ' ').trim();
    };
}

/**
 * Executes event handler on the element. Works with event handlers attached by Prototype,
 * in a browser-agnostic fashion.
 * @param element The element object
 * @param event Event name, like 'change'
 *
 * @example fireEvent($('my-input', 'click'));
 */
function fireEvent(element, event) {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent(event, true, true );
    return element.dispatchEvent(evt);
}

/**
 * Returns more accurate results of floating-point modulo division
 * E.g.:
 * 0.6 % 0.2 = 0.19999999999999996
 * modulo(0.6, 0.2) = 0
 *
 * @param dividend
 * @param divisor
 */
function modulo(dividend, divisor)
{
    var epsilon = divisor / 10000;
    var remainder = dividend % divisor;

    if (Math.abs(remainder - divisor) < epsilon || Math.abs(remainder) < epsilon) {
        remainder = 0;
    }

    return remainder;
}

/**
 * Create form element. Set parameters into it and send
 * @constructor
 */
Varien.formCreator = function(url, parametersArray, method) {
    this.url = url;
    this.parametersArray = JSON.parse(parametersArray);
    this.method = method;
    this.form = '';

    this.createForm();
    this.setFormData();
};

Varien.formCreator.prototype = {
    createForm: function() {
        this.form = document.createElement('form');
        this.form.method = this.method;
        this.form.action = this.url;
    },
    setFormData: function() {
        for (var key in this.parametersArray) {
            if (this.parametersArray.hasOwnProperty(key)) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = this.parametersArray[key];
                this.form.appendChild(input);
            }
        }
    }
};

function customFormSubmit(url, parametersArray, method) {
    var createdForm = new Varien.formCreator(url, parametersArray, method);
    document.body.appendChild(createdForm.form);
    createdForm.form.submit();
}

function customFormSubmitToParent(url, parametersArray, method) {
    var params = JSON.parse(parametersArray);
    var formData = new FormData();
    Object.keys(params).forEach(function(key) {
        formData.append(key, params[key]);
    });

    fetch(url, {
        method: method,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.text(); })
    .then(function(text) {
        var node = document.createElement('div');
        node.innerHTML = text;
        var responseMessage = node.getElementsByClassName('messages')[0];
        var pageTitle = window.document.body.getElementsByClassName('page-title')[0];
        if (pageTitle && responseMessage) {
            pageTitle.insertAdjacentHTML('afterend', responseMessage.outerHTML);
        }
        window.opener.focus();
        window.opener.location.href = url;
    });
}

function buttonDisabler() {
    var buttons = document.querySelectorAll('button.save');
    buttons.forEach(function(button) {
        button.disabled = true;
    });
}
