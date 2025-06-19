# Customer Address API

## Introduction

The Customer Address API allows you to manage customer addresses in your OpenMage store. You can retrieve address information, create new addresses, update existing ones, and delete addresses.

## Available Methods

### list

Retrieve list of addresses for a customer.

**Method Name**: `customer_address.list`

**Parameters**:

- `customerId` (int, required) - Customer ID

**Return**:

- (array) - Array of addresses with the following structure:
  - `customer_address_id` (int) - Customer address ID
  - `created_at` (string) - Creation date
  - `updated_at` (string) - Update date
  - `is_default_billing` (boolean) - Whether this is the default billing address
  - `is_default_shipping` (boolean) - Whether this is the default shipping address

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_address.list",
    1
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
      "customer_address_id": 1,
      "created_at": "2023-01-15 14:30:12",
      "updated_at": "2023-01-15 14:30:12",
      "is_default_billing": true,
      "is_default_shipping": true
    },
    {
      "customer_address_id": 2,
      "created_at": "2023-01-16 09:45:23",
      "updated_at": "2023-01-16 09:45:23",
      "is_default_billing": false,
      "is_default_shipping": false
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `customer_not_exists` - Customer does not exist

### info

Retrieve detailed address information.

**Method Name**: `customer_address.info`

**Parameters**:

- `addressId` (int, required) - Customer address ID

**Return**:

- (object) - Address information with the following structure:
  - `customer_address_id` (int) - Customer address ID
  - `customer_id` (int) - Customer ID
  - `firstname` (string) - First name
  - `lastname` (string) - Last name
  - `middlename` (string) - Middle name
  - `prefix` (string) - Name prefix
  - `suffix` (string) - Name suffix
  - `company` (string) - Company
  - `street` (string) - Street address (may contain multiple lines)
  - `city` (string) - City
  - `region` (string) - Region/state name
  - `region_id` (int) - Region/state ID
  - `postcode` (string) - Postal code
  - `country_id` (string) - Country ID (2-letter code)
  - `telephone` (string) - Telephone number
  - `fax` (string) - Fax number
  - `is_default_billing` (boolean) - Whether this is the default billing address
  - `is_default_shipping` (boolean) - Whether this is the default shipping address

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_address.info",
    1
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "customer_address_id": 1,
    "customer_id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "middlename": "",
    "prefix": "Mr",
    "suffix": "",
    "company": "Example Company",
    "street": "123 Main St\nApt 4B",
    "city": "Anytown",
    "region": "California",
    "region_id": 12,
    "postcode": "12345",
    "country_id": "US",
    "telephone": "555-123-4567",
    "fax": "555-123-4568",
    "is_default_billing": true,
    "is_default_shipping": true
  },
  "id": 1
}
```

**Possible Errors**:

- `address_not_exists` - Address does not exist

### create

Create new address for a customer.

**Method Name**: `customer_address.create`

**Parameters**:

- `customerId` (int, required) - Customer ID
- `addressData` (array, required) - Address data:
  - `firstname` (string, required) - First name
  - `lastname` (string, required) - Last name
  - `street` (string|array, required) - Street address (string or array of lines)
  - `city` (string, required) - City
  - `country_id` (string, required) - Country ID (2-letter code)
  - `telephone` (string, required) - Telephone number
  - `postcode` (string, required for most countries) - Postal code
  - `middlename` (string, optional) - Middle name
  - `prefix` (string, optional) - Name prefix
  - `suffix` (string, optional) - Name suffix
  - `company` (string, optional) - Company
  - `region` (string, optional) - Region/state name
  - `region_id` (int, optional) - Region/state ID
  - `fax` (string, optional) - Fax number
  - `is_default_billing` (boolean, optional) - Whether this is the default billing address
  - `is_default_shipping` (boolean, optional) - Whether this is the default shipping address

**Return**:

- (int) - ID of the created address

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_address.create",
    [
      1,
      {
        "firstname": "John",
        "lastname": "Doe",
        "street": ["123 Main St", "Apt 4B"],
        "city": "Anytown",
        "country_id": "US",
        "region_id": 12,
        "postcode": "12345",
        "telephone": "555-123-4567",
        "company": "Example Company",
        "is_default_billing": false,
        "is_default_shipping": false
      }
    ]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 3,
  "id": 1
}
```

**Possible Errors**:

- `customer_not_exists` - Customer does not exist
- `data_invalid` - Invalid data provided

### update

Update customer address data.

**Method Name**: `customer_address.update`

**Parameters**:

- `addressId` (int, required) - Customer address ID
- `addressData` (array, required) - Address data to update (same structure as in create method)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_address.update",
    [
      1,
      {
        "firstname": "Updated",
        "lastname": "Name",
        "telephone": "555-987-6543"
      }
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

- `address_not_exists` - Address does not exist
- `data_invalid` - Invalid data provided

### delete

Delete customer address.

**Method Name**: `customer_address.delete`

**Parameters**:

- `addressId` (int, required) - Customer address ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer_address.delete",
    1
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

- `address_not_exists` - Address does not exist
- `not_deleted` - Address could not be deleted