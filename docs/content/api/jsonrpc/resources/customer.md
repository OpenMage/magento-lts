# Customer API

## Introduction

The Customer API allows you to manage customers in your OpenMage store. You can retrieve customer information, create new customers, update existing ones, and delete customers.

## Available Methods

### list

Retrieve list of customers with basic info.

**Method Name**: `customer.list`

**Parameters**:

- `filters` (object|array, optional) - Filters to apply to the list:
  - `customer_id` (int|array) - Filter by customer ID(s)
  - `email` (string|array) - Filter by email(s)
  - `firstname` (string|array) - Filter by first name(s)
  - `lastname` (string|array) - Filter by last name(s)
  - `created_at` (string|array) - Filter by creation date
  - `updated_at` (string|array) - Filter by update date
  - `website_id` (int|array) - Filter by website ID(s)
  - `group_id` (int|array) - Filter by customer group ID(s)
  - Other attributes can also be used as filters
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Array of customers with the following structure:
  - `customer_id` (int) - Customer ID
  - `email` (string) - Customer email
  - `firstname` (string) - Customer first name
  - `lastname` (string) - Customer last name
  - `created_at` (string) - Creation date
  - `updated_at` (string) - Update date
  - `website_id` (int) - Website ID
  - `group_id` (int) - Customer group ID

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer.list",
    [{"group_id": 1}]
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
      "customer_id": 1,
      "email": "john.doe@example.com",
      "firstname": "John",
      "lastname": "Doe",
      "created_at": "2023-01-15 14:30:12",
      "updated_at": "2023-01-15 14:30:12",
      "website_id": 1,
      "group_id": 1
    },
    {
      "customer_id": 2,
      "email": "jane.smith@example.com",
      "firstname": "Jane",
      "lastname": "Smith",
      "created_at": "2023-01-16 09:45:23",
      "updated_at": "2023-01-16 09:45:23",
      "website_id": 1,
      "group_id": 1
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `filters_invalid` - Invalid filters provided
- `store_not_exists` - Requested store does not exist

### info

Retrieve detailed customer information.

**Method Name**: `customer.info`

**Parameters**:

- `customerId` (int, required) - Customer ID
- `store` (string|int, optional) - Store ID or code

**Return**:

- (object) - Customer information with the following structure:
  - `customer_id` (int) - Customer ID
  - `email` (string) - Customer email
  - `firstname` (string) - Customer first name
  - `lastname` (string) - Customer last name
  - `middlename` (string) - Customer middle name
  - `prefix` (string) - Name prefix
  - `suffix` (string) - Name suffix
  - `created_at` (string) - Creation date
  - `updated_at` (string) - Update date
  - `website_id` (int) - Website ID
  - `group_id` (int) - Customer group ID
  - `dob` (string) - Date of birth
  - `taxvat` (string) - Tax/VAT number
  - `confirmation` (string) - Confirmation code
  - `created_in` (string) - Store where customer was created
  - `default_billing` (string) - Default billing address ID
  - `default_shipping` (string) - Default shipping address ID
  - `is_active` (int) - Whether customer is active (1 - yes, 0 - no)

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer.info",
    [1, "default"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "customer_id": 1,
    "email": "john.doe@example.com",
    "firstname": "John",
    "lastname": "Doe",
    "middlename": "",
    "prefix": "Mr",
    "suffix": "",
    "created_at": "2023-01-15 14:30:12",
    "updated_at": "2023-01-15 14:30:12",
    "website_id": 1,
    "group_id": 1,
    "dob": "1980-01-01",
    "taxvat": "123456789",
    "confirmation": null,
    "created_in": "Default Store View",
    "default_billing": "1",
    "default_shipping": "1",
    "is_active": 1
  },
  "id": 1
}
```

**Possible Errors**:

- `customer_not_exists` - Customer does not exist
- `store_not_exists` - Requested store does not exist

### create

Create new customer.

**Method Name**: `customer.create`

**Parameters**:

- `customerData` (array, required) - Customer data:
  - `email` (string, required) - Customer email
  - `firstname` (string, required) - Customer first name
  - `lastname` (string, required) - Customer last name
  - `password` (string, required) - Customer password
  - `website_id` (int, required) - Website ID
  - `group_id` (int, optional) - Customer group ID
  - `middlename` (string, optional) - Customer middle name
  - `prefix` (string, optional) - Name prefix
  - `suffix` (string, optional) - Name suffix
  - `dob` (string, optional) - Date of birth (format: `YYYY-MM-DD`)
  - `taxvat` (string, optional) - Tax/VAT number
  - `is_subscribed` (boolean, optional) - Whether customer is subscribed to newsletter
  - `store_id` (int, optional) - Store ID
- `store` (string|int, optional) - Store ID or code

**Return**:

- (int) - ID of the created customer

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer.create",
    [
      {
        "email": "new.customer@example.com",
        "firstname": "New",
        "lastname": "Customer",
        "password": "password123",
        "website_id": 1,
        "group_id": 1,
        "dob": "1985-05-15",
        "taxvat": "987654321",
        "is_subscribed": true
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
  "result": 3,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `customer_data_invalid` - Invalid customer data
- `customer_exists` - Customer with the same email already exists
- `website_not_exists` - Website does not exist
- `group_not_exists` - Customer group does not exist

### update

Update customer data.

**Method Name**: `customer.update`

**Parameters**:

- `customerId` (int, required) - Customer ID
- `customerData` (array, required) - Customer data to update (same structure as in create method, except password is optional)
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
    "customer.update",
    [
      1,
      {
        "firstname": "Updated",
        "lastname": "Name",
        "email": "updated.email@example.com"
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
  "result": true,
  "id": 1
}
```

**Possible Errors**:

- `data_invalid` - Invalid data provided
- `customer_not_exists` - Customer does not exist
- `customer_data_invalid` - Invalid customer data
- `customer_exists` - Another customer with the same email already exists
- `website_not_exists` - Website does not exist
- `group_not_exists` - Customer group does not exist

### delete

Delete customer.

**Method Name**: `customer.delete`

**Parameters**:

- `customerId` (int, required) - Customer ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "customer.delete",
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

- `customer_not_exists` - Customer does not exist
- `not_deleted` - Customer could not be deleted