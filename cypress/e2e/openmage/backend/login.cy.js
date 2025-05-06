describe('Check admin login', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
    });

    const login = cy.openmage.login.admin;

    it('tests valid login', () => {
        const username = login.username.value;
        const password = login.password.value;

        cy.log(`Logging in as ${username}`);
        cy.get(login.username._id).clear().type(username).should('have.value', username);
        cy.get(login.password._id).clear().type(password).should('have.value', password);
        cy.get(login._submit.__selector).click();
        cy.url().should('include', '/dashboard/index');
    })

    it('tests invalid login', () => {
        const username = 'abc';
        const password = '123';

        cy.log(`Logging in as ${username}`);
        cy.get(login.username._id).clear().type(username).should('have.value', username);
        cy.get(login.password._id).clear().type(password).should('have.value', password);
        cy.get(login._submit.__selector).click();
        cy.url().should('include', '/index/index');
    })

    it('tests empty login', () => {
        cy.get(login.username._id).clear().should('have.value', '');
        cy.get(login.password._id).clear().should('have.value', '');
        cy.get(login._submit.__selector).click();
        cy.url().should('not.include', '/index/index');
        cy.url().should('not.include', '/index/dashboard');
    })
})
