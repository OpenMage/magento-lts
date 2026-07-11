# Sales Order API

## Introduction

The Sales Order API allows you to manage orders in your OpenMage store. You can retrieve order information, add comments to orders, and perform various order operations such as holding, un-holding, and canceling orders.

## Available Methods

### list

Retrieve list of orders with basic info.

**Method Name**: `sales_order.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `order_id` (int|array) - Filter by order ID(s)
  - `status` (string|array) - Filter by order status(es)
  - `state` (string|array) - Filter by order state(s)
  - `customer_id` (int|array) - Filter by customer ID(s)
  - `created_at` (string|array) - Filter by creation date
  - `updated_at` (string|array) - Filter by update date
  - Other attributes can also be used as filters
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Array of orders with the following structure:
  - `increment_id` (string) - Order increment ID
  - `order_id` (int) - Order ID
  - `created_at` (string) - Creation date
  - `updated_at` (string) - Update date
  - `status` (string) - Order status
  - `state` (string) - Order state
  - `customer_id` (int) - Customer ID
  - `base_grand_total` (float) - Base grand total
  - `grand_total` (float) - Grand total
  - `store_id` (int) - Store ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.list",
    [{"status": "pending"}]
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
      "increment_id": "100000001",
      "order_id": 1,
      "created_at": "2023-01-15 14:30:12",
      "updated_at": "2023-01-15 14:30:12",
      "status": "pending",
      "state": "new",
      "customer_id": 1,
      "base_grand_total": 150.00,
      "grand_total": 150.00,
      "store_id": 1
    },
    {
      "increment_id": "100000002",
      "order_id": 2,
      "created_at": "2023-01-16 09:45:23",
      "updated_at": "2023-01-16 09:45:23",
      "status": "pending",
      "state": "new",
      "customer_id": 2,
      "base_grand_total": 75.50,
      "grand_total": 75.50,
      "store_id": 1
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `filters_invalid` - Invalid filters provided
- `store_not_exists` - Requested store does not exist

### info

Retrieve detailed order information.

**Method Name**: `sales_order.info`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID

**Return**:

- (object) - Order information with the following structure:
  - `increment_id` (string) - Order increment ID
  - `order_id` (int) - Order ID
  - `created_at` (string) - Creation date
  - `updated_at` (string) - Update date
  - `status` (string) - Order status
  - `state` (string) - Order state
  - `customer_id` (int) - Customer ID
  - `customer_firstname` (string) - Customer first name
  - `customer_lastname` (string) - Customer last name
  - `customer_email` (string) - Customer email
  - `base_grand_total` (float) - Base grand total
  - `grand_total` (float) - Grand total
  - `base_subtotal` (float) - Base subtotal
  - `subtotal` (float) - Subtotal
  - `base_shipping_amount` (float) - Base shipping amount
  - `shipping_amount` (float) - Shipping amount
  - `base_tax_amount` (float) - Base tax amount
  - `tax_amount` (float) - Tax amount
  - `store_id` (int) - Store ID
  - `shipping_address` (object) - Shipping address information
  - `billing_address` (object) - Billing address information
  - `items` (array) - Array of order items
  - `payment` (object) - Payment information
  - `status_history` (array) - Order status history

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.info",
    "100000001"
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "increment_id": "100000001",
    "order_id": 1,
    "created_at": "2023-01-15 14:30:12",
    "updated_at": "2023-01-15 14:30:12",
    "status": "pending",
    "state": "new",
    "customer_id": 1,
    "customer_firstname": "John",
    "customer_lastname": "Doe",
    "customer_email": "john.doe@example.com",
    "base_grand_total": 150.00,
    "grand_total": 150.00,
    "base_subtotal": 140.00,
    "subtotal": 140.00,
    "base_shipping_amount": 10.00,
    "shipping_amount": 10.00,
    "base_tax_amount": 0.00,
    "tax_amount": 0.00,
    "store_id": 1,
    "shipping_address": {
      "firstname": "John",
      "lastname": "Doe",
      "street": "123 Main St",
      "city": "Anytown",
      "region": "California",
      "postcode": "12345",
      "country_id": "US",
      "telephone": "555-123-4567"
    },
    "billing_address": {
      "firstname": "John",
      "lastname": "Doe",
      "street": "123 Main St",
      "city": "Anytown",
      "region": "California",
      "postcode": "12345",
      "country_id": "US",
      "telephone": "555-123-4567"
    },
    "items": [
      {
        "item_id": 1,
        "product_id": 123,
        "sku": "product123",
        "name": "Test Product",
        "qty_ordered": 2,
        "price": 70.00,
        "base_price": 70.00,
        "row_total": 140.00,
        "base_row_total": 140.00
      }
    ],
    "payment": {
      "method": "checkmo",
      "amount_ordered": 150.00,
      "base_amount_ordered": 150.00
    },
    "status_history": [
      {
        "created_at": "2023-01-15 14:30:12",
        "status": "pending",
        "comment": "Order placed"
      }
    ]
  },
  "id": 1
}
```

**Possible Errors**:

- `order_not_exists` - Order does not exist

### `addComment`

Add a comment to an order.

**Method Name**: `sales_order.addComment`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID
- `status` (string, required) - Order status
- `comment` (string, optional) - Comment text
- `notify` (boolean, optional) - Whether to notify customer (default: false)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.addComment",
    ["100000001", "processing", "Order is being processed", true]
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

- `order_not_exists` - Order does not exist
- `status_not_exists` - Status does not exist

### hold

Place an order on hold.

**Method Name**: `sales_order.hold`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.hold",
    "100000001"
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

- `order_not_exists` - Order does not exist
- `order_not_holdable` - Order cannot be put on hold

### `unhold`

Release an order from hold.

**Method Name**: `sales_order.unhold`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.unhold",
    "100000001"
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

- `order_not_exists` - Order does not exist
- `order_not_unholdable` - Order is not on hold

### `cancel`

Cancel an order.

**Method Name**: `sales_order.cancel`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order.cancel",
    "100000001"
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

- `order_not_exists` - Order does not exist
- `order_not_cancelable` - Order cannot be canceled