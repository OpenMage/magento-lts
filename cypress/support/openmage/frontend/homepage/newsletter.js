const test = cy.openmage.test.frontend.homepage.newsletter;

test.config = {
    _buttonSubmit: '#newsletter-validate-detail button[type="submit"]',
    _id: '#newsletter',
    url: cy.openmage.test.frontend.homepage._url
}
