// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'
import './openmage'

import './openmage/_utils/admin'
import './openmage/_utils/check'
import './openmage/_utils/test'
import './openmage/_utils/tools'
import './openmage/_utils/utils'
import './openmage/_utils/validation'

import './openmage/backend/catalog/category'
import './openmage/backend/catalog/product'
import './openmage/backend/catalog/search'
import './openmage/backend/catalog/sitemap'
import './openmage/backend/catalog/urlrewrite'
import './openmage/backend/cms/block'
import './openmage/backend/cms/page'
import './openmage/backend/cms/widget'
import './openmage/backend/customer/customer'
import './openmage/backend/customer/group'
import './openmage/backend/customer/online'
import './openmage/backend/dashboard'
import './openmage/backend/newsletter/queue'
import './openmage/backend/newsletter/report'
import './openmage/backend/newsletter/subscriber'
import './openmage/backend/newsletter/template'
import './openmage/backend/promo/catalog'
import './openmage/backend/promo/quote'
import './openmage/backend/sales/creditmemo'
import './openmage/backend/sales/invoice'
import './openmage/backend/sales/order'
import './openmage/backend/sales/shipment'
import './openmage/backend/sales/transaction'
import './openmage/backend/system/account'
import './openmage/backend/system/cache'
import './openmage/backend/system/config/catalog/configswatches'
import './openmage/backend/system/config/catalog/sitemap'
import './openmage/backend/system/config/customer/promo'
import './openmage/backend/system/config/general/general'
import './openmage/backend/system/currency'
import './openmage/backend/system/design'
import './openmage/backend/system/email'
import './openmage/backend/system/indexer'
import './openmage/backend/system/notification'
import './openmage/backend/system/store'
import './openmage/backend/system/variable'

import './openmage/frontend/customer/account'
import './openmage/frontend/homepage/newsletter'
