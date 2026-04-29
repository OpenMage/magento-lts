---
name: mage-customer
description: Magento 1 Mage_Customer — customer EAV entity, addresses (billing/shipping defaults), sessions, password hashing (md5/sha256/bcrypt), customer groups, login flow. Use when editing under app/code/core/Mage/Customer/, working with customer/* aliases (customer/customer, customer/address, customer/session, customer/group), or wiring customer-related events.
---

# mage-customer

Customer module: EAV-backed customers + a separate EAV-backed address entity, two session models (regular + persistent), legacy-friendly password hashing, and groups that drive pricing and tax. See `m1-eav` for the EAV mechanics this skill assumes, and `mage-checkout` for the consumer of `customer/session` during onepage.

## Aliases owned

- `customer/customer` → `Mage_Customer_Model_Customer` (EAV entity `customer`, table `customer_entity`)
- `customer/address` → `Mage_Customer_Model_Address` (EAV entity `customer_address`, table `customer_address_entity`)
- `customer/session` → `Mage_Customer_Model_Session` (frontend session)
- `customer/group` → `Mage_Customer_Model_Group` (table `customer_group`, flat — not EAV)
- `customer/config_share` — account-share scope (global vs website)
- `customer/observer`, `customer/flowpassword`, `customer/form`

`persistent/session` → `Mage_Persistent_Model_Session` is a sibling module, not `customer/*`.

## EAV layout

Two distinct entities, two attribute sets:

| Entity            | Table                    | Default attribute set | EAV setup class                     |
|-------------------|--------------------------|-----------------------|-------------------------------------|
| `customer`        | `customer_entity`        | `customer` (id 1)     | `Mage_Customer_Model_Resource_Setup` |
| `customer_address`| `customer_address_entity`| `customer_address` (id 2) | same, `entity_type='customer_address'` |

Address rows link back to their customer via `parent_id` (the EAV abstract entity's parent column) — `Mage_Customer_Model_Address::setCustomerId()` writes both `parent_id` and `customer_id` data keys; `getCustomerId()` falls back to `parent_id`. Don't set just one.

Add attributes via setup scripts only — call `$installer->addAttribute('customer', 'my_attr', [...])` or `'customer_address'`. See `m1-db-setup-scripts` and `m1-eav`.

## Default billing / shipping

Customer carries two FK columns on `customer_entity`: `default_billing` and `default_shipping`, each pointing at a `customer_address_entity.entity_id`. Magic accessors: `getDefaultBilling()`, `setDefaultBilling()`, `unsetDefaultBilling()` (and `*Shipping`). The `is_default_billing` / `is_default_shipping` data keys on the address are pseudo-attributes managed by `Mage_Customer_Model_Resource_Customer::_saveAddresses` — they aren't real EAV attributes. Set them on the address before save and the resource will sync the customer columns.

Iteration: `$customer->getAddressesCollection()` (lazy) or `$customer->getAddresses()` (deprecated array form). `getPrimaryBillingAddress()` / `getPrimaryShippingAddress()` resolve the defaults.

## Authentication

`Mage_Customer_Model_Customer::authenticate($login, $password)`:
1. `loadByEmail($login)` — scope-aware via `customer/config_share` (global website if `account_share/scope = 1`, otherwise per-website with `website_id` filter).
2. Confirmation gate — throws `EXCEPTION_EMAIL_NOT_CONFIRMED` (1) if `getConfirmation()` set and `isConfirmationRequired()`.
3. `validatePassword($password)` → `Mage::helper('core')->validateHash(...)` against `password_hash` column.
4. On fail: `EXCEPTION_INVALID_EMAIL_OR_PASSWORD` (2). On success: dispatches `customer_customer_authenticated` (the built-in observer auto-rehashes legacy-format passwords to bcrypt).

Other exception codes on `Mage_Customer_Model_Customer`: `EXCEPTION_EMAIL_EXISTS` (3), `EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN` (4), `EXCEPTION_INVALID_RESET_PASSWORD_LINK_CUSTOMER_ID` (5).

## Password hashing — `Mage_Core_Model_Encryption`

Versions:

| Const                   | Value | Algorithm  |
|-------------------------|-------|------------|
| `HASH_VERSION_MD5`      | 0     | `md5()`    |
| `HASH_VERSION_SHA256`   | 1     | `hash('sha256', ...)` |
| `HASH_VERSION_SHA512`   | 2     | `hash('sha512', ...)` |
| `HASH_VERSION_LATEST`   | 3     | `password_hash($x, PASSWORD_DEFAULT)` (bcrypt today) |

Stored `password_hash` column shape:

- `<hash>:<salt>` — salted form. `validateHashByVersion` splits on `:` (limit 2), prepends salt to password, recomputes, compares with `hash_equals`.
- `<hash>` (no colon) — unsalted. md5/sha256/sha512 only. Compared directly with `hash_equals`.
- `$2y$...` — bcrypt. No `:salt` suffix; verified with `password_verify`. Only valid when current `version_hash` is `LATEST`.

Detection by length is a useful sanity check, not how the code dispatches: md5 = 32 hex chars, sha256 = 64, sha512 = 128, bcrypt starts with `$2`.

`validateHash($password, $hash)` tries `LATEST` → `SHA512` → `SHA256` → `MD5` in that order — any match passes. This is the legacy-friendly migration path: old md5/sha256 hashes still authenticate, then `customer_customer_authenticated` rehashes to bcrypt. Don't shortcut this method; the multi-version walk is the whole point.

`getHashPassword($password, $salt = null)` produces a new hash at the configured version. Empty/`null` salt → unsalted hash for `LATEST` (bcrypt has its own salt) or for legacy versions when called that way; non-empty salt → `hash:salt` form with the configured `sha256`/`sha512` algo. Admin path goes through `Mage_Core_Helper_Data::getHashPassword()` which forces a salt via `Mage_Admin_Model_User::HASH_SALT_EMPTY` when none supplied.

`MAXIMUM_PASSWORD_LENGTH = 256` — `validateHash` rejects longer passwords pre-compute (DoS guard).

## Sessions: customer vs persistent

Two separate models, do not confuse:

`Mage_Customer_Model_Session` (`customer/session`) extends `Mage_Core_Model_Session_Abstract` — PHP session-backed. Public surface:

- `isLoggedIn()` — true iff `getId()` (the customer id stored in session) is set.
- `login($username, $password)` — calls `Customer::authenticate`, then `setCustomerAsLoggedIn`.
- `setCustomerAsLoggedIn($customer)` / `loginById($id)` / `logout()`.
- Redirect bookkeeping: `setBeforeAuthUrl`, `setAfterAuthUrl`, `setNoReferer`. `Mage_Customer_Helper_Data::getLoginUrl()` (route `customer/account/login`) appends `LoginUrlParams` carrying the `referer` query when not on a no-referer page.
- Dispatches `customer_session_init` once at first access.

`Mage_Persistent_Model_Session` (`persistent/session`, table `persistent_session`) extends `Mage_Core_Model_Abstract` — it's a *DB row*, not a session. Cookie `persistent_shopping_cart` (const `COOKIE_NAME`), random key length 50 (`KEY_LENGTH`). Lookup via `loadByCookieKey()` against the cookie value. Lifetime/enable controlled by `persistent/options/*` (`enabled`, `lifetime`, `logout_clear`, `remember_enabled`, `remember_default`, `shopping_cart`).

Two distinct predicates:

```php
Mage::getSingleton('customer/session')->isLoggedIn();        // PHP session has a customer id
Mage::helper('persistent/session')->isPersistent();          // DB-backed remember-me cookie present and valid
```

Persistent ≠ logged-in. A persistent visitor is partially identified (cart, name, dashboard link) but isn't `isLoggedIn()`; checkout still forces re-auth for sensitive actions. Wire-up lives in `Mage_Persistent_Model_Observer*` against `controller_action_predispatch`, `customer_logout`, etc.

## Login redirect flow

1. Frontend hits a `customer/account/*` action while not logged in → `Mage_Customer_AccountController::_loginPostRedirect()` decides the destination.
2. `Mage_Customer_Helper_Data::getLoginUrl()` builds the login URL on `customer/account/login`, appending `getLoginUrlParams()` (`referer` of current URL, base64-encoded) unless the session has `getNoReferer()`.
3. After successful login: redirect to `BeforeAuthUrl` (if set), else dashboard if `customer/startup/redirect_dashboard = 1`, else home.
4. Layout `customer.xml` provides handles `customer_account` (logged-in wrapper with left-nav + dashboard tabs) and `customer_logged_in` / `customer_logged_out` for branching.

`getDashboardUrl()` → `customer/account/`, `getRegisterUrl()` → `customer/account/create`, `getAccountUrl()` → `customer/account/index`. Constant `ROUTE_ACCOUNT_LOGIN = 'customer/account/login'`.

## Customer groups

Flat table `customer_group` (cols: `customer_group_id`, `customer_group_code`, `tax_class_id`). Not EAV.

Reserved IDs on `Mage_Customer_Model_Group`:

- `NOT_LOGGED_IN_ID = 0` — guests; quote uses this when no customer attached.
- `CUST_GROUP_ALL = 32000` — wildcard for catalog rule conditions and tier price "all groups". Never persisted on a customer.
- `ENTITY = 'customer_group'`, `GROUP_CODE_MAX_LENGTH = 32`.

Default for new accounts: config `customer/create_account/default_group` (default `1`, "General"). Helper `Mage::getStoreConfig('customer/create_account/default_group')`.

`customer_group_id` lives on `customer_entity` (column, not EAV attribute). Pricing/tax integrations key off it:

- **Catalog price rules** — condition `customer_group_ids`; reindex `catalogrule_rule` after rule edits (see `mage-promotions`).
- **Cart price rules** — same condition shape on the cart side.
- **Tier price** — column `customer_group_id` on `catalog_product_entity_tier_price`; `CUST_GROUP_ALL` = applies to everyone.
- **Tax** — `tax_class_id` on the group joins to `tax_class` and resolves via the customer-tax-class × product-tax-class matrix in `mage-tax`.

`afterCommitCallback` triggers an indexer event with `ENTITY = 'customer_group'` so dependent indexes can react.

## Adding a customer attribute (cheatsheet)

1. Setup script under `app/code/core/Mage/Customer/sql/customer_setup/upgrade-X.Y.Z-X.Y.Z+1.php` (or your module's own setup):

   ```php
   /** @var Mage_Customer_Model_Resource_Setup $installer */
   $installer = $this;
   $installer->startSetup();
   $installer->addAttribute('customer', 'my_field', [
       'type'         => 'varchar',
       'label'        => 'My Field',
       'input'        => 'text',
       'visible'      => true,
       'required'     => false,
       'user_defined' => true,
       'system'       => 0,
   ]);
   $installer->endSetup();
   ```

2. To surface in the account/edit forms, add to `customer_account` fieldset under `<global><fieldsets><customer_account>` with `<create>1</create>` / `<update>1</update>` flags.

3. Add `@method` entries to `Mage_Customer_Model_Customer` for IDE/PHPStan (per AGENTS.md `__call` conventions).

For an address attribute, swap the entity to `'customer_address'`.

## Common events

Global: `customer_save_before`/`_after`/`_commit_after`, `customer_register_success`, `customer_customer_authenticated` (auto-rehash hook lives here), `customer_address_save_before`/`_after` (VAT VIV observers wired by default).

Frontend: `customer_login`, `customer_logout`, `customer_session_init`. `controller_action_layout_load_before` is observed by the customer module to inject the `customer_logged_in` / `customer_logged_out` layout handles.

See `m1-events-observers` for observer wiring.

## Cross-references

- `m1-eav` — attribute creation, source/backend/frontend models, store-scoped values.
- `mage-checkout` — `customer/session` consumer; quote merging on login; persistent-cart hand-off.
- `mage-sales` — order address conversion from `customer_address`; `customer_group_id` carried onto the order.
- `mage-tax`, `mage-promotions` — group-driven calculation paths.
- `m1-db-setup-scripts` — the only place to add EAV attributes.
- `m1-system-config` — `customer/*` and `persistent/options/*` config trees.
