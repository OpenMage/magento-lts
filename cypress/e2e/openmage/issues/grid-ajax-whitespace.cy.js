const gridMarkup = `<table id="whitespace-grid_table">
    <tbody><tr><td>
        <div class="inline-text"><span>First</span> <span>Last</span></div>
        <pre class="formatted-text"><span>first</span>\n  <span>second</span></pre>
        <textarea class="literal-text"><span>first</span>\n  <span>second</span></textarea>
    </td></tr></tbody>
</table>`;

const fixturePage = `<!doctype html><html><head>
    <script src="/js/prototype/prototype.js"></script>
    <script src="/js/mage/adminhtml/grid.js"></script>
</head><body>
    <div id="whitespace-grid">${gridMarkup}</div>
    <script>
        window.FORM_KEY = 'grid-test-form-key';
        window.setLocation = function (url) {};
        window.whitespaceGrid = new varienGrid('whitespace-grid', '/grid-whitespace-ajax');
        window.whitespaceGrid.useAjax = true;
        window.reloadInitCount = 0;
        window.reloadRowCount = 0;
        window.whitespaceGrid.initCallback = function () { window.reloadInitCount++; };
        window.whitespaceGrid.initRowCallback = function () { window.reloadRowCount++; };
    </script>
</body></html>`;

function checkText() {
    cy.get('.inline-text').should('have.text', 'First Last');
    cy.get('.formatted-text').should('have.text', 'first\n  second');
    cy.get('.literal-text').should('have.value', '<span>first</span>\n  <span>second</span>');
}

describe('Admin grid AJAX response whitespace', () => {
    beforeEach(() => {
        cy.intercept('GET', '/grid-whitespace-fixture', {
            headers: {'content-type': 'text/html'},
            body: fixturePage,
        });
        cy.visit('/grid-whitespace-fixture');
    });

    it('preserves initial inline spacing and literal text after an AJAX reload', () => {
        checkText();
        cy.intercept('POST', '/grid-whitespace-ajax*', {
            headers: {'content-type': 'text/html'},
            body: gridMarkup,
        }).as('gridReload');

        cy.window().then((win) => win.whitespaceGrid.reload());
        cy.wait('@gridReload');

        checkText();
        cy.window().its('reloadInitCount').should('eq', 1);
        cy.window().its('reloadRowCount').should('eq', 1);
    });

    it('keeps JSON errors and expired-session redirects working', () => {
        const message = 'Keep <b>these</b> <i>spaces</i>';
        cy.intercept('POST', '/grid-whitespace-ajax*', {
            body: {error: true, message, ajaxExpired: true, ajaxRedirect: '/admin-login'},
        }).as('gridReload');
        cy.window().then((win) => {
            cy.stub(win, 'alert').as('gridAlert');
            cy.stub(win, 'setLocation').as('gridRedirect');
            win.whitespaceGrid.reload();
        });
        cy.wait('@gridReload');

        cy.get('@gridAlert').should('have.been.calledOnceWithExactly', message);
        cy.get('@gridRedirect').should('have.been.calledOnceWithExactly', '/admin-login');
        checkText();
    });
});
