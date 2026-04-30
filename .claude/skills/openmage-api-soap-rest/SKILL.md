---
name: openmage-api-soap-rest
description: OpenMage web APIs — Mage_Api (SOAP v1/v2 + XML-RPC) via etc/api.xml, Mage_Api2 (REST) via etc/api2.xml, OAuth token flow, wsdl.xml companions. Use when adding an API endpoint, editing files under */etc/api.xml or */etc/api2.xml, working with Mage_Api*/Mage_Oauth*, or wiring web service ACL.
---

# OpenMage Web APIs (SOAP / XML-RPC / REST)

OpenMage ships **two completely separate API stacks**. They share nothing — different XML, different controllers, different ACL, different auth.

| Stack | Module | Declared in | Entry point | Auth |
|---|---|---|---|---|
| SOAP v1 / SOAP v2 / XML-RPC / JSON-RPC | `Mage_Api` | `*/etc/api.xml` (+ `wsdl.xml`, `wsi.xml`) | `api.php` → `/api/{soap,v2_soap,xmlrpc,jsonrpc}` | API user + role (session token) |
| REST | `Mage_Api2` | `*/etc/api2.xml` | `api.php` → `/api/rest/...` | OAuth 1.0a (admin/customer) or anonymous (guest) |

Bootstrap: both stacks dispatch through `api.php` (not `index.php`). `Mage_Api/controllers/SoapController.php`, `XmlrpcController.php`, `JsonrpcController.php`, and `V2/SoapController.php` cover `Mage_Api`. REST is routed via `Mage_Api2_Model_Router` from the same front controller.

## Mage_Api — SOAP v1/v2 + XML-RPC

One handler class implements all transports. Define the resource and its methods in `etc/api.xml`; declare the WSDL types in `etc/wsdl.xml` (and `wsi.xml` for SOAP-v2 WS-I compliant mode).

`app/code/core/Mage/Catalog/etc/api.xml` (trimmed):

```xml
<config>
    <api>
        <resources>
            <catalog_product translate="title" module="catalog">
                <title>Product API</title>
                <model>catalog/product_api</model>
                <acl>catalog/product</acl>
                <methods>
                    <list translate="title" module="catalog">
                        <title>Retrieve products list by filters</title>
                        <method>items</method>           <!-- maps SOAP `list` -> PHP items() -->
                        <acl>catalog/product/info</acl>
                    </list>
                    <create><acl>catalog/product/create</acl></create>
                    <update><acl>catalog/product/update</acl></update>
                    <delete><acl>catalog/product/delete</acl></delete>
                </methods>
                <faults module="catalog">
                    <not_exists><code>101</code><message>Product not exists.</message></not_exists>
                    <data_invalid><code>102</code><message>Invalid data given. Details in error message.</message></data_invalid>
                </faults>
            </catalog_product>
        </resources>
        <acl>
            <resources>
                <catalog translate="title" module="catalog">
                    <title>Catalog</title>
                    <product translate="title" module="catalog">
                        <title>Product</title>
                        <create><title>Create</title></create>
                        <update><title>Update</title></update>
                        <delete><title>Delete</title></delete>
                        <info><title>Retrieve data</title></info>
                    </product>
                </catalog>
            </resources>
        </acl>
    </api>
</config>
```

Key rules:

- `<model>` is a class alias resolving via the standard models registry (`catalog/product_api` → `Mage_Catalog_Model_Product_Api`). Method names in `<methods>` map 1:1 to PHP methods unless overridden by `<method>`.
- `<acl>` strings on a resource or method must exist as nodes under `<api><acl><resources>` or the method 403s.
- `<faults>` is the SOAP fault catalog. Throw via `$this->_fault('not_exists')` from the API model — it picks the matching code/message.
- The same `<resources>` block backs SOAP v1, SOAP v2, XML-RPC, and JSON-RPC. Only the wire format differs.

The companion `wsdl.xml` (one per module) declares the SOAP-v1 type system. Keep it in sync when you add a method or change a return shape:

```xml
<definitions xmlns:typens="urn:OpenMage" ... name="OpenMage" targetNamespace="urn:OpenMage">
    <types>
        <schema xmlns="http://www.w3.org/2001/XMLSchema" targetNamespace="urn:OpenMage">
            <complexType name="catalogProductEntity">
                <all>
                    <element name="product_id" type="xsd:string"/>
                    <element name="sku"        type="xsd:string"/>
                    <element name="name"       type="xsd:string"/>
                    ...
```

`wsi.xml` is the WS-I compliant variant for SOAP v2 (toggle: `Services > Web Services > SOAP/XML-RPC > Enable WS-I Compliance Mode`).

Validation belongs **inside the API model** (`Mage_Catalog_Model_Product_Api::create`, etc.) — not in a request validator. Throw `$this->_fault('data_invalid', $details)` for bad input.

## Mage_Api2 — REST

REST is its own world. Resources, attributes, routes, and per-user-type privileges all live in `etc/api2.xml`. There is no `<faults>` block — REST returns HTTP status codes plus a JSON/XML body.

`app/code/core/Mage/Catalog/etc/api2.xml` (trimmed):

```xml
<config>
    <api2>
        <resource_groups>
            <catalog translate="title" module="api2">
                <title>Catalog</title>
                <children>
                    <catalog_product><title>Product</title></catalog_product>
                </children>
            </catalog>
        </resource_groups>
        <resources>
            <product translate="title" module="api2">
                <group>catalog_product</group>
                <model>catalog/api2_product</model>
                <working_model>catalog/product</working_model>
                <title>Catalog Product</title>
                <privileges>
                    <admin>    <create>1</create><retrieve>1</retrieve><update>1</update><delete>1</delete></admin>
                    <customer> <retrieve>1</retrieve></customer>
                    <guest>    <retrieve>1</retrieve></guest>
                </privileges>
                <attributes translate="entity_id type_id ..." module="api2">
                    <entity_id>Product ID</entity_id>
                    <type_id>Product Type</type_id>
                    <stock_data>Inventory Data</stock_data>
                    ...
                </attributes>
                <exclude_attributes>
                    <customer><read><attribute_set_id>1</attribute_set_id></read></customer>
                    <admin><write><entity_id>1</entity_id></write></admin>
                </exclude_attributes>
                <entity_only_attributes>
                    <customer><read><url>1</url></read></customer>
                </entity_only_attributes>
                <routes>
                    <route_entity>            <route>/products/:id</route>             <action_type>entity</action_type></route_entity>
                    <route_collection>        <route>/products</route>                   <action_type>collection</action_type></route_collection>
                    <route_collection_with_store><route>/products/store/:store</route>   <action_type>collection</action_type></route_collection_with_store>
                </routes>
                <versions>1</versions>
            </product>
        </resources>
    </api2>
</config>
```

Required pieces and what they do:

- `<model>` — REST resource handler (`Mage_Catalog_Model_Api2_Product`). Subclasses `Mage_Api2_Model_Resource` and implements `_create/_retrieve/_update/_delete` (entity) or `_retrieveCollection/_multicreate/...` (collection). The version suffix lives in the class (`..._Product_Rest_Admin_V1`).
- `<working_model>` — the underlying alias the resource hydrates (typically the EAV / AR model alias).
- `<privileges>` — three fixed user types: **`admin`**, **`customer`**, **`guest`**. Each lists which CRUD operations it may perform. Missing entry = forbidden.
- `<attributes>` — the **whitelist** of fields the API knows about. Anything not listed is silently dropped from request and response.
- `<exclude_attributes>` per user-type / direction (`read`/`write`) — narrows the whitelist further.
- `<entity_only_attributes>` — fields exposed on `/products/:id` but not on the collection `/products`, per user-type.
- `<routes>` — `:id`, `:store` etc. are placeholders; `action_type` is `entity` or `collection`.
- `<versions>` — comma list, drives the `Rest_<UserType>_V<n>` class suffix.

Per-user-type customization happens by subclassing the resource:
`Mage_Catalog_Model_Api2_Product_Rest_Admin_V1`,
`Mage_Catalog_Model_Api2_Product_Rest_Customer_V1`,
`Mage_Catalog_Model_Api2_Product_Rest_Guest_V1`.
Override only the methods that user type is allowed to call.

Validation lives in two places:

1. **Schema validation** — `Mage_Api2_Model_Resource::_validate()` runs against the EAV attribute config and the `<attributes>` whitelist. Off-list fields are dropped before the model sees them.
2. **Business validation** — inside the resource model's `_create`/`_update` methods. Throw `Mage_Api2_Exception` with a status code (`Mage_Api2_Model_Server::HTTP_BAD_REQUEST` etc.).

## OAuth (Mage_Oauth)

REST is **OAuth 1.0a-protected for `admin` and `customer`**. `guest` is the one anonymous bucket. Three-legged flow:

1. `POST /oauth/initiate` → `Mage_Oauth_InitiateController` issues a request token.
2. `GET /oauth/authorize` (customer) or `/adminhtml/oauth_authorize` (admin) → user logs in and approves the consumer.
3. `POST /oauth/token` → `Mage_Oauth_TokenController` exchanges the verified request token for an access token.

Subsequent REST requests sign with the access token (HMAC-SHA1 over the request).

Key models:

- `Mage_Oauth_Model_Consumer` — registered application (key + secret), stored in `oauth_consumer`.
- `Mage_Oauth_Model_Token` — request / access tokens in `oauth_token`. Type = `request` | `access`.
- `Mage_Oauth_Model_Nonce` — replay protection in `oauth_nonce`.
- `Mage_Oauth_Model_Server` — protocol-level signature/nonce/timestamp validation.

Consumer registration: `System > Web Services > REST - OAuth Consumers` (admin UI). Each consumer is bound to a user type via the access-token grant flow.

ACL for REST per-attribute access is configured at `System > Web Services > REST - Roles` and `... - Attributes`; the role records bridge `Mage_Api2` resources to admin users.

## Where each thing lives

- **Add a SOAP/XML-RPC method:** edit `<Module>/etc/api.xml` (`<resources>` + `<acl>`), add the PHP method on the `*_Api` model, declare types in `wsdl.xml` / `wsi.xml`, throw via `_fault()` referencing a `<faults>` entry.
- **Add a REST resource:** edit `<Module>/etc/api2.xml` (`<resources>`, `<privileges>`, `<attributes>`, `<routes>`), implement the per-user-type subclasses under `Model/Api2/<Resource>/Rest/<UserType>/V<n>.php`.
- **Restrict by role:** Mage_Api uses the `<acl>` tree it declares; Mage_Api2 uses the per-user-type `<privileges>` block plus admin role attributes.

## Cross-references

- `openmage-acl-adminhtml` — the SOAP `<acl>` tree mirrors admin ACL conventions; admin UI for Mage_Api roles ties into `adminhtml.xml`.
- `openmage-system-config` — REST/OAuth toggles (cleanup probability, token TTL, consumer settings) live under `system.xml` (e.g. `oauth/cleanup/*`, `oauth/email/*`).
- `openmage-eav` — `working_model` for catalog/customer REST resources is EAV-backed; attribute whitelist must match attribute codes.
- `openmage-controllers-routing` — `api.php` is the dedicated front controller; routes for `/oauth/*` are normal frontend routes declared in `Mage_Oauth/etc/config.xml`.
