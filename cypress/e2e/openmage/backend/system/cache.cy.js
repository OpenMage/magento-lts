const test = cy.testBackendSystem.cache;
const check = cy.openmage.check;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        check.pageElements(test, test.index);
    });

    //it(`tests edit route`, () => {
    //});

    //it(`tests new route`, () => {
    //});
});
