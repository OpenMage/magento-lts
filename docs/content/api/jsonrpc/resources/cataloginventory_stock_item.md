# Catalog Inventory Stock Item API

## Introduction

The Catalog Inventory Stock Item API allows you to manage product inventory in your OpenMage store. You can retrieve stock information for products and update stock data for individual or multiple products. This API is also sometimes referred to as the product_stock API.

## Available Methods

### list

Retrieve stock information for products.

**Method Name**: `cataloginventory_stock_item.list`

**Parameters**:

- `productIds` (array, required) - Array of product IDs or SKUs
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (array) - Array of stock items with the following structure:
  - `product_id` (int) - Product ID
  - `sku` (string) - Product SKU
  - `qty` (float) - Quantity
  - `is_in_stock` (int) - Is in stock (1 - yes, 0 - no)
  - `manage_stock` (int) - Manage stock (1 - yes, 0 - no)
  - `use_config_manage_stock` (int) - Use config settings for managing stock (1 - yes, 0 - no)
  - `min_qty` (float) - Minimum quantity
  - `use_config_min_qty` (int) - Use config settings for minimum quantity (1 - yes, 0 - no)
  - `min_sale_qty` (float) - Minimum sale quantity
  - `use_config_min_sale_qty` (int) - Use config settings for minimum sale quantity (1 - yes, 0 - no)
  - `max_sale_qty` (float) - Maximum sale quantity
  - `use_config_max_sale_qty` (int) - Use config settings for maximum sale quantity (1 - yes, 0 - no)
  - `is_qty_decimal` (int) - Is quantity decimal (1 - yes, 0 - no)
  - `backorders` (int) - Backorders status (0 - No Backorders, 1 - Allow Qty Below 0, 2 - Allow Qty Below 0 and Notify Customer)
  - `use_config_backorders` (int) - Use config settings for backorders (1 - yes, 0 - no)
  - `notify_stock_qty` (float) - Notify quantity below
  - `use_config_notify_stock_qty` (int) - Use config settings for notify quantity below (1 - yes, 0 - no)

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "cataloginventory_stock_item.list",
    [["product1", "product2"], "sku"]
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
      "qty": 100.0000,
      "is_in_stock": 1,
      "manage_stock": 1,
      "use_config_manage_stock": 0,
      "min_qty": 0.0000,
      "use_config_min_qty": 1,
      "min_sale_qty": 1.0000,
      "use_config_min_sale_qty": 1,
      "max_sale_qty": 10000.0000,
      "use_config_max_sale_qty": 1,
      "is_qty_decimal": 0,
      "backorders": 0,
      "use_config_backorders": 1,
      "notify_stock_qty": 1.0000,
      "use_config_notify_stock_qty": 1
    },
    {
      "product_id": 2,
      "sku": "product2",
      "qty": 50.0000,
      "is_in_stock": 1,
      "manage_stock": 1,
      "use_config_manage_stock": 0,
      "min_qty": 0.0000,
      "use_config_min_qty": 1,
      "min_sale_qty": 1.0000,
      "use_config_min_sale_qty": 1,
      "max_sale_qty": 10000.0000,
      "use_config_max_sale_qty": 1,
      "is_qty_decimal": 0,
      "backorders": 0,
      "use_config_backorders": 1,
      "notify_stock_qty": 1.0000,
      "use_config_notify_stock_qty": 1
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - One or more products do not exist

### update

Update stock information for a product.

**Method Name**: `cataloginventory_stock_item.update`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `stockData` (array, required) - Stock data:
  - `qty` (float, optional) - Quantity
  - `is_in_stock` (int, optional) - Is in stock (1 - yes, 0 - no)
  - `manage_stock` (int, optional) - Manage stock (1 - yes, 0 - no)
  - `use_config_manage_stock` (int, optional) - Use config settings for managing stock (1 - yes, 0 - no)
  - `min_qty` (float, optional) - Minimum quantity
  - `use_config_min_qty` (int, optional) - Use config settings for minimum quantity (1 - yes, 0 - no)
  - `min_sale_qty` (float, optional) - Minimum sale quantity
  - `use_config_min_sale_qty` (int, optional) - Use config settings for minimum sale quantity (1 - yes, 0 - no)
  - `max_sale_qty` (float, optional) - Maximum sale quantity
  - `use_config_max_sale_qty` (int, optional) - Use config settings for maximum sale quantity (1 - yes, 0 - no)
  - `is_qty_decimal` (int, optional) - Is quantity decimal (1 - yes, 0 - no)
  - `backorders` (int, optional) - Backorders status (0 - No Backorders, 1 - Allow Qty Below 0, 2 - Allow Qty Below 0 and Notify Customer)
  - `use_config_backorders` (int, optional) - Use config settings for backorders (1 - yes, 0 - no)
  - `notify_stock_qty` (float, optional) - Notify quantity below
  - `use_config_notify_stock_qty` (int, optional) - Use config settings for notify quantity below (1 - yes, 0 - no)
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
    "cataloginventory_stock_item.update",
    [
      "product1",
      {
        "qty": 75.0000,
        "is_in_stock": 1,
        "manage_stock": 1
      },
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

- `product_not_exists` - Product does not exist
- `data_invalid` - Invalid data provided

### `multiUpdate`

Update stock information for multiple products in a single call.

**Method Name**: `cataloginventory_stock_item.multiUpdate`

**Parameters**:

- `productIds` (array, required) - Array of product IDs or SKUs
- `stockData` (array, required) - Stock data (same structure as in update method)
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
    "cataloginventory_stock_item.multiUpdate",
    [
      ["product1", "product2"],
      {
        "qty": 100.0000,
        "is_in_stock": 1
      },
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

- `product_not_exists` - One or more products do not exist
- `data_invalid` - Invalid data provided

**Notes**:

- When updating stock information, you only need to include the fields you want to change. Other fields will retain their current values.
- The `use_config_*` fields determine whether to use the system configuration value for the corresponding setting. When set to 1, the system configuration value is used regardless of the value specified for the setting.
- When managing inventory for configurable, grouped, or bundle products, you should update the stock for the associated simple products, as the parent product's stock is calculated based on its child products.