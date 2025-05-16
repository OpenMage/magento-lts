# Sales Order Shipment API

## Introduction

The Sales Order Shipment API allows you to manage shipments in your OpenMage store. You can retrieve shipment information, create new shipments, add comments, and manage tracking information.

## Available Methods

### list

Retrieve list of shipments with basic info.

**Method Name**: `sales_order_shipment.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `shipment_id` (int|array) - Filter by shipment ID(s)
  - `order_id` (int|array) - Filter by order ID(s)
  - `increment_id` (string|array) - Filter by increment ID(s)
  - `created_at` (string|array) - Filter by creation date
  - `order_increment_id` (string|array) - Filter by order increment ID(s)
  - Other attributes can also be used as filters

**Return**:

- (array) - Array of shipments with the following structure:
  - `increment_id` (string) - Shipment increment ID
  - `shipment_id` (int) - Shipment ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `total_qty` (float) - Total quantity
  - `store_id` (int) - Store ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.list",
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
      "shipment_id": 1,
      "order_id": 1,
      "order_increment_id": "100000001",
      "created_at": "2023-01-16 10:30:45",
      "total_qty": 2.0000,
      "store_id": 1
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `filters_invalid` - Invalid filters provided

### info

Retrieve detailed shipment information.

**Method Name**: `sales_order_shipment.info`

**Parameters**:

- `shipmentIncrementId` (string, required) - Shipment increment ID

**Return**:

- (object) - Shipment information with the following structure:
  - `increment_id` (string) - Shipment increment ID
  - `shipment_id` (int) - Shipment ID
  - `order_id` (int) - Order ID
  - `order_increment_id` (string) - Order increment ID
  - `created_at` (string) - Creation date
  - `total_qty` (float) - Total quantity
  - `store_id` (int) - Store ID
  - `shipping_address` (object) - Shipping address information
  - `billing_address` (object) - Billing address information
  - `items` (array) - Array of shipped items
  - `tracks` (array) - Array of tracking information
  - `comments` (array) - Array of shipment comments

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.info",
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
    "shipment_id": 1,
    "order_id": 1,
    "order_increment_id": "100000001",
    "created_at": "2023-01-16 10:30:45",
    "total_qty": 2.0000,
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
        "parent_id": 1,
        "sku": "product123",
        "name": "Test Product",
        "qty": 2.0000,
        "price": 70.00,
        "weight": 1.00,
        "order_item_id": 1
      }
    ],
    "tracks": [
      {
        "track_id": 1,
        "parent_id": 1,
        "track_number": "1Z12345E0291980793",
        "title": "UPS",
        "carrier_code": "ups",
        "created_at": "2023-01-16 10:35:12"
      }
    ],
    "comments": [
      {
        "comment_id": 1,
        "parent_id": 1,
        "created_at": "2023-01-16 10:30:45",
        "comment": "Shipment created"
      }
    ]
  },
  "id": 1
}
```

**Possible Errors**:

- `shipment_not_exists` - Shipment does not exist

### create

Create a new shipment for an order.

**Method Name**: `sales_order_shipment.create`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID
- `itemsQty` (array, optional) - Array of items to ship with quantities:
  - `order_item_id` (int) - Order item ID
  - `qty` (float) - Quantity to ship
- `comment` (string, optional) - Shipment comment
- `email` (boolean, optional) - Whether to send email notification (default: false)
- `includeComment` (boolean, optional) - Whether to include comment in email (default: false)

**Return**:

- (string) - Shipment increment ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.create",
    [
      "100000001",
      {"1": 2},
      "Shipment created",
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
- `order_not_shippable` - Order cannot be shipped
- `data_invalid` - Invalid data provided

### `addComment`

Add a comment to a shipment.

**Method Name**: `sales_order_shipment.addComment`

**Parameters**:

- `shipmentIncrementId` (string, required) - Shipment increment ID
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
    "sales_order_shipment.addComment",
    ["100000001", "Package has been shipped", true, true]
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

- `shipment_not_exists` - Shipment does not exist

### `addTrack`

Add tracking information to a shipment.

**Method Name**: `sales_order_shipment.addTrack`

**Parameters**:

- `shipmentIncrementId` (string, required) - Shipment increment ID
- `carrier` (string, required) - Carrier code
- `title` (string, required) - Carrier title
- `trackNumber` (string, required) - Tracking number

**Return**:

- (int) - Tracking ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.addTrack",
    ["100000001", "ups", "UPS", "1Z12345E0291980793"]
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

**Possible Errors**:

- `shipment_not_exists` - Shipment does not exist
- `data_invalid` - Invalid data provided

### `removeTrack`

Remove tracking information from a shipment.

**Method Name**: `sales_order_shipment.removeTrack`

**Parameters**:

- `shipmentIncrementId` (string, required) - Shipment increment ID
- `trackId` (int, required) - Tracking ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.removeTrack",
    ["100000001", 1]
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

- `shipment_not_exists` - Shipment does not exist
- `track_not_exists` - Tracking information does not exist

### `sendInfo`

Send shipment information to the customer.

**Method Name**: `sales_order_shipment.sendInfo`

**Parameters**:

- `shipmentIncrementId` (string, required) - Shipment increment ID
- `comment` (string, optional) - Comment to include in the email

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.sendInfo",
    ["100000001", "Your order has been shipped"]
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

- `shipment_not_exists` - Shipment does not exist

### `getCarriers`

Get list of available shipping carriers.

**Method Name**: `sales_order_shipment.getCarriers`

**Parameters**:

- `orderIncrementId` (string, required) - Order increment ID

**Return**:

- (object) - Object with carrier codes as keys and carrier titles as values

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "sales_order_shipment.getCarriers",
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
    "ups": "United Parcel Service",
    "usps": "United States Postal Service",
    "fedex": "Federal Express",
    "dhl": "DHL"
  },
  "id": 1
}
```

**Possible Errors**:

- `order_not_exists` - Order does not exist