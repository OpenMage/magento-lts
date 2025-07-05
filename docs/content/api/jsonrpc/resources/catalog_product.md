# Catalog Product API

## Introduction

The Catalog Product API allows you to manage products in your OpenMage store. You can retrieve product information, create new products, update existing ones, delete products, and manage product special prices.

## Available Methods

### `currentStore`

Sets the current store for product operations.

**Method Name**: `catalog_product.currentStore`

**Parameters**:

- `store` (string|int, required) - Store ID or code

**Return**:

- (int) - Current store ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.currentStore",
    "default"
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 1,
  "id": 1
}
```

### `list`

Retrieve list of products with basic info.

**Method Name**: `catalog_product.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `product_id` (int|array) - Filter by product ID(s)
  - `set` (int|array) - Filter by attribute set ID(s)
  - `type` (string|array) - Filter by product type(s)
  - `sku` (string|array) - Filter by SKU(s)
  - `name` (string|array) - Filter by name(s)
  - `status` (int|array) - Filter by status(es)
  - Other attributes can also be used as filters
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Array of products with the following structure:
  - `product_id` (int) - Product ID
  - `sku` (string) - Product SKU
  - `name` (string) - Product name
  - `set` (int) - Attribute set ID
  - `type` (string) - Product type
  - `category_ids` (array) - Array of category IDs
  - `website_ids` (array) - Array of website IDs

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.list",
    [{"type": "simple"}, "default"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": [
    {
      "product_id": 1,
      "sku": "product1",
      "name": "Product 1",
      "set": 4,
      "type": "simple",
      "category_ids": [2, 3, 4],
      "website_ids": [1]
    },
    {
      "product_id": 2,
      "sku": "product2",
      "name": "Product 2",
      "set": 4,
      "type": "simple",
      "category_ids": [2, 3],
      "website_ids": [1]
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `filters_invalid` - Invalid filters provided

### `info`

Retrieve product info.

**Method Name**: `catalog_product.info`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `store` (string|int, optional) - Store ID or code
- `attributes` (array, optional) - Array of attributes to return
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (array) - Product data with the following structure:
  - `product_id` (int) - Product ID
  - `sku` (string) - Product SKU
  - `set` (int) - Attribute set ID
  - `type` (string) - Product type
  - `categories` (array) - Array of category IDs
  - `websites` (array) - Array of website IDs
  - Additional attributes as requested

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.info",
    ["product1", "default", ["name", "description", "price"], "sku"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "product_id": 1,
    "sku": "product1",
    "set": 4,
    "type": "simple",
    "categories": [2, 3, 4],
    "websites": [1],
    "name": "Product 1",
    "description": "Product 1 description",
    "price": 19.99
  },
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist

### `create`

Create new product.

**Method Name**: `catalog_product.create`

**Parameters**:

- `type` (string, required) - Product type (simple, configurable, grouped, virtual, bundle, downloadable)
- `set` (int, required) - Attribute set ID
- `sku` (string, required) - Product SKU
- `productData` (array, required) - Product data:
  - `name` (string, required) - Product name
  - `description` (string, optional) - Product description
  - `short_description` (string, optional) - Product short description
  - `weight` (float, optional) - Product weight
  - `status` (int, optional) - Product status (1 - enabled, 2 - disabled)
  - `url_key` (string, optional) - URL key
  - `visibility` (int, optional) - Visibility (1 - Not Visible, 2 - Catalog, 3 - Search, 4 - Catalog, Search)
  - `price` (float, optional) - Product price
  - `tax_class_id` (int, optional) - Tax class ID
  - `meta_title` (string, optional) - Meta title
  - `meta_keyword` (string, optional) - Meta keywords
  - `meta_description` (string, optional) - Meta description
  - `stock_data` (array, optional) - Stock data:
    - `qty` (float) - Quantity
    - `is_in_stock` (int) - Is in stock (1 - yes, 0 - no)
    - `manage_stock` (int) - Manage stock (1 - yes, 0 - no)
    - `use_config_manage_stock` (int) - Use config settings for managing stock (1 - yes, 0 - no)
  - `website_ids` (array, optional) - Array of website IDs
  - `category_ids` (array, optional) - Array of category IDs
  - Other custom attributes
- `store` (string|int, optional) - Store ID or code

**Return**:

- (int) - ID of the created product

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.create",
    [
      "simple",
      4,
      "new-product",
      {
        "name": "New Product",
        "description": "New product description",
        "short_description": "Short description",
        "weight": 1.0,
        "status": 1,
        "visibility": 4,
        "price": 29.99,
        "tax_class_id": 2,
        "stock_data": {
          "qty": 100,
          "is_in_stock": 1
        },
        "website_ids": [1],
        "category_ids": [2, 3]
      },
      "default"
    ]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 10,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `product_type_not_exists` - Product type does not exist
- `product_attribute_set_not_exists` - Attribute set does not exist
- `product_attribute_set_not_valid` - Attribute set is not valid for the product

### `update`

Update product data.

**Method Name**: `catalog_product.update`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `productData` (array, required) - Product data to update (same structure as in create method)
- `store` (string|int, optional) - Store ID or code
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.update",
    [
      "product1",
      {
        "name": "Updated Product Name",
        "description": "Updated description",
        "price": 24.99
      },
      "default",
      "sku"
    ]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": true,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `product_not_exists` - Product does not exist

### `multiUpdate`

Update multiple products in a single call.

**Method Name**: `catalog_product.multiUpdate`

**Parameters**:

- `productIds` (array, required) - Array of product IDs or SKUs
- `productData` (array, required) - Product data to update (same structure as in update method)
- `store` (string|int, optional) - Store ID or code
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.multiUpdate",
    [
      ["product1", "product2"],
      {
        "status": 1,
        "price": 24.99
      },
      "default",
      "sku"
    ]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": true,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `product_not_exists` - One or more products do not exist

### `delete`

Delete product.

**Method Name**: `catalog_product.delete`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.delete",
    ["product1", "sku"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": true,
  "id": 1
}
```

**Possible Errors**:

- `not_deleted` - Product could not be deleted
- `product_not_exists` - Product does not exist

### `getSpecialPrice`

Retrieve product special price.

**Method Name**: `catalog_product.getSpecialPrice`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Special price data with the following structure:
  - `special_price` (float) - Special price
  - `special_from_date` (string) - Special price from date
  - `special_to_date` (string) - Special price to date

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.getSpecialPrice",
    ["product1", "default"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "special_price": 19.99,
    "special_from_date": "2023-01-01 00:00:00",
    "special_to_date": "2023-12-31 23:59:59"
  },
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist

### `setSpecialPrice`

Update product special price.

**Method Name**: `catalog_product.setSpecialPrice`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `specialPrice` (float, optional) - Special price
- `fromDate` (string, optional) - Special price from date (format: `YYYY-MM-DD`)
- `toDate` (string, optional) - Special price to date (format: `YYYY-MM-DD`)
- `store` (string|int, optional) - Store ID or code

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.setSpecialPrice",
    ["product1", 19.99, "2023-01-01", "2023-12-31", "default"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": true,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `product_not_exists` - Product does not exist

### `listOfAdditionalAttributes`

Get list of additional attributes which are not in default create/update list.

**Method Name**: `catalog_product.listOfAdditionalAttributes`

**Parameters**:

- `productType` (string, required) - Product type
- `attributeSetId` (int, required) - Attribute set ID

**Return**:

- (array) - Array of attributes with the following structure:
  - `attribute_id` (int) - Attribute ID
  - `code` (string) - Attribute code
  - `type` (string) - Attribute type
  - `required` (boolean) - Whether the attribute is required
  - `scope` (string) - Attribute scope (global, website, store)

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product.listOfAdditionalAttributes",
    ["simple", 4]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": [
    {
      "attribute_id": 142,
      "code": "manufacturer",
      "type": "select",
      "required": false,
      "scope": "global"
    },
    {
      "attribute_id": 143,
      "code": "color",
      "type": "select",
      "required": false,
      "scope": "global"
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `product_type_not_exists` - Product type does not exist
- `product_attribute_set_not_exists` - Attribute set does not exist