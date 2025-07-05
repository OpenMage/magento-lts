# Sales Order Invoice API

## Introduction

The Sales Order Invoice API allows you to manage invoices in your OpenMage store. You can retrieve invoice information, create new invoices, add comments, and perform various invoice operations such as capturing payment, voiding, and canceling invoices.

## Available Methods

### list

Retrieve list of invoices with basic info.

**Method Name**: `sales_order_invoice.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `invoice_id` (int|array) - Filter by invoice ID(s)
  - `order_id` (int|array) - Filter by order ID(s)
  - `increment_id` (string|array) - Filter by increment ID(s)
  - `created_at` (string|array) - Filter by creation date
  - `order_increment_id` (string|array) - Filter by order increment ID(s)
  - `state` (int|array) - Filter by state(s)
  - Other attributes can also be used as filters

**Return**:

- (array) - Array of invoices with the following structure:
  - `increment_id` (string) - Invoice increment ID
  - `invoice_id` (int) - Invoice ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `state` (int) - Invoice state
  - `grand_total` (float) - Grand total
  - `store_id` (int) - Store ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.list",
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
      "invoice_id": 1,
      "order_id": 1,
      "order_increment_id": "100000001",
      "created_at": "2023-01-16 11:15:30",
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

Retrieve detailed invoice information.

**Method Name**: `sales_order_invoice.info`

**Parameters**:

- `invoiceIncrementId` (string, required) - Invoice increment ID

**Return**:

- (object) - Invoice information with the following structure:
  - `increment_id` (string) - Invoice increment ID
  - `invoice_id` (int) - Invoice ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `state` (int) - Invoice state
  - `grand_total` (float) - Grand total
  - `subtotal` (float) - Subtotal
  - `tax_amount` (float) - Tax amount
  - `shipping_amount` (float) - Shipping amount
  - `discount_amount` (float) - Discount amount
  - `store_id` (int) - Store ID
  - `billing_address` (object) - Billing address information
  - `shipping_address` (object) - Shipping address information
  - `items` (array) - Array of invoice items
  - `comments` (array) - Array of invoice comments

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.info",
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
    "invoice_id": 1,
    "order_id": 1,
    "order_increment_id": "100000001",
    "created_at": "2023-01-16 11:15:30",
    "state": 2,
    "grand_total": 150.00,
    "subtotal": 140.00,
    "tax_amount": 0.00,
    "shipping_amount": 10.00,
    "discount_amount": 0.00,
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
        "created_at": "2023-01-16 11:15:30",
        "comment": "Invoice created"
      }
    ]
  },
  "id": 1
}
```

**Possible Errors**:

- `invoice_not_exists` - Invoice does not exist

### create

Create a new invoice for an order.

**Method Name**: `sales_order_invoice.create`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID
- `itemsQty` (array, optional) - Array of items to invoice with quantities:
  - `order_item_id` (int) - Order item ID
  - `qty` (float) - Quantity to invoice
- `comment` (string, optional) - Invoice comment
- `email` (boolean, optional) - Whether to send email notification (default: false)
- `includeComment` (boolean, optional) - Whether to include comment in email (default: false)

**Return**:

- (string) - Invoice increment ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.create",
    [
      "100000001",
      {"1": 2},
      "Invoice created",
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
- `order_not_invoiceable` - Order cannot be invoiced
- `data_invalid` - Invalid data provided

### `addComment`

Add a comment to an invoice.

**Method Name**: `sales_order_invoice.addComment`

**Parameters**:

- `invoiceIncrementId` (string, required) - Invoice increment ID
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
    "sales_order_invoice.addComment",
    ["100000001", "Payment received", true, true]
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

- `invoice_not_exists` - Invoice does not exist

### capture

Capture an invoice.

**Method Name**: `sales_order_invoice.capture`

**Parameters**:

- `invoiceIncrementId` (string, required) - Invoice increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.capture",
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

- `invoice_not_exists` - Invoice does not exist
- `invoice_not_capturable` - Invoice cannot be captured

### void

Void an invoice.

**Method Name**: `sales_order_invoice.void`

**Parameters**:

- `invoiceIncrementId` (string, required) - Invoice increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.void",
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

- `invoice_not_exists` - Invoice does not exist
- `invoice_not_voidable` - Invoice cannot be voided

### cancel

Cancel an invoice.

**Method Name**: `sales_order_invoice.cancel`

**Parameters**:

- `invoiceIncrementId` (string, required) - Invoice increment ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_invoice.cancel",
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

- `invoice_not_exists` - Invoice does not exist
- `invoice_not_cancelable` - Invoice cannot be canceled