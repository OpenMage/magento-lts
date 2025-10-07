/**
 * Admin login configuration
 * @type {Object}
 */
cy.openmage.admin = {
    _submit: {
        __selector: '.form-button',
    },
    login: () =>{
        cy.log('Logining in as admin');
        const username = cy.openmage.admin.username;
        const password = cy.openmage.admin.password;

        cy.visit('/admin');

        cy.log(`Logging in as ${username.value}`);
        cy.get(username._id).clear().type(username.value).should('have.value', username.value);
        cy.get(password._id).clear().type(password.value).should('have.value', password.value);
        cy.get(cy.openmage.admin._submit.__selector).click();

        cy.log('Checking for successful login');
        cy.url().should('include', cy.openmage.test.backend.dashbord.config.index.url);
    },
    goToPage: (test, path) =>{
        cy.log('Go to admin page');
        cy.get('body').then($body => {
            const popup = '#message-popup-window .message-popup-head a';
            if ($body.find(popup).length > 0) {
                cy.log('Dismissing popup');
                cy.get(popup).click({force: true});
            }
        });

        cy.log(`Clicking on "${path.title}" menu`);
        cy.get(test._id).click({force: true});
        cy.url().should('include', test.url);
    },
    goToSection: (section) =>{
        cy.log('Go to admin config section');
        cy.log(`Clicking on "${section.title}" menu`);
        cy.get(section._id).click({force: true});
        cy.url().should('include', section.url);
    },
}

cy.openmage.admin.username = {
    _id: '#username',
    value: 'admin',
}

cy.openmage.admin.password = {
    _id: '#login',
    value: 'veryl0ngpassw0rd',
}