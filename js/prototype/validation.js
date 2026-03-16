/*
* Really easy field validation with Prototype
* http://tetlaw.id.au/view/javascript/really-easy-field-validation
* Andrew Tetlaw
* Version 1.5.4.1 (2007-01-05)
*
* Copyright (c) 2007 Andrew Tetlaw
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use, copy,
* modify, merge, publish, distribute, sublicense, and/or sell copies
* of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
* BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
* ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
* CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*
*/

// ---- Internal helpers (not exposed globally) ----

function _camelize(str) {
    return str.replace(/-([a-z])/g, function(m, c) { return c.toUpperCase(); });
}

function _getFieldValue(el) {
    if (typeof el === 'string') { el = document.getElementById(el); }
    if (!el) { return ''; }
    var tag = el.tagName.toLowerCase();
    if (tag === 'select') {
        var idx = el.selectedIndex;
        return (idx >= 0) ? el.options[idx].value : '';
    }
    return el.value || '';
}

function _isVisible(el) {
    if (typeof el === 'string') { el = document.getElementById(el); }
    if (!el) { return false; }
    return !(el.offsetWidth === 0 && el.offsetHeight === 0) && el.style.display !== 'none';
}

function _splitClassName(cn) {
    return (cn || '').split(/\s+/).filter(function(s) { return s.length > 0; });
}

// ---- Validator ----

var Validator = function(className, error, test, options) {
    if (typeof test === 'function') {
        this.options = Object.assign({}, options || {});
        this._test = test;
    } else {
        this.options = Object.assign({}, test || {});
        this._test = function() { return true; };
    }
    this.error = error || 'Validation failed.';
    this.className = className;
};

Validator.prototype.test = function(v, elm) {
    var self = this;
    return this._test(v, elm) && Object.keys(this.options).every(function(key) {
        return Validator.methods[key] ? Validator.methods[key](v, elm, self.options[key]) : true;
    });
};

Validator.methods = {
    pattern : function(v, elm, opt) { return Validation.get('IsEmpty').test(v) || opt.test(v); },
    minLength : function(v, elm, opt) { return v.length >= opt; },
    maxLength : function(v, elm, opt) { return v.length <= opt; },
    min : function(v, elm, opt) { return v >= parseFloat(opt); },
    max : function(v, elm, opt) { return v <= parseFloat(opt); },
    notOneOf : function(v, elm, opt) {
        return Array.from(opt).every(function(value) { return v != value; });
    },
    oneOf : function(v, elm, opt) {
        return Array.from(opt).some(function(value) { return v == value; });
    },
    is : function(v, elm, opt) { return v == opt; },
    isNot : function(v, elm, opt) { return v != opt; },
    equalToField : function(v, elm, opt) { return v == _getFieldValue(opt); },
    notEqualToField : function(v, elm, opt) { return v != _getFieldValue(opt); },
    include : function(v, elm, opt) {
        return Array.from(opt).every(function(value) {
            return Validation.get(value).test(v, elm);
        });
    }
};

// ---- Validation ----

var Validation = function(form, options) {
    if (typeof form === 'string') { form = document.getElementById(form); }
    this.form = form;
    if (!this.form) {
        return;
    }
    this.options = Object.assign({
        onSubmit : Validation.defaultOptions.onSubmit,
        stopOnFirst : Validation.defaultOptions.stopOnFirst,
        immediate : Validation.defaultOptions.immediate,
        focusOnError : Validation.defaultOptions.focusOnError,
        useTitles : Validation.defaultOptions.useTitles,
        onFormValidate : Validation.defaultOptions.onFormValidate,
        onElementValidate : Validation.defaultOptions.onElementValidate
    }, options || {});
    if (this.options.onSubmit) {
        this.form.addEventListener('submit', this.onSubmit.bind(this), false);
    }
    if (this.options.immediate) {
        var self = this;
        Array.from(this.form.elements).forEach(function(input) {
            if (input.tagName.toLowerCase() === 'select') {
                input.addEventListener('blur', self.onChange.bind(self));
            }
            if (input.type.toLowerCase() === 'radio' || input.type.toLowerCase() === 'checkbox') {
                input.addEventListener('click', self.onChange.bind(self));
            } else {
                input.addEventListener('change', self.onChange.bind(self));
            }
        });
    }
};

Validation.defaultOptions = {
    onSubmit : true,
    stopOnFirst : false,
    immediate : false,
    focusOnError : true,
    useTitles : false,
    addClassNameToContainer: false,
    containerClassName: '.input-box',
    onFormValidate : function(result, form) {},
    onElementValidate : function(result, elm) {}
};

Validation.prototype.onChange = function(ev) {
    Validation.isOnChange = true;
    Validation.validate(ev.target, {
        useTitle : this.options.useTitles,
        onElementValidate : this.options.onElementValidate
    });
    Validation.isOnChange = false;
};

Validation.prototype.onSubmit = function(ev) {
    if (!this.validate()) {
        ev.preventDefault();
        ev.stopPropagation();
    }
};

Validation.prototype.validate = function() {
    var result = false;
    var useTitles = this.options.useTitles;
    var callback = this.options.onElementValidate;
    var self = this;
    try {
        var elements = Array.from(this.form.elements);
        if (this.options.stopOnFirst) {
            result = elements.every(function(elm) {
                if (elm.classList.contains('local-validation') && !self.isElementInForm(elm, self.form)) {
                    return true;
                }
                return Validation.validate(elm, {useTitle : useTitles, onElementValidate : callback});
            });
        } else {
            result = elements.map(function(elm) {
                if (elm.classList.contains('local-validation') && !self.isElementInForm(elm, self.form)) {
                    return true;
                }
                return Validation.validate(elm, {useTitle : useTitles, onElementValidate : callback});
            }).every(function(v) { return v; });
        }
    } catch (e) {
    }
    if (!result && this.options.focusOnError) {
        try {
            var failed = Array.from(this.form.elements).filter(function(elm) {
                return elm.classList.contains('validation-failed');
            });
            if (failed.length) { failed[0].focus(); }
        } catch(e) {
        }
    }
    this.options.onFormValidate(result, this.form);
    return result;
};

Validation.prototype.reset = function() {
    Array.from(this.form.elements).forEach(Validation.reset);
};

Validation.prototype.isElementInForm = function(elm, form) {
    var domForm = elm.closest('form');
    return domForm === form;
};

// ---- Validation static methods ----

Validation.validate = function(elm, options) {
    options = Object.assign({
        useTitle : false,
        onElementValidate : function(result, elm) {}
    }, options || {});
    if (typeof elm === 'string') { elm = document.getElementById(elm); }

    var cn = _splitClassName(elm.className);
    return cn.every(function(value) {
        var test = Validation.test(value, elm, options.useTitle);
        options.onElementValidate(test, elm);
        return test;
    });
};

Validation.insertAdvice = function(elm, advice) {
    if (typeof elm === 'string') { elm = document.getElementById(elm); }
    var container = elm.closest('.field-row');
    if (container) {
        container.insertAdjacentHTML('afterend', advice);
    } else if (elm.closest('td.value')) {
        elm.closest('td.value').insertAdjacentHTML('beforeend', advice);
    } else if (elm.advaiceContainer && document.getElementById(elm.advaiceContainer)) {
        document.getElementById(elm.advaiceContainer).innerHTML = advice;
    } else {
        switch (elm.type.toLowerCase()) {
            case 'checkbox':
            case 'radio':
                var p = elm.parentNode;
                if (p) {
                    p.insertAdjacentHTML('beforeend', advice);
                } else {
                    elm.insertAdjacentHTML('afterend', advice);
                }
                break;
            default:
                elm.insertAdjacentHTML('afterend', advice);
        }
    }
};

Validation.showAdvice = function(elm, advice, adviceName) {
    if (!elm.advices) {
        elm.advices = {};
    } else {
        var self = this;
        Object.keys(elm.advices).forEach(function(key) {
            var pair = elm.advices[key];
            if (!advice || pair.id != advice.id) {
                self.hideAdvice(elm, pair);
            }
        });
    }
    elm.advices[adviceName] = advice;
    advice.style.display = 'block';
};

Validation.hideAdvice = function(elm, advice) {
    if (advice != null) {
        advice.style.display = 'none';
    }
};

Validation.updateCallback = function(elm, status) {
    if (typeof elm.callbackFunction != 'undefined') {
        eval(elm.callbackFunction+'(\''+elm.id+'\',\''+status+'\')');
    }
};

Validation.ajaxError = function(elm, errorMsg) {
    var name = 'validate-ajax';
    var advice = Validation.getAdvice(name, elm);
    if (advice == null) {
        advice = this.createAdvice(name, elm, false, errorMsg);
    }
    this.showAdvice(elm, advice, 'validate-ajax');
    this.updateCallback(elm, 'failed');

    elm.classList.add('validation-failed');
    elm.classList.add('validate-ajax');
    if (Validation.defaultOptions.addClassNameToContainer && Validation.defaultOptions.containerClassName != '') {
        var container = elm.closest(Validation.defaultOptions.containerClassName);
        if (container && this.allowContainerClassName(elm)) {
            container.classList.remove('validation-passed');
            container.classList.add('validation-error');
        }
    }
};

Validation.allowContainerClassName = function(elm) {
    if (elm.type == 'radio' || elm.type == 'checkbox') {
        return elm.classList.contains('change-container-classname');
    }
    return true;
};

Validation.test = function(name, elm, useTitle) {
    var v = Validation.get(name);
    var prop = '__advice' + _camelize(name);
    try {
        if (Validation.isVisible(elm) && !v.test(_getFieldValue(elm), elm)) {
            var advice = Validation.getAdvice(name, elm);
            if (advice == null) {
                advice = this.createAdvice(name, elm, useTitle);
            }
            this.showAdvice(elm, advice, name);
            this.updateCallback(elm, 'failed');
            elm[prop] = 1;
            if (!elm.advaiceContainer) {
                elm.classList.remove('validation-passed');
                elm.classList.add('validation-failed');
            }

            if (Validation.defaultOptions.addClassNameToContainer && Validation.defaultOptions.containerClassName != '') {
                var container = elm.closest(Validation.defaultOptions.containerClassName);
                if (container && this.allowContainerClassName(elm)) {
                    container.classList.remove('validation-passed');
                    container.classList.add('validation-error');
                }
            }
            return false;
        } else {
            var advice = Validation.getAdvice(name, elm);
            this.hideAdvice(elm, advice);
            this.updateCallback(elm, 'passed');
            elm[prop] = '';
            elm.classList.remove('validation-failed');
            elm.classList.add('validation-passed');
            if (Validation.defaultOptions.addClassNameToContainer && Validation.defaultOptions.containerClassName != '') {
                var container = elm.closest(Validation.defaultOptions.containerClassName);
                if (container && !container.querySelector('.validation-failed') && this.allowContainerClassName(elm)) {
                    if (!Validation.get('IsEmpty').test(elm.value) || !this.isVisible(elm)) {
                        container.classList.add('validation-passed');
                    } else {
                        container.classList.remove('validation-passed');
                    }
                    container.classList.remove('validation-error');
                }
            }
            return true;
        }
    } catch(e) {
        throw(e);
    }
};

Validation.isVisible = function(elm) {
    while (elm.tagName != 'BODY') {
        if (!_isVisible(elm)) return false;
        elm = elm.parentNode;
    }
    return true;
};

Validation.getAdvice = function(name, elm) {
    return document.getElementById('advice-' + name + '-' + Validation.getElmID(elm))
        || document.getElementById('advice-' + Validation.getElmID(elm));
};

Validation.createAdvice = function(name, elm, useTitle, customError) {
    var v = Validation.get(name);
    var errorMsg = useTitle ? ((elm && elm.title) ? elm.title : v.error) : v.error;
    if (customError) {
        errorMsg = customError;
    }
    try {
        if (Translator) {
            errorMsg = Translator.translate(errorMsg);
        }
    } catch(e) {}

    var advice = '<div class="validation-advice" id="advice-' + name + '-' + Validation.getElmID(elm) + '" style="display:none">' + errorMsg + '</div>';

    Validation.insertAdvice(elm, advice);
    advice = Validation.getAdvice(name, elm);
    if (elm.classList.contains('absolute-advice')) {
        var rect = elm.getBoundingClientRect();
        var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        advice._adviceTop = (rect.top + scrollTop + rect.height) + 'px';
        advice._adviceLeft = (rect.left + scrollLeft) + 'px';
        advice._adviceWidth = rect.width + 'px';
        advice._adviceAbsolutize = true;
    }
    return advice;
};

Validation.getElmID = function(elm) {
    return elm.id ? elm.id : elm.name;
};

Validation.reset = function(elm) {
    if (typeof elm === 'string') { elm = document.getElementById(elm); }
    var cn = _splitClassName(elm.className);
    cn.forEach(function(value) {
        var prop = '__advice' + _camelize(value);
        if (elm[prop]) {
            var advice = Validation.getAdvice(value, elm);
            if (advice) {
                advice.style.display = 'none';
            }
            elm[prop] = '';
        }
        elm.classList.remove('validation-failed');
        elm.classList.remove('validation-passed');
        if (Validation.defaultOptions.addClassNameToContainer && Validation.defaultOptions.containerClassName != '') {
            var container = elm.closest(Validation.defaultOptions.containerClassName);
            if (container) {
                container.classList.remove('validation-passed');
                container.classList.remove('validation-error');
            }
        }
    });
};

Validation.add = function(className, error, test, options) {
    var nv = {};
    nv[className] = new Validator(className, error, test, options);
    Object.assign(Validation.methods, nv);
};

Validation.addAllThese = function(validators) {
    var nv = {};
    validators.forEach(function(value) {
        nv[value[0]] = new Validator(value[0], value[1], value[2], (value.length > 3 ? value[3] : {}));
    });
    Object.assign(Validation.methods, nv);
};

Validation.get = function(name) {
    return Validation.methods[name] ? Validation.methods[name] : Validation.methods['_LikeNoIDIEverSaw_'];
};

Validation.methods = {
    '_LikeNoIDIEverSaw_' : new Validator('_LikeNoIDIEverSaw_', '', {})
};

Validation.add('IsEmpty', '', function(v) {
    return (v == '' || (v == null) || (v.length == 0) || /^\s+$/.test(v));
});

Validation.addAllThese([
    ['validate-no-html-tags', 'HTML tags are not allowed', function(v) {
				return !/<(\/)?\w+/.test(v);
			}],
	['validate-select', 'Please select an option.', function(v) {
                return ((v != "none") && (v != null) && (v.length != 0));
            }],
    ['required-entry', 'This is a required field.', function(v) {
                return !Validation.get('IsEmpty').test(v);
            }],
    ['validate-number', 'Please enter a valid number in this field.', function(v) {
                return Validation.get('IsEmpty').test(v)
                    || (!isNaN(parseNumber(v)) && /^\s*-?\d*(\.\d*)?\s*$/.test(v));
            }],
    ['validate-number-range', 'The value is not within the specified range.', function(v, elm) {
                if (Validation.get('IsEmpty').test(v)) {
                    return true;
                }

                var numValue = parseNumber(v);
                if (isNaN(numValue)) {
                    return false;
                }

                var reRange = /^number-range-(-?[\d.,]+)?-(-?[\d.,]+)?$/,
                    result = true;

                _splitClassName(elm.className).forEach(function(name) {
                    var m = reRange.exec(name);
                    if (m) {
                        result = result
                            && (m[1] == null || m[1] == '' || numValue >= parseNumber(m[1]))
                            && (m[2] == null || m[2] == '' || numValue <= parseNumber(m[2]));
                    }
                });

                return result;
            }],
    ['validate-digits', 'Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas.', function(v) {
                return Validation.get('IsEmpty').test(v) ||  !/[^\d]/.test(v);
            }],
    ['validate-digits-range', 'The value is not within the specified range.', function(v, elm) {
                if (Validation.get('IsEmpty').test(v)) {
                    return true;
                }

                var numValue = parseNumber(v);
                if (isNaN(numValue)) {
                    return false;
                }

                var reRange = /^digits-range-(-?\d+)?-(-?\d+)?$/,
                    result = true;

                _splitClassName(elm.className).forEach(function(name) {
                    var m = reRange.exec(name);
                    if (m) {
                        result = result
                            && (m[1] == null || m[1] == '' || numValue >= parseNumber(m[1]))
                            && (m[2] == null || m[2] == '' || numValue <= parseNumber(m[2]));
                    }
                });

                return result;
            }],
    ['validate-hex-color', 'Please enter a valid hexadecimal color. For example ff0000.', function (v) {
                return Validation.get('IsEmpty').test(v) ||  /^[a-f0-9]{6}$/i.test(v)
            }],
    ['validate-hex-color-hash', 'Please enter a valid hexadecimal color with hash. For example #ff0000.', function (v) {
                return Validation.get('IsEmpty').test(v) ||  /^#[a-f0-9]{6}$/i.test(v)
            }],
    ['validate-alpha', 'Please use letters only (a-z or A-Z) in this field.', function (v) {
                return Validation.get('IsEmpty').test(v) ||  /^[a-zA-Z]+$/.test(v)
            }],
    ['validate-code', 'Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.', function (v) {
                return Validation.get('IsEmpty').test(v) ||  /^[a-z]+[a-z0-9_]+$/.test(v)
            }],
    ['validate-code-event', 'Please do not use "event" for an attribute code.', function (v) {
        return Validation.get('IsEmpty').test(v) || !/^(event)$/.test(v)
            }],
    ['validate-alphanum', 'Please use only letters (a-z or A-Z) or numbers (0-9) only in this field. No spaces or other characters are allowed.', function(v) {
                return Validation.get('IsEmpty').test(v) || /^[a-zA-Z0-9]+$/.test(v)
            }],
    ['validate-alphanum-with-spaces', 'Please use only letters (a-z or A-Z), numbers (0-9) or spaces only in this field.', function(v) {
                    return Validation.get('IsEmpty').test(v) || /^[a-zA-Z0-9 ]+$/.test(v)
            }],
    ['validate-street', 'Please use only letters (a-z or A-Z) or numbers (0-9) or spaces and # only in this field.', function(v) {
                return Validation.get('IsEmpty').test(v) ||  /^[ \w]{3,}([A-Za-z]\.)?([ \w]*\#\d+)?(\r\n| )[ \w]{3,}/.test(v)
            }],
    ['validate-phoneStrict', 'Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.', function(v) {
                return Validation.get('IsEmpty').test(v) || /^(\()?\d{3}(\))?(-|\s)?\d{3}(-|\s)\d{4}$/.test(v);
            }],
    ['validate-phoneLax', 'Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.', function(v) {
                return Validation.get('IsEmpty').test(v) || /^((\d[-. ]?)?((\(\d{3}\))|\d{3}))?[-. ]?\d{3}[-. ]?\d{4}$/.test(v);
            }],
    ['validate-fax', 'Please enter a valid fax number. For example (123) 456-7890 or 123-456-7890.', function(v) {
                return Validation.get('IsEmpty').test(v) || /^(\()?\d{3}(\))?(-|\s)?\d{3}(-|\s)\d{4}$/.test(v);
            }],
    ['validate-date', 'Please enter a valid date.', function(v) {
                var test = new Date(v);
                return Validation.get('IsEmpty').test(v) || !isNaN(test);
            }],
    ['validate-date-range', 'The From Date value should be less than or equal to the To Date value.', function(v, elm) {
            var m = /\bdate-range-(\w+)-(\w+)\b/.exec(elm.className);
            if (!m || m[2] == 'to' || Validation.get('IsEmpty').test(v)) {
                return true;
            }

            var currentYear = new Date().getFullYear() + '';
            var normalizedTime = function(v) {
                v = v.split(/[.\/]/);
                if (v[2] && v[2].length < 4) {
                    v[2] = currentYear.substr(0, v[2].length) + v[2];
                }
                return new Date(v.join('/')).getTime();
            };

            var dependentElements = elm.form.querySelectorAll('.validate-date-range.date-range-' + m[1] + '-to');
            return !dependentElements.length || Validation.get('IsEmpty').test(dependentElements[0].value)
                || normalizedTime(v) <= normalizedTime(dependentElements[0].value);
        }],
    ['validate-email', 'Please enter a valid email address. For example johndoe@domain.com.', function (v) {
                //return Validation.get('IsEmpty').test(v) || /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/.test(v)
                //return Validation.get('IsEmpty').test(v) || /^[\!\#$%\*/?|\^\{\}`~&\'\+\-=_a-z0-9][\!\#$%\*/?|\^\{\}`~&\'\+\-=_a-z0-9\.]{1,30}[\!\#$%\*/?|\^\{\}`~&\'\+\-=_a-z0-9]@([a-z0-9_-]{1,30}\.){1,5}[a-z]{2,4}$/i.test(v)
                return Validation.get('IsEmpty').test(v) || /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v)
            }],
    ['validate-emailSender', 'Please use only visible characters and spaces.', function (v) {
                return Validation.get('IsEmpty').test(v) ||  /^[\S ]+$/.test(v)
                    }],
    ['validate-password', 'Please enter more characters or clean leading or trailing spaces.', function(v, elm) {
                var pass=v.trim(); /*strip leading and trailing spaces*/
                var reMin = new RegExp(/^min-pass-length-[0-9]+$/);
                var minLength = 7;
                _splitClassName(elm.className).forEach(function(name, index) {
                    if (name.match(reMin)) {
                        minLength = name.split('-')[3];
                    }
                });
                return (!(v.length > 0 && v.length < minLength) && v.length == pass.length);
            }],
    ['validate-admin-password', 'Please enter more characters. Password should contain both numeric and alphabetic characters.', function(v, elm) {
                var pass=v.trim();
                if (0 == pass.length) {
                    return true;
                }
                if (!(/[a-z]/i.test(v)) || !(/[0-9]/.test(v))) {
                    return false;
                }
                var reMin = new RegExp(/^min-admin-pass-length-[0-9]+$/);
                var minLength = 7;
                _splitClassName(elm.className).forEach(function(name, index) {
                    if (name.match(reMin)) {
                        minLength = name.split('-')[4];
                    }
                });
                return !(pass.length < minLength);
            }],
    ['validate-cpassword', 'Please make sure your passwords match.', function(v) {
                var conf = document.getElementById('confirmation') ? document.getElementById('confirmation') : document.querySelector('.validate-cpassword');
                var pass = false;
                if (document.getElementById('password')) {
                    pass = document.getElementById('password');
                }
                var passwordElements = document.querySelectorAll('.validate-password');
                for (var i = 0; i < passwordElements.length; i++) {
                    var passwordElement = passwordElements[i];
                    if (passwordElement.closest('form').id == conf.closest('form').id) {
                        pass = passwordElement;
                    }
                }
                if (document.querySelectorAll('.validate-admin-password').length) {
                    pass = document.querySelectorAll('.validate-admin-password')[0];
                }
                return (pass.value == conf.value);
            }],
    ['validate-both-passwords', 'Please make sure your passwords match.', function(v, input) {
                var dependentInput = input.form[input.name == 'password' ? 'confirmation' : 'password'],
                    isEqualValues  = input.value == dependentInput.value;

                if (isEqualValues && dependentInput.classList.contains('validation-failed')) {
                    Validation.test(this.className, dependentInput);
                }

                return dependentInput.value == '' || isEqualValues;
            }],
    ['validate-url', 'Please enter a valid URL. Protocol is required (http://, https:// or ftp://)', function (v) {
                v = (v || '').replace(/^\s+/, '').replace(/\s+$/, '');
                return Validation.get('IsEmpty').test(v) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(v)
            }],
    ['validate-clean-url', 'Please enter a valid URL. For example http://www.example.com or www.example.com', function (v) {
                return Validation.get('IsEmpty').test(v) || /^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i.test(v) || /^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i.test(v)
            }],
    ['validate-identifier', 'Please enter a valid URL Key. For example "example-page", "example-page.html" or "anotherlevel/example-page".', function (v) {
                return Validation.get('IsEmpty').test(v) || /^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/.test(v)
            }],
    ['validate-xml-identifier', 'Please enter a valid XML-identifier. For example something_1, block5, id-4.', function (v) {
                return Validation.get('IsEmpty').test(v) || /^[A-Z][A-Z0-9_\/-]*$/i.test(v)
            }],
    ['validate-ssn', 'Please enter a valid social security number. For example 123-45-6789.', function(v) {
            return Validation.get('IsEmpty').test(v) || /^\d{3}-?\d{2}-?\d{4}$/.test(v);
            }],
    ['validate-zip', 'Please enter a valid zip code. For example 90602 or 90602-1234.', function(v) {
            return Validation.get('IsEmpty').test(v) || /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(v);
            }],
    ['validate-zip-international', 'Please enter a valid zip code.', function(v) {
            //return Validation.get('IsEmpty').test(v) || /(^[A-z0-9]{2,10}([\s]{0,1}|[\-]{0,1})[A-z0-9]{2,10}$)/.test(v);
            return true;
            }],
    ['validate-date-au', 'Please use this date format: dd/mm/yyyy. For example 17/03/2006 for the 17th of March, 2006.', function(v) {
                if(Validation.get('IsEmpty').test(v)) return true;
                var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
                if(!regex.test(v)) return false;
                var d = new Date(v.replace(regex, '$2/$1/$3'));
                return ( parseInt(RegExp.$2, 10) == (1+d.getMonth()) ) &&
                            (parseInt(RegExp.$1, 10) == d.getDate()) &&
                            (parseInt(RegExp.$3, 10) == d.getFullYear() );
            }],
    ['validate-currency-dollar', 'Please enter a valid $ amount. For example $100.00.', function(v) {
                // [$]1[##][,###]+[.##]
                // [$]1###+[.##]
                // [$]0.##
                // [$].##
                return Validation.get('IsEmpty').test(v) ||  /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/.test(v)
            }],
    ['validate-one-required', 'Please select one of the above options.', function (v,elm) {
                var p = elm.parentNode;
                var options = p.getElementsByTagName('INPUT');
                return Array.from(options).some(function(el) {
                    return _getFieldValue(el);
                });
            }],
    ['validate-one-required-by-name', 'Please select one of the options.', function (v,elm) {
                var inputs = document.querySelectorAll('input[name="' + elm.name.replace(/([\\"])/g, '\\$1') + '"]');

                var error = 1;
                for(var i=0;i<inputs.length;i++) {
                    if((inputs[i].type == 'checkbox' || inputs[i].type == 'radio') && inputs[i].checked == true) {
                        error = 0;
                    }

                    if(Validation.isOnChange && (inputs[i].type == 'checkbox' || inputs[i].type == 'radio')) {
                        Validation.reset(inputs[i]);
                    }
                }

                if( error == 0 ) {
                    return true;
                } else {
                    return false;
                }
            }],
    ['validate-not-negative-number', 'Please enter a number 0 or greater in this field.', function(v) {
                if (Validation.get('IsEmpty').test(v)) {
                    return true;
                }
                v = parseNumber(v);
                return !isNaN(v) && v >= 0;
            }],
    ['validate-zero-or-greater', 'Please enter a number 0 or greater in this field.', function(v) {
            return Validation.get('validate-not-negative-number').test(v);
        }],
    ['validate-greater-than-zero', 'Please enter a number greater than 0 in this field.', function(v) {
            if (Validation.get('IsEmpty').test(v)) {
                return true;
            }
            v = parseNumber(v);
            return !isNaN(v) && v > 0;
        }],

    ['validate-special-price', 'The Special Price is active only when lower than the Actual Price.', function(v) {
        var priceInput = document.getElementById('price');
        var priceType = document.getElementById('price_type');
        var priceValue = parseFloat(v);

        // Passed on non-related validators conditions (to not change order of validation)
        if(
            !priceInput
            || !_getFieldValue(priceInput)
            || Validation.get('IsEmpty').test(v)
            || !Validation.get('validate-number').test(v)
        ) {
            return true;
        }
        if(priceType) {
            return (priceType && priceValue <= 99.99);
        }
        return priceValue < parseFloat(_getFieldValue(priceInput));
    }],
    ['validate-state', 'Please select State/Province.', function(v) {
                return (v!=0 || v == '');
            }],
    ['validate-new-password', 'Please enter more characters or clean leading or trailing spaces.', function(v, elm) {
                if (!Validation.get('validate-password').test(v, elm)) return false;
                if (Validation.get('IsEmpty').test(v) && v != '') return false;
                return true;
            }],
    ['validate-cc-number', 'Please enter a valid credit card number.', function(v, elm) {
                // remove non-numerics
                var ccTypeContainer = document.getElementById(elm.id.substr(0,elm.id.indexOf('_cc_number')) + '_cc_type');
                if (ccTypeContainer && typeof Validation.creditCartTypes[ccTypeContainer.value] != 'undefined'
                        && Validation.creditCartTypes[ccTypeContainer.value][2] == false) {
                    if (!Validation.get('IsEmpty').test(v) && Validation.get('validate-digits').test(v)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return validateCreditCard(v);
            }],
    ['validate-cc-type', 'Credit card number does not match credit card type.', function(v, elm) {
                // remove credit card number delimiters such as "-" and space
                elm.value = removeDelimiters(elm.value);
                v         = removeDelimiters(v);

                var ccTypeContainer = document.getElementById(elm.id.substr(0,elm.id.indexOf('_cc_number')) + '_cc_type');
                if (!ccTypeContainer) {
                    return true;
                }
                var ccType = ccTypeContainer.value;

                if (typeof Validation.creditCartTypes[ccType] == 'undefined') {
                    return false;
                }

                // Other card type or switch or solo card
                if (Validation.creditCartTypes[ccType][0]==false) {
                    return true;
                }

                var validationFailure = false;
                var keys = Object.keys(Validation.creditCartTypes);
                for (var i = 0; i < keys.length; i++) {
                    var key = keys[i];
                    if (key == ccType) {
                        if (Validation.creditCartTypes[key][0] && !v.match(Validation.creditCartTypes[key][0])) {
                            validationFailure = true;
                        }
                        break;
                    }
                }

                if (validationFailure) {
                    return false;
                }

                if (ccTypeContainer.classList.contains('validation-failed') && Validation.isOnChange) {
                    Validation.validate(ccTypeContainer);
                }

                return true;
            }],
     ['validate-cc-type-select', 'Card type does not match credit card number.', function(v, elm) {
                var ccNumberContainer = document.getElementById(elm.id.substr(0,elm.id.indexOf('_cc_type')) + '_cc_number');
                if (Validation.isOnChange && Validation.get('IsEmpty').test(ccNumberContainer.value)) {
                    return true;
                }
                if (Validation.get('validate-cc-type').test(ccNumberContainer.value, ccNumberContainer)) {
                    Validation.validate(ccNumberContainer);
                }
                return Validation.get('validate-cc-type').test(ccNumberContainer.value, ccNumberContainer);
            }],
     ['validate-cc-exp', 'Incorrect credit card expiration date.', function(v, elm) {
                var ccExpMonth   = v;
                var ccExpYear    = document.getElementById(elm.id.substr(0,elm.id.indexOf('_expiration')) + '_expiration_yr').value;
                var currentTime  = new Date();
                var currentMonth = currentTime.getMonth() + 1;
                var currentYear  = currentTime.getFullYear();
                if (ccExpMonth < currentMonth && ccExpYear == currentYear) {
                    return false;
                }
                return true;
            }],
     ['validate-cc-cvn', 'Please enter a valid credit card verification number.', function(v, elm) {
                var ccTypeContainer = document.getElementById(elm.id.substr(0,elm.id.indexOf('_cc_cid')) + '_cc_type');
                if (!ccTypeContainer) {
                    return true;
                }
                var ccType = ccTypeContainer.value;

                if (typeof Validation.creditCartTypes[ccType] == 'undefined') {
                    return false;
                }

                var re = Validation.creditCartTypes[ccType][1];

                if (v.match(re)) {
                    return true;
                }

                return false;
            }],
     ['validate-ajax', '', function(v, elm) { return true; }],
     ['validate-data', 'Please use only letters (a-z or A-Z), numbers (0-9) or underscore(_) in this field, first character should be a letter.', function (v) {
                if(v != '' && v) {
                    return /^[A-Za-z]+[A-Za-z0-9_]+$/.test(v);
                }
                return true;
            }],
     ['validate-css-length', 'Please input a valid CSS-length. For example 100px or 77pt or 20em or .5ex or 50%.', function (v) {
                if (v != '' && v) {
                    return /^[0-9\.]+(px|pt|em|ex|%)?$/.test(v) && (!(/\..*\./.test(v))) && !(/\.$/.test(v));
                }
                return true;
            }],
     ['validate-length', 'Text length does not satisfy specified text range.', function (v, elm) {
                var reMax = new RegExp(/^maximum-length-[0-9]+$/);
                var reMin = new RegExp(/^minimum-length-[0-9]+$/);
                var result = true;
                _splitClassName(elm.className).forEach(function(name, index) {
                    if (name.match(reMax) && result) {
                       var length = name.split('-')[2];
                       result = (v.length <= length);
                    }
                    if (name.match(reMin) && result && !Validation.get('IsEmpty').test(v)) {
                        var length = name.split('-')[2];
                        result = (v.length >= length);
                    }
                });
                return result;
            }],
     ['validate-percents', 'Please enter a number lower than 100.', {max:100}],
     ['required-file', 'Please select a file', function(v, elm) {
         var result = !Validation.get('IsEmpty').test(v);
         if (result === false) {
             ovId = elm.id + '_value';
             if (document.getElementById(ovId)) {
                 result = !Validation.get('IsEmpty').test(document.getElementById(ovId).value);
             }
         }
         return result;
     }],
     ['validate-cc-ukss', 'Please enter issue number or start date for switch/solo card type.', function(v,elm) {
         var endposition;

         if (elm.id.match(/(.)+_cc_issue$/)) {
             endposition = elm.id.indexOf('_cc_issue');
         } else if (elm.id.match(/(.)+_start_month$/)) {
             endposition = elm.id.indexOf('_start_month');
         } else {
             endposition = elm.id.indexOf('_start_year');
         }

         var prefix = elm.id.substr(0,endposition);

         var ccTypeContainer = document.getElementById(prefix + '_cc_type');

         if (!ccTypeContainer) {
               return true;
         }
         var ccType = ccTypeContainer.value;

         if(['SS','SM','SO'].indexOf(ccType) == -1){
             return true;
         }

         var issueEl = document.getElementById(prefix + '_cc_issue');
         var startMonthEl = document.getElementById(prefix + '_start_month');
         var startYearEl = document.getElementById(prefix + '_start_year');
         var advContainer = document.getElementById(prefix + '_cc_type_ss_div');

         if (issueEl) {
             issueEl.advaiceContainer = advContainer ? advContainer.querySelector('ul li.adv-container') : null;
         }
         if (startMonthEl) {
             startMonthEl.advaiceContainer = advContainer ? advContainer.querySelector('ul li.adv-container') : null;
         }
         if (startYearEl) {
             startYearEl.advaiceContainer = advContainer ? advContainer.querySelector('ul li.adv-container') : null;
         }

         var ccIssue   = issueEl ? issueEl.value : '';
         var ccSMonth  = startMonthEl ? startMonthEl.value : '';
         var ccSYear   = startYearEl ? startYearEl.value : '';

         var ccStartDatePresent = (ccSMonth && ccSYear) ? true : false;

         if (!ccStartDatePresent && !ccIssue){
             return false;
         }
         return true;
     }]
]);

function removeDelimiters (v) {
    v = v.replace(/\s/g, '');
    v = v.replace(/\-/g, '');
    return v;
}

function parseNumber(v)
{
    if (typeof v != 'string') {
        return parseFloat(v);
    }

    var isDot  = v.indexOf('.');
    var isComa = v.indexOf(',');

    if (isDot != -1 && isComa != -1) {
        if (isComa > isDot) {
            v = v.replace('.', '').replace(',', '.');
        }
        else {
            v = v.replace(',', '');
        }
    }
    else if (isComa != -1) {
        v = v.replace(',', '.');
    }

    return parseFloat(v);
}

/**
 * Hash with credit card types which can be simply extended in payment modules
 * 0 - regexp for card number
 * 1 - regexp for cvn
 * 2 - check or not credit card number trough Luhn algorithm by
 *     function validateCreditCard which you can find above in this file
 */
Validation.creditCartTypes = {
//    'SS': [new RegExp('^((6759[0-9]{12})|(5018|5020|5038|6304|6759|6761|6763[0-9]{12,19})|(49[013][1356][0-9]{12})|(6333[0-9]{12})|(6334[0-4]\d{11})|(633110[0-9]{10})|(564182[0-9]{10}))([0-9]{2,3})?$'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), true],
    'SO': [new RegExp('^(6334[5-9]([0-9]{11}|[0-9]{13,14}))|(6767([0-9]{12}|[0-9]{14,15}))$'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), true],
    'VI': [new RegExp('^4[0-9]{12}([0-9]{3})?$'), new RegExp('^[0-9]{3}$'), true],
    'MC': [new RegExp('^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$'), new RegExp('^[0-9]{3}$'), true],
    'AE': [new RegExp('^3[47][0-9]{13}$'), new RegExp('^[0-9]{4}$'), true],
    'DI': [new RegExp('^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$'), new RegExp('^[0-9]{3}$'), true],
    'JCB': [new RegExp('^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$'), new RegExp('^[0-9]{3,4}$'), true],
    'DICL': [new RegExp('^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$'), new RegExp('^[0-9]{3}$'), true],
    'SM': [new RegExp('(^(5[0678])[0-9]{11,18}$)|(^(6[^05])[0-9]{11,18}$)|(^(601)[^1][0-9]{9,16}$)|(^(6011)[0-9]{9,11}$)|(^(6011)[0-9]{13,16}$)|(^(65)[0-9]{11,13}$)|(^(65)[0-9]{15,18}$)|(^(49030)[2-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49033)[5-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49110)[1-2]([0-9]{10}$|[0-9]{12,13}$))|(^(49117)[4-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49118)[0-2]([0-9]{10}$|[0-9]{12,13}$))|(^(4936)([0-9]{12}$|[0-9]{14,15}$))'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), true],
    'OT': [false, new RegExp('^([0-9]{3}|[0-9]{4})?$'), false]
};

// Backward-compatible .get() method on creditCartTypes for code that used $H().get()
Validation.creditCartTypes.get = function(key) {
    return this[key];
};
