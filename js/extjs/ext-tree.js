/**
 * Ext JS Library 1.1.1
 * Copyright (c) 2006-2007, Ext JS, LLC
 * All rights reserved.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 * 
 * The CSS and Graphics ("Assets") distributed with Ext are licensed for use ONLY
 * with their associated Ext JavaScript component ("Component"). Use of the Assets in
 * any way that does not also include the Component is prohibited without explicit
 * permission from Ext JS, LLC.  Deriving images and CSS from the Assets in an effort
 * to bypass this license is also prohibited.
 * 
 * --
 * 
 * The JavaScript code distributed with Ext (the "Software") is licensed under the
 * Lesser GNU (LGPL) open source license version 3.0.
 * 
 * http://www.gnu.org/licenses/lgpl.html
 * 
 * If you are using this library for commercial purposes, we encourage you to purchase
 * a commercial license. Please visit http://extjs.com/license for more details.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 */


Ext = {};

// for old browsers
window["undefined"] = window["undefined"];

/**
 * @class Ext
 * Ext core utilities and functions.
 * @singleton
 */

/**
 * Copies all the properties of config to obj.
 * @param {Object} obj The receiver of the properties
 * @param {Object} config The source of the properties
 * @param {Object} defaults A different object that will also be applied for default values
 * @return {Object} returns obj
 * @member Ext apply
 */
Ext.apply = function(o, c, defaults){
    if(defaults){
        // no "this" reference for friendly out of scope calls
        Ext.apply(o, defaults);
    }
    if(o && c && typeof c == 'object'){
        for(var p in c){
            o[p] = c[p];
        }
    }
    return o;
};

(function(){
    var idSeed = 0;
    var ua = navigator.userAgent.toLowerCase();

    var isStrict = document.compatMode == "CSS1Compat",
        isOpera = ua.indexOf("opera") > -1,
        isSafari = (/webkit|khtml/).test(ua),
        isIE = ua.indexOf("msie") > -1,
        isIE7 = ua.indexOf("msie 7") > -1,
        isGecko = !isSafari && ua.indexOf("gecko") > -1,
        isBorderBox = isIE && !isStrict,
        isWindows = (ua.indexOf("windows") != -1 || ua.indexOf("win32") != -1),
        isMac = (ua.indexOf("macintosh") != -1 || ua.indexOf("mac os x") != -1),
        isLinux = (ua.indexOf("linux") != -1),
        isSecure = window.location.href.toLowerCase().indexOf("https") === 0;

    // remove css image flicker
	if(isIE && !isIE7){
        try{
            document.execCommand("BackgroundImageCache", false, true);
        }catch(e){}
    }

    Ext.apply(Ext, {
        /**
         * True if the browser is in strict mode
         * @type Boolean
         */
        isStrict : isStrict,
        /**
         * True if the page is running over SSL
         * @type Boolean
         */
        isSecure : isSecure,
        /**
         * True when the document is fully initialized and ready for action
         * @type Boolean
         */
        isReady : false,

        /**
         * True to automatically uncache orphaned Ext.Elements periodically (defaults to true)
         * @type Boolean
         */
        enableGarbageCollector : true,

        /**
         * True to automatically purge event listeners after uncaching an element (defaults to false).
         * Note: this only happens if enableGarbageCollector is true.
         * @type Boolean
         */
        enableListenerCollection:false,

        /**
         * URL to a blank file used by Ext when in secure mode for iframe src and onReady src to prevent
         * the IE insecure content warning (defaults to javascript:false).
         * @type String
         */
        SSL_SECURE_URL : "javascript:false",

        /**
         * URL to a 1x1 transparent gif image used by Ext to create inline icons with CSS background images. (Defaults to
         * "http://extjs.com/s.gif" and you should change this to a URL on your server).
         * @type String
         */
        BLANK_IMAGE_URL : "http:/"+"/extjs.com/s.gif",

        emptyFn : function(){},

        /**
         * Copies all the properties of config to obj if they don't already exist.
         * @param {Object} obj The receiver of the properties
         * @param {Object} config The source of the properties
         * @return {Object} returns obj
         */
        applyIf : function(o, c){
            if(o && c){
                for(var p in c){
                    if(typeof o[p] == "undefined"){ o[p] = c[p]; }
                }
            }
            return o;
        },

        /**
         * Applies event listeners to elements by selectors when the document is ready.
         * The event name is specified with an @ suffix.
<pre><code>
Ext.addBehaviors({
   // add a listener for click on all anchors in element with id foo
   '#foo a@click' : function(e, t){
       // do something
   },

   // add the same listener to multiple selectors (separated by comma BEFORE the @)
   '#foo a, #bar span.some-class@mouseover' : function(){
       // do something
   }
});
</code></pre>
         * @param {Object} obj The list of behaviors to apply
         */
        addBehaviors : function(o){
            if(!Ext.isReady){
                Ext.onReady(function(){
                    Ext.addBehaviors(o);
                });
                return;
            }
            var cache = {}; // simple cache for applying multiple behaviors to same selector does query multiple times
            for(var b in o){
                var parts = b.split('@');
                if(parts[1]){ // for Object prototype breakers
                    var s = parts[0];
                    if(!cache[s]){
                        cache[s] = Ext.select(s);
                    }
                    cache[s].on(parts[1], o[b]);
                }
            }
            cache = null;
        },

        /**
         * Generates unique ids. If the element already has an id, it is unchanged
         * @param {String/HTMLElement/Element} el (optional) The element to generate an id for
         * @param {String} prefix (optional) Id prefix (defaults "ext-gen")
         * @return {String} The generated Id.
         */
        id : function(el, prefix){
            prefix = prefix || "ext-gen";
            el = Ext.getDom(el);
            var id = prefix + (++idSeed);
            return el ? (el.id ? el.id : (el.id = id)) : id;
        },

        /**
         * Extends one class with another class and optionally overrides members with the passed literal. This class
         * also adds the function "override()" to the class that can be used to override
         * members on an instance.
         * @param {Object} subclass The class inheriting the functionality
         * @param {Object} superclass The class being extended
         * @param {Object} overrides (optional) A literal with members
         * @method extend
         */
        extend : function(){
            // inline overrides
            var io = function(o){
                for(var m in o){
                    this[m] = o[m];
                }
            };
            return function(sb, sp, overrides){
                if(typeof sp == 'object'){
                    overrides = sp;
                    sp = sb;
                    sb = function(){sp.apply(this, arguments);};
                }
                var F = function(){}, sbp, spp = sp.prototype;
                F.prototype = spp;
                sbp = sb.prototype = new F();
                sbp.constructor=sb;
                sb.superclass=spp;
                if(spp.constructor == Object.prototype.constructor){
                    spp.constructor=sp;
                }
                sb.override = function(o){
                    Ext.override(sb, o);
                };
                sbp.override = io;
                Ext.override(sb, overrides);
                return sb;
            };
        }(),

        /**
         * Adds a list of functions to the prototype of an existing class, overwriting any existing methods with the same name.
         * Usage:<pre><code>
Ext.override(MyClass, {
    newMethod1: function(){
        // etc.
    },
    newMethod2: function(foo){
        // etc.
    }
});
 </code></pre>
         * @param {Object} origclass The class to override
         * @param {Object} overrides The list of functions to add to origClass.  This should be specified as an object literal
         * containing one or more methods.
         * @method override
         */
        override : function(origclass, overrides){
            if(overrides){
                var p = origclass.prototype;
                for(var method in overrides){
                    p[method] = overrides[method];
                }
            }
        },
        /**
         * Creates namespaces to be used for scoping variables and classes so that they are not global.  Usage:
         * <pre><code>
Ext.namespace('Company', 'Company.data');
Company.Widget = function() { ... }
Company.data.CustomStore = function(config) { ... }
</code></pre>
         * @param {String} namespace1
         * @param {String} namespace2
         * @param {String} etc
         * @method namespace
         */
        namespace : function(){
            var a=arguments, o=null, i, j, d, rt;
            for (i=0; i<a.length; ++i) {
                d=a[i].split(".");
                rt = d[0];
                eval('if (typeof ' + rt + ' == "undefined"){' + rt + ' = {};} o = ' + rt + ';');
                for (j=1; j<d.length; ++j) {
                    o[d[j]]=o[d[j]] || {};
                    o=o[d[j]];
                }
            }
        },

        /**
         * Takes an object and converts it to an encoded URL. e.g. Ext.urlEncode({foo: 1, bar: 2}); would return "foo=1&bar=2".  Optionally, property values can be arrays, instead of keys and the resulting string that's returned will contain a name/value pair for each array value.
         * @param {Object} o
         * @return {String}
         */
        urlEncode : function(o){
            if(!o){
                return "";
            }
            var buf = [];
            for(var key in o){
                var ov = o[key], k = encodeURIComponent(key);
                var type = typeof ov;
                if(type == 'undefined'){
                    buf.push(k, "=&");
                }else if(type != "function" && type != "object"){
                    buf.push(k, "=", encodeURIComponent(ov), "&");
                }else if(ov instanceof Array){
                    if (ov.length) {
	                    for(var i = 0, len = ov.length; i < len; i++) {
	                        buf.push(k, "=", encodeURIComponent(ov[i] === undefined ? '' : ov[i]), "&");
	                    }
	                } else {
	                    buf.push(k, "=&");
	                }
                }
            }
            buf.pop();
            return buf.join("");
        },

        /**
         * Takes an encoded URL and and converts it to an object. e.g. Ext.urlDecode("foo=1&bar=2"); would return {foo: 1, bar: 2} or Ext.urlDecode("foo=1&bar=2&bar=3&bar=4", true); would return {foo: 1, bar: [2, 3, 4]}.
         * @param {String} string
         * @param {Boolean} overwrite (optional) Items of the same name will overwrite previous values instead of creating an an array (Defaults to false).
         * @return {Object} A literal with members
         */
        urlDecode : function(string, overwrite){
            if(!string || !string.length){
                return {};
            }
            var obj = {};
            var pairs = string.split('&');
            var pair, name, value;
            for(var i = 0, len = pairs.length; i < len; i++){
                pair = pairs[i].split('=');
                name = decodeURIComponent(pair[0]);
                value = decodeURIComponent(pair[1]);
                if(overwrite !== true){
                    if(typeof obj[name] == "undefined"){
                        obj[name] = value;
                    }else if(typeof obj[name] == "string"){
                        obj[name] = [obj[name]];
                        obj[name].push(value);
                    }else{
                        obj[name].push(value);
                    }
                }else{
                    obj[name] = value;
                }
            }
            return obj;
        },

        /**
         * Iterates an array calling the passed function with each item, stopping if your function returns false. If the
         * passed array is not really an array, your function is called once with it.
         * The supplied function is called with (Object item, Number index, Array allItems).
         * @param {Array/NodeList/Mixed} array
         * @param {Function} fn
         * @param {Object} scope
         */
        each : function(array, fn, scope){
            if(typeof array.length == "undefined" || typeof array == "string"){
                array = [array];
            }
            for(var i = 0, len = array.length; i < len; i++){
                if(fn.call(scope || array[i], array[i], i, array) === false){ return i; };
            }
        },

        // deprecated
        combine : function(){
            var as = arguments, l = as.length, r = [];
            for(var i = 0; i < l; i++){
                var a = as[i];
                if(a instanceof Array){
                    r = r.concat(a);
                }else if(a.length !== undefined && !a.substr){
                    r = r.concat(Array.prototype.slice.call(a, 0));
                }else{
                    r.push(a);
                }
            }
            return r;
        },

        /**
         * Escapes the passed string for use in a regular expression
         * @param {String} str
         * @return {String}
         */
        escapeRe : function(s) {
            return s.replace(/([.*+?^${}()|[\]\/\\])/g, "\\$1");
        },

        // internal
        callback : function(cb, scope, args, delay){
            if(typeof cb == "function"){
                if(delay){
                    cb.defer(delay, scope, args || []);
                }else{
                    cb.apply(scope, args || []);
                }
            }
        },

        /**
         * Return the dom node for the passed string (id), dom node, or Ext.Element
         * @param {String/HTMLElement/Ext.Element} el
         * @return HTMLElement
         */
        getDom : function(el){
            if(!el){
                return null;
            }
            return el.dom ? el.dom : (typeof el == 'string' ? document.getElementById(el) : el);
        },

        /**
        * Shorthand for {@link Ext.ComponentMgr#get}
        * @param {String} id
        * @return Ext.Component
        */
        getCmp : function(id){
            return Ext.ComponentMgr.get(id);
        },

        num : function(v, defaultValue){
            if(typeof v != 'number'){
                return defaultValue;
            }
            return v;
        },

        destroy : function(){
            for(var i = 0, a = arguments, len = a.length; i < len; i++) {
                var as = a[i];
                if(as){
                    if(as.dom){
                        as.removeAllListeners();
                        as.remove();
                        continue;
                    }
                    if(typeof as.purgeListeners == 'function'){
                        as.purgeListeners();
                    }
                    if(typeof as.destroy == 'function'){
                        as.destroy();
                    }
                }
            }
        },

        // inpired by a similar function in mootools library
        /**
         * Returns the type of object that is passed in. If the object passed in is null or undefined it
         * return false otherwise it returns one of the following values:<ul>
         * <li><b>string</b>: If the object passed is a string</li>
         * <li><b>number</b>: If the object passed is a number</li>
         * <li><b>boolean</b>: If the object passed is a boolean value</li>
         * <li><b>function</b>: If the object passed is a function reference</li>
         * <li><b>object</b>: If the object passed is an object</li>
         * <li><b>array</b>: If the object passed is an array</li>
         * <li><b>regexp</b>: If the object passed is a regular expression</li>
         * <li><b>element</b>: If the object passed is a DOM Element</li>
         * <li><b>nodelist</b>: If the object passed is a DOM NodeList</li>
         * <li><b>textnode</b>: If the object passed is a DOM text node and contains something other than whitespace</li>
         * <li><b>whitespace</b>: If the object passed is a DOM text node and contains only whitespace</li>
         * @param {Mixed} object
         * @return {String}
         */
        type : function(o){
            if(o === undefined || o === null){
                return false;
            }
            if(o.htmlElement){
                return 'element';
            }
            var t = typeof o;
            if(t == 'object' && o.nodeName) {
                switch(o.nodeType) {
                    case 1: return 'element';
                    case 3: return (/\S/).test(o.nodeValue) ? 'textnode' : 'whitespace';
                }
            }
            if(t == 'object' || t == 'function') {
                switch(o.constructor) {
                    case Array: return 'array';
                    case RegExp: return 'regexp';
                }
                if(typeof o.length == 'number' && typeof o.item == 'function') {
                    return 'nodelist';
                }
            }
            return t;
        },

        /**
         * Returns true if the passed value is null, undefined or an empty string (optional).
         * @param {Mixed} value The value to test
         * @param {Boolean} allowBlank (optional) Pass true if an empty string is not considered empty
         * @return {Boolean}
         */
        isEmpty : function(v, allowBlank){
            return v === null || v === undefined || (!allowBlank ? v === '' : false);
        },
        
        /** @type Boolean */
        isOpera : isOpera,
        /** @type Boolean */
        isSafari : isSafari,
        /** @type Boolean */
        isIE : isIE,
        /** @type Boolean */
        isIE7 : isIE7,
        /** @type Boolean */
        isGecko : isGecko,
        /** @type Boolean */
        isBorderBox : isBorderBox,
        /** @type Boolean */
        isWindows : isWindows,
        /** @type Boolean */
        isLinux : isLinux,
        /** @type Boolean */
        isMac : isMac,

    /**
     By default, Ext intelligently decides whether floating elements should be shimmed. If you are using flash,
     you may want to set this to true.
     @type Boolean
     */
        useShims : ((isIE && !isIE7) || (isGecko && isMac))
    });


})();

Ext.namespace("Ext", "Ext.util", "Ext.grid", "Ext.dd", "Ext.tree", "Ext.data",
                "Ext.form", "Ext.menu", "Ext.state", "Ext.lib", "Ext.layout", "Ext.app", "Ext.ux");


/**
 * @class Function
 * These functions are available on every Function object (any JavaScript function).
 */
Ext.apply(Function.prototype, {
     /**
     * Creates a callback that passes arguments[0], arguments[1], arguments[2], ...
     * Call directly on any function. Example: <code>myFunction.createCallback(myarg, myarg2)</code>
     * Will create a function that is bound to those 2 args.
     * @return {Function} The new function
    */
    createCallback : function(/*args...*/){
        // make args available, in function below
        var args = arguments;
        var method = this;
        return function() {
            return method.apply(window, args);
        };
    },

    /**
     * Creates a delegate (callback) that sets the scope to obj.
     * Call directly on any function. Example: <code>this.myFunction.createDelegate(this)</code>
     * Will create a function that is automatically scoped to this.
     * @param {Object} obj (optional) The object for which the scope is set
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     *                                             if a number the args are inserted at the specified position
     * @return {Function} The new function
     */
    createDelegate : function(obj, args, appendArgs){
        var method = this;
        return function() {
            var callArgs = args || arguments;
            if(appendArgs === true){
                callArgs = Array.prototype.slice.call(arguments, 0);
                callArgs = callArgs.concat(args);
            }else if(typeof appendArgs == "number"){
                callArgs = Array.prototype.slice.call(arguments, 0); // copy arguments first
                var applyArgs = [appendArgs, 0].concat(args); // create method call params
                Array.prototype.splice.apply(callArgs, applyArgs); // splice them in
            }
            return method.apply(obj || window, callArgs);
        };
    },

    /**
     * Calls this function after the number of millseconds specified.
     * @param {Number} millis The number of milliseconds for the setTimeout call (if 0 the function is executed immediately)
     * @param {Object} obj (optional) The object for which the scope is set
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     *                                             if a number the args are inserted at the specified position
     * @return {Number} The timeout id that can be used with clearTimeout
     */
    defer : function(millis, obj, args, appendArgs){
        var fn = this.createDelegate(obj, args, appendArgs);
        if(millis){
            return setTimeout(fn, millis);
        }
        fn();
        return 0;
    },
    /**
     * Create a combined function call sequence of the original function + the passed function.
     * The resulting function returns the results of the original function.
     * The passed fcn is called with the parameters of the original function
     * @param {Function} fcn The function to sequence
     * @param {Object} scope (optional) The scope of the passed fcn (Defaults to scope of original function or window)
     * @return {Function} The new function
     */
    createSequence : function(fcn, scope){
        if(typeof fcn != "function"){
            return this;
        }
        var method = this;
        return function() {
            var retval = method.apply(this || window, arguments);
            fcn.apply(scope || this || window, arguments);
            return retval;
        };
    },

    /**
     * Creates an interceptor function. The passed fcn is called before the original one. If it returns false, the original one is not called.
     * The resulting function returns the results of the original function.
     * The passed fcn is called with the parameters of the original function.
     * @addon
     * @param {Function} fcn The function to call before the original
     * @param {Object} scope (optional) The scope of the passed fcn (Defaults to scope of original function or window)
     * @return {Function} The new function
     */
    createInterceptor : function(fcn, scope){
        if(typeof fcn != "function"){
            return this;
        }
        var method = this;
        return function() {
            fcn.target = this;
            fcn.method = method;
            if(fcn.apply(scope || this || window, arguments) === false){
                return;
            }
            return method.apply(this || window, arguments);
        };
    }
});

/**
 * @class String
 * These functions are available as static methods on the JavaScript String object.
 */
Ext.applyIf(String, {

    /**
     * Escapes the passed string for ' and \
     * @param {String} string The string to escape
     * @return {String} The escaped string
     * @static
     */
    escape : function(string) {
        return string.replace(/('|\\)/g, "\\$1");
    },

    /**
     * Pads the left side of a string with a specified character.  This is especially useful
     * for normalizing number and date strings.  Example usage:
     * <pre><code>
var s = String.leftPad('123', 5, '0');
// s now contains the string: '00123'
</code></pre>
     * @param {String} string The original string
     * @param {Number} size The total length of the output string
     * @param {String} char (optional) The character with which to pad the original string (defaults to empty string " ")
     * @return {String} The padded string
     * @static
     */
    leftPad : function (val, size, ch) {
        var result = new String(val);
        if(ch === null || ch === undefined || ch === '') {
            ch = " ";
        }
        while (result.length < size) {
            result = ch + result;
        }
        return result;
    },

    /**
     * Allows you to define a tokenized string and pass an arbitrary number of arguments to replace the tokens.  Each
     * token must be unique, and must increment in the format {0}, {1}, etc.  Example usage:
     * <pre><code>
var cls = 'my-class', text = 'Some text';
var s = String.format('<div class="{0}">{1}</div>', cls, text);
// s now contains the string: '<div class="my-class">Some text</div>'
</code></pre>
     * @param {String} string The tokenized string to be formatted
     * @param {String} value1 The value to replace token {0}
     * @param {String} value2 Etc...
     * @return {String} The formatted string
     * @static
     */
    format : function(format){
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/\{(\d+)\}/g, function(m, i){
            return args[i];
        });
    }
});

/**
 * Utility function that allows you to easily switch a string between two alternating values.  The passed value
 * is compared to the current string, and if they are equal, the other value that was passed in is returned.  If
 * they are already different, the first value passed in is returned.  Note that this method returns the new value
 * but does not change the current string.
 * <pre><code>
// alternate sort directions
sort = sort.toggle('ASC', 'DESC');

// instead of conditional logic:
sort = (sort == 'ASC' ? 'DESC' : 'ASC');
</code></pre>
 * @param {String} value The value to compare to the current string
 * @param {String} other The new value to use if the string already equals the first value passed in
 * @return {String} The new value
 */
String.prototype.toggle = function(value, other){
    return this == value ? other : value;
};
/**
 * @class Number
 */
Ext.applyIf(Number.prototype, {
    /**
     * Checks whether or not the current number is within a desired range.  If the number is already within the
     * range it is returned, otherwise the min or max value is returned depending on which side of the range is
     * exceeded.  Note that this method returns the constrained value but does not change the current number.
     * @param {Number} min The minimum number in the range
     * @param {Number} max The maximum number in the range
     * @return {Number} The constrained value if outside the range, otherwise the current value
     */
    constrain : function(min, max){
        return Math.min(Math.max(this, min), max);
    }
});
/**
 * @class Array
 */
Ext.applyIf(Array.prototype, {
    /**
     * Checks whether or not the specified object exists in the array.
     * @param {Object} o The object to check for
     * @return {Number} The index of o in the array (or -1 if it is not found)
     */
    indexOf : function(o){
       for (var i = 0, len = this.length; i < len; i++){
 	      if(this[i] == o) return i;
       }
 	   return -1;
    },

    /**
     * Removes the specified object from the array.  If the object is not found nothing happens.
     * @param {Object} o The object to remove
     */
    remove : function(o){
       var index = this.indexOf(o);
       if(index != -1){
           this.splice(index, 1);
       }
    }
});

/**
 Returns the number of milliseconds between this date and date
 @param {Date} date (optional) Defaults to now
 @return {Number} The diff in milliseconds
 @member Date getElapsed
 */
Date.prototype.getElapsed = function(date) {
	return Math.abs((date || new Date()).getTime()-this.getTime());
};




(function(){

var libFlyweight;

Ext.lib.Dom = {
    getViewWidth : function(full){
        return full ? this.getDocumentWidth() : this.getViewportWidth();
    },

    getViewHeight : function(full){
        return full ? this.getDocumentHeight() : this.getViewportHeight();
    },

    getDocumentHeight: function() { // missing from prototype?
        var scrollHeight = (document.compatMode != "CSS1Compat") ? document.body.scrollHeight : document.documentElement.scrollHeight;
        return Math.max(scrollHeight, this.getViewportHeight());
    },

    getDocumentWidth: function() { // missing from prototype?
        var scrollWidth = (document.compatMode != "CSS1Compat") ? document.body.scrollWidth : document.documentElement.scrollWidth;
        return Math.max(scrollWidth, this.getViewportWidth());
    },

    getViewportHeight: function() { // missing from prototype?
        var height = self.innerHeight;
        var mode = document.compatMode;

        if ( (mode || Ext.isIE) && !Ext.isOpera ) {
            height = (mode == "CSS1Compat") ?
                    document.documentElement.clientHeight : // Standards
                    document.body.clientHeight; // Quirks
        }

        return height;
    },

    getViewportWidth: function() { // missing from prototype?
        var width = self.innerWidth;  // Safari
        var mode = document.compatMode;

        if (mode || Ext.isIE) { // IE, Gecko, Opera
            width = (mode == "CSS1Compat") ?
                    document.documentElement.clientWidth : // Standards
                    document.body.clientWidth; // Quirks
        }
        return width;
    },

    isAncestor : function(p, c){ // missing from prototype?
        p = Ext.getDom(p);
        c = Ext.getDom(c);
        if (!p || !c) {return false;}

        if(p.contains && !Ext.isSafari) {
            return p.contains(c);
        }else if(p.compareDocumentPosition) {
            return !!(p.compareDocumentPosition(c) & 16);
        }else{
            var parent = c.parentNode;
            while (parent) {
                if (parent == p) {
                    return true;
                }
                else if (!parent.tagName || parent.tagName.toUpperCase() == "HTML") {
                    return false;
                }
                parent = parent.parentNode;
            }
            return false;
        }
    },

    getRegion : function(el){
        return Ext.lib.Region.getRegion(el);
    },

    getY : function(el){
        return this.getXY(el)[1];
    },

    getX : function(el){
        return this.getXY(el)[0];
    },

    getXY : function(el){ // this initially used Position.cumulativeOffset but it is not accurate enough
        var p, pe, b, scroll, bd = document.body;
        el = Ext.getDom(el);

        if (el.getBoundingClientRect) {
            b = el.getBoundingClientRect();
            scroll = fly(document).getScroll();
            return [b.left + scroll.left, b.top + scroll.top];
        }
        var x = 0, y = 0;

        p = el;

        var hasAbsolute = fly(el).getStyle("position") == "absolute";

        while (p) {

            x += p.offsetLeft;
            y += p.offsetTop;

            if (!hasAbsolute && fly(p).getStyle("position") == "absolute") {
                hasAbsolute = true;
            }

            if (Ext.isGecko) {
                pe = fly(p);

                var bt = parseInt(pe.getStyle("borderTopWidth"), 10) || 0;
                var bl = parseInt(pe.getStyle("borderLeftWidth"), 10) || 0;


                x += bl;
                y += bt;


                if (p != el && pe.getStyle('overflow') != 'visible') {
                    x += bl;
                    y += bt;
                }
            }
            p = p.offsetParent;
        }

        if (Ext.isSafari && hasAbsolute) {
            x -= bd.offsetLeft;
            y -= bd.offsetTop;
        }

        if (Ext.isGecko && !hasAbsolute) {
            var dbd = fly(bd);
            x += parseInt(dbd.getStyle("borderLeftWidth"), 10) || 0;
            y += parseInt(dbd.getStyle("borderTopWidth"), 10) || 0;
        }

        p = el.parentNode;
        while (p && p != bd) {
            if (!Ext.isOpera || (p.tagName != 'TR' && fly(p).getStyle("display") != "inline")) {
                x -= p.scrollLeft;
                y -= p.scrollTop;
            }
            p = p.parentNode;
        }
        return [x, y];
    },

    setXY : function(el, xy){ // this initially used Position.cumulativeOffset but it is not accurate enough
        el = Ext.fly(el, '_setXY');
        el.position();
        var pts = el.translatePoints(xy);
        if(xy[0] !== false){
            el.dom.style.left = pts.left + "px";
        }
        if(xy[1] !== false){
            el.dom.style.top = pts.top + "px";
        }
    },

    setX : function(el, x){
        this.setXY(el, [x, false]);
    },

    setY : function(el, y){
        this.setXY(el, [false, y]);
    }
};

Ext.lib.Event = {
    getPageX : function(e){
        return Event.pointerX(e.browserEvent || e);
    },

    getPageY : function(e){
        return Event.pointerY(e.browserEvent || e);
    },

    getXY : function(e){
        e = e.browserEvent || e;
        return [Event.pointerX(e), Event.pointerY(e)];
    },

    getTarget : function(e){
        return Event.element(e.browserEvent || e);
    },

    resolveTextNode: function(node) {
        if (node && 3 == node.nodeType) {
            return node.parentNode;
        } else {
            return node;
        }
    },

    getRelatedTarget: function(ev) { // missing from prototype?
        ev = ev.browserEvent || ev;
        var t = ev.relatedTarget;
        if (!t) {
            if (ev.type == "mouseout") {
                t = ev.toElement;
            } else if (ev.type == "mouseover") {
                t = ev.fromElement;
            }
        }

        return this.resolveTextNode(t);
    },

    on : function(el, eventName, fn){
        Event.observe(el, eventName, fn, false);
    },

    un : function(el, eventName, fn){
        Event.stopObserving(el, eventName, fn, false);
    },

    purgeElement : function(el){
        // no equiv?
    },

    preventDefault : function(e){   // missing from prototype?
        e = e.browserEvent || e;
        if(e.preventDefault) {
            e.preventDefault();
        } else {
            e.returnValue = false;
        }
    },

    stopPropagation : function(e){   // missing from prototype?
        e = e.browserEvent || e;
        if(e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.cancelBubble = true;
        }
    },

    stopEvent : function(e){
        Event.stop(e.browserEvent || e);
    },

    onAvailable : function(id, fn, scope){  // no equiv
        var start = new Date(), iid;
        var f = function(){
            if(start.getElapsed() > 10000){
                clearInterval(iid);
            }
            var el = document.getElementById(id);
            if(el){
                clearInterval(iid);
                fn.call(scope||window, el);
            }
        };
        iid = setInterval(f, 50);
    }
};

Ext.lib.Ajax = function(){
    var createSuccess = function(cb){
         return cb.success ? function(xhr){
            cb.success.call(cb.scope||window, {
                responseText: xhr.responseText,
                responseXML : xhr.responseXML,
                argument: cb.argument
            });
         } : Ext.emptyFn;
    };
    var createFailure = function(cb){
         return cb.failure ? function(xhr){
            cb.failure.call(cb.scope||window, {
                responseText: xhr.responseText,
                responseXML : xhr.responseXML,
                argument: cb.argument
            });
         } : Ext.emptyFn;
    };
    return {
        request : function(method, uri, cb, data, options){
            var o = {
                method: method,
                parameters: data || '',
                timeout: cb.timeout,
                onSuccess: createSuccess(cb),
                onFailure: createFailure(cb)
            };
            if(options){
                if(options.headers){
                    o.requestHeaders =	options.headers;
                }
                if(options.xmlData){
                    method = 'POST';
                    o.contentType = 'text/xml';
                    o.postBody = options.xmlData;
                    delete o.parameters;
                }
            }
            new Ajax.Request(uri, o);
        },

        formRequest : function(form, uri, cb, data, isUpload, sslUri){
            new Ajax.Request(uri, {
                method: Ext.getDom(form).method ||'POST',
                parameters: Form.serialize(form)+(data?'&'+data:''),
                timeout: cb.timeout,
                onSuccess: createSuccess(cb),
                onFailure: createFailure(cb)
            });
        },

        isCallInProgress : function(trans){
            return false;
        },

        abort : function(trans){
            return false;
        },

        serializeForm : function(form){
            return Form.serialize(form.dom||form);
        }
    };
}();


Ext.lib.Anim = function(){

    var easings = {
        easeOut: function(pos) {
            return 1-Math.pow(1-pos,2);
        },
        easeIn: function(pos) {
            return 1-Math.pow(1-pos,2);
        }
    };
    var createAnim = function(cb, scope){
        return {
            stop : function(skipToLast){
                this.effect.cancel();
            },

            isAnimated : function(){
                return this.effect.state == 'running';
            },

            proxyCallback : function(){
                Ext.callback(cb, scope);
            }
        };
    };
    return {
        scroll : function(el, args, duration, easing, cb, scope){
            // not supported so scroll immediately?
            var anim = createAnim(cb, scope);
            el = Ext.getDom(el);
            if(typeof args.scroll.to[0] == 'number'){
                el.scrollLeft = args.scroll.to[0];
            }
            if(typeof args.scroll.to[1] == 'number'){
                el.scrollTop = args.scroll.to[1];
            }
            anim.proxyCallback();
            return anim;
        },

        motion : function(el, args, duration, easing, cb, scope){
            return this.run(el, args, duration, easing, cb, scope);
        },

        color : function(el, args, duration, easing, cb, scope){
            return this.run(el, args, duration, easing, cb, scope);
        },

        run : function(el, args, duration, easing, cb, scope, type){
            var o = {};
            for(var k in args){
                switch(k){   // scriptaculous doesn't support, so convert these
                    case 'points':
                        var by, pts, e = Ext.fly(el, '_animrun');
                        e.position();
                        if(by = args.points.by){
                            var xy = e.getXY();
                            pts = e.translatePoints([xy[0]+by[0], xy[1]+by[1]]);
                        }else{
                            pts = e.translatePoints(args.points.to);
                        }
                        o.left = pts.left+'px';
                        o.top = pts.top+'px';
                    break;
                    case 'width':
                        o.width = args.width.to+'px';
                    break;
                    case 'height':
                        o.height = args.height.to+'px';
                    break;
                    case 'opacity':
                        o.opacity = String(args.opacity.to);
                    break;
                    default:
                        o[k] = String(args[k].to);
                    break;
                }
            }
            var anim = createAnim(cb, scope);
            anim.effect = new Effect.Morph(Ext.id(el), {
                duration: duration,
                afterFinish: anim.proxyCallback,
                transition: easings[easing] || Effect.Transitions.linear,
                style: o
            });
            return anim;
        }
    };
}();


// all lib flyweight calls use their own flyweight to prevent collisions with developer flyweights
function fly(el){
    if(!libFlyweight){
        libFlyweight = new Ext.Element.Flyweight();
    }
    libFlyweight.dom = el;
    return libFlyweight;
}

Ext.lib.Region = function(t, r, b, l) {
    this.top = t;
    this[1] = t;
    this.right = r;
    this.bottom = b;
    this.left = l;
    this[0] = l;
};

Ext.lib.Region.prototype = {
    contains : function(region) {
        return ( region.left   >= this.left   &&
                 region.right  <= this.right  &&
                 region.top    >= this.top    &&
                 region.bottom <= this.bottom    );

    },

    getArea : function() {
        return ( (this.bottom - this.top) * (this.right - this.left) );
    },

    intersect : function(region) {
        var t = Math.max( this.top,    region.top    );
        var r = Math.min( this.right,  region.right  );
        var b = Math.min( this.bottom, region.bottom );
        var l = Math.max( this.left,   region.left   );

        if (b >= t && r >= l) {
            return new Ext.lib.Region(t, r, b, l);
        } else {
            return null;
        }
    },
    union : function(region) {
        var t = Math.min( this.top,    region.top    );
        var r = Math.max( this.right,  region.right  );
        var b = Math.max( this.bottom, region.bottom );
        var l = Math.min( this.left,   region.left   );

        return new Ext.lib.Region(t, r, b, l);
    },

    adjust : function(t, l, b, r){
        this.top += t;
        this.left += l;
        this.right += r;
        this.bottom += b;
        return this;
    }
};

Ext.lib.Region.getRegion = function(el) {
    var p = Ext.lib.Dom.getXY(el);

    var t = p[1];
    var r = p[0] + el.offsetWidth;
    var b = p[1] + el.offsetHeight;
    var l = p[0];

    return new Ext.lib.Region(t, r, b, l);
};

Ext.lib.Point = function(x, y) {
   if (x instanceof Array) {
      y = x[1];
      x = x[0];
   }
    this.x = this.right = this.left = this[0] = x;
    this.y = this.top = this.bottom = this[1] = y;
};

Ext.lib.Point.prototype = new Ext.lib.Region();


// prevent IE leaks
if(Ext.isIE) {
    function fnCleanUp() {
        var p = Function.prototype;
        delete p.createSequence;
        delete p.defer;
        delete p.createDelegate;
        delete p.createCallback;
        delete p.createInterceptor;

        window.detachEvent("onunload", fnCleanUp);
    }
    window.attachEvent("onunload", fnCleanUp);
}
})();



/**
 * @class Ext.DomHelper
 * Utility class for working with DOM and/or Templates. It transparently supports using HTML fragments or DOM.
 * For more information see <a href="http://www.jackslocum.com/yui/2006/10/06/domhelper-create-elements-using-dom-html-fragments-or-templates/">this blog post with examples</a>.
 * @singleton
 */
Ext.DomHelper = function(){
    var tempTableEl = null;
    var emptyTags = /^(?:br|frame|hr|img|input|link|meta|range|spacer|wbr|area|param|col)$/i;
    var tableRe = /^table|tbody|tr|td$/i;
    
    // build as innerHTML where available
    /** @ignore */
    var createHtml = function(o){
        if(typeof o == 'string'){
            return o;
        }
        var b = "";
        if(!o.tag){
            o.tag = "div";
        }
        b += "<" + o.tag;
        for(var attr in o){
            if(attr == "tag" || attr == "children" || attr == "cn" || attr == "html" || typeof o[attr] == "function") continue;
            if(attr == "style"){
                var s = o["style"];
                if(typeof s == "function"){
                    s = s.call();
                }
                if(typeof s == "string"){
                    b += ' style="' + s + '"';
                }else if(typeof s == "object"){
                    b += ' style="';
                    for(var key in s){
                        if(typeof s[key] != "function"){
                            b += key + ":" + s[key] + ";";
                        }
                    }
                    b += '"';
                }
            }else{
                if(attr == "cls"){
                    b += ' class="' + o["cls"] + '"';
                }else if(attr == "htmlFor"){
                    b += ' for="' + o["htmlFor"] + '"';
                }else{
                    b += " " + attr + '="' + o[attr] + '"';
                }
            }
        }
        if(emptyTags.test(o.tag)){
            b += "/>";
        }else{
            b += ">";
            var cn = o.children || o.cn;
            if(cn){
                if(cn instanceof Array){
                    for(var i = 0, len = cn.length; i < len; i++) {
                        b += createHtml(cn[i], b);
                    }
                }else{
                    b += createHtml(cn, b);
                }
            }
            if(o.html){
                b += o.html;
            }
            b += "</" + o.tag + ">";
        }
        return b;
    };

    // build as dom
    /** @ignore */
    var createDom = function(o, parentNode){
        var el = document.createElement(o.tag||'div');
        var useSet = el.setAttribute ? true : false; // In IE some elements don't have setAttribute
        for(var attr in o){
            if(attr == "tag" || attr == "children" || attr == "cn" || attr == "html" || attr == "style" || typeof o[attr] == "function") continue;
            if(attr=="cls"){
                el.className = o["cls"];
            }else{
                if(useSet) el.setAttribute(attr, o[attr]);
                else el[attr] = o[attr];
            }
        }
        Ext.DomHelper.applyStyles(el, o.style);
        var cn = o.children || o.cn;
        if(cn){
            if(cn instanceof Array){
                for(var i = 0, len = cn.length; i < len; i++) {
                    createDom(cn[i], el);
                }
            }else{
                createDom(cn, el);
            }
        }
        if(o.html){
            el.innerHTML = o.html;
        }
        if(parentNode){
           parentNode.appendChild(el);
        }
        return el;
    };

    var ieTable = function(depth, s, h, e){
        tempTableEl.innerHTML = [s, h, e].join('');
        var i = -1, el = tempTableEl;
        while(++i < depth){
            el = el.firstChild;
        }
        return el;
    };

    // kill repeat to save bytes
    var ts = '<table>',
        te = '</table>',
        tbs = ts+'<tbody>',
        tbe = '</tbody>'+te,
        trs = tbs + '<tr>',
        tre = '</tr>'+tbe;

    /**
     * @ignore
     * Nasty code for IE's broken table implementation
     */
    var insertIntoTable = function(tag, where, el, html){
        if(!tempTableEl){
            tempTableEl = document.createElement('div');
        }
        var node;
        var before = null;
        if(tag == 'td'){
            if(where == 'afterbegin' || where == 'beforeend'){ // INTO a TD
                return;
            }
            if(where == 'beforebegin'){
                before = el;
                el = el.parentNode;
            } else{
                before = el.nextSibling;
                el = el.parentNode;
            }
            node = ieTable(4, trs, html, tre);
        }
        else if(tag == 'tr'){
            if(where == 'beforebegin'){
                before = el;
                el = el.parentNode;
                node = ieTable(3, tbs, html, tbe);
            } else if(where == 'afterend'){
                before = el.nextSibling;
                el = el.parentNode;
                node = ieTable(3, tbs, html, tbe);
            } else{ // INTO a TR
                if(where == 'afterbegin'){
                    before = el.firstChild;
                }
                node = ieTable(4, trs, html, tre);
            }
        } else if(tag == 'tbody'){
            if(where == 'beforebegin'){
                before = el;
                el = el.parentNode;
                node = ieTable(2, ts, html, te);
            } else if(where == 'afterend'){
                before = el.nextSibling;
                el = el.parentNode;
                node = ieTable(2, ts, html, te);
            } else{
                if(where == 'afterbegin'){
                    before = el.firstChild;
                }
                node = ieTable(3, tbs, html, tbe);
            }
        } else{ // TABLE
            if(where == 'beforebegin' || where == 'afterend'){ // OUTSIDE the table
                return;
            }
            if(where == 'afterbegin'){
                before = el.firstChild;
            }
            node = ieTable(2, ts, html, te);
        }
        el.insertBefore(node, before);
        return node;
    };

    return {
    /** True to force the use of DOM instead of html fragments @type Boolean */
    useDom : false,

    /**
     * Returns the markup for the passed Element(s) config
     * @param {Object} o The Dom object spec (and children)
     * @return {String}
     */
    markup : function(o){
        return createHtml(o);
    },

    /**
     * Applies a style specification to an element
     * @param {String/HTMLElement} el The element to apply styles to
     * @param {String/Object/Function} styles A style specification string eg "width:100px", or object in the form {width:"100px"}, or
     * a function which returns such a specification.
     */
    applyStyles : function(el, styles){
        if(styles){
           el = Ext.fly(el);
           if(typeof styles == "string"){
               var re = /\s?([a-z\-]*)\:\s?([^;]*);?/gi;
               var matches;
               while ((matches = re.exec(styles)) != null){
                   el.setStyle(matches[1], matches[2]);
               }
           }else if (typeof styles == "object"){
               for (var style in styles){
                  el.setStyle(style, styles[style]);
               }
           }else if (typeof styles == "function"){
                Ext.DomHelper.applyStyles(el, styles.call());
           }
        }
    },

    /**
     * Inserts an HTML fragment into the Dom
     * @param {String} where Where to insert the html in relation to el - beforeBegin, afterBegin, beforeEnd, afterEnd.
     * @param {HTMLElement} el The context element
     * @param {String} html The HTML fragmenet
     * @return {HTMLElement} The new node
     */
    insertHtml : function(where, el, html){
        where = where.toLowerCase();
        if(el.insertAdjacentHTML){
            if(tableRe.test(el.tagName)){
                var rs;
                if(rs = insertIntoTable(el.tagName.toLowerCase(), where, el, html)){
                    return rs;
                }
            }
            switch(where){
                case "beforebegin":
                    el.insertAdjacentHTML('BeforeBegin', html);
                    return el.previousSibling;
                case "afterbegin":
                    el.insertAdjacentHTML('AfterBegin', html);
                    return el.firstChild;
                case "beforeend":
                    el.insertAdjacentHTML('BeforeEnd', html);
                    return el.lastChild;
                case "afterend":
                    el.insertAdjacentHTML('AfterEnd', html);
                    return el.nextSibling;
            }
            throw 'Illegal insertion point -> "' + where + '"';
        }
        var range = el.ownerDocument.createRange();
        var frag;
        switch(where){
             case "beforebegin":
                range.setStartBefore(el);
                frag = range.createContextualFragment(html);
                el.parentNode.insertBefore(frag, el);
                return el.previousSibling;
             case "afterbegin":
                if(el.firstChild){
                    range.setStartBefore(el.firstChild);
                    frag = range.createContextualFragment(html);
                    el.insertBefore(frag, el.firstChild);
                    return el.firstChild;
                }else{
                    el.innerHTML = html;
                    return el.firstChild;
                }
            case "beforeend":
                if(el.lastChild){
                    range.setStartAfter(el.lastChild);
                    frag = range.createContextualFragment(html);
                    el.appendChild(frag);
                    return el.lastChild;
                }else{
                    el.innerHTML = html;
                    return el.lastChild;
                }
            case "afterend":
                range.setStartAfter(el);
                frag = range.createContextualFragment(html);
                el.parentNode.insertBefore(frag, el.nextSibling);
                return el.nextSibling;
            }
            throw 'Illegal insertion point -> "' + where + '"';
    },

    /**
     * Creates new Dom element(s) and inserts them before el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object/String} o The Dom object spec (and children) or raw HTML blob
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement/Ext.Element} The new node
     */
    insertBefore : function(el, o, returnElement){
        return this.doInsert(el, o, returnElement, "beforeBegin");
    },

    /**
     * Creates new Dom element(s) and inserts them after el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} o The Dom object spec (and children)
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement/Ext.Element} The new node
     */
    insertAfter : function(el, o, returnElement){
        return this.doInsert(el, o, returnElement, "afterEnd", "nextSibling");
    },

    /**
     * Creates new Dom element(s) and inserts them as the first child of el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object/String} o The Dom object spec (and children) or raw HTML blob
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement/Ext.Element} The new node
     */
    insertFirst : function(el, o, returnElement){
        return this.doInsert(el, o, returnElement, "afterBegin");
    },

    // private
    doInsert : function(el, o, returnElement, pos, sibling){
        el = Ext.getDom(el);
        var newNode;
        if(this.useDom){
            newNode = createDom(o, null);
            el.parentNode.insertBefore(newNode, sibling ? el[sibling] : el);
        }else{
            var html = createHtml(o);
            newNode = this.insertHtml(pos, el, html);
        }
        return returnElement ? Ext.get(newNode, true) : newNode;
    },

    /**
     * Creates new Dom element(s) and appends them to el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object/String} o The Dom object spec (and children) or raw HTML blob
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement/Ext.Element} The new node
     */
    append : function(el, o, returnElement){
        el = Ext.getDom(el);
        var newNode;
        if(this.useDom){
            newNode = createDom(o, null);
            el.appendChild(newNode);
        }else{
            var html = createHtml(o);
            newNode = this.insertHtml("beforeEnd", el, html);
        }
        return returnElement ? Ext.get(newNode, true) : newNode;
    },

    /**
     * Creates new Dom element(s) and overwrites the contents of el with them
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object/String} o The Dom object spec (and children) or raw HTML blob
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement/Ext.Element} The new node
     */
    overwrite : function(el, o, returnElement){
        el = Ext.getDom(el);
        el.innerHTML = createHtml(o);
        return returnElement ? Ext.get(el.firstChild, true) : el.firstChild;
    },

    /**
     * Creates a new Ext.DomHelper.Template from the Dom object spec
     * @param {Object} o The Dom object spec (and children)
     * @return {Ext.DomHelper.Template} The new template
     */
    createTemplate : function(o){
        var html = createHtml(o);
        return new Ext.Template(html);
    }
    };
}();




/**
 * @class Ext.util.Observable
 * Abstract base class that provides a common interface for publishing events. Subclasses are expected to
 * to have a property "events" with all the events defined.<br>
 * For example:
 * <pre><code>
 Employee = function(name){
    this.name = name;
    this.addEvents({
        "fired" : true,
        "quit" : true
    });
 }
 Ext.extend(Employee, Ext.util.Observable);
</code></pre>
 */
Ext.util.Observable = function(){
    if(this.listeners){
        this.on(this.listeners);
        delete this.listeners;
    }
};
Ext.util.Observable.prototype = {
    /**
     * Fires the specified event with the passed parameters (minus the event name).
     * @param {String} eventName
     * @param {Object...} args Variable number of parameters are passed to handlers
     * @return {Boolean} returns false if any of the handlers return false otherwise it returns true
     */
    fireEvent : function(){
        var ce = this.events[arguments[0].toLowerCase()];
        if(typeof ce == "object"){
            return ce.fire.apply(ce, Array.prototype.slice.call(arguments, 1));
        }else{
            return true;
        }
    },

    // private
    filterOptRe : /^(?:scope|delay|buffer|single)$/,

    /**
     * Appends an event handler to this component
     * @param {String}   eventName The type of event to listen for
     * @param {Function} handler The method the event invokes
     * @param {Object}   scope (optional) The scope in which to execute the handler
     * function. The handler function's "this" context.
     * @param {Object}   options (optional) An object containing handler configuration
     * properties. This may contain any of the following properties:<ul>
     * <li>scope {Object} The scope in which to execute the handler function. The handler function's "this" context.</li>
     * <li>delay {Number} The number of milliseconds to delay the invocation of the handler after te event fires.</li>
     * <li>single {Boolean} True to add a handler to handle just the next firing of the event, and then remove itself.</li>
     * <li>buffer {Number} Causes the handler to be scheduled to run in an {@link Ext.util.DelayedTask} delayed
     * by the specified number of milliseconds. If the event fires again within that time, the original
     * handler is <em>not</em> invoked, but the new handler is scheduled in its place.</li>
     * </ul><br>
     * <p>
     * <b>Combining Options</b><br>
     * Using the options argument, it is possible to combine different types of listeners:<br>
     * <br>
     * A normalized, delayed, one-time listener that auto stops the event and passes a custom argument (forumId)
		<pre><code>
		el.on('click', this.onClick, this, {
 			single: true,
    		delay: 100,
    		forumId: 4
		});
		</code></pre>
     * <p>
     * <b>Attaching multiple handlers in 1 call</b><br>
     * The method also allows for a single argument to be passed which is a config object containing properties
     * which specify multiple handlers.
     * <pre><code>
		el.on({
			'click': {
        		fn: this.onClick,
        		scope: this,
        		delay: 100
    		}, 
    		'mouseover': {
        		fn: this.onMouseOver,
        		scope: this
    		},
    		'mouseout': {
        		fn: this.onMouseOut,
        		scope: this
    		}
		});
		</code></pre>
     * <p>
     * Or a shorthand syntax which passes the same scope object to all handlers:
     	<pre><code>
		el.on({
			'click': this.onClick,
    		'mouseover': this.onMouseOver,
    		'mouseout': this.onMouseOut,
    		scope: this
		});
		</code></pre>
     */
    addListener : function(eventName, fn, scope, o){
        if(typeof eventName == "object"){
            o = eventName;
            for(var e in o){
                if(this.filterOptRe.test(e)){
                    continue;
                }
                if(typeof o[e] == "function"){
                    // shared options
                    this.addListener(e, o[e], o.scope,  o);
                }else{
                    // individual options
                    this.addListener(e, o[e].fn, o[e].scope, o[e]);
                }
            }
            return;
        }
        o = (!o || typeof o == "boolean") ? {} : o;
        eventName = eventName.toLowerCase();
        var ce = this.events[eventName] || true;
        if(typeof ce == "boolean"){
            ce = new Ext.util.Event(this, eventName);
            this.events[eventName] = ce;
        }
        ce.addListener(fn, scope, o);
    },

    /**
     * Removes a listener
     * @param {String}   eventName     The type of event to listen for
     * @param {Function} handler        The handler to remove
     * @param {Object}   scope  (optional) The scope (this object) for the handler
     */
    removeListener : function(eventName, fn, scope){
        var ce = this.events[eventName.toLowerCase()];
        if(typeof ce == "object"){
            ce.removeListener(fn, scope);
        }
    },

    /**
     * Removes all listeners for this object
     */
    purgeListeners : function(){
        for(var evt in this.events){
            if(typeof this.events[evt] == "object"){
                 this.events[evt].clearListeners();
            }
        }
    },

    relayEvents : function(o, events){
        var createHandler = function(ename){
            return function(){
                return this.fireEvent.apply(this, Ext.combine(ename, Array.prototype.slice.call(arguments, 0)));
            };
        };
        for(var i = 0, len = events.length; i < len; i++){
            var ename = events[i];
            if(!this.events[ename]){ this.events[ename] = true; };
            o.on(ename, createHandler(ename), this);
        }
    },

    /**
     * Used to define events on this Observable
     * @param {Object} object The object with the events defined
     */
    addEvents : function(o){
        if(!this.events){
            this.events = {};
        }
        Ext.applyIf(this.events, o);
    },

    /**
     * Checks to see if this object has any listeners for a specified event
     * @param {String} eventName The name of the event to check for
     * @return {Boolean} True if the event is being listened for, else false
     */
    hasListener : function(eventName){
        var e = this.events[eventName];
        return typeof e == "object" && e.listeners.length > 0;
    }
};
/**
 * Appends an event handler to this element (shorthand for addListener)
 * @param {String}   eventName     The type of event to listen for
 * @param {Function} handler        The method the event invokes
 * @param {Object}   scope (optional) The scope in which to execute the handler
 * function. The handler function's "this" context.
 * @param {Object}   options  (optional)
 * @method
 */
Ext.util.Observable.prototype.on = Ext.util.Observable.prototype.addListener;
/**
 * Removes a listener (shorthand for removeListener)
 * @param {String}   eventName     The type of event to listen for
 * @param {Function} handler        The handler to remove
 * @param {Object}   scope  (optional) The scope (this object) for the handler
 * @method
 */
Ext.util.Observable.prototype.un = Ext.util.Observable.prototype.removeListener;

/**
 * Starts capture on the specified Observable. All events will be passed
 * to the supplied function with the event name + standard signature of the event
 * <b>before</b> the event is fired. If the supplied function returns false,
 * the event will not fire.
 * @param {Observable} o The Observable to capture
 * @param {Function} fn The function to call
 * @param {Object} scope (optional) The scope (this object) for the fn
 * @static
 */
Ext.util.Observable.capture = function(o, fn, scope){
    o.fireEvent = o.fireEvent.createInterceptor(fn, scope);
};

/**
 * Removes <b>all</b> added captures from the Observable.
 * @param {Observable} o The Observable to release
 * @static
 */
Ext.util.Observable.releaseCapture = function(o){
    o.fireEvent = Ext.util.Observable.prototype.fireEvent;
};

(function(){

    var createBuffered = function(h, o, scope){
        var task = new Ext.util.DelayedTask();
        return function(){
            task.delay(o.buffer, h, scope, Array.prototype.slice.call(arguments, 0));
        };
    };

    var createSingle = function(h, e, fn, scope){
        return function(){
            e.removeListener(fn, scope);
            return h.apply(scope, arguments);
        };
    };

    var createDelayed = function(h, o, scope){
        return function(){
            var args = Array.prototype.slice.call(arguments, 0);
            setTimeout(function(){
                h.apply(scope, args);
            }, o.delay || 10);
        };
    };

    Ext.util.Event = function(obj, name){
        this.name = name;
        this.obj = obj;
        this.listeners = [];
    };

    Ext.util.Event.prototype = {
        addListener : function(fn, scope, options){
            var o = options || {};
            scope = scope || this.obj;
            if(!this.isListening(fn, scope)){
                var l = {fn: fn, scope: scope, options: o};
                var h = fn;
                if(o.delay){
                    h = createDelayed(h, o, scope);
                }
                if(o.single){
                    h = createSingle(h, this, fn, scope);
                }
                if(o.buffer){
                    h = createBuffered(h, o, scope);
                }
                l.fireFn = h;
                if(!this.firing){ // if we are currently firing this event, don't disturb the listener loop
                    this.listeners.push(l);
                }else{
                    this.listeners = this.listeners.slice(0);
                    this.listeners.push(l);
                }
            }
        },

        findListener : function(fn, scope){
            scope = scope || this.obj;
            var ls = this.listeners;
            for(var i = 0, len = ls.length; i < len; i++){
                var l = ls[i];
                if(l.fn == fn && l.scope == scope){
                    return i;
                }
            }
            return -1;
        },

        isListening : function(fn, scope){
            return this.findListener(fn, scope) != -1;
        },

        removeListener : function(fn, scope){
            var index;
            if((index = this.findListener(fn, scope)) != -1){
                if(!this.firing){
                    this.listeners.splice(index, 1);
                }else{
                    this.listeners = this.listeners.slice(0);
                    this.listeners.splice(index, 1);
                }
                return true;
            }
            return false;
        },

        clearListeners : function(){
            this.listeners = [];
        },

        fire : function(){
            var ls = this.listeners, scope, len = ls.length;
            if(len > 0){
                this.firing = true;
                var args = Array.prototype.slice.call(arguments, 0);
                for(var i = 0; i < len; i++){
                    var l = ls[i];
                    if(l.fireFn.apply(l.scope||this.obj||window, arguments) === false){
                        this.firing = false;
                        return false;
                    }
                }
                this.firing = false;
            }
            return true;
        }
    };
})();



/**
 * @class Ext.EventManager
 * Registers event handlers that want to receive a normalized EventObject instead of the standard browser event and provides 
 * several useful events directly.
 * See {@link Ext.EventObject} for more details on normalized event objects.
 * @singleton
 */
Ext.EventManager = function(){
    var docReadyEvent, docReadyProcId, docReadyState = false;
    var resizeEvent, resizeTask, textEvent, textSize;
    var E = Ext.lib.Event;
    var D = Ext.lib.Dom;


    var fireDocReady = function(){
        if(!docReadyState){
            docReadyState = true;
            Ext.isReady = true;
            if(docReadyProcId){
                clearInterval(docReadyProcId);
            }
            if(Ext.isGecko || Ext.isOpera) {
                document.removeEventListener("DOMContentLoaded", fireDocReady, false);
            }
            if(Ext.isIE){
                var defer = document.getElementById("ie-deferred-loader");
                if(defer){
                    defer.onreadystatechange = null;
                    defer.parentNode.removeChild(defer);
                }
            }
            if(docReadyEvent){
                docReadyEvent.fire();
                docReadyEvent.clearListeners();
            }
        }
    };
    
    var initDocReady = function(){
        docReadyEvent = new Ext.util.Event();
        if(Ext.isGecko || Ext.isOpera) {
            document.addEventListener("DOMContentLoaded", fireDocReady, false);
        }else if(Ext.isIE){
            document.write("<s"+'cript id="ie-deferred-loader" defer="defer" src="/'+'/:"></s'+"cript>");
            var defer = document.getElementById("ie-deferred-loader");
            defer.onreadystatechange = function(){
                if(this.readyState == "complete"){
                    fireDocReady();
                }
            };
        }else if(Ext.isSafari){ 
            docReadyProcId = setInterval(function(){
                var rs = document.readyState;
                if(rs == "complete") {
                    fireDocReady();     
                 }
            }, 10);
        }
        // no matter what, make sure it fires on load
        E.on(window, "load", fireDocReady);
    };

    var createBuffered = function(h, o){
        var task = new Ext.util.DelayedTask(h);
        return function(e){
            // create new event object impl so new events don't wipe out properties
            e = new Ext.EventObjectImpl(e);
            task.delay(o.buffer, h, null, [e]);
        };
    };

    var createSingle = function(h, el, ename, fn){
        return function(e){
            Ext.EventManager.removeListener(el, ename, fn);
            h(e);
        };
    };

    var createDelayed = function(h, o){
        return function(e){
            // create new event object impl so new events don't wipe out properties
            e = new Ext.EventObjectImpl(e);
            setTimeout(function(){
                h(e);
            }, o.delay || 10);
        };
    };

    var listen = function(element, ename, opt, fn, scope){
        var o = (!opt || typeof opt == "boolean") ? {} : opt;
        fn = fn || o.fn; scope = scope || o.scope;
        var el = Ext.getDom(element);
        if(!el){
            throw "Error listening for \"" + ename + '\". Element "' + element + '" doesn\'t exist.';
        }
        var h = function(e){
            e = Ext.EventObject.setEvent(e);
            var t;
            if(o.delegate){
                t = e.getTarget(o.delegate, el);
                if(!t){
                    return;
                }
            }else{
                t = e.target;
            }
            if(o.stopEvent === true){
                e.stopEvent();
            }
            if(o.preventDefault === true){
               e.preventDefault();
            }
            if(o.stopPropagation === true){
                e.stopPropagation();
            }

            if(o.normalized === false){
                e = e.browserEvent;
            }

            fn.call(scope || el, e, t, o);
        };
        if(o.delay){
            h = createDelayed(h, o);
        }
        if(o.single){
            h = createSingle(h, el, ename, fn);
        }
        if(o.buffer){
            h = createBuffered(h, o);
        }
        fn._handlers = fn._handlers || [];
        fn._handlers.push([Ext.id(el), ename, h]);

        E.on(el, ename, h);
        if(ename == "mousewheel" && el.addEventListener){ // workaround for jQuery
            el.addEventListener("DOMMouseScroll", h, false);
            E.on(window, 'beforeunload', function(){
                el.removeEventListener("DOMMouseScroll", h, false);
            });
        }
        if(ename == "mousedown" && el == document){ // fix stopped mousedowns on the document
            Ext.EventManager.stoppedMouseDownEvent.addListener(h);
        }
        return h;
    };

    var stopListening = function(el, ename, fn){
        var id = Ext.id(el), hds = fn._handlers, hd = fn;
        if(hds){
            for(var i = 0, len = hds.length; i < len; i++){
                var h = hds[i];
                if(h[0] == id && h[1] == ename){
                    hd = h[2];
                    hds.splice(i, 1);
                    break;
                }
            }
        }
        E.un(el, ename, hd);
        el = Ext.getDom(el);
        if(ename == "mousewheel" && el.addEventListener){
            el.removeEventListener("DOMMouseScroll", hd, false);
        }
        if(ename == "mousedown" && el == document){ // fix stopped mousedowns on the document
            Ext.EventManager.stoppedMouseDownEvent.removeListener(hd);
        }
    };

    var propRe = /^(?:scope|delay|buffer|single|stopEvent|preventDefault|stopPropagation|normalized|args|delegate)$/;
    var pub = {
        
        /** 
         * This is no longer needed and is deprecated. Places a simple wrapper around an event handler to override the browser event
         * object with a Ext.EventObject
         * @param {Function} fn        The method the event invokes
         * @param {Object}   scope    An object that becomes the scope of the handler
         * @param {boolean}  override If true, the obj passed in becomes
         *                             the execution scope of the listener
         * @return {Function} The wrapped function
         * @deprecated
         */
        wrap : function(fn, scope, override){
            return function(e){
                Ext.EventObject.setEvent(e);
                fn.call(override ? scope || window : window, Ext.EventObject, scope);
            };
        },
        
        /**
     * Appends an event handler to an element (shorthand for addListener)
     * @param {String/HTMLElement}   element        The html element or id to assign the
     * @param {String}   eventName The type of event to listen for
     * @param {Function} handler The method the event invokes
     * @param {Object}   scope (optional) The scope in which to execute the handler
     * function. The handler function's "this" context.
     * @param {Object}   options (optional) An object containing handler configuration
     * properties. This may contain any of the following properties:<ul>
     * <li>scope {Object} The scope in which to execute the handler function. The handler function's "this" context.</li>
     * <li>delegate {String} A simple selector to filter the target or look for a descendant of the target</li>
     * <li>stopEvent {Boolean} True to stop the event. That is stop propagation, and prevent the default action.</li>
     * <li>preventDefault {Boolean} True to prevent the default action</li>
     * <li>stopPropagation {Boolean} True to prevent event propagation</li>
     * <li>normalized {Boolean} False to pass a browser event to the handler function instead of an Ext.EventObject</li>
     * <li>delay {Number} The number of milliseconds to delay the invocation of the handler after te event fires.</li>
     * <li>single {Boolean} True to add a handler to handle just the next firing of the event, and then remove itself.</li>
     * <li>buffer {Number} Causes the handler to be scheduled to run in an {@link Ext.util.DelayedTask} delayed
     * by the specified number of milliseconds. If the event fires again within that time, the original
     * handler is <em>not</em> invoked, but the new handler is scheduled in its place.</li>
     * </ul><br>
     * <p>
     * <b>Combining Options</b><br>
     * Using the options argument, it is possible to combine different types of listeners:<br>
     * <br>
     * A normalized, delayed, one-time listener that auto stops the event and passes a custom argument (forumId)<div style="margin: 5px 20px 20px;">
     * Code:<pre><code>
el.on('click', this.onClick, this, {
    single: true,
    delay: 100,
    stopEvent : true,
    forumId: 4
});</code></pre>
     * <p>
     * <b>Attaching multiple handlers in 1 call</b><br>
      * The method also allows for a single argument to be passed which is a config object containing properties
     * which specify multiple handlers.
     * <p>
     * Code:<pre><code>
el.on({
    'click' : {
        fn: this.onClick
        scope: this,
        delay: 100
    },
    'mouseover' : {
        fn: this.onMouseOver
        scope: this
    },
    'mouseout' : {
        fn: this.onMouseOut
        scope: this
    }
});</code></pre>
     * <p>
     * Or a shorthand syntax:<br>
     * Code:<pre><code>
el.on({
    'click' : this.onClick,
    'mouseover' : this.onMouseOver,
    'mouseout' : this.onMouseOut
    scope: this
});</code></pre>
     */
        addListener : function(element, eventName, fn, scope, options){
            if(typeof eventName == "object"){
                var o = eventName;
                for(var e in o){
                    if(propRe.test(e)){
                        continue;
                    }
                    if(typeof o[e] == "function"){
                        // shared options
                        listen(element, e, o, o[e], o.scope);
                    }else{
                        // individual options
                        listen(element, e, o[e]);
                    }
                }
                return;
            }
            return listen(element, eventName, options, fn, scope);
        },
        
        /**
         * Removes an event handler
         *
         * @param {String/HTMLElement}   element        The id or html element to remove the 
         *                             event from
         * @param {String}   eventName     The type of event
         * @param {Function} fn
         * @return {Boolean} True if a listener was actually removed
         */
        removeListener : function(element, eventName, fn){
            return stopListening(element, eventName, fn);
        },
        
        /**
         * Fires when the document is ready (before onload and before images are loaded). Can be 
         * accessed shorthanded Ext.onReady().
         * @param {Function} fn        The method the event invokes
         * @param {Object}   scope    An  object that becomes the scope of the handler
         * @param {boolean}  options
         */
        onDocumentReady : function(fn, scope, options){
            if(docReadyState){ // if it already fired
                docReadyEvent.addListener(fn, scope, options);
                docReadyEvent.fire();
                docReadyEvent.clearListeners();
                return;
            }
            if(!docReadyEvent){
                initDocReady();
            }
            docReadyEvent.addListener(fn, scope, options);
        },
        
        /**
         * Fires when the window is resized and provides resize event buffering (50 milliseconds), passes new viewport width and height to handlers.
         * @param {Function} fn        The method the event invokes
         * @param {Object}   scope    An object that becomes the scope of the handler
         * @param {boolean}  options
         */
        onWindowResize : function(fn, scope, options){
            if(!resizeEvent){
                resizeEvent = new Ext.util.Event();
                resizeTask = new Ext.util.DelayedTask(function(){
                    resizeEvent.fire(D.getViewWidth(), D.getViewHeight());
                });
                E.on(window, "resize", function(){
                    if(Ext.isIE){
                        resizeTask.delay(50);
                    }else{
                        resizeEvent.fire(D.getViewWidth(), D.getViewHeight());
                    }
                });
            }
            resizeEvent.addListener(fn, scope, options);
        },

        /**
         * Fires when the user changes the active text size. Handler gets called with 2 params, the old size and the new size.
         * @param {Function} fn        The method the event invokes
         * @param {Object}   scope    An object that becomes the scope of the handler
         * @param {boolean}  options
         */
        onTextResize : function(fn, scope, options){
            if(!textEvent){
                textEvent = new Ext.util.Event();
                var textEl = new Ext.Element(document.createElement('div'));
                textEl.dom.className = 'x-text-resize';
                textEl.dom.innerHTML = 'X';
                textEl.appendTo(document.body);
                textSize = textEl.dom.offsetHeight;
                setInterval(function(){
                    if(textEl.dom.offsetHeight != textSize){
                        textEvent.fire(textSize, textSize = textEl.dom.offsetHeight);
                    }
                }, this.textResizeInterval);
            }
            textEvent.addListener(fn, scope, options);
        },

        /**
         * Removes the passed window resize listener.
         * @param {Function} fn        The method the event invokes
         * @param {Object}   scope    The scope of handler
         */
        removeResizeListener : function(fn, scope){
            if(resizeEvent){
                resizeEvent.removeListener(fn, scope);
            }
        },

        // private
        fireResize : function(){
            if(resizeEvent){
                resizeEvent.fire(D.getViewWidth(), D.getViewHeight());
            }   
        },
        /**
         * Url used for onDocumentReady with using SSL (defaults to Ext.SSL_SECURE_URL)
         */
        ieDeferSrc : false,
        /**
         * The frequency, in milliseconds, to check for text resize events (defaults to 50)
         */
        textResizeInterval : 50
    };
     /**
     * Appends an event handler to an element (shorthand for addListener)
     * @param {String/HTMLElement}   element        The html element or id to assign the
     * @param {String}   eventName The type of event to listen for
     * @param {Function} handler The method the event invokes
     * @param {Object}   scope (optional) The scope in which to execute the handler
     * function. The handler function's "this" context.
     * @param {Object}   options (optional) An object containing handler configuration
     * properties. This may contain any of the following properties:<ul>
     * <li>scope {Object} The scope in which to execute the handler function. The handler function's "this" context.</li>
     * <li>delegate {String} A simple selector to filter the target or look for a descendant of the target</li>
     * <li>stopEvent {Boolean} True to stop the event. That is stop propagation, and prevent the default action.</li>
     * <li>preventDefault {Boolean} True to prevent the default action</li>
     * <li>stopPropagation {Boolean} True to prevent event propagation</li>
     * <li>normalized {Boolean} False to pass a browser event to the handler function instead of an Ext.EventObject</li>
     * <li>delay {Number} The number of milliseconds to delay the invocation of the handler after te event fires.</li>
     * <li>single {Boolean} True to add a handler to handle just the next firing of the event, and then remove itself.</li>
     * <li>buffer {Number} Causes the handler to be scheduled to run in an {@link Ext.util.DelayedTask} delayed
     * by the specified number of milliseconds. If the event fires again within that time, the original
     * handler is <em>not</em> invoked, but the new handler is scheduled in its place.</li>
     * </ul><br>
     * <p>
     * <b>Combining Options</b><br>
     * Using the options argument, it is possible to combine different types of listeners:<br>
     * <br>
     * A normalized, delayed, one-time listener that auto stops the event and passes a custom argument (forumId)<div style="margin: 5px 20px 20px;">
     * Code:<pre><code>
el.on('click', this.onClick, this, {
    single: true,
    delay: 100,
    stopEvent : true,
    forumId: 4
});</code></pre>
     * <p>
     * <b>Attaching multiple handlers in 1 call</b><br>
      * The method also allows for a single argument to be passed which is a config object containing properties
     * which specify multiple handlers.
     * <p>
     * Code:<pre><code>
el.on({
    'click' : {
        fn: this.onClick
        scope: this,
        delay: 100
    },
    'mouseover' : {
        fn: this.onMouseOver
        scope: this
    },
    'mouseout' : {
        fn: this.onMouseOut
        scope: this
    }
});</code></pre>
     * <p>
     * Or a shorthand syntax:<br>
     * Code:<pre><code>
el.on({
    'click' : this.onClick,
    'mouseover' : this.onMouseOver,
    'mouseout' : this.onMouseOut
    scope: this
});</code></pre>
     */
    pub.on = pub.addListener;
    pub.un = pub.removeListener;

    pub.stoppedMouseDownEvent = new Ext.util.Event();
    return pub;
}();
/**
  * Fires when the document is ready (before onload and before images are loaded).  Shorthand of {@link Ext.EventManager#onDocumentReady}.
  * @param {Function} fn        The method the event invokes
  * @param {Object}   scope    An  object that becomes the scope of the handler
  * @param {boolean}  override If true, the obj passed in becomes
  *                             the execution scope of the listener
  * @member Ext
  * @method onReady
 */
Ext.onReady = Ext.EventManager.onDocumentReady;

Ext.onReady(function(){
    var bd = Ext.get(document.body);
    if(!bd){ return; }

    var cls = [
            Ext.isIE ? "ext-ie"
            : Ext.isGecko ? "ext-gecko"
            : Ext.isOpera ? "ext-opera"
            : Ext.isSafari ? "ext-safari" : ""];

    if(Ext.isMac){
        cls.push("ext-mac");
    }
    if(Ext.isLinux){
        cls.push("ext-linux");
    }
    if(Ext.isBorderBox){
        cls.push('ext-border-box');
    }
    if(Ext.isStrict){ // add to the parent to allow for selectors like ".ext-strict .ext-ie"
        var p = bd.dom.parentNode;
        if(p){
            p.className += ' ext-strict';
        }
    }
    bd.addClass(cls.join(' '));
});

/**
 * @class Ext.EventObject
 * EventObject exposes the Yahoo! UI Event functionality directly on the object
 * passed to your event handler. It exists mostly for convenience. It also fixes the annoying null checks automatically to cleanup your code 
 * Example:
 * <pre><code>
 function handleClick(e){ // e is not a standard event object, it is a Ext.EventObject
    e.preventDefault();
    var target = e.getTarget();
    ...
 }
 var myDiv = Ext.get("myDiv");
 myDiv.on("click", handleClick);
 //or
 Ext.EventManager.on("myDiv", 'click', handleClick);
 Ext.EventManager.addListener("myDiv", 'click', handleClick);
 </code></pre>
 * @singleton
 */
Ext.EventObject = function(){
    
    var E = Ext.lib.Event;
    
    // safari keypress events for special keys return bad keycodes
    var safariKeys = {
        63234 : 37, // left
        63235 : 39, // right
        63232 : 38, // up
        63233 : 40, // down
        63276 : 33, // page up
        63277 : 34, // page down
        63272 : 46, // delete
        63273 : 36, // home
        63275 : 35  // end
    };

    // normalize button clicks
    var btnMap = Ext.isIE ? {1:0,4:1,2:2} :
                (Ext.isSafari ? {1:0,2:1,3:2} : {0:0,1:1,2:2});

    Ext.EventObjectImpl = function(e){
        if(e){
            this.setEvent(e.browserEvent || e);
        }
    };
    Ext.EventObjectImpl.prototype = {
        /** The normal browser event */
        browserEvent : null,
        /** The button pressed in a mouse event */
        button : -1,
        /** True if the shift key was down during the event */
        shiftKey : false,
        /** True if the control key was down during the event */
        ctrlKey : false,
        /** True if the alt key was down during the event */
        altKey : false,

        /** Key constant @type Number */
        BACKSPACE : 8,
        /** Key constant @type Number */
        TAB : 9,
        /** Key constant @type Number */
        RETURN : 13,
        /** Key constant @type Number */
        ENTER : 13,
        /** Key constant @type Number */
        SHIFT : 16,
        /** Key constant @type Number */
        CONTROL : 17,
        /** Key constant @type Number */
        ESC : 27,
        /** Key constant @type Number */
        SPACE : 32,
        /** Key constant @type Number */
        PAGEUP : 33,
        /** Key constant @type Number */
        PAGEDOWN : 34,
        /** Key constant @type Number */
        END : 35,
        /** Key constant @type Number */
        HOME : 36,
        /** Key constant @type Number */
        LEFT : 37,
        /** Key constant @type Number */
        UP : 38,
        /** Key constant @type Number */
        RIGHT : 39,
        /** Key constant @type Number */
        DOWN : 40,
        /** Key constant @type Number */
        DELETE : 46,
        /** Key constant @type Number */
        F5 : 116,

           /** @private */
        setEvent : function(e){
            if(e == this || (e && e.browserEvent)){ // already wrapped
                return e;
            }
            this.browserEvent = e;
            if(e){
                // normalize buttons
                this.button = e.button ? btnMap[e.button] : (e.which ? e.which-1 : -1);
                if(e.type == 'click' && this.button == -1){
                    this.button = 0;
                }
                this.type = e.type;
                this.shiftKey = e.shiftKey;
                // mac metaKey behaves like ctrlKey
                this.ctrlKey = e.ctrlKey || e.metaKey;
                this.altKey = e.altKey;
                // in getKey these will be normalized for the mac
                this.keyCode = e.keyCode;
                this.charCode = e.charCode;
                // cache the target for the delayed and or buffered events
                this.target = E.getTarget(e);
                // same for XY
                this.xy = E.getXY(e);
            }else{
                this.button = -1;
                this.shiftKey = false;
                this.ctrlKey = false;
                this.altKey = false;
                this.keyCode = 0;
                this.charCode =0;
                this.target = null;
                this.xy = [0, 0];
            }
            return this;
        },

        /**
         * Stop the event (preventDefault and stopPropagation)
         */
        stopEvent : function(){
            if(this.browserEvent){
                if(this.browserEvent.type == 'mousedown'){
                    Ext.EventManager.stoppedMouseDownEvent.fire(this);
                }
                E.stopEvent(this.browserEvent);
            }
        },

        /**
         * Prevents the browsers default handling of the event.
         */
        preventDefault : function(){
            if(this.browserEvent){
                E.preventDefault(this.browserEvent);
            }
        },

        /** @private */
        isNavKeyPress : function(){
            var k = this.keyCode;
            k = Ext.isSafari ? (safariKeys[k] || k) : k;
            return (k >= 33 && k <= 40) || k == this.RETURN || k == this.TAB || k == this.ESC;
        },

        isSpecialKey : function(){
            var k = this.keyCode;
            return (this.type == 'keypress' && this.ctrlKey) || k == 9 || k == 13  || k == 40 || k == 27 ||
            (k == 16) || (k == 17) ||
            (k >= 18 && k <= 20) ||
            (k >= 33 && k <= 35) ||
            (k >= 36 && k <= 39) ||
            (k >= 44 && k <= 45);
        },
        /**
         * Cancels bubbling of the event.
         */
        stopPropagation : function(){
            if(this.browserEvent){
                if(this.type == 'mousedown'){
                    Ext.EventManager.stoppedMouseDownEvent.fire(this);
                }
                E.stopPropagation(this.browserEvent);
            }
        },

        /**
         * Gets the key code for the event.
         * @return {Number}
         */
        getCharCode : function(){
            return this.charCode || this.keyCode;
        },

        /**
         * Returns a normalized keyCode for the event.
         * @return {Number} The key code
         */
        getKey : function(){
            var k = this.keyCode || this.charCode;
            return Ext.isSafari ? (safariKeys[k] || k) : k;
        },

        /**
         * Gets the x coordinate of the event.
         * @return {Number}
         */
        getPageX : function(){
            return this.xy[0];
        },

        /**
         * Gets the y coordinate of the event.
         * @return {Number}
         */
        getPageY : function(){
            return this.xy[1];
        },

        /**
         * Gets the time of the event.
         * @return {Number}
         */
        getTime : function(){
            if(this.browserEvent){
                return E.getTime(this.browserEvent);
            }
            return null;
        },

        /**
         * Gets the page coordinates of the event.
         * @return {Array} The xy values like [x, y]
         */
        getXY : function(){
            return this.xy;
        },

        /**
         * Gets the target for the event.
         * @param {String} selector (optional) A simple selector to filter the target or look for an ancestor of the target
         * @param {Number/String/HTMLElement/Element} maxDepth (optional) The max depth to
                search as a number or element (defaults to 10 || document.body)
         * @param {Boolean} returnEl (optional) True to return a Ext.Element object instead of DOM node
         * @return {HTMLelement}
         */
        getTarget : function(selector, maxDepth, returnEl){
            return selector ? Ext.fly(this.target).findParent(selector, maxDepth, returnEl) : this.target;
        },
        /**
         * Gets the related target.
         * @return {HTMLElement}
         */
        getRelatedTarget : function(){
            if(this.browserEvent){
                return E.getRelatedTarget(this.browserEvent);
            }
            return null;
        },

        /**
         * Normalizes mouse wheel delta across browsers
         * @return {Number} The delta
         */
        getWheelDelta : function(){
            var e = this.browserEvent;
            var delta = 0;
            if(e.wheelDelta){ /* IE/Opera. */
                delta = e.wheelDelta/120;
            }else if(e.detail){ /* Mozilla case. */
                delta = -e.detail/3;
            }
            return delta;
        },

        /**
         * Returns true if the control, meta, shift or alt key was pressed during this event.
         * @return {Boolean}
         */
        hasModifier : function(){
            return !!((this.ctrlKey || this.altKey) || this.shiftKey);
        },

        /**
         * Returns true if the target of this event equals el or is a child of el
         * @param {String/HTMLElement/Element} el
         * @param {Boolean} related (optional) true to test if the related target is within el instead of the target
         * @return {Boolean}
         */
        within : function(el, related){
            var t = this[related ? "getRelatedTarget" : "getTarget"]();
            return t && Ext.fly(el).contains(t);
        },

        getPoint : function(){
            return new Ext.lib.Point(this.xy[0], this.xy[1]);
        }
    };

    return new Ext.EventObjectImpl();
}();
            
    



/**
 * @class Ext.Element
 * Represents an Element in the DOM.<br><br>
 * Usage:<br>
<pre><code>
var el = Ext.get("my-div");

// or with getEl
var el = getEl("my-div");

// or with a DOM element
var el = Ext.get(myDivElement);
</code></pre>
 * Using Ext.get() or getEl() instead of calling the constructor directly ensures you get the same object
 * each call instead of constructing a new one.<br><br>
 * <b>Animations</b><br />
 * Many of the functions for manipulating an element have an optional "animate" parameter. The animate parameter
 * should either be a boolean (true) or an object literal with animation options. The animation options are:
<pre>
Option    Default   Description
--------- --------  ---------------------------------------------
duration  .35       The duration of the animation in seconds
easing    easeOut   The YUI easing method
callback  none      A function to execute when the anim completes
scope     this      The scope (this) of the callback function
</pre>
* Also, the Anim object being used for the animation will be set on your options object as "anim", which allows you to stop or
* manipulate the animation. Here's an example:
<pre><code>
var el = Ext.get("my-div");

// no animation
el.setWidth(100);

// default animation
el.setWidth(100, true);

// animation with some options set
el.setWidth(100, {
    duration: 1,
    callback: this.foo,
    scope: this
});

// using the "anim" property to get the Anim object
var opt = {
    duration: 1,
    callback: this.foo,
    scope: this
};
el.setWidth(100, opt);
...
if(opt.anim.isAnimated()){
    opt.anim.stop();
}
</code></pre>
* <b> Composite (Collections of) Elements</b><br />
 * For working with collections of Elements, see <a href="Ext.CompositeElement.html">Ext.CompositeElement</a>
 * @constructor Create a new Element directly.
 * @param {String/HTMLElement} element
 * @param {Boolean} forceNew (optional) By default the constructor checks to see if there is already an instance of this element in the cache and if there is it returns the same instance. This will skip that check (useful for extending this class).
 */
(function(){
var D = Ext.lib.Dom;
var E = Ext.lib.Event;
var A = Ext.lib.Anim;

// local style camelizing for speed
var propCache = {};
var camelRe = /(-[a-z])/gi;
var camelFn = function(m, a){ return a.charAt(1).toUpperCase(); };
var view = document.defaultView;

Ext.Element = function(element, forceNew){
    var dom = typeof element == "string" ?
            document.getElementById(element) : element;
    if(!dom){ // invalid id/element
        return null;
    }
    var id = dom.id;
    if(forceNew !== true && id && Ext.Element.cache[id]){ // element object already exists
        return Ext.Element.cache[id];
    }

    /**
     * The DOM element
     * @type HTMLElement
     */
    this.dom = dom;

    /**
     * The DOM element ID
     * @type String
     */
    this.id = id || Ext.id(dom);
};

var El = Ext.Element;

El.prototype = {
    /**
     * The element's default display mode  (defaults to "")
     * @type String
     */
    originalDisplay : "",

    visibilityMode : 1,
    /**
     * The default unit to append to CSS values where a unit isn't provided (defaults to px).
     * @type String
     */
    defaultUnit : "px",
    /**
     * Sets the element's visibility mode. When setVisible() is called it
     * will use this to determine whether to set the visibility or the display property.
     * @param visMode Element.VISIBILITY or Element.DISPLAY
     * @return {Ext.Element} this
     */
    setVisibilityMode : function(visMode){
        this.visibilityMode = visMode;
        return this;
    },
    /**
     * Convenience method for setVisibilityMode(Element.DISPLAY)
     * @param {String} display (optional) What to set display to when visible
     * @return {Ext.Element} this
     */
    enableDisplayMode : function(display){
        this.setVisibilityMode(El.DISPLAY);
        if(typeof display != "undefined") this.originalDisplay = display;
        return this;
    },

    /**
     * Looks at this node and then at parent nodes for a match of the passed simple selector (e.g. div.some-class or span:first-child)
     * @param {String} selector The simple selector to test
     * @param {Number/String/HTMLElement/Element} maxDepth (optional) The max depth to
            search as a number or element (defaults to 10 || document.body)
     * @param {Boolean} returnEl (optional) True to return a Ext.Element object instead of DOM node
     * @return {HTMLElement} The matching DOM node (or null if no match was found)
     */
    findParent : function(simpleSelector, maxDepth, returnEl){
        var p = this.dom, b = document.body, depth = 0, dq = Ext.DomQuery, stopEl;
        maxDepth = maxDepth || 50;
        if(typeof maxDepth != "number"){
            stopEl = Ext.getDom(maxDepth);
            maxDepth = 10;
        }
        while(p && p.nodeType == 1 && depth < maxDepth && p != b && p != stopEl){
            if(dq.is(p, simpleSelector)){
                return returnEl ? Ext.get(p) : p;
            }
            depth++;
            p = p.parentNode;
        }
        return null;
    },


    /**
     * Looks at parent nodes for a match of the passed simple selector (e.g. div.some-class or span:first-child)
     * @param {String} selector The simple selector to test
     * @param {Number/String/HTMLElement/Element} maxDepth (optional) The max depth to
            search as a number or element (defaults to 10 || document.body)
     * @param {Boolean} returnEl (optional) True to return a Ext.Element object instead of DOM node
     * @return {HTMLElement} The matching DOM node (or null if no match was found)
     */
    findParentNode : function(simpleSelector, maxDepth, returnEl){
        var p = Ext.fly(this.dom.parentNode, '_internal');
        return p ? p.findParent(simpleSelector, maxDepth, returnEl) : null;
    },

    /**
     * Walks up the dom looking for a parent node that matches the passed simple selector (e.g. div.some-class or span:first-child).
     * This is a shortcut for findParentNode() that always returns an Ext.Element.
     * @param {String} selector The simple selector to test
     * @param {Number/String/HTMLElement/Element} maxDepth (optional) The max depth to
            search as a number or element (defaults to 10 || document.body)
     * @return {Ext.Element} The matching DOM node (or null if no match was found)
     */
    up : function(simpleSelector, maxDepth){
        return this.findParentNode(simpleSelector, maxDepth, true);
    },



    /**
     * Returns true if this element matches the passed simple selector (e.g. div.some-class or span:first-child)
     * @param {String} selector The simple selector to test
     * @return {Boolean} True if this element matches the selector, else false
     */
    is : function(simpleSelector){
        return Ext.DomQuery.is(this.dom, simpleSelector);
    },

    /**
     * Perform animation on this element.
     * @param {Object} args The YUI animation control args
     * @param {Float} duration (optional) How long the animation lasts in seconds (defaults to .35)
     * @param {Function} onComplete (optional) Function to call when animation completes
     * @param {String} easing (optional) Easing method to use (defaults to 'easeOut')
     * @param {String} animType (optional) 'run' is the default. Can also be 'color', 'motion', or 'scroll'
     * @return {Ext.Element} this
     */
    animate : function(args, duration, onComplete, easing, animType){
        this.anim(args, {duration: duration, callback: onComplete, easing: easing}, animType);
        return this;
    },

    /*
     * @private Internal animation call
     */
    anim : function(args, opt, animType, defaultDur, defaultEase, cb){
        animType = animType || 'run';
        opt = opt || {};
        var anim = Ext.lib.Anim[animType](
            this.dom, args,
            (opt.duration || defaultDur) || .35,
            (opt.easing || defaultEase) || 'easeOut',
            function(){
                Ext.callback(cb, this);
                Ext.callback(opt.callback, opt.scope || this, [this, opt]);
            },
            this
        );
        opt.anim = anim;
        return anim;
    },

    // private legacy anim prep
    preanim : function(a, i){
        return !a[i] ? false : (typeof a[i] == "object" ? a[i]: {duration: a[i+1], callback: a[i+2], easing: a[i+3]});
    },

    /**
     * Removes worthless text nodes
     * @param {Boolean} forceReclean (optional) By default the element
     * keeps track if it has been cleaned already so
     * you can call this over and over. However, if you update the element and
     * need to force a reclean, you can pass true.
     */
    clean : function(forceReclean){
        if(this.isCleaned && forceReclean !== true){
            return this;
        }
        var ns = /\S/;
        var d = this.dom, n = d.firstChild, ni = -1;
 	    while(n){
 	        var nx = n.nextSibling;
 	        if(n.nodeType == 3 && !ns.test(n.nodeValue)){
 	            d.removeChild(n);
 	        }else{
 	            n.nodeIndex = ++ni;
 	        }
 	        n = nx;
 	    }
 	    this.isCleaned = true;
 	    return this;
 	},

    // private
    calcOffsetsTo : function(el){
        el = Ext.get(el);
        var d = el.dom;
        var restorePos = false;
        if(el.getStyle('position') == 'static'){
            el.position('relative');
            restorePos = true;
        }
        var x = 0, y =0;
        var op = this.dom;
        while(op && op != d && op.tagName != 'HTML'){
            x+= op.offsetLeft;
            y+= op.offsetTop;
            op = op.offsetParent;
        }
        if(restorePos){
            el.position('static');
        }
        return [x, y];
    },

    /**
     * Scrolls this element into view within the passed container.
     * @param {String/HTMLElement/Element} container (optional) The container element to scroll (defaults to document.body)
     * @param {Boolean} hscroll (optional) False to disable horizontal scroll (defaults to true)
     * @return {Ext.Element} this
     */
    scrollIntoView : function(container, hscroll){
        var c = Ext.getDom(container) || document.body;
        var el = this.dom;

        var o = this.calcOffsetsTo(c),
            l = o[0],
            t = o[1],
            b = t+el.offsetHeight,
            r = l+el.offsetWidth;

        var ch = c.clientHeight;
        var ct = parseInt(c.scrollTop, 10);
        var cl = parseInt(c.scrollLeft, 10);
        var cb = ct + ch;
        var cr = cl + c.clientWidth;

        if(t < ct){
        	c.scrollTop = t;
        }else if(b > cb){
            c.scrollTop = b-ch;
        }

        if(hscroll !== false){
            if(l < cl){
                c.scrollLeft = l;
            }else if(r > cr){
                c.scrollLeft = r-c.clientWidth;
            }
        }
        return this;
    },

    // private
    scrollChildIntoView : function(child, hscroll){
        Ext.fly(child, '_scrollChildIntoView').scrollIntoView(this, hscroll);
    },

    /**
     * Measures the element's content height and updates height to match. Note: this function uses setTimeout so
     * the new height may not be available immediately.
     * @param {Boolean} animate (optional) Animate the transition (defaults to false)
     * @param {Float} duration (optional) Length of the animation in seconds (defaults to .35)
     * @param {Function} onComplete (optional) Function to call when animation completes
     * @param {String} easing (optional) Easing method to use (defaults to easeOut)
     * @return {Ext.Element} this
     */
    autoHeight : function(animate, duration, onComplete, easing){
        var oldHeight = this.getHeight();
        this.clip();
        this.setHeight(1); // force clipping
        setTimeout(function(){
            var height = parseInt(this.dom.scrollHeight, 10); // parseInt for Safari
            if(!animate){
                this.setHeight(height);
                this.unclip();
                if(typeof onComplete == "function"){
                    onComplete();
                }
            }else{
                this.setHeight(oldHeight); // restore original height
                this.setHeight(height, animate, duration, function(){
                    this.unclip();
                    if(typeof onComplete == "function") onComplete();
                }.createDelegate(this), easing);
            }
        }.createDelegate(this), 0);
        return this;
    },

    /**
     * Returns true if this element is an ancestor of the passed element
     * @param {HTMLElement/String} el The element to check
     * @return {Boolean} True if this element is an ancestor of el, else false
     */
    contains : function(el){
        if(!el){return false;}
        return D.isAncestor(this.dom, el.dom ? el.dom : el);
    },

    /**
     * Checks whether the element is currently visible using both visibility and display properties.
     * @param {Boolean} deep (optional) True to walk the dom and see if parent elements are hidden (defaults to false)
     * @return {Boolean} True if the element is currently visible, else false
     */
    isVisible : function(deep) {
        var vis = !(this.getStyle("visibility") == "hidden" || this.getStyle("display") == "none");
        if(deep !== true || !vis){
            return vis;
        }
        var p = this.dom.parentNode;
        while(p && p.tagName.toLowerCase() != "body"){
            if(!Ext.fly(p, '_isVisible').isVisible()){
                return false;
            }
            p = p.parentNode;
        }
        return true;
    },

    /**
     * Creates a {@link Ext.CompositeElement} for child nodes based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @param {Boolean} unique (optional) True to create a unique Ext.Element for each child (defaults to false, which creates a single shared flyweight object)
     * @return {CompositeElement/CompositeElementLite} The composite element
     */
    select : function(selector, unique){
        return El.select(selector, unique, this.dom);
    },

    /**
     * Selects child nodes based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @return {Array} An array of the matched nodes
     */
    query : function(selector, unique){
        return Ext.DomQuery.select(selector, this.dom);
    },

    /**
     * Selects a single child at any depth below this element based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @param {Boolean} returnDom (optional) True to return the DOM node instead of Ext.Element (defaults to false)
     * @return {HTMLElement/Ext.Element} The child Ext.Element (or DOM node if returnDom = true)
     */
    child : function(selector, returnDom){
        var n = Ext.DomQuery.selectNode(selector, this.dom);
        return returnDom ? n : Ext.get(n);
    },

    /**
     * Selects a single *direct* child based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @param {Boolean} returnDom (optional) True to return the DOM node instead of Ext.Element (defaults to false)
     * @return {HTMLElement/Ext.Element} The child Ext.Element (or DOM node if returnDom = true)
     */
    down : function(selector, returnDom){
        var n = Ext.DomQuery.selectNode(" > " + selector, this.dom);
        return returnDom ? n : Ext.get(n);
    },

    /**
     * Initializes a {@link Ext.dd.DD} drag drop object for this element.
     * @param {String} group The group the DD object is member of
     * @param {Object} config The DD config object
     * @param {Object} overrides An object containing methods to override/implement on the DD object
     * @return {Ext.dd.DD} The DD object
     */
    initDD : function(group, config, overrides){
        var dd = new Ext.dd.DD(Ext.id(this.dom), group, config);
        return Ext.apply(dd, overrides);
    },

    /**
     * Initializes a {@link Ext.dd.DDProxy} object for this element.
     * @param {String} group The group the DDProxy object is member of
     * @param {Object} config The DDProxy config object
     * @param {Object} overrides An object containing methods to override/implement on the DDProxy object
     * @return {Ext.dd.DDProxy} The DDProxy object
     */
    initDDProxy : function(group, config, overrides){
        var dd = new Ext.dd.DDProxy(Ext.id(this.dom), group, config);
        return Ext.apply(dd, overrides);
    },

    /**
     * Initializes a {@link Ext.dd.DDTarget} object for this element.
     * @param {String} group The group the DDTarget object is member of
     * @param {Object} config The DDTarget config object
     * @param {Object} overrides An object containing methods to override/implement on the DDTarget object
     * @return {Ext.dd.DDTarget} The DDTarget object
     */
    initDDTarget : function(group, config, overrides){
        var dd = new Ext.dd.DDTarget(Ext.id(this.dom), group, config);
        return Ext.apply(dd, overrides);
    },

    /**
     * Sets the visibility of the element (see details). If the visibilityMode is set to Element.DISPLAY, it will use
     * the display property to hide the element, otherwise it uses visibility. The default is to hide and show using the visibility property.
     * @param {Boolean} visible Whether the element is visible
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
     setVisible : function(visible, animate){
        if(!animate || !A){
            if(this.visibilityMode == El.DISPLAY){
                this.setDisplayed(visible);
            }else{
                this.fixDisplay();
                this.dom.style.visibility = visible ? "visible" : "hidden";
            }
        }else{
            // closure for composites
            var dom = this.dom;
            var visMode = this.visibilityMode;
            if(visible){
                this.setOpacity(.01);
                this.setVisible(true);
            }
            this.anim({opacity: { to: (visible?1:0) }},
                  this.preanim(arguments, 1),
                  null, .35, 'easeIn', function(){
                     if(!visible){
                         if(visMode == El.DISPLAY){
                             dom.style.display = "none";
                         }else{
                             dom.style.visibility = "hidden";
                         }
                         Ext.get(dom).setOpacity(1);
                     }
                 });
        }
        return this;
    },

    /**
     * Returns true if display is not "none"
     * @return {Boolean}
     */
    isDisplayed : function() {
        return this.getStyle("display") != "none";
    },

    /**
     * Toggles the element's visibility or display, depending on visibility mode.
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    toggle : function(animate){
        this.setVisible(!this.isVisible(), this.preanim(arguments, 0));
        return this;
    },

    /**
     * Sets the CSS display property. Uses originalDisplay if the specified value is a boolean true.
     * @param {Boolean} value Boolean value to display the element using its default display, or a string to set the display directly
     * @return {Ext.Element} this
     */
    setDisplayed : function(value) {
        if(typeof value == "boolean"){
           value = value ? this.originalDisplay : "none";
        }
        this.setStyle("display", value);
        return this;
    },

    /**
     * Tries to focus the element. Any exceptions are caught and ignored.
     * @return {Ext.Element} this
     */
    focus : function() {
        try{
            this.dom.focus();
        }catch(e){}
        return this;
    },

    /**
     * Tries to blur the element. Any exceptions are caught and ignored.
     * @return {Ext.Element} this
     */
    blur : function() {
        try{
            this.dom.blur();
        }catch(e){}
        return this;
    },

    /**
     * Adds one or more CSS classes to the element. Duplicate classes are automatically filtered out.
     * @param {String/Array} className The CSS class to add, or an array of classes
     * @return {Ext.Element} this
     */
    addClass : function(className){
        if(className instanceof Array){
            for(var i = 0, len = className.length; i < len; i++) {
            	this.addClass(className[i]);
            }
        }else{
            if(className && !this.hasClass(className)){
                this.dom.className = this.dom.className + " " + className;
            }
        }
        return this;
    },

    /**
     * Adds one or more CSS classes to this element and removes the same class(es) from all siblings.
     * @param {String/Array} className The CSS class to add, or an array of classes
     * @return {Ext.Element} this
     */
    radioClass : function(className){
        var siblings = this.dom.parentNode.childNodes;
        for(var i = 0; i < siblings.length; i++) {
        	var s = siblings[i];
        	if(s.nodeType == 1){
        	    Ext.get(s).removeClass(className);
        	}
        }
        this.addClass(className);
        return this;
    },

    /**
     * Removes one or more CSS classes from the element.
     * @param {String/Array} className The CSS class to remove, or an array of classes
     * @return {Ext.Element} this
     */
    removeClass : function(className){
        if(!className || !this.dom.className){
            return this;
        }
        if(className instanceof Array){
            for(var i = 0, len = className.length; i < len; i++) {
            	this.removeClass(className[i]);
            }
        }else{
            if(this.hasClass(className)){
                var re = this.classReCache[className];
                if (!re) {
                   re = new RegExp('(?:^|\\s+)' + className + '(?:\\s+|$)', "g");
                   this.classReCache[className] = re;
                }
                this.dom.className =
                    this.dom.className.replace(re, " ");
            }
        }
        return this;
    },

    // private
    classReCache: {},

    /**
     * Toggles the specified CSS class on this element (removes it if it already exists, otherwise adds it).
     * @param {String} className The CSS class to toggle
     * @return {Ext.Element} this
     */
    toggleClass : function(className){
        if(this.hasClass(className)){
            this.removeClass(className);
        }else{
            this.addClass(className);
        }
        return this;
    },

    /**
     * Checks if the specified CSS class exists on this element's DOM node.
     * @param {String} className The CSS class to check for
     * @return {Boolean} True if the class exists, else false
     */
    hasClass : function(className){
        return className && (' '+this.dom.className+' ').indexOf(' '+className+' ') != -1;
    },

    /**
     * Replaces a CSS class on the element with another.  If the old name does not exist, the new name will simply be added.
     * @param {String} oldClassName The CSS class to replace
     * @param {String} newClassName The replacement CSS class
     * @return {Ext.Element} this
     */
    replaceClass : function(oldClassName, newClassName){
        this.removeClass(oldClassName);
        this.addClass(newClassName);
        return this;
    },

    /**
     * Returns an object with properties matching the styles requested.
     * For example, el.getStyles('color', 'font-size', 'width') might return
     * {'color': '#FFFFFF', 'font-size': '13px', 'width': '100px'}.
     * @param {String} style1 A style name
     * @param {String} style2 A style name
     * @param {String} etc.
     * @return {Object} The style object
     */
    getStyles : function(){
        var a = arguments, len = a.length, r = {};
        for(var i = 0; i < len; i++){
            r[a[i]] = this.getStyle(a[i]);
        }
        return r;
    },

    /**
     * Normalizes currentStyle and computedStyle. This is not YUI getStyle, it is an optimised version.
     * @param {String} property The style property whose value is returned.
     * @return {String} The current value of the style property for this element.
     */
    getStyle : function(){
        return view && view.getComputedStyle ?
            function(prop){
                var el = this.dom, v, cs, camel;
                if(prop == 'float'){
                    prop = "cssFloat";
                }
                if(v = el.style[prop]){
                    return v;
                }
                if(cs = view.getComputedStyle(el, "")){
                    if(!(camel = propCache[prop])){
                        camel = propCache[prop] = prop.replace(camelRe, camelFn);
                    }
                    return cs[camel];
                }
                return null;
            } :
            function(prop){
                var el = this.dom, v, cs, camel;
                if(prop == 'opacity'){
                    if(typeof el.style.filter == 'string'){
                        var m = el.style.filter.match(/alpha\(opacity=(.*)\)/i);
                        if(m){
                            var fv = parseFloat(m[1]);
                            if(!isNaN(fv)){
                                return fv ? fv / 100 : 0;
                            }
                        }
                    }
                    return 1;
                }else if(prop == 'float'){
                    prop = "styleFloat";
                }
                if(!(camel = propCache[prop])){
                    camel = propCache[prop] = prop.replace(camelRe, camelFn);
                }
                if(v = el.style[camel]){
                    return v;
                }
                if(cs = el.currentStyle){
                    return cs[camel];
                }
                return null;
            };
    }(),

    /**
     * Wrapper for setting style properties, also takes single object parameter of multiple styles.
     * @param {String/Object} property The style property to be set, or an object of multiple styles.
     * @param {String} value (optional) The value to apply to the given property, or null if an object was passed.
     * @return {Ext.Element} this
     */
    setStyle : function(prop, value){
        if(typeof prop == "string"){
            var camel;
            if(!(camel = propCache[prop])){
                camel = propCache[prop] = prop.replace(camelRe, camelFn);
            }
            if(camel == 'opacity') {
                this.setOpacity(value);
            }else{
                this.dom.style[camel] = value;
            }
        }else{
            for(var style in prop){
                if(typeof prop[style] != "function"){
                   this.setStyle(style, prop[style]);
                }
            }
        }
        return this;
    },

    /**
     * More flexible version of {@link #setStyle} for setting style properties.
     * @param {String/Object/Function} styles A style specification string, e.g. "width:100px", or object in the form {width:"100px"}, or
     * a function which returns such a specification.
     * @return {Ext.Element} this
     */
    applyStyles : function(style){
        Ext.DomHelper.applyStyles(this.dom, style);
        return this;
    },

    /**
      * Gets the current X position of the element based on page coordinates.  Element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
      * @return {Number} The X position of the element
      */
    getX : function(){
        return D.getX(this.dom);
    },

    /**
      * Gets the current Y position of the element based on page coordinates.  Element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
      * @return {Number} The Y position of the element
      */
    getY : function(){
        return D.getY(this.dom);
    },

    /**
      * Gets the current position of the element based on page coordinates.  Element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
      * @return {Array} The XY position of the element
      */
    getXY : function(){
        return D.getXY(this.dom);
    },

    /**
     * Sets the X position of the element based on page coordinates.  Element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
     * @param {Number} The X position of the element
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setX : function(x, animate){
        if(!animate || !A){
            D.setX(this.dom, x);
        }else{
            this.setXY([x, this.getY()], this.preanim(arguments, 1));
        }
        return this;
    },

    /**
     * Sets the Y position of the element based on page coordinates.  Element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
     * @param {Number} The Y position of the element
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setY : function(y, animate){
        if(!animate || !A){
            D.setY(this.dom, y);
        }else{
            this.setXY([this.getX(), y], this.preanim(arguments, 1));
        }
        return this;
    },

    /**
     * Sets the element's left position directly using CSS style (instead of {@link #setX}).
     * @param {String} left The left CSS property value
     * @return {Ext.Element} this
     */
    setLeft : function(left){
        this.setStyle("left", this.addUnits(left));
        return this;
    },

    /**
     * Sets the element's top position directly using CSS style (instead of {@link #setY}).
     * @param {String} top The top CSS property value
     * @return {Ext.Element} this
     */
    setTop : function(top){
        this.setStyle("top", this.addUnits(top));
        return this;
    },

    /**
     * Sets the element's CSS right style.
     * @param {String} right The right CSS property value
     * @return {Ext.Element} this
     */
    setRight : function(right){
        this.setStyle("right", this.addUnits(right));
        return this;
    },

    /**
     * Sets the element's CSS bottom style.
     * @param {String} bottom The bottom CSS property value
     * @return {Ext.Element} this
     */
    setBottom : function(bottom){
        this.setStyle("bottom", this.addUnits(bottom));
        return this;
    },

    /**
     * Sets the position of the element in page coordinates, regardless of how the element is positioned.
     * The element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
     * @param {Array} pos Contains X & Y [x, y] values for new position (coordinates are page-based)
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setXY : function(pos, animate){
        if(!animate || !A){
            D.setXY(this.dom, pos);
        }else{
            this.anim({points: {to: pos}}, this.preanim(arguments, 1), 'motion');
        }
        return this;
    },

    /**
     * Sets the position of the element in page coordinates, regardless of how the element is positioned.
     * The element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
     * @param {Number} x X value for new position (coordinates are page-based)
     * @param {Number} y Y value for new position (coordinates are page-based)
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setLocation : function(x, y, animate){
        this.setXY([x, y], this.preanim(arguments, 2));
        return this;
    },

    /**
     * Sets the position of the element in page coordinates, regardless of how the element is positioned.
     * The element must be part of the DOM tree to have page coordinates (display:none or elements not appended return false).
     * @param {Number} x X value for new position (coordinates are page-based)
     * @param {Number} y Y value for new position (coordinates are page-based)
     * @param {Boolean/Object} animate (optional) True for the default animation, or a standard Element animation config object
     * @return {Ext.Element} this
     */
    moveTo : function(x, y, animate){
        this.setXY([x, y], this.preanim(arguments, 2));
        return this;
    },

    /**
     * Returns the region of the given element.
     * The element must be part of the DOM tree to have a region (display:none or elements not appended return false).
     * @return {Region} A Ext.lib.Region containing "top, left, bottom, right" member data.
     */
    getRegion : function(){
        return D.getRegion(this.dom);
    },

    /**
     * Returns the offset height of the element
     * @param {Boolean} contentHeight (optional) true to get the height minus borders and padding
     * @return {Number} The element's height
     */
    getHeight : function(contentHeight){
        var h = this.dom.offsetHeight || 0;
        return contentHeight !== true ? h : h-this.getBorderWidth("tb")-this.getPadding("tb");
    },

    /**
     * Returns the offset width of the element
     * @param {Boolean} contentWidth (optional) true to get the width minus borders and padding
     * @return {Number} The element's width
     */
    getWidth : function(contentWidth){
        var w = this.dom.offsetWidth || 0;
        return contentWidth !== true ? w : w-this.getBorderWidth("lr")-this.getPadding("lr");
    },

    /**
     * Returns either the offsetHeight or the height of this element based on CSS height adjusted by padding or borders
     * when needed to simulate offsetHeight when offsets aren't available. This may not work on display:none elements
     * if a height has not been set using CSS.
     * @return {Number}
     */
    getComputedHeight : function(){
        var h = Math.max(this.dom.offsetHeight, this.dom.clientHeight);
        if(!h){
            h = parseInt(this.getStyle('height'), 10) || 0;
            if(!this.isBorderBox()){
                h += this.getFrameWidth('tb');
            }
        }
        return h;
    },

    /**
     * Returns either the offsetWidth or the width of this element based on CSS width adjusted by padding or borders
     * when needed to simulate offsetWidth when offsets aren't available. This may not work on display:none elements
     * if a width has not been set using CSS.
     * @return {Number}
     */
    getComputedWidth : function(){
        var w = Math.max(this.dom.offsetWidth, this.dom.clientWidth);
        if(!w){
            w = parseInt(this.getStyle('width'), 10) || 0;
            if(!this.isBorderBox()){
                w += this.getFrameWidth('lr');
            }
        }
        return w;
    },

    /**
     * Returns the size of the element.
     * @param {Boolean} contentSize (optional) true to get the width/size minus borders and padding
     * @return {Object} An object containing the element's size {width: (element width), height: (element height)}
     */
    getSize : function(contentSize){
        return {width: this.getWidth(contentSize), height: this.getHeight(contentSize)};
    },

    /**
     * Returns the width and height of the viewport.
     * @return {Object} An object containing the viewport's size {width: (viewport width), height: (viewport height)}
     */
    getViewSize : function(){
        var d = this.dom, doc = document, aw = 0, ah = 0;
        if(d == doc || d == doc.body){
            return {width : D.getViewWidth(), height: D.getViewHeight()};
        }else{
            return {
                width : d.clientWidth,
                height: d.clientHeight
            };
        }
    },

    /**
     * Returns the value of the "value" attribute
     * @param {Boolean} asNumber true to parse the value as a number
     * @return {String/Number}
     */
    getValue : function(asNumber){
        return asNumber ? parseInt(this.dom.value, 10) : this.dom.value;
    },

    // private
    adjustWidth : function(width){
        if(typeof width == "number"){
            if(this.autoBoxAdjust && !this.isBorderBox()){
               width -= (this.getBorderWidth("lr") + this.getPadding("lr"));
            }
            if(width < 0){
                width = 0;
            }
        }
        return width;
    },

    // private
    adjustHeight : function(height){
        if(typeof height == "number"){
           if(this.autoBoxAdjust && !this.isBorderBox()){
               height -= (this.getBorderWidth("tb") + this.getPadding("tb"));
           }
           if(height < 0){
               height = 0;
           }
        }
        return height;
    },

    /**
     * Set the width of the element
     * @param {Number} width The new width
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setWidth : function(width, animate){
        width = this.adjustWidth(width);
        if(!animate || !A){
            this.dom.style.width = this.addUnits(width);
        }else{
            this.anim({width: {to: width}}, this.preanim(arguments, 1));
        }
        return this;
    },

    /**
     * Set the height of the element
     * @param {Number} height The new height
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
     setHeight : function(height, animate){
        height = this.adjustHeight(height);
        if(!animate || !A){
            this.dom.style.height = this.addUnits(height);
        }else{
            this.anim({height: {to: height}}, this.preanim(arguments, 1));
        }
        return this;
    },

    /**
     * Set the size of the element. If animation is true, both width an height will be animated concurrently.
     * @param {Number} width The new width
     * @param {Number} height The new height
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
     setSize : function(width, height, animate){
        if(typeof width == "object"){ // in case of object from getSize()
            height = width.height; width = width.width;
        }
        width = this.adjustWidth(width); height = this.adjustHeight(height);
        if(!animate || !A){
            this.dom.style.width = this.addUnits(width);
            this.dom.style.height = this.addUnits(height);
        }else{
            this.anim({width: {to: width}, height: {to: height}}, this.preanim(arguments, 2));
        }
        return this;
    },

    /**
     * Sets the element's position and size in one shot. If animation is true then width, height, x and y will be animated concurrently.
     * @param {Number} x X value for new position (coordinates are page-based)
     * @param {Number} y Y value for new position (coordinates are page-based)
     * @param {Number} width The new width
     * @param {Number} height The new height
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setBounds : function(x, y, width, height, animate){
        if(!animate || !A){
            this.setSize(width, height);
            this.setLocation(x, y);
        }else{
            width = this.adjustWidth(width); height = this.adjustHeight(height);
            this.anim({points: {to: [x, y]}, width: {to: width}, height: {to: height}},
                          this.preanim(arguments, 4), 'motion');
        }
        return this;
    },

    /**
     * Sets the element's position and size the the specified region. If animation is true then width, height, x and y will be animated concurrently.
     * @param {Ext.lib.Region} region The region to fill
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setRegion : function(region, animate){
        this.setBounds(region.left, region.top, region.right-region.left, region.bottom-region.top, this.preanim(arguments, 1));
        return this;
    },

    /**
     * Appends an event handler
     *
     * @param {String}   eventName     The type of event to append
     * @param {Function} fn        The method the event invokes
     * @param {Object} scope       (optional) The scope (this object) of the fn
     * @param {Object}   options   (optional)An object with standard {@link Ext.EventManager#addListener} options
     */
    addListener : function(eventName, fn, scope, options){
        Ext.EventManager.on(this.dom,  eventName, fn, scope || this, options);
    },

    /**
     * Removes an event handler from this element
     * @param {String} eventName the type of event to remove
     * @param {Function} fn the method the event invokes
     * @return {Ext.Element} this
     */
    removeListener : function(eventName, fn){
        Ext.EventManager.removeListener(this.dom,  eventName, fn);
        return this;
    },

    /**
     * Removes all previous added listeners from this element
     * @return {Ext.Element} this
     */
    removeAllListeners : function(){
        E.purgeElement(this.dom);
        return this;
    },

    relayEvent : function(eventName, observable){
        this.on(eventName, function(e){
            observable.fireEvent(eventName, e);
        });
    },

    /**
     * Set the opacity of the element
     * @param {Float} opacity The new opacity. 0 = transparent, .5 = 50% visibile, 1 = fully visible, etc
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
     setOpacity : function(opacity, animate){
        if(!animate || !A){
            var s = this.dom.style;
            if(Ext.isIE){
                s.zoom = 1;
                s.filter = (s.filter || '').replace(/alpha\([^\)]*\)/gi,"") +
                           (opacity == 1 ? "" : "alpha(opacity=" + opacity * 100 + ")");
            }else{
                s.opacity = opacity;
            }
        }else{
            this.anim({opacity: {to: opacity}}, this.preanim(arguments, 1), null, .35, 'easeIn');
        }
        return this;
    },

    /**
     * Gets the left X coordinate
     * @param {Boolean} local True to get the local css position instead of page coordinate
     * @return {Number}
     */
    getLeft : function(local){
        if(!local){
            return this.getX();
        }else{
            return parseInt(this.getStyle("left"), 10) || 0;
        }
    },

    /**
     * Gets the right X coordinate of the element (element X position + element width)
     * @param {Boolean} local True to get the local css position instead of page coordinate
     * @return {Number}
     */
    getRight : function(local){
        if(!local){
            return this.getX() + this.getWidth();
        }else{
            return (this.getLeft(true) + this.getWidth()) || 0;
        }
    },

    /**
     * Gets the top Y coordinate
     * @param {Boolean} local True to get the local css position instead of page coordinate
     * @return {Number}
     */
    getTop : function(local) {
        if(!local){
            return this.getY();
        }else{
            return parseInt(this.getStyle("top"), 10) || 0;
        }
    },

    /**
     * Gets the bottom Y coordinate of the element (element Y position + element height)
     * @param {Boolean} local True to get the local css position instead of page coordinate
     * @return {Number}
     */
    getBottom : function(local){
        if(!local){
            return this.getY() + this.getHeight();
        }else{
            return (this.getTop(true) + this.getHeight()) || 0;
        }
    },

    /**
    * Initializes positioning on this element. If a desired position is not passed, it will make the
    * the element positioned relative IF it is not already positioned.
    * @param {String} pos (optional) Positioning to use "relative", "absolute" or "fixed"
    * @param {Number} zIndex (optional) The zIndex to apply
    * @param {Number} x (optional) Set the page X position
    * @param {Number} y (optional) Set the page Y position
    */
    position : function(pos, zIndex, x, y){
        if(!pos){
           if(this.getStyle('position') == 'static'){
               this.setStyle('position', 'relative');
           }
        }else{
            this.setStyle("position", pos);
        }
        if(zIndex){
            this.setStyle("z-index", zIndex);
        }
        if(x !== undefined && y !== undefined){
            this.setXY([x, y]);
        }else if(x !== undefined){
            this.setX(x);
        }else if(y !== undefined){
            this.setY(y);
        }
    },

    /**
    * Clear positioning back to the default when the document was loaded
    * @param {String} value (optional) The value to use for the left,right,top,bottom, defaults to '' (empty string). You could use 'auto'.
    * @return {Ext.Element} this
     */
    clearPositioning : function(value){
        value = value ||'';
        this.setStyle({
            "left": value,
            "right": value,
            "top": value,
            "bottom": value,
            "z-index": "",
            "position" : "static"
        });
        return this;
    },

    /**
    * Gets an object with all CSS positioning properties. Useful along with setPostioning to get
    * snapshot before performing an update and then restoring the element.
    * @return {Object}
    */
    getPositioning : function(){
        var l = this.getStyle("left");
        var t = this.getStyle("top");
        return {
            "position" : this.getStyle("position"),
            "left" : l,
            "right" : l ? "" : this.getStyle("right"),
            "top" : t,
            "bottom" : t ? "" : this.getStyle("bottom"),
            "z-index" : this.getStyle("z-index")
        };
    },

    /**
     * Gets the width of the border(s) for the specified side(s)
     * @param {String} side Can be t, l, r, b or any combination of those to add multiple values. For example,
     * passing lr would get the border (l)eft width + the border (r)ight width.
     * @return {Number} The width of the sides passed added together
     */
    getBorderWidth : function(side){
        return this.addStyles(side, El.borders);
    },

    /**
     * Gets the width of the padding(s) for the specified side(s)
     * @param {String} side Can be t, l, r, b or any combination of those to add multiple values. For example,
     * passing lr would get the padding (l)eft + the padding (r)ight.
     * @return {Number} The padding of the sides passed added together
     */
    getPadding : function(side){
        return this.addStyles(side, El.paddings);
    },

    /**
    * Set positioning with an object returned by getPositioning().
    * @param {Object} posCfg
    * @return {Ext.Element} this
     */
    setPositioning : function(pc){
        this.applyStyles(pc);
        if(pc.right == "auto"){
            this.dom.style.right = "";
        }
        if(pc.bottom == "auto"){
            this.dom.style.bottom = "";
        }
        return this;
    },

    // private
    fixDisplay : function(){
        if(this.getStyle("display") == "none"){
            this.setStyle("visibility", "hidden");
            this.setStyle("display", this.originalDisplay); // first try reverting to default
            if(this.getStyle("display") == "none"){ // if that fails, default to block
                this.setStyle("display", "block");
            }
        }
    },

    /**
     * Quick set left and top adding default units
     * @param {String} left The left CSS property value
     * @param {String} top The top CSS property value
     * @return {Ext.Element} this
     */
     setLeftTop : function(left, top){
        this.dom.style.left = this.addUnits(left);
        this.dom.style.top = this.addUnits(top);
        return this;
    },

    /**
     * Move this element relative to its current position.
     * @param {String} direction Possible values are: "l","left" - "r","right" - "t","top","up" - "b","bottom","down".
     * @param {Number} distance How far to move the element in pixels
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
     move : function(direction, distance, animate){
        var xy = this.getXY();
        direction = direction.toLowerCase();
        switch(direction){
            case "l":
            case "left":
                this.moveTo(xy[0]-distance, xy[1], this.preanim(arguments, 2));
                break;
           case "r":
           case "right":
                this.moveTo(xy[0]+distance, xy[1], this.preanim(arguments, 2));
                break;
           case "t":
           case "top":
           case "up":
                this.moveTo(xy[0], xy[1]-distance, this.preanim(arguments, 2));
                break;
           case "b":
           case "bottom":
           case "down":
                this.moveTo(xy[0], xy[1]+distance, this.preanim(arguments, 2));
                break;
        }
        return this;
    },

    /**
     *  Store the current overflow setting and clip overflow on the element - use {@link #unclip} to remove
     * @return {Ext.Element} this
     */
    clip : function(){
        if(!this.isClipped){
           this.isClipped = true;
           this.originalClip = {
               "o": this.getStyle("overflow"),
               "x": this.getStyle("overflow-x"),
               "y": this.getStyle("overflow-y")
           };
           this.setStyle("overflow", "hidden");
           this.setStyle("overflow-x", "hidden");
           this.setStyle("overflow-y", "hidden");
        }
        return this;
    },

    /**
     *  Return clipping (overflow) to original clipping before clip() was called
     * @return {Ext.Element} this
     */
    unclip : function(){
        if(this.isClipped){
            this.isClipped = false;
            var o = this.originalClip;
            if(o.o){this.setStyle("overflow", o.o);}
            if(o.x){this.setStyle("overflow-x", o.x);}
            if(o.y){this.setStyle("overflow-y", o.y);}
        }
        return this;
    },


    /**
     * Gets the x,y coordinates specified by the anchor position on the element.
     * @param {String} anchor (optional) The specified anchor position (defaults to "c").  See {@link #alignTo} for details on supported anchor positions.
     * @param {Object} size (optional) An object containing the size to use for calculating anchor position
     *                       {width: (target width), height: (target height)} (defaults to the element's current size)
     * @param {Boolean} local (optional) True to get the local (element top/left-relative) anchor position instead of page coordinates
     * @return {Array} [x, y] An array containing the element's x and y coordinates
     */
    getAnchorXY : function(anchor, local, s){
        //Passing a different size is useful for pre-calculating anchors,
        //especially for anchored animations that change the el size.

        var w, h, vp = false;
        if(!s){
            var d = this.dom;
            if(d == document.body || d == document){
                vp = true;
                w = D.getViewWidth(); h = D.getViewHeight();
            }else{
                w = this.getWidth(); h = this.getHeight();
            }
        }else{
            w = s.width;  h = s.height;
        }
        var x = 0, y = 0, r = Math.round;
        switch((anchor || "tl").toLowerCase()){
            case "c":
                x = r(w*.5);
                y = r(h*.5);
            break;
            case "t":
                x = r(w*.5);
                y = 0;
            break;
            case "l":
                x = 0;
                y = r(h*.5);
            break;
            case "r":
                x = w;
                y = r(h*.5);
            break;
            case "b":
                x = r(w*.5);
                y = h;
            break;
            case "tl":
                x = 0;
                y = 0;
            break;
            case "bl":
                x = 0;
                y = h;
            break;
            case "br":
                x = w;
                y = h;
            break;
            case "tr":
                x = w;
                y = 0;
            break;
        }
        if(local === true){
            return [x, y];
        }
        if(vp){
            var sc = this.getScroll();
            return [x + sc.left, y + sc.top];
        }
        //Add the element's offset xy
        var o = this.getXY();
        return [x+o[0], y+o[1]];
    },

    /**
     * Gets the x,y coordinates to align this element with another element. See {@link #alignTo} for more info on the
     * supported position values.
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} position The position to align to.
     * @param {Array} offsets (optional) Offset the positioning by [x, y]
     * @return {Array} [x, y]
     */
    getAlignToXY : function(el, p, o){
        el = Ext.get(el);
        var d = this.dom;
        if(!el.dom){
            throw "Element.alignTo with an element that doesn't exist";
        }
        var c = false; //constrain to viewport
        var p1 = "", p2 = "";
        o = o || [0,0];

        if(!p){
            p = "tl-bl";
        }else if(p == "?"){
            p = "tl-bl?";
        }else if(p.indexOf("-") == -1){
            p = "tl-" + p;
        }
        p = p.toLowerCase();
        var m = p.match(/^([a-z]+)-([a-z]+)(\?)?$/);
        if(!m){
           throw "Element.alignTo with an invalid alignment " + p;
        }
        p1 = m[1]; p2 = m[2]; c = !!m[3];

        //Subtract the aligned el's internal xy from the target's offset xy
        //plus custom offset to get the aligned el's new offset xy
        var a1 = this.getAnchorXY(p1, true);
        var a2 = el.getAnchorXY(p2, false);
        var x = a2[0] - a1[0] + o[0];
        var y = a2[1] - a1[1] + o[1];
        if(c){
            //constrain the aligned el to viewport if necessary
            var w = this.getWidth(), h = this.getHeight(), r = el.getRegion();
            // 5px of margin for ie
            var dw = D.getViewWidth()-5, dh = D.getViewHeight()-5;

            //If we are at a viewport boundary and the aligned el is anchored on a target border that is
            //perpendicular to the vp border, allow the aligned el to slide on that border,
            //otherwise swap the aligned el to the opposite border of the target.
            var p1y = p1.charAt(0), p1x = p1.charAt(p1.length-1);
           var p2y = p2.charAt(0), p2x = p2.charAt(p2.length-1);
           var swapY = ((p1y=="t" && p2y=="b") || (p1y=="b" && p2y=="t"));
           var swapX = ((p1x=="r" && p2x=="l") || (p1x=="l" && p2x=="r"));

           var doc = document;
           var scrollX = (doc.documentElement.scrollLeft || doc.body.scrollLeft || 0)+5;
           var scrollY = (doc.documentElement.scrollTop || doc.body.scrollTop || 0)+5;

           if((x+w) > dw + scrollX){
                x = swapX ? r.left-w : dw+scrollX-w;
            }
           if(x < scrollX){
               x = swapX ? r.right : scrollX;
           }
           if((y+h) > dh + scrollY){
                y = swapY ? r.top-h : dh+scrollY-h;
            }
           if (y < scrollY){
               y = swapY ? r.bottom : scrollY;
           }
        }
        return [x,y];
    },

    // private
    getConstrainToXY : function(){
        var os = {top:0, left:0, bottom:0, right: 0};

        return function(el, local, offsets, proposedXY){
            el = Ext.get(el);
            offsets = offsets ? Ext.applyIf(offsets, os) : os;

            var vw, vh, vx = 0, vy = 0;
            if(el.dom == document.body || el.dom == document){
                vw = Ext.lib.Dom.getViewWidth();
                vh = Ext.lib.Dom.getViewHeight();
            }else{
                vw = el.dom.clientWidth;
                vh = el.dom.clientHeight;
                if(!local){
                    var vxy = el.getXY();
                    vx = vxy[0];
                    vy = vxy[1];
                }
            }

            var s = el.getScroll();

            vx += offsets.left + s.left;
            vy += offsets.top + s.top;

            vw -= offsets.right;
            vh -= offsets.bottom;

            var vr = vx+vw;
            var vb = vy+vh;

            var xy = proposedXY || (!local ? this.getXY() : [this.getLeft(true), this.getTop(true)]);
            var x = xy[0], y = xy[1];
            var w = this.dom.offsetWidth, h = this.dom.offsetHeight;

            // only move it if it needs it
            var moved = false;

            // first validate right/bottom
            if((x + w) > vr){
                x = vr - w;
                moved = true;
            }
            if((y + h) > vb){
                y = vb - h;
                moved = true;
            }
            // then make sure top/left isn't negative
            if(x < vx){
                x = vx;
                moved = true;
            }
            if(y < vy){
                y = vy;
                moved = true;
            }
            return moved ? [x, y] : false;
        };
    }(),

    // private
    adjustForConstraints : function(xy, parent, offsets){
        return this.getConstrainToXY(parent || document, false, offsets, xy) ||  xy;
    },

    /**
     * Aligns this element with another element relative to the specified anchor points. If the other element is the
     * document it aligns it to the viewport.
     * The position parameter is optional, and can be specified in any one of the following formats:
     * <ul>
     *   <li><b>Blank</b>: Defaults to aligning the element's top-left corner to the target's bottom-left corner ("tl-bl").</li>
     *   <li><b>One anchor (deprecated)</b>: The passed anchor position is used as the target element's anchor point.
     *       The element being aligned will position its top-left corner (tl) to that point.  <i>This method has been
     *       deprecated in favor of the newer two anchor syntax below</i>.</li>
     *   <li><b>Two anchors</b>: If two values from the table below are passed separated by a dash, the first value is used as the
     *       element's anchor point, and the second value is used as the target's anchor point.</li>
     * </ul>
     * In addition to the anchor points, the position parameter also supports the "?" character.  If "?" is passed at the end of
     * the position string, the element will attempt to align as specified, but the position will be adjusted to constrain to
     * the viewport if necessary.  Note that the element being aligned might be swapped to align to a different position than
     * that specified in order to enforce the viewport constraints.
     * Following are all of the supported anchor positions:
<pre>
Value  Description
-----  -----------------------------
tl     The top left corner (default)
t      The center of the top edge
tr     The top right corner
l      The center of the left edge
c      In the center of the element
r      The center of the right edge
bl     The bottom left corner
b      The center of the bottom edge
br     The bottom right corner
</pre>
Example Usage:
<pre><code>
// align el to other-el using the default positioning ("tl-bl", non-constrained)
el.alignTo("other-el");

// align the top left corner of el with the top right corner of other-el (constrained to viewport)
el.alignTo("other-el", "tr?");

// align the bottom right corner of el with the center left edge of other-el
el.alignTo("other-el", "br-l?");

// align the center of el with the bottom left corner of other-el and
// adjust the x position by -6 pixels (and the y position by 0)
el.alignTo("other-el", "c-bl", [-6, 0]);
</code></pre>
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} position The position to align to.
     * @param {Array} offsets (optional) Offset the positioning by [x, y]
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    alignTo : function(element, position, offsets, animate){
        var xy = this.getAlignToXY(element, position, offsets);
        this.setXY(xy, this.preanim(arguments, 3));
        return this;
    },

    /**
     * Anchors an element to another element and realigns it when the window is resized.
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} position The position to align to.
     * @param {Array} offsets (optional) Offset the positioning by [x, y]
     * @param {Boolean/Object} animate (optional) True for the default animation or a standard Element animation config object
     * @param {Boolean/Number} monitorScroll (optional) True to monitor body scroll and reposition. If this parameter
     * is a number, it is used as the buffer delay (defaults to 50ms).
     * @param {Function} callback The function to call after the animation finishes
     * @return {Ext.Element} this
     */
    anchorTo : function(el, alignment, offsets, animate, monitorScroll, callback){
        var action = function(){
            this.alignTo(el, alignment, offsets, animate);
            Ext.callback(callback, this);
        };
        Ext.EventManager.onWindowResize(action, this);
        var tm = typeof monitorScroll;
        if(tm != 'undefined'){
            Ext.EventManager.on(window, 'scroll', action, this,
                {buffer: tm == 'number' ? monitorScroll : 50});
        }
        action.call(this); // align immediately
        return this;
    },
    /**
     * Clears any opacity settings from this element. Required in some cases for IE.
     * @return {Ext.Element} this
     */
    clearOpacity : function(){
        if (window.ActiveXObject) {
            if(typeof this.dom.style.filter == 'string' && (/alpha/i).test(this.dom.style.filter)){
                this.dom.style.filter = "";
            }
        } else {
            this.dom.style.opacity = "";
            this.dom.style["-moz-opacity"] = "";
            this.dom.style["-khtml-opacity"] = "";
        }
        return this;
    },

    /**
     * Hide this element - Uses display mode to determine whether to use "display" or "visibility". See {@link #setVisible}.
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    hide : function(animate){
        this.setVisible(false, this.preanim(arguments, 0));
        return this;
    },

    /**
    * Show this element - Uses display mode to determine whether to use "display" or "visibility". See {@link #setVisible}.
    * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    show : function(animate){
        this.setVisible(true, this.preanim(arguments, 0));
        return this;
    },

    /**
     * @private Test if size has a unit, otherwise appends the default
     */
    addUnits : function(size){
        return Ext.Element.addUnits(size, this.defaultUnit);
    },

    /**
     * Temporarily enables offsets (width,height,x,y) for an element with display:none, use endMeasure() when done.
     * @return {Ext.Element} this
     */
    beginMeasure : function(){
        var el = this.dom;
        if(el.offsetWidth || el.offsetHeight){
            return this; // offsets work already
        }
        var changed = [];
        var p = this.dom, b = document.body; // start with this element
        while((!el.offsetWidth && !el.offsetHeight) && p && p.tagName && p != b){
            var pe = Ext.get(p);
            if(pe.getStyle('display') == 'none'){
                changed.push({el: p, visibility: pe.getStyle("visibility")});
                p.style.visibility = "hidden";
                p.style.display = "block";
            }
            p = p.parentNode;
        }
        this._measureChanged = changed;
        return this;

    },

    /**
     * Restores displays to before beginMeasure was called
     * @return {Ext.Element} this
     */
    endMeasure : function(){
        var changed = this._measureChanged;
        if(changed){
            for(var i = 0, len = changed.length; i < len; i++) {
            	var r = changed[i];
            	r.el.style.visibility = r.visibility;
                r.el.style.display = "none";
            }
            this._measureChanged = null;
        }
        return this;
    },

    /**
    * Update the innerHTML of this element, optionally searching for and processing scripts
    * @param {String} html The new HTML
    * @param {Boolean} loadScripts (optional) true to look for and process scripts
    * @param {Function} callback For async script loading you can be noticed when the update completes
    * @return {Ext.Element} this
     */
    update : function(html, loadScripts, callback){
        if(typeof html == "undefined"){
            html = "";
        }
        if(loadScripts !== true){
            this.dom.innerHTML = html;
            if(typeof callback == "function"){
                callback();
            }
            return this;
        }
        var id = Ext.id();
        var dom = this.dom;

        html += '<span id="' + id + '"></span>';

        E.onAvailable(id, function(){
            var hd = document.getElementsByTagName("head")[0];
            var re = /(?:<script([^>]*)?>)((\n|\r|.)*?)(?:<\/script>)/ig;
            var srcRe = /\ssrc=([\'\"])(.*?)\1/i;
            var typeRe = /\stype=([\'\"])(.*?)\1/i;

            var match;
            while(match = re.exec(html)){
                var attrs = match[1];
                var srcMatch = attrs ? attrs.match(srcRe) : false;
                if(srcMatch && srcMatch[2]){
                   var s = document.createElement("script");
                   s.src = srcMatch[2];
                   var typeMatch = attrs.match(typeRe);
                   if(typeMatch && typeMatch[2]){
                       s.type = typeMatch[2];
                   }
                   hd.appendChild(s);
                }else if(match[2] && match[2].length > 0){
                    if(window.execScript) {
                       window.execScript(match[2]);
                    } else {
                       window.eval(match[2]);
                    }
                }
            }
            var el = document.getElementById(id);
            if(el){el.parentNode.removeChild(el);}
            if(typeof callback == "function"){
                callback();
            }
        });
        dom.innerHTML = html.replace(/(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)/ig, "");
        return this;
    },

    /**
     * Direct access to the UpdateManager update() method (takes the same parameters).
     * @param {String/Function} url The url for this request or a function to call to get the url
     * @param {String/Object} params (optional) The parameters to pass as either a url encoded string "param1=1&amp;param2=2" or an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess)
     * @param {Boolean} discardUrl (optional) By default when you execute an update the defaultUrl is changed to the last used url. If true, it will not store the url.
     * @return {Ext.Element} this
     */
    load : function(){
        var um = this.getUpdateManager();
        um.update.apply(um, arguments);
        return this;
    },

    /**
    * Gets this element's UpdateManager
    * @return {Ext.UpdateManager} The UpdateManager
    */
    getUpdateManager : function(){
        if(!this.updateManager){
            this.updateManager = new Ext.UpdateManager(this);
        }
        return this.updateManager;
    },

    /**
     * Disables text selection for this element (normalized across browsers)
     * @return {Ext.Element} this
     */
    unselectable : function(){
        this.dom.unselectable = "on";
        this.swallowEvent("selectstart", true);
        this.applyStyles("-moz-user-select:none;-khtml-user-select:none;");
        this.addClass("x-unselectable");
        return this;
    },

    /**
    * Calculates the x, y to center this element on the screen
    * @return {Array} The x, y values [x, y]
    */
    getCenterXY : function(){
        return this.getAlignToXY(document, 'c-c');
    },

    /**
    * Centers the Element in either the viewport, or another Element.
    * @param {String/HTMLElement/Ext.Element} centerIn (optional) The element in which to center the element.
    */
    center : function(centerIn){
        this.alignTo(centerIn || document, 'c-c');
        return this;
    },

    /**
     * Tests various css rules/browsers to determine if this element uses a border box
     * @return {Boolean}
     */
    isBorderBox : function(){
        return noBoxAdjust[this.dom.tagName.toLowerCase()] || Ext.isBorderBox;
    },

    /**
     * Return a box {x, y, width, height} that can be used to set another elements
     * size/location to match this element.
     * @param {Boolean} contentBox (optional) If true a box for the content of the element is returned.
     * @param {Boolean} local (optional) If true the element's left and top are returned instead of page x/y.
     * @return {Object} box An object in the format {x, y, width, height}
     */
    getBox : function(contentBox, local){
        var xy;
        if(!local){
            xy = this.getXY();
        }else{
            var left = parseInt(this.getStyle("left"), 10) || 0;
            var top = parseInt(this.getStyle("top"), 10) || 0;
            xy = [left, top];
        }
        var el = this.dom, w = el.offsetWidth, h = el.offsetHeight, bx;
        if(!contentBox){
            bx = {x: xy[0], y: xy[1], 0: xy[0], 1: xy[1], width: w, height: h};
        }else{
            var l = this.getBorderWidth("l")+this.getPadding("l");
            var r = this.getBorderWidth("r")+this.getPadding("r");
            var t = this.getBorderWidth("t")+this.getPadding("t");
            var b = this.getBorderWidth("b")+this.getPadding("b");
            bx = {x: xy[0]+l, y: xy[1]+t, 0: xy[0]+l, 1: xy[1]+t, width: w-(l+r), height: h-(t+b)};
        }
        bx.right = bx.x + bx.width;
        bx.bottom = bx.y + bx.height;
        return bx;
    },

    /**
     * Returns the sum width of the padding and borders for the passed "sides". See getBorderWidth()
     for more information about the sides.
     * @param {String} sides
     * @return {Number}
     */
    getFrameWidth : function(sides, onlyContentBox){
        return onlyContentBox && Ext.isBorderBox ? 0 : (this.getPadding(sides) + this.getBorderWidth(sides));
    },

    /**
     * Sets the element's box. Use getBox() on another element to get a box obj. If animate is true then width, height, x and y will be animated concurrently.
     * @param {Object} box The box to fill {x, y, width, height}
     * @param {Boolean} adjust (optional) Whether to adjust for box-model issues automatically
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Ext.Element} this
     */
    setBox : function(box, adjust, animate){
        var w = box.width, h = box.height;
        if((adjust && !this.autoBoxAdjust) && !this.isBorderBox()){
           w -= (this.getBorderWidth("lr") + this.getPadding("lr"));
           h -= (this.getBorderWidth("tb") + this.getPadding("tb"));
        }
        this.setBounds(box.x, box.y, w, h, this.preanim(arguments, 2));
        return this;
    },

    /**
     * Forces the browser to repaint this element
     * @return {Ext.Element} this
     */
     repaint : function(){
        var dom = this.dom;
        this.addClass("x-repaint");
        setTimeout(function(){
            Ext.get(dom).removeClass("x-repaint");
        }, 1);
        return this;
    },

    /**
     * Returns an object with properties top, left, right and bottom representing the margins of this element unless sides is passed,
     * then it returns the calculated width of the sides (see getPadding)
     * @param {String} sides (optional) Any combination of l, r, t, b to get the sum of those sides
     * @return {Object/Number}
     */
    getMargins : function(side){
        if(!side){
            return {
                top: parseInt(this.getStyle("margin-top"), 10) || 0,
                left: parseInt(this.getStyle("margin-left"), 10) || 0,
                bottom: parseInt(this.getStyle("margin-bottom"), 10) || 0,
                right: parseInt(this.getStyle("margin-right"), 10) || 0
            };
        }else{
            return this.addStyles(side, El.margins);
         }
    },

    // private
    addStyles : function(sides, styles){
        var val = 0, v, w;
        for(var i = 0, len = sides.length; i < len; i++){
            v = this.getStyle(styles[sides.charAt(i)]);
            if(v){
                 w = parseInt(v, 10);
                 if(w){ val += w; }
            }
        }
        return val;
    },

    /**
     * Creates a proxy element of this element
     * @param {String/Object} config The class name of the proxy element or a DomHelper config object
     * @param {String/HTMLElement} renderTo (optional) The element or element id to render the proxy to (defaults to document.body)
     * @param {Boolean} matchBox (optional) True to align and size the proxy to this element now (defaults to false)
     * @return {Ext.Element} The new proxy element
     */
    createProxy : function(config, renderTo, matchBox){
        if(renderTo){
            renderTo = Ext.getDom(renderTo);
        }else{
            renderTo = document.body;
        }
        config = typeof config == "object" ?
            config : {tag : "div", cls: config};
        var proxy = Ext.DomHelper.append(renderTo, config, true);
        if(matchBox){
           proxy.setBox(this.getBox());
        }
        return proxy;
    },

    /**
     * Puts a mask over this element to disable user interaction. Requires core.css.
     * This method can only be applied to elements which accept child nodes.
     * @param {String} msg (optional) A message to display in the mask
     * @param {String} msgCls (optional) A css class to apply to the msg element
     * @return {Element} The mask  element
     */
    mask : function(msg, msgCls){
        if(this.getStyle("position") == "static"){
            this.setStyle("position", "relative");
        }
        if(!this._mask){
            this._mask = Ext.DomHelper.append(this.dom, {cls:"ext-el-mask"}, true);
        }
        this.addClass("x-masked");
        this._mask.setDisplayed(true);
        if(typeof msg == 'string'){
            if(!this._maskMsg){
                this._maskMsg = Ext.DomHelper.append(this.dom, {cls:"ext-el-mask-msg", cn:{tag:'div'}}, true);
            }
            var mm = this._maskMsg;
            mm.dom.className = msgCls ? "ext-el-mask-msg " + msgCls : "ext-el-mask-msg";
            mm.dom.firstChild.innerHTML = msg;
            mm.setDisplayed(true);
            mm.center(this);
        }
        if(Ext.isIE && !(Ext.isIE7 && Ext.isStrict) && this.getStyle('height') == 'auto'){ // ie will not expand full height automatically
            this._mask.setHeight(this.getHeight());
        }
        return this._mask;
    },

    /**
     * Removes a previously applied mask. If removeEl is true the mask overlay is destroyed, otherwise
     * it is cached for reuse.
     */
    unmask : function(removeEl){
        if(this._mask){
            if(removeEl === true){
                this._mask.remove();
                delete this._mask;
                if(this._maskMsg){
                    this._maskMsg.remove();
                    delete this._maskMsg;
                }
            }else{
                this._mask.setDisplayed(false);
                if(this._maskMsg){
                    this._maskMsg.setDisplayed(false);
                }
            }
        }
        this.removeClass("x-masked");
    },

    /**
     * Returns true if this element is masked
     * @return {Boolean}
     */
    isMasked : function(){
        return this._mask && this._mask.isVisible();
    },

    /**
     * Creates an iframe shim for this element to keep selects and other windowed objects from
     * showing through.
     * @return {Ext.Element} The new shim element
     */
    createShim : function(){
        var el = document.createElement('iframe');
        el.frameBorder = 'no';
        el.className = 'ext-shim';
        if(Ext.isIE && Ext.isSecure){
            el.src = Ext.SSL_SECURE_URL;
        }
        var shim = Ext.get(this.dom.parentNode.insertBefore(el, this.dom));
        shim.autoBoxAdjust = false;
        return shim;
    },

    /**
     * Removes this element from the DOM and deletes it from the cache
     */
    remove : function(){
        if(this.dom.parentNode){
            this.dom.parentNode.removeChild(this.dom);
        }
        delete El.cache[this.dom.id];
    },

    /**
     * Sets up event handlers to add and remove a css class when the mouse is over this element
     * @param {String} className
     * @param {Boolean} preventFlicker (optional) If set to true, it prevents flickering by filtering
     * mouseout events for children elements
     * @return {Ext.Element} this
     */
    addClassOnOver : function(className, preventFlicker){
        this.on("mouseover", function(){
            Ext.fly(this, '_internal').addClass(className);
        }, this.dom);
        var removeFn = function(e){
            if(preventFlicker !== true || !e.within(this, true)){
                Ext.fly(this, '_internal').removeClass(className);
            }
        };
        this.on("mouseout", removeFn, this.dom);
        return this;
    },

    /**
     * Sets up event handlers to add and remove a css class when this element has the focus
     * @param {String} className
     * @return {Ext.Element} this
     */
    addClassOnFocus : function(className){
        this.on("focus", function(){
            Ext.fly(this, '_internal').addClass(className);
        }, this.dom);
        this.on("blur", function(){
            Ext.fly(this, '_internal').removeClass(className);
        }, this.dom);
        return this;
    },
    /**
     * Sets up event handlers to add and remove a css class when the mouse is down and then up on this element (a click effect)
     * @param {String} className
     * @return {Ext.Element} this
     */
    addClassOnClick : function(className){
        var dom = this.dom;
        this.on("mousedown", function(){
            Ext.fly(dom, '_internal').addClass(className);
            var d = Ext.get(document);
            var fn = function(){
                Ext.fly(dom, '_internal').removeClass(className);
                d.removeListener("mouseup", fn);
            };
            d.on("mouseup", fn);
        });
        return this;
    },

    /**
     * Stops the specified event from bubbling and optionally prevents the default action
     * @param {String} eventName
     * @param {Boolean} preventDefault (optional) true to prevent the default action too
     * @return {Ext.Element} this
     */
    swallowEvent : function(eventName, preventDefault){
        var fn = function(e){
            e.stopPropagation();
            if(preventDefault){
                e.preventDefault();
            }
        };
        if(eventName instanceof Array){
            for(var i = 0, len = eventName.length; i < len; i++){
                 this.on(eventName[i], fn);
            }
            return this;
        }
        this.on(eventName, fn);
        return this;
    },

    /**
     * @private
     */
  fitToParentDelegate : Ext.emptyFn, // keep a reference to the fitToParent delegate

    /**
     * Sizes this element to its parent element's dimensions performing
     * neccessary box adjustments.
     * @param {Boolean} monitorResize (optional) If true maintains the fit when the browser window is resized.
     * @param {String/HTMLElment/Element} targetParent (optional) The target parent, default to the parentNode.
     * @return {Ext.Element} this
     */
    fitToParent : function(monitorResize, targetParent) {
      Ext.EventManager.removeResizeListener(this.fitToParentDelegate); // always remove previous fitToParent delegate from onWindowResize
      this.fitToParentDelegate = Ext.emptyFn; // remove reference to previous delegate
      if (monitorResize === true && !this.dom.parentNode) { // check if this Element still exists
        return;
      }
      var p = Ext.get(targetParent || this.dom.parentNode);
      this.setSize(p.getComputedWidth() - p.getFrameWidth('lr'), p.getComputedHeight() - p.getFrameWidth('tb'));
      if (monitorResize === true) {
        this.fitToParentDelegate = this.fitToParent.createDelegate(this, [true, targetParent]);
        Ext.EventManager.onWindowResize(this.fitToParentDelegate);
      }
      return this;
    },

    /**
     * Gets the next sibling, skipping text nodes
     * @return {HTMLElement} The next sibling or null
	 */
    getNextSibling : function(){
        var n = this.dom.nextSibling;
        while(n && n.nodeType != 1){
            n = n.nextSibling;
        }
        return n;
    },

    /**
     * Gets the previous sibling, skipping text nodes
     * @return {HTMLElement} The previous sibling or null
	 */
    getPrevSibling : function(){
        var n = this.dom.previousSibling;
        while(n && n.nodeType != 1){
            n = n.previousSibling;
        }
        return n;
    },


    /**
     * Appends the passed element(s) to this element
     * @param {String/HTMLElement/Array/Element/CompositeElement} el
     * @return {Ext.Element} this
     */
    appendChild: function(el){
        el = Ext.get(el);
        el.appendTo(this);
        return this;
    },

    /**
     * Creates the passed DomHelper config and appends it to this element or optionally inserts it before the passed child element.
     * @param {Object} config DomHelper element config object.  If no tag is specified (e.g., {tag:'input'}) then a div will be
     * automatically generated with the specified attributes.
     * @param {HTMLElement} insertBefore (optional) a child element of this element
     * @param {Boolean} returnDom (optional) true to return the dom node instead of creating an Element
     * @return {Ext.Element} The new child element
     */
    createChild: function(config, insertBefore, returnDom){
        config = config || {tag:'div'};
        if(insertBefore){
            return Ext.DomHelper.insertBefore(insertBefore, config, returnDom !== true);
        }
        return Ext.DomHelper[!this.dom.firstChild ? 'overwrite' : 'append'](this.dom, config,  returnDom !== true);
    },

    /**
     * Appends this element to the passed element
     * @param {String/HTMLElement/Element} el The new parent element
     * @return {Ext.Element} this
     */
    appendTo: function(el){
        el = Ext.getDom(el);
        el.appendChild(this.dom);
        return this;
    },

    /**
     * Inserts this element before the passed element in the DOM
     * @param {String/HTMLElement/Element} el The element to insert before
     * @return {Ext.Element} this
     */
    insertBefore: function(el){
        el = Ext.getDom(el);
        el.parentNode.insertBefore(this.dom, el);
        return this;
    },

    /**
     * Inserts this element after the passed element in the DOM
     * @param {String/HTMLElement/Element} el The element to insert after
     * @return {Ext.Element} this
     */
    insertAfter: function(el){
        el = Ext.getDom(el);
        el.parentNode.insertBefore(this.dom, el.nextSibling);
        return this;
    },

    /**
     * Inserts (or creates) an element (or DomHelper config) as the first child of the this element
     * @param {String/HTMLElement/Element/Object} el The id or element to insert or a DomHelper config to create and insert
     * @return {Ext.Element} The new child
     */
    insertFirst: function(el, returnDom){
        el = el || {};
        if(typeof el == 'object' && !el.nodeType){ // dh config
            return this.createChild(el, this.dom.firstChild, returnDom);
        }else{
            el = Ext.getDom(el);
            this.dom.insertBefore(el, this.dom.firstChild);
            return !returnDom ? Ext.get(el) : el;
        }
    },

    /**
     * Inserts (or creates) the passed element (or DomHelper config) as a sibling of this element
     * @param {String/HTMLElement/Element/Object} el The id or element to insert or a DomHelper config to create and insert
     * @param {String} where (optional) 'before' or 'after' defaults to before
     * @param {Boolean} returnDom (optional) True to return the raw DOM element instead of Ext.Element
     * @return {Ext.Element} the inserted Element
     */
    insertSibling: function(el, where, returnDom){
        where = where ? where.toLowerCase() : 'before';
        el = el || {};
        var rt, refNode = where == 'before' ? this.dom : this.dom.nextSibling;

        if(typeof el == 'object' && !el.nodeType){ // dh config
            if(where == 'after' && !this.dom.nextSibling){
                rt = Ext.DomHelper.append(this.dom.parentNode, el, !returnDom);
            }else{
                rt = Ext.DomHelper[where == 'after' ? 'insertAfter' : 'insertBefore'](this.dom, el, !returnDom);
            }

        }else{
            rt = this.dom.parentNode.insertBefore(Ext.getDom(el),
                        where == 'before' ? this.dom : this.dom.nextSibling);
            if(!returnDom){
                rt = Ext.get(rt);
            }
        }
        return rt;
    },

    /**
     * Creates and wraps this element with another element
     * @param {Object} config (optional) DomHelper element config object for the wrapper element or null for an empty div
     * @param {Boolean} returnDom (optional) True to return the raw DOM element instead of Ext.Element
     * @return {HTMLElement/Element} The newly created wrapper element
     */
    wrap: function(config, returnDom){
        if(!config){
            config = {tag: "div"};
        }
        var newEl = Ext.DomHelper.insertBefore(this.dom, config, !returnDom);
        newEl.dom ? newEl.dom.appendChild(this.dom) : newEl.appendChild(this.dom);
        return newEl;
    },

    /**
     * Replaces the passed element with this element
     * @param {String/HTMLElement/Element} el The element to replace
     * @return {Ext.Element} this
     */
    replace: function(el){
        el = Ext.get(el);
        this.insertBefore(el);
        el.remove();
        return this;
    },

    /**
     * Inserts an html fragment into this element
     * @param {String} where Where to insert the html in relation to the this element - beforeBegin, afterBegin, beforeEnd, afterEnd.
     * @param {String} html The HTML fragment
     * @param {Boolean} returnEl True to return an Ext.Element
     * @return {HTMLElement/Ext.Element} The inserted node (or nearest related if more than 1 inserted)
     */
    insertHtml : function(where, html, returnEl){
        var el = Ext.DomHelper.insertHtml(where, this.dom, html);
        return returnEl ? Ext.get(el) : el;
    },

    /**
     * Sets the passed attributes as attributes of this element (a style attribute can be a string, object or function)
     * @param {Object} o The object with the attributes
     * @param {Boolean} useSet (optional) false to override the default setAttribute to use expandos.
     * @return {Ext.Element} this
     */
    set : function(o, useSet){
        var el = this.dom;
        useSet = typeof useSet == 'undefined' ? (el.setAttribute ? true : false) : useSet;
        for(var attr in o){
            if(attr == "style" || typeof o[attr] == "function") continue;
            if(attr=="cls"){
                el.className = o["cls"];
            }else{
                if(useSet) el.setAttribute(attr, o[attr]);
                else el[attr] = o[attr];
            }
        }
        if(o.style){
            Ext.DomHelper.applyStyles(el, o.style);
        }
        return this;
    },

    /**
     * Convenience method for constructing a KeyMap
     * @param {Number/Array/Object/String} key Either a string with the keys to listen for, the numeric key code, array of key codes or an object with the following options:
     *                                  {key: (number or array), shift: (true/false), ctrl: (true/false), alt: (true/false)}
     * @param {Function} fn The function to call
     * @param {Object} scope (optional) The scope of the function
     * @return {Ext.KeyMap} The KeyMap created
     */
    addKeyListener : function(key, fn, scope){
        var config;
        if(typeof key != "object" || key instanceof Array){
            config = {
                key: key,
                fn: fn,
                scope: scope
            };
        }else{
            config = {
                key : key.key,
                shift : key.shift,
                ctrl : key.ctrl,
                alt : key.alt,
                fn: fn,
                scope: scope
            };
        }
        return new Ext.KeyMap(this, config);
    },

    /**
     * Creates a KeyMap for this element
     * @param {Object} config The KeyMap config. See {@link Ext.KeyMap} for more details
     * @return {Ext.KeyMap} The KeyMap created
     */
    addKeyMap : function(config){
        return new Ext.KeyMap(this, config);
    },

    /**
     * Returns true if this element is scrollable.
     * @return {Boolean}
     */
     isScrollable : function(){
        var dom = this.dom;
        return dom.scrollHeight > dom.clientHeight || dom.scrollWidth > dom.clientWidth;
    },

    /**
     * Scrolls this element the specified scroll point. It does NOT do bounds checking so if you scroll to a weird value it will try to do it. For auto bounds checking, use scroll().
     * @param {String} side Either "left" for scrollLeft values or "top" for scrollTop values.
     * @param {Number} value The new scroll value
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Element} this
     */

    scrollTo : function(side, value, animate){
        var prop = side.toLowerCase() == "left" ? "scrollLeft" : "scrollTop";
        if(!animate || !A){
            this.dom[prop] = value;
        }else{
            var to = prop == "scrollLeft" ? [value, this.dom.scrollTop] : [this.dom.scrollLeft, value];
            this.anim({scroll: {"to": to}}, this.preanim(arguments, 2), 'scroll');
        }
        return this;
    },

    /**
     * Scrolls this element the specified direction. Does bounds checking to make sure the scroll is
     * within this element's scrollable range.
     * @param {String} direction Possible values are: "l","left" - "r","right" - "t","top","up" - "b","bottom","down".
     * @param {Number} distance How far to scroll the element in pixels
     * @param {Boolean/Object} animate (optional) true for the default animation or a standard Element animation config object
     * @return {Boolean} Returns true if a scroll was triggered or false if the element
     * was scrolled as far as it could go.
     */
     scroll : function(direction, distance, animate){
         if(!this.isScrollable()){
             return;
         }
         var el = this.dom;
         var l = el.scrollLeft, t = el.scrollTop;
         var w = el.scrollWidth, h = el.scrollHeight;
         var cw = el.clientWidth, ch = el.clientHeight;
         direction = direction.toLowerCase();
         var scrolled = false;
         var a = this.preanim(arguments, 2);
         switch(direction){
             case "l":
             case "left":
                 if(w - l > cw){
                     var v = Math.min(l + distance, w-cw);
                     this.scrollTo("left", v, a);
                     scrolled = true;
                 }
                 break;
            case "r":
            case "right":
                 if(l > 0){
                     var v = Math.max(l - distance, 0);
                     this.scrollTo("left", v, a);
                     scrolled = true;
                 }
                 break;
            case "t":
            case "top":
            case "up":
                 if(t > 0){
                     var v = Math.max(t - distance, 0);
                     this.scrollTo("top", v, a);
                     scrolled = true;
                 }
                 break;
            case "b":
            case "bottom":
            case "down":
                 if(h - t > ch){
                     var v = Math.min(t + distance, h-ch);
                     this.scrollTo("top", v, a);
                     scrolled = true;
                 }
                 break;
         }
         return scrolled;
    },

    /**
     * Translates the passed page coordinates into left/top css values for this element
     * @param {Number/Array} x The page x or an array containing [x, y]
     * @param {Number} y The page y
     * @return {Object} An object with left and top properties. e.g. {left: (value), top: (value)}
     */
    translatePoints : function(x, y){
        if(typeof x == 'object' || x instanceof Array){
            y = x[1]; x = x[0];
        }
        var p = this.getStyle('position');
        var o = this.getXY();

        var l = parseInt(this.getStyle('left'), 10);
        var t = parseInt(this.getStyle('top'), 10);

        if(isNaN(l)){
            l = (p == "relative") ? 0 : this.dom.offsetLeft;
        }
        if(isNaN(t)){
            t = (p == "relative") ? 0 : this.dom.offsetTop;
        }

        return {left: (x - o[0] + l), top: (y - o[1] + t)};
    },

    /**
     * Returns the current scroll position of the element.
     * @return {Object} An object containing the scroll position in the format {left: (scrollLeft), top: (scrollTop)}
     */
    getScroll : function(){
        var d = this.dom, doc = document;
        if(d == doc || d == doc.body){
            var l = window.pageXOffset || doc.documentElement.scrollLeft || doc.body.scrollLeft || 0;
            var t = window.pageYOffset || doc.documentElement.scrollTop || doc.body.scrollTop || 0;
            return {left: l, top: t};
        }else{
            return {left: d.scrollLeft, top: d.scrollTop};
        }
    },

    /**
     * Return the CSS color for the specified CSS attribute. rgb, 3 digit (like #fff) and valid values
     * are convert to standard 6 digit hex color.
     * @param {String} attr The css attribute
     * @param {String} defaultValue The default value to use when a valid color isn't found
     * @param {String} prefix (optional) defaults to #. Use an empty string when working with
     * YUI color anims.
     */
    getColor : function(attr, defaultValue, prefix){
        var v = this.getStyle(attr);
        if(!v || v == "transparent" || v == "inherit") {
            return defaultValue;
        }
        var color = typeof prefix == "undefined" ? "#" : prefix;
        if(v.substr(0, 4) == "rgb("){
            var rvs = v.slice(4, v.length -1).split(",");
            for(var i = 0; i < 3; i++){
                var h = parseInt(rvs[i]).toString(16);
                if(h < 16){
                    h = "0" + h;
                }
                color += h;
            }
        } else {
            if(v.substr(0, 1) == "#"){
                if(v.length == 4) {
                    for(var i = 1; i < 4; i++){
                        var c = v.charAt(i);
                        color +=  c + c;
                    }
                }else if(v.length == 7){
                    color += v.substr(1);
                }
            }
        }
        return(color.length > 5 ? color.toLowerCase() : defaultValue);
    },

    /**
     * Wraps the specified element with a special markup/CSS block that renders by default as a gray container with a
     * gradient background, rounded corners and a 4-way shadow.
     * @param {String} class (optional) A base CSS class to apply to the containing wrapper element (defaults to 'x-box').
     * Note that there are a number of CSS rules that are dependent on this name to make the overall effect work,
     * so if you supply an alternate base class, make sure you also supply all of the necessary rules.
     * @return {Ext.Element} this
     */
    boxWrap : function(cls){
        cls = cls || 'x-box';
        var el = Ext.get(this.insertHtml('beforeBegin', String.format('<div class="{0}">'+El.boxMarkup+'</div>', cls)));
        el.child('.'+cls+'-mc').dom.appendChild(this.dom);
        return el;
    },

    /**
     * Returns the value of a namespaced attribute from the element's underlying DOM node.
     * @param {String} namespace The namespace in which to look for the attribute
     * @param {String} name The attribute name
     * @return {String} The attribute value
     */
    getAttributeNS : Ext.isIE ? function(ns, name){
        var d = this.dom;
        var type = typeof d[ns+":"+name];
        if(type != 'undefined' && type != 'unknown'){
            return d[ns+":"+name];
        }
        return d[name];
    } : function(ns, name){
        var d = this.dom;
        return d.getAttributeNS(ns, name) || d.getAttribute(ns+":"+name) || d.getAttribute(name) || d[name];
    }
};

var ep = El.prototype;

/**
 * Appends an event handler (Shorthand for addListener)
 * @param {String}   eventName     The type of event to append
 * @param {Function} fn        The method the event invokes
 * @param {Object} scope       (optional) The scope (this object) of the fn
 * @param {Object}   options   (optional)An object with standard {@link Ext.EventManager#addListener} options
 * @method
 */
ep.on = ep.addListener;
    // backwards compat
ep.mon = ep.addListener;

/**
 * Removes an event handler from this element (shorthand for removeListener)
 * @param {String} eventName the type of event to remove
 * @param {Function} fn the method the event invokes
 * @return {Ext.Element} this
 * @method
 */
ep.un = ep.removeListener;

/**
 * true to automatically adjust width and height settings for box-model issues (default to true)
 */
ep.autoBoxAdjust = true;

// private
El.unitPattern = /\d+(px|em|%|en|ex|pt|in|cm|mm|pc)$/i;

// private
El.addUnits = function(v, defaultUnit){
    if(v === "" || v == "auto"){
        return v;
    }
    if(v === undefined){
        return '';
    }
    if(typeof v == "number" || !El.unitPattern.test(v)){
        return v + (defaultUnit || 'px');
    }
    return v;
};

// special markup used throughout Ext when box wrapping elements
El.boxMarkup = '<div class="{0}-tl"><div class="{0}-tr"><div class="{0}-tc"></div></div></div><div class="{0}-ml"><div class="{0}-mr"><div class="{0}-mc"></div></div></div><div class="{0}-bl"><div class="{0}-br"><div class="{0}-bc"></div></div></div>';
/**
 * Visibility mode constant - Use visibility to hide element
 * @static
 * @type Number
 */
El.VISIBILITY = 1;
/**
 * Visibility mode constant - Use display to hide element
 * @static
 * @type Number
 */
El.DISPLAY = 2;

El.borders = {l: "border-left-width", r: "border-right-width", t: "border-top-width", b: "border-bottom-width"};
El.paddings = {l: "padding-left", r: "padding-right", t: "padding-top", b: "padding-bottom"};
El.margins = {l: "margin-left", r: "margin-right", t: "margin-top", b: "margin-bottom"};



/**
 * @private
 */
El.cache = {};

var docEl;

/**
 * Static method to retrieve Element objects. Uses simple caching to consistently return the same object.
 * Automatically fixes if an object was recreated with the same id via AJAX or DOM.
 * @param {String/HTMLElement/Element} el The id of the node, a DOM Node or an existing Element.
 * @return {Element} The Element object
 * @static
 */
El.get = function(el){
    var ex, elm, id;
    if(!el){ return null; }
    if(typeof el == "string"){ // element id
        if(!(elm = document.getElementById(el))){
            return null;
        }
        if(ex = El.cache[el]){
            ex.dom = elm;
        }else{
            ex = El.cache[el] = new El(elm);
        }
        return ex;
    }else if(el.tagName){ // dom element
        if(!(id = el.id)){
            id = Ext.id(el);
        }
        if(ex = El.cache[id]){
            ex.dom = el;
        }else{
            ex = El.cache[id] = new El(el);
        }
        return ex;
    }else if(el instanceof El){
        if(el != docEl){
            el.dom = document.getElementById(el.id) || el.dom; // refresh dom element in case no longer valid,
                                                          // catch case where it hasn't been appended
            El.cache[el.id] = el; // in case it was created directly with Element(), let's cache it
        }
        return el;
    }else if(el.isComposite){
        return el;
    }else if(el instanceof Array){
        return El.select(el);
    }else if(el == document){
        // create a bogus element object representing the document object
        if(!docEl){
            var f = function(){};
            f.prototype = El.prototype;
            docEl = new f();
            docEl.dom = document;
        }
        return docEl;
    }
    return null;
};

// private
El.uncache = function(el){
    for(var i = 0, a = arguments, len = a.length; i < len; i++) {
        if(a[i]){
            delete El.cache[a[i].id || a[i]];
        }
    }
};

// private
// Garbage collection - uncache elements/purge listeners on orphaned elements
// so we don't hold a reference and cause the browser to retain them
El.garbageCollect = function(){
    if(!Ext.enableGarbageCollector){
        clearInterval(El.collectorThread);
        return;
    }
    for(var eid in El.cache){
        var el = El.cache[eid], d = el.dom;
        // -------------------------------------------------------
        // Determining what is garbage:
        // -------------------------------------------------------
        // !d
        // dom node is null, definitely garbage
        // -------------------------------------------------------
        // !d.parentNode
        // no parentNode == direct orphan, definitely garbage
        // -------------------------------------------------------
        // !d.offsetParent && !document.getElementById(eid)
        // display none elements have no offsetParent so we will
        // also try to look it up by it's id. However, check
        // offsetParent first so we don't do unneeded lookups.
        // This enables collection of elements that are not orphans
        // directly, but somewhere up the line they have an orphan
        // parent.
        // -------------------------------------------------------
        if(!d || !d.parentNode || (!d.offsetParent && !document.getElementById(eid))){
            delete El.cache[eid];
            if(d && Ext.enableListenerCollection){
                E.purgeElement(d);
            }
        }
    }
}
El.collectorThreadId = setInterval(El.garbageCollect, 30000);


// dom is optional
El.Flyweight = function(dom){
    this.dom = dom;
};
El.Flyweight.prototype = El.prototype;

El._flyweights = {};
/**
 * Gets the globally shared flyweight Element, with the passed node as the active element. Do not store a reference to this element -
 * the dom node can be overwritten by other code.
 * @param {String/HTMLElement} el The dom node or id
 * @param {String} named (optional) Allows for creation of named reusable flyweights to
 *                                  prevent conflicts (e.g. internally Ext uses "_internal")
 * @static
 * @return {Element} The shared Element object
 */
El.fly = function(el, named){
    named = named || '_global';
    el = Ext.getDom(el);
    if(!el){
        return null;
    }
    if(!El._flyweights[named]){
        El._flyweights[named] = new El.Flyweight();
    }
    El._flyweights[named].dom = el;
    return El._flyweights[named];
};

/**
 * Static method to retrieve Element objects. Uses simple caching to consistently return the same object.
 * Automatically fixes if an object was recreated with the same id via AJAX or DOM.
 * Shorthand of {@link Ext.Element#get}
 * @param {String/HTMLElement/Element} el The id of the node, a DOM Node or an existing Element.
 * @return {Element} The Element object
 * @member Ext
 * @method get
 */
Ext.get = El.get;
/**
 * Gets the globally shared flyweight Element, with the passed node as the active element. Do not store a reference to this element -
 * the dom node can be overwritten by other code.
 * Shorthand of {@link Ext.Element#fly}
 * @param {String/HTMLElement} el The dom node or id
 * @param {String} named (optional) Allows for creation of named reusable flyweights to
 *                                  prevent conflicts (e.g. internally Ext uses "_internal")
 * @static
 * @return {Element} The shared Element object
 * @member Ext
 * @method fly
 */
Ext.fly = El.fly;

// speedy lookup for elements never to box adjust
var noBoxAdjust = Ext.isStrict ? {
    select:1
} : {
    input:1, select:1, textarea:1
};
if(Ext.isIE || Ext.isGecko){
    noBoxAdjust['button'] = 1;
}


Ext.EventManager.on(window, 'beforeunload', function(){
    delete El.cache;
    delete El._flyweights;
});
})();




//Notifies Element that fx methods are available
Ext.enableFx = true;

/**
 * @class Ext.Fx
 * <p>A class to provide basic animation and visual effects support.  <b>Note:</b> This class is automatically applied
 * to the {@link Ext.Element} interface when included, so all effects calls should be performed via Element.
 * Conversely, since the effects are not actually defined in Element, Ext.Fx <b>must</b> be included in order for the 
 * Element effects to work.</p><br/>
 *
 * <p>It is important to note that although the Fx methods and many non-Fx Element methods support "method chaining" in that
 * they return the Element object itself as the method return value, it is not always possible to mix the two in a single
 * method chain.  The Fx methods use an internal effects queue so that each effect can be properly timed and sequenced.
 * Non-Fx methods, on the other hand, have no such internal queueing and will always execute immediately.  For this reason,
 * while it may be possible to mix certain Fx and non-Fx method calls in a single chain, it may not always provide the
 * expected results and should be done with care.</p><br/>
 *
 * <p>Motion effects support 8-way anchoring, meaning that you can choose one of 8 different anchor points on the Element
 * that will serve as either the start or end point of the animation.  Following are all of the supported anchor positions:</p>
<pre>
Value  Description
-----  -----------------------------
tl     The top left corner
t      The center of the top edge
tr     The top right corner
l      The center of the left edge
r      The center of the right edge
bl     The bottom left corner
b      The center of the bottom edge
br     The bottom right corner
</pre>
 * <b>Although some Fx methods accept specific custom config parameters, the ones shown in the Config Options section
 * below are common options that can be passed to any Fx method.</b>
 * @cfg {Function} callback A function called when the effect is finished
 * @cfg {Object} scope The scope of the effect function
 * @cfg {String} easing A valid Easing value for the effect
 * @cfg {String} afterCls A css class to apply after the effect
 * @cfg {Number} duration The length of time (in seconds) that the effect should last
 * @cfg {Boolean} remove Whether the Element should be removed from the DOM and destroyed after the effect finishes
 * @cfg {Boolean} useDisplay Whether to use the <i>display</i> CSS property instead of <i>visibility</i> when hiding Elements (only applies to 
 * effects that end with the element being visually hidden, ignored otherwise)
 * @cfg {String/Object/Function} afterStyle A style specification string, e.g. "width:100px", or an object in the form {width:"100px"}, or
 * a function which returns such a specification that will be applied to the Element after the effect finishes
 * @cfg {Boolean} block Whether the effect should block other effects from queueing while it runs
 * @cfg {Boolean} concurrent Whether to allow subsequently-queued effects to run at the same time as the current effect, or to ensure that they run in sequence
 * @cfg {Boolean} stopFx Whether subsequent effects should be stopped and removed after the current effect finishes
 */
Ext.Fx = {
	/**
	 * Slides the element into view.  An anchor point can be optionally passed to set the point of
	 * origin for the slide effect.  This function automatically handles wrapping the element with
	 * a fixed-size container if needed.  See the Fx class overview for valid anchor point options.
	 * Usage:
	 *<pre><code>
// default: slide the element in from the top
el.slideIn();

// custom: slide the element in from the right with a 2-second duration
el.slideIn('r', { duration: 2 });

// common config options shown with default values
el.slideIn('t', {
    easing: 'easeOut',
    duration: .5
});
</code></pre>
	 * @param {String} anchor (optional) One of the valid Fx anchor positions (defaults to top: 't')
	 * @param {Object} options (optional) Object literal with any of the Fx config options
	 * @return {Ext.Element} The Element
	 */
    slideIn : function(anchor, o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){

            anchor = anchor || "t";

            // fix display to visibility
            this.fixDisplay();

            // restore values after effect
            var r = this.getFxRestore();
            var b = this.getBox();
            // fixed size for slide
            this.setSize(b);

            // wrap if needed
            var wrap = this.fxWrap(r.pos, o, "hidden");

            var st = this.dom.style;
            st.visibility = "visible";
            st.position = "absolute";

            // clear out temp styles after slide and unwrap
            var after = function(){
                el.fxUnwrap(wrap, r.pos, o);
                st.width = r.width;
                st.height = r.height;
                el.afterFx(o);
            };
            // time to calc the positions
            var a, pt = {to: [b.x, b.y]}, bw = {to: b.width}, bh = {to: b.height};

            switch(anchor.toLowerCase()){
                case "t":
                    wrap.setSize(b.width, 0);
                    st.left = st.bottom = "0";
                    a = {height: bh};
                break;
                case "l":
                    wrap.setSize(0, b.height);
                    st.right = st.top = "0";
                    a = {width: bw};
                break;
                case "r":
                    wrap.setSize(0, b.height);
                    wrap.setX(b.right);
                    st.left = st.top = "0";
                    a = {width: bw, points: pt};
                break;
                case "b":
                    wrap.setSize(b.width, 0);
                    wrap.setY(b.bottom);
                    st.left = st.top = "0";
                    a = {height: bh, points: pt};
                break;
                case "tl":
                    wrap.setSize(0, 0);
                    st.right = st.bottom = "0";
                    a = {width: bw, height: bh};
                break;
                case "bl":
                    wrap.setSize(0, 0);
                    wrap.setY(b.y+b.height);
                    st.right = st.top = "0";
                    a = {width: bw, height: bh, points: pt};
                break;
                case "br":
                    wrap.setSize(0, 0);
                    wrap.setXY([b.right, b.bottom]);
                    st.left = st.top = "0";
                    a = {width: bw, height: bh, points: pt};
                break;
                case "tr":
                    wrap.setSize(0, 0);
                    wrap.setX(b.x+b.width);
                    st.left = st.bottom = "0";
                    a = {width: bw, height: bh, points: pt};
                break;
            }
            this.dom.style.visibility = "visible";
            wrap.show();

            arguments.callee.anim = wrap.fxanim(a,
                o,
                'motion',
                .5,
                'easeOut', after);
        });
        return this;
    },
    
	/**
	 * Slides the element out of view.  An anchor point can be optionally passed to set the end point
	 * for the slide effect.  When the effect is completed, the element will be hidden (visibility = 
	 * 'hidden') but block elements will still take up space in the document.  The element must be removed
	 * from the DOM using the 'remove' config option if desired.  This function automatically handles 
	 * wrapping the element with a fixed-size container if needed.  See the Fx class overview for valid anchor point options.
	 * Usage:
	 *<pre><code>
// default: slide the element out to the top
el.slideOut();

// custom: slide the element out to the right with a 2-second duration
el.slideOut('r', { duration: 2 });

// common config options shown with default values
el.slideOut('t', {
    easing: 'easeOut',
    duration: .5,
    remove: false,
    useDisplay: false
});
</code></pre>
	 * @param {String} anchor (optional) One of the valid Fx anchor positions (defaults to top: 't')
	 * @param {Object} options (optional) Object literal with any of the Fx config options
	 * @return {Ext.Element} The Element
	 */
    slideOut : function(anchor, o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){

            anchor = anchor || "t";

            // restore values after effect
            var r = this.getFxRestore();
            
            var b = this.getBox();
            // fixed size for slide
            this.setSize(b);

            // wrap if needed
            var wrap = this.fxWrap(r.pos, o, "visible");

            var st = this.dom.style;
            st.visibility = "visible";
            st.position = "absolute";

            wrap.setSize(b);

            var after = function(){
                if(o.useDisplay){
                    el.setDisplayed(false);
                }else{
                    el.hide();
                }

                el.fxUnwrap(wrap, r.pos, o);

                st.width = r.width;
                st.height = r.height;

                el.afterFx(o);
            };

            var a, zero = {to: 0};
            switch(anchor.toLowerCase()){
                case "t":
                    st.left = st.bottom = "0";
                    a = {height: zero};
                break;
                case "l":
                    st.right = st.top = "0";
                    a = {width: zero};
                break;
                case "r":
                    st.left = st.top = "0";
                    a = {width: zero, points: {to:[b.right, b.y]}};
                break;
                case "b":
                    st.left = st.top = "0";
                    a = {height: zero, points: {to:[b.x, b.bottom]}};
                break;
                case "tl":
                    st.right = st.bottom = "0";
                    a = {width: zero, height: zero};
                break;
                case "bl":
                    st.right = st.top = "0";
                    a = {width: zero, height: zero, points: {to:[b.x, b.bottom]}};
                break;
                case "br":
                    st.left = st.top = "0";
                    a = {width: zero, height: zero, points: {to:[b.x+b.width, b.bottom]}};
                break;
                case "tr":
                    st.left = st.bottom = "0";
                    a = {width: zero, height: zero, points: {to:[b.right, b.y]}};
                break;
            }

            arguments.callee.anim = wrap.fxanim(a,
                o,
                'motion',
                .5,
                "easeOut", after);
        });
        return this;
    },

	/**
	 * Fades the element out while slowly expanding it in all directions.  When the effect is completed, the 
	 * element will be hidden (visibility = 'hidden') but block elements will still take up space in the document. 
	 * The element must be removed from the DOM using the 'remove' config option if desired.
	 * Usage:
	 *<pre><code>
// default
el.puff();

// common config options shown with default values
el.puff({
    easing: 'easeOut',
    duration: .5,
    remove: false,
    useDisplay: false
});
</code></pre>
	 * @param {Object} options (optional) Object literal with any of the Fx config options
	 * @return {Ext.Element} The Element
	 */
    puff : function(o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){
            this.clearOpacity();
            this.show();

            // restore values after effect
            var r = this.getFxRestore();
            var st = this.dom.style;

            var after = function(){
                if(o.useDisplay){
                    el.setDisplayed(false);
                }else{
                    el.hide();
                }

                el.clearOpacity();

                el.setPositioning(r.pos);
                st.width = r.width;
                st.height = r.height;
                st.fontSize = '';
                el.afterFx(o);
            };

            var width = this.getWidth();
            var height = this.getHeight();

            arguments.callee.anim = this.fxanim({
                    width : {to: this.adjustWidth(width * 2)},
                    height : {to: this.adjustHeight(height * 2)},
                    points : {by: [-(width * .5), -(height * .5)]},
                    opacity : {to: 0},
                    fontSize: {to:200, unit: "%"}
                },
                o,
                'motion',
                .5,
                "easeOut", after);
        });
        return this;
    },

	/**
	 * Blinks the element as if it was clicked and then collapses on its center (similar to switching off a television).
	 * When the effect is completed, the element will be hidden (visibility = 'hidden') but block elements will still 
	 * take up space in the document. The element must be removed from the DOM using the 'remove' config option if desired.
	 * Usage:
	 *<pre><code>
// default
el.switchOff();

// all config options shown with default values
el.switchOff({
    easing: 'easeIn',
    duration: .3,
    remove: false,
    useDisplay: false
});
</code></pre>
	 * @param {Object} options (optional) Object literal with any of the Fx config options
	 * @return {Ext.Element} The Element
	 */
    switchOff : function(o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){
            this.clearOpacity();
            this.clip();

            // restore values after effect
            var r = this.getFxRestore();
            var st = this.dom.style;

            var after = function(){
                if(o.useDisplay){
                    el.setDisplayed(false);
                }else{
                    el.hide();
                }

                el.clearOpacity();
                el.setPositioning(r.pos);
                st.width = r.width;
                st.height = r.height;

                el.afterFx(o);
            };

            this.fxanim({opacity:{to:0.3}}, null, null, .1, null, function(){
                this.clearOpacity();
                (function(){
                    this.fxanim({
                        height:{to:1},
                        points:{by:[0, this.getHeight() * .5]}
                    }, o, 'motion', 0.3, 'easeIn', after);
                }).defer(100, this);
            });
        });
        return this;
    },

    /**
     * Highlights the Element by setting a color (applies to the background-color by default, but can be
     * changed using the "attr" config option) and then fading back to the original color. If no original
     * color is available, you should provide the "endColor" config option which will be cleared after the animation.
     * Usage:
<pre><code>
// default: highlight background to yellow
el.highlight();

// custom: highlight foreground text to blue for 2 seconds
el.highlight("0000ff", { attr: 'color', duration: 2 });

// common config options shown with default values
el.highlight("ffff9c", {
    attr: "background-color", //can be any valid CSS property (attribute) that supports a color value
    endColor: (current color) or "ffffff",
    easing: 'easeIn',
    duration: 1
});
</code></pre>
     * @param {String} color (optional) The highlight color. Should be a 6 char hex color without the leading # (defaults to yellow: 'ffff9c')
     * @param {Object} options (optional) Object literal with any of the Fx config options
     * @return {Ext.Element} The Element
     */	
    highlight : function(color, o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){
            color = color || "ffff9c";
            attr = o.attr || "backgroundColor";

            this.clearOpacity();
            this.show();

            var origColor = this.getColor(attr);
            var restoreColor = this.dom.style[attr];
            endColor = (o.endColor || origColor) || "ffffff";

            var after = function(){
                el.dom.style[attr] = restoreColor;
                el.afterFx(o);
            };

            var a = {};
            a[attr] = {from: color, to: endColor};
            arguments.callee.anim = this.fxanim(a,
                o,
                'color',
                1,
                'easeIn', after);
        });
        return this;
    },

   /**
    * Shows a ripple of exploding, attenuating borders to draw attention to an Element.
    * Usage:
<pre><code>
// default: a single light blue ripple
el.frame();

// custom: 3 red ripples lasting 3 seconds total
el.frame("ff0000", 3, { duration: 3 });

// common config options shown with default values
el.frame("C3DAF9", 1, {
    duration: 1 //duration of entire animation (not each individual ripple)
    // Note: Easing is not configurable and will be ignored if included
});
</code></pre>
    * @param {String} color (optional) The color of the border.  Should be a 6 char hex color without the leading # (defaults to light blue: 'C3DAF9').
    * @param {Number} count (optional) The number of ripples to display (defaults to 1)
    * @param {Object} options (optional) Object literal with any of the Fx config options
    * @return {Ext.Element} The Element
    */
    frame : function(color, count, o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){
            color = color || "#C3DAF9";
            if(color.length == 6){
                color = "#" + color;
            }
            count = count || 1;
            duration = o.duration || 1;
            this.show();

            var b = this.getBox();
            var animFn = function(){
                var proxy = this.createProxy({

                     style:{
                        visbility:"hidden",
                        position:"absolute",
                        "z-index":"35000", // yee haw
                        border:"0px solid " + color
                     }
                  });
                var scale = Ext.isBorderBox ? 2 : 1;
                proxy.animate({
                    top:{from:b.y, to:b.y - 20},
                    left:{from:b.x, to:b.x - 20},
                    borderWidth:{from:0, to:10},
                    opacity:{from:1, to:0},
                    height:{from:b.height, to:(b.height + (20*scale))},
                    width:{from:b.width, to:(b.width + (20*scale))}
                }, duration, function(){
                    proxy.remove();
                });
                if(--count > 0){
                     animFn.defer((duration/2)*1000, this);
                }else{
                    el.afterFx(o);
                }
            };
            animFn.call(this);
        });
        return this;
    },

   /**
    * Creates a pause before any subsequent queued effects begin.  If there are
    * no effects queued after the pause it will have no effect.
    * Usage:
<pre><code>
el.pause(1);
</code></pre>
    * @param {Number} seconds The length of time to pause (in seconds)
    * @return {Ext.Element} The Element
    */
    pause : function(seconds){
        var el = this.getFxEl();
        var o = {};

        el.queueFx(o, function(){
            setTimeout(function(){
                el.afterFx(o);
            }, seconds * 1000);
        });
        return this;
    },

   /**
    * Fade an element in (from transparent to opaque).  The ending opacity can be specified
    * using the "endOpacity" config option.
    * Usage:
<pre><code>
// default: fade in from opacity 0 to 100%
el.fadeIn();

// custom: fade in from opacity 0 to 75% over 2 seconds
el.fadeIn({ endOpacity: .75, duration: 2});

// common config options shown with default values
el.fadeIn({
    endOpacity: 1, //can be any value between 0 and 1 (e.g. .5)
    easing: 'easeOut',
    duration: .5
});
</code></pre>
    * @param {Object} options (optional) Object literal with any of the Fx config options
    * @return {Ext.Element} The Element
    */
    fadeIn : function(o){
        var el = this.getFxEl();
        o = o || {};
        el.queueFx(o, function(){
            this.setOpacity(0);
            this.fixDisplay();
            this.dom.style.visibility = 'visible';
            var to = o.endOpacity || 1;
            arguments.callee.anim = this.fxanim({opacity:{to:to}},
                o, null, .5, "easeOut", function(){
                if(to == 1){
                    this.clearOpacity();
                }
                el.afterFx(o);
            });
        });
        return this;
    },

   /**
    * Fade an element out (from opaque to transparent).  The ending opacity can be specified
    * using the "endOpacity" config option.
    * Usage:
<pre><code>
// default: fade out from the element's current opacity to 0
el.fadeOut();

// custom: fade out from the element's current opacity to 25% over 2 seconds
el.fadeOut({ endOpacity: .25, duration: 2});

// common config options shown with default values
el.fadeOut({
    endOpacity: 0, //can be any value between 0 and 1 (e.g. .5)
    easing: 'easeOut',
    duration: .5
    remove: false,
    useDisplay: false
});
</code></pre>
    * @param {Object} options (optional) Object literal with any of the Fx config options
    * @return {Ext.Element} The Element
    */
    fadeOut : function(o){
        var el = this.getFxEl();
        o = o || {};
        el.queueFx(o, function(){
            arguments.callee.anim = this.fxanim({opacity:{to:o.endOpacity || 0}},
                o, null, .5, "easeOut", function(){
                if(this.visibilityMode == Ext.Element.DISPLAY || o.useDisplay){
                     this.dom.style.display = "none";
                }else{
                     this.dom.style.visibility = "hidden";
                }
                this.clearOpacity();
                el.afterFx(o);
            });
        });
        return this;
    },

   /**
    * Animates the transition of an element's dimensions from a starting height/width
    * to an ending height/width.
    * Usage:
<pre><code>
// change height and width to 100x100 pixels
el.scale(100, 100);

// common config options shown with default values.  The height and width will default to
// the element's existing values if passed as null.
el.scale(
    [element's width],
    [element's height], {
    easing: 'easeOut',
    duration: .35
});
</code></pre>
    * @param {Number} width  The new width (pass undefined to keep the original width)
    * @param {Number} height  The new height (pass undefined to keep the original height)
    * @param {Object} options (optional) Object literal with any of the Fx config options
    * @return {Ext.Element} The Element
    */
    scale : function(w, h, o){
        this.shift(Ext.apply({}, o, {
            width: w,
            height: h
        }));
        return this;
    },

   /**
    * Animates the transition of any combination of an element's dimensions, xy position and/or opacity.
    * Any of these properties not specified in the config object will not be changed.  This effect 
    * requires that at least one new dimension, position or opacity setting must be passed in on
    * the config object in order for the function to have any effect.
    * Usage:
<pre><code>
// slide the element horizontally to x position 200 while changing the height and opacity
el.shift({ x: 200, height: 50, opacity: .8 });

// common config options shown with default values.
el.shift({
    width: [element's width],
    height: [element's height],
    x: [element's x position],
    y: [element's y position],
    opacity: [element's opacity],
    easing: 'easeOut',
    duration: .35
});
</code></pre>
    * @param {Object} options  Object literal with any of the Fx config options
    * @return {Ext.Element} The Element
    */
    shift : function(o){
        var el = this.getFxEl();
        o = o || {};
        el.queueFx(o, function(){
            var a = {}, w = o.width, h = o.height, x = o.x, y = o.y,  op = o.opacity;
            if(w !== undefined){
                a.width = {to: this.adjustWidth(w)};
            }
            if(h !== undefined){
                a.height = {to: this.adjustHeight(h)};
            }
            if(x !== undefined || y !== undefined){
                a.points = {to: [
                    x !== undefined ? x : this.getX(),
                    y !== undefined ? y : this.getY()
                ]};
            }
            if(op !== undefined){
                a.opacity = {to: op};
            }
            if(o.xy !== undefined){
                a.points = {to: o.xy};
            }
            arguments.callee.anim = this.fxanim(a,
                o, 'motion', .35, "easeOut", function(){
                el.afterFx(o);
            });
        });
        return this;
    },

	/**
	 * Slides the element while fading it out of view.  An anchor point can be optionally passed to set the 
	 * ending point of the effect.
	 * Usage:
	 *<pre><code>
// default: slide the element downward while fading out
el.ghost();

// custom: slide the element out to the right with a 2-second duration
el.ghost('r', { duration: 2 });

// common config options shown with default values
el.ghost('b', {
    easing: 'easeOut',
    duration: .5
    remove: false,
    useDisplay: false
});
</code></pre>
	 * @param {String} anchor (optional) One of the valid Fx anchor positions (defaults to bottom: 'b')
	 * @param {Object} options (optional) Object literal with any of the Fx config options
	 * @return {Ext.Element} The Element
	 */
    ghost : function(anchor, o){
        var el = this.getFxEl();
        o = o || {};

        el.queueFx(o, function(){
            anchor = anchor || "b";

            // restore values after effect
            var r = this.getFxRestore();
            var w = this.getWidth(),
                h = this.getHeight();

            var st = this.dom.style;

            var after = function(){
                if(o.useDisplay){
                    el.setDisplayed(false);
                }else{
                    el.hide();
                }

                el.clearOpacity();
                el.setPositioning(r.pos);
                st.width = r.width;
                st.height = r.height;

                el.afterFx(o);
            };

            var a = {opacity: {to: 0}, points: {}}, pt = a.points;
            switch(anchor.toLowerCase()){
                case "t":
                    pt.by = [0, -h];
                break;
                case "l":
                    pt.by = [-w, 0];
                break;
                case "r":
                    pt.by = [w, 0];
                break;
                case "b":
                    pt.by = [0, h];
                break;
                case "tl":
                    pt.by = [-w, -h];
                break;
                case "bl":
                    pt.by = [-w, h];
                break;
                case "br":
                    pt.by = [w, h];
                break;
                case "tr":
                    pt.by = [w, -h];
                break;
            }

            arguments.callee.anim = this.fxanim(a,
                o,
                'motion',
                .5,
                "easeOut", after);
        });
        return this;
    },

	/**
	 * Ensures that all effects queued after syncFx is called on the element are
	 * run concurrently.  This is the opposite of {@link #sequenceFx}.
	 * @return {Ext.Element} The Element
	 */
    syncFx : function(){
        this.fxDefaults = Ext.apply(this.fxDefaults || {}, {
            block : false,
            concurrent : true,
            stopFx : false
        });
        return this;
    },

	/**
	 * Ensures that all effects queued after sequenceFx is called on the element are
	 * run in sequence.  This is the opposite of {@link #syncFx}.
	 * @return {Ext.Element} The Element
	 */
    sequenceFx : function(){
        this.fxDefaults = Ext.apply(this.fxDefaults || {}, {
            block : false,
            concurrent : false,
            stopFx : false
        });
        return this;
    },

	/* @private */
    nextFx : function(){
        var ef = this.fxQueue[0];
        if(ef){
            ef.call(this);
        }
    },

	/**
	 * Returns true if the element has any effects actively running or queued, else returns false.
	 * @return {Boolean} True if element has active effects, else false
	 */
    hasActiveFx : function(){
        return this.fxQueue && this.fxQueue[0];
    },

	/**
	 * Stops any running effects and clears the element's internal effects queue if it contains
	 * any additional effects that haven't started yet.
	 * @return {Ext.Element} The Element
	 */
    stopFx : function(){
        if(this.hasActiveFx()){
            var cur = this.fxQueue[0];
            if(cur && cur.anim && cur.anim.isAnimated()){
                this.fxQueue = [cur]; // clear out others
                cur.anim.stop(true);
            }
        }
        return this;
    },

	/* @private */
    beforeFx : function(o){
        if(this.hasActiveFx() && !o.concurrent){
           if(o.stopFx){
               this.stopFx();
               return true;
           }
           return false;
        }
        return true;
    },

	/**
	 * Returns true if the element is currently blocking so that no other effect can be queued
	 * until this effect is finished, else returns false if blocking is not set.  This is commonly
	 * used to ensure that an effect initiated by a user action runs to completion prior to the
	 * same effect being restarted (e.g., firing only one effect even if the user clicks several times).
	 * @return {Boolean} True if blocking, else false
	 */
    hasFxBlock : function(){
        var q = this.fxQueue;
        return q && q[0] && q[0].block;
    },

	/* @private */
    queueFx : function(o, fn){
        if(!this.fxQueue){
            this.fxQueue = [];
        }
        if(!this.hasFxBlock()){
            Ext.applyIf(o, this.fxDefaults);
            if(!o.concurrent){
                var run = this.beforeFx(o);
                fn.block = o.block;
                this.fxQueue.push(fn);
                if(run){
                    this.nextFx();
                }
            }else{
                fn.call(this);
            }
        }
        return this;
    },

	/* @private */
    fxWrap : function(pos, o, vis){
        var wrap;
        if(!o.wrap || !(wrap = Ext.get(o.wrap))){
            var wrapXY;
            if(o.fixPosition){
                wrapXY = this.getXY();
            }
            var div = document.createElement("div");
            div.style.visibility = vis;
            wrap = Ext.get(this.dom.parentNode.insertBefore(div, this.dom));
            wrap.setPositioning(pos);
            if(wrap.getStyle("position") == "static"){
                wrap.position("relative");
            }
            this.clearPositioning('auto');
            wrap.clip();
            wrap.dom.appendChild(this.dom);
            if(wrapXY){
                wrap.setXY(wrapXY);
            }
        }
        return wrap;
    },

	/* @private */
    fxUnwrap : function(wrap, pos, o){
        this.clearPositioning();
        this.setPositioning(pos);
        if(!o.wrap){
            wrap.dom.parentNode.insertBefore(this.dom, wrap.dom);
            wrap.remove();
        }
    },

	/* @private */
    getFxRestore : function(){
        var st = this.dom.style;
        return {pos: this.getPositioning(), width: st.width, height : st.height};
    },

	/* @private */
    afterFx : function(o){
        if(o.afterStyle){
            this.applyStyles(o.afterStyle);
        }
        if(o.afterCls){
            this.addClass(o.afterCls);
        }
        if(o.remove === true){
            this.remove();
        }
        Ext.callback(o.callback, o.scope, [this]);
        if(!o.concurrent){
            this.fxQueue.shift();
            this.nextFx();
        }
    },

	/* @private */
    getFxEl : function(){ // support for composite element fx
        return Ext.get(this.dom);
    },

	/* @private */
    fxanim : function(args, opt, animType, defaultDur, defaultEase, cb){
        animType = animType || 'run';
        opt = opt || {};
        var anim = Ext.lib.Anim[animType](
            this.dom, args,
            (opt.duration || defaultDur) || .35,
            (opt.easing || defaultEase) || 'easeOut',
            function(){
                Ext.callback(cb, this);
            },
            this
        );
        opt.anim = anim;
        return anim;
    }
};

// backwords compat
Ext.Fx.resize = Ext.Fx.scale;

//When included, Ext.Fx is automatically applied to Element so that all basic
//effects are available directly via the Element API
Ext.apply(Ext.Element.prototype, Ext.Fx);




/**
 * @class Ext.UpdateManager
 * @extends Ext.util.Observable
 * Provides AJAX-style update for Element object.<br><br>
 * Usage:<br>
 * <pre><code>
 * // Get it from a Ext.Element object
 * var el = Ext.get("foo");
 * var mgr = el.getUpdateManager();
 * mgr.update("http://myserver.com/index.php", "param1=1&amp;param2=2");
 * ...
 * mgr.formUpdate("myFormId", "http://myserver.com/index.php");
 * <br>
 * // or directly (returns the same UpdateManager instance)
 * var mgr = new Ext.UpdateManager("myElementId");
 * mgr.startAutoRefresh(60, "http://myserver.com/index.php");
 * mgr.on("update", myFcnNeedsToKnow);
 * <br>
   // short handed call directly from the element object
   Ext.get("foo").load({
        url: "bar.php",
        scripts:true,
        params: "for=bar",
        text: "Loading Foo..."
   });
 * </code></pre>
 * @constructor
 * Create new UpdateManager directly.
 * @param {String/HTMLElement/Ext.Element} el The element to update
 * @param {Boolean} forceNew (optional) By default the constructor checks to see if the passed element already has an UpdateManager and if it does it returns the same instance. This will skip that check (useful for extending this class).
 */
Ext.UpdateManager = function(el, forceNew){
    el = Ext.get(el);
    if(!forceNew && el.updateManager){
        return el.updateManager;
    }
    /**
     * The Element object
     * @type Ext.Element
     */
    this.el = el;
    /**
     * Cached url to use for refreshes. Overwritten every time update() is called unless "discardUrl" param is set to true.
     * @type String
     */
    this.defaultUrl = null;

    this.addEvents({
        /**
         * @event beforeupdate
         * Fired before an update is made, return false from your handler and the update is cancelled.
         * @param {Ext.Element} el
         * @param {String/Object/Function} url
         * @param {String/Object} params
         */
        "beforeupdate": true,
        /**
         * @event update
         * Fired after successful update is made.
         * @param {Ext.Element} el
         * @param {Object} oResponseObject The response Object
         */
        "update": true,
        /**
         * @event failure
         * Fired on update failure.
         * @param {Ext.Element} el
         * @param {Object} oResponseObject The response Object
         */
        "failure": true
    });
    var d = Ext.UpdateManager.defaults;
    /**
     * Blank page URL to use with SSL file uploads (Defaults to Ext.UpdateManager.defaults.sslBlankUrl or "about:blank").
     * @type String
     */
    this.sslBlankUrl = d.sslBlankUrl;
    /**
     * Whether to append unique parameter on get request to disable caching (Defaults to Ext.UpdateManager.defaults.disableCaching or false).
     * @type Boolean
     */
    this.disableCaching = d.disableCaching;
    /**
     * Text for loading indicator (Defaults to Ext.UpdateManager.defaults.indicatorText or '&lt;div class="loading-indicator"&gt;Loading...&lt;/div&gt;').
     * @type String
     */
    this.indicatorText = d.indicatorText;
    /**
     * Whether to show indicatorText when loading (Defaults to Ext.UpdateManager.defaults.showLoadIndicator or true).
     * @type String
     */
    this.showLoadIndicator = d.showLoadIndicator;
    /**
     * Timeout for requests or form posts in seconds (Defaults to Ext.UpdateManager.defaults.timeout or 30 seconds).
     * @type Number
     */
    this.timeout = d.timeout;

    /**
     * True to process scripts in the output (Defaults to Ext.UpdateManager.defaults.loadScripts (false)).
     * @type Boolean
     */
    this.loadScripts = d.loadScripts;

    /**
     * Transaction object of current executing transaction
     */
    this.transaction = null;

    /**
     * @private
     */
    this.autoRefreshProcId = null;
    /**
     * Delegate for refresh() prebound to "this", use myUpdater.refreshDelegate.createCallback(arg1, arg2) to bind arguments
     * @type Function
     */
    this.refreshDelegate = this.refresh.createDelegate(this);
    /**
     * Delegate for update() prebound to "this", use myUpdater.updateDelegate.createCallback(arg1, arg2) to bind arguments
     * @type Function
     */
    this.updateDelegate = this.update.createDelegate(this);
    /**
     * Delegate for formUpdate() prebound to "this", use myUpdater.formUpdateDelegate.createCallback(arg1, arg2) to bind arguments
     * @type Function
     */
    this.formUpdateDelegate = this.formUpdate.createDelegate(this);
    /**
     * @private
     */
    this.successDelegate = this.processSuccess.createDelegate(this);
    /**
     * @private
     */
    this.failureDelegate = this.processFailure.createDelegate(this);

    if(!this.renderer){
     /**
      * The renderer for this UpdateManager. Defaults to {@link Ext.UpdateManager.BasicRenderer}.
      */
    this.renderer = new Ext.UpdateManager.BasicRenderer();
    }
    
    Ext.UpdateManager.superclass.constructor.call(this);
};

Ext.extend(Ext.UpdateManager, Ext.util.Observable, {
    /**
     * Get the Element this UpdateManager is bound to
     * @return {Ext.Element} The element
     */
    getEl : function(){
        return this.el;
    },
    /**
     * Performs an async request, updating this element with the response. If params are specified it uses POST, otherwise it uses GET.
     * @param {Object/String/Function} url The url for this request or a function to call to get the url or a config object containing any of the following options:
<pre><code>
um.update({<br/>
    url: "your-url.php",<br/>
    params: {param1: "foo", param2: "bar"}, // or a URL encoded string<br/>
    callback: yourFunction,<br/>
    scope: yourObject, //(optional scope)  <br/>
    discardUrl: false, <br/>
    nocache: false,<br/>
    text: "Loading...",<br/>
    timeout: 30,<br/>
    scripts: false<br/>
});
</code></pre>
     * The only required property is url. The optional properties nocache, text and scripts
     * are shorthand for disableCaching, indicatorText and loadScripts and are used to set their associated property on this UpdateManager instance.
     * @param {String/Object} params (optional) The parameters to pass as either a url encoded string "param1=1&amp;param2=2" or an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess, oResponse)
     * @param {Boolean} discardUrl (optional) By default when you execute an update the defaultUrl is changed to the last used url. If true, it will not store the url.
     */
    update : function(url, params, callback, discardUrl){
        if(this.fireEvent("beforeupdate", this.el, url, params) !== false){
            var method = this.method, cfg;
            if(typeof url == "object"){ // must be config object
                cfg = url;
                url = cfg.url;
                params = params || cfg.params;
                callback = callback || cfg.callback;
                discardUrl = discardUrl || cfg.discardUrl;
                if(callback && cfg.scope){
                    callback = callback.createDelegate(cfg.scope);
                }
                if(typeof cfg.method != "undefined"){method = cfg.method;};
                if(typeof cfg.nocache != "undefined"){this.disableCaching = cfg.nocache;};
                if(typeof cfg.text != "undefined"){this.indicatorText = '<div class="loading-indicator">'+cfg.text+"</div>";};
                if(typeof cfg.scripts != "undefined"){this.loadScripts = cfg.scripts;};
                if(typeof cfg.timeout != "undefined"){this.timeout = cfg.timeout;};
            }
            this.showLoading();
            if(!discardUrl){
                this.defaultUrl = url;
            }
            if(typeof url == "function"){
                url = url.call(this);
            }

            method = method || (params ? "POST" : "GET");
            if(method == "GET"){
                url = this.prepareUrl(url);
            }

            var o = Ext.apply(cfg ||{}, {
                url : url,
                params: params,
                success: this.successDelegate,
                failure: this.failureDelegate,
                callback: undefined,
                timeout: (this.timeout*1000),
                argument: {"url": url, "form": null, "callback": callback, "params": params}
            });

            this.transaction = Ext.Ajax.request(o);
        }
    },

    /**
     * Performs an async form post, updating this element with the response. If the form has the attribute enctype="multipart/form-data", it assumes it's a file upload.
     * Uses this.sslBlankUrl for SSL file uploads to prevent IE security warning.
     * @param {String/HTMLElement} form The form Id or form element
     * @param {String} url (optional) The url to pass the form to. If omitted the action attribute on the form will be used.
     * @param {Boolean} reset (optional) Whether to try to reset the form after the update
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess, oResponse)
     */
    formUpdate : function(form, url, reset, callback){
        if(this.fireEvent("beforeupdate", this.el, form, url) !== false){
            if(typeof url == "function"){
                url = url.call(this);
            }
            form = Ext.getDom(form)
            this.transaction = Ext.Ajax.request({
                form: form,
                url:url,
                success: this.successDelegate,
                failure: this.failureDelegate,
                timeout: (this.timeout*1000),
                argument: {"url": url, "form": form, "callback": callback, "reset": reset}
            });
            this.showLoading.defer(1, this);
        }
    },

    /**
     * Refresh the element with the last used url or defaultUrl. If there is no url, it returns immediately
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess)
     */
    refresh : function(callback){
        if(this.defaultUrl == null){
            return;
        }
        this.update(this.defaultUrl, null, callback, true);
    },

    /**
     * Set this element to auto refresh.
     * @param {Number} interval How often to update (in seconds).
     * @param {String/Function} url (optional) The url for this request or a function to call to get the url (Defaults to the last used url)
     * @param {String/Object} params (optional) The parameters to pass as either a url encoded string "&param1=1&param2=2" or as an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess)
     * @param {Boolean} refreshNow (optional) Whether to execute the refresh now, or wait the interval
     */
    startAutoRefresh : function(interval, url, params, callback, refreshNow){
        if(refreshNow){
            this.update(url || this.defaultUrl, params, callback, true);
        }
        if(this.autoRefreshProcId){
            clearInterval(this.autoRefreshProcId);
        }
        this.autoRefreshProcId = setInterval(this.update.createDelegate(this, [url || this.defaultUrl, params, callback, true]), interval*1000);
    },

    /**
     * Stop auto refresh on this element.
     */
     stopAutoRefresh : function(){
        if(this.autoRefreshProcId){
            clearInterval(this.autoRefreshProcId);
            delete this.autoRefreshProcId;
        }
    },

    isAutoRefreshing : function(){
       return this.autoRefreshProcId ? true : false;
    },
    /**
     * Called to update the element to "Loading" state. Override to perform custom action.
     */
    showLoading : function(){
        if(this.showLoadIndicator){
            this.el.update(this.indicatorText);
        }
    },

    /**
     * Adds unique parameter to query string if disableCaching = true
     * @private
     */
    prepareUrl : function(url){
        if(this.disableCaching){
            var append = "_dc=" + (new Date().getTime());
            if(url.indexOf("?") !== -1){
                url += "&" + append;
            }else{
                url += "?" + append;
            }
        }
        return url;
    },

    /**
     * @private
     */
    processSuccess : function(response){
        this.transaction = null;
        if(response.argument.form && response.argument.reset){
            try{ // put in try/catch since some older FF releases had problems with this
                response.argument.form.reset();
            }catch(e){}
        }
        if(this.loadScripts){
            this.renderer.render(this.el, response, this,
                this.updateComplete.createDelegate(this, [response]));
        }else{
            this.renderer.render(this.el, response, this);
            this.updateComplete(response);
        }
    },

    updateComplete : function(response){
        this.fireEvent("update", this.el, response);
        if(typeof response.argument.callback == "function"){
            response.argument.callback(this.el, true, response);
        }
    },

    /**
     * @private
     */
    processFailure : function(response){
        this.transaction = null;
        this.fireEvent("failure", this.el, response);
        if(typeof response.argument.callback == "function"){
            response.argument.callback(this.el, false, response);
        }
    },

    /**
     * Set the content renderer for this UpdateManager. See {@link Ext.UpdateManager.BasicRenderer#render} for more details.
     * @param {Object} renderer The object implementing the render() method
     */
    setRenderer : function(renderer){
        this.renderer = renderer;
    },

    getRenderer : function(){
       return this.renderer;
    },

    /**
     * Set the defaultUrl used for updates
     * @param {String/Function} defaultUrl The url or a function to call to get the url
     */
    setDefaultUrl : function(defaultUrl){
        this.defaultUrl = defaultUrl;
    },

    /**
     * Aborts the executing transaction
     */
    abort : function(){
        if(this.transaction){
            Ext.Ajax.abort(this.transaction);
        }
    },

    /**
     * Returns true if an update is in progress
     * @return {Boolean}
     */
    isUpdating : function(){
        if(this.transaction){
            return Ext.Ajax.isLoading(this.transaction);
        }
        return false;
    }
});

/**
 * @class Ext.UpdateManager.defaults
 * The defaults collection enables customizing the default properties of UpdateManager
 */
   Ext.UpdateManager.defaults = {
       /**
         * Timeout for requests or form posts in seconds (Defaults 30 seconds).
         * @type Number
         */
         timeout : 30,

         /**
         * True to process scripts by default (Defaults to false).
         * @type Boolean
         */
        loadScripts : false,

        /**
        * Blank page URL to use with SSL file uploads (Defaults to "javascript:false").
        * @type String
        */
        sslBlankUrl : (Ext.SSL_SECURE_URL || "javascript:false"),
        /**
         * Whether to append unique parameter on get request to disable caching (Defaults to false).
         * @type Boolean
         */
        disableCaching : false,
        /**
         * Whether to show indicatorText when loading (Defaults to true).
         * @type Boolean
         */
        showLoadIndicator : true,
        /**
         * Text for loading indicator (Defaults to '&lt;div class="loading-indicator"&gt;Loading...&lt;/div&gt;').
         * @type String
         */
        indicatorText : '<div class="loading-indicator">Loading...</div>'
   };

/**
 * Static convenience method. This method is deprecated in favor of el.load({url:'foo.php', ...}).
 *Usage:
 * <pre><code>Ext.UpdateManager.updateElement("my-div", "stuff.php");</code></pre>
 * @param {String/HTMLElement/Ext.Element} el The element to update
 * @param {String} url The url
 * @param {String/Object} params (optional) Url encoded param string or an object of name/value pairs
 * @param {Object} options (optional) A config object with any of the UpdateManager properties you want to set - for example: {disableCaching:true, indicatorText: "Loading data..."}
 * @static
 * @deprecated
 * @member Ext.UpdateManager
 */
Ext.UpdateManager.updateElement = function(el, url, params, options){
    var um = Ext.get(el, true).getUpdateManager();
    Ext.apply(um, options);
    um.update(url, params, options ? options.callback : null);
};
// alias for backwards compat
Ext.UpdateManager.update = Ext.UpdateManager.updateElement;
/**
 * @class Ext.UpdateManager.BasicRenderer
 * Default Content renderer. Updates the elements innerHTML with the responseText.
 */
Ext.UpdateManager.BasicRenderer = function(){};

Ext.UpdateManager.BasicRenderer.prototype = {
    /**
     * This is called when the transaction is completed and it's time to update the element - The BasicRenderer
     * updates the elements innerHTML with the responseText - To perform a custom render (i.e. XML or JSON processing),
     * create an object with a "render(el, response)" method and pass it to setRenderer on the UpdateManager.
     * @param {Ext.Element} el The element being rendered
     * @param {Object} response The YUI Connect response object
     * @param {UpdateManager} updateManager The calling update manager
     * @param {Function} callback A callback that will need to be called if loadScripts is true on the UpdateManager
     */
     render : function(el, response, updateManager, callback){
        el.update(response.responseText, updateManager.loadScripts, callback);
    }
};




/**
 * @class Ext.util.MixedCollection
 * @extends Ext.util.Observable
 * A Collection class that maintains both numeric indexes and keys and exposes events.
 * @constructor
 * @param {Boolean} allowFunctions True if the addAll function should add function references to the
 * collection (defaults to false)
 * @param {Function} keyFn A function that can accept an item of the type(s) stored in this MixedCollection
 * and return the key value for that item.  This is used when available to look up the key on items that
 * were passed without an explicit key parameter to a MixedCollection method.  Passing this parameter is
 * equivalent to providing an implementation for the {@link #getKey} method.
 */
Ext.util.MixedCollection = function(allowFunctions, keyFn){
    this.items = [];
    this.map = {};
    this.keys = [];
    this.length = 0;
    this.addEvents({
        /**
         * @event clear
         * Fires when the collection is cleared.
         */
        "clear" : true,
        /**
         * @event add
         * Fires when an item is added to the collection.
         * @param {Number} index The index at which the item was added.
         * @param {Object} o The item added.
         * @param {String} key The key associated with the added item.
         */
        "add" : true,
        /**
         * @event replace
         * Fires when an item is replaced in the collection.
         * @param {String} key he key associated with the new added.
         * @param {Object} old The item being replaced.
         * @param {Object} new The new item.
         */
        "replace" : true,
        /**
         * @event remove
         * Fires when an item is removed from the collection.
         * @param {Object} o The item being removed.
         * @param {String} key (optional) The key associated with the removed item.
         */
        "remove" : true,
        "sort" : true
    });
    this.allowFunctions = allowFunctions === true;
    if(keyFn){
        this.getKey = keyFn;
    }
    Ext.util.MixedCollection.superclass.constructor.call(this);
};

Ext.extend(Ext.util.MixedCollection, Ext.util.Observable, {
    allowFunctions : false,
    
/**
 * Adds an item to the collection.
 * @param {String} key The key to associate with the item
 * @param {Object} o The item to add.
 * @return {Object} The item added.
 */
    add : function(key, o){
        if(arguments.length == 1){
            o = arguments[0];
            key = this.getKey(o);
        }
        if(typeof key == "undefined" || key === null){
            this.length++;
            this.items.push(o);
            this.keys.push(null);
        }else{
            var old = this.map[key];
            if(old){
                return this.replace(key, o);
            }
            this.length++;
            this.items.push(o);
            this.map[key] = o;
            this.keys.push(key);
        }
        this.fireEvent("add", this.length-1, o, key);
        return o;
    },
   
/**
  * MixedCollection has a generic way to fetch keys if you implement getKey.
<pre><code>
// normal way
var mc = new Ext.util.MixedCollection();
mc.add(someEl.dom.id, someEl);
mc.add(otherEl.dom.id, otherEl);
//and so on

// using getKey
var mc = new Ext.util.MixedCollection();
mc.getKey = function(el){
   return el.dom.id;
};
mc.add(someEl);
mc.add(otherEl);

// or via the constructor
var mc = new Ext.util.MixedCollection(false, function(el){
   return el.dom.id;
});
mc.add(someEl);
mc.add(otherEl);
</code></pre>
 * @param o {Object} The item for which to find the key.
 * @return {Object} The key for the passed item.
 */
    getKey : function(o){
         return o.id; 
    },
   
/**
 * Replaces an item in the collection.
 * @param {String} key The key associated with the item to replace, or the item to replace.
 * @param o {Object} o (optional) If the first parameter passed was a key, the item to associate with that key.
 * @return {Object}  The new item.
 */
    replace : function(key, o){
        if(arguments.length == 1){
            o = arguments[0];
            key = this.getKey(o);
        }
        var old = this.item(key);
        if(typeof key == "undefined" || key === null || typeof old == "undefined"){
             return this.add(key, o);
        }
        var index = this.indexOfKey(key);
        this.items[index] = o;
        this.map[key] = o;
        this.fireEvent("replace", key, old, o);
        return o;
    },
   
/**
 * Adds all elements of an Array or an Object to the collection.
 * @param {Object/Array} objs An Object containing properties which will be added to the collection, or
 * an Array of values, each of which are added to the collection.
 */
    addAll : function(objs){
        if(arguments.length > 1 || objs instanceof Array){
            var args = arguments.length > 1 ? arguments : objs;
            for(var i = 0, len = args.length; i < len; i++){
                this.add(args[i]);
            }
        }else{
            for(var key in objs){
                if(this.allowFunctions || typeof objs[key] != "function"){
                    this.add(key, objs[key]);
                }
            }
        }
    },
   
/**
 * Executes the specified function once for every item in the collection, passing each
 * item as the first and only parameter. returning false from the function will stop the iteration.
 * @param {Function} fn The function to execute for each item.
 * @param {Object} scope (optional) The scope in which to execute the function.
 */
    each : function(fn, scope){
        var items = [].concat(this.items); // each safe for removal
        for(var i = 0, len = items.length; i < len; i++){
            if(fn.call(scope || items[i], items[i], i, len) === false){
                break;
            }
        }
    },
   
/**
 * Executes the specified function once for every key in the collection, passing each
 * key, and its associated item as the first two parameters.
 * @param {Function} fn The function to execute for each item.
 * @param {Object} scope (optional) The scope in which to execute the function.
 */
    eachKey : function(fn, scope){
        for(var i = 0, len = this.keys.length; i < len; i++){
            fn.call(scope || window, this.keys[i], this.items[i], i, len);
        }
    },
   
/**
 * Returns the first item in the collection which elicits a true return value from the
 * passed selection function.
 * @param {Function} fn The selection function to execute for each item.
 * @param {Object} scope (optional) The scope in which to execute the function.
 * @return {Object} The first item in the collection which returned true from the selection function.
 */
    find : function(fn, scope){
        for(var i = 0, len = this.items.length; i < len; i++){
            if(fn.call(scope || window, this.items[i], this.keys[i])){
                return this.items[i];
            }
        }
        return null;
    },
   
/**
 * Inserts an item at the specified index in the collection.
 * @param {Number} index The index to insert the item at.
 * @param {String} key The key to associate with the new item, or the item itself.
 * @param {Object} o  (optional) If the second parameter was a key, the new item.
 * @return {Object} The item inserted.
 */
    insert : function(index, key, o){
        if(arguments.length == 2){
            o = arguments[1];
            key = this.getKey(o);
        }
        if(index >= this.length){
            return this.add(key, o);
        }
        this.length++;
        this.items.splice(index, 0, o);
        if(typeof key != "undefined" && key != null){
            this.map[key] = o;
        }
        this.keys.splice(index, 0, key);
        this.fireEvent("add", index, o, key);
        return o;
    },
   
/**
 * Removed an item from the collection.
 * @param {Object} o The item to remove.
 * @return {Object} The item removed.
 */
    remove : function(o){
        return this.removeAt(this.indexOf(o));
    },
   
/**
 * Remove an item from a specified index in the collection.
 * @param {Number} index The index within the collection of the item to remove.
 */
    removeAt : function(index){
        if(index < this.length && index >= 0){
            this.length--;
            var o = this.items[index];
            this.items.splice(index, 1);
            var key = this.keys[index];
            if(typeof key != "undefined"){
                delete this.map[key];
            }
            this.keys.splice(index, 1);
            this.fireEvent("remove", o, key);
        }
    },
   
/**
 * Removed an item associated with the passed key fom the collection.
 * @param {String} key The key of the item to remove.
 */
    removeKey : function(key){
        return this.removeAt(this.indexOfKey(key));
    },
   
/**
 * Returns the number of items in the collection.
 * @return {Number} the number of items in the collection.
 */
    getCount : function(){
        return this.length; 
    },
   
/**
 * Returns index within the collection of the passed Object.
 * @param {Object} o The item to find the index of.
 * @return {Number} index of the item.
 */
    indexOf : function(o){
        if(!this.items.indexOf){
            for(var i = 0, len = this.items.length; i < len; i++){
                if(this.items[i] == o) return i;
            }
            return -1;
        }else{
            return this.items.indexOf(o);
        }
    },
   
/**
 * Returns index within the collection of the passed key.
 * @param {String} key The key to find the index of.
 * @return {Number} index of the key.
 */
    indexOfKey : function(key){
        if(!this.keys.indexOf){
            for(var i = 0, len = this.keys.length; i < len; i++){
                if(this.keys[i] == key) return i;
            }
            return -1;
        }else{
            return this.keys.indexOf(key);
        }
    },
   
/**
 * Returns the item associated with the passed key OR index. Key has priority over index.
 * @param {String/Number} key The key or index of the item.
 * @return {Object} The item associated with the passed key.
 */
    item : function(key){
        var item = typeof this.map[key] != "undefined" ? this.map[key] : this.items[key];
        return typeof item != 'function' || this.allowFunctions ? item : null; // for prototype!
    },
    
/**
 * Returns the item at the specified index.
 * @param {Number} index The index of the item.
 * @return {Object}
 */
    itemAt : function(index){
        return this.items[index];
    },
    
/**
 * Returns the item associated with the passed key.
 * @param {String/Number} key The key of the item.
 * @return {Object} The item associated with the passed key.
 */
    key : function(key){
        return this.map[key];
    },
   
/**
 * Returns true if the collection contains the passed Object as an item.
 * @param {Object} o  The Object to look for in the collection.
 * @return {Boolean} True if the collection contains the Object as an item.
 */
    contains : function(o){
        return this.indexOf(o) != -1;
    },
   
/**
 * Returns true if the collection contains the passed Object as a key.
 * @param {String} key The key to look for in the collection.
 * @return {Boolean} True if the collection contains the Object as a key.
 */
    containsKey : function(key){
        return typeof this.map[key] != "undefined";
    },
   
/**
 * Removes all items from the collection.
 */
    clear : function(){
        this.length = 0;
        this.items = [];
        this.keys = [];
        this.map = {};
        this.fireEvent("clear");
    },
   
/**
 * Returns the first item in the collection.
 * @return {Object} the first item in the collection..
 */
    first : function(){
        return this.items[0]; 
    },
   
/**
 * Returns the last item in the collection.
 * @return {Object} the last item in the collection..
 */
    last : function(){
        return this.items[this.length-1];   
    },
    
    _sort : function(property, dir, fn){
        var dsc = String(dir).toUpperCase() == "DESC" ? -1 : 1;
        fn = fn || function(a, b){
            return a-b;
        };
        var c = [], k = this.keys, items = this.items;
        for(var i = 0, len = items.length; i < len; i++){
            c[c.length] = {key: k[i], value: items[i], index: i};
        }
        c.sort(function(a, b){
            var v = fn(a[property], b[property]) * dsc;
            if(v == 0){
                v = (a.index < b.index ? -1 : 1);
            }
            return v;
        });
        for(var i = 0, len = c.length; i < len; i++){
            items[i] = c[i].value;
            k[i] = c[i].key;
        }
        this.fireEvent("sort", this);
    },
    
    /**
     * Sorts this collection with the passed comparison function
     * @param {String} direction (optional) "ASC" or "DESC"
     * @param {Function} fn (optional) comparison function
     */
    sort : function(dir, fn){
        this._sort("value", dir, fn);
    },
    
    /**
     * Sorts this collection by keys
     * @param {String} direction (optional) "ASC" or "DESC"
     * @param {Function} fn (optional) a comparison function (defaults to case insensitive string)
     */
    keySort : function(dir, fn){
        this._sort("key", dir, fn || function(a, b){
            return String(a).toUpperCase()-String(b).toUpperCase();
        });
    },
    
    /**
     * Returns a range of items in this collection
     * @param {Number} startIndex (optional) defaults to 0
     * @param {Number} endIndex (optional) default to the last item
     * @return {Array} An array of items
     */
    getRange : function(start, end){
        var items = this.items;
        if(items.length < 1){
            return [];
        }
        start = start || 0;
        end = Math.min(typeof end == "undefined" ? this.length-1 : end, this.length-1);
        var r = [];
        if(start <= end){
            for(var i = start; i <= end; i++) {
        	    r[r.length] = items[i];
            }
        }else{
            for(var i = start; i >= end; i--) {
        	    r[r.length] = items[i];
            }
        }
        return r;
    },
        
    /**
     * Filter the <i>objects</i> in this collection by a specific property. 
     * Returns a new collection that has been filtered.
     * @param {String} property A property on your objects
     * @param {String/RegExp} value Either string that the property values 
     * should start with or a RegExp to test against the property
     * @return {MixedCollection} The new filtered collection
     */
    filter : function(property, value){
        if(!value.exec){ // not a regex
            value = String(value);
            if(value.length == 0){
                return this.clone();
            }
            value = new RegExp("^" + Ext.escapeRe(value), "i");
        }
        return this.filterBy(function(o){
            return o && value.test(o[property]);
        });
	},
    
    /**
     * Filter by a function. * Returns a new collection that has been filtered.
     * The passed function will be called with each 
     * object in the collection. If the function returns true, the value is included 
     * otherwise it is filtered.
     * @param {Function} fn The function to be called, it will receive the args o (the object), k (the key)
     * @param {Object} scope (optional) The scope of the function (defaults to this) 
     * @return {MixedCollection} The new filtered collection
     */
    filterBy : function(fn, scope){
        var r = new Ext.util.MixedCollection();
        r.getKey = this.getKey;
        var k = this.keys, it = this.items;
        for(var i = 0, len = it.length; i < len; i++){
            if(fn.call(scope||this, it[i], k[i])){
				r.add(k[i], it[i]);
			}
        }
        return r;
    },
    
    /**
     * Creates a duplicate of this collection
     * @return {MixedCollection}
     */
    clone : function(){
        var r = new Ext.util.MixedCollection();
        var k = this.keys, it = this.items;
        for(var i = 0, len = it.length; i < len; i++){
            r.add(k[i], it[i]);
        }
        r.getKey = this.getKey;
        return r;
    }
});
/**
 * Returns the item associated with the passed key or index.
 * @method
 * @param {String/Number} key The key or index of the item.
 * @return {Object} The item associated with the passed key.
 */
Ext.util.MixedCollection.prototype.get = Ext.util.MixedCollection.prototype.item;



/**
 * @class Ext.util.JSON
 * Modified version of Douglas Crockford"s json.js that doesn"t
 * mess with the Object prototype 
 * http://www.json.org/js.html
 * @singleton
 */
Ext.util.JSON = new (function(){
    var useHasOwn = {}.hasOwnProperty ? true : false;
    
    // crashes Safari in some instances
    //var validRE = /^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/;
    
    var pad = function(n) {
        return n < 10 ? "0" + n : n;
    };
    
    var m = {
        "\b": '\\b',
        "\t": '\\t',
        "\n": '\\n',
        "\f": '\\f',
        "\r": '\\r',
        '"' : '\\"',
        "\\": '\\\\'
    };

    var encodeString = function(s){
        if (/["\\\x00-\x1f]/.test(s)) {
            return '"' + s.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                var c = m[b];
                if(c){
                    return c;
                }
                c = b.charCodeAt();
                return "\\u00" +
                    Math.floor(c / 16).toString(16) +
                    (c % 16).toString(16);
            }) + '"';
        }
        return '"' + s + '"';
    };
    
    var encodeArray = function(o){
        var a = ["["], b, i, l = o.length, v;
            for (i = 0; i < l; i += 1) {
                v = o[i];
                switch (typeof v) {
                    case "undefined":
                    case "function":
                    case "unknown":
                        break;
                    default:
                        if (b) {
                            a.push(',');
                        }
                        a.push(v === null ? "null" : Ext.util.JSON.encode(v));
                        b = true;
                }
            }
            a.push("]");
            return a.join("");
    };
    
    var encodeDate = function(o){
        return '"' + o.getFullYear() + "-" +
                pad(o.getMonth() + 1) + "-" +
                pad(o.getDate()) + "T" +
                pad(o.getHours()) + ":" +
                pad(o.getMinutes()) + ":" +
                pad(o.getSeconds()) + '"';
    };
    
    /**
     * Encodes an Object, Array or other value
     * @param {Mixed} o The variable to encode
     * @return {String} The JSON string
     */
    this.encode = function(o){
        if(typeof o == "undefined" || o === null){
            return "null";
        }else if(o instanceof Array){
            return encodeArray(o);
        }else if(o instanceof Date){
            return encodeDate(o);
        }else if(typeof o == "string"){
            return encodeString(o);
        }else if(typeof o == "number"){
            return isFinite(o) ? String(o) : "null";
        }else if(typeof o == "boolean"){
            return String(o);
        }else {
            var a = ["{"], b, i, v;
            for (i in o) {
                if(!useHasOwn || o.hasOwnProperty(i)) {
                    v = o[i];
                    switch (typeof v) {
                    case "undefined":
                    case "function":
                    case "unknown":
                        break;
                    default:
                        if(b){
                            a.push(',');
                        }
                        a.push(this.encode(i), ":",
                                v === null ? "null" : this.encode(v));
                        b = true;
                    }
                }
            }
            a.push("}");
            return a.join("");
        }
    };
    
    /**
     * Decodes (parses) a JSON string to an object. If the JSON is invalid, this function throws a SyntaxError.
     * @param {String} json The JSON string
     * @return {Object} The resulting object
     */
    this.decode = function(json){
        return eval("(" + json + ')');
    };
})();
/** 
 * Shorthand for {@link Ext.util.JSON#encode}
 * @member Ext encode 
 * @method */
Ext.encode = Ext.util.JSON.encode;
/** 
 * Shorthand for {@link Ext.util.JSON#decode}
 * @member Ext decode 
 * @method */
Ext.decode = Ext.util.JSON.decode;




/*
 * These classes are derivatives of the similarly named classes in the YUI Library.
 * The original license:
 * Copyright (c) 2006, Yahoo! Inc. All rights reserved.
 * Code licensed under the BSD License:
 * http://developer.yahoo.net/yui/license.txt
 */

(function() {

var Event=Ext.EventManager;
var Dom=Ext.lib.Dom;

/**
 * @class Ext.dd.DragDrop
 * Defines the interface and base operation of items that that can be
 * dragged or can be drop targets.  It was designed to be extended, overriding
 * the event handlers for startDrag, onDrag, onDragOver and onDragOut.
 * Up to three html elements can be associated with a DragDrop instance:
 * <ul>
 * <li>linked element: the element that is passed into the constructor.
 * This is the element which defines the boundaries for interaction with
 * other DragDrop objects.</li>
 * <li>handle element(s): The drag operation only occurs if the element that
 * was clicked matches a handle element.  By default this is the linked
 * element, but there are times that you will want only a portion of the
 * linked element to initiate the drag operation, and the setHandleElId()
 * method provides a way to define this.</li>
 * <li>drag element: this represents the element that would be moved along
 * with the cursor during a drag operation.  By default, this is the linked
 * element itself as in {@link Ext.dd.DD}.  setDragElId() lets you define
 * a separate element that would be moved, as in {@link Ext.dd.DDProxy}.
 * </li>
 * </ul>
 * This class should not be instantiated until the onload event to ensure that
 * the associated elements are available.
 * The following would define a DragDrop obj that would interact with any
 * other DragDrop obj in the "group1" group:
 * <pre>
 *  dd = new Ext.dd.DragDrop("div1", "group1");
 * </pre>
 * Since none of the event handlers have been implemented, nothing would
 * actually happen if you were to run the code above.  Normally you would
 * override this class or one of the default implementations, but you can
 * also override the methods you want on an instance of the class...
 * <pre>
 *  dd.onDragDrop = function(e, id) {
 *  &nbsp;&nbsp;alert("dd was dropped on " + id);
 *  }
 * </pre>
 * @constructor
 * @param {String} id of the element that is linked to this instance
 * @param {String} sGroup the group of related DragDrop objects
 * @param {object} config an object containing configurable attributes
 *                Valid properties for DragDrop:
 *                    padding, isTarget, maintainOffset, primaryButtonOnly
 */
Ext.dd.DragDrop = function(id, sGroup, config) {
    if (id) {
        this.init(id, sGroup, config);
    }
};

Ext.dd.DragDrop.prototype = {

    /**
     * The id of the element associated with this object.  This is what we
     * refer to as the "linked element" because the size and position of
     * this element is used to determine when the drag and drop objects have
     * interacted.
     * @property id
     * @type String
     */
    id: null,

    /**
     * Configuration attributes passed into the constructor
     * @property config
     * @type object
     */
    config: null,

    /**
     * The id of the element that will be dragged.  By default this is same
     * as the linked element , but could be changed to another element. Ex:
     * Ext.dd.DDProxy
     * @property dragElId
     * @type String
     * @private
     */
    dragElId: null,

    /**
     * the id of the element that initiates the drag operation.  By default
     * this is the linked element, but could be changed to be a child of this
     * element.  This lets us do things like only starting the drag when the
     * header element within the linked html element is clicked.
     * @property handleElId
     * @type String
     * @private
     */
    handleElId: null,

    /**
     * An associative array of HTML tags that will be ignored if clicked.
     * @property invalidHandleTypes
     * @type {string: string}
     */
    invalidHandleTypes: null,

    /**
     * An associative array of ids for elements that will be ignored if clicked
     * @property invalidHandleIds
     * @type {string: string}
     */
    invalidHandleIds: null,

    /**
     * An indexted array of css class names for elements that will be ignored
     * if clicked.
     * @property invalidHandleClasses
     * @type string[]
     */
    invalidHandleClasses: null,

    /**
     * The linked element's absolute X position at the time the drag was
     * started
     * @property startPageX
     * @type int
     * @private
     */
    startPageX: 0,

    /**
     * The linked element's absolute X position at the time the drag was
     * started
     * @property startPageY
     * @type int
     * @private
     */
    startPageY: 0,

    /**
     * The group defines a logical collection of DragDrop objects that are
     * related.  Instances only get events when interacting with other
     * DragDrop object in the same group.  This lets us define multiple
     * groups using a single DragDrop subclass if we want.
     * @property groups
     * @type {string: string}
     */
    groups: null,

    /**
     * Individual drag/drop instances can be locked.  This will prevent
     * onmousedown start drag.
     * @property locked
     * @type boolean
     * @private
     */
    locked: false,

    /**
     * Lock this instance
     * @method lock
     */
    lock: function() { this.locked = true; },

    /**
     * Unlock this instace
     * @method unlock
     */
    unlock: function() { this.locked = false; },

    /**
     * By default, all insances can be a drop target.  This can be disabled by
     * setting isTarget to false.
     * @method isTarget
     * @type boolean
     */
    isTarget: true,

    /**
     * The padding configured for this drag and drop object for calculating
     * the drop zone intersection with this object.
     * @method padding
     * @type int[]
     */
    padding: null,

    /**
     * Cached reference to the linked element
     * @property _domRef
     * @private
     */
    _domRef: null,

    /**
     * Internal typeof flag
     * @property __ygDragDrop
     * @private
     */
    __ygDragDrop: true,

    /**
     * Set to true when horizontal contraints are applied
     * @property constrainX
     * @type boolean
     * @private
     */
    constrainX: false,

    /**
     * Set to true when vertical contraints are applied
     * @property constrainY
     * @type boolean
     * @private
     */
    constrainY: false,

    /**
     * The left constraint
     * @property minX
     * @type int
     * @private
     */
    minX: 0,

    /**
     * The right constraint
     * @property maxX
     * @type int
     * @private
     */
    maxX: 0,

    /**
     * The up constraint
     * @property minY
     * @type int
     * @type int
     * @private
     */
    minY: 0,

    /**
     * The down constraint
     * @property maxY
     * @type int
     * @private
     */
    maxY: 0,

    /**
     * Maintain offsets when we resetconstraints.  Set to true when you want
     * the position of the element relative to its parent to stay the same
     * when the page changes
     *
     * @property maintainOffset
     * @type boolean
     */
    maintainOffset: false,

    /**
     * Array of pixel locations the element will snap to if we specified a
     * horizontal graduation/interval.  This array is generated automatically
     * when you define a tick interval.
     * @property xTicks
     * @type int[]
     */
    xTicks: null,

    /**
     * Array of pixel locations the element will snap to if we specified a
     * vertical graduation/interval.  This array is generated automatically
     * when you define a tick interval.
     * @property yTicks
     * @type int[]
     */
    yTicks: null,

    /**
     * By default the drag and drop instance will only respond to the primary
     * button click (left button for a right-handed mouse).  Set to true to
     * allow drag and drop to start with any mouse click that is propogated
     * by the browser
     * @property primaryButtonOnly
     * @type boolean
     */
    primaryButtonOnly: true,

    /**
     * The availabe property is false until the linked dom element is accessible.
     * @property available
     * @type boolean
     */
    available: false,

    /**
     * By default, drags can only be initiated if the mousedown occurs in the
     * region the linked element is.  This is done in part to work around a
     * bug in some browsers that mis-report the mousedown if the previous
     * mouseup happened outside of the window.  This property is set to true
     * if outer handles are defined.
     *
     * @property hasOuterHandles
     * @type boolean
     * @default false
     */
    hasOuterHandles: false,

    /**
     * Code that executes immediately before the startDrag event
     * @method b4StartDrag
     * @private
     */
    b4StartDrag: function(x, y) { },

    /**
     * Abstract method called after a drag/drop object is clicked
     * and the drag or mousedown time thresholds have beeen met.
     * @method startDrag
     * @param {int} X click location
     * @param {int} Y click location
     */
    startDrag: function(x, y) { /* override this */ },

    /**
     * Code that executes immediately before the onDrag event
     * @method b4Drag
     * @private
     */
    b4Drag: function(e) { },

    /**
     * Abstract method called during the onMouseMove event while dragging an
     * object.
     * @method onDrag
     * @param {Event} e the mousemove event
     */
    onDrag: function(e) { /* override this */ },

    /**
     * Abstract method called when this element fist begins hovering over
     * another DragDrop obj
     * @method onDragEnter
     * @param {Event} e the mousemove event
     * @param {String|DragDrop[]} id In POINT mode, the element
     * id this is hovering over.  In INTERSECT mode, an array of one or more
     * dragdrop items being hovered over.
     */
    onDragEnter: function(e, id) { /* override this */ },

    /**
     * Code that executes immediately before the onDragOver event
     * @method b4DragOver
     * @private
     */
    b4DragOver: function(e) { },

    /**
     * Abstract method called when this element is hovering over another
     * DragDrop obj
     * @method onDragOver
     * @param {Event} e the mousemove event
     * @param {String|DragDrop[]} id In POINT mode, the element
     * id this is hovering over.  In INTERSECT mode, an array of dd items
     * being hovered over.
     */
    onDragOver: function(e, id) { /* override this */ },

    /**
     * Code that executes immediately before the onDragOut event
     * @method b4DragOut
     * @private
     */
    b4DragOut: function(e) { },

    /**
     * Abstract method called when we are no longer hovering over an element
     * @method onDragOut
     * @param {Event} e the mousemove event
     * @param {String|DragDrop[]} id In POINT mode, the element
     * id this was hovering over.  In INTERSECT mode, an array of dd items
     * that the mouse is no longer over.
     */
    onDragOut: function(e, id) { /* override this */ },

    /**
     * Code that executes immediately before the onDragDrop event
     * @method b4DragDrop
     * @private
     */
    b4DragDrop: function(e) { },

    /**
     * Abstract method called when this item is dropped on another DragDrop
     * obj
     * @method onDragDrop
     * @param {Event} e the mouseup event
     * @param {String|DragDrop[]} id In POINT mode, the element
     * id this was dropped on.  In INTERSECT mode, an array of dd items this
     * was dropped on.
     */
    onDragDrop: function(e, id) { /* override this */ },

    /**
     * Abstract method called when this item is dropped on an area with no
     * drop target
     * @method onInvalidDrop
     * @param {Event} e the mouseup event
     */
    onInvalidDrop: function(e) { /* override this */ },

    /**
     * Code that executes immediately before the endDrag event
     * @method b4EndDrag
     * @private
     */
    b4EndDrag: function(e) { },

    /**
     * Fired when we are done dragging the object
     * @method endDrag
     * @param {Event} e the mouseup event
     */
    endDrag: function(e) { /* override this */ },

    /**
     * Code executed immediately before the onMouseDown event
     * @method b4MouseDown
     * @param {Event} e the mousedown event
     * @private
     */
    b4MouseDown: function(e) {  },

    /**
     * Event handler that fires when a drag/drop obj gets a mousedown
     * @method onMouseDown
     * @param {Event} e the mousedown event
     */
    onMouseDown: function(e) { /* override this */ },

    /**
     * Event handler that fires when a drag/drop obj gets a mouseup
     * @method onMouseUp
     * @param {Event} e the mouseup event
     */
    onMouseUp: function(e) { /* override this */ },

    /**
     * Override the onAvailable method to do what is needed after the initial
     * position was determined.
     * @method onAvailable
     */
    onAvailable: function () {
    },

    /*
     * Provides default constraint padding to "constrainTo" elements (defaults to {left: 0, right:0, top:0, bottom:0}).
     * @type Object
     */
    defaultPadding : {left:0, right:0, top:0, bottom:0},

    /*
     * Initializes the drag drop object's constraints to restrict movement to a certain element.
 *
 * Usage:
 <pre><code>
 var dd = new Ext.dd.DDProxy("dragDiv1", "proxytest",
                { dragElId: "existingProxyDiv" });
 dd.startDrag = function(){
     this.constrainTo("parent-id");
 };
 </code></pre>
 * Or you can initalize it using the {@link Ext.Element} object:
 <pre><code>
 Ext.get("dragDiv1").initDDProxy("proxytest", {dragElId: "existingProxyDiv"}, {
     startDrag : function(){
         this.constrainTo("parent-id");
     }
 });
 </code></pre>
     * @param {String/HTMLElement/Element} constrainTo The element to constrain to.
     * @param {Object/Number} pad (optional) Pad provides a way to specify "padding" of the constraints,
     * and can be either a number for symmetrical padding (4 would be equal to {left:4, right:4, top:4, bottom:4}) or
     * an object containing the sides to pad. For example: {right:10, bottom:10}
     * @param {Boolean} inContent (optional) Constrain the draggable in the content box of the element (inside padding and borders)
     */
    constrainTo : function(constrainTo, pad, inContent){
        if(typeof pad == "number"){
            pad = {left: pad, right:pad, top:pad, bottom:pad};
        }
        pad = pad || this.defaultPadding;
        var b = Ext.get(this.getEl()).getBox();
        var ce = Ext.get(constrainTo);
        var s = ce.getScroll();
        var c, cd = ce.dom;
        if(cd == document.body){
            c = { x: s.left, y: s.top, width: Ext.lib.Dom.getViewWidth(), height: Ext.lib.Dom.getViewHeight()};
        }else{
            xy = ce.getXY();
            c = {x : xy[0]+s.left, y: xy[1]+s.top, width: cd.clientWidth, height: cd.clientHeight};
        }


        var topSpace = b.y - c.y;
        var leftSpace = b.x - c.x;

        this.resetConstraints();
        this.setXConstraint(leftSpace - (pad.left||0), // left
                c.width - leftSpace - b.width - (pad.right||0) //right
        );
        this.setYConstraint(topSpace - (pad.top||0), //top
                c.height - topSpace - b.height - (pad.bottom||0) //bottom
        );
    },

    /**
     * Returns a reference to the linked element
     * @method getEl
     * @return {HTMLElement} the html element
     */
    getEl: function() {
        if (!this._domRef) {
            this._domRef = Ext.getDom(this.id);
        }

        return this._domRef;
    },

    /**
     * Returns a reference to the actual element to drag.  By default this is
     * the same as the html element, but it can be assigned to another
     * element. An example of this can be found in Ext.dd.DDProxy
     * @method getDragEl
     * @return {HTMLElement} the html element
     */
    getDragEl: function() {
        return Ext.getDom(this.dragElId);
    },

    /**
     * Sets up the DragDrop object.  Must be called in the constructor of any
     * Ext.dd.DragDrop subclass
     * @method init
     * @param id the id of the linked element
     * @param {String} sGroup the group of related items
     * @param {object} config configuration attributes
     */
    init: function(id, sGroup, config) {
        this.initTarget(id, sGroup, config);
        Event.on(this.id, "mousedown", this.handleMouseDown, this);
        // Event.on(this.id, "selectstart", Event.preventDefault);
    },

    /**
     * Initializes Targeting functionality only... the object does not
     * get a mousedown handler.
     * @method initTarget
     * @param id the id of the linked element
     * @param {String} sGroup the group of related items
     * @param {object} config configuration attributes
     */
    initTarget: function(id, sGroup, config) {

        // configuration attributes
        this.config = config || {};

        // create a local reference to the drag and drop manager
        this.DDM = Ext.dd.DDM;
        // initialize the groups array
        this.groups = {};

        // assume that we have an element reference instead of an id if the
        // parameter is not a string
        if (typeof id !== "string") {
            id = Ext.id(id);
        }

        // set the id
        this.id = id;

        // add to an interaction group
        this.addToGroup((sGroup) ? sGroup : "default");

        // We don't want to register this as the handle with the manager
        // so we just set the id rather than calling the setter.
        this.handleElId = id;

        // the linked element is the element that gets dragged by default
        this.setDragElId(id);

        // by default, clicked anchors will not start drag operations.
        this.invalidHandleTypes = { A: "A" };
        this.invalidHandleIds = {};
        this.invalidHandleClasses = [];

        this.applyConfig();

        this.handleOnAvailable();
    },

    /**
     * Applies the configuration parameters that were passed into the constructor.
     * This is supposed to happen at each level through the inheritance chain.  So
     * a DDProxy implentation will execute apply config on DDProxy, DD, and
     * DragDrop in order to get all of the parameters that are available in
     * each object.
     * @method applyConfig
     */
    applyConfig: function() {

        // configurable properties:
        //    padding, isTarget, maintainOffset, primaryButtonOnly
        this.padding           = this.config.padding || [0, 0, 0, 0];
        this.isTarget          = (this.config.isTarget !== false);
        this.maintainOffset    = (this.config.maintainOffset);
        this.primaryButtonOnly = (this.config.primaryButtonOnly !== false);

    },

    /**
     * Executed when the linked element is available
     * @method handleOnAvailable
     * @private
     */
    handleOnAvailable: function() {
        this.available = true;
        this.resetConstraints();
        this.onAvailable();
    },

     /**
     * Configures the padding for the target zone in px.  Effectively expands
     * (or reduces) the virtual object size for targeting calculations.
     * Supports css-style shorthand; if only one parameter is passed, all sides
     * will have that padding, and if only two are passed, the top and bottom
     * will have the first param, the left and right the second.
     * @method setPadding
     * @param {int} iTop    Top pad
     * @param {int} iRight  Right pad
     * @param {int} iBot    Bot pad
     * @param {int} iLeft   Left pad
     */
    setPadding: function(iTop, iRight, iBot, iLeft) {
        // this.padding = [iLeft, iRight, iTop, iBot];
        if (!iRight && 0 !== iRight) {
            this.padding = [iTop, iTop, iTop, iTop];
        } else if (!iBot && 0 !== iBot) {
            this.padding = [iTop, iRight, iTop, iRight];
        } else {
            this.padding = [iTop, iRight, iBot, iLeft];
        }
    },

    /**
     * Stores the initial placement of the linked element.
     * @method setInitialPosition
     * @param {int} diffX   the X offset, default 0
     * @param {int} diffY   the Y offset, default 0
     */
    setInitPosition: function(diffX, diffY) {
        var el = this.getEl();

        if (!this.DDM.verifyEl(el)) {
            return;
        }

        var dx = diffX || 0;
        var dy = diffY || 0;

        var p = Dom.getXY( el );

        this.initPageX = p[0] - dx;
        this.initPageY = p[1] - dy;

        this.lastPageX = p[0];
        this.lastPageY = p[1];


        this.setStartPosition(p);
    },

    /**
     * Sets the start position of the element.  This is set when the obj
     * is initialized, the reset when a drag is started.
     * @method setStartPosition
     * @param pos current position (from previous lookup)
     * @private
     */
    setStartPosition: function(pos) {
        var p = pos || Dom.getXY( this.getEl() );
        this.deltaSetXY = null;

        this.startPageX = p[0];
        this.startPageY = p[1];
    },

    /**
     * Add this instance to a group of related drag/drop objects.  All
     * instances belong to at least one group, and can belong to as many
     * groups as needed.
     * @method addToGroup
     * @param sGroup {string} the name of the group
     */
    addToGroup: function(sGroup) {
        this.groups[sGroup] = true;
        this.DDM.regDragDrop(this, sGroup);
    },

    /**
     * Remove's this instance from the supplied interaction group
     * @method removeFromGroup
     * @param {string}  sGroup  The group to drop
     */
    removeFromGroup: function(sGroup) {
        if (this.groups[sGroup]) {
            delete this.groups[sGroup];
        }

        this.DDM.removeDDFromGroup(this, sGroup);
    },

    /**
     * Allows you to specify that an element other than the linked element
     * will be moved with the cursor during a drag
     * @method setDragElId
     * @param id {string} the id of the element that will be used to initiate the drag
     */
    setDragElId: function(id) {
        this.dragElId = id;
    },

    /**
     * Allows you to specify a child of the linked element that should be
     * used to initiate the drag operation.  An example of this would be if
     * you have a content div with text and links.  Clicking anywhere in the
     * content area would normally start the drag operation.  Use this method
     * to specify that an element inside of the content div is the element
     * that starts the drag operation.
     * @method setHandleElId
     * @param id {string} the id of the element that will be used to
     * initiate the drag.
     */
    setHandleElId: function(id) {
        if (typeof id !== "string") {
            id = Ext.id(id);
        }
        this.handleElId = id;
        this.DDM.regHandle(this.id, id);
    },

    /**
     * Allows you to set an element outside of the linked element as a drag
     * handle
     * @method setOuterHandleElId
     * @param id the id of the element that will be used to initiate the drag
     */
    setOuterHandleElId: function(id) {
        if (typeof id !== "string") {
            id = Ext.id(id);
        }
        Event.on(id, "mousedown",
                this.handleMouseDown, this);
        this.setHandleElId(id);

        this.hasOuterHandles = true;
    },

    /**
     * Remove all drag and drop hooks for this element
     * @method unreg
     */
    unreg: function() {
        Event.un(this.id, "mousedown",
                this.handleMouseDown);
        this._domRef = null;
        this.DDM._remove(this);
    },

    destroy : function(){
        this.unreg();
    },

    /**
     * Returns true if this instance is locked, or the drag drop mgr is locked
     * (meaning that all drag/drop is disabled on the page.)
     * @method isLocked
     * @return {boolean} true if this obj or all drag/drop is locked, else
     * false
     */
    isLocked: function() {
        return (this.DDM.isLocked() || this.locked);
    },

    /**
     * Fired when this object is clicked
     * @method handleMouseDown
     * @param {Event} e
     * @param {Ext.dd.DragDrop} oDD the clicked dd object (this dd obj)
     * @private
     */
    handleMouseDown: function(e, oDD){
        if (this.primaryButtonOnly && e.button != 0) {
            return;
        }

        if (this.isLocked()) {
            return;
        }

        this.DDM.refreshCache(this.groups);

        var pt = new Ext.lib.Point(Ext.lib.Event.getPageX(e), Ext.lib.Event.getPageY(e));
        if (!this.hasOuterHandles && !this.DDM.isOverTarget(pt, this) )  {
        } else {
            if (this.clickValidator(e)) {

                // set the initial element position
                this.setStartPosition();


                this.b4MouseDown(e);
                this.onMouseDown(e);

                this.DDM.handleMouseDown(e, this);

                this.DDM.stopEvent(e);
            } else {


            }
        }
    },

    clickValidator: function(e) {
        var target = e.getTarget();
        return ( this.isValidHandleChild(target) &&
                    (this.id == this.handleElId ||
                        this.DDM.handleWasClicked(target, this.id)) );
    },

    /**
     * Allows you to specify a tag name that should not start a drag operation
     * when clicked.  This is designed to facilitate embedding links within a
     * drag handle that do something other than start the drag.
     * @method addInvalidHandleType
     * @param {string} tagName the type of element to exclude
     */
    addInvalidHandleType: function(tagName) {
        var type = tagName.toUpperCase();
        this.invalidHandleTypes[type] = type;
    },

    /**
     * Lets you to specify an element id for a child of a drag handle
     * that should not initiate a drag
     * @method addInvalidHandleId
     * @param {string} id the element id of the element you wish to ignore
     */
    addInvalidHandleId: function(id) {
        if (typeof id !== "string") {
            id = Ext.id(id);
        }
        this.invalidHandleIds[id] = id;
    },

    /**
     * Lets you specify a css class of elements that will not initiate a drag
     * @method addInvalidHandleClass
     * @param {string} cssClass the class of the elements you wish to ignore
     */
    addInvalidHandleClass: function(cssClass) {
        this.invalidHandleClasses.push(cssClass);
    },

    /**
     * Unsets an excluded tag name set by addInvalidHandleType
     * @method removeInvalidHandleType
     * @param {string} tagName the type of element to unexclude
     */
    removeInvalidHandleType: function(tagName) {
        var type = tagName.toUpperCase();
        // this.invalidHandleTypes[type] = null;
        delete this.invalidHandleTypes[type];
    },

    /**
     * Unsets an invalid handle id
     * @method removeInvalidHandleId
     * @param {string} id the id of the element to re-enable
     */
    removeInvalidHandleId: function(id) {
        if (typeof id !== "string") {
            id = Ext.id(id);
        }
        delete this.invalidHandleIds[id];
    },

    /**
     * Unsets an invalid css class
     * @method removeInvalidHandleClass
     * @param {string} cssClass the class of the element(s) you wish to
     * re-enable
     */
    removeInvalidHandleClass: function(cssClass) {
        for (var i=0, len=this.invalidHandleClasses.length; i<len; ++i) {
            if (this.invalidHandleClasses[i] == cssClass) {
                delete this.invalidHandleClasses[i];
            }
        }
    },

    /**
     * Checks the tag exclusion list to see if this click should be ignored
     * @method isValidHandleChild
     * @param {HTMLElement} node the HTMLElement to evaluate
     * @return {boolean} true if this is a valid tag type, false if not
     */
    isValidHandleChild: function(node) {

        var valid = true;
        // var n = (node.nodeName == "#text") ? node.parentNode : node;
        var nodeName;
        try {
            nodeName = node.nodeName.toUpperCase();
        } catch(e) {
            nodeName = node.nodeName;
        }
        valid = valid && !this.invalidHandleTypes[nodeName];
        valid = valid && !this.invalidHandleIds[node.id];

        for (var i=0, len=this.invalidHandleClasses.length; valid && i<len; ++i) {
            valid = !Dom.hasClass(node, this.invalidHandleClasses[i]);
        }


        return valid;

    },

    /**
     * Create the array of horizontal tick marks if an interval was specified
     * in setXConstraint().
     * @method setXTicks
     * @private
     */
    setXTicks: function(iStartX, iTickSize) {
        this.xTicks = [];
        this.xTickSize = iTickSize;

        var tickMap = {};

        for (var i = this.initPageX; i >= this.minX; i = i - iTickSize) {
            if (!tickMap[i]) {
                this.xTicks[this.xTicks.length] = i;
                tickMap[i] = true;
            }
        }

        for (i = this.initPageX; i <= this.maxX; i = i + iTickSize) {
            if (!tickMap[i]) {
                this.xTicks[this.xTicks.length] = i;
                tickMap[i] = true;
            }
        }

        this.xTicks.sort(this.DDM.numericSort) ;
    },

    /**
     * Create the array of vertical tick marks if an interval was specified in
     * setYConstraint().
     * @method setYTicks
     * @private
     */
    setYTicks: function(iStartY, iTickSize) {
        this.yTicks = [];
        this.yTickSize = iTickSize;

        var tickMap = {};

        for (var i = this.initPageY; i >= this.minY; i = i - iTickSize) {
            if (!tickMap[i]) {
                this.yTicks[this.yTicks.length] = i;
                tickMap[i] = true;
            }
        }

        for (i = this.initPageY; i <= this.maxY; i = i + iTickSize) {
            if (!tickMap[i]) {
                this.yTicks[this.yTicks.length] = i;
                tickMap[i] = true;
            }
        }

        this.yTicks.sort(this.DDM.numericSort) ;
    },

    /**
     * By default, the element can be dragged any place on the screen.  Use
     * this method to limit the horizontal travel of the element.  Pass in
     * 0,0 for the parameters if you want to lock the drag to the y axis.
     * @method setXConstraint
     * @param {int} iLeft the number of pixels the element can move to the left
     * @param {int} iRight the number of pixels the element can move to the
     * right
     * @param {int} iTickSize optional parameter for specifying that the
     * element
     * should move iTickSize pixels at a time.
     */
    setXConstraint: function(iLeft, iRight, iTickSize) {
        this.leftConstraint = iLeft;
        this.rightConstraint = iRight;

        this.minX = this.initPageX - iLeft;
        this.maxX = this.initPageX + iRight;
        if (iTickSize) { this.setXTicks(this.initPageX, iTickSize); }

        this.constrainX = true;
    },

    /**
     * Clears any constraints applied to this instance.  Also clears ticks
     * since they can't exist independent of a constraint at this time.
     * @method clearConstraints
     */
    clearConstraints: function() {
        this.constrainX = false;
        this.constrainY = false;
        this.clearTicks();
    },

    /**
     * Clears any tick interval defined for this instance
     * @method clearTicks
     */
    clearTicks: function() {
        this.xTicks = null;
        this.yTicks = null;
        this.xTickSize = 0;
        this.yTickSize = 0;
    },

    /**
     * By default, the element can be dragged any place on the screen.  Set
     * this to limit the vertical travel of the element.  Pass in 0,0 for the
     * parameters if you want to lock the drag to the x axis.
     * @method setYConstraint
     * @param {int} iUp the number of pixels the element can move up
     * @param {int} iDown the number of pixels the element can move down
     * @param {int} iTickSize optional parameter for specifying that the
     * element should move iTickSize pixels at a time.
     */
    setYConstraint: function(iUp, iDown, iTickSize) {
        this.topConstraint = iUp;
        this.bottomConstraint = iDown;

        this.minY = this.initPageY - iUp;
        this.maxY = this.initPageY + iDown;
        if (iTickSize) { this.setYTicks(this.initPageY, iTickSize); }

        this.constrainY = true;

    },

    /**
     * resetConstraints must be called if you manually reposition a dd element.
     * @method resetConstraints
     * @param {boolean} maintainOffset
     */
    resetConstraints: function() {


        // Maintain offsets if necessary
        if (this.initPageX || this.initPageX === 0) {
            // figure out how much this thing has moved
            var dx = (this.maintainOffset) ? this.lastPageX - this.initPageX : 0;
            var dy = (this.maintainOffset) ? this.lastPageY - this.initPageY : 0;

            this.setInitPosition(dx, dy);

        // This is the first time we have detected the element's position
        } else {
            this.setInitPosition();
        }

        if (this.constrainX) {
            this.setXConstraint( this.leftConstraint,
                                 this.rightConstraint,
                                 this.xTickSize        );
        }

        if (this.constrainY) {
            this.setYConstraint( this.topConstraint,
                                 this.bottomConstraint,
                                 this.yTickSize         );
        }
    },

    /**
     * Normally the drag element is moved pixel by pixel, but we can specify
     * that it move a number of pixels at a time.  This method resolves the
     * location when we have it set up like this.
     * @method getTick
     * @param {int} val where we want to place the object
     * @param {int[]} tickArray sorted array of valid points
     * @return {int} the closest tick
     * @private
     */
    getTick: function(val, tickArray) {

        if (!tickArray) {
            // If tick interval is not defined, it is effectively 1 pixel,
            // so we return the value passed to us.
            return val;
        } else if (tickArray[0] >= val) {
            // The value is lower than the first tick, so we return the first
            // tick.
            return tickArray[0];
        } else {
            for (var i=0, len=tickArray.length; i<len; ++i) {
                var next = i + 1;
                if (tickArray[next] && tickArray[next] >= val) {
                    var diff1 = val - tickArray[i];
                    var diff2 = tickArray[next] - val;
                    return (diff2 > diff1) ? tickArray[i] : tickArray[next];
                }
            }

            // The value is larger than the last tick, so we return the last
            // tick.
            return tickArray[tickArray.length - 1];
        }
    },

    /**
     * toString method
     * @method toString
     * @return {string} string representation of the dd obj
     */
    toString: function() {
        return ("DragDrop " + this.id);
    }

};

})();
/**
 * The drag and drop utility provides a framework for building drag and drop
 * applications.  In addition to enabling drag and drop for specific elements,
 * the drag and drop elements are tracked by the manager class, and the
 * interactions between the various elements are tracked during the drag and
 * the implementing code is notified about these important moments.
 */

// Only load the library once.  Rewriting the manager class would orphan
// existing drag and drop instances.
if (!Ext.dd.DragDropMgr) {

/**
 * @class Ext.dd.DragDropMgr
 * DragDropMgr is a singleton that tracks the element interaction for
 * all DragDrop items in the window.  Generally, you will not call
 * this class directly, but it does have helper methods that could
 * be useful in your DragDrop implementations.
 * @singleton
 */
Ext.dd.DragDropMgr = function() {

    var Event = Ext.EventManager;

    return {

        /**
         * Two dimensional Array of registered DragDrop objects.  The first
         * dimension is the DragDrop item group, the second the DragDrop
         * object.
         * @property ids
         * @type {string: string}
         * @private
         * @static
         */
        ids: {},

        /**
         * Array of element ids defined as drag handles.  Used to determine
         * if the element that generated the mousedown event is actually the
         * handle and not the html element itself.
         * @property handleIds
         * @type {string: string}
         * @private
         * @static
         */
        handleIds: {},

        /**
         * the DragDrop object that is currently being dragged
         * @property dragCurrent
         * @type DragDrop
         * @private
         * @static
         **/
        dragCurrent: null,

        /**
         * the DragDrop object(s) that are being hovered over
         * @property dragOvers
         * @type Array
         * @private
         * @static
         */
        dragOvers: {},

        /**
         * the X distance between the cursor and the object being dragged
         * @property deltaX
         * @type int
         * @private
         * @static
         */
        deltaX: 0,

        /**
         * the Y distance between the cursor and the object being dragged
         * @property deltaY
         * @type int
         * @private
         * @static
         */
        deltaY: 0,

        /**
         * Flag to determine if we should prevent the default behavior of the
         * events we define. By default this is true, but this can be set to
         * false if you need the default behavior (not recommended)
         * @property preventDefault
         * @type boolean
         * @static
         */
        preventDefault: true,

        /**
         * Flag to determine if we should stop the propagation of the events
         * we generate. This is true by default but you may want to set it to
         * false if the html element contains other features that require the
         * mouse click.
         * @property stopPropagation
         * @type boolean
         * @static
         */
        stopPropagation: true,

        /**
         * Internal flag that is set to true when drag and drop has been
         * intialized
         * @property initialized
         * @private
         * @static
         */
        initalized: false,

        /**
         * All drag and drop can be disabled.
         * @property locked
         * @private
         * @static
         */
        locked: false,

        /**
         * Called the first time an element is registered.
         * @method init
         * @private
         * @static
         */
        init: function() {
            this.initialized = true;
        },

        /**
         * In point mode, drag and drop interaction is defined by the
         * location of the cursor during the drag/drop
         * @property POINT
         * @type int
         * @static
         */
        POINT: 0,

        /**
         * In intersect mode, drag and drop interactio nis defined by the
         * overlap of two or more drag and drop objects.
         * @property INTERSECT
         * @type int
         * @static
         */
        INTERSECT: 1,

        /**
         * The current drag and drop mode.  Default: POINT
         * @property mode
         * @type int
         * @static
         */
        mode: 0,

        /**
         * Runs method on all drag and drop objects
         * @method _execOnAll
         * @private
         * @static
         */
        _execOnAll: function(sMethod, args) {
            for (var i in this.ids) {
                for (var j in this.ids[i]) {
                    var oDD = this.ids[i][j];
                    if (! this.isTypeOfDD(oDD)) {
                        continue;
                    }
                    oDD[sMethod].apply(oDD, args);
                }
            }
        },

        /**
         * Drag and drop initialization.  Sets up the global event handlers
         * @method _onLoad
         * @private
         * @static
         */
        _onLoad: function() {

            this.init();


            Event.on(document, "mouseup",   this.handleMouseUp, this, true);
            Event.on(document, "mousemove", this.handleMouseMove, this, true);
            Event.on(window,   "beforeunload",    this._onUnload, this, true);
            Event.on(window,   "resize",    this._onResize, this, true);
            // Event.on(window,   "mouseout",    this._test);

        },

        /**
         * Reset constraints on all drag and drop objs
         * @method _onResize
         * @private
         * @static
         */
        _onResize: function(e) {
            this._execOnAll("resetConstraints", []);
        },

        /**
         * Lock all drag and drop functionality
         * @method lock
         * @static
         */
        lock: function() { this.locked = true; },

        /**
         * Unlock all drag and drop functionality
         * @method unlock
         * @static
         */
        unlock: function() { this.locked = false; },

        /**
         * Is drag and drop locked?
         * @method isLocked
         * @return {boolean} True if drag and drop is locked, false otherwise.
         * @static
         */
        isLocked: function() { return this.locked; },

        /**
         * Location cache that is set for all drag drop objects when a drag is
         * initiated, cleared when the drag is finished.
         * @property locationCache
         * @private
         * @static
         */
        locationCache: {},

        /**
         * Set useCache to false if you want to force object the lookup of each
         * drag and drop linked element constantly during a drag.
         * @property useCache
         * @type boolean
         * @static
         */
        useCache: true,

        /**
         * The number of pixels that the mouse needs to move after the
         * mousedown before the drag is initiated.  Default=3;
         * @property clickPixelThresh
         * @type int
         * @static
         */
        clickPixelThresh: 3,

        /**
         * The number of milliseconds after the mousedown event to initiate the
         * drag if we don't get a mouseup event. Default=1000
         * @property clickTimeThresh
         * @type int
         * @static
         */
        clickTimeThresh: 350,

        /**
         * Flag that indicates that either the drag pixel threshold or the
         * mousdown time threshold has been met
         * @property dragThreshMet
         * @type boolean
         * @private
         * @static
         */
        dragThreshMet: false,

        /**
         * Timeout used for the click time threshold
         * @property clickTimeout
         * @type Object
         * @private
         * @static
         */
        clickTimeout: null,

        /**
         * The X position of the mousedown event stored for later use when a
         * drag threshold is met.
         * @property startX
         * @type int
         * @private
         * @static
         */
        startX: 0,

        /**
         * The Y position of the mousedown event stored for later use when a
         * drag threshold is met.
         * @property startY
         * @type int
         * @private
         * @static
         */
        startY: 0,

        /**
         * Each DragDrop instance must be registered with the DragDropMgr.
         * This is executed in DragDrop.init()
         * @method regDragDrop
         * @param {DragDrop} oDD the DragDrop object to register
         * @param {String} sGroup the name of the group this element belongs to
         * @static
         */
        regDragDrop: function(oDD, sGroup) {
            if (!this.initialized) { this.init(); }

            if (!this.ids[sGroup]) {
                this.ids[sGroup] = {};
            }
            this.ids[sGroup][oDD.id] = oDD;
        },

        /**
         * Removes the supplied dd instance from the supplied group. Executed
         * by DragDrop.removeFromGroup, so don't call this function directly.
         * @method removeDDFromGroup
         * @private
         * @static
         */
        removeDDFromGroup: function(oDD, sGroup) {
            if (!this.ids[sGroup]) {
                this.ids[sGroup] = {};
            }

            var obj = this.ids[sGroup];
            if (obj && obj[oDD.id]) {
                delete obj[oDD.id];
            }
        },

        /**
         * Unregisters a drag and drop item.  This is executed in
         * DragDrop.unreg, use that method instead of calling this directly.
         * @method _remove
         * @private
         * @static
         */
        _remove: function(oDD) {
            for (var g in oDD.groups) {
                if (g && this.ids[g][oDD.id]) {
                    delete this.ids[g][oDD.id];
                }
            }
            delete this.handleIds[oDD.id];
        },

        /**
         * Each DragDrop handle element must be registered.  This is done
         * automatically when executing DragDrop.setHandleElId()
         * @method regHandle
         * @param {String} sDDId the DragDrop id this element is a handle for
         * @param {String} sHandleId the id of the element that is the drag
         * handle
         * @static
         */
        regHandle: function(sDDId, sHandleId) {
            if (!this.handleIds[sDDId]) {
                this.handleIds[sDDId] = {};
            }
            this.handleIds[sDDId][sHandleId] = sHandleId;
        },

        /**
         * Utility function to determine if a given element has been
         * registered as a drag drop item.
         * @method isDragDrop
         * @param {String} id the element id to check
         * @return {boolean} true if this element is a DragDrop item,
         * false otherwise
         * @static
         */
        isDragDrop: function(id) {
            return ( this.getDDById(id) ) ? true : false;
        },

        /**
         * Returns the drag and drop instances that are in all groups the
         * passed in instance belongs to.
         * @method getRelated
         * @param {DragDrop} p_oDD the obj to get related data for
         * @param {boolean} bTargetsOnly if true, only return targetable objs
         * @return {DragDrop[]} the related instances
         * @static
         */
        getRelated: function(p_oDD, bTargetsOnly) {
            var oDDs = [];
            for (var i in p_oDD.groups) {
                for (j in this.ids[i]) {
                    var dd = this.ids[i][j];
                    if (! this.isTypeOfDD(dd)) {
                        continue;
                    }
                    if (!bTargetsOnly || dd.isTarget) {
                        oDDs[oDDs.length] = dd;
                    }
                }
            }

            return oDDs;
        },

        /**
         * Returns true if the specified dd target is a legal target for
         * the specifice drag obj
         * @method isLegalTarget
         * @param {DragDrop} the drag obj
         * @param {DragDrop} the target
         * @return {boolean} true if the target is a legal target for the
         * dd obj
         * @static
         */
        isLegalTarget: function (oDD, oTargetDD) {
            var targets = this.getRelated(oDD, true);
            for (var i=0, len=targets.length;i<len;++i) {
                if (targets[i].id == oTargetDD.id) {
                    return true;
                }
            }

            return false;
        },

        /**
         * My goal is to be able to transparently determine if an object is
         * typeof DragDrop, and the exact subclass of DragDrop.  typeof
         * returns "object", oDD.constructor.toString() always returns
         * "DragDrop" and not the name of the subclass.  So for now it just
         * evaluates a well-known variable in DragDrop.
         * @method isTypeOfDD
         * @param {Object} the object to evaluate
         * @return {boolean} true if typeof oDD = DragDrop
         * @static
         */
        isTypeOfDD: function (oDD) {
            return (oDD && oDD.__ygDragDrop);
        },

        /**
         * Utility function to determine if a given element has been
         * registered as a drag drop handle for the given Drag Drop object.
         * @method isHandle
         * @param {String} id the element id to check
         * @return {boolean} true if this element is a DragDrop handle, false
         * otherwise
         * @static
         */
        isHandle: function(sDDId, sHandleId) {
            return ( this.handleIds[sDDId] &&
                            this.handleIds[sDDId][sHandleId] );
        },

        /**
         * Returns the DragDrop instance for a given id
         * @method getDDById
         * @param {String} id the id of the DragDrop object
         * @return {DragDrop} the drag drop object, null if it is not found
         * @static
         */
        getDDById: function(id) {
            for (var i in this.ids) {
                if (this.ids[i][id]) {
                    return this.ids[i][id];
                }
            }
            return null;
        },

        /**
         * Fired after a registered DragDrop object gets the mousedown event.
         * Sets up the events required to track the object being dragged
         * @method handleMouseDown
         * @param {Event} e the event
         * @param oDD the DragDrop object being dragged
         * @private
         * @static
         */
        handleMouseDown: function(e, oDD) {
            if(Ext.QuickTips){
                Ext.QuickTips.disable();
            }
            this.currentTarget = e.getTarget();

            this.dragCurrent = oDD;

            var el = oDD.getEl();

            // track start position
            this.startX = e.getPageX();
            this.startY = e.getPageY();

            this.deltaX = this.startX - el.offsetLeft;
            this.deltaY = this.startY - el.offsetTop;

            this.dragThreshMet = false;

            this.clickTimeout = setTimeout(
                    function() {
                        var DDM = Ext.dd.DDM;
                        DDM.startDrag(DDM.startX, DDM.startY);
                    },
                    this.clickTimeThresh );
        },

        /**
         * Fired when either the drag pixel threshol or the mousedown hold
         * time threshold has been met.
         * @method startDrag
         * @param x {int} the X position of the original mousedown
         * @param y {int} the Y position of the original mousedown
         * @static
         */
        startDrag: function(x, y) {
            clearTimeout(this.clickTimeout);
            if (this.dragCurrent) {
                this.dragCurrent.b4StartDrag(x, y);
                this.dragCurrent.startDrag(x, y);
            }
            this.dragThreshMet = true;
        },

        /**
         * Internal function to handle the mouseup event.  Will be invoked
         * from the context of the document.
         * @method handleMouseUp
         * @param {Event} e the event
         * @private
         * @static
         */
        handleMouseUp: function(e) {

            if(Ext.QuickTips){
                Ext.QuickTips.enable();
            }
            if (! this.dragCurrent) {
                return;
            }

            clearTimeout(this.clickTimeout);

            if (this.dragThreshMet) {
                this.fireEvents(e, true);
            } else {
            }

            this.stopDrag(e);

            this.stopEvent(e);
        },

        /**
         * Utility to stop event propagation and event default, if these
         * features are turned on.
         * @method stopEvent
         * @param {Event} e the event as returned by this.getEvent()
         * @static
         */
        stopEvent: function(e){
            if(this.stopPropagation) {
                e.stopPropagation();
            }

            if (this.preventDefault) {
                e.preventDefault();
            }
        },

        /**
         * Internal function to clean up event handlers after the drag
         * operation is complete
         * @method stopDrag
         * @param {Event} e the event
         * @private
         * @static
         */
        stopDrag: function(e) {
            // Fire the drag end event for the item that was dragged
            if (this.dragCurrent) {
                if (this.dragThreshMet) {
                    this.dragCurrent.b4EndDrag(e);
                    this.dragCurrent.endDrag(e);
                }

                this.dragCurrent.onMouseUp(e);
            }

            this.dragCurrent = null;
            this.dragOvers = {};
        },

        /**
         * Internal function to handle the mousemove event.  Will be invoked
         * from the context of the html element.
         *
         * @TODO figure out what we can do about mouse events lost when the
         * user drags objects beyond the window boundary.  Currently we can
         * detect this in internet explorer by verifying that the mouse is
         * down during the mousemove event.  Firefox doesn't give us the
         * button state on the mousemove event.
         * @method handleMouseMove
         * @param {Event} e the event
         * @private
         * @static
         */
        handleMouseMove: function(e) {
            if (! this.dragCurrent) {
                return true;
            }

            // var button = e.which || e.button;

            // check for IE mouseup outside of page boundary
            if (Ext.isIE && (e.button !== 0 && e.button !== 1 && e.button !== 2)) {
                this.stopEvent(e);
                return this.handleMouseUp(e);
            }

            if (!this.dragThreshMet) {
                var diffX = Math.abs(this.startX - e.getPageX());
                var diffY = Math.abs(this.startY - e.getPageY());
                if (diffX > this.clickPixelThresh ||
                            diffY > this.clickPixelThresh) {
                    this.startDrag(this.startX, this.startY);
                }
            }

            if (this.dragThreshMet) {
                this.dragCurrent.b4Drag(e);
                this.dragCurrent.onDrag(e);
                if(!this.dragCurrent.moveOnly){
                    this.fireEvents(e, false);
                }
            }

            this.stopEvent(e);

            return true;
        },

        /**
         * Iterates over all of the DragDrop elements to find ones we are
         * hovering over or dropping on
         * @method fireEvents
         * @param {Event} e the event
         * @param {boolean} isDrop is this a drop op or a mouseover op?
         * @private
         * @static
         */
        fireEvents: function(e, isDrop) {
            var dc = this.dragCurrent;

            // If the user did the mouse up outside of the window, we could
            // get here even though we have ended the drag.
            if (!dc || dc.isLocked()) {
                return;
            }

            var pt = e.getPoint();

            // cache the previous dragOver array
            var oldOvers = [];

            var outEvts   = [];
            var overEvts  = [];
            var dropEvts  = [];
            var enterEvts = [];

            // Check to see if the object(s) we were hovering over is no longer
            // being hovered over so we can fire the onDragOut event
            for (var i in this.dragOvers) {

                var ddo = this.dragOvers[i];

                if (! this.isTypeOfDD(ddo)) {
                    continue;
                }

                if (! this.isOverTarget(pt, ddo, this.mode)) {
                    outEvts.push( ddo );
                }

                oldOvers[i] = true;
                delete this.dragOvers[i];
            }

            for (var sGroup in dc.groups) {

                if ("string" != typeof sGroup) {
                    continue;
                }

                for (i in this.ids[sGroup]) {
                    var oDD = this.ids[sGroup][i];
                    if (! this.isTypeOfDD(oDD)) {
                        continue;
                    }

                    if (oDD.isTarget && !oDD.isLocked() && oDD != dc) {
                        if (this.isOverTarget(pt, oDD, this.mode)) {
                            // look for drop interactions
                            if (isDrop) {
                                dropEvts.push( oDD );
                            // look for drag enter and drag over interactions
                            } else {

                                // initial drag over: dragEnter fires
                                if (!oldOvers[oDD.id]) {
                                    enterEvts.push( oDD );
                                // subsequent drag overs: dragOver fires
                                } else {
                                    overEvts.push( oDD );
                                }

                                this.dragOvers[oDD.id] = oDD;
                            }
                        }
                    }
                }
            }

            if (this.mode) {
                if (outEvts.length) {
                    dc.b4DragOut(e, outEvts);
                    dc.onDragOut(e, outEvts);
                }

                if (enterEvts.length) {
                    dc.onDragEnter(e, enterEvts);
                }

                if (overEvts.length) {
                    dc.b4DragOver(e, overEvts);
                    dc.onDragOver(e, overEvts);
                }

                if (dropEvts.length) {
                    dc.b4DragDrop(e, dropEvts);
                    dc.onDragDrop(e, dropEvts);
                }

            } else {
                // fire dragout events
                var len = 0;
                for (i=0, len=outEvts.length; i<len; ++i) {
                    dc.b4DragOut(e, outEvts[i].id);
                    dc.onDragOut(e, outEvts[i].id);
                }

                // fire enter events
                for (i=0,len=enterEvts.length; i<len; ++i) {
                    // dc.b4DragEnter(e, oDD.id);
                    dc.onDragEnter(e, enterEvts[i].id);
                }

                // fire over events
                for (i=0,len=overEvts.length; i<len; ++i) {
                    dc.b4DragOver(e, overEvts[i].id);
                    dc.onDragOver(e, overEvts[i].id);
                }

                // fire drop events
                for (i=0, len=dropEvts.length; i<len; ++i) {
                    dc.b4DragDrop(e, dropEvts[i].id);
                    dc.onDragDrop(e, dropEvts[i].id);
                }

            }

            // notify about a drop that did not find a target
            if (isDrop && !dropEvts.length) {
                dc.onInvalidDrop(e);
            }

        },

        /**
         * Helper function for getting the best match from the list of drag
         * and drop objects returned by the drag and drop events when we are
         * in INTERSECT mode.  It returns either the first object that the
         * cursor is over, or the object that has the greatest overlap with
         * the dragged element.
         * @method getBestMatch
         * @param  {DragDrop[]} dds The array of drag and drop objects
         * targeted
         * @return {DragDrop}       The best single match
         * @static
         */
        getBestMatch: function(dds) {
            var winner = null;
            // Return null if the input is not what we expect
            //if (!dds || !dds.length || dds.length == 0) {
               // winner = null;
            // If there is only one item, it wins
            //} else if (dds.length == 1) {

            var len = dds.length;

            if (len == 1) {
                winner = dds[0];
            } else {
                // Loop through the targeted items
                for (var i=0; i<len; ++i) {
                    var dd = dds[i];
                    // If the cursor is over the object, it wins.  If the
                    // cursor is over multiple matches, the first one we come
                    // to wins.
                    if (dd.cursorIsOver) {
                        winner = dd;
                        break;
                    // Otherwise the object with the most overlap wins
                    } else {
                        if (!winner ||
                            winner.overlap.getArea() < dd.overlap.getArea()) {
                            winner = dd;
                        }
                    }
                }
            }

            return winner;
        },

        /**
         * Refreshes the cache of the top-left and bottom-right points of the
         * drag and drop objects in the specified group(s).  This is in the
         * format that is stored in the drag and drop instance, so typical
         * usage is:
         * <code>
         * Ext.dd.DragDropMgr.refreshCache(ddinstance.groups);
         * </code>
         * Alternatively:
         * <code>
         * Ext.dd.DragDropMgr.refreshCache({group1:true, group2:true});
         * </code>
         * @TODO this really should be an indexed array.  Alternatively this
         * method could accept both.
         * @method refreshCache
         * @param {Object} groups an associative array of groups to refresh
         * @static
         */
        refreshCache: function(groups) {
            for (var sGroup in groups) {
                if ("string" != typeof sGroup) {
                    continue;
                }
                for (var i in this.ids[sGroup]) {
                    var oDD = this.ids[sGroup][i];

                    if (this.isTypeOfDD(oDD)) {
                    // if (this.isTypeOfDD(oDD) && oDD.isTarget) {
                        var loc = this.getLocation(oDD);
                        if (loc) {
                            this.locationCache[oDD.id] = loc;
                        } else {
                            delete this.locationCache[oDD.id];
                            // this will unregister the drag and drop object if
                            // the element is not in a usable state
                            // oDD.unreg();
                        }
                    }
                }
            }
        },

        /**
         * This checks to make sure an element exists and is in the DOM.  The
         * main purpose is to handle cases where innerHTML is used to remove
         * drag and drop objects from the DOM.  IE provides an 'unspecified
         * error' when trying to access the offsetParent of such an element
         * @method verifyEl
         * @param {HTMLElement} el the element to check
         * @return {boolean} true if the element looks usable
         * @static
         */
        verifyEl: function(el) {
            if (el) {
                var parent;
                if(Ext.isIE){
                    try{
                        parent = el.offsetParent;
                    }catch(e){}
                }else{
                    parent = el.offsetParent;
                }
                if (parent) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Returns a Region object containing the drag and drop element's position
         * and size, including the padding configured for it
         * @method getLocation
         * @param {DragDrop} oDD the drag and drop object to get the
         *                       location for
         * @return {Ext.lib.Region} a Region object representing the total area
         *                             the element occupies, including any padding
         *                             the instance is configured for.
         * @static
         */
        getLocation: function(oDD) {
            if (! this.isTypeOfDD(oDD)) {
                return null;
            }

            var el = oDD.getEl(), pos, x1, x2, y1, y2, t, r, b, l;

            try {
                pos= Ext.lib.Dom.getXY(el);
            } catch (e) { }

            if (!pos) {
                return null;
            }

            x1 = pos[0];
            x2 = x1 + el.offsetWidth;
            y1 = pos[1];
            y2 = y1 + el.offsetHeight;

            t = y1 - oDD.padding[0];
            r = x2 + oDD.padding[1];
            b = y2 + oDD.padding[2];
            l = x1 - oDD.padding[3];

            return new Ext.lib.Region( t, r, b, l );
        },

        /**
         * Checks the cursor location to see if it over the target
         * @method isOverTarget
         * @param {Ext.lib.Point} pt The point to evaluate
         * @param {DragDrop} oTarget the DragDrop object we are inspecting
         * @return {boolean} true if the mouse is over the target
         * @private
         * @static
         */
        isOverTarget: function(pt, oTarget, intersect) {
            // use cache if available
            var loc = this.locationCache[oTarget.id];
            if (!loc || !this.useCache) {
                loc = this.getLocation(oTarget);
                this.locationCache[oTarget.id] = loc;

            }

            if (!loc) {
                return false;
            }

            oTarget.cursorIsOver = loc.contains( pt );

            // DragDrop is using this as a sanity check for the initial mousedown
            // in this case we are done.  In POINT mode, if the drag obj has no
            // contraints, we are also done. Otherwise we need to evaluate the
            // location of the target as related to the actual location of the
            // dragged element.
            var dc = this.dragCurrent;
            if (!dc || !dc.getTargetCoord ||
                    (!intersect && !dc.constrainX && !dc.constrainY)) {
                return oTarget.cursorIsOver;
            }

            oTarget.overlap = null;

            // Get the current location of the drag element, this is the
            // location of the mouse event less the delta that represents
            // where the original mousedown happened on the element.  We
            // need to consider constraints and ticks as well.
            var pos = dc.getTargetCoord(pt.x, pt.y);

            var el = dc.getDragEl();
            var curRegion = new Ext.lib.Region( pos.y,
                                                   pos.x + el.offsetWidth,
                                                   pos.y + el.offsetHeight,
                                                   pos.x );

            var overlap = curRegion.intersect(loc);

            if (overlap) {
                oTarget.overlap = overlap;
                return (intersect) ? true : oTarget.cursorIsOver;
            } else {
                return false;
            }
        },

        /**
         * unload event handler
         * @method _onUnload
         * @private
         * @static
         */
        _onUnload: function(e, me) {
            Ext.dd.DragDropMgr.unregAll();
        },

        /**
         * Cleans up the drag and drop events and objects.
         * @method unregAll
         * @private
         * @static
         */
        unregAll: function() {

            if (this.dragCurrent) {
                this.stopDrag();
                this.dragCurrent = null;
            }

            this._execOnAll("unreg", []);

            for (i in this.elementCache) {
                delete this.elementCache[i];
            }

            this.elementCache = {};
            this.ids = {};
        },

        /**
         * A cache of DOM elements
         * @property elementCache
         * @private
         * @static
         */
        elementCache: {},

        /**
         * Get the wrapper for the DOM element specified
         * @method getElWrapper
         * @param {String} id the id of the element to get
         * @return {Ext.dd.DDM.ElementWrapper} the wrapped element
         * @private
         * @deprecated This wrapper isn't that useful
         * @static
         */
        getElWrapper: function(id) {
            var oWrapper = this.elementCache[id];
            if (!oWrapper || !oWrapper.el) {
                oWrapper = this.elementCache[id] =
                    new this.ElementWrapper(Ext.getDom(id));
            }
            return oWrapper;
        },

        /**
         * Returns the actual DOM element
         * @method getElement
         * @param {String} id the id of the elment to get
         * @return {Object} The element
         * @deprecated use Ext.lib.Ext.getDom instead
         * @static
         */
        getElement: function(id) {
            return Ext.getDom(id);
        },

        /**
         * Returns the style property for the DOM element (i.e.,
         * document.getElById(id).style)
         * @method getCss
         * @param {String} id the id of the elment to get
         * @return {Object} The style property of the element
         * @deprecated use Ext.lib.Dom instead
         * @static
         */
        getCss: function(id) {
            var el = Ext.getDom(id);
            return (el) ? el.style : null;
        },

        /**
         * Inner class for cached elements
         * @class DragDropMgr.ElementWrapper
         * @for DragDropMgr
         * @private
         * @deprecated
         */
        ElementWrapper: function(el) {
                /**
                 * The element
                 * @property el
                 */
                this.el = el || null;
                /**
                 * The element id
                 * @property id
                 */
                this.id = this.el && el.id;
                /**
                 * A reference to the style property
                 * @property css
                 */
                this.css = this.el && el.style;
            },

        /**
         * Returns the X position of an html element
         * @method getPosX
         * @param el the element for which to get the position
         * @return {int} the X coordinate
         * @for DragDropMgr
         * @deprecated use Ext.lib.Dom.getX instead
         * @static
         */
        getPosX: function(el) {
            return Ext.lib.Dom.getX(el);
        },

        /**
         * Returns the Y position of an html element
         * @method getPosY
         * @param el the element for which to get the position
         * @return {int} the Y coordinate
         * @deprecated use Ext.lib.Dom.getY instead
         * @static
         */
        getPosY: function(el) {
            return Ext.lib.Dom.getY(el);
        },

        /**
         * Swap two nodes.  In IE, we use the native method, for others we
         * emulate the IE behavior
         * @method swapNode
         * @param n1 the first node to swap
         * @param n2 the other node to swap
         * @static
         */
        swapNode: function(n1, n2) {
            if (n1.swapNode) {
                n1.swapNode(n2);
            } else {
                var p = n2.parentNode;
                var s = n2.nextSibling;

                if (s == n1) {
                    p.insertBefore(n1, n2);
                } else if (n2 == n1.nextSibling) {
                    p.insertBefore(n2, n1);
                } else {
                    n1.parentNode.replaceChild(n2, n1);
                    p.insertBefore(n1, s);
                }
            }
        },

        /**
         * Returns the current scroll position
         * @method getScroll
         * @private
         * @static
         */
        getScroll: function () {
            var t, l, dde=document.documentElement, db=document.body;
            if (dde && (dde.scrollTop || dde.scrollLeft)) {
                t = dde.scrollTop;
                l = dde.scrollLeft;
            } else if (db) {
                t = db.scrollTop;
                l = db.scrollLeft;
            } else {

            }
            return { top: t, left: l };
        },

        /**
         * Returns the specified element style property
         * @method getStyle
         * @param {HTMLElement} el          the element
         * @param {string}      styleProp   the style property
         * @return {string} The value of the style property
         * @deprecated use Ext.lib.Dom.getStyle
         * @static
         */
        getStyle: function(el, styleProp) {
            return Ext.fly(el).getStyle(styleProp);
        },

        /**
         * Gets the scrollTop
         * @method getScrollTop
         * @return {int} the document's scrollTop
         * @static
         */
        getScrollTop: function () { return this.getScroll().top; },

        /**
         * Gets the scrollLeft
         * @method getScrollLeft
         * @return {int} the document's scrollTop
         * @static
         */
        getScrollLeft: function () { return this.getScroll().left; },

        /**
         * Sets the x/y position of an element to the location of the
         * target element.
         * @method moveToEl
         * @param {HTMLElement} moveEl      The element to move
         * @param {HTMLElement} targetEl    The position reference element
         * @static
         */
        moveToEl: function (moveEl, targetEl) {
            var aCoord = Ext.lib.Dom.getXY(targetEl);
            Ext.lib.Dom.setXY(moveEl, aCoord);
        },

        /**
         * Numeric array sort function
         * @method numericSort
         * @static
         */
        numericSort: function(a, b) { return (a - b); },

        /**
         * Internal counter
         * @property _timeoutCount
         * @private
         * @static
         */
        _timeoutCount: 0,

        /**
         * Trying to make the load order less important.  Without this we get
         * an error if this file is loaded before the Event Utility.
         * @method _addListeners
         * @private
         * @static
         */
        _addListeners: function() {
            var DDM = Ext.dd.DDM;
            if ( Ext.lib.Event && document ) {
                DDM._onLoad();
            } else {
                if (DDM._timeoutCount > 2000) {
                } else {
                    setTimeout(DDM._addListeners, 10);
                    if (document && document.body) {
                        DDM._timeoutCount += 1;
                    }
                }
            }
        },

        /**
         * Recursively searches the immediate parent and all child nodes for
         * the handle element in order to determine wheter or not it was
         * clicked.
         * @method handleWasClicked
         * @param node the html element to inspect
         * @static
         */
        handleWasClicked: function(node, id) {
            if (this.isHandle(id, node.id)) {
                return true;
            } else {
                // check to see if this is a text node child of the one we want
                var p = node.parentNode;

                while (p) {
                    if (this.isHandle(id, p.id)) {
                        return true;
                    } else {
                        p = p.parentNode;
                    }
                }
            }

            return false;
        }

    };

}();

// shorter alias, save a few bytes
Ext.dd.DDM = Ext.dd.DragDropMgr;
Ext.dd.DDM._addListeners();

}

/**
 * @class Ext.dd.DD
 * A DragDrop implementation where the linked element follows the
 * mouse cursor during a drag.
 * @extends Ext.dd.DragDrop
 * @constructor
 * @param {String} id the id of the linked element
 * @param {String} sGroup the group of related DragDrop items
 * @param {object} config an object containing configurable attributes
 *                Valid properties for DD:
 *                    scroll
 */
Ext.dd.DD = function(id, sGroup, config) {
    if (id) {
        this.init(id, sGroup, config);
    }
};

Ext.extend(Ext.dd.DD, Ext.dd.DragDrop, {

    /**
     * When set to true, the utility automatically tries to scroll the browser
     * window wehn a drag and drop element is dragged near the viewport boundary.
     * Defaults to true.
     * @property scroll
     * @type boolean
     */
    scroll: true,

    /**
     * Sets the pointer offset to the distance between the linked element's top
     * left corner and the location the element was clicked
     * @method autoOffset
     * @param {int} iPageX the X coordinate of the click
     * @param {int} iPageY the Y coordinate of the click
     */
    autoOffset: function(iPageX, iPageY) {
        var x = iPageX - this.startPageX;
        var y = iPageY - this.startPageY;
        this.setDelta(x, y);
    },

    /**
     * Sets the pointer offset.  You can call this directly to force the
     * offset to be in a particular location (e.g., pass in 0,0 to set it
     * to the center of the object)
     * @method setDelta
     * @param {int} iDeltaX the distance from the left
     * @param {int} iDeltaY the distance from the top
     */
    setDelta: function(iDeltaX, iDeltaY) {
        this.deltaX = iDeltaX;
        this.deltaY = iDeltaY;
    },

    /**
     * Sets the drag element to the location of the mousedown or click event,
     * maintaining the cursor location relative to the location on the element
     * that was clicked.  Override this if you want to place the element in a
     * location other than where the cursor is.
     * @method setDragElPos
     * @param {int} iPageX the X coordinate of the mousedown or drag event
     * @param {int} iPageY the Y coordinate of the mousedown or drag event
     */
    setDragElPos: function(iPageX, iPageY) {
        // the first time we do this, we are going to check to make sure
        // the element has css positioning

        var el = this.getDragEl();
        this.alignElWithMouse(el, iPageX, iPageY);
    },

    /**
     * Sets the element to the location of the mousedown or click event,
     * maintaining the cursor location relative to the location on the element
     * that was clicked.  Override this if you want to place the element in a
     * location other than where the cursor is.
     * @method alignElWithMouse
     * @param {HTMLElement} el the element to move
     * @param {int} iPageX the X coordinate of the mousedown or drag event
     * @param {int} iPageY the Y coordinate of the mousedown or drag event
     */
    alignElWithMouse: function(el, iPageX, iPageY) {
        var oCoord = this.getTargetCoord(iPageX, iPageY);
        var fly = el.dom ? el : Ext.fly(el);
        if (!this.deltaSetXY) {
            var aCoord = [oCoord.x, oCoord.y];
            fly.setXY(aCoord);
            var newLeft = fly.getLeft(true);
            var newTop  = fly.getTop(true);
            this.deltaSetXY = [ newLeft - oCoord.x, newTop - oCoord.y ];
        } else {
            fly.setLeftTop(oCoord.x + this.deltaSetXY[0], oCoord.y + this.deltaSetXY[1]);
        }

        this.cachePosition(oCoord.x, oCoord.y);
        this.autoScroll(oCoord.x, oCoord.y, el.offsetHeight, el.offsetWidth);
        return oCoord;
    },

    /**
     * Saves the most recent position so that we can reset the constraints and
     * tick marks on-demand.  We need to know this so that we can calculate the
     * number of pixels the element is offset from its original position.
     * @method cachePosition
     * @param iPageX the current x position (optional, this just makes it so we
     * don't have to look it up again)
     * @param iPageY the current y position (optional, this just makes it so we
     * don't have to look it up again)
     */
    cachePosition: function(iPageX, iPageY) {
        if (iPageX) {
            this.lastPageX = iPageX;
            this.lastPageY = iPageY;
        } else {
            var aCoord = Ext.lib.Dom.getXY(this.getEl());
            this.lastPageX = aCoord[0];
            this.lastPageY = aCoord[1];
        }
    },

    /**
     * Auto-scroll the window if the dragged object has been moved beyond the
     * visible window boundary.
     * @method autoScroll
     * @param {int} x the drag element's x position
     * @param {int} y the drag element's y position
     * @param {int} h the height of the drag element
     * @param {int} w the width of the drag element
     * @private
     */
    autoScroll: function(x, y, h, w) {

        if (this.scroll) {
            // The client height
            var clientH = Ext.lib.Dom.getViewWidth();

            // The client width
            var clientW = Ext.lib.Dom.getViewHeight();

            // The amt scrolled down
            var st = this.DDM.getScrollTop();

            // The amt scrolled right
            var sl = this.DDM.getScrollLeft();

            // Location of the bottom of the element
            var bot = h + y;

            // Location of the right of the element
            var right = w + x;

            // The distance from the cursor to the bottom of the visible area,
            // adjusted so that we don't scroll if the cursor is beyond the
            // element drag constraints
            var toBot = (clientH + st - y - this.deltaY);

            // The distance from the cursor to the right of the visible area
            var toRight = (clientW + sl - x - this.deltaX);


            // How close to the edge the cursor must be before we scroll
            // var thresh = (document.all) ? 100 : 40;
            var thresh = 40;

            // How many pixels to scroll per autoscroll op.  This helps to reduce
            // clunky scrolling. IE is more sensitive about this ... it needs this
            // value to be higher.
            var scrAmt = (document.all) ? 80 : 30;

            // Scroll down if we are near the bottom of the visible page and the
            // obj extends below the crease
            if ( bot > clientH && toBot < thresh ) {
                window.scrollTo(sl, st + scrAmt);
            }

            // Scroll up if the window is scrolled down and the top of the object
            // goes above the top border
            if ( y < st && st > 0 && y - st < thresh ) {
                window.scrollTo(sl, st - scrAmt);
            }

            // Scroll right if the obj is beyond the right border and the cursor is
            // near the border.
            if ( right > clientW && toRight < thresh ) {
                window.scrollTo(sl + scrAmt, st);
            }

            // Scroll left if the window has been scrolled to the right and the obj
            // extends past the left border
            if ( x < sl && sl > 0 && x - sl < thresh ) {
                window.scrollTo(sl - scrAmt, st);
            }
        }
    },

    /**
     * Finds the location the element should be placed if we want to move
     * it to where the mouse location less the click offset would place us.
     * @method getTargetCoord
     * @param {int} iPageX the X coordinate of the click
     * @param {int} iPageY the Y coordinate of the click
     * @return an object that contains the coordinates (Object.x and Object.y)
     * @private
     */
    getTargetCoord: function(iPageX, iPageY) {


        var x = iPageX - this.deltaX;
        var y = iPageY - this.deltaY;

        if (this.constrainX) {
            if (x < this.minX) { x = this.minX; }
            if (x > this.maxX) { x = this.maxX; }
        }

        if (this.constrainY) {
            if (y < this.minY) { y = this.minY; }
            if (y > this.maxY) { y = this.maxY; }
        }

        x = this.getTick(x, this.xTicks);
        y = this.getTick(y, this.yTicks);


        return {x:x, y:y};
    },

    /*
     * Sets up config options specific to this class. Overrides
     * Ext.dd.DragDrop, but all versions of this method through the
     * inheritance chain are called
     */
    applyConfig: function() {
        Ext.dd.DD.superclass.applyConfig.call(this);
        this.scroll = (this.config.scroll !== false);
    },

    /*
     * Event that fires prior to the onMouseDown event.  Overrides
     * Ext.dd.DragDrop.
     */
    b4MouseDown: function(e) {
        // this.resetConstraints();
        this.autoOffset(e.getPageX(),
                            e.getPageY());
    },

    /*
     * Event that fires prior to the onDrag event.  Overrides
     * Ext.dd.DragDrop.
     */
    b4Drag: function(e) {
        this.setDragElPos(e.getPageX(),
                            e.getPageY());
    },

    toString: function() {
        return ("DD " + this.id);
    }

    //////////////////////////////////////////////////////////////////////////
    // Debugging ygDragDrop events that can be overridden
    //////////////////////////////////////////////////////////////////////////
    /*
    startDrag: function(x, y) {
    },

    onDrag: function(e) {
    },

    onDragEnter: function(e, id) {
    },

    onDragOver: function(e, id) {
    },

    onDragOut: function(e, id) {
    },

    onDragDrop: function(e, id) {
    },

    endDrag: function(e) {
    }

    */

});
/**
 * @class Ext.dd.DDProxy
 * A DragDrop implementation that inserts an empty, bordered div into
 * the document that follows the cursor during drag operations.  At the time of
 * the click, the frame div is resized to the dimensions of the linked html
 * element, and moved to the exact location of the linked element.
 *
 * References to the "frame" element refer to the single proxy element that
 * was created to be dragged in place of all DDProxy elements on the
 * page.
 *
 * @extends Ext.dd.DD
 * @constructor
 * @param {String} id the id of the linked html element
 * @param {String} sGroup the group of related DragDrop objects
 * @param {object} config an object containing configurable attributes
 *                Valid properties for DDProxy in addition to those in DragDrop:
 *                   resizeFrame, centerFrame, dragElId
 */
Ext.dd.DDProxy = function(id, sGroup, config) {
    if (id) {
        this.init(id, sGroup, config);
        this.initFrame();
    }
};

/**
 * The default drag frame div id
 * @property Ext.dd.DDProxy.dragElId
 * @type String
 * @static
 */
Ext.dd.DDProxy.dragElId = "ygddfdiv";

Ext.extend(Ext.dd.DDProxy, Ext.dd.DD, {

    /**
     * By default we resize the drag frame to be the same size as the element
     * we want to drag (this is to get the frame effect).  We can turn it off
     * if we want a different behavior.
     * @property resizeFrame
     * @type boolean
     */
    resizeFrame: true,

    /**
     * By default the frame is positioned exactly where the drag element is, so
     * we use the cursor offset provided by Ext.dd.DD.  Another option that works only if
     * you do not have constraints on the obj is to have the drag frame centered
     * around the cursor.  Set centerFrame to true for this effect.
     * @property centerFrame
     * @type boolean
     */
    centerFrame: false,

    /**
     * Creates the proxy element if it does not yet exist
     * @method createFrame
     */
    createFrame: function() {
        var self = this;
        var body = document.body;

        if (!body || !body.firstChild) {
            setTimeout( function() { self.createFrame(); }, 50 );
            return;
        }

        var div = this.getDragEl();

        if (!div) {
            div    = document.createElement("div");
            div.id = this.dragElId;
            var s  = div.style;

            s.position   = "absolute";
            s.visibility = "hidden";
            s.cursor     = "move";
            s.border     = "2px solid #aaa";
            s.zIndex     = 999;

            // appendChild can blow up IE if invoked prior to the window load event
            // while rendering a table.  It is possible there are other scenarios
            // that would cause this to happen as well.
            body.insertBefore(div, body.firstChild);
        }
    },

    /**
     * Initialization for the drag frame element.  Must be called in the
     * constructor of all subclasses
     * @method initFrame
     */
    initFrame: function() {
        this.createFrame();
    },

    applyConfig: function() {
        Ext.dd.DDProxy.superclass.applyConfig.call(this);

        this.resizeFrame = (this.config.resizeFrame !== false);
        this.centerFrame = (this.config.centerFrame);
        this.setDragElId(this.config.dragElId || Ext.dd.DDProxy.dragElId);
    },

    /**
     * Resizes the drag frame to the dimensions of the clicked object, positions
     * it over the object, and finally displays it
     * @method showFrame
     * @param {int} iPageX X click position
     * @param {int} iPageY Y click position
     * @private
     */
    showFrame: function(iPageX, iPageY) {
        var el = this.getEl();
        var dragEl = this.getDragEl();
        var s = dragEl.style;

        this._resizeProxy();

        if (this.centerFrame) {
            this.setDelta( Math.round(parseInt(s.width,  10)/2),
                           Math.round(parseInt(s.height, 10)/2) );
        }

        this.setDragElPos(iPageX, iPageY);

        Ext.fly(dragEl).show();
    },

    /**
     * The proxy is automatically resized to the dimensions of the linked
     * element when a drag is initiated, unless resizeFrame is set to false
     * @method _resizeProxy
     * @private
     */
    _resizeProxy: function() {
        if (this.resizeFrame) {
            var el = this.getEl();
            Ext.fly(this.getDragEl()).setSize(el.offsetWidth, el.offsetHeight);
        }
    },

    // overrides Ext.dd.DragDrop
    b4MouseDown: function(e) {
        var x = e.getPageX();
        var y = e.getPageY();
        this.autoOffset(x, y);
        this.setDragElPos(x, y);
    },

    // overrides Ext.dd.DragDrop
    b4StartDrag: function(x, y) {
        // show the drag frame
        this.showFrame(x, y);
    },

    // overrides Ext.dd.DragDrop
    b4EndDrag: function(e) {
        Ext.fly(this.getDragEl()).hide();
    },

    // overrides Ext.dd.DragDrop
    // By default we try to move the element to the last location of the frame.
    // This is so that the default behavior mirrors that of Ext.dd.DD.
    endDrag: function(e) {

        var lel = this.getEl();
        var del = this.getDragEl();

        // Show the drag frame briefly so we can get its position
        del.style.visibility = "";

        this.beforeMove();
        // Hide the linked element before the move to get around a Safari
        // rendering bug.
        lel.style.visibility = "hidden";
        Ext.dd.DDM.moveToEl(lel, del);
        del.style.visibility = "hidden";
        lel.style.visibility = "";

        this.afterDrag();
    },

    beforeMove : function(){

    },

    afterDrag : function(){

    },

    toString: function() {
        return ("DDProxy " + this.id);
    }

});
/**
 * @class Ext.dd.DDTarget
 * A DragDrop implementation that does not move, but can be a drop
 * target.  You would get the same result by simply omitting implementation
 * for the event callbacks, but this way we reduce the processing cost of the
 * event listener and the callbacks.
 * @extends Ext.dd.DragDrop
 * @constructor
 * @param {String} id the id of the element that is a drop target
 * @param {String} sGroup the group of related DragDrop objects
 * @param {object} config an object containing configurable attributes
 *                 Valid properties for DDTarget in addition to those in
 *                 DragDrop:
 *                    none
 */
Ext.dd.DDTarget = function(id, sGroup, config) {
    if (id) {
        this.initTarget(id, sGroup, config);
    }
};

// Ext.dd.DDTarget.prototype = new Ext.dd.DragDrop();
Ext.extend(Ext.dd.DDTarget, Ext.dd.DragDrop, {
    toString: function() {
        return ("DDTarget " + this.id);
    }
});




/**
 * @class Ext.dd.ScrollManager
 * Provides automatic scrolling of overflow regions in the page during drag operations.<br><br>
 * <b>Note: This class uses "Point Mode" and is untested in "Intersect Mode".</b>
 * @singleton
 */
Ext.dd.ScrollManager = function(){
    var ddm = Ext.dd.DragDropMgr;
    var els = {};
    var dragEl = null;
    var proc = {};
    
    var onStop = function(e){
        dragEl = null;
        clearProc();
    };
    
    var triggerRefresh = function(){
        if(ddm.dragCurrent){
             ddm.refreshCache(ddm.dragCurrent.groups);
        }
    };
    
    var doScroll = function(){
        if(ddm.dragCurrent){
            var dds = Ext.dd.ScrollManager;
            if(!dds.animate){
                if(proc.el.scroll(proc.dir, dds.increment)){
                    triggerRefresh();
                }
            }else{
                proc.el.scroll(proc.dir, dds.increment, true, dds.animDuration, triggerRefresh);
            }
        }
    };
    
    var clearProc = function(){
        if(proc.id){
            clearInterval(proc.id);
        }
        proc.id = 0;
        proc.el = null;
        proc.dir = "";
    };
    
    var startProc = function(el, dir){
        clearProc();
        proc.el = el;
        proc.dir = dir;
        proc.id = setInterval(doScroll, Ext.dd.ScrollManager.frequency);
    };
    
    var onFire = function(e, isDrop){
        if(isDrop || !ddm.dragCurrent){ return; }
        var dds = Ext.dd.ScrollManager;
        if(!dragEl || dragEl != ddm.dragCurrent){
            dragEl = ddm.dragCurrent;
            // refresh regions on drag start
            dds.refreshCache();
        }
        
        var xy = Ext.lib.Event.getXY(e);
        var pt = new Ext.lib.Point(xy[0], xy[1]);
        for(var id in els){
            var el = els[id], r = el._region;
            if(r && r.contains(pt) && el.isScrollable()){
                if(r.bottom - pt.y <= dds.thresh){
                    if(proc.el != el){
                        startProc(el, "down");
                    }
                    return;
                }else if(r.right - pt.x <= dds.thresh){
                    if(proc.el != el){
                        startProc(el, "left");
                    }
                    return;
                }else if(pt.y - r.top <= dds.thresh){
                    if(proc.el != el){
                        startProc(el, "up");
                    }
                    return;
                }else if(pt.x - r.left <= dds.thresh){
                    if(proc.el != el){
                        startProc(el, "right");
                    }
                    return;
                }
            }
        }
        clearProc();
    };
    
    ddm.fireEvents = ddm.fireEvents.createSequence(onFire, ddm);
    ddm.stopDrag = ddm.stopDrag.createSequence(onStop, ddm);
    
    return {
        /**
         * Registers new overflow element(s) to auto scroll
         * @param {String/HTMLElement/Element/Array} el The id of or the element to be scrolled or an array of either
         */
        register : function(el){
            if(el instanceof Array){
                for(var i = 0, len = el.length; i < len; i++) {
                	this.register(el[i]);
                }
            }else{
                el = Ext.get(el);
                els[el.id] = el;
            }
        },
        
        /**
         * Unregisters overflow element(s) so they are no longer scrolled
         * @param {String/HTMLElement/Element/Array} el The id of or the element to be removed or an array of either
         */
        unregister : function(el){
            if(el instanceof Array){
                for(var i = 0, len = el.length; i < len; i++) {
                	this.unregister(el[i]);
                }
            }else{
                el = Ext.get(el);
                delete els[el.id];
            }
        },
        
        /**
         * The number of pixels from the edge of a container the pointer needs to be to 
         * trigger scrolling (defaults to 25)
         * @type Number
         */
        thresh : 25,
        
        /**
         * The number of pixels to scroll in each scroll increment (defaults to 50)
         * @type Number
         */
        increment : 100,
        
        /**
         * The frequency of scrolls in milliseconds (defaults to 500)
         * @type Number
         */
        frequency : 500,
        
        /**
         * True to animate the scroll (defaults to true)
         * @type Boolean
         */
        animate: true,
        
        /**
         * The animation duration in seconds - 
         * MUST BE less than Ext.dd.ScrollManager.frequency! (defaults to .4)
         * @type Number
         */
        animDuration: .4,
        
        /**
         * Manually trigger a cache refresh.
         */
        refreshCache : function(){
            for(var id in els){
                if(typeof els[id] == 'object'){ // for people extending the object prototype
                    els[id]._region = els[id].getRegion();
                }
            }
        }
    };
}();



/**
 * @class Ext.dd.Registry
 * Provides easy access to all drag drop components that are registered on a page.  Items can be retrieved either
 * directly by DOM node id, or by passing in the drag drop event that occurred and looking up the event target.
 * @singleton
 */
Ext.dd.Registry = function(){
    var elements = {}; 
    var handles = {}; 
    var autoIdSeed = 0;

    var getId = function(el, autogen){
        if(typeof el == "string"){
            return el;
        }
        var id = el.id;
        if(!id && autogen !== false){
            id = "extdd-" + (++autoIdSeed);
            el.id = id;
        }
        return id;
    };
    
    return {
    /**
     * Register a drag drop element
     * @param {String/HTMLElement) element The id or DOM node to register
     * @param {Object} data (optional) A custom data object that will be passed between the elements that are involved
     * in drag drop operations.  You can populate this object with any arbitrary properties that your own code
     * knows how to interpret, plus there are some specific properties known to the Registry that should be
     * populated in the data object (if applicable):
     * <pre>
Value      Description<br />
---------  ------------------------------------------<br />
handles    Array of DOM nodes that trigger dragging<br />
           for the element being registered<br />
isHandle   True if the element passed in triggers<br />
           dragging itself, else false
</pre>
     */
        register : function(el, data){
            data = data || {};
            if(typeof el == "string"){
                el = document.getElementById(el);
            }
            data.ddel = el;
            elements[getId(el)] = data;
            if(data.isHandle !== false){
                handles[data.ddel.id] = data;
            }
            if(data.handles){
                var hs = data.handles;
                for(var i = 0, len = hs.length; i < len; i++){
                	handles[getId(hs[i])] = data;
                }
            }
        },

    /**
     * Unregister a drag drop element
     * @param {String/HTMLElement) element The id or DOM node to unregister
     */
        unregister : function(el){
            var id = getId(el, false);
            var data = elements[id];
            if(data){
                delete elements[id];
                if(data.handles){
                    var hs = data.handles;
                    for(var i = 0, len = hs.length; i < len; i++){
                    	delete handles[getId(hs[i], false)];
                    }
                }
            }
        },

    /**
     * Returns the handle registered for a DOM Node by id
     * @param {String/HTMLElement} id The DOM node or id to look up
     * @return {Object} handle The custom handle data
     */
        getHandle : function(id){
            if(typeof id != "string"){ // must be element?
                id = id.id;
            }
            return handles[id];
        },

    /**
     * Returns the handle that is registered for the DOM node that is the target of the event
     * @param {Event} e The event
     * @return {Object} handle The custom handle data
     */
        getHandleFromEvent : function(e){
            var t = Ext.lib.Event.getTarget(e);
            return t ? handles[t.id] : null;
        },

    /**
     * Returns a custom data object that is registered for a DOM node by id
     * @param {String/HTMLElement} id The DOM node or id to look up
     * @return {Object} data The custom data
     */
        getTarget : function(id){
            if(typeof id != "string"){ // must be element?
                id = id.id;
            }
            return elements[id];
        },

    /**
     * Returns a custom data object that is registered for the DOM node that is the target of the event
     * @param {Event} e The event
     * @return {Object} data The custom data
     */
        getTargetFromEvent : function(e){
            var t = Ext.lib.Event.getTarget(e);
            return t ? elements[t.id] || handles[t.id] : null;
        }
    };
}();



/**
 * @class Ext.dd.StatusProxy
 * A specialized drag proxy that supports a drop status icon, {@link Ext.Layer} styles and auto-repair.  This is the
 * default drag proxy used by all Ext.dd components.
 * @constructor
 * @param {Object} config
 */
Ext.dd.StatusProxy = function(config){
    Ext.apply(this, config);
    this.id = this.id || Ext.id();
    this.el = new Ext.Layer({
        dh: {
            id: this.id, tag: "div", cls: "x-dd-drag-proxy "+this.dropNotAllowed, children: [
                {tag: "div", cls: "x-dd-drop-icon"},
                {tag: "div", cls: "x-dd-drag-ghost"}
            ]
        }, 
        shadow: !config || config.shadow !== false
    });
    this.ghost = Ext.get(this.el.dom.childNodes[1]);
    this.dropStatus = this.dropNotAllowed;
};

Ext.dd.StatusProxy.prototype = {
    /**
     * @cfg {String} dropAllowed
     * The CSS class to apply to the status element when drop is allowed (defaults to "x-dd-drop-ok").
     */
    dropAllowed : "x-dd-drop-ok",
    /**
     * @cfg {String} dropNotAllowed
     * The CSS class to apply to the status element when drop is not allowed (defaults to "x-dd-drop-nodrop").
     */
    dropNotAllowed : "x-dd-drop-nodrop",

    /**
     * Updates the proxy's visual element to indicate the status of whether or not drop is allowed
     * over the current target element.
     * @param {String} cssClass The css class for the new drop status indicator image
     */
    setStatus : function(cssClass){
        cssClass = cssClass || this.dropNotAllowed;
        if(this.dropStatus != cssClass){
            this.el.replaceClass(this.dropStatus, cssClass);
            this.dropStatus = cssClass;
        }
    },

    /**
     * Resets the status indicator to the default dropNotAllowed value
     * @param {Boolean} clearGhost True to also remove all content from the ghost, false to preserve it
     */
    reset : function(clearGhost){
        this.el.dom.className = "x-dd-drag-proxy " + this.dropNotAllowed;
        this.dropStatus = this.dropNotAllowed;
        if(clearGhost){
            this.ghost.update("");
        }
    },

    /**
     * Updates the contents of the ghost element
     * @param {String} html The html that will replace the current innerHTML of the ghost element
     */
    update : function(html){
        if(typeof html == "string"){
            this.ghost.update(html);
        }else{
            this.ghost.update("");
            html.style.margin = "0";
            this.ghost.dom.appendChild(html);
        }        
    },

    /**
     * Returns the underlying proxy {@link Ext.Layer}
     * @return {Ext.Layer} el
    */
    getEl : function(){
        return this.el;
    },

    /**
     * Returns the ghost element
     * @return {Ext.Element} el
     */
    getGhost : function(){
        return this.ghost;
    },

    /**
     * Hides the proxy
     * @param {Boolean} clear True to reset the status and clear the ghost contents, false to preserve them
     */
    hide : function(clear){
        this.el.hide();
        if(clear){
            this.reset(true);
        }
    },

    /**
     * Stops the repair animation if it's currently running
     */
    stop : function(){
        if(this.anim && this.anim.isAnimated && this.anim.isAnimated()){
            this.anim.stop();
        }
    },

    /**
     * Displays this proxy
     */
    show : function(){
        this.el.show();
    },

    /**
     * Force the Layer to sync its shadow and shim positions to the element
     */
    sync : function(){
        this.el.sync();
    },

    /**
     * Causes the proxy to return to its position of origin via an animation.  Should be called after an
     * invalid drop operation by the item being dragged.
     * @param {Array} xy The XY position of the element ([x, y])
     * @param {Function} callback The function to call after the repair is complete
     * @param {Object} scope The scope in which to execute the callback
     */
    repair : function(xy, callback, scope){
        this.callback = callback;
        this.scope = scope;
        if(xy && this.animRepair !== false){
            this.el.addClass("x-dd-drag-repair");
            this.el.hideUnders(true);
            this.anim = this.el.shift({
                duration: this.repairDuration || .5,
                easing: 'easeOut',
                xy: xy,
                stopFx: true,
                callback: this.afterRepair,
                scope: this
            });
        }else{
            this.afterRepair();
        }
    },

    // private
    afterRepair : function(){
        this.hide(true);
        if(typeof this.callback == "function"){
            this.callback.call(this.scope || this);
        }
        this.callback = null;
        this.scope = null;
    }
};



/**
 * @class Ext.dd.DragSource
 * @extends Ext.dd.DDProxy
 * A simple class that provides the basic implementation needed to make any element draggable.
 * @constructor
 * @param {String/HTMLElement/Element} el The container element
 * @param {Object} config
 */
Ext.dd.DragSource = function(el, config){
    this.el = Ext.get(el);
    this.dragData = {};
    
    Ext.apply(this, config);
    
    if(!this.proxy){
        this.proxy = new Ext.dd.StatusProxy();
    }

    Ext.dd.DragSource.superclass.constructor.call(this, this.el.dom, this.ddGroup || this.group,
          {dragElId : this.proxy.id, resizeFrame: false, isTarget: false, scroll: this.scroll === true});
    
    this.dragging = false;
};

Ext.extend(Ext.dd.DragSource, Ext.dd.DDProxy, {
    /**
     * @cfg {String} dropAllowed
     * The CSS class returned to the drag source when drop is allowed (defaults to "x-dd-drop-ok").
     */
    dropAllowed : "x-dd-drop-ok",
    /**
     * @cfg {String} dropNotAllowed
     * The CSS class returned to the drag source when drop is not allowed (defaults to "x-dd-drop-nodrop").
     */
    dropNotAllowed : "x-dd-drop-nodrop",

    /**
     * Returns the data object associated with this drag source
     * @return {Object} data An object containing arbitrary data
     */
    getDragData : function(e){
        return this.dragData;
    },

    // private
    onDragEnter : function(e, id){
        var target = Ext.dd.DragDropMgr.getDDById(id);
        this.cachedTarget = target;
        if(this.beforeDragEnter(target, e, id) !== false){
            if(target.isNotifyTarget){
                var status = target.notifyEnter(this, e, this.dragData);
                this.proxy.setStatus(status);
            }else{
                this.proxy.setStatus(this.dropAllowed);
            }
            
            if(this.afterDragEnter){
                /**
                 * An empty function by default, but provided so that you can perform a custom action
                 * when the dragged item enters the drop target by providing an implementation.
                 * @param {Ext.dd.DragDrop} target The drop target
                 * @param {Event} e The event object
                 * @param {String} id The id of the dragged element
                 * @method afterDragEnter
                 */
                this.afterDragEnter(target, e, id);
            }
        }
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action
     * before the dragged item enters the drop target and optionally cancel the onDragEnter.
     * @param {Ext.dd.DragDrop} target The drop target
     * @param {Event} e The event object
     * @param {String} id The id of the dragged element
     * @return {Boolean} isValid True if the drag event is valid, else false to cancel
     */
    beforeDragEnter : function(target, e, id){
        return true;
    },

    // private
    alignElWithMouse: function() {
        Ext.dd.DragSource.superclass.alignElWithMouse.apply(this, arguments);
        this.proxy.sync();
    },

    // private
    onDragOver : function(e, id){
        var target = this.cachedTarget || Ext.dd.DragDropMgr.getDDById(id);
        if(this.beforeDragOver(target, e, id) !== false){
            if(target.isNotifyTarget){
                var status = target.notifyOver(this, e, this.dragData);
                this.proxy.setStatus(status);
            }

            if(this.afterDragOver){
                /**
                 * An empty function by default, but provided so that you can perform a custom action
                 * while the dragged item is over the drop target by providing an implementation.
                 * @param {Ext.dd.DragDrop} target The drop target
                 * @param {Event} e The event object
                 * @param {String} id The id of the dragged element
                 * @method afterDragOver
                 */
                this.afterDragOver(target, e, id);
            }
        }
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action
     * while the dragged item is over the drop target and optionally cancel the onDragOver.
     * @param {Ext.dd.DragDrop} target The drop target
     * @param {Event} e The event object
     * @param {String} id The id of the dragged element
     * @return {Boolean} isValid True if the drag event is valid, else false to cancel
     */
    beforeDragOver : function(target, e, id){
        return true;
    },

    // private
    onDragOut : function(e, id){
        var target = this.cachedTarget || Ext.dd.DragDropMgr.getDDById(id);
        if(this.beforeDragOut(target, e, id) !== false){
            if(target.isNotifyTarget){
                target.notifyOut(this, e, this.dragData);
            }
            this.proxy.reset();
            if(this.afterDragOut){
                /**
                 * An empty function by default, but provided so that you can perform a custom action
                 * after the dragged item is dragged out of the target without dropping.
                 * @param {Ext.dd.DragDrop} target The drop target
                 * @param {Event} e The event object
                 * @param {String} id The id of the dragged element
                 * @method afterDragOut
                 */
                this.afterDragOut(target, e, id);
            }
        }
        this.cachedTarget = null;
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action before the dragged
     * item is dragged out of the target without dropping, and optionally cancel the onDragOut.
     * @param {Ext.dd.DragDrop} target The drop target
     * @param {Event} e The event object
     * @param {String} id The id of the dragged element
     * @return {Boolean} isValid True if the drag event is valid, else false to cancel
     */
    beforeDragOut : function(target, e, id){
        return true;
    },
    
    // private
    onDragDrop : function(e, id){
        var target = this.cachedTarget || Ext.dd.DragDropMgr.getDDById(id);
        if(this.beforeDragDrop(target, e, id) !== false){
            if(target.isNotifyTarget){
                if(target.notifyDrop(this, e, this.dragData)){ // valid drop?
                    this.onValidDrop(target, e, id);
                }else{
                    this.onInvalidDrop(target, e, id);
                }
            }else{
                this.onValidDrop(target, e, id);
            }
            
            if(this.afterDragDrop){
                /**
                 * An empty function by default, but provided so that you can perform a custom action
                 * after a valid drag drop has occurred by providing an implementation.
                 * @param {Ext.dd.DragDrop} target The drop target
                 * @param {Event} e The event object
                 * @param {String} id The id of the dropped element
                 * @method afterDragDrop
                 */
                this.afterDragDrop(target, e, id);
            }
        }
        delete this.cachedTarget;
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action before the dragged
     * item is dropped onto the target and optionally cancel the onDragDrop.
     * @param {Ext.dd.DragDrop} target The drop target
     * @param {Event} e The event object
     * @param {String} id The id of the dragged element
     * @return {Boolean} isValid True if the drag drop event is valid, else false to cancel
     */
    beforeDragDrop : function(target, e, id){
        return true;
    },

    // private
    onValidDrop : function(target, e, id){
        this.hideProxy();
        if(this.afterValidDrop){
            /**
             * An empty function by default, but provided so that you can perform a custom action
             * after a valid drop has occurred by providing an implementation.
             * @param {Object} target The target DD 
             * @param {Event} e The event object
             * @param {String} id The id of the dropped element
             * @method afterInvalidDrop
             */
            this.afterValidDrop(target, e, id);
        }
    },

    // private
    getRepairXY : function(e, data){
        return this.el.getXY();  
    },

    // private
    onInvalidDrop : function(target, e, id){
        this.beforeInvalidDrop(target, e, id);
        if(this.cachedTarget){
            if(this.cachedTarget.isNotifyTarget){
                this.cachedTarget.notifyOut(this, e, this.dragData);
            }
            this.cacheTarget = null;
        }
        this.proxy.repair(this.getRepairXY(e, this.dragData), this.afterRepair, this);

        if(this.afterInvalidDrop){
            /**
             * An empty function by default, but provided so that you can perform a custom action
             * after an invalid drop has occurred by providing an implementation.
             * @param {Event} e The event object
             * @param {String} id The id of the dropped element
             * @method afterInvalidDrop
             */
            this.afterInvalidDrop(e, id);
        }
    },

    // private
    afterRepair : function(){
        if(Ext.enableFx){
            this.el.highlight(this.hlColor || "c3daf9");
        }
        this.dragging = false;
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action after an invalid
     * drop has occurred.
     * @param {Ext.dd.DragDrop} target The drop target
     * @param {Event} e The event object
     * @param {String} id The id of the dragged element
     * @return {Boolean} isValid True if the invalid drop should proceed, else false to cancel
     */
    beforeInvalidDrop : function(target, e, id){
        return true;
    },

    // private
    handleMouseDown : function(e){
        if(this.dragging) {
            return;
        }
        var data = this.getDragData(e);
        if(data && this.onBeforeDrag(data, e) !== false){
            this.dragData = data;
            this.proxy.stop();
            Ext.dd.DragSource.superclass.handleMouseDown.apply(this, arguments);
        } 
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action before the initial
     * drag event begins and optionally cancel it.
     * @param {Object} data An object containing arbitrary data to be shared with drop targets
     * @param {Event} e The event object
     * @return {Boolean} isValid True if the drag event is valid, else false to cancel
     */
    onBeforeDrag : function(data, e){
        return true;
    },

    /**
     * An empty function by default, but provided so that you can perform a custom action once the initial
     * drag event has begun.  The drag cannot be canceled from this function.
     * @param {Number} x The x position of the click on the dragged object
     * @param {Number} y The y position of the click on the dragged object
     */
    onStartDrag : Ext.emptyFn,

    // private - YUI override
    startDrag : function(x, y){
        this.proxy.reset();
        this.dragging = true;
        this.proxy.update("");
        this.onInitDrag(x, y);
        this.proxy.show();
    },

    // private
    onInitDrag : function(x, y){
        var clone = this.el.dom.cloneNode(true);
        clone.id = Ext.id(); // prevent duplicate ids
        this.proxy.update(clone);
        this.onStartDrag(x, y);
        return true;
    },

    /**
     * Returns the drag source's underlying {@link Ext.dd.StatusProxy}
     * @return {Ext.dd.StatusProxy} proxy The StatusProxy
     */
    getProxy : function(){
        return this.proxy;  
    },

    /**
     * Hides the drag source's {@link Ext.dd.StatusProxy}
     */
    hideProxy : function(){
        this.proxy.hide();  
        this.proxy.reset(true);
        this.dragging = false;
    },

    // private
    triggerCacheRefresh : function(){
        Ext.dd.DDM.refreshCache(this.groups);
    },

    // private - override to prevent hiding
    b4EndDrag: function(e) {
    },

    // private - override to prevent moving
    endDrag : function(e){
        this.onEndDrag(this.dragData, e);
    },

    // private
    onEndDrag : function(data, e){
    },
    
    // private - pin to cursor
    autoOffset : function(x, y) {
        this.setDelta(-12, -20);
    }    
});



/**
 * @class Ext.dd.DropTarget
 * @extends Ext.dd.DDTarget
 * A simple class that provides the basic implementation needed to make any element a drop target that can have
 * draggable items dropped onto it.  The drop has no effect until an implementation of notifyDrop is provided.
 * @constructor
 * @param {String/HTMLElement/Element} el The container element
 * @param {Object} config
 */
Ext.dd.DropTarget = function(el, config){
    this.el = Ext.get(el);
    
    Ext.apply(this, config);
    
    if(this.containerScroll){
        Ext.dd.ScrollManager.register(this.el);
    }
    
    Ext.dd.DropTarget.superclass.constructor.call(this, this.el.dom, this.ddGroup || this.group, 
          {isTarget: true});

};

Ext.extend(Ext.dd.DropTarget, Ext.dd.DDTarget, {
    /**
     * @cfg {String} overClass
     * The CSS class applied to the drop target element while the drag source is over it (defaults to "").
     */
    /**
     * @cfg {String} dropAllowed
     * The CSS class returned to the drag source when drop is allowed (defaults to "x-dd-drop-ok").
     */
    dropAllowed : "x-dd-drop-ok",
    /**
     * @cfg {String} dropNotAllowed
     * The CSS class returned to the drag source when drop is not allowed (defaults to "x-dd-drop-nodrop").
     */
    dropNotAllowed : "x-dd-drop-nodrop",

    // private
    isTarget : true,

    // private
    isNotifyTarget : true,

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop target that the source is now over the
     * target.  This default implementation adds the CSS class specified by overClass (if any) to the drop element
     * and returns the dropAllowed config value.  This method should be overridden if drop validation is required.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    notifyEnter : function(dd, e, data){
        if(this.overClass){
            this.el.addClass(this.overClass);
        }
        return this.dropAllowed;
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls continuously while it is being dragged over the target.
     * This method will be called on every mouse movement while the drag source is over the drop target.
     * This default implementation simply returns the dropAllowed config value.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    notifyOver : function(dd, e, data){
        return this.dropAllowed;
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop target that the source has been dragged
     * out of the target without dropping.  This default implementation simply removes the CSS class specified by
     * overClass (if any) from the drop element.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     */
    notifyOut : function(dd, e, data){
        if(this.overClass){
            this.el.removeClass(this.overClass);
        }
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop target that the dragged item has
     * been dropped on it.  This method has no default implementation and returns false, so you must provide an
     * implementation that does something to process the drop event and returns true so that the drag source's
     * repair action does not run.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {Boolean} True if the drop was valid, else false
     */
    notifyDrop : function(dd, e, data){
        return false;
    }
});



/**
 * @class Ext.dd.DragZone
 * @extends Ext.dd.DragSource
 * This class provides a container DD instance that proxies for multiple child node sources.<br />
 * By default, this class requires that draggable child nodes are registered with {@link Ext.dd.Registry}.
 * @constructor
 * @param {String/HTMLElement/Element} el The container element
 * @param {Object} config
 */
Ext.dd.DragZone = function(el, config){
    Ext.dd.DragZone.superclass.constructor.call(this, el, config);
    if(this.containerScroll){
        Ext.dd.ScrollManager.register(this.el);
    }
};

Ext.extend(Ext.dd.DragZone, Ext.dd.DragSource, {
    /**
     * @cfg {Boolean} containerScroll True to register this container with the Scrollmanager
     * for auto scrolling during drag operations.
     */
    /**
     * @cfg {String} hlColor The color to use when visually highlighting the drag source in the afterRepair
     * method after a failed drop (defaults to "c3daf9" - light blue)
     */

    /**
     * Called when a mousedown occurs in this container. Looks in {@link Ext.dd.Registry}
     * for a valid target to drag based on the mouse down. Override this method
     * to provide your own lookup logic (e.g. finding a child by class name). Make sure your returned
     * object has a "ddel" attribute (with an HTML Element) for other functions to work.
     * @param {EventObject} e The mouse down event
     * @return {Object} The dragData
     */
    getDragData : function(e){
        return Ext.dd.Registry.getHandleFromEvent(e);
    },
    
    /**
     * Called once drag threshold has been reached to initialize the proxy element. By default, it clones the
     * this.dragData.ddel
     * @param {Number} x The x position of the click on the dragged object
     * @param {Number} y The y position of the click on the dragged object
     * @return {Boolean} true to continue the drag, false to cancel
     */
    onInitDrag : function(x, y){
        this.proxy.update(this.dragData.ddel.cloneNode(true));
        this.onStartDrag(x, y);
        return true;
    },
    
    /**
     * Called after a repair of an invalid drop. By default, highlights this.dragData.ddel 
     */
    afterRepair : function(){
        if(Ext.enableFx){
            Ext.Element.fly(this.dragData.ddel).highlight(this.hlColor || "c3daf9");
        }
        this.dragging = false;
    },

    /**
     * Called before a repair of an invalid drop to get the XY to animate to. By default returns
     * the XY of this.dragData.ddel
     * @param {EventObject} e The mouse up event
     * @return {Array} The xy location (e.g. [100, 200])
     */
    getRepairXY : function(e){
        return Ext.Element.fly(this.dragData.ddel).getXY();  
    }
});



/**
 * @class Ext.dd.DropZone
 * @extends Ext.dd.DropTarget
 * This class provides a container DD instance that proxies for multiple child node targets.<br />
 * By default, this class requires that child nodes accepting drop are registered with {@link Ext.dd.Registry}.
 * @constructor
 * @param {String/HTMLElement/Element} el The container element
 * @param {Object} config
 */
Ext.dd.DropZone = function(el, config){
    Ext.dd.DropZone.superclass.constructor.call(this, el, config);
};

Ext.extend(Ext.dd.DropZone, Ext.dd.DropTarget, {
    /**
     * Returns a custom data object associated with the DOM node that is the target of the event.  By default
     * this looks up the event target in the {@link Ext.dd.Registry}, although you can override this method to
     * provide your own custom lookup.
     * @param {Event} e The event
     * @return {Object} data The custom data
     */
    getTargetFromEvent : function(e){
        return Ext.dd.Registry.getTargetFromEvent(e);
    },

    /**
     * Called internally when the DropZone determines that a {@link Ext.dd.DragSource} has entered a drop node
     * that it has registered.  This method has no default implementation and should be overridden to provide
     * node-specific processing if necessary.
     * @param {Object} nodeData The custom data associated with the drop node (this is the same value returned from 
     * {@link #getTargetFromEvent} for this node)
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     */
    onNodeEnter : function(n, dd, e, data){
        
    },

    /**
     * Called internally while the DropZone determines that a {@link Ext.dd.DragSource} is over a drop node
     * that it has registered.  The default implementation returns this.dropNotAllowed, so it should be
     * overridden to provide the proper feedback.
     * @param {Object} nodeData The custom data associated with the drop node (this is the same value returned from
     * {@link #getTargetFromEvent} for this node)
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    onNodeOver : function(n, dd, e, data){
        return this.dropAllowed;
    },

    /**
     * Called internally when the DropZone determines that a {@link Ext.dd.DragSource} has been dragged out of
     * the drop node without dropping.  This method has no default implementation and should be overridden to provide
     * node-specific processing if necessary.
     * @param {Object} nodeData The custom data associated with the drop node (this is the same value returned from
     * {@link #getTargetFromEvent} for this node)
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     */
    onNodeOut : function(n, dd, e, data){
        
    },

    /**
     * Called internally when the DropZone determines that a {@link Ext.dd.DragSource} has been dropped onto
     * the drop node.  The default implementation returns false, so it should be overridden to provide the
     * appropriate processing of the drop event and return true so that the drag source's repair action does not run.
     * @param {Object} nodeData The custom data associated with the drop node (this is the same value returned from
     * {@link #getTargetFromEvent} for this node)
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {Boolean} True if the drop was valid, else false
     */
    onNodeDrop : function(n, dd, e, data){
        return false;
    },

    /**
     * Called internally while the DropZone determines that a {@link Ext.dd.DragSource} is being dragged over it,
     * but not over any of its registered drop nodes.  The default implementation returns this.dropNotAllowed, so
     * it should be overridden to provide the proper feedback if necessary.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    onContainerOver : function(dd, e, data){
        return this.dropNotAllowed;
    },

    /**
     * Called internally when the DropZone determines that a {@link Ext.dd.DragSource} has been dropped on it,
     * but not on any of its registered drop nodes.  The default implementation returns false, so it should be
     * overridden to provide the appropriate processing of the drop event if you need the drop zone itself to
     * be able to accept drops.  It should return true when valid so that the drag source's repair action does not run.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {Boolean} True if the drop was valid, else false
     */
    onContainerDrop : function(dd, e, data){
        return false;
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop zone that the source is now over
     * the zone.  The default implementation returns this.dropNotAllowed and expects that only registered drop
     * nodes can process drag drop operations, so if you need the drop zone itself to be able to process drops
     * you should override this method and provide a custom implementation.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    notifyEnter : function(dd, e, data){
        return this.dropNotAllowed;
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls continuously while it is being dragged over the drop zone.
     * This method will be called on every mouse movement while the drag source is over the drop zone.
     * It will call {@link #onNodeOver} while the drag source is over a registered node, and will also automatically
     * delegate to the appropriate node-specific methods as necessary when the drag source enters and exits
     * registered nodes ({@link #onNodeEnter}, {@link #onNodeOut}). If the drag source is not currently over a
     * registered node, it will call {@link #onContainerOver}.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    notifyOver : function(dd, e, data){
        var n = this.getTargetFromEvent(e);
        if(!n){ // not over valid drop target
            if(this.lastOverNode){
                this.onNodeOut(this.lastOverNode, dd, e, data);
                this.lastOverNode = null;
            }
            return this.onContainerOver(dd, e, data);
        }
        if(this.lastOverNode != n){
            if(this.lastOverNode){
                this.onNodeOut(this.lastOverNode, dd, e, data);
            }
            this.onNodeEnter(n, dd, e, data);
            this.lastOverNode = n;
        }
        return this.onNodeOver(n, dd, e, data);
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop zone that the source has been dragged
     * out of the zone without dropping.  If the drag source is currently over a registered node, the notification
     * will be delegated to {@link #onNodeOut} for node-specific handling, otherwise it will be ignored.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag zone
     */
    notifyOut : function(dd, e, data){
        if(this.lastOverNode){
            this.onNodeOut(this.lastOverNode, dd, e, data);
            this.lastOverNode = null;
        }
    },

    /**
     * The function a {@link Ext.dd.DragSource} calls once to notify this drop zone that the dragged item has
     * been dropped on it.  The drag zone will look up the target node based on the event passed in, and if there
     * is a node registered for that event, it will delegate to {@link #onNodeDrop} for node-specific handling,
     * otherwise it will call {@link #onContainerDrop}.
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop zone
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {Boolean} True if the drop was valid, else false
     */
    notifyDrop : function(dd, e, data){
        if(this.lastOverNode){
            this.onNodeOut(this.lastOverNode, dd, e, data);
            this.lastOverNode = null;
        }
        var n = this.getTargetFromEvent(e);
        return n ?
            this.onNodeDrop(n, dd, e, data) :
            this.onContainerDrop(dd, e, data);
    },

    // private
    triggerCacheRefresh : function(){
        Ext.dd.DDM.refreshCache(this.groups);
    }  
});



/**
 * @class Ext.data.Connection
 * @extends Ext.util.Observable
 * The class encapsulates a connection to the page's originating domain, allowing requests to be made
 * either to a configured URL, or to a URL specified at request time.<br><br>
 * <p>
 * Requests made by this class are asynchronous, and will return immediately. No data from
 * the server will be available to the statement immediately following the {@link #request} call.
 * To process returned data, use a callback in the request options object, or an event listener.</p><br>
 * <p>
 * Note: If you are doing a file upload, you will not get a normal response object sent back to
 * your callback or event handler.  Since the upload is handled via in IFRAME, there is no XMLHttpRequest.
 * The response object is created using the innerHTML of the IFRAME's document as the responseText
 * property and, if present, the IFRAME's XML document as the responseXML property.</p><br>
 * This means that a valid XML or HTML document must be returned. If JSON data is required, it is suggested
 * that it be placed either inside a &lt;textarea> in an HTML document and retrieved from the responseText
 * using a regex, or inside a CDATA section in an XML document and retrieved from the responseXML using
 * standard DOM methods.
 * @constructor
 * @param {Object} config a configuration object.
 */
Ext.data.Connection = function(config){
    Ext.apply(this, config);
    this.addEvents({
        /**
         * @event beforerequest
         * Fires before a network request is made to retrieve a data object.
         * @param {Connection} conn This Connection object.
         * @param {Object} options The options config object passed to the {@link #request} method.
         */
        "beforerequest" : true,
        /**
         * @event requestcomplete
         * Fires if the request was successfully completed.
         * @param {Connection} conn This Connection object.
         * @param {Object} response The XHR object containing the response data.
         * See {@link http://www.w3.org/TR/XMLHttpRequest/} for details.
         * @param {Object} options The options config object passed to the {@link #request} method.
         */
        "requestcomplete" : true,
        /**
         * @event requestexception
         * Fires if an error HTTP status was returned from the server.
         * See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html} for details of HTTP status codes.
         * @param {Connection} conn This Connection object.
         * @param {Object} response The XHR object containing the response data.
         * See {@link http://www.w3.org/TR/XMLHttpRequest/} for details.
         * @param {Object} options The options config object passed to the {@link #request} method.
         */
        "requestexception" : true
    });
    Ext.data.Connection.superclass.constructor.call(this);
};

Ext.extend(Ext.data.Connection, Ext.util.Observable, {
    /**
     * @cfg {String} url (Optional) The default URL to be used for requests to the server. (defaults to undefined)
     */
    /**
     * @cfg {Object} extraParams (Optional) An object containing properties which are used as
     * extra parameters to each request made by this object. (defaults to undefined)
     */
    /**
     * @cfg {Object} defaultHeaders (Optional) An object containing request headers which are added
     *  to each request made by this object. (defaults to undefined)
     */
    /**
     * @cfg {String} method (Optional) The default HTTP method to be used for requests. (defaults to undefined; if not set but parms are present will use POST, otherwise GET)
     */
    /**
     * @cfg {Number} timeout (Optional) The timeout in milliseconds to be used for requests. (defaults to 30000)
     */
    timeout : 30000,
    /**
     * @cfg {Boolean} autoAbort (Optional) Whether this request should abort any pending requests. (defaults to false)
     * @type Boolean
     */
    autoAbort:false,

    /**
     * @cfg {Boolean} disableCaching (Optional) True to add a unique cache-buster param to GET requests. (defaults to true)
     * @type Boolean
     */
    disableCaching: true,

    /**
     * Sends an HTTP request to a remote server.
     * @param {Object} options An object which may contain the following properties:<ul>
     * <li><b>url</b> {String} (Optional) The URL to which to send the request. Defaults to configured URL</li>
     * <li><b>params</b> {Object/String/Function} (Optional) An object containing properties which are used as parameters to the
     * request, a url encoded string or a function to call to get either.</li>
     * <li><b>method</b> {String} (Optional) The HTTP method to use for the request. Defaults to the configured method, or
     * if no method was configured, "GET" if no parameters are being sent, and "POST" if parameters are being sent.</li>
     * <li><b>callback</b> {Function} (Optional) The function to be called upon receipt of the HTTP response.
     * The callback is called regardless of success or failure and is passed the following parameters:<ul>
     * <li>options {Object} The parameter to the request call.</li>
     * <li>success {Boolean} True if the request succeeded.</li>
     * <li>response {Object} The XMLHttpRequest object containing the response data.</li>
     * </ul></li>
     * <li><b>success</b> {Function} (Optional) The function to be called upon success of the request.
     * The callback is passed the following parameters:<ul>
     * <li>response {Object} The XMLHttpRequest object containing the response data.</li>
     * <li>options {Object} The parameter to the request call.</li>
     * </ul></li>
     * <li><b>failure</b> {Function} (Optional) The function to be called upon failure of the request.
     * The callback is passed the following parameters:<ul>
     * <li>response {Object} The XMLHttpRequest object containing the response data.</li>
     * <li>options {Object} The parameter to the request call.</li>
     * </ul></li>
     * <li><b>scope</b> {Object} (Optional) The scope in which to execute the callbacks: The "this" object
     * for the callback function. Defaults to the browser window.</li>
     * <li><b>form</b> {Object/String} (Optional) A form object or id to pull parameters from.</li>
     * <li><b>isUpload</b> {Boolean} (Optional) True if the form object is a file upload (will usually be automatically detected).</li>
     * <li><b>headers</b> {Object} (Optional) Request headers to set for the request.</li>
     * <li><b>xmlData</b> {Object} (Optional) XML document to use for the post. Note: This will be used instead of
     * params for the post data. Any params will be appended to the URL.</li>
     * <li><b>disableCaching</b> {Boolean} (Optional) True to add a unique cache-buster param to GET requests.</li>
     * </ul>
     * @return {Number} transactionId
     */
    request : function(o){
        if(this.fireEvent("beforerequest", this, o) !== false){
            var p = o.params;

            if(typeof p == "function"){
                p = p.call(o.scope||window, o);
            }
            if(typeof p == "object"){
                p = Ext.urlEncode(o.params);
            }
            if(this.extraParams){
                var extras = Ext.urlEncode(this.extraParams);
                p = p ? (p + '&' + extras) : extras;
            }

            var url = o.url || this.url;
            if(typeof url == 'function'){
                url = url.call(o.scope||window, o);
            }

            if(o.form){
                var form = Ext.getDom(o.form);
                url = url || form.action;

                var enctype = form.getAttribute("enctype");
                if(o.isUpload || (enctype && enctype.toLowerCase() == 'multipart/form-data')){
                    return this.doFormUpload(o, p, url);
                }
                var f = Ext.lib.Ajax.serializeForm(form);
                p = p ? (p + '&' + f) : f;
            }

            var hs = o.headers;
            if(this.defaultHeaders){
                hs = Ext.apply(hs || {}, this.defaultHeaders);
                if(!o.headers){
                    o.headers = hs;
                }
            }

            var cb = {
                success: this.handleResponse,
                failure: this.handleFailure,
                scope: this,
                argument: {options: o},
                timeout : this.timeout
            };

            var method = o.method||this.method||(p ? "POST" : "GET");

            if(method == 'GET' && (this.disableCaching && o.disableCaching !== false) || o.disableCaching === true){
                url += (url.indexOf('?') != -1 ? '&' : '?') + '_dc=' + (new Date().getTime());
            }

            if(typeof o.autoAbort == 'boolean'){ // options gets top priority
                if(o.autoAbort){
                    this.abort();
                }
            }else if(this.autoAbort !== false){
                this.abort();
            }

            if((method == 'GET' && p) || o.xmlData){
                url += (url.indexOf('?') != -1 ? '&' : '?') + p;
                p = '';
            }
            this.transId = Ext.lib.Ajax.request(method, url, cb, p, o);
            return this.transId;
        }else{
            Ext.callback(o.callback, o.scope, [o, null, null]);
            return null;
        }
    },

    /**
     * Determine whether this object has a request outstanding.
     * @param {Number} transactionId (Optional) defaults to the last transaction
     * @return {Boolean} True if there is an outstanding request.
     */
    isLoading : function(transId){
        if(transId){
            return Ext.lib.Ajax.isCallInProgress(transId);
        }else{
            return this.transId ? true : false;
        }
    },

    /**
     * Aborts any outstanding request.
     * @param {Number} transactionId (Optional) defaults to the last transaction
     */
    abort : function(transId){
        if(transId || this.isLoading()){
            Ext.lib.Ajax.abort(transId || this.transId);
        }
    },

    // private
    handleResponse : function(response){
        this.transId = false;
        var options = response.argument.options;
        response.argument = options ? options.argument : null;
        this.fireEvent("requestcomplete", this, response, options);
        Ext.callback(options.success, options.scope, [response, options]);
        Ext.callback(options.callback, options.scope, [options, true, response]);
    },

    // private
    handleFailure : function(response, e){
        this.transId = false;
        var options = response.argument.options;
        response.argument = options ? options.argument : null;
        this.fireEvent("requestexception", this, response, options, e);
        Ext.callback(options.failure, options.scope, [response, options]);
        Ext.callback(options.callback, options.scope, [options, false, response]);
    },

    // private
    doFormUpload : function(o, ps, url){
        var id = Ext.id();
        var frame = document.createElement('iframe');
        frame.id = id;
        frame.name = id;
        frame.className = 'x-hidden';
        if(Ext.isIE){
            frame.src = Ext.SSL_SECURE_URL;
        }
        document.body.appendChild(frame);

        if(Ext.isIE){
           document.frames[id].name = id;
        }

        var form = Ext.getDom(o.form);
        form.target = id;
        form.method = 'POST';
        form.enctype = form.encoding = 'multipart/form-data';
        if(url){
            form.action = url;
        }

        var hiddens, hd;
        if(ps){ // add dynamic params
            hiddens = [];
            ps = Ext.urlDecode(ps, false);
            for(var k in ps){
                if(ps.hasOwnProperty(k)){
                    hd = document.createElement('input');
                    hd.type = 'hidden';
                    hd.name = k;
                    hd.value = ps[k];
                    form.appendChild(hd);
                    hiddens.push(hd);
                }
            }
        }

        function cb(){
            var r = {  // bogus response object
                responseText : '',
                responseXML : null
            };

            r.argument = o ? o.argument : null;

            try { //
                var doc;
                if(Ext.isIE){
                    doc = frame.contentWindow.document;
                }else {
                    doc = (frame.contentDocument || window.frames[id].document);
                }
                if(doc && doc.body){
                    r.responseText = doc.body.innerHTML;
                }
                if(doc && doc.XMLDocument){
                    r.responseXML = doc.XMLDocument;
                }else {
                    r.responseXML = doc;
                }
            }
            catch(e) {
                // ignore
            }

            Ext.EventManager.removeListener(frame, 'load', cb, this);

            this.fireEvent("requestcomplete", this, r, o);
            Ext.callback(o.success, o.scope, [r, o]);
            Ext.callback(o.callback, o.scope, [o, true, r]);

            setTimeout(function(){document.body.removeChild(frame);}, 100);
        }

        Ext.EventManager.on(frame, 'load', cb, this);
        form.submit();

        if(hiddens){ // remove dynamic params
            for(var i = 0, len = hiddens.length; i < len; i++){
                form.removeChild(hiddens[i]);
            }
        }
    }
});

/**
 * @class Ext.Ajax
 * @extends Ext.data.Connection
 * Global Ajax request class.
 *
 * @singleton
 */
Ext.Ajax = new Ext.data.Connection({
    // fix up the docs
   /**
     * @cfg {String} url @hide
     */
    /**
     * @cfg {Object} extraParams @hide
     */
    /**
     * @cfg {Object} defaultHeaders @hide
     */
    /**
     * @cfg {String} method (Optional) @hide
     */
    /**
     * @cfg {Number} timeout (Optional) @hide
     */
    /**
     * @cfg {Boolean} autoAbort (Optional) @hide
     */

    /**
     * @cfg {Boolean} disableCaching (Optional) @hide
     */

    /**
     * @property  disableCaching
     * True to add a unique cache-buster param to GET requests. (defaults to true)
     * @type Boolean
     */
    /**
     * @property  url
     * The default URL to be used for requests to the server. (defaults to undefined)
     * @type String
     */
    /**
     * @property  extraParams
     * An object containing properties which are used as
     * extra parameters to each request made by this object. (defaults to undefined)
     * @type Object
     */
    /**
     * @property  defaultHeaders
     * An object containing request headers which are added to each request made by this object. (defaults to undefined)
     * @type Object
     */
    /**
     * @property  method
     * The default HTTP method to be used for requests. (defaults to undefined; if not set but parms are present will use POST, otherwise GET)
     * @type String
     */
    /**
     * @property  timeout
     * The timeout in milliseconds to be used for requests. (defaults to 30000)
     * @type Number
     */

    /**
     * @property  autoAbort
     * Whether a new request should abort any pending requests. (defaults to false)
     * @type Boolean
     */
    autoAbort : false,

    /**
     * Serialize the passed form into a url encoded string
     * @param {String/HTMLElement} form
     * @return {String}
     */
    serializeForm : function(form){
        return Ext.lib.Ajax.serializeForm(form);
    }
});



/**
 * @class Ext.data.Tree
 * @extends Ext.util.Observable
 * Represents a tree data structure and bubbles all the events for its nodes. The nodes
 * in the tree have most standard DOM functionality.
 * @constructor
 * @param {Node} root (optional) The root node
 */
Ext.data.Tree = function(root){
   this.nodeHash = {};
   /**
    * The root node for this tree
    * @type Node
    */
   this.root = null;
   if(root){
       this.setRootNode(root);
   }
   this.addEvents({
       /**
        * @event append
        * Fires when a new child node is appended to a node in this tree.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The newly appended node
        * @param {Number} index The index of the newly appended node
        */
       "append" : true,
       /**
        * @event remove
        * Fires when a child node is removed from a node in this tree.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The child node removed
        */
       "remove" : true,
       /**
        * @event move
        * Fires when a node is moved to a new location in the tree
        * @param {Tree} tree The owner tree
        * @param {Node} node The node moved
        * @param {Node} oldParent The old parent of this node
        * @param {Node} newParent The new parent of this node
        * @param {Number} index The index it was moved to
        */
       "move" : true,
       /**
        * @event insert
        * Fires when a new child node is inserted in a node in this tree.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The child node inserted
        * @param {Node} refNode The child node the node was inserted before
        */
       "insert" : true,
       /**
        * @event beforeappend
        * Fires before a new child is appended to a node in this tree, return false to cancel the append.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The child node to be appended
        */
       "beforeappend" : true,
       /**
        * @event beforeremove
        * Fires before a child is removed from a node in this tree, return false to cancel the remove.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The child node to be removed
        */
       "beforeremove" : true,
       /**
        * @event beforemove
        * Fires before a node is moved to a new location in the tree. Return false to cancel the move.
        * @param {Tree} tree The owner tree
        * @param {Node} node The node being moved
        * @param {Node} oldParent The parent of the node
        * @param {Node} newParent The new parent the node is moving to
        * @param {Number} index The index it is being moved to
        */
       "beforemove" : true,
       /**
        * @event beforeinsert
        * Fires before a new child is inserted in a node in this tree, return false to cancel the insert.
        * @param {Tree} tree The owner tree
        * @param {Node} parent The parent node
        * @param {Node} node The child node to be inserted
        * @param {Node} refNode The child node the node is being inserted before
        */
       "beforeinsert" : true
   });

    Ext.data.Tree.superclass.constructor.call(this);
};

Ext.extend(Ext.data.Tree, Ext.util.Observable, {
    pathSeparator: "/",

    proxyNodeEvent : function(){
        return this.fireEvent.apply(this, arguments);
    },

    /**
     * Returns the root node for this tree.
     * @return {Node}
     */
    getRootNode : function(){
        return this.root;
    },

    /**
     * Sets the root node for this tree.
     * @param {Node} node
     * @return {Node}
     */
    setRootNode : function(node){
        this.root = node;
        node.ownerTree = this;
        node.isRoot = true;
        this.registerNode(node);
        return node;
    },

    /**
     * Gets a node in this tree by its id.
     * @param {String} id
     * @return {Node}
     */
    getNodeById : function(id){
        return this.nodeHash[id];
    },

    registerNode : function(node){
        this.nodeHash[node.id] = node;
    },

    unregisterNode : function(node){
        delete this.nodeHash[node.id];
    },

    toString : function(){
        return "[Tree"+(this.id?" "+this.id:"")+"]";
    }
});

/**
 * @class Ext.data.Node
 * @extends Ext.util.Observable
 * @cfg {Boolean} leaf true if this node is a leaf and does not have children
 * @cfg {String} id The id for this node. If one is not specified, one is generated.
 * @constructor
 * @param {Object} attributes The attributes/config for the node
 */
Ext.data.Node = function(attributes){
    /**
     * The attributes supplied for the node. You can use this property to access any custom attributes you supplied.
     * @type {Object}
     */
    this.attributes = attributes || {};
    this.leaf = this.attributes.leaf;
    /**
     * The node id. @type String
     */
    this.id = this.attributes.id;
    if(!this.id){
        this.id = Ext.id(null, "ynode-");
        this.attributes.id = this.id;
    }
    /**
     * All child nodes of this node. @type Array
     */
    this.childNodes = [];
    if(!this.childNodes.indexOf){ // indexOf is a must
        this.childNodes.indexOf = function(o){
            for(var i = 0, len = this.length; i < len; i++){
                if(this[i] == o) return i;
            }
            return -1;
        };
    }
    /**
     * The parent node for this node. @type Node
     */
    this.parentNode = null;
    /**
     * The first direct child node of this node, or null if this node has no child nodes. @type Node
     */
    this.firstChild = null;
    /**
     * The last direct child node of this node, or null if this node has no child nodes. @type Node
     */
    this.lastChild = null;
    /**
     * The node immediately preceding this node in the tree, or null if there is no sibling node. @type Node
     */
    this.previousSibling = null;
    /**
     * The node immediately following this node in the tree, or null if there is no sibling node. @type Node
     */
    this.nextSibling = null;

    this.addEvents({
       /**
        * @event append
        * Fires when a new child node is appended
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The newly appended node
        * @param {Number} index The index of the newly appended node
        */
       "append" : true,
       /**
        * @event remove
        * Fires when a child node is removed
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The removed node
        */
       "remove" : true,
       /**
        * @event move
        * Fires when this node is moved to a new location in the tree
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} oldParent The old parent of this node
        * @param {Node} newParent The new parent of this node
        * @param {Number} index The index it was moved to
        */
       "move" : true,
       /**
        * @event insert
        * Fires when a new child node is inserted.
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The child node inserted
        * @param {Node} refNode The child node the node was inserted before
        */
       "insert" : true,
       /**
        * @event beforeappend
        * Fires before a new child is appended, return false to cancel the append.
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The child node to be appended
        */
       "beforeappend" : true,
       /**
        * @event beforeremove
        * Fires before a child is removed, return false to cancel the remove.
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The child node to be removed
        */
       "beforeremove" : true,
       /**
        * @event beforemove
        * Fires before this node is moved to a new location in the tree. Return false to cancel the move.
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} oldParent The parent of this node
        * @param {Node} newParent The new parent this node is moving to
        * @param {Number} index The index it is being moved to
        */
       "beforemove" : true,
       /**
        * @event beforeinsert
        * Fires before a new child is inserted, return false to cancel the insert.
        * @param {Tree} tree The owner tree
        * @param {Node} this This node
        * @param {Node} node The child node to be inserted
        * @param {Node} refNode The child node the node is being inserted before
        */
       "beforeinsert" : true
   });
    this.listeners = this.attributes.listeners;
    Ext.data.Node.superclass.constructor.call(this);
};

Ext.extend(Ext.data.Node, Ext.util.Observable, {
    fireEvent : function(evtName){
        // first do standard event for this node
        if(Ext.data.Node.superclass.fireEvent.apply(this, arguments) === false){
            return false;
        }
        // then bubble it up to the tree if the event wasn't cancelled
        var ot = this.getOwnerTree();
        if(ot){
            if(ot.proxyNodeEvent.apply(ot, arguments) === false){
                return false;
            }
        }
        return true;
    },

    /**
     * Returns true if this node is a leaf
     * @return {Boolean}
     */
    isLeaf : function(){
        return this.leaf === true;
    },

    // private
    setFirstChild : function(node){
        this.firstChild = node;
    },

    //private
    setLastChild : function(node){
        this.lastChild = node;
    },


    /**
     * Returns true if this node is the last child of its parent
     * @return {Boolean}
     */
    isLast : function(){
       return (!this.parentNode ? true : this.parentNode.lastChild == this);
    },

    /**
     * Returns true if this node is the first child of its parent
     * @return {Boolean}
     */
    isFirst : function(){
       return (!this.parentNode ? true : this.parentNode.firstChild == this);
    },

    hasChildNodes : function(){
        return !this.isLeaf() && this.childNodes.length > 0;
    },

    /**
     * Insert node(s) as the last child node of this node.
     * @param {Node/Array} node The node or Array of nodes to append
     * @return {Node} The appended node if single append, or null if an array was passed
     */
    appendChild : function(node){
        var multi = false;
        if(node instanceof Array){
            multi = node;
        }else if(arguments.length > 1){
            multi = arguments;
        }
        // if passed an array or multiple args do them one by one
        if(multi){
            for(var i = 0, len = multi.length; i < len; i++) {
            	this.appendChild(multi[i]);
            }
        }else{
            if(this.fireEvent("beforeappend", this.ownerTree, this, node) === false){
                return false;
            }
            var index = this.childNodes.length;
            var oldParent = node.parentNode;
            // it's a move, make sure we move it cleanly
            if(oldParent){
                if(node.fireEvent("beforemove", node.getOwnerTree(), node, oldParent, this, index) === false){
                    return false;
                }
                oldParent.removeChild(node);
            }
            index = this.childNodes.length;
            if(index == 0){
                this.setFirstChild(node);
            }
            this.childNodes.push(node);
            node.parentNode = this;
            var ps = this.childNodes[index-1];
            if(ps){
                node.previousSibling = ps;
                ps.nextSibling = node;
            }else{
                node.previousSibling = null;
            }
            node.nextSibling = null;
            this.setLastChild(node);
            node.setOwnerTree(this.getOwnerTree());
            this.fireEvent("append", this.ownerTree, this, node, index);
            if(oldParent){
                node.fireEvent("move", this.ownerTree, node, oldParent, this, index);
            }
            return node;
        }
    },

    /**
     * Removes a child node from this node.
     * @param {Node} node The node to remove
     * @return {Node} The removed node
     */
    removeChild : function(node){
        var index = this.childNodes.indexOf(node);
        if(index == -1){
            return false;
        }
        if(this.fireEvent("beforeremove", this.ownerTree, this, node) === false){
            return false;
        }

        // remove it from childNodes collection
        this.childNodes.splice(index, 1);

        // update siblings
        if(node.previousSibling){
            node.previousSibling.nextSibling = node.nextSibling;
        }
        if(node.nextSibling){
            node.nextSibling.previousSibling = node.previousSibling;
        }

        // update child refs
        if(this.firstChild == node){
            this.setFirstChild(node.nextSibling);
        }
        if(this.lastChild == node){
            this.setLastChild(node.previousSibling);
        }

        node.setOwnerTree(null);
        // clear any references from the node
        node.parentNode = null;
        node.previousSibling = null;
        node.nextSibling = null;
        this.fireEvent("remove", this.ownerTree, this, node);
        return node;
    },

    /**
     * Inserts the first node before the second node in this nodes childNodes collection.
     * @param {Node} node The node to insert
     * @param {Node} refNode The node to insert before (if null the node is appended)
     * @return {Node} The inserted node
     */
    insertBefore : function(node, refNode){
        if(!refNode){ // like standard Dom, refNode can be null for append
            return this.appendChild(node);
        }
        // nothing to do
        if(node == refNode){
            return false;
        }

        if(this.fireEvent("beforeinsert", this.ownerTree, this, node, refNode) === false){
            return false;
        }
        var index = this.childNodes.indexOf(refNode);
        var oldParent = node.parentNode;
        var refIndex = index;

        // when moving internally, indexes will change after remove
        if(oldParent == this && this.childNodes.indexOf(node) < index){
            refIndex--;
        }

        // it's a move, make sure we move it cleanly
        if(oldParent){
            if(node.fireEvent("beforemove", node.getOwnerTree(), node, oldParent, this, index, refNode) === false){
                return false;
            }
            oldParent.removeChild(node);
        }
        if(refIndex == 0){
            this.setFirstChild(node);
        }
        this.childNodes.splice(refIndex, 0, node);
        node.parentNode = this;
        var ps = this.childNodes[refIndex-1];
        if(ps){
            node.previousSibling = ps;
            ps.nextSibling = node;
        }else{
            node.previousSibling = null;
        }
        node.nextSibling = refNode;
        refNode.previousSibling = node;
        node.setOwnerTree(this.getOwnerTree());
        this.fireEvent("insert", this.ownerTree, this, node, refNode);
        if(oldParent){
            node.fireEvent("move", this.ownerTree, node, oldParent, this, refIndex, refNode);
        }
        return node;
    },

    /**
     * Returns the child node at the specified index.
     * @param {Number} index
     * @return {Node}
     */
    item : function(index){
        return this.childNodes[index];
    },

    /**
     * Replaces one child node in this node with another.
     * @param {Node} newChild The replacement node
     * @param {Node} oldChild The node to replace
     * @return {Node} The replaced node
     */
    replaceChild : function(newChild, oldChild){
        this.insertBefore(newChild, oldChild);
        this.removeChild(oldChild);
        return oldChild;
    },

    /**
     * Returns the index of a child node
     * @param {Node} node
     * @return {Number} The index of the node or -1 if it was not found
     */
    indexOf : function(child){
        return this.childNodes.indexOf(child);
    },

    /**
     * Returns the tree this node is in.
     * @return {Tree}
     */
    getOwnerTree : function(){
        // if it doesn't have one, look for one
        if(!this.ownerTree){
            var p = this;
            while(p){
                if(p.ownerTree){
                    this.ownerTree = p.ownerTree;
                    break;
                }
                p = p.parentNode;
            }
        }
        return this.ownerTree;
    },

    /**
     * Returns depth of this node (the root node has a depth of 0)
     * @return {Number}
     */
    getDepth : function(){
        var depth = 0;
        var p = this;
        while(p.parentNode){
            ++depth;
            p = p.parentNode;
        }
        return depth;
    },

    // private
    setOwnerTree : function(tree){
        // if it's move, we need to update everyone
        if(tree != this.ownerTree){
            if(this.ownerTree){
                this.ownerTree.unregisterNode(this);
            }
            this.ownerTree = tree;
            var cs = this.childNodes;
            for(var i = 0, len = cs.length; i < len; i++) {
            	cs[i].setOwnerTree(tree);
            }
            if(tree){
                tree.registerNode(this);
            }
        }
    },

    /**
     * Returns the path for this node. The path can be used to expand or select this node programmatically.
     * @param {String} attr (optional) The attr to use for the path (defaults to the node's id)
     * @return {String} The path
     */
    getPath : function(attr){
        attr = attr || "id";
        var p = this.parentNode;
        var b = [this.attributes[attr]];
        while(p){
            b.unshift(p.attributes[attr]);
            p = p.parentNode;
        }
        var sep = this.getOwnerTree().pathSeparator;
        return sep + b.join(sep);
    },

    /**
     * Bubbles up the tree from this node, calling the specified function with each node. The scope (<i>this</i>) of
     * function call will be the scope provided or the current node. The arguments to the function
     * will be the args provided or the current node. If the function returns false at any point,
     * the bubble is stopped.
     * @param {Function} fn The function to call
     * @param {Object} scope (optional) The scope of the function (defaults to current node)
     * @param {Array} args (optional) The args to call the function with (default to passing the current node)
     */
    bubble : function(fn, scope, args){
        var p = this;
        while(p){
            if(fn.call(scope || p, args || p) === false){
                break;
            }
            p = p.parentNode;
        }
    },

    /**
     * Cascades down the tree from this node, calling the specified function with each node. The scope (<i>this</i>) of
     * function call will be the scope provided or the current node. The arguments to the function
     * will be the args provided or the current node. If the function returns false at any point,
     * the cascade is stopped on that branch.
     * @param {Function} fn The function to call
     * @param {Object} scope (optional) The scope of the function (defaults to current node)
     * @param {Array} args (optional) The args to call the function with (default to passing the current node)
     */
    cascade : function(fn, scope, args){
        if(fn.call(scope || this, args || this) !== false){
            var cs = this.childNodes;
            for(var i = 0, len = cs.length; i < len; i++) {
            	cs[i].cascade(fn, scope, args);
            }
        }
    },

    /**
     * Interates the child nodes of this node, calling the specified function with each node. The scope (<i>this</i>) of
     * function call will be the scope provided or the current node. The arguments to the function
     * will be the args provided or the current node. If the function returns false at any point,
     * the iteration stops.
     * @param {Function} fn The function to call
     * @param {Object} scope (optional) The scope of the function (defaults to current node)
     * @param {Array} args (optional) The args to call the function with (default to passing the current node)
     */
    eachChild : function(fn, scope, args){
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++) {
        	if(fn.call(scope || this, args || cs[i]) === false){
        	    break;
        	}
        }
    },

    /**
     * Finds the first child that has the attribute with the specified value.
     * @param {String} attribute The attribute name
     * @param {Mixed} value The value to search for
     * @return {Node} The found child or null if none was found
     */
    findChild : function(attribute, value){
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++) {
        	if(cs[i].attributes[attribute] == value){
        	    return cs[i];
        	}
        }
        return null;
    },

    /**
     * Finds the first child by a custom function. The child matches if the function passed
     * returns true.
     * @param {Function} fn
     * @param {Object} scope (optional)
     * @return {Node} The found child or null if none was found
     */
    findChildBy : function(fn, scope){
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++) {
        	if(fn.call(scope||cs[i], cs[i]) === true){
        	    return cs[i];
        	}
        }
        return null;
    },

    /**
     * Sorts this nodes children using the supplied sort function
     * @param {Function} fn
     * @param {Object} scope (optional)
     */
    sort : function(fn, scope){
        var cs = this.childNodes;
        var len = cs.length;
        if(len > 0){
            var sortFn = scope ? function(){fn.apply(scope, arguments);} : fn;
            cs.sort(sortFn);
            for(var i = 0; i < len; i++){
                var n = cs[i];
                n.previousSibling = cs[i-1];
                n.nextSibling = cs[i+1];
                if(i == 0){
                    this.setFirstChild(n);
                }
                if(i == len-1){
                    this.setLastChild(n);
                }
            }
        }
    },

    /**
     * Returns true if this node is an ancestor (at any point) of the passed node.
     * @param {Node} node
     * @return {Boolean}
     */
    contains : function(node){
        return node.isAncestor(this);
    },

    /**
     * Returns true if the passed node is an ancestor (at any point) of this node.
     * @param {Node} node
     * @return {Boolean}
     */
    isAncestor : function(node){
        var p = this.parentNode;
        while(p){
            if(p == node){
                return true;
            }
            p = p.parentNode;
        }
        return false;
    },

    toString : function(){
        return "[Node"+(this.id?" "+this.id:"")+"]";
    }
});



/**
 * @class Ext.ComponentMgr
 * Provides a common registry of all components on a page so that they can be easily accessed by component id (see {@link Ext.getCmp}).
 * @singleton
 */
Ext.ComponentMgr = function(){
    var all = new Ext.util.MixedCollection();

    return {
        /**
         * Registers a component.
         * @param {Ext.Component} c The component
         */
        register : function(c){
            all.add(c);
        },

        /**
         * Unregisters a component.
         * @param {Ext.Component} c The component
         */
        unregister : function(c){
            all.remove(c);
        },

        /**
         * Returns a component by id
         * @param {String} id The component id
         */
        get : function(id){
            return all.get(id);
        },

        /**
         * Registers a function that will be called when a specified component is added to ComponentMgr
         * @param {String} id The component id
         * @param {Funtction} fn The callback function
         * @param {Object} scope The scope of the callback
         */
        onAvailable : function(id, fn, scope){
            all.on("add", function(index, o){
                if(o.id == id){
                    fn.call(scope || o, o);
                    all.un("add", fn, scope);
                }
            });
        }
    };
}();

/**
 * @class Ext.Component
 * @extends Ext.util.Observable
 * Base class for all major Ext components.  All subclasses of Component can automatically participate in the standard
 * Ext component lifecycle of creation, rendering and destruction.  They also have automatic support for basic hide/show
 * and enable/disable behavior.  Component allows any subclass to be lazy-rendered into any {@link Ext.Container} and
 * to be automatically registered with the {@link Ext.ComponentMgr} so that it can be referenced at any time via {@link Ext.getCmp}.
 * All visual components (widgets) that require rendering into a layout should subclass Component.
 * @constructor
 * @param {Ext.Element/String/Object} config The configuration options.  If an element is passed, it is set as the internal
 * element and its id used as the component id.  If a string is passed, it is assumed to be the id of an existing element
 * and is used as the component id.  Otherwise, it is assumed to be a standard config object and is applied to the component.
 */
Ext.Component = function(config){
    config = config || {};
    if(config.tagName || config.dom || typeof config == "string"){ // element object
        config = {el: config, id: config.id || config};
    }
    this.initialConfig = config;

    Ext.apply(this, config);
    this.addEvents({
        /**
         * @event disable
         * Fires after the component is disabled.
	     * @param {Ext.Component} this
	     */
        disable : true,
        /**
         * @event enable
         * Fires after the component is enabled.
	     * @param {Ext.Component} this
	     */
        enable : true,
        /**
         * @event beforeshow
         * Fires before the component is shown.  Return false to stop the show.
	     * @param {Ext.Component} this
	     */
        beforeshow : true,
        /**
         * @event show
         * Fires after the component is shown.
	     * @param {Ext.Component} this
	     */
        show : true,
        /**
         * @event beforehide
         * Fires before the component is hidden. Return false to stop the hide.
	     * @param {Ext.Component} this
	     */
        beforehide : true,
        /**
         * @event hide
         * Fires after the component is hidden.
	     * @param {Ext.Component} this
	     */
        hide : true,
        /**
         * @event beforerender
         * Fires before the component is rendered. Return false to stop the render.
	     * @param {Ext.Component} this
	     */
        beforerender : true,
        /**
         * @event render
         * Fires after the component is rendered.
	     * @param {Ext.Component} this
	     */
        render : true,
        /**
         * @event beforedestroy
         * Fires before the component is destroyed. Return false to stop the destroy.
	     * @param {Ext.Component} this
	     */
        beforedestroy : true,
        /**
         * @event destroy
         * Fires after the component is destroyed.
	     * @param {Ext.Component} this
	     */
        destroy : true
    });
    if(!this.id){
        this.id = "ext-comp-" + (++Ext.Component.AUTO_ID);
    }
    Ext.ComponentMgr.register(this);
    Ext.Component.superclass.constructor.call(this);
    this.initComponent();
    if(this.renderTo){ // not supported by all components yet. use at your own risk!
        this.render(this.renderTo);
        delete this.renderTo;
    }
};

// private
Ext.Component.AUTO_ID = 1000;

Ext.extend(Ext.Component, Ext.util.Observable, {
    /**
     * true if this component is hidden. Read-only.
     */
    hidden : false,
    /**
     * true if this component is disabled. Read-only.
     */
    disabled : false,
    /**
     * true if this component has been rendered. Read-only.
     */
    rendered : false,
    
    /** @cfg {String} disableClass
     * CSS class added to the component when it is disabled (defaults to "x-item-disabled").
     */
    disabledClass : "x-item-disabled",
	/** @cfg {Boolean} allowDomMove
	 * Whether the component can move the Dom node when rendering (defaults to true).
	 */
    allowDomMove : true,
    /** @cfg {String} hideMode
     * How this component should hidden. Supported values are
     * "visibility" (css visibility), "offsets" (negative offset position) and
     * "display" (css display) - defaults to "display".
     */
    hideMode: 'display',

    // private
    ctype : "Ext.Component",

    // private
    actionMode : "el",

    // private
    getActionEl : function(){
        return this[this.actionMode];
    },

    initComponent : Ext.emptyFn,
    /**
     * If this is a lazy rendering component, render it to its container element.
     * @param {String/HTMLElement/Element} container (optional) The element this component should be rendered into. If it is being applied to existing markup, this should be left off.
     */
    render : function(container, position){
        if(!this.rendered && this.fireEvent("beforerender", this) !== false){
            if(!container && this.el){
                this.el = Ext.get(this.el);
                container = this.el.dom.parentNode;
                this.allowDomMove = false;
            }
            this.container = Ext.get(container);
            this.rendered = true;
            if(position !== undefined){
                if(typeof position == 'number'){
                    position = this.container.dom.childNodes[position];
                }else{
                    position = Ext.getDom(position);
                }
            }
            this.onRender(this.container, position || null);
            if(this.cls){
                this.el.addClass(this.cls);
                delete this.cls;
            }
            if(this.style){
                this.el.applyStyles(this.style);
                delete this.style;
            }
            this.fireEvent("render", this);
            this.afterRender(this.container);
            if(this.hidden){
                this.hide();
            }
            if(this.disabled){
                this.disable();
            }
        }
        return this;
    },

    // private
    // default function is not really useful
    onRender : function(ct, position){
        if(this.el){
            this.el = Ext.get(this.el);
            if(this.allowDomMove !== false){
                ct.dom.insertBefore(this.el.dom, position);
            }
        }
    },

    // private
    getAutoCreate : function(){
        var cfg = typeof this.autoCreate == "object" ?
                      this.autoCreate : Ext.apply({}, this.defaultAutoCreate);
        if(this.id && !cfg.id){
            cfg.id = this.id;
        }
        return cfg;
    },

    // private
    afterRender : Ext.emptyFn,

    /**
     * Destroys this component by purging any event listeners, removing the component's element from the DOM,
     * removing the component from its {@link Ext.Container} (if applicable) and unregistering it from {@link Ext.ComponentMgr}.
     */
    destroy : function(){
        if(this.fireEvent("beforedestroy", this) !== false){
            this.purgeListeners();
            this.beforeDestroy();
            if(this.rendered){
                this.el.removeAllListeners();
                this.el.remove();
                if(this.actionMode == "container"){
                    this.container.remove();
                }
            }
            this.onDestroy();
            Ext.ComponentMgr.unregister(this);
            this.fireEvent("destroy", this);
        }
    },

	// private
    beforeDestroy : function(){

    },

	// private
	onDestroy : function(){

    },

    /**
     * Returns the underlying {@link Ext.Element}.
     * @return {Ext.Element} The element
     */
    getEl : function(){
        return this.el;
    },

    /**
     * Returns the id of this component.
     * @return {String}
     */
    getId : function(){
        return this.id;
    },

    /**
     * Try to focus this component.
     * @param {Boolean} selectText True to also select the text in this component (if applicable)
     * @return {Ext.Component} this
     */
    focus : function(selectText){
        if(this.rendered){
            this.el.focus();
            if(selectText === true){
                this.el.dom.select();
            }
        }
        return this;
    },

    // private
    blur : function(){
        if(this.rendered){
            this.el.blur();
        }
        return this;
    },

    /**
     * Disable this component.
     * @return {Ext.Component} this
     */
    disable : function(){
        if(this.rendered){
            this.onDisable();
        }
        this.disabled = true;
        this.fireEvent("disable", this);
        return this;
    },

	// private
    onDisable : function(){
        this.getActionEl().addClass(this.disabledClass);
        this.el.dom.disabled = true;
    },

    /**
     * Enable this component.
     * @return {Ext.Component} this
     */
    enable : function(){
        if(this.rendered){
            this.onEnable();
        }
        this.disabled = false;
        this.fireEvent("enable", this);
        return this;
    },

	// private
    onEnable : function(){
        this.getActionEl().removeClass(this.disabledClass);
        this.el.dom.disabled = false;
    },

    /**
     * Convenience function for setting disabled/enabled by boolean.
     * @param {Boolean} disabled
     */
    setDisabled : function(disabled){
        this[disabled ? "disable" : "enable"]();
    },

    /**
     * Show this component.
     * @return {Ext.Component} this
     */
    show: function(){
        if(this.fireEvent("beforeshow", this) !== false){
            this.hidden = false;
            if(this.rendered){
                this.onShow();
            }
            this.fireEvent("show", this);
        }
        return this;
    },

    // private
    onShow : function(){
        var ae = this.getActionEl();
        if(this.hideMode == 'visibility'){
            ae.dom.style.visibility = "visible";
        }else if(this.hideMode == 'offsets'){
            ae.removeClass('x-hidden');
        }else{
            ae.dom.style.display = "";
        }
    },

    /**
     * Hide this component.
     * @return {Ext.Component} this
     */
    hide: function(){
        if(this.fireEvent("beforehide", this) !== false){
            this.hidden = true;
            if(this.rendered){
                this.onHide();
            }
            this.fireEvent("hide", this);
        }
        return this;
    },

    // private
    onHide : function(){
        var ae = this.getActionEl();
        if(this.hideMode == 'visibility'){
            ae.dom.style.visibility = "hidden";
        }else if(this.hideMode == 'offsets'){
            ae.addClass('x-hidden');
        }else{
            ae.dom.style.display = "none";
        }
    },

    /**
     * Convenience function to hide or show this component by boolean.
     * @param {Boolean} visible True to show, false to hide
     * @return {Ext.Component} this
     */
    setVisible: function(visible){
        if(visible) {
            this.show();
        }else{
            this.hide();
        }
        return this;
    },

    /**
     * Returns true if this component is visible.
     */
    isVisible : function(){
        return this.getActionEl().isVisible();
    },

    cloneConfig : function(overrides){
        overrides = overrides || {};
        var id = overrides.id || Ext.id();
        var cfg = Ext.applyIf(overrides, this.initialConfig);
        cfg.id = id; // prevent dup id
        return new this.constructor(cfg);
    }
});



/**
 * @class Ext.BoxComponent
 * @extends Ext.Component
 * Base class for any visual {@link Ext.Component} that uses a box container.  BoxComponent provides automatic box
 * model adjustments for sizing and positioning and will work correctly withnin the Component rendering model.  All
 * container classes should subclass BoxComponent so that they will work consistently when nested within other Ext
 * layout containers.
 * @constructor
 * @param {Ext.Element/String/Object} config The configuration options.
 */
Ext.BoxComponent = function(config){
    Ext.BoxComponent.superclass.constructor.call(this, config);
    this.addEvents({
        /**
         * @event resize
         * Fires after the component is resized.
	     * @param {Ext.Component} this
	     * @param {Number} adjWidth The box-adjusted width that was set
	     * @param {Number} adjHeight The box-adjusted height that was set
	     * @param {Number} rawWidth The width that was originally specified
	     * @param {Number} rawHeight The height that was originally specified
	     */
        resize : true,
        /**
         * @event move
         * Fires after the component is moved.
	     * @param {Ext.Component} this
	     * @param {Number} x The new x position
	     * @param {Number} y The new y position
	     */
        move : true
    });
};

Ext.extend(Ext.BoxComponent, Ext.Component, {
    // private, set in afterRender to signify that the component has been rendered
    boxReady : false,
    // private, used to defer height settings to subclasses
    deferHeight: false,

    /**
     * Sets the width and height of the component.  This method fires the resize event.  This method can accept
     * either width and height as separate numeric arguments, or you can pass a size object like {width:10, height:20}.
     * @param {Number/Object} width The new width to set, or a size object in the format {width, height}
     * @param {Number} height The new height to set (not required if a size object is passed as the first arg)
     * @return {Ext.BoxComponent} this
     */
    setSize : function(w, h){
        // support for standard size objects
        if(typeof w == 'object'){
            h = w.height;
            w = w.width;
        }
        // not rendered
        if(!this.boxReady){
            this.width = w;
            this.height = h;
            return this;
        }

        // prevent recalcs when not needed
        if(this.lastSize && this.lastSize.width == w && this.lastSize.height == h){
            return this;
        }
        this.lastSize = {width: w, height: h};

        var adj = this.adjustSize(w, h);
        var aw = adj.width, ah = adj.height;
        if(aw !== undefined || ah !== undefined){ // this code is nasty but performs better with floaters
            var rz = this.getResizeEl();
            if(!this.deferHeight && aw !== undefined && ah !== undefined){
                rz.setSize(aw, ah);
            }else if(!this.deferHeight && ah !== undefined){
                rz.setHeight(ah);
            }else if(aw !== undefined){
                rz.setWidth(aw);
            }
            this.onResize(aw, ah, w, h);
            this.fireEvent('resize', this, aw, ah, w, h);
        }
        return this;
    },

    /**
     * Gets the current size of the component's underlying element.
     * @return {Object} An object containing the element's size {width: (element width), height: (element height)}
     */
    getSize : function(){
        return this.el.getSize();
    },

    /**
     * Gets the current XY position of the component's underlying element.
     * @param {Boolean} local (optional) If true the element's left and top are returned instead of page XY (defaults to false)
     * @return {Array} The XY position of the element (e.g., [100, 200])
     */
    getPosition : function(local){
        if(local === true){
            return [this.el.getLeft(true), this.el.getTop(true)];
        }
        return this.xy || this.el.getXY();
    },

    /**
     * Gets the current box measurements of the component's underlying element.
     * @param {Boolean} local (optional) If true the element's left and top are returned instead of page XY (defaults to false)
     * @returns {Object} box An object in the format {x, y, width, height}
     */
    getBox : function(local){
        var s = this.el.getSize();
        if(local){
            s.x = this.el.getLeft(true);
            s.y = this.el.getTop(true);
        }else{
            var xy = this.xy || this.el.getXY();
            s.x = xy[0];
            s.y = xy[1];
        }
        return s;
    },

    /**
     * Sets the current box measurements of the component's underlying element.
     * @param {Object} box An object in the format {x, y, width, height}
     * @returns {Ext.BoxComponent} this
     */
    updateBox : function(box){
        this.setSize(box.width, box.height);
        this.setPagePosition(box.x, box.y);
        return this;
    },

    // protected
    getResizeEl : function(){
        return this.resizeEl || this.el;
    },

    // protected
    getPositionEl : function(){
        return this.positionEl || this.el;
    },

    /**
     * Sets the left and top of the component.  To set the page XY position instead, use {@link #setPagePosition}.
     * This method fires the move event.
     * @param {Number} left The new left
     * @param {Number} top The new top
     * @returns {Ext.BoxComponent} this
     */
    setPosition : function(x, y){
        this.x = x;
        this.y = y;
        if(!this.boxReady){
            return this;
        }
        var adj = this.adjustPosition(x, y);
        var ax = adj.x, ay = adj.y;

        var el = this.getPositionEl();
        if(ax !== undefined || ay !== undefined){
            if(ax !== undefined && ay !== undefined){
                el.setLeftTop(ax, ay);
            }else if(ax !== undefined){
                el.setLeft(ax);
            }else if(ay !== undefined){
                el.setTop(ay);
            }
            this.onPosition(ax, ay);
            this.fireEvent('move', this, ax, ay);
        }
        return this;
    },

    /**
     * Sets the page XY position of the component.  To set the left and top instead, use {@link #setPosition}.
     * This method fires the move event.
     * @param {Number} x The new x position
     * @param {Number} y The new y position
     * @returns {Ext.BoxComponent} this
     */
    setPagePosition : function(x, y){
        this.pageX = x;
        this.pageY = y;
        if(!this.boxReady){
            return;
        }
        if(x === undefined || y === undefined){ // cannot translate undefined points
            return;
        }
        var p = this.el.translatePoints(x, y);
        this.setPosition(p.left, p.top);
        return this;
    },

    // private
    onRender : function(ct, position){
        Ext.BoxComponent.superclass.onRender.call(this, ct, position);
        if(this.resizeEl){
            this.resizeEl = Ext.get(this.resizeEl);
        }
        if(this.positionEl){
            this.positionEl = Ext.get(this.positionEl);
        }
    },

    // private
    afterRender : function(){
        Ext.BoxComponent.superclass.afterRender.call(this);
        this.boxReady = true;
        this.setSize(this.width, this.height);
        if(this.x || this.y){
            this.setPosition(this.x, this.y);
        }
        if(this.pageX || this.pageY){
            this.setPagePosition(this.pageX, this.pageY);
        }
    },

    /**
     * Force the component's size to recalculate based on the underlying element's current height and width.
     * @returns {Ext.BoxComponent} this
     */
    syncSize : function(){
        delete this.lastSize;
        this.setSize(this.el.getWidth(), this.el.getHeight());
        return this;
    },

    /**
     * Called after the component is resized, this method is empty by default but can be implemented by any
     * subclass that needs to perform custom logic after a resize occurs.
     * @param {Number} adjWidth The box-adjusted width that was set
     * @param {Number} adjHeight The box-adjusted height that was set
     * @param {Number} rawWidth The width that was originally specified
     * @param {Number} rawHeight The height that was originally specified
     */
    onResize : function(adjWidth, adjHeight, rawWidth, rawHeight){

    },

    /**
     * Called after the component is moved, this method is empty by default but can be implemented by any
     * subclass that needs to perform custom logic after a move occurs.
     * @param {Number} x The new x position
     * @param {Number} y The new y position
     */
    onPosition : function(x, y){

    },

    // private
    adjustSize : function(w, h){
        if(this.autoWidth){
            w = 'auto';
        }
        if(this.autoHeight){
            h = 'auto';
        }
        return {width : w, height: h};
    },

    // private
    adjustPosition : function(x, y){
        return {x : x, y: y};
    }
});



/**
 * @class Ext.Layer
 * @extends Ext.Element
 * An extended {@link Ext.Element} object that supports a shadow and shim, constrain to viewport and
 * automatic maintaining of shadow/shim positions.
 * @cfg {Boolean} shim False to disable the iframe shim in browsers which need one (defaults to true)
 * @cfg {String/Boolean} shadow True to create a shadow element with default class "x-layer-shadow", or
 * you can pass a string with a CSS class name. False turns off the shadow.
 * @cfg {Object} dh DomHelper object config to create element with (defaults to {tag: "div", cls: "x-layer"}).
 * @cfg {Boolean} constrain False to disable constrain to viewport (defaults to true)
 * @cfg {String} cls CSS class to add to the element
 * @cfg {Number} zindex Starting z-index (defaults to 11000)
 * @cfg {Number} shadowOffset Number of pixels to offset the shadow (defaults to 3)
 * @constructor
 * @param {Object} config An object with config options.
 * @param {String/HTMLElement} existingEl (optional) Uses an existing DOM element. If the element is not found it creates it.
 */
(function(){ 
Ext.Layer = function(config, existingEl){
    config = config || {};
    var dh = Ext.DomHelper;
    var cp = config.parentEl, pel = cp ? Ext.getDom(cp) : document.body;
    if(existingEl){
        this.dom = Ext.getDom(existingEl);
    }
    if(!this.dom){
        var o = config.dh || {tag: "div", cls: "x-layer"};
        this.dom = dh.append(pel, o);
    }
    if(config.cls){
        this.addClass(config.cls);
    }
    this.constrain = config.constrain !== false;
    this.visibilityMode = Ext.Element.VISIBILITY;
    if(config.id){
        this.id = this.dom.id = config.id;
    }else{
        this.id = Ext.id(this.dom);
    }
    this.zindex = config.zindex || this.getZIndex();
    this.position("absolute", this.zindex);
    if(config.shadow){
        this.shadowOffset = config.shadowOffset || 4;
        this.shadow = new Ext.Shadow({
            offset : this.shadowOffset,
            mode : config.shadow
        });
    }else{
        this.shadowOffset = 0;
    }
    this.useShim = config.shim !== false && Ext.useShims;
    this.useDisplay = config.useDisplay;
    this.hide();
};

var supr = Ext.Element.prototype;

// shims are shared among layer to keep from having 100 iframes
var shims = [];

Ext.extend(Ext.Layer, Ext.Element, {

    getZIndex : function(){
        return this.zindex || parseInt(this.getStyle("z-index"), 10) || 11000;
    },

    getShim : function(){
        if(!this.useShim){
            return null;
        }
        if(this.shim){
            return this.shim;
        }
        var shim = shims.shift();
        if(!shim){
            shim = this.createShim();
            shim.enableDisplayMode('block');
            shim.dom.style.display = 'none';
            shim.dom.style.visibility = 'visible';
        }
        var pn = this.dom.parentNode;
        if(shim.dom.parentNode != pn){
            pn.insertBefore(shim.dom, this.dom);
        }
        shim.setStyle('z-index', this.getZIndex()-2);
        this.shim = shim;
        return shim;
    },

    hideShim : function(){
        if(this.shim){
            this.shim.setDisplayed(false);
            shims.push(this.shim);
            delete this.shim;
        }
    },

    disableShadow : function(){
        if(this.shadow){
            this.shadowDisabled = true;
            this.shadow.hide();
            this.lastShadowOffset = this.shadowOffset;
            this.shadowOffset = 0;
        }
    },

    enableShadow : function(show){
        if(this.shadow){
            this.shadowDisabled = false;
            this.shadowOffset = this.lastShadowOffset;
            delete this.lastShadowOffset;
            if(show){
                this.sync(true);
            }
        }
    },

    // private
    // this code can execute repeatedly in milliseconds (i.e. during a drag) so
    // code size was sacrificed for effeciency (e.g. no getBox/setBox, no XY calls)
    sync : function(doShow){
        var sw = this.shadow;
        if(!this.updating && this.isVisible() && (sw || this.useShim)){
            var sh = this.getShim();

            var w = this.getWidth(),
                h = this.getHeight();

            var l = this.getLeft(true),
                t = this.getTop(true);

            if(sw && !this.shadowDisabled){
                if(doShow && !sw.isVisible()){
                    sw.show(this);
                }else{
                    sw.realign(l, t, w, h);
                }
                if(sh){
                    if(doShow){
                       sh.show();
                    }
                    // fit the shim behind the shadow, so it is shimmed too
                    var a = sw.adjusts, s = sh.dom.style;
                    s.left = (Math.min(l, l+a.l))+"px";
                    s.top = (Math.min(t, t+a.t))+"px";
                    s.width = (w+a.w)+"px";
                    s.height = (h+a.h)+"px";
                }
            }else if(sh){
                if(doShow){
                   sh.show();
                }
                sh.setSize(w, h);
                sh.setLeftTop(l, t);
            }
            
        }
    },

    // private
    destroy : function(){
        this.hideShim();
        if(this.shadow){
            this.shadow.hide();
        }
        this.removeAllListeners();
        var pn = this.dom.parentNode;
        if(pn){
            pn.removeChild(this.dom);
        }
        Ext.Element.uncache(this.id);
    },

    remove : function(){
        this.destroy();
    },

    // private
    beginUpdate : function(){
        this.updating = true;
    },

    // private
    endUpdate : function(){
        this.updating = false;
        this.sync(true);
    },

    // private
    hideUnders : function(negOffset){
        if(this.shadow){
            this.shadow.hide();
        }
        this.hideShim();
    },

    // private
    constrainXY : function(){
        if(this.constrain){
            var vw = Ext.lib.Dom.getViewWidth(),
                vh = Ext.lib.Dom.getViewHeight();
            var s = Ext.get(document).getScroll();

            var xy = this.getXY();
            var x = xy[0], y = xy[1];   
            var w = this.dom.offsetWidth+this.shadowOffset, h = this.dom.offsetHeight+this.shadowOffset;
            // only move it if it needs it
            var moved = false;
            // first validate right/bottom
            if((x + w) > vw+s.left){
                x = vw - w - this.shadowOffset;
                moved = true;
            }
            if((y + h) > vh+s.top){
                y = vh - h - this.shadowOffset;
                moved = true;
            }
            // then make sure top/left isn't negative
            if(x < s.left){
                x = s.left;
                moved = true;
            }
            if(y < s.top){
                y = s.top;
                moved = true;
            }
            if(moved){
                if(this.avoidY){
                    var ay = this.avoidY;
                    if(y <= ay && (y+h) >= ay){
                        y = ay-h-5;   
                    }
                }
                xy = [x, y];
                this.storeXY(xy);
                supr.setXY.call(this, xy);
                this.sync();
            }
        }
    },

    isVisible : function(){
        return this.visible;    
    },

    // private
    showAction : function(){
        this.visible = true; // track visibility to prevent getStyle calls
        if(this.useDisplay === true){
            this.setDisplayed("");
        }else if(this.lastXY){
            supr.setXY.call(this, this.lastXY);
        }else if(this.lastLT){
            supr.setLeftTop.call(this, this.lastLT[0], this.lastLT[1]);
        }
    },

    // private
    hideAction : function(){
        this.visible = false;
        if(this.useDisplay === true){
            this.setDisplayed(false);
        }else{
            this.setLeftTop(-10000,-10000);
        }
    },

    // overridden Element method
    setVisible : function(v, a, d, c, e){
        if(v){
            this.showAction();
        }
        if(a && v){
            var cb = function(){
                this.sync(true);
                if(c){
                    c();
                }
            }.createDelegate(this);
            supr.setVisible.call(this, true, true, d, cb, e);
        }else{
            if(!v){
                this.hideUnders(true);
            }
            var cb = c;
            if(a){
                cb = function(){
                    this.hideAction();
                    if(c){
                        c();
                    }
                }.createDelegate(this);
            }
            supr.setVisible.call(this, v, a, d, cb, e);
            if(v){
                this.sync(true);
            }else if(!a){
                this.hideAction();
            }
        }
    },

    storeXY : function(xy){
        delete this.lastLT;
        this.lastXY = xy;
    },

    storeLeftTop : function(left, top){
        delete this.lastXY;
        this.lastLT = [left, top];
    },

    // private
    beforeFx : function(){
        this.beforeAction();
        return Ext.Layer.superclass.beforeFx.apply(this, arguments);
    },

    // private
    afterFx : function(){
        Ext.Layer.superclass.afterFx.apply(this, arguments);
        this.sync(this.isVisible());
    },

    // private
    beforeAction : function(){
        if(!this.updating && this.shadow){
            this.shadow.hide();
        }
    },

    // overridden Element method
    setLeft : function(left){
        this.storeLeftTop(left, this.getTop(true));
        supr.setLeft.apply(this, arguments);
        this.sync();
    },

    setTop : function(top){
        this.storeLeftTop(this.getLeft(true), top);
        supr.setTop.apply(this, arguments);
        this.sync();
    },

    setLeftTop : function(left, top){
        this.storeLeftTop(left, top);
        supr.setLeftTop.apply(this, arguments);
        this.sync();
    },

    setXY : function(xy, a, d, c, e){
        this.fixDisplay();
        this.beforeAction();
        this.storeXY(xy);
        var cb = this.createCB(c);
        supr.setXY.call(this, xy, a, d, cb, e);
        if(!a){
            cb();
        }
    },

    // private
    createCB : function(c){
        var el = this;
        return function(){
            el.constrainXY();
            el.sync(true);
            if(c){
                c();
            }
        };
    },

    // overridden Element method
    setX : function(x, a, d, c, e){
        this.setXY([x, this.getY()], a, d, c, e);
    },

    // overridden Element method
    setY : function(y, a, d, c, e){
        this.setXY([this.getX(), y], a, d, c, e);
    },

    // overridden Element method
    setSize : function(w, h, a, d, c, e){
        this.beforeAction();
        var cb = this.createCB(c);
        supr.setSize.call(this, w, h, a, d, cb, e);
        if(!a){
            cb();
        }
    },

    // overridden Element method
    setWidth : function(w, a, d, c, e){
        this.beforeAction();
        var cb = this.createCB(c);
        supr.setWidth.call(this, w, a, d, cb, e);
        if(!a){
            cb();
        }
    },

    // overridden Element method
    setHeight : function(h, a, d, c, e){
        this.beforeAction();
        var cb = this.createCB(c);
        supr.setHeight.call(this, h, a, d, cb, e);
        if(!a){
            cb();
        }
    },

    // overridden Element method
    setBounds : function(x, y, w, h, a, d, c, e){
        this.beforeAction();
        var cb = this.createCB(c);
        if(!a){
            this.storeXY([x, y]);
            supr.setXY.call(this, [x, y]);
            supr.setSize.call(this, w, h, a, d, cb, e);
            cb();
        }else{
            supr.setBounds.call(this, x, y, w, h, a, d, cb, e);
        }
        return this;
    },
    
    /**
     * Sets the z-index of this layer and adjusts any shadow and shim z-indexes. The layer z-index is automatically
     * incremented by two more than the value passed in so that it always shows above any shadow or shim (the shadow
     * element, if any, will be assigned z-index + 1, and the shim element, if any, will be assigned the unmodified z-index).
     * @param {Number} zindex The new z-index to set
     * @return {this} The Layer
     */
    setZIndex : function(zindex){
        this.zindex = zindex;
        this.setStyle("z-index", zindex + 2);
        if(this.shadow){
            this.shadow.setZIndex(zindex + 1);
        }
        if(this.shim){
            this.shim.setStyle("z-index", zindex);
        }
    }
});
})();



/**
 * @class Ext.Shadow
 * Simple class that can provide a shadow effect for any element.  Note that the element MUST be absolutely positioned,
 * and the shadow does not provide any shimming.  This should be used only in simple cases -- for more advanced
 * functionality that can also provide the same shadow effect, see the {@link Ext.Layer} class.
 * @constructor
 * Create a new Shadow
 * @param {Object} config The config object
 */
Ext.Shadow = function(config){
    Ext.apply(this, config);
    if(typeof this.mode != "string"){
        this.mode = this.defaultMode;
    }
    var o = this.offset, a = {h: 0};
    var rad = Math.floor(this.offset/2);
    switch(this.mode.toLowerCase()){ // all this hideous nonsense calculates the various offsets for shadows
        case "drop":
            a.w = 0;
            a.l = a.t = o;
            a.t -= 1;
            if(Ext.isIE){
                a.l -= this.offset + rad;
                a.t -= this.offset + rad;
                a.w -= rad;
                a.h -= rad;
                a.t += 1;
            }
        break;
        case "sides":
            a.w = (o*2);
            a.l = -o;
            a.t = o-1;
            if(Ext.isIE){
                a.l -= (this.offset - rad);
                a.t -= this.offset + rad;
                a.l += 1;
                a.w -= (this.offset - rad)*2;
                a.w -= rad + 1;
                a.h -= 1;
            }
        break;
        case "frame":
            a.w = a.h = (o*2);
            a.l = a.t = -o;
            a.t += 1;
            a.h -= 2;
            if(Ext.isIE){
                a.l -= (this.offset - rad);
                a.t -= (this.offset - rad);
                a.l += 1;
                a.w -= (this.offset + rad + 1);
                a.h -= (this.offset + rad);
                a.h += 1;
            }
        break;
    };

    this.adjusts = a;
};

Ext.Shadow.prototype = {
    /**
     * @cfg {String} mode
     * The shadow display mode.  Supports the following options:<br />
     * sides: Shadow displays on both sides and bottom only<br />
     * frame: Shadow displays equally on all four sides<br />
     * drop: Traditional bottom-right drop shadow (default)
     */
    /**
     * @cfg {String} offset
     * The number of pixels to offset the shadow from the element (defaults to 4)
     */
    offset: 4,

    // private
    defaultMode: "drop",

    /**
     * Displays the shadow under the target element
     * @param {String/HTMLElement/Element} targetEl The id or element under which the shadow should display
     */
    show : function(target){
        target = Ext.get(target);
        if(!this.el){
            this.el = Ext.Shadow.Pool.pull();
            if(this.el.dom.nextSibling != target.dom){
                this.el.insertBefore(target);
            }
        }
        this.el.setStyle("z-index", this.zIndex || parseInt(target.getStyle("z-index"), 10)-1);
        if(Ext.isIE){
            this.el.dom.style.filter="progid:DXImageTransform.Microsoft.alpha(opacity=50) progid:DXImageTransform.Microsoft.Blur(pixelradius="+(this.offset)+")";
        }
        this.realign(
            target.getLeft(true),
            target.getTop(true),
            target.getWidth(),
            target.getHeight()
        );
        this.el.dom.style.display = "block";
    },

    /**
     * Returns true if the shadow is visible, else false
     */
    isVisible : function(){
        return this.el ? true : false;  
    },

    /**
     * Direct alignment when values are already available. Show must be called at least once before
     * calling this method to ensure it is initialized.
     * @param {Number} left The target element left position
     * @param {Number} top The target element top position
     * @param {Number} width The target element width
     * @param {Number} height The target element height
     */
    realign : function(l, t, w, h){
        if(!this.el){
            return;
        }
        var a = this.adjusts, d = this.el.dom, s = d.style;
        var iea = 0;
        s.left = (l+a.l)+"px";
        s.top = (t+a.t)+"px";
        var sw = (w+a.w), sh = (h+a.h), sws = sw +"px", shs = sh + "px";
        if(s.width != sws || s.height != shs){
            s.width = sws;
            s.height = shs;
            if(!Ext.isIE){
                var cn = d.childNodes;
                var sww = Math.max(0, (sw-12))+"px";
                cn[0].childNodes[1].style.width = sww;
                cn[1].childNodes[1].style.width = sww;
                cn[2].childNodes[1].style.width = sww;
                cn[1].style.height = Math.max(0, (sh-12))+"px";
            }
        }
    },

    /**
     * Hides this shadow
     */
    hide : function(){
        if(this.el){
            this.el.dom.style.display = "none";
            Ext.Shadow.Pool.push(this.el);
            delete this.el;
        }
    },

    /**
     * Adjust the z-index of this shadow
     * @param {Number} zindex The new z-index
     */
    setZIndex : function(z){
        this.zIndex = z;
        if(this.el){
            this.el.setStyle("z-index", z);
        }
    }
};

// Private utility class that manages the internal Shadow cache
Ext.Shadow.Pool = function(){
    var p = [];
    var markup = Ext.isIE ?
                 '<div class="x-ie-shadow"></div>' :
                 '<div class="x-shadow"><div class="xst"><div class="xstl"></div><div class="xstc"></div><div class="xstr"></div></div><div class="xsc"><div class="xsml"></div><div class="xsmc"></div><div class="xsmr"></div></div><div class="xsb"><div class="xsbl"></div><div class="xsbc"></div><div class="xsbr"></div></div></div>';
    return {
        pull : function(){
            var sh = p.shift();
            if(!sh){
                sh = Ext.get(Ext.DomHelper.insertHtml("beforeBegin", document.body.firstChild, markup));
                sh.autoBoxAdjust = false;
            }
            return sh;
        },

        push : function(sh){
            p.push(sh);
        }
    };
}();



/**
 * @class Ext.Editor
 * @extends Ext.Component
 * A base editor field that handles displaying/hiding on demand and has some built-in sizing and event handling logic.
 * @constructor
 * Create a new Editor
 * @param {Ext.form.Field} field The Field object (or descendant)
 * @param {Object} config The config object
 */
Ext.Editor = function(field, config){
    Ext.Editor.superclass.constructor.call(this, config);
    this.field = field;
    this.addEvents({
        /**
	     * @event beforestartedit
	     * Fires when editing is initiated, but before the value changes.  Editing can be canceled by returning
	     * false from the handler of this event.
	     * @param {Editor} this
	     * @param {Ext.Element} boundEl The underlying element bound to this editor
	     * @param {Mixed} value The field value being set
	     */
        "beforestartedit" : true,
        /**
	     * @event startedit
	     * Fires when this editor is displayed
	     * @param {Ext.Element} boundEl The underlying element bound to this editor
	     * @param {Mixed} value The starting field value
	     */
        "startedit" : true,
        /**
	     * @event beforecomplete
	     * Fires after a change has been made to the field, but before the change is reflected in the underlying
	     * field.  Saving the change to the field can be canceled by returning false from the handler of this event.
	     * Note that if the value has not changed and ignoreNoChange = true, the editing will still end but this
	     * event will not fire since no edit actually occurred.
	     * @param {Editor} this
	     * @param {Mixed} value The current field value
	     * @param {Mixed} startValue The original field value
	     */
        "beforecomplete" : true,
        /**
	     * @event complete
	     * Fires after editing is complete and any changed value has been written to the underlying field.
	     * @param {Editor} this
	     * @param {Mixed} value The current field value
	     * @param {Mixed} startValue The original field value
	     */
        "complete" : true,
        /**
         * @event specialkey
         * Fires when any key related to navigation (arrows, tab, enter, esc, etc.) is pressed.  You can check
         * {@link Ext.EventObject#getKey} to determine which key was pressed.
         * @param {Ext.form.Field} this
         * @param {Ext.EventObject} e The event object
         */
        "specialkey" : true
    });
};

Ext.extend(Ext.Editor, Ext.Component, {
    /**
     * @cfg {Boolean/String} autosize
     * True for the editor to automatically adopt the size of the underlying field, "width" to adopt the width only,
     * or "height" to adopt the height only (defaults to false)
     */
    /**
     * @cfg {Boolean} revertInvalid
     * True to automatically revert the field value and cancel the edit when the user completes an edit and the field
     * validation fails (defaults to true)
     */
    /**
     * @cfg {Boolean} ignoreNoChange
     * True to skip the the edit completion process (no save, no events fired) if the user completes an edit and
     * the value has not changed (defaults to false).  Applies only to string values - edits for other data types
     * will never be ignored.
     */
    /**
     * @cfg {Boolean} hideEl
     * False to keep the bound element visible while the editor is displayed (defaults to true)
     */
    /**
     * @cfg {Mixed} value
     * The data value of the underlying field (defaults to "")
     */
    value : "",
    /**
     * @cfg {String} alignment
     * The position to align to (see {@link Ext.Element#alignTo} for more details, defaults to "c-c?").
     */
    alignment: "c-c?",
    /**
     * @cfg {Boolean/String} shadow "sides" for sides/bottom only, "frame" for 4-way shadow, and "drop"
     * for bottom-right shadow (defaults to "frame")
     */
    shadow : "frame",
    /**
     * @cfg {Boolean} constrain True to constrain the editor to the viewport
     */
    constrain : false,
    /**
     * @cfg {Boolean} completeOnEnter True to complete the edit when the enter key is pressed (defaults to false)
     */
    completeOnEnter : false,
    /**
     * @cfg {Boolean} cancelOnEsc True to cancel the edit when the escape key is pressed (defaults to false)
     */
    cancelOnEsc : false,
    /**
     * @cfg {Boolean} updateEl True to update the innerHTML of the bound element when the update completes (defaults to false)
     */
    updateEl : false,

    // private
    onRender : function(ct, position){
        this.el = new Ext.Layer({
            shadow: this.shadow,
            cls: "x-editor",
            parentEl : ct,
            shim : this.shim,
            shadowOffset:4,
            id: this.id,
            constrain: this.constrain
        });
        this.el.setStyle("overflow", Ext.isGecko ? "auto" : "hidden");
        if(this.field.msgTarget != 'title'){
            this.field.msgTarget = 'qtip';
        }
        this.field.render(this.el);
        if(Ext.isGecko){
            this.field.el.dom.setAttribute('autocomplete', 'off');
        }
        this.field.on("specialkey", this.onSpecialKey, this);
        if(this.swallowKeys){
            this.field.el.swallowEvent(['keydown','keypress']);
        }
        this.field.show();
        this.field.on("blur", this.onBlur, this);
        if(this.field.grow){
            this.field.on("autosize", this.el.sync,  this.el, {delay:1});
        }
    },

    onSpecialKey : function(field, e){
        if(this.completeOnEnter && e.getKey() == e.ENTER){
            e.stopEvent();
            this.completeEdit();
        }else if(this.cancelOnEsc && e.getKey() == e.ESC){
            this.cancelEdit();
        }else{
            this.fireEvent('specialkey', field, e);
        }
    },

    /**
     * Starts the editing process and shows the editor.
     * @param {String/HTMLElement/Element} el The element to edit
     * @param {String} value (optional) A value to initialize the editor with. If a value is not provided, it defaults
      * to the innerHTML of el.
     */
    startEdit : function(el, value){
        if(this.editing){
            this.completeEdit();
        }
        this.boundEl = Ext.get(el);
        var v = value !== undefined ? value : this.boundEl.dom.innerHTML;
        if(!this.rendered){
            this.render(this.parentEl || document.body);
        }
        if(this.fireEvent("beforestartedit", this, this.boundEl, v) === false){
            return;
        }
        this.startValue = v;
        this.field.setValue(v);
        if(this.autoSize){
            var sz = this.boundEl.getSize();
            switch(this.autoSize){
                case "width":
                this.setSize(sz.width,  "");
                break;
                case "height":
                this.setSize("",  sz.height);
                break;
                default:
                this.setSize(sz.width,  sz.height);
            }
        }
        this.el.alignTo(this.boundEl, this.alignment);
        this.editing = true;
        if(Ext.QuickTips){
            Ext.QuickTips.disable();
        }
        this.show();
    },

    /**
     * Sets the height and width of this editor.
     * @param {Number} width The new width
     * @param {Number} height The new height
     */
    setSize : function(w, h){
        this.field.setSize(w, h);
        if(this.el){
            this.el.sync();
        }
    },

    /**
     * Realigns the editor to the bound field based on the current alignment config value.
     */
    realign : function(){
        this.el.alignTo(this.boundEl, this.alignment);
    },

    /**
     * Ends the editing process, persists the changed value to the underlying field, and hides the editor.
     * @param {Boolean} remainVisible Override the default behavior and keep the editor visible after edit (defaults to false)
     */
    completeEdit : function(remainVisible){
        if(!this.editing){
            return;
        }
        var v = this.getValue();
        if(this.revertInvalid !== false && !this.field.isValid()){
            v = this.startValue;
            this.cancelEdit(true);
        }
        if(String(v) === String(this.startValue) && this.ignoreNoChange){
            this.editing = false;
            this.hide();
            return;
        }
        if(this.fireEvent("beforecomplete", this, v, this.startValue) !== false){
            this.editing = false;
            if(this.updateEl && this.boundEl){
                this.boundEl.update(v);
            }
            if(remainVisible !== true){
                this.hide();
            }
            this.fireEvent("complete", this, v, this.startValue);
        }
    },

    // private
    onShow : function(){
        this.el.show();
        if(this.hideEl !== false){
            this.boundEl.hide();
        }
        this.field.show();
        if(Ext.isIE && !this.fixIEFocus){ // IE has problems with focusing the first time
            this.fixIEFocus = true;
            this.deferredFocus.defer(50, this);
        }else{
            this.field.focus();
        }
        this.fireEvent("startedit", this.boundEl, this.startValue);
    },

    deferredFocus : function(){
        if(this.editing){
            this.field.focus();
        }
    },

    /**
     * Cancels the editing process and hides the editor without persisting any changes.  The field value will be
     * reverted to the original starting value.
     * @param {Boolean} remainVisible Override the default behavior and keep the editor visible after
     * cancel (defaults to false)
     */
    cancelEdit : function(remainVisible){
        if(this.editing){
            this.setValue(this.startValue);
            if(remainVisible !== true){
                this.hide();
            }
        }
    },

    // private
    onBlur : function(){
        if(this.allowBlur !== true && this.editing){
            this.completeEdit();
        }
    },

    // private
    onHide : function(){
        if(this.editing){
            this.completeEdit();
            return;
        }
        this.field.blur();
        if(this.field.collapse){
            this.field.collapse();
        }
        this.el.hide();
        if(this.hideEl !== false){
            this.boundEl.show();
        }
        if(Ext.QuickTips){
            Ext.QuickTips.enable();
        }
    },

    /**
     * Sets the data value of the editor
     * @param {Mixed} value Any valid value supported by the underlying field
     */
    setValue : function(v){
        this.field.setValue(v);
    },

    /**
     * Gets the data value of the editor
     * @return {Mixed} The data value
     */
    getValue : function(){
        return this.field.getValue();
    }
});



/**
 * @class Ext.tree.TreePanel
 * @extends Ext.data.Tree

 * @cfg {Boolean} rootVisible false to hide the root node (defaults to true)
 * @cfg {Boolean} lines false to disable tree lines (defaults to true)
 * @cfg {Boolean} enableDD true to enable drag and drop
 * @cfg {Boolean} enableDrag true to enable just drag
 * @cfg {Boolean} enableDrop true to enable just drop
 * @cfg {Object} dragConfig Custom config to pass to the {@link Ext.tree.TreeDragZone} instance
 * @cfg {Object} dropConfig Custom config to pass to the {@link Ext.tree.TreeDropZone} instance
 * @cfg {String} ddGroup The DD group this TreePanel belongs to
 * @cfg {String} ddAppendOnly True if the tree should only allow append drops (use for trees which are sorted)
 * @cfg {Boolean} ddScroll true to enable YUI body scrolling
 * @cfg {Boolean} containerScroll true to register this container with ScrollManager
 * @cfg {Boolean} hlDrop false to disable node highlight on drop (defaults to the value of Ext.enableFx)
 * @cfg {String} hlColor The color of the node highlight (defaults to C3DAF9)
 * @cfg {Boolean} animate true to enable animated expand/collapse (defaults to the value of Ext.enableFx)
 * @cfg {Boolean} singleExpand true if only 1 node per branch may be expanded
 * @cfg {Boolean} selModel A tree selection model to use with this TreePanel (defaults to a {@link Ext.tree.DefaultSelectionModel})
 * @cfg {Boolean} loader A TreeLoader for use with this TreePanel
  * @cfg {String} pathSeparator The token used to separate sub-paths in path strings (defaults to '/')
 * @constructor
 * @param {String/HTMLElement/Element} el The container element
 * @param {Object} config
 */
Ext.tree.TreePanel = function(el, config){
   Ext.apply(this, config);
   Ext.tree.TreePanel.superclass.constructor.call(this);
   this.el = Ext.get(el);
    this.el.addClass('x-tree');
   /**
    * Read-only. The id of the container element becomes this TreePanel's id.
    */
   this.id = this.el.id;
   this.addEvents({
        /**
        * @event beforeload
        * Fires before a node is loaded, return false to cancel
        * @param {Node} node The node being loaded
        */
        "beforeload" : true,
        /**
        * @event load
        * Fires when a node is loaded
        * @param {Node} node The node that was loaded
        */
        "load" : true,
        /**
        * @event textchange
        * Fires when the text for a node is changed
        * @param {Node} node The node
        * @param {String} text The new text
        * @param {String} oldText The old text
        */
        "textchange" : true,
        /**
        * @event beforeexpand
        * Fires before a node is expanded, return false to cancel.
        * @param {Node} node The node
        * @param {Boolean} deep
        * @param {Boolean} anim
        */
        "beforeexpand" : true,
        /**
        * @event beforecollapse
        * Fires before a node is collapsed, return false to cancel.
        * @param {Node} node The node
        * @param {Boolean} deep
        * @param {Boolean} anim
        */
        "beforecollapse" : true,
        /**
        * @event expand
        * Fires when a node is expanded
        * @param {Node} node The node
        */
        "expand" : true,
        /**
        * @event disabledchange
        * Fires when the disabled status of a node changes
        * @param {Node} node The node
        * @param {Boolean} disabled
        */
        "disabledchange" : true,
        /**
        * @event collapse
        * Fires when a node is collapsed
        * @param {Node} node The node
        */
        "collapse" : true,
        /**
        * @event beforeclick
        * Fires before click processing on a node. Return false to cancel the default action.
        * @param {Node} node The node
        * @param {Ext.EventObject} e The event object
        */
        "beforeclick":true,
        /**
        * @event checkchange
        * Fires when a node with a checkbox's checked property changes
        * @param {Node} this This node
        * @param {Boolean} checked
        */
        "checkchange":true,
        /**
        * @event click
        * Fires when a node is clicked
        * @param {Node} node The node
        * @param {Ext.EventObject} e The event object
        */
        "click":true,
        /**
        * @event dblclick
        * Fires when a node is double clicked
        * @param {Node} node The node
        * @param {Ext.EventObject} e The event object
        */
        "dblclick":true,
        /**
        * @event contextmenu
        * Fires when a node is right clicked
        * @param {Node} node The node
        * @param {Ext.EventObject} e The event object
        */
        "contextmenu":true,
        /**
        * @event beforechildrenrendered
        * Fires right before the child nodes for a node are rendered
        * @param {Node} node The node
        */
        "beforechildrenrendered":true,
       /**
	     * @event startdrag
	     * Fires when a node starts being dragged
	     * @param {Ext.tree.TreePanel} this
	     * @param {Ext.tree.TreeNode} node
	     * @param {event} e The raw browser event
	     */
	    "startdrag" : true,
	    /**
	     * @event enddrag
	     * Fires when a drag operation is complete
	     * @param {Ext.tree.TreePanel} this
	     * @param {Ext.tree.TreeNode} node
	     * @param {event} e The raw browser event
	     */
	    "enddrag" : true,
	    /**
	     * @event dragdrop
	     * Fires when a dragged node is dropped on a valid DD target
	     * @param {Ext.tree.TreePanel} this
	     * @param {Ext.tree.TreeNode} node
	     * @param {DD} dd The dd it was dropped on
	     * @param {event} e The raw browser event
	     */
	    "dragdrop" : true,
	    /**
	     * @event beforenodedrop
	     * Fires when a DD object is dropped on a node in this tree for preprocessing. Return false to cancel the drop. The dropEvent
	     * passed to handlers has the following properties:<br />
	     * <ul style="padding:5px;padding-left:16px;">
	     * <li>tree - The TreePanel</li>
	     * <li>target - The node being targeted for the drop</li>
	     * <li>data - The drag data from the drag source</li>
	     * <li>point - The point of the drop - append, above or below</li>
	     * <li>source - The drag source</li>
	     * <li>rawEvent - Raw mouse event</li>
	     * <li>dropNode - Drop node(s) provided by the source <b>OR</b> you can supply node(s)
	     * to be inserted by setting them on this object.</li>
	     * <li>cancel - Set this to true to cancel the drop.</li>
	     * </ul>
	     * @param {Object} dropEvent
	     */
	    "beforenodedrop" : true,
	    /**
	     * @event nodedrop
	     * Fires after a DD object is dropped on a node in this tree. The dropEvent
	     * passed to handlers has the following properties:<br />
	     * <ul style="padding:5px;padding-left:16px;">
	     * <li>tree - The TreePanel</li>
	     * <li>target - The node being targeted for the drop</li>
	     * <li>data - The drag data from the drag source</li>
	     * <li>point - The point of the drop - append, above or below</li>
	     * <li>source - The drag source</li>
	     * <li>rawEvent - Raw mouse event</li>
	     * <li>dropNode - Dropped node(s).</li>
	     * </ul>
	     * @param {Object} dropEvent
	     */
	    "nodedrop" : true,
	     /**
	     * @event nodedragover
	     * Fires when a tree node is being targeted for a drag drop, return false to signal drop not allowed. The dragOverEvent
	     * passed to handlers has the following properties:<br />
	     * <ul style="padding:5px;padding-left:16px;">
	     * <li>tree - The TreePanel</li>
	     * <li>target - The node being targeted for the drop</li>
	     * <li>data - The drag data from the drag source</li>
	     * <li>point - The point of the drop - append, above or below</li>
	     * <li>source - The drag source</li>
	     * <li>rawEvent - Raw mouse event</li>
	     * <li>dropNode - Drop node(s) provided by the source.</li>
	     * <li>cancel - Set this to true to signal drop not allowed.</li>
	     * </ul>
	     * @param {Object} dragOverEvent
	     */
	    "nodedragover" : true
   });
   if(this.singleExpand){
       this.on("beforeexpand", this.restrictExpand, this);
   }
};
Ext.extend(Ext.tree.TreePanel, Ext.data.Tree, {
    rootVisible : true,
    animate: Ext.enableFx,
    lines : true,
    enableDD : false,
    hlDrop : Ext.enableFx,

    // private
    restrictExpand : function(node){
        var p = node.parentNode;
        if(p){
            if(p.expandedChild && p.expandedChild.parentNode == p){
                p.expandedChild.collapse();
            }
            p.expandedChild = node;
        }
    },

    // private override
    setRootNode : function(node){
        Ext.tree.TreePanel.superclass.setRootNode.call(this, node);
        if(!this.rootVisible){
            node.ui = new Ext.tree.RootTreeNodeUI(node);
        }
        return node;
    },

    /**
     * Returns the container element for this TreePanel
     */
    getEl : function(){
        return this.el;
    },

    /**
     * Returns the default TreeLoader for this TreePanel
     */
    getLoader : function(){
        return this.loader;
    },

    /**
     * Expand all nodes
     */
    expandAll : function(){
        this.root.expand(true);
    },

    /**
     * Collapse all nodes
     */
    collapseAll : function(){
        this.root.collapse(true);
    },

    /**
     * Returns the selection model used by this TreePanel
     */
    getSelectionModel : function(){
        if(!this.selModel){
            this.selModel = new Ext.tree.DefaultSelectionModel();
        }
        return this.selModel;
    },

    /**
     * Retrieve an array of checked nodes, or an array of a specific attribute of checked nodes (e.g. "id")
     * @param {String} attribute (optional) Defaults to null (return the actual nodes)
     * @param {TreeNode} startNode (optional) The node to start from, defaults to the root
     * @return {Array}
     */
    getChecked : function(a, startNode){
        startNode = startNode || this.root;
        var r = [];
        var f = function(){
            if(this.attributes.checked){
                r.push(!a ? this : (a == 'id' ? this.id : this.attributes[a]));
            }
        }
        startNode.cascade(f);
        return r;
    },

    /**
     * Expands a specified path in this TreePanel. A path can be retrieved from a node with {@link Ext.data.Node#getPath}
     * @param {String} path
     * @param {String} attr (optional) The attribute used in the path (see {@link Ext.data.Node#getPath} for more info)
     * @param {Function} callback (optional) The callback to call when the expand is complete. The callback will be called with
     * (bSuccess, oLastNode) where bSuccess is if the expand was successful and oLastNode is the last node that was expanded.
     */
    expandPath : function(path, attr, callback){
        attr = attr || "id";
        var keys = path.split(this.pathSeparator);
        var curNode = this.root;
        if(curNode.attributes[attr] != keys[1]){ // invalid root
            if(callback){
                callback(false, null);
            }
            return;
        }
        var index = 1;
        var f = function(){
            if(++index == keys.length){
                if(callback){
                    callback(true, curNode);
                }
                return;
            }
            var c = curNode.findChild(attr, keys[index]);
            if(!c){
                if(callback){
                    callback(false, curNode);
                }
                return;
            }
            curNode = c;
            c.expand(false, false, f);
        };
        curNode.expand(false, false, f);
    },

    /**
     * Selects the node in this tree at the specified path. A path can be retrieved from a node with {@link Ext.data.Node#getPath}
     * @param {String} path
     * @param {String} attr (optional) The attribute used in the path (see {@link Ext.data.Node#getPath} for more info)
     * @param {Function} callback (optional) The callback to call when the selection is complete. The callback will be called with
     * (bSuccess, oSelNode) where bSuccess is if the selection was successful and oSelNode is the selected node.
     */
    selectPath : function(path, attr, callback){
        attr = attr || "id";
        var keys = path.split(this.pathSeparator);
        var v = keys.pop();
        if(keys.length > 0){
            var f = function(success, node){
                if(success && node){
                    var n = node.findChild(attr, v);
                    if(n){
                        n.select();
                        if(callback){
                            callback(true, n);
                        }
                    }else if(callback){
                        callback(false, n);
                    }
                }else{
                    if(callback){
                        callback(false, n);
                    }
                }
            };
            this.expandPath(keys.join(this.pathSeparator), attr, f);
        }else{
            this.root.select();
            if(callback){
                callback(true, this.root);
            }
        }
    },

    getTreeEl : function(){
        return this.el;
    },

    /**
     * Trigger rendering of this TreePanel
     */
    render : function(){
        this.innerCt = this.el.createChild({tag:"ul",
               cls:"x-tree-root-ct " +
               (this.lines ? "x-tree-lines" : "x-tree-no-lines")});

        if(this.containerScroll){
            Ext.dd.ScrollManager.register(this.el);
        }
        if((this.enableDD || this.enableDrop) && !this.dropZone){
           /**
            * The dropZone used by this tree if drop is enabled
            * @type Ext.tree.TreeDropZone
            */
             this.dropZone = new Ext.tree.TreeDropZone(this, this.dropConfig || {
               ddGroup: this.ddGroup || "TreeDD", appendOnly: this.ddAppendOnly === true
           });
        }
        if((this.enableDD || this.enableDrag) && !this.dragZone){
           /**
            * The dragZone used by this tree if drag is enabled
            * @type Ext.tree.TreeDragZone
            */
            this.dragZone = new Ext.tree.TreeDragZone(this, this.dragConfig || {
               ddGroup: this.ddGroup || "TreeDD",
               scroll: this.ddScroll
           });
        }
        this.getSelectionModel().init(this);
        this.root.render();
        if(!this.rootVisible){
            this.root.renderChildren();
        }
        return this;
    }
});



/**
 * @class Ext.tree.DefaultSelectionModel
 * @extends Ext.util.Observable
 * The default single selection for a TreePanel.
 */
Ext.tree.DefaultSelectionModel = function(){
   this.selNode = null;
   
   this.addEvents({
       /**
        * @event selectionchange
        * Fires when the selected node changes
        * @param {DefaultSelectionModel} this
        * @param {TreeNode} node the new selection
        */
       "selectionchange" : true,

       /**
        * @event beforeselect
        * Fires before the selected node changes, return false to cancel the change
        * @param {DefaultSelectionModel} this
        * @param {TreeNode} node the new selection
        * @param {TreeNode} node the old selection
        */
       "beforeselect" : true
   });
};

Ext.extend(Ext.tree.DefaultSelectionModel, Ext.util.Observable, {
    init : function(tree){
        this.tree = tree;
        tree.getTreeEl().on("keydown", this.onKeyDown, this);
        tree.on("click", this.onNodeClick, this);
    },
    
    onNodeClick : function(node, e){
        this.select(node);
    },
    
    /**
     * Select a node.
     * @param {TreeNode} node The node to select
     * @return {TreeNode} The selected node
     */
    select : function(node){
        var last = this.selNode;
        if(last != node && this.fireEvent('beforeselect', this, node, last) !== false){
            if(last){
                last.ui.onSelectedChange(false);
            }
            this.selNode = node;
            node.ui.onSelectedChange(true);
            this.fireEvent("selectionchange", this, node, last);
        }
        return node;
    },
    
    /**
     * Deselect a node.
     * @param {TreeNode} node The node to unselect
     */
    unselect : function(node){
        if(this.selNode == node){
            this.clearSelections();
        }    
    },
    
    /**
     * Clear all selections
     */
    clearSelections : function(){
        var n = this.selNode;
        if(n){
            n.ui.onSelectedChange(false);
            this.selNode = null;
            this.fireEvent("selectionchange", this, null);
        }
        return n;
    },
    
    /**
     * Get the selected node
     * @return {TreeNode} The selected node
     */
    getSelectedNode : function(){
        return this.selNode;    
    },
    
    /**
     * Returns true if the node is selected
     * @param {TreeNode} node The node to check
     * @return {Boolean}
     */
    isSelected : function(node){
        return this.selNode == node;  
    },

    /**
     * Selects the node above the selected node in the tree, intelligently walking the nodes
     * @return TreeNode The new selection
     */
    selectPrevious : function(){
        var s = this.selNode || this.lastSelNode;
        if(!s){
            return null;
        }
        var ps = s.previousSibling;
        if(ps){
            if(!ps.isExpanded() || ps.childNodes.length < 1){
                return this.select(ps);
            } else{
                var lc = ps.lastChild;
                while(lc && lc.isExpanded() && lc.childNodes.length > 0){
                    lc = lc.lastChild;
                }
                return this.select(lc);
            }
        } else if(s.parentNode && (this.tree.rootVisible || !s.parentNode.isRoot)){
            return this.select(s.parentNode);
        }
        return null;
    },

    /**
     * Selects the node above the selected node in the tree, intelligently walking the nodes
     * @return TreeNode The new selection
     */
    selectNext : function(){
        var s = this.selNode || this.lastSelNode;
        if(!s){
            return null;
        }
        if(s.firstChild && s.isExpanded()){
             return this.select(s.firstChild);
         }else if(s.nextSibling){
             return this.select(s.nextSibling);
         }else if(s.parentNode){
            var newS = null;
            s.parentNode.bubble(function(){
                if(this.nextSibling){
                    newS = this.getOwnerTree().selModel.select(this.nextSibling);
                    return false;
                }
            });
            return newS;
         }
        return null;
    },

    onKeyDown : function(e){
        var s = this.selNode || this.lastSelNode;
        // undesirable, but required
        var sm = this;
        if(!s){
            return;
        }
        var k = e.getKey();
        switch(k){
             case e.DOWN:
                 e.stopEvent();
                 this.selectNext();
             break;
             case e.UP:
                 e.stopEvent();
                 this.selectPrevious();
             break;
             case e.RIGHT:
                 e.preventDefault();
                 if(s.hasChildNodes()){
                     if(!s.isExpanded()){
                         s.expand();
                     }else if(s.firstChild){
                         this.select(s.firstChild, e);
                     }
                 }
             break;
             case e.LEFT:
                 e.preventDefault();
                 if(s.hasChildNodes() && s.isExpanded()){
                     s.collapse();
                 }else if(s.parentNode && (this.tree.rootVisible || s.parentNode != this.tree.getRootNode())){
                     this.select(s.parentNode, e);
                 }
             break;
        };
    }
});

/**
 * @class Ext.tree.MultiSelectionModel
 * @extends Ext.util.Observable
 * Multi selection for a TreePanel.
 */
Ext.tree.MultiSelectionModel = function(){
   this.selNodes = [];
   this.selMap = {};
   this.addEvents({
       /**
        * @event selectionchange
        * Fires when the selected nodes change
        * @param {MultiSelectionModel} this
        * @param {Array} nodes Array of the selected nodes
        */
       "selectionchange" : true
   });
};

Ext.extend(Ext.tree.MultiSelectionModel, Ext.util.Observable, {
    init : function(tree){
        this.tree = tree;
        tree.getTreeEl().on("keydown", this.onKeyDown, this);
        tree.on("click", this.onNodeClick, this);
    },
    
    onNodeClick : function(node, e){
        this.select(node, e, e.ctrlKey);
    },
    
    /**
     * Select a node.
     * @param {TreeNode} node The node to select
     * @param {EventObject} e (optional) An event associated with the selection
     * @param {Boolean} keepExisting True to retain existing selections
     * @return {TreeNode} The selected node
     */
    select : function(node, e, keepExisting){
        if(keepExisting !== true){
            this.clearSelections(true);
        }
        if(this.isSelected(node)){
            this.lastSelNode = node;
            return node;
        }
        this.selNodes.push(node);
        this.selMap[node.id] = node;
        this.lastSelNode = node;
        node.ui.onSelectedChange(true);
        this.fireEvent("selectionchange", this, this.selNodes);
        return node;
    },
    
    /**
     * Deselect a node.
     * @param {TreeNode} node The node to unselect
     */
    unselect : function(node){
        if(this.selMap[node.id]){
            node.ui.onSelectedChange(false);
            var sn = this.selNodes;
            var index = -1;
            if(sn.indexOf){
                index = sn.indexOf(node);
            }else{
                for(var i = 0, len = sn.length; i < len; i++){
                    if(sn[i] == node){
                        index = i;
                        break;
                    }
                }
            }
            if(index != -1){
                this.selNodes.splice(index, 1);
            }
            delete this.selMap[node.id];
            this.fireEvent("selectionchange", this, this.selNodes);
        }
    },
    
    /**
     * Clear all selections
     */
    clearSelections : function(suppressEvent){
        var sn = this.selNodes;
        if(sn.length > 0){
            for(var i = 0, len = sn.length; i < len; i++){
                sn[i].ui.onSelectedChange(false);
            }
            this.selNodes = [];
            this.selMap = {};
            if(suppressEvent !== true){
                this.fireEvent("selectionchange", this, this.selNodes);
            }
        }
    },
    
    /**
     * Returns true if the node is selected
     * @param {TreeNode} node The node to check
     * @return {Boolean}
     */
    isSelected : function(node){
        return this.selMap[node.id] ? true : false;  
    },
    
    /**
     * Returns an array of the selected nodes
     * @return {Array}
     */
    getSelectedNodes : function(){
        return this.selNodes;    
    },

    onKeyDown : Ext.tree.DefaultSelectionModel.prototype.onKeyDown,

    selectNext : Ext.tree.DefaultSelectionModel.prototype.selectNext,

    selectPrevious : Ext.tree.DefaultSelectionModel.prototype.selectPrevious
});



/**
 * @class Ext.tree.TreeNode
 * @extends Ext.data.Node
 * @cfg {String} text The text for this node
 * @cfg {Boolean} expanded true to start the node expanded
 * @cfg {Boolean} allowDrag false to make this node undraggable if DD is on (defaults to true)
 * @cfg {Boolean} allowDrop false if this node cannot be drop on
 * @cfg {Boolean} disabled true to start the node disabled
 * @cfg {String} icon The path to an icon for the node. The preferred way to do this
 * is to use the cls or iconCls attributes and add the icon via a CSS background image.
 * @cfg {String} cls A css class to be added to the node
 * @cfg {String} iconCls A css class to be added to the nodes icon element for applying css background images
 * @cfg {String} href URL of the link used for the node (defaults to #)
 * @cfg {String} hrefTarget target frame for the link
 * @cfg {String} qtip An Ext QuickTip for the node
 * @cfg {String} qtipCfg An Ext QuickTip config for the node (used instead of qtip)
 * @cfg {Boolean} singleClickExpand True for single click expand on this node
 * @cfg {Function} uiProvider A UI <b>class</b> to use for this node (defaults to Ext.tree.TreeNodeUI)
 * @cfg {Boolean} checked True to render a checked checkbox for this node, false to render an unchecked checkbox
 * (defaults to undefined with no checkbox rendered)
 * @constructor
 * @param {Object/String} attributes The attributes/config for the node or just a string with the text for the node
 */
Ext.tree.TreeNode = function(attributes){
    attributes = attributes || {};
    if(typeof attributes == "string"){
        attributes = {text: attributes};
    }
    this.childrenRendered = false;
    this.rendered = false;
    Ext.tree.TreeNode.superclass.constructor.call(this, attributes);
    this.expanded = attributes.expanded === true;
    this.isTarget = attributes.isTarget !== false;
    this.draggable = attributes.draggable !== false && attributes.allowDrag !== false;
    this.allowChildren = attributes.allowChildren !== false && attributes.allowDrop !== false;

    /**
     * Read-only. The text for this node. To change it use setText().
     * @type String
     */
    this.text = attributes.text;
    /**
     * True if this node is disabled.
     * @type Boolean
     */
    this.disabled = attributes.disabled === true;

    this.addEvents({
        /**
        * @event textchange
        * Fires when the text for this node is changed
        * @param {Node} this This node
        * @param {String} text The new text
        * @param {String} oldText The old text
        */
        "textchange" : true,
        /**
        * @event beforeexpand
        * Fires before this node is expanded, return false to cancel.
        * @param {Node} this This node
        * @param {Boolean} deep
        * @param {Boolean} anim
        */
        "beforeexpand" : true,
        /**
        * @event beforecollapse
        * Fires before this node is collapsed, return false to cancel.
        * @param {Node} this This node
        * @param {Boolean} deep
        * @param {Boolean} anim
        */
        "beforecollapse" : true,
        /**
        * @event expand
        * Fires when this node is expanded
        * @param {Node} this This node
        */
        "expand" : true,
        /**
        * @event disabledchange
        * Fires when the disabled status of this node changes
        * @param {Node} this This node
        * @param {Boolean} disabled
        */
        "disabledchange" : true,
        /**
        * @event collapse
        * Fires when this node is collapsed
        * @param {Node} this This node
        */
        "collapse" : true,
        /**
        * @event beforeclick
        * Fires before click processing. Return false to cancel the default action.
        * @param {Node} this This node
        * @param {Ext.EventObject} e The event object
        */
        "beforeclick":true,
        /**
        * @event checkchange
        * Fires when a node with a checkbox's checked property changes
        * @param {Node} this This node
        * @param {Boolean} checked
        */
        "checkchange":true,
        /**
        * @event click
        * Fires when this node is clicked
        * @param {Node} this This node
        * @param {Ext.EventObject} e The event object
        */
        "click":true,
        /**
        * @event dblclick
        * Fires when this node is double clicked
        * @param {Node} this This node
        * @param {Ext.EventObject} e The event object
        */
        "dblclick":true,
        /**
        * @event contextmenu
        * Fires when this node is right clicked
        * @param {Node} this This node
        * @param {Ext.EventObject} e The event object
        */
        "contextmenu":true,
        /**
        * @event beforechildrenrendered
        * Fires right before the child nodes for this node are rendered
        * @param {Node} this This node
        */
        "beforechildrenrendered":true
    });

    var uiClass = this.attributes.uiProvider || Ext.tree.TreeNodeUI;

    /**
     * Read-only. The UI for this node
     * @type TreeNodeUI
     */
    this.ui = new uiClass(this);
};
Ext.extend(Ext.tree.TreeNode, Ext.data.Node, {
    preventHScroll: true,
    /**
     * Returns true if this node is expanded
     * @return {Boolean}
     */
    isExpanded : function(){
        return this.expanded;
    },

    /**
     * Returns the UI object for this node
     * @return {TreeNodeUI}
     */
    getUI : function(){
        return this.ui;
    },

    // private override
    setFirstChild : function(node){
        var of = this.firstChild;
        Ext.tree.TreeNode.superclass.setFirstChild.call(this, node);
        if(this.childrenRendered && of && node != of){
            of.renderIndent(true, true);
        }
        if(this.rendered){
            this.renderIndent(true, true);
        }
    },

    // private override
    setLastChild : function(node){
        var ol = this.lastChild;
        Ext.tree.TreeNode.superclass.setLastChild.call(this, node);
        if(this.childrenRendered && ol && node != ol){
            ol.renderIndent(true, true);
        }
        if(this.rendered){
            this.renderIndent(true, true);
        }
    },

    // these methods are overridden to provide lazy rendering support
    // private override
    appendChild : function(){
        var node = Ext.tree.TreeNode.superclass.appendChild.apply(this, arguments);
        if(node && this.childrenRendered){
            node.render();
        }
        this.ui.updateExpandIcon();
        return node;
    },

    // private override
    removeChild : function(node){
        this.ownerTree.getSelectionModel().unselect(node);
        Ext.tree.TreeNode.superclass.removeChild.apply(this, arguments);
        // if it's been rendered remove dom node
        if(this.childrenRendered){
            node.ui.remove();
        }
        if(this.childNodes.length < 1){
            this.collapse(false, false);
        }else{
            this.ui.updateExpandIcon();
        }
        if(!this.firstChild) {
            this.childrenRendered = false;
        }
        return node;
    },

    // private override
    insertBefore : function(node, refNode){
        var newNode = Ext.tree.TreeNode.superclass.insertBefore.apply(this, arguments);
        if(newNode && refNode && this.childrenRendered){
            node.render();
        }
        this.ui.updateExpandIcon();
        return newNode;
    },

    /**
     * Sets the text for this node
     * @param {String} text
     */
    setText : function(text){
        var oldText = this.text;
        this.text = text;
        this.attributes.text = text;
        if(this.rendered){ // event without subscribing
            this.ui.onTextChange(this, text, oldText);
        }
        this.fireEvent("textchange", this, text, oldText);
    },

    /**
     * Triggers selection of this node
     */
    select : function(){
        this.getOwnerTree().getSelectionModel().select(this);
    },

    /**
     * Triggers deselection of this node
     */
    unselect : function(){
        this.getOwnerTree().getSelectionModel().unselect(this);
    },

    /**
     * Returns true if this node is selected
     * @return {Boolean}
     */
    isSelected : function(){
        return this.getOwnerTree().getSelectionModel().isSelected(this);
    },

    /**
     * Expand this node.
     * @param {Boolean} deep (optional) True to expand all children as well
     * @param {Boolean} anim (optional) false to cancel the default animation
     * @param {Function} callback (optional) A callback to be called when
     * expanding this node completes (does not wait for deep expand to complete).
     * Called with 1 parameter, this node.
     */
    expand : function(deep, anim, callback){
        if(!this.expanded){
            if(this.fireEvent("beforeexpand", this, deep, anim) === false){
                return;
            }
            if(!this.childrenRendered){
                this.renderChildren();
            }
            this.expanded = true;
            if(!this.isHiddenRoot() && (this.getOwnerTree().animate && anim !== false) || anim){
                this.ui.animExpand(function(){
                    this.fireEvent("expand", this);
                    if(typeof callback == "function"){
                        callback(this);
                    }
                    if(deep === true){
                        this.expandChildNodes(true);
                    }
                }.createDelegate(this));
                return;
            }else{
                this.ui.expand();
                this.fireEvent("expand", this);
                if(typeof callback == "function"){
                    callback(this);
                }
            }
        }else{
           if(typeof callback == "function"){
               callback(this);
           }
        }
        if(deep === true){
            this.expandChildNodes(true);
        }
    },

    isHiddenRoot : function(){
        return this.isRoot && !this.getOwnerTree().rootVisible;
    },

    /**
     * Collapse this node.
     * @param {Boolean} deep (optional) True to collapse all children as well
     * @param {Boolean} anim (optional) false to cancel the default animation
     */
    collapse : function(deep, anim){
        if(this.expanded && !this.isHiddenRoot()){
            if(this.fireEvent("beforecollapse", this, deep, anim) === false){
                return;
            }
            this.expanded = false;
            if((this.getOwnerTree().animate && anim !== false) || anim){
                this.ui.animCollapse(function(){
                    this.fireEvent("collapse", this);
                    if(deep === true){
                        this.collapseChildNodes(true);
                    }
                }.createDelegate(this));
                return;
            }else{
                this.ui.collapse();
                this.fireEvent("collapse", this);
            }
        }
        if(deep === true){
            var cs = this.childNodes;
            for(var i = 0, len = cs.length; i < len; i++) {
            	cs[i].collapse(true, false);
            }
        }
    },

    // private
    delayedExpand : function(delay){
        if(!this.expandProcId){
            this.expandProcId = this.expand.defer(delay, this);
        }
    },

    // private
    cancelExpand : function(){
        if(this.expandProcId){
            clearTimeout(this.expandProcId);
        }
        this.expandProcId = false;
    },

    /**
     * Toggles expanded/collapsed state of the node
     */
    toggle : function(){
        if(this.expanded){
            this.collapse();
        }else{
            this.expand();
        }
    },

    /**
     * Ensures all parent nodes are expanded
     */
    ensureVisible : function(callback){
        var tree = this.getOwnerTree();
        tree.expandPath(this.parentNode.getPath(), false, function(){
            tree.getTreeEl().scrollChildIntoView(this.ui.anchor);
            Ext.callback(callback);
        }.createDelegate(this));
    },

    /**
     * Expand all child nodes
     * @param {Boolean} deep (optional) true if the child nodes should also expand their child nodes
     */
    expandChildNodes : function(deep){
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++) {
        	cs[i].expand(deep);
        }
    },

    /**
     * Collapse all child nodes
     * @param {Boolean} deep (optional) true if the child nodes should also collapse their child nodes
     */
    collapseChildNodes : function(deep){
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++) {
        	cs[i].collapse(deep);
        }
    },

    /**
     * Disables this node
     */
    disable : function(){
        this.disabled = true;
        this.unselect();
        if(this.rendered && this.ui.onDisableChange){ // event without subscribing
            this.ui.onDisableChange(this, true);
        }
        this.fireEvent("disabledchange", this, true);
    },

    /**
     * Enables this node
     */
    enable : function(){
        this.disabled = false;
        if(this.rendered && this.ui.onDisableChange){ // event without subscribing
            this.ui.onDisableChange(this, false);
        }
        this.fireEvent("disabledchange", this, false);
    },

    // private
    renderChildren : function(suppressEvent){
        if(suppressEvent !== false){
            this.fireEvent("beforechildrenrendered", this);
        }
        var cs = this.childNodes;
        for(var i = 0, len = cs.length; i < len; i++){
            cs[i].render(true);
        }
        this.childrenRendered = true;
    },

    // private
    sort : function(fn, scope){
        Ext.tree.TreeNode.superclass.sort.apply(this, arguments);
        if(this.childrenRendered){
            var cs = this.childNodes;
            for(var i = 0, len = cs.length; i < len; i++){
                cs[i].render(true);
            }
        }
    },

    // private
    render : function(bulkRender){
        this.ui.render(bulkRender);
        if(!this.rendered){
            this.rendered = true;
            if(this.expanded){
                this.expanded = false;
                this.expand(false, false);
            }
        }
    },

    // private
    renderIndent : function(deep, refresh){
        if(refresh){
            this.ui.childIndent = null;
        }
        this.ui.renderIndent();
        if(deep === true && this.childrenRendered){
            var cs = this.childNodes;
            for(var i = 0, len = cs.length; i < len; i++){
                cs[i].renderIndent(true, refresh);
            }
        }
    }
});



/**
 * @class Ext.tree.AsyncTreeNode
 * @extends Ext.tree.TreeNode
 * @cfg {TreeLoader} loader A TreeLoader to be used by this node (defaults to the loader defined on the tree)
 * @constructor
 * @param {Object/String} attributes The attributes/config for the node or just a string with the text for the node 
 */
 Ext.tree.AsyncTreeNode = function(config){
    this.loaded = false;
    this.loading = false;
    Ext.tree.AsyncTreeNode.superclass.constructor.apply(this, arguments);
    /**
    * @event beforeload
    * Fires before this node is loaded, return false to cancel
    * @param {Node} this This node
    */
    this.addEvents({'beforeload':true, 'load': true});
    /**
    * @event load
    * Fires when this node is loaded
    * @param {Node} this This node
    */
    /**
     * The loader used by this node (defaults to using the tree's defined loader)
     * @type TreeLoader
     * @property loader
     */
};
Ext.extend(Ext.tree.AsyncTreeNode, Ext.tree.TreeNode, {
    expand : function(deep, anim, callback){
        if(this.loading){ // if an async load is already running, waiting til it's done
            var timer;
            var f = function(){
                if(!this.loading){ // done loading
                    clearInterval(timer);
                    this.expand(deep, anim, callback);
                }
            }.createDelegate(this);
            timer = setInterval(f, 200);
            return;
        }
        if(!this.loaded){
            if(this.fireEvent("beforeload", this) === false){
                return;
            }
            this.loading = true;
            this.ui.beforeLoad(this);
            var loader = this.loader || this.attributes.loader || this.getOwnerTree().getLoader();
            if(loader){
                loader.load(this, this.loadComplete.createDelegate(this, [deep, anim, callback]));
                return;
            }
        }
        Ext.tree.AsyncTreeNode.superclass.expand.call(this, deep, anim, callback);
    },
    
    /**
     * Returns true if this node is currently loading
     * @return {Boolean}
     */
    isLoading : function(){
        return this.loading;  
    },
    
    loadComplete : function(deep, anim, callback){
        this.loading = false;
        this.loaded = true;
        this.ui.afterLoad(this);
        this.fireEvent("load", this);
        this.expand(deep, anim, callback);
    },
    
    /**
     * Returns true if this node has been loaded
     * @return {Boolean}
     */
    isLoaded : function(){
        return this.loaded;
    },
    
    hasChildNodes : function(){
        if(!this.isLeaf() && !this.loaded){
            return true;
        }else{
            return Ext.tree.AsyncTreeNode.superclass.hasChildNodes.call(this);
        }
    },

    /**
     * Trigger a reload for this node
     * @param {Function} callback
     */
    reload : function(callback){
        this.collapse(false, false);
        while(this.firstChild){
            this.removeChild(this.firstChild);
        }
        this.childrenRendered = false;
        this.loaded = false;
        if(this.isHiddenRoot()){
            this.expanded = false;
        }
        this.expand(false, false, callback);
    }
});



/**
 * The TreeNode UI implementation is separate from the
 * tree implementation. Unless you are customizing the tree UI,
 * you should never have to use this directly.
 */
Ext.tree.TreeNodeUI = function(node){
    this.node = node;
    this.rendered = false;
    this.animating = false;
    this.emptyIcon = Ext.BLANK_IMAGE_URL;
};

Ext.tree.TreeNodeUI.prototype = {
    removeChild : function(node){
        if(this.rendered){
            this.ctNode.removeChild(node.ui.getEl());
        }
    },

    beforeLoad : function(){
         this.addClass("x-tree-node-loading");
    },

    afterLoad : function(){
         this.removeClass("x-tree-node-loading");
    },

    onTextChange : function(node, text, oldText){
        if(this.rendered){
            this.textNode.innerHTML = text;
        }
    },

    onDisableChange : function(node, state){
        this.disabled = state;
        if(state){
            this.addClass("x-tree-node-disabled");
        }else{
            this.removeClass("x-tree-node-disabled");
        }
    },

    onSelectedChange : function(state){
        if(state){
            this.focus();
            this.addClass("x-tree-selected");
        }else{
            //this.blur();
            this.removeClass("x-tree-selected");
        }
    },

    onMove : function(tree, node, oldParent, newParent, index, refNode){
        this.childIndent = null;
        if(this.rendered){
            var targetNode = newParent.ui.getContainer();
            if(!targetNode){//target not rendered
                this.holder = document.createElement("div");
                this.holder.appendChild(this.wrap);
                return;
            }
            var insertBefore = refNode ? refNode.ui.getEl() : null;
            if(insertBefore){
                targetNode.insertBefore(this.wrap, insertBefore);
            }else{
                targetNode.appendChild(this.wrap);
            }
            this.node.renderIndent(true);
        }
    },

    addClass : function(cls){
        if(this.elNode){
            Ext.fly(this.elNode).addClass(cls);
        }
    },

    removeClass : function(cls){
        if(this.elNode){
            Ext.fly(this.elNode).removeClass(cls);
        }
    },

    remove : function(){
        if(this.rendered){
            this.holder = document.createElement("div");
            this.holder.appendChild(this.wrap);
        }
    },

    fireEvent : function(){
        return this.node.fireEvent.apply(this.node, arguments);
    },

    initEvents : function(){
        this.node.on("move", this.onMove, this);
        var E = Ext.EventManager;
        var a = this.anchor;

        var el = Ext.fly(a, '_treeui');

        if(Ext.isOpera){ // opera render bug ignores the CSS
            el.setStyle("text-decoration", "none");
        }

        el.on("click", this.onClick, this);
        el.on("dblclick", this.onDblClick, this);

        if(this.checkbox){
            Ext.EventManager.on(this.checkbox,
                    Ext.isIE ? 'click' : 'change', this.onCheckChange, this);
        }

        el.on("contextmenu", this.onContextMenu, this);

        var icon = Ext.fly(this.iconNode);
        icon.on("click", this.onClick, this);
        icon.on("dblclick", this.onDblClick, this);
        icon.on("contextmenu", this.onContextMenu, this);
        E.on(this.ecNode, "click", this.ecClick, this, true);

        if(this.node.disabled){
            this.addClass("x-tree-node-disabled");
        }
        if(this.node.hidden){
            this.addClass("x-tree-node-disabled");
        }
        var ot = this.node.getOwnerTree();
        var dd = ot.enableDD || ot.enableDrag || ot.enableDrop;
        if(dd && (!this.node.isRoot || ot.rootVisible)){
            Ext.dd.Registry.register(this.elNode, {
                node: this.node,
                handles: this.getDDHandles(),
                isHandle: false
            });
        }
    },

    getDDHandles : function(){
        return [this.iconNode, this.textNode];
    },

    hide : function(){
        if(this.rendered){
            this.wrap.style.display = "none";
        }
    },

    show : function(){
        if(this.rendered){
            this.wrap.style.display = "";
        }
    },

    onContextMenu : function(e){
        if (this.node.hasListener("contextmenu") || this.node.getOwnerTree().hasListener("contextmenu")) {
            e.preventDefault();
            this.focus();
            this.fireEvent("contextmenu", this.node, e);
        }
    },

    onClick : function(e){
        if(this.dropping){
            e.stopEvent();
            return;
        }
        if(this.fireEvent("beforeclick", this.node, e) !== false){
            if(!this.disabled && this.node.attributes.href){
                this.fireEvent("click", this.node, e);
                return;
            }
            e.preventDefault();
            if(this.disabled){
                return;
            }

            if(this.node.attributes.singleClickExpand && !this.animating && this.node.hasChildNodes()){
                this.node.toggle();
            }

            this.fireEvent("click", this.node, e);
        }else{
            e.stopEvent();
        }
    },

    onDblClick : function(e){
        e.preventDefault();
        if(this.disabled){
            return;
        }
        if(this.checkbox){
            this.toggleCheck();
        }
        if(!this.animating && this.node.hasChildNodes()){
            this.node.toggle();
        }
        this.fireEvent("dblclick", this.node, e);
    },

    onCheckChange : function(){
        var checked = this.checkbox.checked;
        this.node.attributes.checked = checked;
        this.fireEvent('checkchange', this.node, checked);
    },

    ecClick : function(e){
        if(!this.animating && this.node.hasChildNodes()){
            this.node.toggle();
        }
    },

    startDrop : function(){
        this.dropping = true;
    },

    // delayed drop so the click event doesn't get fired on a drop
    endDrop : function(){
       setTimeout(function(){
           this.dropping = false;
       }.createDelegate(this), 50);
    },

    expand : function(){
        this.updateExpandIcon();
        this.ctNode.style.display = "";
    },

    focus : function(){
        if(!this.node.preventHScroll){
            try{this.anchor.focus();
            }catch(e){}
        }else if(!Ext.isIE){
            try{
                var noscroll = this.node.getOwnerTree().getTreeEl().dom;
                var l = noscroll.scrollLeft;
                this.anchor.focus();
                noscroll.scrollLeft = l;
            }catch(e){}
        }
    },

    toggleCheck : function(value){
        var cb = this.checkbox;
        if(cb){
            cb.checked = (value === undefined ? !cb.checked : value);
        }
    },

    blur : function(){
        try{
            this.anchor.blur();
        }catch(e){}
    },

    animExpand : function(callback){
        var ct = Ext.get(this.ctNode);
        ct.stopFx();
        if(!this.node.hasChildNodes()){
            this.updateExpandIcon();
            this.ctNode.style.display = "";
            Ext.callback(callback);
            return;
        }
        this.animating = true;
        this.updateExpandIcon();

        ct.slideIn('t', {
           callback : function(){
               this.animating = false;
               Ext.callback(callback);
            },
            scope: this,
            duration: this.node.ownerTree.duration || .25
        });
    },

    highlight : function(){
        var tree = this.node.getOwnerTree();
        Ext.fly(this.wrap).highlight(
            tree.hlColor || "C3DAF9",
            {endColor: tree.hlBaseColor}
        );
    },

    collapse : function(){
        this.updateExpandIcon();
        this.ctNode.style.display = "none";
    },

    animCollapse : function(callback){
        var ct = Ext.get(this.ctNode);
        ct.enableDisplayMode('block');
        ct.stopFx();

        this.animating = true;
        this.updateExpandIcon();

        ct.slideOut('t', {
            callback : function(){
               this.animating = false;
               Ext.callback(callback);
            },
            scope: this,
            duration: this.node.ownerTree.duration || .25
        });
    },

    getContainer : function(){
        return this.ctNode;
    },

    getEl : function(){
        return this.wrap;
    },

    appendDDGhost : function(ghostNode){
        ghostNode.appendChild(this.elNode.cloneNode(true));
    },

    getDDRepairXY : function(){
        return Ext.lib.Dom.getXY(this.iconNode);
    },

    onRender : function(){
        this.render();
    },

    render : function(bulkRender){
        var n = this.node, a = n.attributes;
        var targetNode = n.parentNode ?
              n.parentNode.ui.getContainer() : n.ownerTree.innerCt.dom;

        if(!this.rendered){
            this.rendered = true;

            this.renderElements(n, a, targetNode, bulkRender);

            if(a.qtip){
               if(this.textNode.setAttributeNS){
                   this.textNode.setAttributeNS("ext", "qtip", a.qtip);
                   if(a.qtipTitle){
                       this.textNode.setAttributeNS("ext", "qtitle", a.qtipTitle);
                   }
               }else{
                   this.textNode.setAttribute("ext:qtip", a.qtip);
                   if(a.qtipTitle){
                       this.textNode.setAttribute("ext:qtitle", a.qtipTitle);
                   }
               }
            }else if(a.qtipCfg){
                a.qtipCfg.target = Ext.id(this.textNode);
                Ext.QuickTips.register(a.qtipCfg);
            }
            this.initEvents();
            if(!this.node.expanded){
                this.updateExpandIcon();
            }
        }else{
            if(bulkRender === true) {
                targetNode.appendChild(this.wrap);
            }
        }
    },

    renderElements : function(n, a, targetNode, bulkRender){
        // add some indent caching, this helps performance when rendering a large tree
        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var cb = typeof a.checked == 'boolean';
        var href = a.href ? a.href : Ext.isGecko ? "" : "#";
        var buf = ['<li class="x-tree-node"><div class="x-tree-node-el ', a.cls,'">',
            '<span class="x-tree-node-indent">',this.indentMarkup,"</span>",
            '<img src="', this.emptyIcon, '" class="x-tree-ec-icon" />',
            '<img src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon',(a.icon ? " x-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on" />',
            cb ? ('<input class="x-tree-node-cb" type="checkbox" ' + (a.checked ? 'checked="checked" />' : ' />')) : '',
            '<a hidefocus="on" href="',href,'" tabIndex="1" ',
             a.hrefTarget ? ' target="'+a.hrefTarget+'"' : "", '><span unselectable="on">',n.text,"</span></a></div>",
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"];

        if(bulkRender !== true && n.nextSibling && n.nextSibling.ui.getEl()){
            this.wrap = Ext.DomHelper.insertHtml("beforeBegin",
                                n.nextSibling.ui.getEl(), buf.join(""));
        }else{
            this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf.join(""));
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        var index = 3;
        if(cb){
            this.checkbox = cs[3];
            index++;
        }
        this.anchor = cs[index];
        this.textNode = cs[index].firstChild;
    },

    getAnchor : function(){
        return this.anchor;
    },

    getTextEl : function(){
        return this.textNode;
    },

    getIconEl : function(){
        return this.iconNode;
    },

    isChecked : function(){
        return this.checkbox ? this.checkbox.checked : false;
    },

    updateExpandIcon : function(){
        if(this.rendered){
            var n = this.node, c1, c2;
            var cls = n.isLast() ? "x-tree-elbow-end" : "x-tree-elbow";
            var hasChild = n.hasChildNodes();
            if(hasChild){
                if(n.expanded){
                    cls += "-minus";
                    c1 = "x-tree-node-collapsed";
                    c2 = "x-tree-node-expanded";
                }else{
                    cls += "-plus";
                    c1 = "x-tree-node-expanded";
                    c2 = "x-tree-node-collapsed";
                }
                if(this.wasLeaf){
                    this.removeClass("x-tree-node-leaf");
                    this.wasLeaf = false;
                }
                if(this.c1 != c1 || this.c2 != c2){
                    Ext.fly(this.elNode).replaceClass(c1, c2);
                    this.c1 = c1; this.c2 = c2;
                }
            }else{
                if(!this.wasLeaf){
                    Ext.fly(this.elNode).replaceClass("x-tree-node-expanded", "x-tree-node-leaf");
                    delete this.c1;
                    delete this.c2;
                    this.wasLeaf = true;
                }
            }
            var ecc = "x-tree-ec-icon "+cls;
            if(this.ecc != ecc){
                this.ecNode.className = ecc;
                this.ecc = ecc;
            }
        }
    },

    getChildIndent : function(){
        if(!this.childIndent){
            var buf = [];
            var p = this.node;
            while(p){
                if(!p.isRoot || (p.isRoot && p.ownerTree.rootVisible)){
                    if(!p.isLast()) {
                        buf.unshift('<img src="'+this.emptyIcon+'" class="x-tree-elbow-line" />');
                    } else {
                        buf.unshift('<img src="'+this.emptyIcon+'" class="x-tree-icon" />');
                    }
                }
                p = p.parentNode;
            }
            this.childIndent = buf.join("");
        }
        return this.childIndent;
    },

    renderIndent : function(){
        if(this.rendered){
            var indent = "";
            var p = this.node.parentNode;
            if(p){
                indent = p.ui.getChildIndent();
            }
            if(this.indentMarkup != indent){ // don't rerender if not required
                this.indentNode.innerHTML = indent;
                this.indentMarkup = indent;
            }
            this.updateExpandIcon();
        }
    }
};

Ext.tree.RootTreeNodeUI = function(){
    Ext.tree.RootTreeNodeUI.superclass.constructor.apply(this, arguments);
};
Ext.extend(Ext.tree.RootTreeNodeUI, Ext.tree.TreeNodeUI, {
    render : function(){
        if(!this.rendered){
            var targetNode = this.node.ownerTree.innerCt.dom;
            this.node.expanded = true;
            targetNode.innerHTML = '<div class="x-tree-root-node"></div>';
            this.wrap = this.ctNode = targetNode.firstChild;
        }
    },
    collapse : function(){
    },
    expand : function(){
    }
});



/**
 * @class Ext.tree.TreeLoader
 * @extends Ext.util.Observable
 * A TreeLoader provides for lazy loading of an {@link Ext.tree.TreeNode}'s child
 * nodes from a specified URL. The response must be a javascript Array definition
 * who's elements are node definition objects. eg:
 * <pre><code>
   [{ 'id': 1, 'text': 'A folder Node', 'leaf': false },
    { 'id': 2, 'text': 'A leaf Node', 'leaf': true }]
</code></pre>
 * <br><br>
 * A server request is sent, and child nodes are loaded only when a node is expanded.
 * The loading node's id is passed to the server under the parameter name "node" to
 * enable the server to produce the correct child nodes.
 * <br><br>
 * To pass extra parameters, an event handler may be attached to the "beforeload"
 * event, and the parameters specified in the TreeLoader's baseParams property:
 * <pre><code>
    myTreeLoader.on("beforeload", function(treeLoader, node) {
        this.baseParams.category = node.attributes.category;
    }, this);
</code></pre><
 * This would pass an HTTP parameter called "category" to the server containing
 * the value of the Node's "category" attribute.
 * @constructor
 * Creates a new Treeloader.
 * @param {Object} config A config object containing config properties.
 */
Ext.tree.TreeLoader = function(config){
    this.baseParams = {};
    this.requestMethod = "POST";
    Ext.apply(this, config);

    this.addEvents({
        /**
         * @event beforeload
         * Fires before a network request is made to retrieve the Json text which specifies a node's children.
         * @param {Object} This TreeLoader object.
         * @param {Object} node The {@link Ext.tree.TreeNode} object being loaded.
         * @param {Object} callback The callback function specified in the {@link #load} call.
         */
        "beforeload" : true,
        /**
         * @event load
         * Fires when the node has been successfuly loaded.
         * @param {Object} This TreeLoader object.
         * @param {Object} node The {@link Ext.tree.TreeNode} object being loaded.
         * @param {Object} response The response object containing the data from the server.
         */
        "load" : true,
        /**
         * @event loadexception
         * Fires if the network request failed.
         * @param {Object} This TreeLoader object.
         * @param {Object} node The {@link Ext.tree.TreeNode} object being loaded.
         * @param {Object} response The response object containing the data from the server.
         */
        "loadexception" : true
    });

    Ext.tree.TreeLoader.superclass.constructor.call(this);
};

Ext.extend(Ext.tree.TreeLoader, Ext.util.Observable, {
    /**
    * @cfg {String} dataUrl The URL from which to request a Json string which
    * specifies an array of node definition object representing the child nodes
    * to be loaded.
    */
    /**
    * @cfg {Object} baseParams (optional) An object containing properties which
    * specify HTTP parameters to be passed to each request for child nodes.
    */
    /**
    * @cfg {Object} baseAttrs (optional) An object containing attributes to be added to all nodes
    * created by this loader. If the attributes sent by the server have an attribute in this object,
    * they take priority.
    */
    /**
    * @cfg {Object} uiProviders (optional) An object containing properties which
    * specify custom {@link Ext.tree.TreeNodeUI} implementations. If the optional
    * <i>uiProvider</i> attribute of a returned child node is a string rather
    * than a reference to a TreeNodeUI implementation, this that string value
    * is used as a property name in the uiProviders object.
    */
    uiProviders : {},

    /**
    * @cfg {Boolean} clearOnLoad (optional) Default to true. Remove previously existing
    * child nodes before loading.
    */
    clearOnLoad : true,

    /**
     * Load an {@link Ext.tree.TreeNode} from the URL specified in the constructor.
     * This is called automatically when a node is expanded, but may be used to reload
     * a node (or append new children if the {@link #clearOnLoad} option is false.)
     * @param {Ext.tree.TreeNode} node
     * @param {Function} callback
     */
    load : function(node, callback){
        if(this.clearOnLoad){
            while(node.firstChild){
                node.removeChild(node.firstChild);
            }
        }
        if(node.attributes.children){ // preloaded json children
            var cs = node.attributes.children;
            for(var i = 0, len = cs.length; i < len; i++){
                node.appendChild(this.createNode(cs[i]));
            }
            if(typeof callback == "function"){
                callback();
            }
        }else if(this.dataUrl){
            this.requestData(node, callback);
        }
    },

    getParams: function(node){
        var buf = [], bp = this.baseParams;
        for(var key in bp){
            if(typeof bp[key] != "function"){
                buf.push(encodeURIComponent(key), "=", encodeURIComponent(bp[key]), "&");
            }
        }
        buf.push("node=", encodeURIComponent(node.id));
        return buf.join("");
    },

    requestData : function(node, callback){
        if(this.fireEvent("beforeload", this, node, callback) !== false){
            this.transId = Ext.Ajax.request({
                method:this.requestMethod,
                url: this.dataUrl||this.url,
                success: this.handleResponse,
                failure: this.handleFailure,
                scope: this,
                argument: {callback: callback, node: node},
                params: this.getParams(node)
            });
        }else{
            // if the load is cancelled, make sure we notify
            // the node that we are done
            if(typeof callback == "function"){
                callback();
            }
        }
    },

    isLoading : function(){
        return this.transId ? true : false;
    },

    abort : function(){
        if(this.isLoading()){
            Ext.Ajax.abort(this.transId);
        }
    },

    /**
    * Override this function for custom TreeNode node implementation
    */
    createNode : function(attr){
        // apply baseAttrs, nice idea Corey!
        if(this.baseAttrs){
            Ext.applyIf(attr, this.baseAttrs);
        }
        if(this.applyLoader !== false){
            attr.loader = this;
        }
        if(typeof attr.uiProvider == 'string'){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        return(attr.leaf ?
                        new Ext.tree.TreeNode(attr) :
                        new Ext.tree.AsyncTreeNode(attr));
    },

    processResponse : function(response, node, callback){
        var json = response.responseText;
        try {
            var o = eval("("+json+")");
            for(var i = 0, len = o.length; i < len; i++){
                var n = this.createNode(o[i]);
                if(n){
                    node.appendChild(n);
                }
            }
            if(typeof callback == "function"){
                callback(this, node);
            }
        }catch(e){
            this.handleFailure(response);
        }
    },

    handleResponse : function(response){
        this.transId = false;
        var a = response.argument;
        this.processResponse(response, a.node, a.callback);
        this.fireEvent("load", this, a.node, response);
    },

    handleFailure : function(response){
        this.transId = false;
        var a = response.argument;
        this.fireEvent("loadexception", this, a.node, response);
        if(typeof a.callback == "function"){
            a.callback(this, a.node);
        }
    }
});



/**
* @class Ext.tree.TreeFilter
* Note this class is experimental and doesn't update the indent (lines) or expand collapse icons of the nodes
* @param {TreePanel} tree
* @param {Object} config (optional)
 */
Ext.tree.TreeFilter = function(tree, config){
    this.tree = tree;
    this.filtered = {};
    Ext.apply(this, config);
};

Ext.tree.TreeFilter.prototype = {
    clearBlank:false,
    reverse:false,
    autoClear:false,
    remove:false,

     /**
     * Filter the data by a specific attribute.
     * @param {String/RegExp} value Either string that the attribute value
     * should start with or a RegExp to test against the attribute
     * @param {String} attr (optional) The attribute passed in your node's attributes collection. Defaults to "text".
     * @param {TreeNode} startNode (optional) The node to start the filter at.
     */
    filter : function(value, attr, startNode){
        attr = attr || "text";
        var f;
        if(typeof value == "string"){
            var vlen = value.length;
            // auto clear empty filter
            if(vlen == 0 && this.clearBlank){
                this.clear();
                return;
            }
            value = value.toLowerCase();
            f = function(n){
                return n.attributes[attr].substr(0, vlen).toLowerCase() == value;
            };
        }else if(value.exec){ // regex?
            f = function(n){
                return value.test(n.attributes[attr]);
            };
        }else{
            throw 'Illegal filter type, must be string or regex';
        }
        this.filterBy(f, null, startNode);
	},

    /**
     * Filter by a function. The passed function will be called with each
     * node in the tree (or from the startNode). If the function returns true, the node is kept
     * otherwise it is filtered. If a node is filtered, its children are also filtered.
     * @param {Function} fn The filter function
     * @param {Object} scope (optional) The scope of the function (defaults to the current node)
     */
    filterBy : function(fn, scope, startNode){
        startNode = startNode || this.tree.root;
        if(this.autoClear){
            this.clear();
        }
        var af = this.filtered, rv = this.reverse;
        var f = function(n){
            if(n == startNode){
                return true;
            }
            if(af[n.id]){
                return false;
            }
            var m = fn.call(scope || n, n);
            if(!m || rv){
                af[n.id] = n;
                n.ui.hide();
                return false;
            }
            return true;
        };
        startNode.cascade(f);
        if(this.remove){
           for(var id in af){
               if(typeof id != "function"){
                   var n = af[id];
                   if(n && n.parentNode){
                       n.parentNode.removeChild(n);
                   }
               }
           }
        }
    },

    /**
     * Clears the current filter. Note: with the "remove" option
     * set a filter cannot be cleared.
     */
    clear : function(){
        var t = this.tree;
        var af = this.filtered;
        for(var id in af){
            if(typeof id != "function"){
                var n = af[id];
                if(n){
                    n.ui.show();
                }
            }
        }
        this.filtered = {};
    }
};




/**
 * @class Ext.tree.TreeSorter
 * Provides sorting of nodes in a TreePanel
 * 
 * @cfg {Boolean} folderSort True to sort leaf nodes under non leaf nodes
 * @cfg {String} property The named attribute on the node to sort by (defaults to text)
 * @cfg {String} dir The direction to sort (asc or desc) (defaults to asc)
 * @cfg {String} leafAttr The attribute used to determine leaf nodes in folder sort (defaults to "leaf")
 * @cfg {Boolean} caseSensitive true for case sensitive sort (defaults to false)
 * @cfg {Function} sortType A custom "casting" function used to convert node values before sorting
 * @constructor
 * @param {TreePanel} tree
 * @param {Object} config
 */
Ext.tree.TreeSorter = function(tree, config){
    Ext.apply(this, config);
    tree.on("beforechildrenrendered", this.doSort, this);
    tree.on("append", this.updateSort, this);
    tree.on("insert", this.updateSort, this);
    
    var dsc = this.dir && this.dir.toLowerCase() == "desc";
    var p = this.property || "text";
    var sortType = this.sortType;
    var fs = this.folderSort;
    var cs = this.caseSensitive === true;
    var leafAttr = this.leafAttr || 'leaf';

    this.sortFn = function(n1, n2){
        if(fs){
            if(n1.attributes[leafAttr] && !n2.attributes[leafAttr]){
                return 1;
            }
            if(!n1.attributes[leafAttr] && n2.attributes[leafAttr]){
                return -1;
            }
        }
    	var v1 = sortType ? sortType(n1) : (cs ? n1.attributes[p] : n1.attributes[p].toUpperCase());
    	var v2 = sortType ? sortType(n2) : (cs ? n2.attributes[p] : n2.attributes[p].toUpperCase());
    	if(v1 < v2){
			return dsc ? +1 : -1;
		}else if(v1 > v2){
			return dsc ? -1 : +1;
        }else{
	    	return 0;
        }
    };
};

Ext.tree.TreeSorter.prototype = {
    doSort : function(node){
        node.sort(this.sortFn);
    },
    
    compareNodes : function(n1, n2){
        return (n1.text.toUpperCase() > n2.text.toUpperCase() ? 1 : -1);
    },
    
    updateSort : function(tree, node){
        if(node.childrenRendered){
            this.doSort.defer(1, this, [node]);
        }
    }
};



if(Ext.dd.DropZone){
    
Ext.tree.TreeDropZone = function(tree, config){
    this.allowParentInsert = false;
    this.allowContainerDrop = false;
    this.appendOnly = false;
    Ext.tree.TreeDropZone.superclass.constructor.call(this, tree.innerCt, config);
    this.tree = tree;
    this.lastInsertClass = "x-tree-no-status";
    this.dragOverData = {};
};

Ext.extend(Ext.tree.TreeDropZone, Ext.dd.DropZone, {
    ddGroup : "TreeDD",
    
    expandDelay : 1000,
    
    expandNode : function(node){
        if(node.hasChildNodes() && !node.isExpanded()){
            node.expand(false, null, this.triggerCacheRefresh.createDelegate(this));
        }
    },
    
    queueExpand : function(node){
        this.expandProcId = this.expandNode.defer(this.expandDelay, this, [node]);
    },
    
    cancelExpand : function(){
        if(this.expandProcId){
            clearTimeout(this.expandProcId);
            this.expandProcId = false;
        }
    },
    
    isValidDropPoint : function(n, pt, dd, e, data){
        if(!n || !data){ return false; }
        var targetNode = n.node;
        var dropNode = data.node;
        // default drop rules
        if(!(targetNode && targetNode.isTarget && pt)){
            return false;
        }
        if(pt == "append" && targetNode.allowChildren === false){
            return false;
        }
        if((pt == "above" || pt == "below") && (targetNode.parentNode && targetNode.parentNode.allowChildren === false)){
            return false;
        }
        if(dropNode && (targetNode == dropNode || dropNode.contains(targetNode))){
            return false;
        }
        // reuse the object
        var overEvent = this.dragOverData;
        overEvent.tree = this.tree;
        overEvent.target = targetNode;
        overEvent.data = data;
        overEvent.point = pt;
        overEvent.source = dd;
        overEvent.rawEvent = e;
        overEvent.dropNode = dropNode;
        overEvent.cancel = false;  
        var result = this.tree.fireEvent("nodedragover", overEvent);
        return overEvent.cancel === false && result !== false;
    },
    
    getDropPoint : function(e, n, dd){
        var tn = n.node;
        if(tn.isRoot){
            return tn.allowChildren !== false ? "append" : false; // always append for root
        }
        var dragEl = n.ddel;
        var t = Ext.lib.Dom.getY(dragEl), b = t + dragEl.offsetHeight;
        var y = Ext.lib.Event.getPageY(e);
        var noAppend = tn.allowChildren === false || tn.isLeaf();
        if(this.appendOnly || tn.parentNode.allowChildren === false){
            return noAppend ? false : "append";
        }
        var noBelow = false;
        if(!this.allowParentInsert){
            noBelow = tn.hasChildNodes() && tn.isExpanded();
        }
        var q = (b - t) / (noAppend ? 2 : 3);
        if(y >= t && y < (t + q)){
            return "above";
        }else if(!noBelow && (noAppend || y >= b-q && y <= b)){
            return "below";
        }else{
            return "append";
        }
    },
    
    onNodeEnter : function(n, dd, e, data){
        this.cancelExpand();
    },
    
    onNodeOver : function(n, dd, e, data){
        var pt = this.getDropPoint(e, n, dd);
        var node = n.node;
        
        // auto node expand check
        if(!this.expandProcId && pt == "append" && node.hasChildNodes() && !n.node.isExpanded()){
            this.queueExpand(node);
        }else if(pt != "append"){
            this.cancelExpand();
        }
        
        // set the insert point style on the target node
        var returnCls = this.dropNotAllowed;
        if(this.isValidDropPoint(n, pt, dd, e, data)){
           if(pt){
               var el = n.ddel;
               var cls;
               if(pt == "above"){
                   returnCls = n.node.isFirst() ? "x-tree-drop-ok-above" : "x-tree-drop-ok-between";
                   cls = "x-tree-drag-insert-above";
               }else if(pt == "below"){
                   returnCls = n.node.isLast() ? "x-tree-drop-ok-below" : "x-tree-drop-ok-between";
                   cls = "x-tree-drag-insert-below";
               }else{
                   returnCls = "x-tree-drop-ok-append";
                   cls = "x-tree-drag-append";
               }
               if(this.lastInsertClass != cls){
                   Ext.fly(el).replaceClass(this.lastInsertClass, cls);
                   this.lastInsertClass = cls;
               }
           }
       }
       return returnCls;
    },
    
    onNodeOut : function(n, dd, e, data){
        this.cancelExpand();
        this.removeDropIndicators(n);
    },
    
    onNodeDrop : function(n, dd, e, data){
        var point = this.getDropPoint(e, n, dd);
        var targetNode = n.node;
        targetNode.ui.startDrop();
        if(!this.isValidDropPoint(n, point, dd, e, data)){
            targetNode.ui.endDrop();
            return false;
        }
        // first try to find the drop node
        var dropNode = data.node || (dd.getTreeNode ? dd.getTreeNode(data, targetNode, point, e) : null);
        var dropEvent = {
            tree : this.tree,
            target: targetNode,
            data: data,
            point: point,
            source: dd,
            rawEvent: e,
            dropNode: dropNode,
            cancel: !dropNode   
        };
        var retval = this.tree.fireEvent("beforenodedrop", dropEvent);
        if(retval === false || dropEvent.cancel === true || !dropEvent.dropNode){
            targetNode.ui.endDrop();
            return false;
        }
        // allow target changing
        targetNode = dropEvent.target;
        if(point == "append" && !targetNode.isExpanded()){
            targetNode.expand(false, null, function(){
                this.completeDrop(dropEvent);
            }.createDelegate(this));
        }else{
            this.completeDrop(dropEvent);
        }
        return true;
    },
    
    completeDrop : function(de){
        var ns = de.dropNode, p = de.point, t = de.target;
        if(!(ns instanceof Array)){
            ns = [ns];
        }
        var n;
        for(var i = 0, len = ns.length; i < len; i++){
            n = ns[i];
            if(p == "above"){
                t.parentNode.insertBefore(n, t);
            }else if(p == "below"){
                t.parentNode.insertBefore(n, t.nextSibling);
            }else{
                t.appendChild(n);
            }
        }
        n.ui.focus();
        if(this.tree.hlDrop){
            n.ui.highlight();
        }
        t.ui.endDrop();
        this.tree.fireEvent("nodedrop", de);
    },
    
    afterNodeMoved : function(dd, data, e, targetNode, dropNode){
        if(this.tree.hlDrop){
            dropNode.ui.focus();
            dropNode.ui.highlight();
        }
        this.tree.fireEvent("nodedrop", this.tree, targetNode, data, dd, e);
    },
    
    getTree : function(){
        return this.tree;
    },
    
    removeDropIndicators : function(n){
        if(n && n.ddel){
            var el = n.ddel;
            Ext.fly(el).removeClass([
                    "x-tree-drag-insert-above",
                    "x-tree-drag-insert-below",
                    "x-tree-drag-append"]);
            this.lastInsertClass = "_noclass";
        }
    },
    
    beforeDragDrop : function(target, e, id){
        this.cancelExpand();
        return true;
    },
    
    afterRepair : function(data){
        if(data && Ext.enableFx){
            data.node.ui.highlight();
        }
        this.hideProxy();
    }    
});

}



if(Ext.dd.DragZone){
Ext.tree.TreeDragZone = function(tree, config){
    Ext.tree.TreeDragZone.superclass.constructor.call(this, tree.getTreeEl(), config);
    this.tree = tree;
};

Ext.extend(Ext.tree.TreeDragZone, Ext.dd.DragZone, {
    ddGroup : "TreeDD",
    
    onBeforeDrag : function(data, e){
        var n = data.node;
        return n && n.draggable && !n.disabled;
    },
    
    onInitDrag : function(e){
        var data = this.dragData;
        this.tree.getSelectionModel().select(data.node);
        this.proxy.update("");
        data.node.ui.appendDDGhost(this.proxy.ghost.dom);
        this.tree.fireEvent("startdrag", this.tree, data.node, e);
    },
    
    getRepairXY : function(e, data){
        return data.node.ui.getDDRepairXY();
    },
    
    onEndDrag : function(data, e){
        this.tree.fireEvent("enddrag", this.tree, data.node, e);
    },
    
    onValidDrop : function(dd, e, id){
        this.tree.fireEvent("dragdrop", this.tree, this.dragData.node, dd, e);
        this.hideProxy();
    },
    
    beforeInvalidDrop : function(e, id){
        // this scrolls the original position back into view
        var sm = this.tree.getSelectionModel();
        sm.clearSelections();
        sm.select(this.dragData.node);
    }
});
}



/**
 * @class Ext.tree.TreeEditor
 * @extends Ext.Editor
 * Provides editor functionality for inline tree node editing.  Any valid {@link Ext.form.Field} can be used
 * as the editor field.
 * @constructor
 * @param {TreePanel} tree
 * @param {Object} config Either a prebuilt {@link Ext.form.Field} instance or a Field config object
 */
Ext.tree.TreeEditor = function(tree, config){
    config = config || {};
    var field = config.events ? config : new Ext.form.TextField(config);
    Ext.tree.TreeEditor.superclass.constructor.call(this, field);

    this.tree = tree;

    tree.on('beforeclick', this.beforeNodeClick, this);
    tree.getTreeEl().on('mousedown', this.hide, this);
    this.on('complete', this.updateNode, this);
    this.on('beforestartedit', this.fitToTree, this);
    this.on('startedit', this.bindScroll, this, {delay:10});
    this.on('specialkey', this.onSpecialKey, this);
};

Ext.extend(Ext.tree.TreeEditor, Ext.Editor, {
    /**
     * @cfg {String} alignment
     * The position to align to (see {@link Ext.Element#alignTo} for more details, defaults to "l-l").
     */
    alignment: "l-l",
    // inherit
    autoSize: false,
    /**
     * @cfg {Boolean} hideEl
     * True to hide the bound element while the editor is displayed (defaults to false)
     */
    hideEl : false,
    /**
     * @cfg {String} cls
     * CSS class to apply to the editor (defaults to "x-small-editor x-tree-editor")
     */
    cls: "x-small-editor x-tree-editor",
    /**
     * @cfg {Boolean} shim
     * True to shim the editor if selects/iframes could be displayed beneath it (defaults to false)
     */
    shim:false,
    // inherit
    shadow:"frame",
    /**
     * @cfg {Number} maxWidth
     * The maximum width in pixels of the editor field (defaults to 250).  Note that if the maxWidth would exceed
     * the containing tree element's size, it will be automatically limited for you to the container width, taking
     * scroll and client offsets into account prior to each edit.
     */
    maxWidth: 250,

    editDelay : 350,

    // private
    fitToTree : function(ed, el){
        var td = this.tree.getTreeEl().dom, nd = el.dom;
        if(td.scrollLeft >  nd.offsetLeft){ // ensure the node left point is visible
            td.scrollLeft = nd.offsetLeft;
        }
        var w = Math.min(
                this.maxWidth,
                (td.clientWidth > 20 ? td.clientWidth : td.offsetWidth) - Math.max(0, nd.offsetLeft-td.scrollLeft) - /*cushion*/5);
        this.setSize(w, '');
    },

    // private
    triggerEdit : function(node){
        this.completeEdit();
        this.editNode = node;
        this.startEdit(node.ui.textNode, node.text);
    },

    // private
    bindScroll : function(){
        this.tree.getTreeEl().on('scroll', this.cancelEdit, this);
    },

    // private
    beforeNodeClick : function(node, e){
        var sinceLast = (this.lastClick ? this.lastClick.getElapsed() : 0);
        this.lastClick = new Date();
        if(sinceLast > this.editDelay && this.tree.getSelectionModel().isSelected(node)){
            e.stopEvent();
            this.triggerEdit(node);
            return false;
        }
    },

    // private
    updateNode : function(ed, value){
        this.tree.getTreeEl().un('scroll', this.cancelEdit, this);
        this.editNode.setText(value);
    },

    // private
    onHide : function(){
        Ext.tree.TreeEditor.superclass.onHide.call(this);
        if(this.editNode){
            this.editNode.ui.focus();
        }
    },

    // private
    onSpecialKey : function(field, e){
        var k = e.getKey();
        if(k == e.ESC){
            e.stopEvent();
            this.cancelEdit();
        }else if(k == e.ENTER && !e.hasModifier()){
            e.stopEvent();
            this.completeEdit();
        }
    }
});



/**
 * @class Ext.form.Field
 * @extends Ext.BoxComponent
 * Base class for form fields that provides default event handling, sizing, value handling and other functionality.
 * @constructor
 * Creates a new Field
 * @param {Object} config Configuration options
 */
Ext.form.Field = function(config){
    Ext.form.Field.superclass.constructor.call(this, config);
};

Ext.extend(Ext.form.Field, Ext.BoxComponent,  {
    /**
     * @cfg {String} invalidClass The CSS class to use when marking a field invalid (defaults to "x-form-invalid")
     */
    invalidClass : "x-form-invalid",
    /**
     * @cfg {String} invalidText The error text to use when marking a field invalid and no message is provided (defaults to "The value in this field is invalid")
     */
    invalidText : "The value in this field is invalid",
    /**
     * @cfg {String} focusClass The CSS class to use when the field receives focus (defaults to "x-form-focus")
     */
    focusClass : "x-form-focus",
    /**
     * @cfg {String/Boolean} validationEvent The event that should initiate field validation. Set to false to disable
      automatic validation (defaults to "keyup").
     */
    validationEvent : "keyup",
    /**
     * @cfg {Boolean} validateOnBlur Whether the field should validate when it loses focus (defaults to true).
     */
    validateOnBlur : true,
    /**
     * @cfg {Number} validationDelay The length of time in milliseconds after user input begins until validation is initiated (defaults to 250)
     */
    validationDelay : 250,
    /**
     * @cfg {String/Object} autoCreate A DomHelper element spec, or true for a default element spec (defaults to
     * {tag: "input", type: "text", size: "20", autocomplete: "off"})
     */
    defaultAutoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off"},
    /**
     * @cfg {String} fieldClass The default CSS class for the field (defaults to "x-form-field")
     */
    fieldClass : "x-form-field",
    /**
     * @cfg {String} msgTarget The location where error text should display.  Should be one of the following values (defaults to 'qtip'):
     *<pre>
Value         Description
-----------   ----------------------------------------------------------------------
qtip          Display a quick tip when the user hovers over the field
title         Display a default browser title attribute popup
under         Add a block div beneath the field containing the error text
side          Add an error icon to the right of the field with a popup on hover
[element id]  Add the error text directly to the innerHTML of the specified element
</pre>
     */
    msgTarget : 'qtip',
    /**
     * @cfg {String} msgFx <b>Experimental</b> The effect used when displaying a validation message under the field (defaults to 'normal').
     */
    msgFx : 'normal',

    /**
     * @cfg {Boolean} readOnly True to mark the field as readOnly in HTML (defaults to false) -- Note: this only sets the element's readOnly DOM attribute.
     */
    readOnly : false,

    /**
     * @cfg {Boolean} disabled True to disable the field (defaults to false).
     */
    disabled : false,

    /**
     * @cfg {String} inputType The type attribute for input fields -- e.g. radio, text, password (defaults to "text").
     */
    inputType : undefined,
    
    /**
     * @cfg {Number} tabIndex The tabIndex for this field. Note this only applies to fields that are rendered, not those which are built via applyTo (defaults to undefined).
	 */
	tabIndex : undefined,
	
    // private
    isFormField : true,

    // private
    hasFocus : false,

    /**
     * @cfg {Mixed} value A value to initialize this field with.
     */
    value : undefined,

    /**
     * @cfg {String} name The field's HTML name attribute.
     */
    /**
     * @cfg {String} cls A CSS class to apply to the field's underlying element.
     */

	// private ??
	initComponent : function(){
        Ext.form.Field.superclass.initComponent.call(this);
        this.addEvents({
            /**
             * @event focus
             * Fires when this field receives input focus.
             * @param {Ext.form.Field} this
             */
            focus : true,
            /**
             * @event blur
             * Fires when this field loses input focus.
             * @param {Ext.form.Field} this
             */
            blur : true,
            /**
             * @event specialkey
             * Fires when any key related to navigation (arrows, tab, enter, esc, etc.) is pressed.  You can check
             * {@link Ext.EventObject#getKey} to determine which key was pressed.
             * @param {Ext.form.Field} this
             * @param {Ext.EventObject} e The event object
             */
            specialkey : true,
            /**
             * @event change
             * Fires just before the field blurs if the field value has changed.
             * @param {Ext.form.Field} this
             * @param {Mixed} newValue The new value
             * @param {Mixed} oldValue The original value
             */
            change : true,
            /**
             * @event invalid
             * Fires after the field has been marked as invalid.
             * @param {Ext.form.Field} this
             * @param {String} msg The validation message
             */
            invalid : true,
            /**
             * @event valid
             * Fires after the field has been validated with no errors.
             * @param {Ext.form.Field} this
             */
            valid : true
        });
    },

    /**
     * Returns the name attribute of the field if available
     * @return {String} name The field name
     */
    getName: function(){
         return this.rendered && this.el.dom.name ? this.el.dom.name : (this.hiddenName || '');
    },

    // private
    onRender : function(ct, position){
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        if(!this.el){
            var cfg = this.getAutoCreate();
            if(!cfg.name){
                cfg.name = this.name || this.id;
            }
            if(this.inputType){
                cfg.type = this.inputType;
            }
            this.el = ct.createChild(cfg, position);
        }
        var type = this.el.dom.type;
        if(type){
            if(type == 'password'){
                type = 'text';
            }
            this.el.addClass('x-form-'+type);
        }
        if(this.readOnly){
            this.el.dom.readOnly = true;
        }
        if(this.tabIndex !== undefined){
            this.el.dom.setAttribute('tabIndex', this.tabIndex);
        }

        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
    },

    /**
     * Apply the behaviors of this component to an existing element. <b>This is used instead of render().</b>
     * @param {String/HTMLElement/Element} el The id of the node, a DOM node or an existing Element
     * @return {Ext.form.Field} this
     */
    applyTo : function(target){
        this.allowDomMove = false;
        this.el = Ext.get(target);
        this.render(this.el.dom.parentNode);
        return this;
    },

    // private
    initValue : function(){
        if(this.value !== undefined){
            this.setValue(this.value);
        }else if(this.el.dom.value.length > 0){
            this.setValue(this.el.dom.value);
        }
    },

    /**
     * Returns true if this field has been changed since it was originally loaded and is not disabled.
     */
    isDirty : function() {
        if(this.disabled) {
            return false;
        }
        return String(this.getValue()) !== String(this.originalValue);
    },

    // private
    afterRender : function(){
        Ext.form.Field.superclass.afterRender.call(this);
        this.initEvents();
    },

    // private
    fireKey : function(e){
        if(e.isNavKeyPress()){
            this.fireEvent("specialkey", this, e);
        }
    },

    /**
     * Resets the current field value to the originally loaded value and clears any validation messages
     */
    reset : function(){
        this.setValue(this.originalValue);
        this.clearInvalid();
    },

    // private
    initEvents : function(){
        this.el.on(Ext.isIE ? "keydown" : "keypress", this.fireKey,  this);
        this.el.on("focus", this.onFocus,  this);
        this.el.on("blur", this.onBlur,  this);

        // reference to original value for reset
        this.originalValue = this.getValue();
    },

    // private
    onFocus : function(){
        if(!Ext.isOpera && this.focusClass){ // don't touch in Opera
            this.el.addClass(this.focusClass);
        }
        if(!this.hasFocus){
            this.hasFocus = true;
            this.startValue = this.getValue();
            this.fireEvent("focus", this);
        }
    },

    beforeBlur : Ext.emptyFn,

    // private
    onBlur : function(){
        this.beforeBlur();
        if(!Ext.isOpera && this.focusClass){ // don't touch in Opera
            this.el.removeClass(this.focusClass);
        }
        this.hasFocus = false;
        if(this.validationEvent !== false && this.validateOnBlur && this.validationEvent != "blur"){
            this.validate();
        }
        var v = this.getValue();
        if(String(v) !== String(this.startValue)){
            this.fireEvent('change', this, v, this.startValue);
        }
        this.fireEvent("blur", this);
    },

    /**
     * Returns whether or not the field value is currently valid
     * @param {Boolean} preventMark True to disable marking the field invalid
     * @return {Boolean} True if the value is valid, else false
     */
    isValid : function(preventMark){
        if(this.disabled){
            return true;
        }
        var restore = this.preventMark;
        this.preventMark = preventMark === true;
        var v = this.validateValue(this.processValue(this.getRawValue()));
        this.preventMark = restore;
        return v;
    },

    /**
     * Validates the field value
     * @return {Boolean} True if the value is valid, else false
     */
    validate : function(){
        if(this.disabled || this.validateValue(this.processValue(this.getRawValue()))){
            this.clearInvalid();
            return true;
        }
        return false;
    },

    processValue : function(value){
        return value;
    },

    // private
    // Subclasses should provide the validation implementation by overriding this
    validateValue : function(value){
        return true;
    },

    /**
     * Mark this field as invalid
     * @param {String} msg The validation message
     */
    markInvalid : function(msg){
        if(!this.rendered || this.preventMark){ // not rendered
            return;
        }
        this.el.addClass(this.invalidClass);
        msg = msg || this.invalidText;
        switch(this.msgTarget){
            case 'qtip':
                this.el.dom.qtip = msg;
                this.el.dom.qclass = 'x-form-invalid-tip';
                if(Ext.QuickTips){ // fix for floating editors interacting with DND
                    Ext.QuickTips.enable();
                }
                break;
            case 'title':
                this.el.dom.title = msg;
                break;
            case 'under':
                if(!this.errorEl){
                    var elp = this.el.findParent('.x-form-element', 5, true);
                    this.errorEl = elp.createChild({cls:'x-form-invalid-msg'});
                    this.errorEl.setWidth(elp.getWidth(true)-20);
                }
                this.errorEl.update(msg);
                Ext.form.Field.msgFx[this.msgFx].show(this.errorEl, this);
                break;
            case 'side':
                if(!this.errorIcon){
                    var elp = this.el.findParent('.x-form-element', 5, true);
                    this.errorIcon = elp.createChild({cls:'x-form-invalid-icon'});
                }
                this.alignErrorIcon();
                this.errorIcon.dom.qtip = msg;
                this.errorIcon.dom.qclass = 'x-form-invalid-tip';
                this.errorIcon.show();
                this.on('resize', this.alignErrorIcon, this);
                break;
            default:
                var t = Ext.getDom(this.msgTarget);
                t.innerHTML = msg;
                t.style.display = this.msgDisplay;
                break;
        }
        this.fireEvent('invalid', this, msg);
    },

    // private
    alignErrorIcon : function(){
        this.errorIcon.alignTo(this.el, 'tl-tr', [2, 0]);
    },

    /**
     * Clear any invalid styles/messages for this field
     */
    clearInvalid : function(){
        if(!this.rendered || this.preventMark){ // not rendered
            return;
        }
        this.el.removeClass(this.invalidClass);
        switch(this.msgTarget){
            case 'qtip':
                this.el.dom.qtip = '';
                break;
            case 'title':
                this.el.dom.title = '';
                break;
            case 'under':
                if(this.errorEl){
                    Ext.form.Field.msgFx[this.msgFx].hide(this.errorEl, this);
                }
                break;
            case 'side':
                if(this.errorIcon){
                    this.errorIcon.dom.qtip = '';
                    this.errorIcon.hide();
                    this.un('resize', this.alignErrorIcon, this);
                }
                break;
            default:
                var t = Ext.getDom(this.msgTarget);
                t.innerHTML = '';
                t.style.display = 'none';
                break;
        }
        this.fireEvent('valid', this);
    },

    /**
     * Returns the raw data value which may or may not be a valid, defined value.  To return a normalized value see {@link #getValue}.
     * @return {Mixed} value The field value
     */
    getRawValue : function(){
        var v = this.el.getValue();
        if(v === this.emptyText){
            v = '';
        }
        return v;
    },

    /**
     * Returns the normalized data value (undefined or emptyText will be returned as '').  To return the raw value see {@link #getRawValue}.
     * @return {Mixed} value The field value
     */
    getValue : function(){
        var v = this.el.getValue();
        if(v === this.emptyText || v === undefined){
            v = '';
        }
        return v;
    },

    /**
     * Sets the underlying DOM field's value directly, bypassing validation.  To set the value with validation see {@link #setValue}.
     * @param {Mixed} value The value to set
     */
    setRawValue : function(v){
        return this.el.dom.value = (v === null || v === undefined ? '' : v);
    },

    /**
     * Sets a data value into the field and validates it.  To set the value directly without validation see {@link #setRawValue}.
     * @param {Mixed} value The value to set
     */
    setValue : function(v){
        this.value = v;
        if(this.rendered){
            this.el.dom.value = (v === null || v === undefined ? '' : v);
            this.validate();
        }
    },

    adjustSize : function(w, h){
        var s = Ext.form.Field.superclass.adjustSize.call(this, w, h);
        s.width = this.adjustWidth(this.el.dom.tagName, s.width);
        return s;
    },

    adjustWidth : function(tag, w){
        tag = tag.toLowerCase();
        if(typeof w == 'number' && Ext.isStrict && !Ext.isSafari){
            if(Ext.isIE && (tag == 'input' || tag == 'textarea')){
                if(tag == 'input'){
                    return w + 2;
                }
                if(tag = 'textarea'){
                    return w-2;
                }
            }else if(Ext.isOpera){
                if(tag == 'input'){
                    return w + 2;
                }
                if(tag = 'textarea'){
                    return w-2;
                }
            }
        }
        return w;
    }
});


// anything other than normal should be considered experimental
Ext.form.Field.msgFx = {
    normal : {
        show: function(msgEl, f){
            msgEl.setDisplayed('block');
        },

        hide : function(msgEl, f){
            msgEl.setDisplayed(false).update('');
        }
    },

    slide : {
        show: function(msgEl, f){
            msgEl.slideIn('t', {stopFx:true});
        },

        hide : function(msgEl, f){
            msgEl.slideOut('t', {stopFx:true,useDisplay:true});
        }
    },

    slideRight : {
        show: function(msgEl, f){
            msgEl.fixDisplay();
            msgEl.alignTo(f.el, 'tl-tr');
            msgEl.slideIn('l', {stopFx:true});
        },

        hide : function(msgEl, f){
            msgEl.slideOut('l', {stopFx:true,useDisplay:true});
        }
    }
};



/**
 * @class Ext.form.TextField
 * @extends Ext.form.Field
 * Basic text field.  Can be used as a direct replacement for traditional text inputs, or as the base
 * class for more sophisticated input controls (like {@link Ext.form.TextArea} and {@link Ext.form.ComboBox}).
 * @constructor
 * Creates a new TextField
 * @param {Object} config Configuration options
 */
Ext.form.TextField = function(config){
    Ext.form.TextField.superclass.constructor.call(this, config);
    this.addEvents({
        /**
         * @event autosize
         * Fires when the autosize function is triggered.  The field may or may not have actually changed size
         * according to the default logic, but this event provides a hook for the developer to apply additional
         * logic at runtime to resize the field if needed.
	     * @param {Ext.form.Field} this This text field
	     * @param {Number} width The new field width
	     */
        autosize : true
    });
};

Ext.extend(Ext.form.TextField, Ext.form.Field,  {
    /**
     * @cfg {Boolean} grow True if this field should automatically grow and shrink to its content
     */
    grow : false,
    /**
     * @cfg {Number} growMin The minimum width to allow when grow = true (defaults to 30)
     */
    growMin : 30,
    /**
     * @cfg {Number} growMax The maximum width to allow when grow = true (defaults to 800)
     */
    growMax : 800,
    /**
     * @cfg {String} vtype A validation type name as defined in {@link Ext.form.VTypes} (defaults to null)
     */
    vtype : null,
    /**
     * @cfg {String} maskRe An input mask regular expression that will be used to filter keystrokes that don't match (defaults to null)
     */
    maskRe : null,
    /**
     * @cfg {Boolean} disableKeyFilter True to disable input keystroke filtering (defaults to false)
     */
    disableKeyFilter : false,
    /**
     * @cfg {Boolean} allowBlank False to validate that the value length > 0 (defaults to true)
     */
    allowBlank : true,
    /**
     * @cfg {Number} minLength Minimum input field length required (defaults to 0)
     */
    minLength : 0,
    /**
     * @cfg {Number} maxLength Maximum input field length allowed (defaults to Number.MAX_VALUE)
     */
    maxLength : Number.MAX_VALUE,
    /**
     * @cfg {String} minLengthText Error text to display if the minimum length validation fails (defaults to "The minimum length for this field is {minLength}")
     */
    minLengthText : "The minimum length for this field is {0}",
    /**
     * @cfg {String} maxLengthText Error text to display if the maximum length validation fails (defaults to "The maximum length for this field is {maxLength}")
     */
    maxLengthText : "The maximum length for this field is {0}",
    /**
     * @cfg {Boolean} selectOnFocus True to automatically select any existing field text when the field receives input focus (defaults to false)
     */
    selectOnFocus : false,
    /**
     * @cfg {String} blankText Error text to display if the allow blank validation fails (defaults to "This field is required")
     */
    blankText : "This field is required",
    /**
     * @cfg {Function} validator A custom validation function to be called during field validation (defaults to null).
     * If available, this function will be called only after the basic validators all return true, and will be passed the
     * current field value and expected to return boolean true if the value is valid or a string error message if invalid.
     */
    validator : null,
    /**
     * @cfg {RegExp} regex A JavaScript RegExp object to be tested against the field value during validation (defaults to null).
     * If available, this regex will be evaluated only after the basic validators all return true, and will be passed the
     * current field value.  If the test fails, the field will be marked invalid using {@link #regexText}.
     */
    regex : null,
    /**
     * @cfg {String} regexText The error text to display if {@link #regex} is used and the test fails during validation (defaults to "")
     */
    regexText : "",
    /**
     * @cfg {String} emptyText The default text to display in an empty field (defaults to null).
     */
    emptyText : null,
    /**
     * @cfg {String} emptyClass The CSS class to apply to an empty field to style the {@link #emptyText} (defaults to
     * 'x-form-empty-field').  This class is automatically added and removed as needed depending on the current field value.
     */
    emptyClass : 'x-form-empty-field',

    // private
    initEvents : function(){
        Ext.form.TextField.superclass.initEvents.call(this);
        if(this.validationEvent == 'keyup'){
            this.validationTask = new Ext.util.DelayedTask(this.validate, this);
            this.el.on('keyup', this.filterValidation, this);
        }
        else if(this.validationEvent !== false){
            this.el.on(this.validationEvent, this.validate, this, {buffer: this.validationDelay});
        }
        if(this.selectOnFocus || this.emptyText){
            this.on("focus", this.preFocus, this);
            if(this.emptyText){
                this.on('blur', this.postBlur, this);
                this.applyEmptyText();
            }
        }
        if(this.maskRe || (this.vtype && this.disableKeyFilter !== true && (this.maskRe = Ext.form.VTypes[this.vtype+'Mask']))){
            this.el.on("keypress", this.filterKeys, this);
        }
        if(this.grow){
            this.el.on("keyup", this.onKeyUp,  this, {buffer:50});
            this.el.on("click", this.autoSize,  this);
        }
    },

    processValue : function(value){
        if(this.stripCharsRe){
            var newValue = value.replace(this.stripCharsRe, '');
            if(newValue !== value){
                this.setRawValue(newValue);
                return newValue;
            }
        }
        return value;
    },

    filterValidation : function(e){
        if(!e.isNavKeyPress()){
            this.validationTask.delay(this.validationDelay);
        }
    },

    // private
    onKeyUp : function(e){
        if(!e.isNavKeyPress()){
            this.autoSize();
        }
    },

    /**
     * Resets the current field value to the originally-loaded value and clears any validation messages.
     * Also adds emptyText and emptyClass if the original value was blank.
     */
    reset : function(){
        Ext.form.TextField.superclass.reset.call(this);
        this.applyEmptyText();
    },

    applyEmptyText : function(){
        if(this.rendered && this.emptyText && this.getRawValue().length < 1){
            this.setRawValue(this.emptyText);
            this.el.addClass(this.emptyClass);
        }
    },

    // private
    preFocus : function(){
        if(this.emptyText){
            if(this.el.dom.value == this.emptyText){
                this.setRawValue('');
            }
            this.el.removeClass(this.emptyClass);
        }
        if(this.selectOnFocus){
            this.el.dom.select();
        }
    },

    // private
    postBlur : function(){
        this.applyEmptyText();
    },

    // private
    filterKeys : function(e){
        var k = e.getKey();
        if(!Ext.isIE && (e.isNavKeyPress() || k == e.BACKSPACE || (k == e.DELETE && e.button == -1))){
            return;
        }
        var c = e.getCharCode(), cc = String.fromCharCode(c);
        if(Ext.isIE && (e.isSpecialKey() || !cc)){
            return;
        }
        if(!this.maskRe.test(cc)){
            e.stopEvent();
        }
    },

    setValue : function(v){
        if(this.emptyText && this.el && v !== undefined && v !== null && v !== ''){
            this.el.removeClass(this.emptyClass);
        }
        Ext.form.TextField.superclass.setValue.apply(this, arguments);
        this.applyEmptyText();
        this.autoSize();
    },

    /**
     * Validates a value according to the field's validation rules and marks the field as invalid
     * if the validation fails
     * @param {Mixed} value The value to validate
     * @return {Boolean} True if the value is valid, else false
     */
    validateValue : function(value){
        if(value.length < 1 || value === this.emptyText){ // if it's blank
             if(this.allowBlank){
                 this.clearInvalid();
                 return true;
             }else{
                 this.markInvalid(this.blankText);
                 return false;
             }
        }
        if(value.length < this.minLength){
            this.markInvalid(String.format(this.minLengthText, this.minLength));
            return false;
        }
        if(value.length > this.maxLength){
            this.markInvalid(String.format(this.maxLengthText, this.maxLength));
            return false;
        }
        if(this.vtype){
            var vt = Ext.form.VTypes;
            if(!vt[this.vtype](value, this)){
                this.markInvalid(this.vtypeText || vt[this.vtype +'Text']);
                return false;
            }
        }
        if(typeof this.validator == "function"){
            var msg = this.validator(value);
            if(msg !== true){
                this.markInvalid(msg);
                return false;
            }
        }
        if(this.regex && !this.regex.test(value)){
            this.markInvalid(this.regexText);
            return false;
        }
        return true;
    },

    /**
     * Selects text in this field
     * @param {Number} start (optional) The index where the selection should start (defaults to 0)
     * @param {Number} end (optional) The index where the selection should end (defaults to the text length)
     */
    selectText : function(start, end){
        var v = this.getRawValue();
        if(v.length > 0){
            start = start === undefined ? 0 : start;
            end = end === undefined ? v.length : end;
            var d = this.el.dom;
            if(d.setSelectionRange){
                d.setSelectionRange(start, end);
            }else if(d.createTextRange){
                var range = d.createTextRange();
                range.moveStart("character", start);
                range.moveEnd("character", v.length-end);
                range.select();
            }
        }
    },

    /**
     * Automatically grows the field to accomodate the width of the text up to the maximum field width allowed.
     * This only takes effect if grow = true, and fires the autosize event.
     */
    autoSize : function(){
        if(!this.grow || !this.rendered){
            return;
        }
        if(!this.metrics){
            this.metrics = Ext.util.TextMetrics.createInstance(this.el);
        }
        var el = this.el;
        var v = el.dom.value;
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(v));
        v = d.innerHTML;
        d = null;
        v += "&#160;";
        var w = Math.min(this.growMax, Math.max(this.metrics.getWidth(v) + /* add extra padding */ 10, this.growMin));
        this.el.setWidth(w);
        this.fireEvent("autosize", this, w);
    }
});


