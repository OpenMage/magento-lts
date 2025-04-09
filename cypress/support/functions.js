cy.openmage = {
    generateRandomEmail: () => {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let email = '';
        for (let i = 0; i < 16; i++) {
            email += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return email + '@example.com';
    }
}