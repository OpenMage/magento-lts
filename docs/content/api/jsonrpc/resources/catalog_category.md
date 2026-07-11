# Catalog Category API

## Introduction

The Catalog Category API allows you to manage product categories in your OpenMage store. You can retrieve category information, create new categories, update existing ones, move categories within the category tree, and manage product assignments to categories.

## Available Methods

### `currentStore`

Sets the current store for category operations.

**Method Name**: `catalog_category.currentStore`

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
    "catalog_category.currentStore",
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

### tree

Retrieve category tree.

**Method Name**: `catalog_category.tree`

**Parameters**:

- `parentId` (int, optional) - Parent category ID (default: 1 - root category)
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Category tree with the following structure:
  - `category_id` (int) - Category ID
  - `parent_id` (int) - Parent category ID
  - `name` (string) - Category name
  - `is_active` (boolean) - Whether the category is active
  - `position` (int) - Position
  - `level` (int) - Level in the category tree
  - `children` (array) - Array of child categories with the same structure

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.tree",
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
    "category_id": 1,
    "parent_id": 0,
    "name": "Root",
    "is_active": true,
    "position": 0,
    "level": 0,
    "children": [
      {
        "category_id": 2,
        "parent_id": 1,
        "name": "Default Category",
        "is_active": true,
        "position": 1,
        "level": 1,
        "children": []
      }
    ]
  },
  "id": 1
}
```

### level

Retrieve level of categories for category/store view/website.

**Method Name**: `catalog_category.level`

**Parameters**:

- `website` (string|int, optional) - Website ID or code
- `store` (string|int, optional) - Store ID or code
- `categoryId` (int, optional) - Category ID

**Return**:

- (array) - Array of categories with the following structure:
  - `category_id` (int) - Category ID
  - `parent_id` (int) - Parent category ID
  - `name` (string) - Category name
  - `is_active` (boolean) - Whether the category is active
  - `position` (int) - Position
  - `level` (int) - Level in the category tree

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.level",
    [null, "default", 2]
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
      "category_id": 3,
      "parent_id": 2,
      "name": "Furniture",
      "is_active": true,
      "position": 1,
      "level": 2
    },
    {
      "category_id": 4,
      "parent_id": 2,
      "name": "Electronics",
      "is_active": true,
      "position": 2,
      "level": 2
    }
  ],
  "id": 1
}
```

### info

Retrieve category data.

**Method Name**: `catalog_category.info`

**Parameters**:

- `categoryId` (int, required) - Category ID
- `store` (string|int, optional) - Store ID or code
- `attributes` (array, optional) - Array of attributes to return

**Return**:

- (array) - Category data with the following structure:
  - `category_id` (int) - Category ID
  - `is_active` (boolean) - Whether the category is active
  - `position` (int) - Position
  - `level` (int) - Level in the category tree
  - Additional attributes as requested
  - `parent_id` (int) - Parent category ID
  - `children` (string) - Comma-separated list of child category IDs
  - `all_children` (string) - Comma-separated list of all child category IDs

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.info",
    [3, "default", ["name", "description", "url_key"]]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "category_id": 3,
    "is_active": true,
    "position": 1,
    "level": 2,
    "name": "Furniture",
    "description": "Furniture category description",
    "url_key": "furniture",
    "parent_id": 2,
    "children": "5,6,7",
    "all_children": "5,6,7,8,9"
  },
  "id": 1
}
```

### create

Create new category.

**Method Name**: `catalog_category.create`

**Parameters**:

- `parentId` (int, required) - Parent category ID
- `categoryData` (array, required) - Category data:
  - `name` (string, required) - Category name
  - `is_active` (boolean, optional) - Whether the category is active
  - `position` (int, optional) - Position
  - `available_sort_by` (array, optional) - Available sort by options
  - `default_sort_by` (string, optional) - Default sort by option
  - `include_in_menu` (boolean, optional) - Include in navigation menu
  - `url_key` (string, optional) - URL key
  - `description` (string, optional) - Description
  - `meta_title` (string, optional) - Meta title
  - `meta_keywords` (string, optional) - Meta keywords
  - `meta_description` (string, optional) - Meta description
  - `display_mode` (string, optional) - Display mode
  - Other custom attributes
- `store` (string|int, optional) - Store ID or code

**Return**:

- (int) - ID of the created category

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.create",
    [
      2,
      {
        "name": "New Category",
        "is_active": true,
        "position": 3,
        "description": "New category description",
        "url_key": "new-category"
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
- `not_exists` - Parent category does not exist

### update

Update category data.

**Method Name**: `catalog_category.update`

**Parameters**:

- `categoryId` (int, required) - Category ID
- `categoryData` (array, required) - Category data to update (same structure as in create method)
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
    "catalog_category.update",
    [
      3,
      {
        "name": "Updated Category Name",
        "description": "Updated description"
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
- `not_exists` - Category does not exist

### move

Move category in tree.

**Method Name**: `catalog_category.move`

**Parameters**:

- `categoryId` (int, required) - Category ID to move
- `parentId` (int, required) - New parent category ID
- `afterId` (int, optional) - Category ID to place the moved category after (if null, the category will be placed at the end)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.move",
    [3, 4, null]
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

- `not_moved` - Category could not be moved
- `not_exists` - Category does not exist

### delete

Delete category.

**Method Name**: `catalog_category.delete`

**Parameters**:

- `categoryId` (int, required) - Category ID to delete

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.delete",
    3
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

- `not_deleted` - Category could not be deleted
- `not_exists` - Category does not exist

### `assignedProducts`

Retrieve list of assigned products to category.

**Method Name**: `catalog_category.assignedProducts`

**Parameters**:

- `categoryId` (int, required) - Category ID
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Array of products with the following structure:
  - `product_id` (int) - Product ID
  - `type` (string) - Product type
  - `set` (int) - Attribute set ID
  - `sku` (string) - Product SKU
  - `position` (int) - Position in category

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_category.assignedProducts",
    [3, "default"]
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
      "product_id": 14,
      "type": "simple",
      "set": 4,
      "sku": "product1",
      "position": 1
    },
    {
      "product_id": 15,
      "type": "simple",
      "set": 4,
      "sku": "product2",
      "position": 2
    }
  ],
  "id": 1
}
```

### `assignProduct`

Assign product to category.

**Method Name**: `catalog_category.assignProduct`

**Parameters**:

- `categoryId` (int, required) - Category ID
- `productId` (int|string, required) - Product ID or SKU
- `position` (int, optional) - Position in category
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
    "catalog_category.assignProduct",
    [3, "product3", 3, "sku"]
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
- `not_exists` - Category does not exist
- `product_not_exists` - Product does not exist

### `updateProduct`

Update product assignment.

**Method Name**: `catalog_category.updateProduct`

**Parameters**:

- `categoryId` (int, required) - Category ID
- `productId` (int|string, required) - Product ID or SKU
- `position` (int, optional) - New position in category
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
    "catalog_category.updateProduct",
    [3, "product1", 5, "sku"]
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
- `not_exists` - Category does not exist
- `product_not_exists` - Product does not exist
- `product_not_assigned` - Product is not assigned to the category

### `removeProduct`

Remove product assignment from category.

**Method Name**: `catalog_category.removeProduct`

**Parameters**:

- `categoryId` (int, required) - Category ID
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
    "catalog_category.removeProduct",
    [3, "product1", "sku"]
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

- `not_exists` - Category does not exist
- `product_not_exists` - Product does not exist