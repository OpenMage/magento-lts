const test = cy.testBackendSystemIndex.config;
const check = cy.openmage.check;

describe(`Checks admin system "${test.list.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.list);
    });

    it(`tests index route`, () => {
        check.pageElements(test, test.list);
    });
});
