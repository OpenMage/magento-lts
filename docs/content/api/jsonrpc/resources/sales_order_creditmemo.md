# Sales Order Credit Memo API

## Introduction

The Sales Order Credit Memo API allows you to manage credit memos in your OpenMage store. You can retrieve credit memo information, create new credit memos, add comments, and cancel credit memos.

## Available Methods

### list

Retrieve list of credit memos with basic info.

**Method Name**: `sales_order_creditmemo.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `creditmemo_id` (int|array) - Filter by credit memo ID(s)
  - `order_id` (int|array) - Filter by order ID(s)
  - `increment_id` (string|array) - Filter by increment ID(s)
  - `created_at` (string|array) - Filter by creation date
  - `order_increment_id` (string|array) - Filter by order increment ID(s)
  - Other attributes can also be used as filters

**Return**:

- (array) - Array of credit memos with the following structure:
  - `increment_id` (string) - Credit memo increment ID
  - `creditmemo_id` (int) - Credit memo ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `state` (int) - Credit memo state
  - `grand_total` (float) - Grand total
  - `store_id` (int) - Store ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_creditmemo.list",
    [{"order_increment_id": "100000001"}]
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
      "creditmemo_id": 1,
      "order_id": 1,
      "order_increment_id": "100000001",
      "created_at": "2023-01-17 09:45:20",
      "state": 2,
      "grand_total": 150.00,
      "store_id": 1
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `filters_invalid` - Invalid filters provided

### info

Retrieve detailed credit memo information.

**Method Name**: `sales_order_creditmemo.info`

**Parameters**:

- `creditmemoIncrementId` (string, required) - Credit memo increment ID

**Return**:

- (object) - Credit memo information with the following structure:
  - `increment_id` (string) - Credit memo increment ID
  - `creditmemo_id` (int) - Credit memo ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `state` (int) - Credit memo state
  - `grand_total` (float) - Grand total
  - `subtotal` (float) - Subtotal
  - `adjustment_positive` (float) - Positive adjustment
  - `adjustment_negative` (float) - Negative adjustment
  - `shipping_amount` (float) - Shipping amount
  - `tax_amount` (float) - Tax amount
  - `store_id` (int) - Store ID
  - `billing_address` (object) - Billing address information
  - `shipping_address` (object) - Shipping address information
  - `items` (array) - Array of credit memo items
  - `comments` (array) - Array of credit memo comments

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_creditmemo.info",
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
    "creditmemo_id": 1,
    "order_id": 1,
    "order_increment_id": "100000001",
    "created_at": "2023-01-17 09:45:20",
    "state": 2,
    "grand_total": 150.00,
    "subtotal": 140.00,
    "adjustment_positive": 0.00,
    "adjustment_negative": 0.00,
    "shipping_amount": 10.00,
    "tax_amount": 0.00,
    "store_id": 1,
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
    "items": [
      {
        "item_id": 1,
        "parent_id": 1,
        "sku": "product123",
        "name": "Test Product",
        "qty": 2.0000,
        "price": 70.00,
        "tax_amount": 0.00,
        "row_total": 140.00,
        "order_item_id": 1
      }
    ],
    "comments": [
      {
        "comment_id": 1,
        "parent_id": 1,
        "created_at": "2023-01-17 09:45:20",
        "comment": "Credit memo created"
      }
    ]
  },
  "id": 1
}
```

**Possible Errors**:

- `creditmemo_not_exists` - Credit memo does not exist

### create

Create a new credit memo for an order.

**Method Name**: `sales_order_creditmemo.create`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID
- `creditmemoData` (object, optional) - Credit memo data:
  - `items` (array, optional) - Array of items to refund:
    - `order_item_id` (int) - Order item ID
    - `qty` (float) - Quantity to refund
  - `comment` (string, optional) - Credit memo comment
  - `adjustment_positive` (float, optional) - Positive adjustment amount
  - `adjustment_negative` (float, optional) - Negative adjustment amount
  - `shipping_amount` (float, optional) - Shipping amount to refund
  - `refund_to_store_credit` (boolean, optional) - Whether to refund to store credit
- `comment` (string, optional) - Credit memo comment
- `email` (boolean, optional) - Whether to send email notification (default: false)
- `includeComment` (boolean, optional) - Whether to include comment in email (default: false)

**Return**:

- (string) - Credit memo increment ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_creditmemo.create",
    [
      "100000001",
      {
        "items": {
          "1": {"qty": 2}
        },
        "shipping_amount": 10.00
      },
      "Credit memo created",
      true,
      true
    ]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": "100000001",
  "id": 1
}
```

**Possible Errors**:

- `order_not_exists` - Order does not exist
- `order_not_refundable` - Order cannot be refunded
- `data_invalid` - Invalid data provided

### `addComment`

Add a comment to a credit memo.

**Method Name**: `sales_order_creditmemo.addComment`

**Parameters**:

- `creditmemoIncrementId` (string, required) - Credit memo increment ID
- `comment` (string, required) - Comment text
- `email` (boolean, optional) - Whether to send email notification (default: false)
- `includeInEmail` (boolean, optional) - Whether to include comment in email (default: false)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_creditmemo.addComment",
    ["100000001", "Refund processed", true, true]
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

- `creditmemo_not_exists` - Credit memo does not exist

### cancel

Cancel a credit memo.

**Method Name**: `sales_order_creditmemo.cancel`

**Parameters**:

- `creditmemoIncrementId` (string, required) - Credit memo increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_creditmemo.cancel",
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

- `creditmemo_not_exists` - Credit memo does not exist
- `creditmemo_not_cancelable` - Credit memo cannot be canceled