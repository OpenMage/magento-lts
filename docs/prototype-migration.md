# Prototype.js Removal from OpenMage — Implementation Status

## Overview

This document tracks the migration of OpenMage from Prototype.js (1.7.3) + Scriptaculous (1.8.2) to native browser JavaScript. The work lives on branch `feature/prototype-removal-compatibility-layer`.

**Why:** Prototype.js pollutes the global namespace (`$()`, `$$()`, String/Array prototype extensions), causing conflicts with modern third-party libraries. The ~520KB unminified payload is loaded on every page.

**Approach:** Three-layer strategy — deprecation instrumentation, a compatibility shim, and file-by-file rewrites — controlled by a `dev/js/prototype_mode` config flag (`full` | `shim` | `none`).

---

## Current Status

### Completed

| Phase | Description | Files | Status |
|-------|-------------|-------|--------|
| **Phase 0** | Config flag + admin UI (`dev/js/prototype_mode`) | 3 PHP/XML | Done |
| **Phase 0** | `Head.php` conditional JS loading | 1 PHP | Done |
| **Phase 1** | Compatibility shim (`prototype-shim.js`, 1741 lines) | 1 JS | Done |
| **Phase 0** | Deprecation wrapper (`prototype-deprecation.js`) | 1 JS | Done |
| **Wave 1** | `loader.js`, `events.js` | 2 JS | Done |
| **Wave 2** | `translate.js`, `accordion.js`, `varien/js.js` | 3 JS | Done |
| **Wave 3** | `form.js`, `configurable.js`, `product.js`, `opcheckout.js`, `msrp.js`, `bundle.js`, RWD overlays | 8 JS | Done |
| **Wave 4** | Admin `tools.js`, `accordion.js`, `tabs.js`, `form.js`, `grid.js` | 5 JS | Done |
| **Wave 5** | Admin `rules.js`, `product.js`, `sales.js`, `packaging.js`, `browser.js`, `uploader/instance.js` | 6 JS | Done |
| **Wave 6** | `validation.js`, `directpost.js`, `translate_inline.js`, `captcha.js`, `centinel.js` (x2) | 6 JS | Done |
| **Wave 7** | Inline JS in `.phtml` templates | 175 `.phtml` | Done |

**Totals:** 200+ files changed, ~7,300 lines modified, 30 standalone JS files rewritten.

### Remaining Work

| Task | Description | Effort |
|------|-------------|--------|
| **Wave 7 remainder** | ~54 .phtml files with complex inline JS (these work with the shim) | Medium |
| **Phase 3** | Remove prototype.js + scriptaculous source files, update layout XML | Low |
| **ExtJS replacement** | `ext-tree.js` used for category tree + URL rewrites — needs separate solution | Medium |
| **Testing** | End-to-end testing with `prototype_mode=shim` and `prototype_mode=none` | High |
| **Extension guide** | Publish migration guide for extension developers | Low |

---

## Architecture

### Config Flag: `dev/js/prototype_mode`

Set in **Admin > System > Configuration > Developer > JavaScript Settings > Prototype.js Mode**

| Mode | Behavior | Use Case |
|------|----------|----------|
| `full` | Load prototype.js + scriptaculous (current default) | Extensions not yet migrated |
| `shim` | Load `prototype-shim.js` (~15-20KB) instead of full stack (~520KB) | Transition period |
| `none` | Load neither | Fully migrated sites |

**Implementation:** `Mage_Page_Block_Html_Head::_applyPrototypeMode()` swaps script tags in `getCssJsHtml()` based on config.

### Compatibility Shim (`js/prototype/prototype-shim.js`)

A 1741-line file implementing the Prototype.js API surface using native browser APIs. Covers:

- **Globals:** `$()`, `$$()`, `$F()`, `$H()`, `$A()`, `$w()`, `$break`
- **Element methods:** show/hide, `classList`, traversal, DOM manipulation, events, styles, attributes (patched on `HTMLElement.prototype`)
- **OOP:** `Class.create()` with inheritance and `$super`
- **Ajax:** `Ajax.Request`, `Ajax.Updater`, `Ajax.Responders` (fetch-based)
- **Events:** `Event.observe/stop/element/findElement`, KEY_* constants
- **Form:** `Form.serialize`, `Form.getElements`, `Form.Element.*`
- **Template:** `#{variable}` interpolation
- **String/Array/Number prototype extensions**
- **Scriptaculous effects:** Fade, Appear, Highlight, `BlindDown`/`BlindUp` (CSS transition-based)
- **Stubs:** `Sortable`, `Draggable`, `Droppables` (console warnings)

Each shimmed function emits a one-time deprecation warning via `_protoWarn()`.

### Deprecation Wrapper (`js/prototype/prototype-deprecation.js`)

Loaded after full prototype.js in `full` mode. Activated by `?protodebug=1` query param. Wraps 16 key Prototype functions with throttled deprecation warnings pointing to this document.

---

## Rewritten Files Reference

### Wave 1 — Prerequisites
| File | Key Changes |
|------|-------------|
| `js/mage/adminhtml/loader.js` | `form_key` injection via fetch interceptor; `SessionError`; `varienLoader`; `showLoader`/`hideLoader` |
| `js/mage/adminhtml/events.js` | `varienEvents` constructor; `.reject()` → `.filter()` |
| `js/mage/adminhtml/hash.js` | No changes needed (already pure JS) |

### Wave 2 — Simple Files
| File | Key Changes |
|------|-------------|
| `js/mage/translate.js` | `$H()` → plain object |
| `js/varien/accordion.js` | `querySelectorAll`, `addEventListener`, `classList` |
| `js/varien/js.js` | All Varien utilities: `searchForm`, `Tabs`, `DateElement`, `DOB`, `formCreator` |

### Wave 3 — Frontend Critical Path
| File | Key Changes |
|------|-------------|
| `js/varien/form.js` | `VarienForm`, `RegionUpdater`, `ZipUpdater` — fetch for child loading |
| `js/varien/configurable.js` | Product.Config — Template via regex, Hash → plain object |
| `js/varien/product.js` | `Product.Zoom` (`Draggable`/`Slider` guarded), `Product.Config`, `Product.Super` |
| `skin/.../opcheckout.js` | `Checkout`, `Billing`, `Shipping`, `ShippingMethod`, `Payment`, `Review` — fetch-based AJAX |
| `skin/.../msrp.js` | `Catalog.Map` — `CustomEvent` for `bundle:reload-price` |
| `skin/.../bundle.js` | `Product.Bundle` — `CustomEvent` dispatch |
| `skin/.../opcheckout_rwd.js` | jQuery fallback for animations |
| `skin/.../msrp_rwd.js` | Closure-based function wrapping |

### Wave 4 — Admin Medium Complexity
| File | Key Changes |
|------|-------------|
| `js/mage/adminhtml/tools.js` | `toolbarToggle`, `Cookie`, `Fieldset`, `Base64` — `cumulativeOffset` helper |
| `js/mage/adminhtml/accordion.js` | `varienAccordion` — `Cookie` integration preserved |
| `js/mage/adminhtml/tabs.js` | `varienTabs` — fetch for AJAX tabs, shadow tab loading |
| `js/mage/adminhtml/form.js` | `varienForm`, `Validation.isVisible`, `FormElementDependenceController`, admin `RegionUpdater` |
| `js/mage/adminhtml/grid.js` | `varienGrid`, `varienGridMassaction`, `serializerController` |

### Wave 5 — Admin High Complexity
| File | Key Changes |
|------|-------------|
| `js/mage/adminhtml/rules.js` | `VarienRulesForm` — chooser grid integration |
| `js/mage/adminhtml/product.js` | `Product.Gallery`, `Product.Attributes`, `Product.Configurable` |
| `js/mage/adminhtml/sales.js` | `AdminOrder` (1279 lines), `OrderFormArea`, `ControlButton` |
| `js/mage/adminhtml/sales/packaging.js` | Shipping packaging UI |
| `js/mage/adminhtml/browser.js` | Media browser — `Dialog`/`Windows` availability checks with fallback |
| `js/mage/adminhtml/uploader/instance.js` | Flow.js uploader — `CustomEvent` for upload lifecycle |

### Wave 6 — Validation + Remaining
| File | Key Changes |
|------|-------------|
| `js/prototype/validation.js` | Validation framework (937 lines) — all 100+ validators preserved |
| `js/mage/directpost.js` | Authorize.Net Direct Post — `iframe` form submission |
| `js/mage/translate_inline.js` | Inline translation — `Dialog.confirm` with availability guard |
| `js/mage/captcha.js` | CAPTCHA refresh — `CustomEvent` for billing/login events |
| `js/mage/centinel.js` | 3D Secure authentication |
| `js/mage/adminhtml/sales/centinel.js` | Admin Centinel validator |

### Wave 7 — Template Files
175 .phtml files across admin, frontend base, frontend RWD, and install areas. Inline `<script>` blocks rewritten. ~54 files with complex patterns deferred (work with shim).

---

## Pattern Replacement Reference

### Selectors
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `$('id')` | `document.getElementById('id')` |
| `$$('.class')` | `document.querySelectorAll('.class')` |
| `$F('id')` | `document.getElementById('id').value` |

### Element Methods
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `el.show()` | `el.style.display = ''` |
| `el.hide()` | `el.style.display = 'none'` |
| `el.visible()` | `el.style.display !== 'none'` |
| `el.addClassName('x')` | `el.classList.add('x')` |
| `el.removeClassName('x')` | `el.classList.remove('x')` |
| `el.hasClassName('x')` | `el.classList.contains('x')` |
| `el.up('selector')` | `el.closest('selector')` |
| `el.down('selector')` | `el.querySelector('selector')` |
| `el.next()` | `el.nextElementSibling` |
| `el.select('sel')` | `el.querySelectorAll('sel')` |
| `el.update(html)` | `el.innerHTML = html` |
| `el.insert({bottom: html})` | `el.insertAdjacentHTML('beforeend', html)` |
| `el.readAttribute(a)` | `el.getAttribute(a)` |
| `el.writeAttribute(a, v)` | `el.setAttribute(a, v)` |

### Events
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `Event.observe(el, 'click', fn)` | `el.addEventListener('click', fn)` |
| `el.observe('click', fn)` | `el.addEventListener('click', fn)` |
| `el.stopObserving('click', fn)` | `el.removeEventListener('click', fn)` |
| `Event.stop(e)` | `e.preventDefault(); e.stopPropagation()` |
| `Event.element(e)` | `e.target` |
| `Event.findElement(e, 'tag')` | `e.target.closest('tag')` |
| `fn.bindAsEventListener(ctx)` | `fn.bind(ctx)` |
| `document.observe('dom:loaded', fn)` | `document.addEventListener('DOMContentLoaded', fn)` |

### AJAX
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `new Ajax.Request(url, {onSuccess: fn})` | `fetch(url, opts).then(fn)` |
| `new Ajax.Updater(el, url)` | `fetch(url).then(r => r.text()).then(html => el.innerHTML = html)` |
| `transport.responseText` | `await response.text()` |
| `transport.responseJSON` | `await response.json()` |
| `Form.serialize(form)` | `new URLSearchParams(new FormData(form)).toString()` |

### OOP
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `Class.create()` | Constructor function + prototype |
| `initialize: function()` | Constructor body |
| `$super` | Call parent method explicitly |

### Arrays / Objects
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `.each(fn)` | `.forEach(fn)` |
| `.collect(fn)` | `.map(fn)` |
| `.select(fn)` | `.filter(fn)` |
| `.detect(fn)` | `.find(fn)` |
| `.invoke('method')` | `.forEach(el => el.method())` |
| `.pluck('prop')` | `.map(x => x.prop)` |
| `$H(obj)` | Plain object + `Object.keys()` |
| `Object.extend(a, b)` | `Object.assign(a, b)` |
| `Object.toJSON(x)` | `JSON.stringify(x)` |

### Strings
| Prototype.js | Vanilla JS |
|-------------|-----------|
| `str.strip()` | `str.trim()` |
| `str.blank()` | `str.trim().length === 0` |
| `str.stripTags()` | `str.replace(/<[^>]*>/g, '')` |
| `str.escapeHTML()` | Helper using `textContent` |
| `str.evalJSON()` | `JSON.parse(str)` |
| `str.isJSON()` | `try { JSON.parse(str); } catch(e) {}` |

### Scriptaculous Effects
| Scriptaculous | Vanilla JS |
|--------------|-----------|
| `Effect.Fade(el)` | CSS transition on opacity |
| `Effect.Appear(el)` | CSS transition on opacity |
| `Effect.Highlight(el)` | CSS animation |
| `new Draggable(el)` | Guard with `typeof Draggable !== 'undefined'` |

---

## Next Steps

### Immediate (to complete this branch)

1. **Finish Wave 7 remainder** — ~54 .phtml files with complex inline JS. These are mostly admin templates with deeply intertwined PHP/JS. They work fine with the shim, so this is lower priority.

2. **Phase 3: Remove source files** — Once testing confirms shim mode works:
   - Delete `/js/prototype/prototype.js` (7594 lines)
   - Delete `/js/prototype/window.js`, `window_effects.js`, `window_ext.js`, `effects.js`
   - Delete `/js/prototype/tooltip.js`, `tooltip_manager.js`, `debug.js`, `extended_debug.js`
   - Delete `/js/scriptaculous/` (8 files)
   - Update layout XML: `page.xml`, `main.xml` — remove prototype/scriptaculous `addJs` actions
   - Change default `prototype_mode` from `full` to `shim`

### Testing Required

Before merging, test these critical paths with `prototype_mode=shim`:

**Frontend:**
- [ ] Homepage loads without JS errors
- [ ] Category page — layered navigation, sorting, pagination
- [ ] Product page — configurable options, add to cart, quantity
- [ ] Cart — update quantities, remove items, apply coupon
- [ ] Checkout — all steps complete, payment works, order placed
- [ ] Customer account — login, register, address book

**Admin:**
- [ ] Admin login
- [ ] Dashboard loads
- [ ] Product grid — sorting, filtering, pagination, mass actions
- [ ] Product create/edit — all tabs, images, custom options
- [ ] Order grid + order view — invoice, ship, credit memo
- [ ] System config — save settings
- [ ] Category tree — drag and drop (requires ExtJS check)

### Future Releases

| Release | Default Mode | Ships |
|---------|-------------|-------|
| Next minor | `full` | All waves complete, shim available |
| Next major (v21.0) | `shim` | Shim is default, `full` available for compat |
| v22.0 | `none` | Prototype/Scriptaculous removed from repo |

### ExtJS Dependency

`/js/extjs/ext-tree.js` depends on Prototype.js (used for category tree, URL rewrites). This needs its own replacement strategy — out of scope for this migration but tracked as a follow-up.

---

## Sources

- [Maho Commerce GitHub](https://github.com/MahoCommerce/maho) — Reference implementation (completed this migration)
- [Maho PR #71](https://github.com/MahoCommerce/maho/pull/71) — Frontend/checkout migration
- [Maho PR #136](https://github.com/MahoCommerce/maho/pull/136) — Admin sales.js rewrite
- [Maho Differences](https://mahocommerce.com/differences/) — Migration overview
