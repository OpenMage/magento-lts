/**
 * Prototype.js Deprecation Warnings
 *
 * Loaded after prototype.js in "full" mode. Wraps key Prototype functions
 * with throttled console.warn() deprecation notices.
 *
 * Activate via ?protodebug=1 in URL or window.PROTOTYPE_DEPRECATION_WARNINGS = true
 */
(function () {
    'use strict';

    var enabled = window.PROTOTYPE_DEPRECATION_WARNINGS === true
        || (window.location.search && window.location.search.indexOf('protodebug=1') !== -1);

    if (!enabled || typeof Prototype === 'undefined') {
        return;
    }

    var _warned = new Set();

    function warn(name) {
        if (_warned.has(name)) {
            return;
        }
        _warned.add(name);
        console.warn(
            '[Prototype.js Deprecation] "' + name + '" is deprecated and will be removed'
            + ' in OpenMage v22.0. See docs/prototype-migration.md for migration guide.'
        );
    }

    /**
     * Wrap a function on an object (or window) so it warns once then delegates.
     */
    function wrapFunction(owner, prop, displayName) {
        var original = owner[prop];
        if (typeof original !== 'function') {
            return;
        }
        owner[prop] = function () {
            warn(displayName);
            return original.apply(this, arguments);
        };
        // Preserve any static properties on the original (e.g. Ajax.Request.Events)
        for (var key in original) {
            if (original.hasOwnProperty(key)) {
                owner[prop][key] = original[key];
            }
        }
        if (original.prototype) {
            owner[prop].prototype = original.prototype;
        }
    }

    /**
     * Wrap a prototype method, preserving `this` context.
     */
    function wrapProto(proto, method, displayName) {
        var original = proto[method];
        if (typeof original !== 'function') {
            return;
        }
        proto[method] = function () {
            warn(displayName);
            return original.apply(this, arguments);
        };
    }

    /**
     * Wrap a constructor so that `new Ctor(...)` still works.
     */
    function wrapConstructor(owner, prop, displayName) {
        var Original = owner[prop];
        if (typeof Original !== 'function') {
            return;
        }
        var Wrapper = function () {
            warn(displayName);
            var args = Array.prototype.slice.call(arguments);
            // Use original initialize path — Prototype constructors call initialize()
            var instance = Object.create(Original.prototype);
            Original.apply(instance, args);
            return instance;
        };
        Wrapper.prototype = Original.prototype;
        for (var key in Original) {
            if (Original.hasOwnProperty(key)) {
                Wrapper[key] = Original[key];
            }
        }
        owner[prop] = Wrapper;
    }

    // --- Global selectors ---
    wrapFunction(window, '$', '$()');
    wrapFunction(window, '$$', '$$()');
    if (typeof $F !== 'undefined') {
        wrapFunction(window, '$F', '$F()');
    }

    // --- Class.create ---
    if (typeof Class !== 'undefined') {
        wrapFunction(Class, 'create', 'Class.create()');
    }

    // --- Ajax ---
    if (typeof Ajax !== 'undefined') {
        if (Ajax.Request) {
            wrapConstructor(Ajax, 'Request', 'Ajax.Request');
        }
        if (Ajax.Updater) {
            wrapConstructor(Ajax, 'Updater', 'Ajax.Updater');
        }
    }

    // --- Event.observe ---
    if (typeof Event !== 'undefined' && Event.observe) {
        wrapFunction(Event, 'observe', 'Event.observe()');
    }

    // --- Element static methods ---
    if (typeof Element !== 'undefined') {
        wrapFunction(Element, 'addClassName', 'Element.addClassName()');
        wrapFunction(Element, 'removeClassName', 'Element.removeClassName()');
        wrapFunction(Element, 'hasClassName', 'Element.hasClassName()');
    }

    // --- Scriptaculous effects (optional) ---
    if (typeof Effect !== 'undefined') {
        if (Effect.Fade) {
            wrapFunction(Effect, 'Fade', 'Effect.Fade()');
        }
        if (Effect.Appear) {
            wrapFunction(Effect, 'Appear', 'Effect.Appear()');
        }
    }

    // --- Form.serialize (optional) ---
    if (typeof Form !== 'undefined' && Form.serialize) {
        wrapFunction(Form, 'serialize', 'Form.serialize()');
    }

    // --- String prototype methods ---
    wrapProto(String.prototype, 'strip', 'String.prototype.strip()');
    wrapProto(String.prototype, 'evalJSON', 'String.prototype.evalJSON()');

    // --- Array prototype methods ---
    wrapProto(Array.prototype, 'each', 'Array.prototype.each()');
    wrapProto(Array.prototype, 'invoke', 'Array.prototype.invoke()');
})();
