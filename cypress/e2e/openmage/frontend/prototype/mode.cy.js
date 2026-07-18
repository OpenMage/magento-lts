/**
 * Prototype mode loadout + shim contract tests.
 *
 * The suite adapts to whichever dev/js/prototype_mode the site under test is
 * configured with (CI runs the suite once per mode):
 *  - every mode: asserts the emitted script loadout is consistent
 *  - shim mode:  additionally exercises the prototype-shim.js API contract
 *    (Class.create $super gating, Ajax.Request GET parameters, Ajax.Updater
 *    single script evaluation, Form serialization, setValue, stopObserving)
 */

const SHIM_VERSION = '1.7.3-shim';

function scriptSources(win) {
    return Array.from(win.document.querySelectorAll('script[src]')).map((s) => s.src);
}

function detectMode(win) {
    const sources = scriptSources(win);
    if (sources.some((src) => src.includes('/js/prototype/prototype.js'))) {
        return 'full';
    }
    if (sources.some((src) => src.includes('/js/prototype/prototype-shim.js'))) {
        return 'shim';
    }
    return 'none';
}

describe('prototype_mode script loadout', () => {
    it('emits a consistent script set for the configured mode', () => {
        cy.visit('/');
        cy.window().then((win) => {
            const mode = detectMode(win);
            const sources = scriptSources(win);
            const hasShim = sources.some((src) => src.includes('prototype-shim.js'));
            const hasScriptaculous = sources.some((src) => src.includes('/js/scriptaculous/'));

            cy.log(`Detected prototype_mode: ${mode}`);

            if (mode === 'full') {
                expect(hasShim, 'shim must not load beside full Prototype').to.equal(false);
                expect(win.Prototype, 'Prototype global').to.exist;
                expect(win.Prototype.Version).to.not.equal(SHIM_VERSION);
            } else if (mode === 'shim') {
                expect(hasScriptaculous, 'Scriptaculous must not load in shim mode').to.equal(false);
                expect(win.Prototype, 'Prototype global').to.exist;
                expect(win.Prototype.Version).to.equal(SHIM_VERSION);
            } else {
                expect(hasShim).to.equal(false);
                expect(hasScriptaculous).to.equal(false);
                expect(win.Prototype, 'no Prototype global in none mode').to.equal(undefined);
            }
        });
    });
});

describe('prototype-shim API contract (shim mode only)', () => {
    beforeEach(function () {
        cy.visit('/');
        cy.window().then(function (win) {
            if (detectMode(win) !== 'shim') {
                this.skip();
            }
        });
    });

    it('Class.create only injects $super when declared', () => {
        cy.window().then((win) => {
            const Parent = win.Class.create({
                greet: function (name) { return 'hi ' + name; },
            });

            // Plain override: arguments must arrive unshifted.
            const Child = win.Class.create(Parent, {
                greet: function (name) { return 'child ' + name; },
            });
            expect(new Child().greet('bob')).to.equal('child bob');

            // $super override: first param receives the bound parent method.
            const SuperChild = win.Class.create(Parent, {
                greet: function ($super, name) { return $super(name) + '!'; },
            });
            expect(new SuperChild().greet('bob')).to.equal('hi bob!');
        });
    });

    it('Ajax.Request sends GET parameters on the wire', () => {
        cy.intercept('GET', '**/shim-ajax-probe*', { statusCode: 200, body: 'ok' }).as('probe');
        cy.window().then((win) => {
            new win.Ajax.Request('/shim-ajax-probe', {
                method: 'get',
                parameters: { probe: 'value42', second: 'x' },
            });
        });
        cy.wait('@probe').then(({ request }) => {
            expect(request.url).to.include('probe=value42');
            expect(request.url).to.include('second=x');
        });
    });

    it('Ajax.Updater inserts content and evaluates scripts exactly once', () => {
        cy.intercept('GET', '**/shim-updater-probe*', {
            statusCode: 200,
            body: '<p id="shim-updater-frag">updated</p>'
                + '<script>window.__shimEvalCount = (window.__shimEvalCount || 0) + 1;</script>',
        }).as('updater');
        cy.window().then((win) => {
            const target = win.document.createElement('div');
            target.id = 'shim-updater-target';
            win.document.body.appendChild(target);
            new win.Ajax.Updater('shim-updater-target', '/shim-updater-probe', {
                method: 'get',
                evalScripts: true,
            });
        });
        cy.wait('@updater');
        cy.get('#shim-updater-frag').should('contain', 'updated');
        cy.window().should((win) => {
            expect(win.__shimEvalCount, 'response script must run exactly once').to.equal(1);
        });
    });

    it('Form serialization keeps repeated names and multiselects; enable undoes disable', () => {
        cy.window().then((win) => {
            const doc = win.document;
            const form = doc.createElement('form');
            form.innerHTML = '<input type="text" name="billing[street][]" value="line1">'
                + '<input type="text" name="billing[street][]" value="line2">'
                + '<select name="colors[]" multiple>'
                + '<option value="red" selected></option>'
                + '<option value="blue" selected></option>'
                + '<option value="green"></option>'
                + '</select>';
            doc.body.appendChild(form);

            const serialized = win.Form.serialize(form);
            expect(serialized).to.include('line1');
            expect(serialized).to.include('line2');
            expect(serialized).to.include('red');
            expect(serialized).to.include('blue');
            expect(serialized).to.not.include('green');

            const hash = win.Form.serialize(form, true);
            expect(hash['billing[street][]']).to.deep.equal(['line1', 'line2']);
            expect(hash['colors[]']).to.deep.equal(['red', 'blue']);

            win.Form.disable(form);
            expect(form.elements[0].disabled).to.equal(true);
            win.Form.enable(form);
            expect(form.elements[0].disabled).to.equal(false);

            form.remove();
        });
    });

    it('setValue follows Prototype checkbox and multiselect semantics', () => {
        cy.window().then((win) => {
            const doc = win.document;
            const checkbox = doc.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = 'yes';
            doc.body.appendChild(checkbox);
            checkbox.setValue(true);
            expect(checkbox.checked).to.equal(true);
            checkbox.setValue(false);
            expect(checkbox.checked).to.equal(false);

            const select = doc.createElement('select');
            select.multiple = true;
            select.innerHTML = '<option value="a"></option><option value="b"></option><option value="c"></option>';
            doc.body.appendChild(select);
            select.setValue(['a', 'c']);
            const selected = Array.from(select.options).filter((o) => o.selected).map((o) => o.value);
            expect(selected).to.deep.equal(['a', 'c']);

            checkbox.remove();
            select.remove();
        });
    });

    it('stopObserving supports the per-event bulk overload', () => {
        cy.window().then((win) => {
            const el = win.document.createElement('button');
            win.document.body.appendChild(el);
            let count = 0;
            el.observe('click', () => { count++; });
            el.click();
            expect(count).to.equal(1);
            el.stopObserving('click');
            el.click();
            expect(count, 'handler must be removed by event-only overload').to.equal(1);
            el.remove();
        });
    });
});
