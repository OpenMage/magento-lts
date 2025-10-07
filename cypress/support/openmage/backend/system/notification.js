const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.notification;

/**
 * Configuration for "System > Notifications" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-adminnotification',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    url: 'notification/index',
}

/**
 * Configuration for "Messages Inbox" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Messages Inbox',
    url: test.config.url,
    _grid: '#notificationGrid_table',
}
