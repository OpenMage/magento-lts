const test = cy.openmage.test.backend.login.config;
const validation = cy.openmage.validation;

describe('Check admin login', () => {
    beforeEach('Log in the user', () => {
        cy.visit(test.url);
        cy.fixture(test.__fixture).as('fixture');
    });

    const login = cy.openmage.admin;

    it('tests valid login', function () {
        validation.fixture.fillFields(this.fixture.validUser);
        cy.get(login._submit._).click();
        cy.url().should('include', '/dashboard/index');
    })

    it('tests invalid login', function () {
        validation.fixture.fillFields(this.fixture.invalidUser);
        cy.get(login._submit._).click();
        cy.url().should('include', '/index/index');
    })

    it('tests empty login', function () {
        validation.fixture.fillFields(this.fixture.validUser, true);
        cy.get(login._submit._).click();
        cy.url().should('not.include', '/index/index');
        cy.url().should('not.include', '/index/dashboard');
    })
})
