/**
 * Prototype.js + Scriptaculous Compatibility Shim
 *
 * Replaces Prototype.js and Scriptaculous with native browser APIs.
 * Provides the same API surface so existing OpenMage/Magento code continues
 * to work without modification.
 *
 * Target: ES5+ (modern browsers, no IE)
 * This is a drop-in replacement — not a full reimplementation. Edge cases
 * in the original Prototype.js that no shipping code relies on may not be
 * covered.
 *
 * @license MIT
 */
(function () {
  'use strict';

  // ---------------------------------------------------------------------------
  // Deprecation warning helper — fires once per function name per page load
  // ---------------------------------------------------------------------------
  var _warned = {};
  function _protoWarn(name) {
    if (_warned[name]) return;
    _warned[name] = true;
    console.warn('[Prototype Shim] "' + name + '" is deprecated. Migrate to native APIs.');
  }

  // ---------------------------------------------------------------------------
  // Prototype global
  // ---------------------------------------------------------------------------
  window.Prototype = {
    Version: '1.7.3-shim',
    Browser: {
      IE:     false,
      Opera:  !!window.opr || /OPR/.test(navigator.userAgent),
      WebKit: /AppleWebKit/.test(navigator.userAgent),
      Gecko:  /Gecko/.test(navigator.userAgent) && !/KHTML/.test(navigator.userAgent),
      MobileSafari: /Apple.*Mobile/.test(navigator.userAgent)
    },
    BrowserFeatures: { XPath: !!document.evaluate },
    ScriptFragment: '<script[^>]*>([\\S\\s]*?)<\/script>',
    emptyFunction: function () {},
    K: function (x) { return x; }
  };

  // ---------------------------------------------------------------------------
  // $break sentinel
  // ---------------------------------------------------------------------------
  window.$break = {};

  // ---------------------------------------------------------------------------
  // $() — getElementById, multi-arg returns array
  // ---------------------------------------------------------------------------
  window.$ = function () {
    if (arguments.length === 0) return null;
    if (arguments.length === 1) {
      var el = arguments[0];
      if (typeof el === 'string') return document.getElementById(el);
      return el;
    }
    var results = [];
    for (var i = 0; i < arguments.length; i++) {
      var a = arguments[i];
      results.push(typeof a === 'string' ? document.getElementById(a) : a);
    }
    return results;
  };

  // ---------------------------------------------------------------------------
  // $$() — querySelectorAll returning extended array
  // ---------------------------------------------------------------------------
  window.$$ = function () {
    _protoWarn('$$');
    var selector = Array.prototype.join.call(arguments, ', ');
    var nodes = Array.from(document.querySelectorAll(selector));
    nodes.each = function (fn) { this.forEach(fn); return this; };
    nodes.invoke = function (method) {
      var args = Array.prototype.slice.call(arguments, 1);
      return this.map(function (el) { return el[method].apply(el, args); });
    };
    return nodes;
  };

  // ---------------------------------------------------------------------------
  // $w() — split string on whitespace
  // ---------------------------------------------------------------------------
  window.$w = function (str) {
    if (!str || typeof str !== 'string') return [];
    return str.trim().split(/\s+/);
  };

  // ---------------------------------------------------------------------------
  // $A() — to array
  // ---------------------------------------------------------------------------
  window.$A = function (iterable) {
    if (!iterable) return [];
    if (iterable.toArray) return iterable.toArray();
    return Array.from(iterable);
  };

  // ---------------------------------------------------------------------------
  // $H() — Hash
  // ---------------------------------------------------------------------------
  window.$H = function (obj) {
    return new Hash(obj);
  };

  function Hash(obj) {
    this._data = {};
    if (obj) {
      for (var k in obj) {
        if (obj.hasOwnProperty(k)) this._data[k] = obj[k];
      }
    }
  }
  Hash.prototype.get = function (key) { return this._data[key]; };
  Hash.prototype.set = function (key, val) { this._data[key] = val; return val; };
  Hash.prototype.unset = function (key) { var v = this._data[key]; delete this._data[key]; return v; };
  Hash.prototype.keys = function () { return Object.keys(this._data); };
  Hash.prototype.values = function () {
    var d = this._data, r = [];
    for (var k in d) { if (d.hasOwnProperty(k)) r.push(d[k]); }
    return r;
  };
  Hash.prototype.each = function (fn) {
    var d = this._data;
    for (var k in d) {
      if (d.hasOwnProperty(k)) fn({ key: k, value: d[k] });
    }
  };
  Hash.prototype.toObject = function () {
    var r = {}, d = this._data;
    for (var k in d) { if (d.hasOwnProperty(k)) r[k] = d[k]; }
    return r;
  };
  Hash.prototype.merge = function (other) {
    var result = new Hash(this._data);
    var src = (other instanceof Hash) ? other._data : other;
    for (var k in src) { if (src.hasOwnProperty(k)) result._data[k] = src[k]; }
    return result;
  };
  Hash.prototype.toQueryString = function () {
    var parts = [], d = this._data;
    for (var k in d) {
      if (!d.hasOwnProperty(k)) continue;
      var v = d[k];
      if (v == null) { parts.push(encodeURIComponent(k) + '='); continue; }
      if (Array.isArray(v)) {
        v.forEach(function (item) {
          parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(item));
        });
      } else {
        parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
      }
    }
    return parts.join('&');
  };
  Hash.prototype.inspect = function () { return '#<Hash:{' + this.toQueryString() + '}>'; };
  Hash.prototype.toJSON = function () { return this.toObject(); };
  window.Hash = Hash;

  // ---------------------------------------------------------------------------
  // Object.extend / Object.toQueryString
  // ---------------------------------------------------------------------------
  Object.extend = function (dest, src) {
    for (var k in src) {
      if (src.hasOwnProperty(k)) dest[k] = src[k];
    }
    return dest;
  };

  Object.toQueryString = function (obj) {
    return new Hash(obj).toQueryString();
  };

  Object.toJSON = function (obj) {
    return JSON.stringify(obj);
  };

  if (!Object.keys) {
    Object.keys = function (o) {
      var r = [];
      for (var k in o) { if (o.hasOwnProperty(k)) r.push(k); }
      return r;
    };
  }

  // ---------------------------------------------------------------------------
  // Class.create — OOP with inheritance and $super
  // ---------------------------------------------------------------------------
  var Class = {
    create: function () {
      var parent = null;
      var properties = {};

      if (arguments.length === 0) {
        properties = {};
      } else if (typeof arguments[0] === 'function') {
        parent = arguments[0];
        properties = arguments[1] || {};
      } else {
        properties = arguments[0];
      }

      function klass() {
        if (this.initialize) {
          this.initialize.apply(this, arguments);
        }
      }

      if (parent) {
        var F = function () {};
        F.prototype = parent.prototype;
        klass.prototype = new F();
        klass.prototype.constructor = klass;
        klass.superclass = parent.prototype;
      }

      klass.addMethods = function (source) {
        _addMethods(klass, parent, source);
        return klass;
      };

      if (properties) {
        _addMethods(klass, parent, properties);
      }

      return klass;
    }
  };

  function _addMethods(klass, parent, source) {
    for (var name in source) {
      if (!source.hasOwnProperty(name)) continue;
      var value = source[name];
      if (parent && typeof value === 'function' && typeof parent.prototype[name] === 'function') {
        klass.prototype[name] = (function (method, parentMethod) {
          return function () {
            var args = Array.prototype.slice.call(arguments);
            args.unshift(parentMethod.bind(this));
            return method.apply(this, args);
          };
        })(value, parent.prototype[name]);
      } else {
        klass.prototype[name] = value;
      }
    }
  }

  window.Class = Class;

  // ---------------------------------------------------------------------------
  // Enumerable
  // ---------------------------------------------------------------------------
  var Enumerable = {
    each: function (fn, context) {
      try {
        for (var i = 0; i < this.length; i++) {
          fn.call(context || this, this[i], i);
        }
      } catch (e) {
        if (e !== $break) throw e;
      }
      return this;
    },
    collect: function (fn, ctx) { return Array.prototype.map.call(this, fn, ctx); },
    detect: function (fn, ctx) { return Array.prototype.find.call(this, fn, ctx); },
    select: function (fn, ctx) { return Array.prototype.filter.call(this, fn, ctx); },
    reject: function (fn, ctx) { return Array.prototype.filter.call(this, function (v, i) { return !fn.call(this, v, i); }, ctx); },
    include: function (val) { return Array.prototype.indexOf.call(this, val) !== -1; },
    all: function (fn, ctx) { fn = fn || Prototype.K; return Array.prototype.every.call(this, fn, ctx); },
    any: function (fn, ctx) { fn = fn || Prototype.K; return Array.prototype.some.call(this, fn, ctx); },
    pluck: function (prop) { return Array.prototype.map.call(this, function (el) { return el[prop]; }); },
    invoke: function (method) {
      var args = Array.prototype.slice.call(arguments, 1);
      return Array.prototype.map.call(this, function (el) { return el[method].apply(el, args); });
    },
    flatten: function () {
      var result = [];
      for (var i = 0; i < this.length; i++) {
        var v = this[i];
        if (Array.isArray(v)) {
          result = result.concat($A(v).flatten());
        } else {
          result.push(v);
        }
      }
      return result;
    },
    compact: function () {
      return Array.prototype.filter.call(this, function (v) { return v != null; });
    },
    uniq: function () {
      var seen = [];
      return Array.prototype.filter.call(this, function (v) {
        if (seen.indexOf(v) !== -1) return false;
        seen.push(v);
        return true;
      });
    },
    without: function () {
      var values = Array.prototype.slice.call(arguments);
      return Array.prototype.filter.call(this, function (v) { return values.indexOf(v) === -1; });
    }
  };
  window.Enumerable = Enumerable;

  // ---------------------------------------------------------------------------
  // Array.prototype extensions
  // ---------------------------------------------------------------------------
  var arrayMethods = {
    each: Enumerable.each,
    collect: Enumerable.collect,
    detect: Enumerable.detect,
    select: Enumerable.select,
    findAll: Enumerable.select,
    reject: Enumerable.reject,
    include: Enumerable.include,
    all: Enumerable.all,
    any: Enumerable.any,
    pluck: Enumerable.pluck,
    invoke: Enumerable.invoke,
    flatten: Enumerable.flatten,
    compact: Enumerable.compact,
    uniq: Enumerable.uniq,
    without: Enumerable.without,
    first: function () { return this[0]; },
    last: function () { return this[this.length - 1]; },
    clear: function () { this.length = 0; return this; },
    size: function () { return this.length; },
    clone: function () { return this.slice(0); },
    toArray: function () { return this.slice(0); },
    inspect: function () { return '[' + this.map(function (v) { return typeof v === 'string' ? "'" + v + "'" : String(v); }).join(', ') + ']'; },
    intersect: function (other) {
      return this.uniq().select(function (item) { return other.indexOf(item) !== -1; });
    }
  };

  if (!Array.prototype.indexOf) {
    arrayMethods.indexOf = function (item, from) {
      from = from || 0;
      for (var i = from; i < this.length; i++) {
        if (this[i] === item) return i;
      }
      return -1;
    };
  }

  for (var m in arrayMethods) {
    if (arrayMethods.hasOwnProperty(m) && !Array.prototype[m]) {
      Array.prototype[m] = arrayMethods[m];
    } else if (arrayMethods.hasOwnProperty(m) && m !== 'indexOf' && m !== 'find') {
      // Overwrite for Prototype compat (e.g. each with $break)
      Array.prototype[m] = arrayMethods[m];
    }
  }

  // ---------------------------------------------------------------------------
  // String prototype extensions
  // ---------------------------------------------------------------------------
  var stringMethods = {
    strip: function () { return this.trim(); },
    blank: function () { return /^\s*$/.test(this); },
    empty: function () { return this.length === 0; },
    evalJSON: function (sanitize) {
      var str = this.toString();
      if (sanitize) {
        try { JSON.parse(str); } catch (e) { throw new SyntaxError('Badly formed JSON string'); }
      }
      return JSON.parse(str);
    },
    isJSON: function () {
      try { JSON.parse(this); return true; } catch (e) { return false; }
    },
    stripTags: function () { return this.replace(/<\/?[^>]+>/gi, ''); },
    stripScripts: function () { return this.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, ''); },
    extractScripts: function () {
      var scripts = [];
      this.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function (m, src) { scripts.push(src); });
      return scripts;
    },
    evalScripts: function () {
      var scripts = [];
      this.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function (m, src) { scripts.push(src); });
      scripts.forEach(function (src) {
        var el = document.createElement('script');
        el.textContent = src;
        document.head.appendChild(el);
        document.head.removeChild(el);
      });
      return this.toString();
    },
    escapeHTML: function () {
      return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    },
    unescapeHTML: function () {
      var div = document.createElement('div');
      div.innerHTML = this.toString();
      return div.textContent || div.innerText || '';
    },
    toQueryParams: function (sep) {
      sep = sep || '&';
      var str = this.toString();
      if (str.indexOf('?') === 0) str = str.substring(1);
      if (str.blank()) return {};
      var result = {};
      str.split(sep).forEach(function (pair) {
        var parts = pair.split('=');
        var key = decodeURIComponent(parts[0]);
        var val = parts.length > 1 ? decodeURIComponent(parts.slice(1).join('=')) : undefined;
        if (result.hasOwnProperty(key)) {
          if (!Array.isArray(result[key])) result[key] = [result[key]];
          result[key].push(val);
        } else {
          result[key] = val;
        }
      });
      return result;
    },
    parseQuery: function (sep) { return this.toQueryParams(sep); },
    include: function (str) { return this.indexOf(str) > -1; },
    sub: function (pattern, replacement, count) {
      count = count === undefined ? 1 : count;
      var result = this.toString();
      for (var i = 0; i < count; i++) {
        var fn = typeof replacement === 'function' ? replacement : function () { return replacement; };
        result = result.replace(pattern, fn);
      }
      return result;
    },
    gsub: function (pattern, replacement) {
      if (typeof pattern === 'string') pattern = new RegExp(pattern.replace(/([.*+?^${}()|[\]\\])/g, '\\$1'), 'g');
      if (!pattern.global) {
        var flags = 'g' + (pattern.ignoreCase ? 'i' : '') + (pattern.multiline ? 'm' : '');
        pattern = new RegExp(pattern.source, flags);
      }
      if (typeof replacement !== 'function') {
        var tpl = replacement;
        return this.replace(pattern, tpl);
      }
      // Prototype's gsub passes an array-like match object to the callback
      return this.replace(pattern, function () {
        var args = Array.prototype.slice.call(arguments);
        var match = [args[0]]; // match[0] = full match
        for (var gi = 1; gi < args.length - 2; gi++) {
          match.push(args[gi]);
        }
        return replacement(match);
      });
    },
    scan: function (pattern, iterator) {
      this.gsub(pattern, function (match) {
        iterator(match);
        return match;
      });
      return String(this);
    },
    truncate: function (length, truncation) {
      length = length || 30;
      truncation = truncation === undefined ? '...' : truncation;
      if (this.length <= length) return String(this);
      return this.slice(0, length - truncation.length) + truncation;
    },
    camelize: function () {
      return this.replace(/-+(.)?/g, function (match, chr) {
        return chr ? chr.toUpperCase() : '';
      });
    },
    underscore: function () {
      return this.replace(/::/g, '/').replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
        .replace(/([a-z\d])([A-Z])/g, '$1_$2').replace(/-/g, '_').toLowerCase();
    },
    dasherize: function () { return this.replace(/_/g, '-'); },
    capitalize: function () {
      return this.charAt(0).toUpperCase() + this.substring(1).toLowerCase();
    },
    times: function (count) {
      var r = '';
      for (var i = 0; i < count; i++) r += this;
      return r;
    },
    interpolate: function (obj, pattern) {
      return new Template(this, pattern).evaluate(obj);
    }
  };

  if (!String.prototype.startsWith) {
    stringMethods.startsWith = function (search, pos) {
      pos = pos || 0;
      return this.substr(pos, search.length) === search;
    };
  }
  if (!String.prototype.endsWith) {
    stringMethods.endsWith = function (search, len) {
      len = len === undefined ? this.length : len;
      return this.substring(len - search.length, len) === search;
    };
  }

  for (var sm in stringMethods) {
    if (stringMethods.hasOwnProperty(sm)) {
      if (!String.prototype[sm]) {
        String.prototype[sm] = stringMethods[sm];
      }
    }
  }
  // Force-set Prototype-specific methods that won't collide with native
  String.prototype.strip = stringMethods.strip;
  String.prototype.blank = stringMethods.blank;
  String.prototype.empty = stringMethods.empty;
  String.prototype.evalJSON = stringMethods.evalJSON;
  String.prototype.isJSON = stringMethods.isJSON;
  String.prototype.stripTags = stringMethods.stripTags;
  String.prototype.stripScripts = stringMethods.stripScripts;
  String.prototype.extractScripts = stringMethods.extractScripts;
  String.prototype.evalScripts = stringMethods.evalScripts;
  String.prototype.escapeHTML = stringMethods.escapeHTML;
  String.prototype.unescapeHTML = stringMethods.unescapeHTML;
  String.prototype.toQueryParams = stringMethods.toQueryParams;
  String.prototype.parseQuery = stringMethods.parseQuery;
  String.prototype.sub = stringMethods.sub;
  String.prototype.gsub = stringMethods.gsub;
  String.prototype.scan = stringMethods.scan;
  String.prototype.truncate = stringMethods.truncate;
  String.prototype.camelize = stringMethods.camelize;
  String.prototype.underscore = stringMethods.underscore;
  String.prototype.dasherize = stringMethods.dasherize;
  String.prototype.capitalize = stringMethods.capitalize;
  String.prototype.times = stringMethods.times;
  String.prototype.interpolate = stringMethods.interpolate;

  // ---------------------------------------------------------------------------
  // Number extensions
  // ---------------------------------------------------------------------------
  Number.prototype.toColorPart = function () {
    return this.toPaddedString(2, 16);
  };
  Number.prototype.toPaddedString = function (length, radix) {
    var str = this.toString(radix || 10);
    while (str.length < length) str = '0' + str;
    return str;
  };
  Number.prototype.succ = function () { return this + 1; };
  Number.prototype.times = function (fn, ctx) {
    for (var i = 0; i < this; i++) fn.call(ctx, i);
    return this;
  };
  Number.prototype.abs = function () { return Math.abs(this); };
  Number.prototype.round = function () { return Math.round(this); };
  Number.prototype.ceil = function () { return Math.ceil(this); };
  Number.prototype.floor = function () { return Math.floor(this); };

  // ---------------------------------------------------------------------------
  // Function extensions
  // ---------------------------------------------------------------------------
  if (!Function.prototype.bind) {
    Function.prototype.bind = function (ctx) {
      var fn = this, args = Array.prototype.slice.call(arguments, 1);
      return function () { return fn.apply(ctx, args.concat(Array.prototype.slice.call(arguments))); };
    };
  }

  Function.prototype.bindAsEventListener = function (ctx) {
    var fn = this, args = Array.prototype.slice.call(arguments, 1);
    return function (event) {
      return fn.apply(ctx, [event || window.event].concat(args));
    };
  };

  Function.prototype.curry = function () {
    var fn = this, args = Array.prototype.slice.call(arguments);
    return function () { return fn.apply(this, args.concat(Array.prototype.slice.call(arguments))); };
  };

  Function.prototype.delay = function (timeout) {
    var fn = this, args = Array.prototype.slice.call(arguments, 1);
    return window.setTimeout(function () { fn.apply(fn, args); }, timeout * 1000);
  };

  Function.prototype.defer = function () {
    var args = [0.01].concat(Array.prototype.slice.call(arguments));
    return this.delay.apply(this, args);
  };

  Function.prototype.wrap = function (wrapper) {
    var fn = this;
    return function () {
      var args = [fn.bind(this)].concat(Array.prototype.slice.call(arguments));
      return wrapper.apply(this, args);
    };
  };

  Function.prototype.argumentNames = function () {
    var src = this.toString();
    var names = src.match(/^[\s\(]*function[^(]*\(([^)]*)\)/);
    if (!names) return [];
    return names[1].split(',').map(function (a) { return a.trim(); }).filter(function (a) { return a; });
  };

  // ---------------------------------------------------------------------------
  // Template
  // ---------------------------------------------------------------------------
  function Template(template, pattern) {
    this.template = template.toString();
    this.pattern = pattern || Template.Pattern;
  }
  Template.Pattern = /(^|.|\r|\n)(#\{(.*?)\})/;
  Template.prototype.evaluate = function (object) {
    if (object instanceof Hash) object = object.toObject();
    return this.template.gsub(this.pattern, function (match) {
      if (match[1] === '\\') return match[2];
      var before = match[1] || '';
      var key = match[3];
      var value = object;
      var keys = key.split('.');
      for (var i = 0; i < keys.length; i++) {
        if (value == null) { value = ''; break; }
        value = value[keys[i]];
      }
      return before + (value == null ? '' : String(value));
    });
  };
  window.Template = Template;

  // ---------------------------------------------------------------------------
  // Element prototype methods
  // ---------------------------------------------------------------------------
  var EP = HTMLElement.prototype;

  // Visibility
  EP.show = function () { this.style.display = ''; return this; };
  EP.hide = function () { this.style.display = 'none'; return this; };
  EP.visible = function () { return this.style.display !== 'none'; };
  EP.toggle = function () { return this[this.visible() ? 'hide' : 'show'](); };

  // Classes
  EP.addClassName = function (name) { this.classList.add(name); return this; };
  EP.removeClassName = function (name) { this.classList.remove(name); return this; };
  EP.hasClassName = function (name) { return this.classList.contains(name); };
  EP.toggleClassName = function (name) { this.classList.toggle(name); return this; };

  // Traversal
  EP.up = function (selector, index) {
    if (selector === undefined) return this.parentElement;
    if (typeof selector === 'number') {
      var el = this;
      for (var i = 0; i <= selector; i++) { el = el ? el.parentElement : null; }
      return el;
    }
    index = index || 0;
    var parent = this.parentElement;
    var count = 0;
    while (parent) {
      if (parent.matches && parent.matches(selector)) {
        if (count === index) return parent;
        count++;
      }
      parent = parent.parentElement;
    }
    return null;
  };

  EP.down = function (selector, index) {
    if (selector === undefined) return this.firstElementChild;
    if (typeof selector === 'number') { selector = '*'; index = arguments[0]; }
    index = index || 0;
    var els = this.querySelectorAll(selector);
    return els[index] || null;
  };

  EP.next = function (selector, index) {
    if (selector === undefined) return this.nextElementSibling;
    index = index || 0;
    var sib = this.nextElementSibling;
    var count = 0;
    while (sib) {
      if (sib.matches && sib.matches(selector)) {
        if (count === index) return sib;
        count++;
      }
      sib = sib.nextElementSibling;
    }
    return null;
  };

  EP.previous = function (selector, index) {
    if (selector === undefined) return this.previousElementSibling;
    index = index || 0;
    var sib = this.previousElementSibling;
    var count = 0;
    while (sib) {
      if (sib.matches && sib.matches(selector)) {
        if (count === index) return sib;
        count++;
      }
      sib = sib.previousElementSibling;
    }
    return null;
  };

  EP.select = EP.select || function (selector) {
    return Array.from(this.querySelectorAll(selector));
  };
  // Force set since Array.prototype.select may interfere on NodeList
  EP.select = function (selector) {
    return Array.from(this.querySelectorAll(selector));
  };

  EP.ancestors = function () {
    var result = [], el = this.parentElement;
    while (el) { result.push(el); el = el.parentElement; }
    return result;
  };

  EP.descendants = function () {
    return Array.from(this.querySelectorAll('*'));
  };

  EP.childElements = function () {
    return Array.from(this.children);
  };

  // Content manipulation
  EP.update = function (content) {
    if (content === undefined || content === null) content = '';
    this.innerHTML = String(content);
    _evalScripts(this);
    return this;
  };

  EP.insert = function (insertions) {
    if (typeof insertions === 'string' || typeof insertions === 'number' ||
        (insertions && insertions.nodeType === 1)) {
      insertions = { bottom: insertions };
    }
    var self = this;
    function _doInsert(where, content) {
      if (!content) return;
      if (content.nodeType === 1) {
        if (where === 'top') self.insertBefore(content, self.firstChild);
        else if (where === 'bottom') self.appendChild(content);
        else if (where === 'before' && self.parentNode) self.parentNode.insertBefore(content, self);
        else if (where === 'after' && self.parentNode) self.parentNode.insertBefore(content, self.nextSibling);
      } else {
        var posMap = { top: 'afterbegin', bottom: 'beforeend', before: 'beforebegin', after: 'afterend' };
        self.insertAdjacentHTML(posMap[where], String(content));
      }
    }
    if (insertions.top) _doInsert('top', insertions.top);
    if (insertions.bottom) _doInsert('bottom', insertions.bottom);
    if (insertions.before) _doInsert('before', insertions.before);
    if (insertions.after) _doInsert('after', insertions.after);
    _evalScripts(this);
    return this;
  };

  EP.remove = function () {
    if (this.parentNode) this.parentNode.removeChild(this);
    return this;
  };

  EP.replace = function (content) {
    if (this.parentNode) {
      this.outerHTML = String(content);
    }
    return this;
  };

  // Inline script evaluation helper — skips scripts already marked data-evaluated
  function _evalScripts(container) {
    var scripts = container.querySelectorAll('script:not([data-evaluated])');
    scripts.forEach(function (s) {
      s.setAttribute('data-evaluated', '1');
      var ns = document.createElement('script');
      if (s.src) {
        ns.src = s.src;
      } else {
        ns.textContent = s.textContent;
      }
      document.head.appendChild(ns).parentNode.removeChild(ns);
    });
  }

  // Style
  EP.setStyle = function (styles) {
    if (typeof styles === 'string') {
      this.style.cssText += ';' + styles;
      return this;
    }
    for (var prop in styles) {
      if (styles.hasOwnProperty(prop)) {
        var name = prop === 'float' ? 'cssFloat' : prop.replace(/-([a-z])/g, function (m, c) { return c.toUpperCase(); });
        this.style[name] = styles[prop];
      }
    }
    return this;
  };

  EP.getStyle = function (prop) {
    var name = prop === 'float' ? 'cssFloat' : prop.replace(/-([a-z])/g, function (m, c) { return c.toUpperCase(); });
    var val = this.style[name];
    if (!val || val === 'auto') {
      val = window.getComputedStyle(this).getPropertyValue(prop);
    }
    return val;
  };

  EP.getDimensions = function () {
    return { width: this.offsetWidth, height: this.offsetHeight };
  };
  EP.getHeight = function () { return this.offsetHeight; };
  EP.getWidth = function () { return this.offsetWidth; };

  // Form element enable/disable
  EP.disable = function () { this.disabled = true; return this; };
  EP.enable = function () { this.disabled = false; return this; };

  // Attributes
  EP.readAttribute = function (name) {
    return this.getAttribute(name);
  };

  EP.writeAttribute = function (name, value) {
    if (typeof name === 'object') {
      for (var k in name) { if (name.hasOwnProperty(k)) this.writeAttribute(k, name[k]); }
      return this;
    }
    if (value === false || value === null) {
      this.removeAttribute(name);
    } else {
      this.setAttribute(name, value === true ? name : value);
    }
    return this;
  };

  // Events on elements
  EP.observe = function (eventName, handler) {
    this.addEventListener(eventName, handler, false);
    return this;
  };

  EP.stopObserving = function (eventName, handler) {
    if (eventName) {
      this.removeEventListener(eventName, handler, false);
    }
    return this;
  };

  EP.on = EP.observe;

  EP.fire = function (eventName, memo) {
    var evt = new CustomEvent(eventName, { bubbles: true, cancelable: true, detail: memo || {} });
    evt.memo = memo || {};
    this.dispatchEvent(evt);
    return evt;
  };

  // Form element values
  EP.getValue = function () {
    return _getFieldValue(this);
  };

  EP.setValue = function (val) {
    var tag = this.tagName.toLowerCase();
    if (tag === 'input' && (this.type === 'checkbox' || this.type === 'radio')) {
      this.checked = (this.value == val);
    } else if (tag === 'select') {
      Array.from(this.options).forEach(function (opt) { opt.selected = (opt.value == val); });
    } else {
      this.value = val !== undefined ? val : '';
    }
    return this;
  };

  // Per-element data storage (Prototype's Element.store / Element.retrieve)
  EP.store = function (key, value) {
    if (!this._protoStorage) this._protoStorage = {};
    this._protoStorage[key] = value;
    return this;
  };
  EP.retrieve = function (key, defaultValue) {
    if (!this._protoStorage) this._protoStorage = {};
    var val = this._protoStorage[key];
    if (typeof val === 'undefined') {
      if (typeof defaultValue !== 'undefined') {
        this._protoStorage[key] = defaultValue;
        return defaultValue;
      }
      return null;
    }
    return val;
  };

  // OpenMage custom from varien/js.js
  EP.getInnerText = function () {
    return this.textContent || this.innerText || '';
  };

  // ---------------------------------------------------------------------------
  // Element static API
  // ---------------------------------------------------------------------------
  window.Element = window.Element || {};

  Element.extend = function (el) { return el; };

  Element.addMethods = function (methods) {
    if (!methods) return;
    for (var name in methods) {
      if (methods.hasOwnProperty(name)) {
        // Wrap: Element.addMethods methods take (element, ...) as first arg
        // but we put them on prototype where `this` is the element
        (function (methodName, fn) {
          HTMLElement.prototype[methodName] = function () {
            var args = [this].concat(Array.prototype.slice.call(arguments));
            return fn.apply(null, args);
          };
        })(name, methods[name]);
      }
    }
  };

  // Static versions: Element.show(el), Element.hide(el), etc.
  var staticElementMethods = [
    'show', 'hide', 'visible', 'toggle',
    'addClassName', 'removeClassName', 'hasClassName', 'toggleClassName',
    'up', 'down', 'next', 'previous', 'select',
    'update', 'insert', 'remove', 'replace',
    'observe', 'stopObserving', 'fire',
    'setStyle', 'getStyle', 'getDimensions', 'getHeight', 'getWidth',
    'readAttribute', 'writeAttribute',
    'ancestors', 'descendants', 'childElements',
    'getValue', 'setValue', 'getInnerText',
    'store', 'retrieve',
    'disable', 'enable'
  ];

  staticElementMethods.forEach(function (name) {
    Element[name] = function (el) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      if (!el) return el;
      var args = Array.prototype.slice.call(arguments, 1);
      return el[name].apply(el, args);
    };
  });

  // Element.getStyle as static
  Element.getStyle = function (el, prop) {
    el = typeof el === 'string' ? document.getElementById(el) : el;
    if (!el) return null;
    return el.getStyle(prop);
  };

  // Element.setStyle as static
  Element.setStyle = function (el, styles) {
    el = typeof el === 'string' ? document.getElementById(el) : el;
    if (!el) return null;
    return el.setStyle(styles);
  };

  // ---------------------------------------------------------------------------
  // document.observe / document.fire
  // ---------------------------------------------------------------------------
  document.observe = function (eventName, handler) {
    document.addEventListener(eventName, handler, false);
    return document;
  };
  document.on = function (eventName, selector, handler) {
    if (typeof selector === 'function') {
      return document.observe(eventName, selector);
    }
    document.addEventListener(eventName, function (evt) {
      var matched = evt.target.closest(selector);
      if (matched) {
        handler.call(matched, evt, matched);
      }
    }, false);
    return document;
  };
  document.stopObserving = function (eventName, handler) {
    document.removeEventListener(eventName, handler, false);
    return document;
  };
  document.fire = function (eventName, memo) {
    var evt = new CustomEvent(eventName, { bubbles: true, cancelable: true, detail: memo || {} });
    evt.memo = memo || {};
    document.dispatchEvent(evt);
    return evt;
  };
  document.loaded = true;

  // window.observe
  window.observe = function (eventName, handler) {
    window.addEventListener(eventName, handler, false);
    return window;
  };

  // ---------------------------------------------------------------------------
  // Event
  // ---------------------------------------------------------------------------
  var Event = window.Event || {};

  Event.KEY_BACKSPACE = 8;
  Event.KEY_TAB       = 9;
  Event.KEY_RETURN    = 13;
  Event.KEY_ESC       = 27;
  Event.KEY_LEFT      = 37;
  Event.KEY_UP        = 38;
  Event.KEY_RIGHT     = 39;
  Event.KEY_DOWN      = 40;
  Event.KEY_DELETE    = 46;
  Event.KEY_HOME      = 36;
  Event.KEY_END       = 35;
  Event.KEY_PAGEUP    = 33;
  Event.KEY_PAGEDOWN  = 34;
  Event.KEY_INSERT    = 45;

  Event.observe = function (element, eventName, handler) {
    element = typeof element === 'string' ? document.getElementById(element) : element;
    if (element) element.addEventListener(eventName, handler, false);
  };

  Event.stopObserving = function (element, eventName, handler) {
    element = typeof element === 'string' ? document.getElementById(element) : element;
    if (element) element.removeEventListener(eventName, handler, false);
  };

  Event.stop = function (event) {
    event.preventDefault();
    event.stopPropagation();
  };

  Event.element = function (event) {
    var node = event.target || event.srcElement;
    if (node && node.nodeType === 3) node = node.parentNode; // text node
    return node;
  };

  Event.findElement = function (event, selector) {
    var el = Event.element(event);
    if (!selector) return el;
    while (el && el !== document) {
      if (el.matches && el.matches(selector)) return el;
      el = el.parentElement;
    }
    return null;
  };

  Event.isLeftClick = function (event) {
    return event.button === 0;
  };

  Event.pointerX = function (event) { return event.pageX; };
  Event.pointerY = function (event) { return event.pageY; };

  window.Event = Event;

  // ---------------------------------------------------------------------------
  // Form / $F
  // ---------------------------------------------------------------------------
  var Form = {
    serialize: function (form, asObject) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      var elements = Form.getElements(form);
      var data = {};
      elements.forEach(function (el) {
        var name = el.name;
        if (!name || el.disabled) return;
        var val = _getFieldValue(el);
        if (val === null) return;
        if (data.hasOwnProperty(name)) {
          if (!Array.isArray(data[name])) data[name] = [data[name]];
          data[name].push(val);
        } else {
          data[name] = val;
        }
      });
      if (asObject) return data;
      return Object.toQueryString(data);
    },

    getElements: function (form) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      return Array.from(form.elements).filter(function (el) {
        return el.name && !el.disabled && el.type !== 'submit' && el.type !== 'reset' &&
               el.type !== 'button' && el.type !== 'image';
      });
    },

    getInputs: function (form, type, name) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      var inputs = Array.from(form.getElementsByTagName('input'));
      if (type) inputs = inputs.filter(function (i) { return i.type.toLowerCase() === type.toLowerCase(); });
      if (name) inputs = inputs.filter(function (i) { return i.name === name; });
      return inputs;
    },

    disable: function (form) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      Form.getElements(form).forEach(function (el) { el.disabled = true; });
      return form;
    },

    enable: function (form) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      Form.getElements(form).forEach(function (el) { el.disabled = false; });
      return form;
    },

    serializeElements: function (elements, options) {
      var asObject = options === true || (options && options.hash);
      var data = {};
      elements.forEach(function (el) {
        if (!el.name || el.disabled) return;
        var val = _getFieldValue(el);
        if (val === null) return;
        if (data.hasOwnProperty(el.name)) {
          if (!Array.isArray(data[el.name])) data[el.name] = [data[el.name]];
          data[el.name].push(val);
        } else {
          data[el.name] = val;
        }
      });
      if (asObject) return data;
      return Object.toQueryString(data);
    },

    reset: function (form) {
      form = typeof form === 'string' ? document.getElementById(form) : form;
      form.reset();
      return form;
    },

    Element: {
      getValue: function (el) {
        el = typeof el === 'string' ? document.getElementById(el) : el;
        return _getFieldValue(el);
      },
      focus: function (el) {
        el = typeof el === 'string' ? document.getElementById(el) : el;
        if (el) el.focus();
        return el;
      },
      select: function (el) {
        el = typeof el === 'string' ? document.getElementById(el) : el;
        if (el && el.select) el.select();
        return el;
      },
      serialize: function (el) {
        el = typeof el === 'string' ? document.getElementById(el) : el;
        var val = _getFieldValue(el);
        if (val === null) return '';
        return encodeURIComponent(el.name) + '=' + encodeURIComponent(val);
      }
    }
  };

  function _getFieldValue(el) {
    if (!el) return null;
    var tag = el.tagName.toLowerCase();
    if (tag === 'select') {
      if (el.type === 'select-multiple') {
        var vals = [];
        Array.from(el.options).forEach(function (o) { if (o.selected) vals.push(o.value); });
        return vals;
      }
      return el.value;
    }
    if (tag === 'input') {
      if (el.type === 'checkbox' || el.type === 'radio') {
        return el.checked ? el.value : null;
      }
      return el.value;
    }
    if (tag === 'textarea') return el.value;
    return el.value !== undefined ? el.value : null;
  }

  window.Form = Form;

  window.$F = function (el) {
    el = typeof el === 'string' ? document.getElementById(el) : el;
    return _getFieldValue(el);
  };

  // ---------------------------------------------------------------------------
  // Ajax
  // ---------------------------------------------------------------------------
  var Ajax = {
    activeRequestCount: 0,
    Responders: {
      _responders: [],
      register: function (responder) { this._responders.push(responder); },
      unregister: function (responder) {
        this._responders = this._responders.filter(function (r) { return r !== responder; });
      },
      _dispatch: function (name, request, response) {
        this._responders.forEach(function (r) {
          if (typeof r[name] === 'function') {
            try { r[name](request, response); } catch (e) { /* swallow */ }
          }
        });
      }
    }
  };

  Ajax.Request = Class.create({
    initialize: function (url, options) {
      this.options = Object.extend({
        method: 'post',
        asynchronous: true,
        contentType: 'application/x-www-form-urlencoded',
        encoding: 'UTF-8',
        parameters: '',
        evalJSON: true,
        evalScripts: false
      }, options || {});

      this.transport = {};
      this.url = url;

      var params = this.options.parameters;
      if (typeof params === 'object' && !(params instanceof String)) {
        params = Object.toQueryString(params);
      }

      var method = this.options.method.toLowerCase();

      if (method === 'get' && params) {
        this.url += (this.url.indexOf('?') > -1 ? '&' : '?') + params;
        params = null;
      }

      Ajax.activeRequestCount++;
      Ajax.Responders._dispatch('onCreate', this);
      if (typeof this.options.onCreate === 'function') this.options.onCreate(this);

      var headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
      };

      if (method === 'post') {
        headers['Content-Type'] = this.options.contentType + '; charset=' + this.options.encoding;
      }

      if (this.options.requestHeaders) {
        var rh = this.options.requestHeaders;
        if (Array.isArray(rh)) {
          for (var i = 0; i < rh.length; i += 2) headers[rh[i]] = rh[i + 1];
        } else if (typeof rh === 'object') {
          Object.extend(headers, rh);
        }
      }

      var self = this;
      var body = this.options.postBody || params;

      fetch(url, {
        method: method.toUpperCase(),
        headers: headers,
        body: (method === 'get' || method === 'head') ? undefined : body,
        credentials: 'same-origin'
      }).then(function (fetchResponse) {
        return fetchResponse.text().then(function (text) {
          var response = {
            status: fetchResponse.status,
            statusText: fetchResponse.statusText,
            responseText: text,
            responseJSON: null,
            getHeader: function (name) { return fetchResponse.headers.get(name); },
            getAllHeaders: function () { return ''; },
            headerJSON: null,
            request: self,
            transport: { status: fetchResponse.status, statusText: fetchResponse.statusText, responseText: text }
          };
          self.transport = response.transport;

          if (self.options.evalJSON) {
            try { response.responseJSON = JSON.parse(text); } catch (e) { /* not JSON */ }
          }

          var success = fetchResponse.status >= 200 && fetchResponse.status < 300;

          if (success) {
            if (typeof self.options.onSuccess === 'function') self.options.onSuccess(response);
            Ajax.Responders._dispatch('onSuccess', self, response);
          } else {
            if (typeof self.options.onFailure === 'function') self.options.onFailure(response);
            Ajax.Responders._dispatch('onFailure', self, response);
          }

          if (typeof self.options.onComplete === 'function') self.options.onComplete(response);
          Ajax.activeRequestCount--;
          Ajax.Responders._dispatch('onComplete', self, response);
        });
      }).catch(function (err) {
        var response = {
          status: 0,
          statusText: err.message,
          responseText: '',
          responseJSON: null,
          getHeader: function () { return null; },
          transport: {}
        };
        if (typeof self.options.onFailure === 'function') self.options.onFailure(response);
        if (typeof self.options.onComplete === 'function') self.options.onComplete(response);
        Ajax.activeRequestCount--;
        Ajax.Responders._dispatch('onFailure', self, response);
        Ajax.Responders._dispatch('onComplete', self, response);
      });
    }
  });

  Ajax.Updater = Class.create({
    initialize: function (container, url, options) {
      this.container = typeof container === 'string' ? document.getElementById(container) : container;
      if (container.success) {
        this.containers = {
          success: typeof container.success === 'string' ? document.getElementById(container.success) : container.success,
          failure: typeof container.failure === 'string' ? document.getElementById(container.failure) : container.failure
        };
      } else {
        this.containers = { success: this.container };
      }

      var self = this;
      var origSuccess = options.onSuccess;
      var origFailure = options.onFailure;

      options.onSuccess = function (response) {
        var target = self.containers.success;
        if (target) {
          if (options.insertion) {
            if (typeof options.insertion === 'string') {
              target.insert({ bottom: response.responseText });
            } else {
              options.insertion(target, response.responseText);
            }
          } else {
            target.innerHTML = response.responseText;
          }
          if (options.evalScripts) {
            target.querySelectorAll('script').forEach(function (s) {
              var ns = document.createElement('script');
              if (s.src) { ns.src = s.src; } else { ns.textContent = s.textContent; }
              document.head.appendChild(ns).parentNode.removeChild(ns);
            });
          }
        }
        if (typeof origSuccess === 'function') origSuccess(response);
      };

      options.onFailure = function (response) {
        var target = self.containers.failure;
        if (target) target.innerHTML = response.responseText;
        if (typeof origFailure === 'function') origFailure(response);
      };

      this.request = new Ajax.Request(url, options);
    }
  });

  Ajax.PeriodicalUpdater = Class.create({
    initialize: function (container, url, options) {
      this.container = container;
      this.url = url;
      this.options = Object.extend({ frequency: 2 }, options || {});
      this.start();
    },
    start: function () {
      var self = this;
      this.timer = setInterval(function () {
        new Ajax.Updater(self.container, self.url, self.options);
      }, this.options.frequency * 1000);
    },
    stop: function () { clearInterval(this.timer); }
  });

  // ---------------------------------------------------------------------------
  // Ajax.Autocompleter — lightweight reimplementation for admin global search
  // Supports: paramName, minChars, method, frequency, indicator,
  //           updateElement, afterUpdateElement, onShow, onHide, evalJSON
  // ---------------------------------------------------------------------------
  Ajax.Autocompleter = Class.create({
    initialize: function (element, update, url, options) {
      this.element = $(element);
      this.update = $(update);
      this.url = url;
      this.options = Object.extend({
        paramName: 'value',
        minChars: 1,
        method: 'get',
        frequency: 0.4,
        indicator: null,
        updateElement: null,
        afterUpdateElement: null,
        onShow: null,
        onHide: null,
        evalJSON: false,
        parameters: {}
      }, options || {});

      this.active = false;
      this.hasFocus = false;
      this.index = -1;
      this.entryCount = 0;
      this.observer = null;
      this.oldValue = this.element.value;

      this.update.style.display = 'none';

      this.element.setAttribute('autocomplete', 'off');
      var self = this;
      this.element.addEventListener('keydown', function (e) { self.onKeyDown(e); }, false);
      this.element.addEventListener('focus', function () { self.onFocus(); }, false);
      this.element.addEventListener('blur', function () { self.onBlur(); }, false);
    },

    onKeyDown: function (evt) {
      switch (evt.keyCode) {
        case Event.KEY_UP:
          evt.preventDefault();
          this.markPrevious();
          return;
        case Event.KEY_DOWN:
          evt.preventDefault();
          this.markNext();
          return;
        case Event.KEY_RETURN:
          if (this.active && this.index >= 0) {
            evt.preventDefault();
            this.selectEntry();
          }
          return;
        case Event.KEY_ESC:
          this.hide();
          this.active = false;
          return;
        case Event.KEY_TAB:
          if (this.active && this.index >= 0) this.selectEntry();
          return;
      }
    },

    onFocus: function () {
      this.hasFocus = true;
      this.changed = false;
      this.startObserving();
    },

    onBlur: function () {
      var self = this;
      this.hasFocus = false;
      // Delay to allow click on dropdown item
      setTimeout(function () {
        if (!self.hasFocus) self.hide();
      }, 250);
    },

    startObserving: function () {
      if (this.observer) return;
      var self = this;
      this.observer = setInterval(function () { self.onObserverEvent(); }, this.options.frequency * 1000);
    },

    stopObserving: function () {
      if (this.observer) {
        clearInterval(this.observer);
        this.observer = null;
      }
    },

    onObserverEvent: function () {
      var val = this.element.value;
      if (val === this.oldValue) return;
      this.oldValue = val;
      if (val.length >= this.options.minChars) {
        this.getUpdatedChoices();
      } else {
        this.hide();
        this.active = false;
      }
    },

    getUpdatedChoices: function () {
      var params = {};
      params[this.options.paramName] = this.element.value;
      Object.extend(params, this.options.parameters);

      var indicator = this.options.indicator ? $(this.options.indicator) : null;
      if (indicator) indicator.style.display = '';

      var self = this;
      new Ajax.Request(this.url, {
        method: this.options.method,
        parameters: params,
        onSuccess: function (response) {
          if (indicator) indicator.style.display = 'none';
          self.updateChoices(response.responseText);
        },
        onFailure: function () {
          if (indicator) indicator.style.display = 'none';
        }
      });
    },

    updateChoices: function (html) {
      this.update.innerHTML = html;
      var entries = this.update.querySelectorAll('li');
      this.entryCount = entries.length;
      this.index = -1;

      for (var i = 0; i < entries.length; i++) {
        var self = this;
        (function (idx) {
          entries[idx].addEventListener('click', function (e) {
            e.preventDefault();
            self.index = idx;
            self.selectEntry();
          }, false);
          entries[idx].addEventListener('mouseover', function () {
            self.setActive(idx);
          }, false);
        })(i);
      }

      if (this.entryCount > 0) {
        this.show();
        this.active = true;
      } else {
        this.hide();
        this.active = false;
      }
    },

    show: function () {
      if (typeof this.options.onShow === 'function') {
        this.options.onShow(this.element, this.update);
      } else {
        this.update.style.display = '';
      }
    },

    hide: function () {
      this.stopObserving();
      if (typeof this.options.onHide === 'function') {
        this.options.onHide(this.element, this.update);
      } else {
        this.update.style.display = 'none';
      }
    },

    setActive: function (idx) {
      var entries = this.update.querySelectorAll('li');
      for (var i = 0; i < entries.length; i++) {
        entries[i].className = (i === idx) ? 'selected' : '';
      }
      this.index = idx;
    },

    markPrevious: function () {
      if (this.index > 0) {
        this.setActive(this.index - 1);
      } else {
        this.setActive(this.entryCount - 1);
      }
    },

    markNext: function () {
      if (this.index < this.entryCount - 1) {
        this.setActive(this.index + 1);
      } else {
        this.setActive(0);
      }
    },

    selectEntry: function () {
      this.active = false;
      var entries = this.update.querySelectorAll('li');
      var selected = entries[this.index];
      if (!selected) return;
      this.updateElement(selected);
      this.hide();
    },

    updateElement: function (selected) {
      if (typeof this.options.updateElement === 'function') {
        this.options.updateElement(selected);
      } else {
        var value = (selected.textContent || selected.innerText || '').replace(/^\s+|\s+$/g, '');
        this.element.value = value;
        this.oldValue = value;
        this.element.focus();
      }
      if (typeof this.options.afterUpdateElement === 'function') {
        this.options.afterUpdateElement(this.element, selected);
      }
    }
  });

  window.Ajax = Ajax;

  // ---------------------------------------------------------------------------
  // Position
  // ---------------------------------------------------------------------------
  window.Position = {
    cumulativeOffset: function (el) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      var rect = el.getBoundingClientRect();
      return [rect.left + window.scrollX, rect.top + window.scrollY];
    },
    positionedOffset: function (el) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      return [el.offsetLeft, el.offsetTop];
    },
    clone: function (source, target, options) {
      source = typeof source === 'string' ? document.getElementById(source) : source;
      target = typeof target === 'string' ? document.getElementById(target) : target;
      options = Object.extend({ setLeft: true, setTop: true, setWidth: true, setHeight: true, offsetLeft: 0, offsetTop: 0 }, options || {});
      var rect = source.getBoundingClientRect();
      if (options.setLeft)   target.style.left   = (rect.left + window.scrollX + options.offsetLeft) + 'px';
      if (options.setTop)    target.style.top    = (rect.top + window.scrollY + options.offsetTop) + 'px';
      if (options.setWidth)  target.style.width  = rect.width + 'px';
      if (options.setHeight) target.style.height = rect.height + 'px';
      return target;
    },
    absolutize: function (el) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      if (el.style.position === 'absolute') return el;
      var rect = el.getBoundingClientRect();
      el.style.position = 'absolute';
      el.style.left = (rect.left + window.scrollX) + 'px';
      el.style.top = (rect.top + window.scrollY) + 'px';
      return el;
    },
    relativize: function (el) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      el.style.position = 'relative';
      el.style.left = '';
      el.style.top = '';
      return el;
    }
  };

  // ---------------------------------------------------------------------------
  // Scriptaculous Effects Shim
  // ---------------------------------------------------------------------------
  var Effect = {
    DefaultOptions: { duration: 0.3 },
    _resolved: function (el) {
      return typeof el === 'string' ? document.getElementById(el) : el;
    }
  };

  Effect.Fade = function (el, options) {
    _protoWarn('Effect.Fade');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration, from: 1, to: 0 }, options || {});
    el.style.opacity = String(options.from);
    el.style.transition = 'opacity ' + options.duration + 's';
    // Force reflow
    void el.offsetWidth;
    el.style.opacity = String(options.to);
    setTimeout(function () {
      if (options.to === 0) el.style.display = 'none';
      el.style.transition = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.Appear = function (el, options) {
    _protoWarn('Effect.Appear');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration, from: 0, to: 1 }, options || {});
    el.style.opacity = String(options.from);
    el.style.display = '';
    el.style.transition = 'opacity ' + options.duration + 's';
    void el.offsetWidth;
    el.style.opacity = String(options.to);
    setTimeout(function () {
      el.style.transition = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.Highlight = function (el, options) {
    _protoWarn('Effect.Highlight');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({
      duration: Effect.DefaultOptions.duration,
      startcolor: '#ffff99',
      endcolor: '#ffffff'
    }, options || {});
    el.style.backgroundColor = options.startcolor;
    el.style.transition = 'background-color ' + options.duration + 's';
    void el.offsetWidth;
    el.style.backgroundColor = options.endcolor;
    setTimeout(function () {
      el.style.transition = '';
      el.style.backgroundColor = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.toggle = function (el, type, options) {
    _protoWarn('Effect.toggle');
    el = Effect._resolved(el);
    if (!el) return;
    var visible = el.style.display !== 'none';
    if (visible) {
      Effect.Fade(el, options);
    } else {
      Effect.Appear(el, options);
    }
  };

  Effect.BlindDown = function (el, options) {
    _protoWarn('Effect.BlindDown');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration }, options || {});
    el.style.overflow = 'hidden';
    el.style.display = '';
    var targetHeight = el.scrollHeight;
    el.style.height = '0px';
    el.style.transition = 'height ' + options.duration + 's';
    void el.offsetWidth;
    el.style.height = targetHeight + 'px';
    setTimeout(function () {
      el.style.transition = '';
      el.style.height = '';
      el.style.overflow = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.BlindUp = function (el, options) {
    _protoWarn('Effect.BlindUp');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration }, options || {});
    el.style.overflow = 'hidden';
    el.style.height = el.offsetHeight + 'px';
    el.style.transition = 'height ' + options.duration + 's';
    void el.offsetWidth;
    el.style.height = '0px';
    setTimeout(function () {
      el.style.display = 'none';
      el.style.transition = '';
      el.style.height = '';
      el.style.overflow = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.SlideDown = Effect.BlindDown;
  Effect.SlideUp = Effect.BlindUp;

  Effect.Morph = function (el, options) {
    _protoWarn('Effect.Morph');
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration, style: {} }, options || {});
    var styles = options.style;
    if (typeof styles === 'string') {
      var parsed = {};
      styles.split(';').forEach(function (rule) {
        var parts = rule.split(':');
        if (parts.length === 2) parsed[parts[0].trim()] = parts[1].trim();
      });
      styles = parsed;
    }
    el.style.transition = 'all ' + options.duration + 's';
    void el.offsetWidth;
    for (var prop in styles) {
      if (styles.hasOwnProperty(prop)) {
        var cssProp = prop.replace(/-([a-z])/g, function (m, c) { return c.toUpperCase(); });
        el.style[cssProp] = styles[prop];
      }
    }
    setTimeout(function () {
      el.style.transition = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
      if (typeof options.onComplete === 'function') options.onComplete({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.Opacity = function (el, options) {
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration, from: 1, to: 0 }, options || {});
    el.style.opacity = String(options.from);
    el.style.transition = 'opacity ' + options.duration + 's';
    void el.offsetWidth;
    el.style.opacity = String(options.to);
    setTimeout(function () {
      el.style.transition = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.Scale = function (el, percent, options) {
    el = Effect._resolved(el);
    if (!el) return;
    options = Object.extend({ duration: Effect.DefaultOptions.duration }, options || {});
    el.style.transition = 'transform ' + options.duration + 's';
    void el.offsetWidth;
    el.style.transform = 'scale(' + (percent / 100) + ')';
    setTimeout(function () {
      el.style.transition = '';
      if (typeof options.afterFinish === 'function') options.afterFinish({ element: el });
    }, options.duration * 1000 + 50);
  };

  Effect.Transitions = {
    linear: function (pos) { return pos; },
    sinoidal: function (pos) { return (-Math.cos(pos * Math.PI) / 2) + 0.5; },
    reverse: function (pos) { return 1 - pos; },
    flicker: function (pos) { return (-Math.cos(pos * Math.PI) / 4) + 0.75 + Math.random() / 4; },
    wobble: function (pos) { return (-Math.cos(pos * Math.PI * (9 * pos)) / 2) + 0.5; },
    pulse: function (pos, pulses) { return (-Math.cos((pos * ((pulses || 5) - 0.5) * 2) * Math.PI) / 2) + 0.5; },
    spring: function (pos) { return 1 - (Math.cos(pos * 4.5 * Math.PI) * Math.exp(-pos * 6)); },
    none: function (pos) { return 0; },
    full: function (pos) { return 1; }
  };

  window.Effect = Effect;

  // ---------------------------------------------------------------------------
  // Sortable / Draggable / Droppable stubs
  // ---------------------------------------------------------------------------
  window.Sortable = {
    create: function () { _protoWarn('Sortable.create'); },
    destroy: function () {},
    serialize: function () { return ''; }
  };

  window.Draggable = function () { _protoWarn('Draggable'); };
  window.Draggable.prototype = { initialize: function () {} };
  window.Draggables = { register: function () {}, unregister: function () {}, drags: [] };

  window.Droppables = {
    add: function () { _protoWarn('Droppables.add'); },
    remove: function () {},
    drops: []
  };

  // ---------------------------------------------------------------------------
  // Try.these
  // ---------------------------------------------------------------------------
  var Try = {
    these: function () {
      for (var i = 0; i < arguments.length; i++) {
        try { return arguments[i](); } catch (e) { /* continue */ }
      }
      return undefined;
    }
  };
  window.Try = Try;

  // ---------------------------------------------------------------------------
  // PeriodicalExecuter
  // ---------------------------------------------------------------------------
  window.PeriodicalExecuter = Class.create({
    initialize: function (callback, frequency) {
      this.callback = callback;
      this.frequency = frequency;
      this.currentlyExecuting = false;
      this.timer = setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
    },
    onTimerEvent: function () {
      if (!this.currentlyExecuting) {
        try {
          this.currentlyExecuting = true;
          this.callback(this);
        } finally {
          this.currentlyExecuting = false;
        }
      }
    },
    stop: function () {
      clearInterval(this.timer);
    }
  });

  // ---------------------------------------------------------------------------
  // Element.Layout / measure (simplified)
  // ---------------------------------------------------------------------------
  Element.Layout = Class.create({
    initialize: function (element) {
      this.element = typeof element === 'string' ? document.getElementById(element) : element;
    },
    get: function (property) {
      var cs = window.getComputedStyle(this.element);
      return parseInt(cs.getPropertyValue(property), 10) || 0;
    }
  });

  Element.getLayout = function (el) {
    return new Element.Layout(el);
  };

  // ---------------------------------------------------------------------------
  // Selector (legacy)
  // ---------------------------------------------------------------------------
  window.Selector = Class.create({
    initialize: function (expression) {
      this.expression = expression;
    },
    findElements: function (root) {
      root = root || document;
      return Array.from(root.querySelectorAll(this.expression));
    },
    match: function (element) {
      return element.matches(this.expression);
    }
  });

  // ---------------------------------------------------------------------------
  // Insertion (legacy, used by some admin scripts)
  // ---------------------------------------------------------------------------
  window.Insertion = {
    Before: function (el, content) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      el.insertAdjacentHTML('beforebegin', content);
    },
    After: function (el, content) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      el.insertAdjacentHTML('afterend', content);
    },
    Top: function (el, content) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      el.insertAdjacentHTML('afterbegin', content);
    },
    Bottom: function (el, content) {
      el = typeof el === 'string' ? document.getElementById(el) : el;
      el.insertAdjacentHTML('beforeend', content);
    }
  };

  // ---------------------------------------------------------------------------
  // Element.Methods.Simulated (for certain admin JS)
  // ---------------------------------------------------------------------------
  if (!Element.Methods) Element.Methods = {};
  if (!Element.Methods.Simulated) Element.Methods.Simulated = {};

  // ---------------------------------------------------------------------------
  // Prototype.Selector
  // ---------------------------------------------------------------------------
  Prototype.Selector = {
    select: function (selector, root) {
      root = root || document;
      return Array.from(root.querySelectorAll(selector));
    },
    match: function (element, selector) {
      return element.matches(selector);
    },
    find: function (elements, selector, index) {
      index = index || 0;
      var results = [];
      for (var i = 0; i < elements.length; i++) {
        if (elements[i].matches && elements[i].matches(selector)) results.push(elements[i]);
      }
      return results[index] || null;
    },
    extendElements: function (elements) { return elements; }
  };

  // ---------------------------------------------------------------------------
  // document.viewport
  // ---------------------------------------------------------------------------
  document.viewport = {
    getDimensions: function () {
      return {
        width: window.innerWidth || document.documentElement.clientWidth,
        height: window.innerHeight || document.documentElement.clientHeight
      };
    },
    getWidth: function () { return this.getDimensions().width; },
    getHeight: function () { return this.getDimensions().height; },
    getScrollOffsets: function () {
      return {
        left: window.pageXOffset || document.documentElement.scrollLeft || 0,
        top: window.pageYOffset || document.documentElement.scrollTop || 0
      };
    }
  };

  // ---------------------------------------------------------------------------
  // Abstract.TimedObserver / Form.Element.Observer / Form.Observer
  // ---------------------------------------------------------------------------
  var Abstract = {};
  Abstract.TimedObserver = Class.create({
    initialize: function (element, frequency, callback) {
      this.element = typeof element === 'string' ? document.getElementById(element) : element;
      this.frequency = frequency;
      this.callback = callback;
      this.lastValue = this.getValue();
      this.timer = setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
    },
    onTimerEvent: function () {
      var value = this.getValue();
      if (value !== this.lastValue) {
        this.callback(this.element, value);
        this.lastValue = value;
      }
    },
    stop: function () { clearInterval(this.timer); },
    getValue: function () { return ''; }
  });

  Form.Element.Observer = Class.create(Abstract.TimedObserver, {
    getValue: function () { return _getFieldValue(this.element); }
  });

  Form.Observer = Class.create(Abstract.TimedObserver, {
    getValue: function () { return Form.serialize(this.element); }
  });

  Abstract.EventObserver = Class.create({
    initialize: function (element, callback) {
      this.element = typeof element === 'string' ? document.getElementById(element) : element;
      this.callback = callback;
      this.lastValue = this.getValue();
      var eventName = this._getEventName();
      this.element.addEventListener(eventName, this.onElementEvent.bind(this));
    },
    onElementEvent: function () {
      var value = this.getValue();
      if (value !== this.lastValue) {
        this.callback(this.element, value);
        this.lastValue = value;
      }
    },
    _getEventName: function () {
      var tag = this.element.tagName.toLowerCase();
      if (tag === 'select' || (tag === 'input' && (this.element.type === 'checkbox' || this.element.type === 'radio'))) {
        return 'change';
      }
      return 'change';
    },
    getValue: function () { return ''; }
  });

  Form.Element.EventObserver = Class.create(Abstract.EventObserver, {
    getValue: function () { return _getFieldValue(this.element); }
  });

  window.Abstract = Abstract;

  // ---------------------------------------------------------------------------
  // String.interpret / Object.isFunction / Object.isString / Object.isUndefined
  // ---------------------------------------------------------------------------
  String.interpret = function (value) {
    return value == null ? '' : String(value);
  };

  Object.isFunction = function (obj) { return typeof obj === 'function'; };
  Object.isString = function (obj) { return typeof obj === 'string'; };
  Object.isNumber = function (obj) { return typeof obj === 'number'; };
  Object.isUndefined = function (obj) { return typeof obj === 'undefined'; };
  Object.isArray = function (obj) { return Array.isArray(obj); };
  Object.isElement = function (obj) { return !!(obj && obj.nodeType === 1); };
  Object.isHash = function (obj) { return obj instanceof Hash; };
  Object.clone = function (obj) { return Object.extend({}, obj); };
  Object.toHTML = function (obj) {
    if (obj && typeof obj.toHTML === 'function') return obj.toHTML();
    return String.interpret(obj);
  };

  // ---------------------------------------------------------------------------
  // Ensure dom:loaded fires
  // ---------------------------------------------------------------------------
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      document.fire('dom:loaded');
      document.loaded = true;
    });
  } else {
    // Already loaded
    setTimeout(function () { document.fire('dom:loaded'); }, 0);
  }

})();
