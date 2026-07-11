describe('Check trailing slash', () => {
    it('tests category without slash', () => {
        cy.log('Should return status 200');
        cy.request({
            url: 'women.html',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(200)
        })
    })

    it('tests category with slash', () => {
        cy.log('Should return status 301, but its 200');
        cy.request({
            url: 'women.html/',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(200)
        })
    })

    it('tests cms-page without slash', () => {
        cy.log('Should return status 200');
        cy.request({
            url: 'contacts',
        }).then((response) => {
            expect(response.status).to.eq(200)
        })
    })

    it('tests cms-page with slash', () => {
        cy.log('Should return status 301, but its 200');
        cy.request({
            url: 'contacts/',
            followRedirect: false,
        }).then((response) => {
            expect(response.status).to.eq(200)
        })
    })
})
