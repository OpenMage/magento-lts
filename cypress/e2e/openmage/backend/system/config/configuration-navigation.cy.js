const general = cy.openmage.test.backend.system.config.general.general.config;
const sitemap = cy.openmage.test.backend.system.config.catalog.sitemap.config;
const sidebarSelector = '[id="page:left"]';
const contentSelector = '#content';
const carriersSection = {
    _: '#section-carriers',
    title: 'Shipping Methods',
    url: 'system_config/edit/section/carriers',
};

const openAllFieldsets = () => {
    cy.get('#content .section-config').each($section => {
        if (!$section.hasClass('active')) {
            cy.wrap($section).find('.entry-edit-head > a').click({force: true});
        }
    });
};

describe('System configuration navigation', () => {
    beforeEach('Log in and open system configuration', () => {
        cy.viewport(1280, 800);
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(general, general.section);
        cy.openmage.admin.goToSection(general.section);
        cy.get('body').should('have.class', 'config-panels');
    });

    it('keeps the menu position when opening a section near the bottom', () => {
        cy.get(sidebarSelector).scrollTo('bottom');
        cy.get(sidebarSelector).then($sidebar => {
            const previousTop = $sidebar[0].scrollTop;
            expect(previousTop).to.be.greaterThan(0);

            cy.get('#system_config_tabs a[id^="section-"]').last().then($link => {
                const sectionId = $link.attr('id');
                cy.wrap($link).click({scrollBehavior: false});
                cy.get(`#${sectionId}`).should('have.class', 'active');
                cy.get(sidebarSelector).should($current => {
                    expect($current[0].scrollTop).to.be.closeTo(previousTop, 2);
                });
                cy.get(contentSelector).should($content => {
                    expect($content[0].scrollTop).to.equal(0);
                });
                cy.window().then(win => {
                    expect(win.sessionStorage.getItem('openmage-config-navigation')).to.equal(null);
                });
            });
        });
    });

    it('scrolls the form independently while retaining a visible save action', () => {
        openAllFieldsets();
        cy.get(sidebarSelector).scrollTo('bottom');
        cy.get(sidebarSelector).then($sidebar => {
            const previousTop = $sidebar[0].scrollTop;
            cy.get(contentSelector).scrollTo('bottom');
            cy.get(sidebarSelector).should($current => {
                expect($current[0].scrollTop).to.equal(previousTop);
            });
        });
        cy.get(contentSelector).should($content => {
            const panel = $content[0];
            expect(panel.scrollTop).to.be.greaterThan(0);
            const bounds = panel.getBoundingClientRect();
            const header = panel.querySelector('.content-header').getBoundingClientRect();
            expect(header.top).to.be.closeTo(bounds.top, 2);
            expect(header.bottom).to.be.lessThan(bounds.bottom);
        });
        cy.get('#content button[title="Save Config"]').should('be.visible');
        cy.get(contentSelector).should('have.attr', 'tabindex', '0')
            .and('have.attr', 'role', 'region');
        cy.get(contentSelector).focus().should('be.focused');
        cy.get(sidebarSelector).should('have.attr', 'tabindex', '0');
    });

    it('fits standard configuration fields at 1024px without horizontal scrolling', () => {
        cy.viewport(1024, 768);
        openAllFieldsets();
        cy.get(contentSelector).should($content => {
            expect($content[0].scrollWidth).to.be.at.most($content[0].clientWidth + 1);
        });

        cy.openmage.admin.goToConfigScope(general.section, 'store_default');
        openAllFieldsets();
        cy.get(contentSelector).should($content => {
            expect($content[0].scrollWidth).to.be.at.most($content[0].clientWidth + 1);
        });
        cy.get('#content .use-default').should('exist');
    });

    ['default', 'website'].forEach(scope => {
        it(`keeps USPS tooltip controls and help inside the panel at 1024px in ${scope} scope`, () => {
            cy.viewport(1024, 768);
            cy.openmage.admin.goToSection(carriersSection);
            if (scope === 'website') {
                cy.get('#store_switcher option[value^="website_"]').first().invoke('val').then(value => {
                    cy.openmage.admin.goToConfigScope(carriersSection, value);
                });
            }
            cy.get('#carriers_usps').closest('.section-config').then($section => {
                if (!$section.hasClass('active')) {
                    cy.wrap($section).find('.entry-edit-head > a').click({force: true});
                }
            });
            cy.get('#carriers_usps_active').then($field => {
                const inherit = $field.closest('tr').find('input.config-inherit');
                if (inherit.length) {
                    cy.wrap(inherit).uncheck();
                }
            });
            // Reveal the dependent controls only; never save or call the carrier API.
            cy.get('#carriers_usps_active').select('1');

            ['environment', 'gateway_url'].forEach(field => {
                const selector = `#carriers_usps_${field}`;
                cy.get(selector).scrollIntoView({offset: {top: -150, left: 0}}).should('be.visible');
                cy.get(selector).should($field => {
                    const control = $field[0].getBoundingClientRect();
                    const icon = $field[0].parentElement.querySelector('.field-tooltip').getBoundingClientRect();
                    expect(icon.left, `${field} icon beside its control`).to.be.at.least(control.right - 1);
                    expect(icon.top, `${field} icon on the same line`).to.be.lessThan(control.bottom);
                    expect(icon.bottom).to.be.greaterThan(control.top);
                });

                const popup = `#row_carriers_usps_${field} .field-tooltip > div`;
                // Cypress does not trigger CSS :hover. Reveal only visibility/opacity;
                // retain the real popup dimensions and positioning for this layout test.
                cy.get(popup).invoke('css', {visibility: 'visible', opacity: 1}).should('be.visible')
                    .should($popup => {
                        const panel = $popup[0].closest(contentSelector);
                        const bounds = panel.getBoundingClientRect();
                        const help = $popup[0].getBoundingClientRect();
                        const left = bounds.left + panel.clientLeft;
                        expect(help.width).to.be.greaterThan(0);
                        expect(help.left, `${field} help stays inside left edge`).to.be.at.least(left - 1);
                        expect(help.right, `${field} help stays inside right edge`).to.be.at.most(left + panel.clientWidth + 1);
                        expect(panel.scrollWidth).to.be.at.most(panel.clientWidth + 1);
                    });
                cy.get(popup).invoke('css', {visibility: '', opacity: ''});
            });
        });
    });

    it('restores normal document scrolling on narrow and short viewports', () => {
        [[800, 800], [1280, 600]].forEach(([width, height]) => {
            cy.viewport(width, height);
            cy.get('body').should('not.have.class', 'config-panels');
            cy.get(sidebarSelector).should('not.have.attr', 'tabindex');
            cy.get(contentSelector).should('not.have.attr', 'tabindex');
            cy.get(contentSelector).should($content => {
                const style = $content[0].ownerDocument.defaultView.getComputedStyle($content[0]);
                expect(style.overflowY).to.equal('visible');
            });
        });
        cy.viewport(1280, 800);
        cy.get('body').should('have.class', 'config-panels');
        cy.get(contentSelector).should('have.attr', 'tabindex', '0');
    });

    it('keeps an invalid field visible after validation focuses it', () => {
        cy.openmage.admin.goToSection(sitemap.section);
        openAllFieldsets();
        const priority = sitemap.section.priority.__fields.page._;
        cy.get(priority).clear().type('invalid');
        cy.get(contentSelector).scrollTo('top');
        cy.openmage.test.backend.system.config.clickSave();
        cy.get(priority).should('have.class', 'validation-failed').and('be.focused');
        cy.get(priority).should($field => {
            const field = $field[0];
            const panel = field.closest('#content');
            const bounds = panel.getBoundingClientRect();
            const header = panel.querySelector('.content-header').getBoundingClientRect();
            const rect = field.getBoundingClientRect();
            expect(rect.top).to.be.at.least(header.bottom);
            expect(rect.bottom).to.be.at.most(bounds.bottom);
        });
    });
});
