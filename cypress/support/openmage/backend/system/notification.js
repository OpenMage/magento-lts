const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.notification;

/**
 * Configuration for "System > Notifications" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-adminnotification',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'admin/notification',
    index: {},
}

/**
 * Configuration for "Messages Inbox" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Messages Inbox',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'date_added', dir: 'desc' } }},
}
