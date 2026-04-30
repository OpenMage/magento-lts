---
name: openmage-system-config
description: OpenMage system configuration — system.xml, scopes (default/website/store), backend/source/frontend models, encrypted fields, XML_PATH constants, Mage::getStoreConfig, default values in config.xml. Use when editing files under */etc/system.xml, adding a system config field, defining XML_PATH_* constants, or reading store config via getStoreConfig/getStoreConfigFlag.
---

# openmage-system-config

Admin "System > Configuration" screen is wired entirely through `*/etc/system.xml` (form definition) plus `*/etc/config.xml` `<default>` (initial values). Reads happen at runtime via `Mage::getStoreConfig($path, $store)`. This skill covers field shape, scope semantics, backend/source/frontend models, encryption, and the helper-constant convention.

See also: `openmage-module-structure` (where these files live in a module), `openmage-acl-adminhtml` (every section needs a matching ACL node or it won't render for non-root admins).

## File anatomy

`<config><sections><section><groups><group><fields><field>`. Top-level `<tabs>` declares the left-nav tab a section attaches to via `<tab>name</tab>`.

Real example from `app/code/core/Mage/Sales/etc/system.xml`:

```xml
<sections>
    <sales translate="label" module="sales">
        <class>separator-top</class>
        <label>Sales</label>
        <tab>sales</tab>
        <sort_order>300</sort_order>
        <show_in_default>1</show_in_default>
        <show_in_website>1</show_in_website>
        <show_in_store>1</show_in_store>
        <groups>
            <reorder translate="label">
                <label>Reorder</label>
                <sort_order>20</sort_order>
                <show_in_default>1</show_in_default>
                <show_in_website>1</show_in_website>
                <show_in_store>1</show_in_store>
                <fields>
                    <allow translate="label">
                        <label>Allow Reorder</label>
                        <frontend_type>select</frontend_type>
                        <source_model>adminhtml/system_config_source_yesno</source_model>
                        <sort_order>1</sort_order>
                        <show_in_default>1</show_in_default>
                        <show_in_website>1</show_in_website>
                        <show_in_store>1</show_in_store>
                    </allow>
                </fields>
            </reorder>
        </groups>
    </sales>
</sections>
```

Key attributes:

- `translate="label"` (or `"label comment"`) plus `module="sales"` on the section — picks the locale CSV for the listed nodes. Only nodes named in `translate=` are translated.
- `sort_order` — ordering at every level (tab, section, group, field).
- `show_in_default` / `show_in_website` / `show_in_store` — which scope switcher renders the field. A field with `show_in_store=0` is hidden when the admin is scoped to a store view; reads still inherit the website/default value.
- `frontend_type` — `text`, `textarea`, `select`, `multiselect`, `password`, `obscure`, `image`, `file`, `date`, `time`, `label`, `allowspecific`, etc. Default is `text`.
- `<validate>required-entry validate-digits validate-zero-or-greater</validate>` — space-separated client-side validators (Prototype Validation classes).
- `<comment><![CDATA[...]]></comment>` — help text under the field. CDATA lets you include HTML.

## Default values in config.xml

`system.xml` declares the form; **defaults live in `etc/config.xml` under `<default>`**, mirroring the `section/group/field` path. Example from `app/code/core/Mage/Sales/etc/config.xml`:

```xml
<default>
    <sales>
        <reorder>
            <allow>1</allow>
        </reorder>
    </sales>
    <sales_email>
        <order>
            <enabled>1</enabled>
            <template>sales_email_order_template</template>
            <identity>sales</identity>
            <copy_method>bcc</copy_method>
        </order>
    </sales_email>
</default>
```

A field with no DB row falls back to the `<default>` value. Fields with no `<default>` entry return `null`.

## Source models — dropdowns

A source model exposes options for `select` / `multiselect` fields. Convention: `toOptionArray(): array<int, array{value: mixed, label: string}>`. Real example from `app/code/core/Mage/Adminhtml/Model/System/Config/Source/Yesno.php`:

```php
class Mage_Adminhtml_Model_System_Config_Source_Yesno
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes')],
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No')],
        ];
    }
}
```

Reference it as `<source_model>adminhtml/system_config_source_yesno</source_model>` (alias resolves through `<global><models>`). Common reusable ones: `adminhtml/system_config_source_yesno`, `adminhtml/system_config_source_enabledisable`, `adminhtml/system_config_source_email_identity`, `adminhtml/system_config_source_email_template`, `adminhtml/system_config_source_email_method`.

## Backend models — validation / transforms / save hooks

Extend `Mage_Core_Model_Config_Data` and override `_beforeSave()` / `_afterLoad()`. Wire with `<backend_model>module/path</backend_model>`. Stock library under `Mage_Adminhtml_Model_System_Config_Backend_*` covers: image upload (`_image`, `_image_pdf`), file upload (`_file`), email-address CSV (`_email_address`), regex/serialized/cookie/locale validators, currency lists, etc.

## Encrypted fields

For credentials, pair `frontend_type=obscure` with `backend_model=adminhtml/system_config_backend_encrypted`. From `app/code/core/Mage/Paygate/etc/system.xml`:

```xml
<trans_key translate="label">
    <label>Transaction Key</label>
    <frontend_type>obscure</frontend_type>
    <backend_model>adminhtml/system_config_backend_encrypted</backend_model>
    <sort_order>25</sort_order>
    <show_in_default>1</show_in_default>
    <show_in_website>1</show_in_website>
    <show_in_store>0</show_in_store>
    <depends><active>1</active></depends>
</trans_key>
```

Backend implementation (`Mage_Adminhtml_Model_System_Config_Backend_Encrypted`) encrypts in `_beforeSave()` and decrypts in `_afterLoad()`. **`Mage::getStoreConfig()` returns the raw encrypted ciphertext** — it does not reload through the backend model. To read the plaintext from app code:

```php
$cipher = Mage::getStoreConfig('payment/authorizenet/trans_key', $store);
$plain  = Mage::helper('core')->decrypt($cipher);
```

The admin form re-displays `obscure` fields as `****`; preserving the masked value on save short-circuits re-encryption (see `_beforeSave` in `Backend/Encrypted.php`).

## Field dependencies

`<depends><field>value</field></depends>` greys out / hides a field unless the named sibling field equals the value. Both must live in the same group. Example: every Paygate field above is gated on `<active>1</active>` so the form collapses when the payment method is disabled.

## XML_PATH constants and reading config

Convention: define `XML_PATH_*` constants on the relevant model or helper. The full path is `section/group/field`. From `app/code/core/Mage/Sales/Model/Order.php`:

```php
public const XML_PATH_EMAIL_COPY_TO     = 'sales_email/order/copy_to';
public const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order/copy_method';
public const XML_PATH_EMAIL_ENABLED     = 'sales_email/order/enabled';
```

Read it (always pass `$store` so scope inheritance works on multi-store):

```php
// Mage_Sales_Helper_Data
public function canSendNewOrderConfirmationEmail($store = null)
{
    return Mage::getStoreConfigFlag(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, $store);
}
```

- `Mage::getStoreConfig($path, $store)` — string/scalar.
- `Mage::getStoreConfigFlag($path, $store)` — boolean (`'1'`/`'true'`/`'yes'` → `true`).
- `$store` accepts a store id, code, or `Mage_Core_Model_Store` instance. `null` means "current store" — fine in request context, dangerous in cron/API/observers that touch multiple stores. **Always thread `$store` through.**

## Scope inheritance

Saved config rows live in `core_config_data` keyed by `(scope, scope_id, path)` with scopes `default`, `websites`, `stores`. Resolution order at read time: store → website → default. A blank/missing store-scoped row inherits website; a blank website row inherits default; a missing default falls back to the `<default>` block in `etc/config.xml`. The "Use Default" checkbox in the admin form deletes the scope-specific row.

`show_in_store=0` only affects rendering — code can still read store-scoped values that exist (they just have to be set via website or default).

## ACL gating (cross-ref `openmage-acl-adminhtml`)

Every `<section>` must have a matching ACL resource under `<acl><resources><admin><children><system><children><config><children><SECTION_NAME>` in the module's `etc/adminhtml.xml`, otherwise non-root admin roles can't see or save the section. Group/field-level ACL is also possible. Add the ACL entry whenever you add a section.

## Adding a new field — checklist

1. Add `<field>` under the right `<section>/<group>` in `etc/system.xml` with `frontend_type`, scope flags, `sort_order`, label.
2. If it's a dropdown, point `<source_model>` at an existing source model or write a new one returning `toOptionArray()`.
3. If it's a credential, set `frontend_type=obscure` + `backend_model=adminhtml/system_config_backend_encrypted` and decrypt on read with `Mage::helper('core')->decrypt()`.
4. Add the default value under `<default>` in `etc/config.xml` (mirroring `section/group/field`).
5. Add an `XML_PATH_FOO = 'section/group/field'` constant on the helper or model that reads it.
6. Use `Mage::getStoreConfig(self::XML_PATH_FOO, $store)` / `getStoreConfigFlag(...)` — pass `$store` through.
7. If you added a brand-new section, add the matching ACL resource node (see `openmage-acl-adminhtml`).
8. If labels need translation, add the strings to `app/locale/<locale>/Mage_<Module>.csv` and ensure `translate="label"` + `module="<module>"` are set on the node.
