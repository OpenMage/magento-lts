describe('Check admin login', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
    });

    const login = cy.openmage.admin;

    it('tests valid login', () => {
        const username = login.username.value;
        const password = login.password.value;

        cy.log(`Logging in as ${username}`);
        cy.get(login.username._).clear().type(username).should('have.value', username);
        cy.get(login.password._).clear().type(password).should('have.value', password);
        cy.get(login._submit._).click();
        cy.url().should('include', '/dashboard/index');
    })

    it('tests invalid login', () => {
        const username = 'abc';
        const password = '123';

        cy.log(`Logging in as ${username}`);
        cy.get(login.username._).clear().type(username).should('have.value', username);
        cy.get(login.password._).clear().type(password).should('have.value', password);
        cy.get(login._submit._).click();
        cy.url().should('include', '/index/index');
    })

    it('tests empty login', () => {
        cy.get(login.username._).clear().should('have.value', '');
        cy.get(login.password._).clear().should('have.value', '');
        cy.get(login._submit._).click();
        cy.url().should('not.include', '/index/index');
        cy.url().should('not.include', '/index/dashboard');
    })
})
