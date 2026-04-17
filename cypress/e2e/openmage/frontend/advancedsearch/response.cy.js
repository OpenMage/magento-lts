describe('Check Advanced search', () => {
    it('tests response', () => {
        cy.request({
            url: '/catalogsearch/advanced/',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(200)
            expect(response.body).to.not.have.length(0)
        })
    })

    it('tests response for valid search param', () => {
        cy.request({
            url: '/catalogsearch/advanced/result/?name=glas/',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(200)
            expect(response.body).to.not.have.length(0)
        })
    })

    it('tests response for invalid search param', () => {
        cy.request({
            url: '/catalogsearch/advanced/result/?invalidParam=2/',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(302)
            expect(response.body).to.have.length(0)
        })
    })
})
