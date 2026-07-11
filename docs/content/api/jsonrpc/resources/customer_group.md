# Customer Group API

## Introduction

The Customer Group API allows you to manage customer groups in your OpenMage store. Customer groups are used to categorize customers and apply specific pricing rules, tax classes, and discounts to different customer segments.

## Available Methods

### list

Retrieve list of customer groups.

**Method Name**: `customer_group.list`

**Parameters**:

- None

**Return**:

- (array) - Array of customer groups with the following structure:
  - `customer_group_id` (int) - Customer group ID
  - `customer_group_code` (string) - Customer group name/code
  - `tax_class_id` (int) - Tax class ID assigned to the customer group

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_group.list"
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
      "customer_group_id": 0,
      "customer_group_code": "NOT LOGGED IN",
      "tax_class_id": 3
    },
    {
      "customer_group_id": 1,
      "customer_group_code": "General",
      "tax_class_id": 3
    },
    {
      "customer_group_id": 2,
      "customer_group_code": "Wholesale",
      "tax_class_id": 3
    },
    {
      "customer_group_id": 3,
      "customer_group_code": "Retailer",
      "tax_class_id": 3
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- None specific to this method