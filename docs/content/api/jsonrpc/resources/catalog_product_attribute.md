# Catalog Product Attribute API

## Introduction

The Catalog Product Attribute API allows you to manage product attributes, attribute sets, attribute options, product types, media, and tier prices in your OpenMage store.

## Catalog Product Attribute

### items

Retrieve attributes from specified attribute set.

**Method Name**: `catalog_product_attribute.items`

**Parameters**:

- `setId` (int, required) - Attribute set ID

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
    "catalog_product_attribute.items",
    4
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
      "attribute_id": 73,
      "code": "name",
      "type": "text",
      "required": true,
      "scope": "store"
    },
    {
      "attribute_id": 74,
      "code": "description",
      "type": "textarea",
      "required": false,
      "scope": "store"
    }
  ],
  "id": 1
}
```

### options

Retrieve attribute options.

**Method Name**: `catalog_product_attribute.options`

**Parameters**:

- `attributeId` (int, required) - Attribute ID
- `store` (string|int, optional) - Store ID or code

**Return**:

- (array) - Array of attribute options with the following structure:
  - `value` (string) - Option value
  - `label` (string) - Option label

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.options",
    [142, "default"]
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
      "value": "",
      "label": ""
    },
    {
      "value": "1",
      "label": "Option 1"
    },
    {
      "value": "2",
      "label": "Option 2"
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `not_exists` - Attribute does not exist

### types

Retrieve list of possible attribute types.

**Method Name**: `catalog_product_attribute.types`

**Parameters**: None

**Return**:

- (array) - Array of attribute types with the following structure:
  - `value` (string) - Type value
  - `label` (string) - Type label

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.types",
    []
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
      "value": "text",
      "label": "Text Field"
    },
    {
      "value": "textarea",
      "label": "Text Area"
    },
    {
      "value": "select",
      "label": "Dropdown"
    },
    {
      "value": "multiselect",
      "label": "Multiple Select"
    },
    {
      "value": "boolean",
      "label": "Yes/No"
    },
    {
      "value": "date",
      "label": "Date"
    },
    {
      "value": "price",
      "label": "Price"
    }
  ],
  "id": 1
}
```

### create

Create new product attribute.

**Method Name**: `catalog_product_attribute.create`

**Parameters**:

- `data` (array, required) - Attribute data:
  - `attribute_code` (string, required) - Attribute code
  - `frontend_input` (string, required) - Frontend input type
  - `scope` (string, required) - Attribute scope (global, website, store)
  - `default_value` (string, optional) - Default value
  - `is_unique` (boolean, optional) - Whether the attribute is unique
  - `is_required` (boolean, optional) - Whether the attribute is required
  - `apply_to` (array, optional) - Product types to apply to
  - `is_configurable` (boolean, optional) - Whether the attribute is configurable
  - `is_searchable` (boolean, optional) - Whether the attribute is searchable
  - `is_visible_in_advanced_search` (boolean, optional) - Whether the attribute is visible in advanced search
  - `is_comparable` (boolean, optional) - Whether the attribute is comparable
  - `is_used_for_promo_rules` (boolean, optional) - Whether the attribute is used for promo rules
  - `is_visible_on_front` (boolean, optional) - Whether the attribute is visible on front
  - `used_in_product_listing` (boolean, optional) - Whether the attribute is used in product listing
  - `frontend_label` (array, required) - Array of frontend labels with the following structure:
    - `store_id` (int) - Store ID
    - `label` (string) - Label
  - `additional_fields` (array, optional) - Additional fields based on frontend input type

**Return**:

- (int) - ID of the created attribute

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.create",
    {
      "attribute_code": "custom_attribute",
      "frontend_input": "select",
      "scope": "global",
      "is_required": false,
      "frontend_label": [
        {
          "store_id": 0,
          "label": "Custom Attribute"
        }
      ],
      "is_searchable": true,
      "is_visible_in_advanced_search": true,
      "is_comparable": true,
      "is_visible_on_front": true
    }
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 145,
  "id": 1
}
```

**Possible Errors**:

- `invalid_parameters` - Invalid parameters provided
- `invalid_code` - Invalid attribute code
- `invalid_frontend_input` - Invalid frontend input type
- `unable_to_save` - Unable to save attribute

### update

Update product attribute.

**Method Name**: `catalog_product_attribute.update`

**Parameters**:

- `attribute` (int|string, required) - Attribute ID or code
- `data` (array, required) - Attribute data to update (same structure as in create method)

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.update",
    [
      "custom_attribute",
      {
        "frontend_label": [
          {
            "store_id": 0,
            "label": "Updated Custom Attribute"
          }
        ],
        "is_searchable": false
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

- `can_not_edit` - Attribute cannot be edited
- `unable_to_save` - Unable to save attribute
### remove

Remove attribute.

**Method Name**: `catalog_product_attribute.remove`

**Parameters**:

- `attribute` (int|string, required) - Attribute ID or code

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.remove",
    "custom_attribute"
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

- `can_not_delete` - Attribute cannot be deleted

### info

Get full information about attribute with list of options.

**Method Name**: `catalog_product_attribute.info`

**Parameters**:

- `attribute` (int|string, required) - Attribute ID or code

**Return**:

- (array) - Attribute data with the following structure:
  - `attribute_id` (int) - Attribute ID
  - `attribute_code` (string) - Attribute code
  - `frontend_input` (string) - Frontend input type
  - `default_value` (string) - Default value
  - `is_unique` (boolean) - Whether the attribute is unique
  - `is_required` (boolean) - Whether the attribute is required
  - `apply_to` (array) - Product types to apply to
  - `is_configurable` (boolean) - Whether the attribute is configurable
  - `is_searchable` (boolean) - Whether the attribute is searchable
  - `is_visible_in_advanced_search` (boolean) - Whether the attribute is visible in advanced search
  - `is_comparable` (boolean) - Whether the attribute is comparable
  - `is_used_for_promo_rules` (boolean) - Whether the attribute is used for promo rules
  - `is_visible_on_front` (boolean) - Whether the attribute is visible on front
  - `used_in_product_listing` (boolean) - Whether the attribute is used in product listing
  - `frontend_label` (array) - Array of frontend labels
  - `scope` (string) - Attribute scope (global, website, store)
  - `additional_fields` (array) - Additional fields based on frontend input type
  - `options` (array) - Array of attribute options

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.info",
    "color"
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "attribute_id": 143,
    "attribute_code": "color",
    "frontend_input": "select",
    "default_value": "",
    "is_unique": false,
    "is_required": false,
    "apply_to": [],
    "is_configurable": true,
    "is_searchable": true,
    "is_visible_in_advanced_search": true,
    "is_comparable": true,
    "is_used_for_promo_rules": false,
    "is_visible_on_front": true,
    "used_in_product_listing": false,
    "frontend_label": [
      {
        "store_id": 0,
        "label": "Color"
      }
    ],
    "scope": "global",
    "additional_fields": {
      "is_filterable": 1,
      "is_filterable_in_search": 1,
      "position": 0,
      "used_for_sort_by": 0
    },
    "options": [
      {
        "value": "1",
        "label": "Red"
      },
      {
        "value": "2",
        "label": "Blue"
      },
      {
        "value": "3",
        "label": "Green"
      }
    ]
  },
  "id": 1
}
```

### `addOption`

Add option to select or multi-select attribute.

**Method Name**: `catalog_product_attribute.addOption`

**Parameters**:

- `attribute` (int|string, required) - Attribute ID or code
- `data` (array, required) - Option data:
  - `label` (array, required) - Array of option labels with the following structure:
    - `store_id` (int|array) - Store ID or array of store IDs
    - `value` (string) - Option label
  - `order` (int, optional) - Option order
  - `is_default` (boolean, optional) - Whether the option is default

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.addOption",
    [
      "color",
      {
        "label": [
          {
            "store_id": 0,
            "value": "Yellow"
          }
        ],
        "order": 4,
        "is_default": false
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

- `invalid_frontend_input` - Invalid frontend input type
- `unable_to_add_option` - Unable to add option

### `removeOption`

Remove option from select or multi-select attribute.

**Method Name**: `catalog_product_attribute.removeOption`

**Parameters**:

- `attribute` (int|string, required) - Attribute ID or code
- `optionId` (int, required) - Option ID to remove

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute.removeOption",
    ["color", 4]
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

- `invalid_frontend_input` - Invalid frontend input type
- `unable_to_remove_option` - Unable to remove option

## Catalog Product Attribute Set

### items

Retrieve attribute set list.

**Method Name**: `catalog_product_attribute_set.items`

**Parameters**: None

**Return**:

- (array) - Array of attribute sets with the following structure:
  - `set_id` (int) - Attribute set ID
  - `name` (string) - Attribute set name

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.items",
    []
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
      "set_id": 4,
      "name": "Default"
    },
    {
      "set_id": 5,
      "name": "Custom"
    }
  ],
  "id": 1
}
```

### create

Create new attribute set based on another set.

**Method Name**: `catalog_product_attribute_set.create`

**Parameters**:

- `attributeSetName` (string, required) - Attribute set name
- `skeletonSetId` (int, required) - Skeleton attribute set ID

**Return**:

- (int) - ID of the created attribute set

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.create",
    ["New Attribute Set", 4]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 6,
  "id": 1
}
```

**Possible Errors**:

- `invalid_skeleton_set_id` - Invalid skeleton attribute set ID
- `invalid_data` - Invalid data provided
- `create_attribute_set_error` - Error creating attribute set

### remove

Remove attribute set.

**Method Name**: `catalog_product_attribute_set.remove`

**Parameters**:

- `attributeSetId` (int, required) - Attribute set ID
- `forceProductsRemove` (boolean, optional) - Force removal of products with this attribute set

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.remove",
    [6, false]
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

- `invalid_attribute_set_id` - Invalid attribute set ID
- `attribute_set_has_related_products` - Attribute set has related products
- `remove_attribute_set_error` - Error removing attribute set

### `attributeAdd`

Add attribute to attribute set.

**Method Name**: `catalog_product_attribute_set.attributeAdd`

**Parameters**:

- `attributeId` (int, required) - Attribute ID
- `attributeSetId` (int, required) - Attribute set ID
- `attributeGroupId` (int, optional) - Attribute group ID
- `sortOrder` (int, optional) - Sort order

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.attributeAdd",
    [145, 5, null, 0]
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

- `invalid_attribute_id` - Invalid attribute ID
- `invalid_attribute_set_id` - Invalid attribute set ID
- `invalid_attribute_group_id` - Invalid attribute group ID
- `attribute_is_already_in_set` - Attribute is already in set
- `add_attribute_error` - Error adding attribute

### `attributeRemove`

Remove attribute from attribute set.

**Method Name**: `catalog_product_attribute_set.attributeRemove`

**Parameters**:

- `attributeId` (int, required) - Attribute ID
- `attributeSetId` (int, required) - Attribute set ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.attributeRemove",
    [145, 5]
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

- `invalid_attribute_id` - Invalid attribute ID
- `invalid_attribute_set_id` - Invalid attribute set ID
- `attribute_is_not_in_set` - Attribute is not in set
- `remove_attribute_error` - Error removing attribute

### `groupAdd`

Create group within existing attribute set.

**Method Name**: `catalog_product_attribute_set.groupAdd`

**Parameters**:

- `attributeSetId` (int, required) - Attribute set ID
- `groupName` (string, required) - Group name

**Return**:

- (int) - ID of the created group

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.groupAdd",
    [5, "New Group"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": 15,
  "id": 1
}
```

**Possible Errors**:

- `group_already_exists` - Group already exists
- `group_add_error` - Error adding group

### `groupRename`

Rename existing group.

**Method Name**: `catalog_product_attribute_set.groupRename`

**Parameters**:

- `groupId` (int, required) - Group ID
- `groupName` (string, required) - New group name

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.groupRename",
    [15, "Renamed Group"]
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

- `invalid_attribute_group_id` - Invalid attribute group ID
- `group_rename_error` - Error renaming group

### `groupRemove`

Remove group from existing attribute set.

**Method Name**: `catalog_product_attribute_set.groupRemove`

**Parameters**:

- `attributeGroupId` (int, required) - Attribute group ID

**Return**:

- (boolean) - True on success

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_set.groupRemove",
    15
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

- `invalid_attribute_group_id` - Invalid attribute group ID
- `group_has_configurable_attributes` - Group has configurable attributes
- `group_has_system_attributes` - Group has system attributes
- `group_remove_error` - Error removing group

## Catalog Product Type

### items

Retrieve product type list.

**Method Name**: `catalog_product_type.items`

**Parameters**: None

**Return**:

- (array) - Array of product types with the following structure:
  - `type` (string) - Product type code
  - `label` (string) - Product type label

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_type.items",
    []
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
      "type": "simple",
      "label": "Simple Product"
    },
    {
      "type": "configurable",
      "label": "Configurable Product"
    },
    {
      "type": "grouped",
      "label": "Grouped Product"
    },
    {
      "type": "virtual",
      "label": "Virtual Product"
    },
    {
      "type": "bundle",
      "label": "Bundle Product"
    },
    {
      "type": "downloadable",
      "label": "Downloadable Product"
    }
  ],
  "id": 1
}
```

## Catalog Product Attribute Media

### items

Retrieve images for product.

**Method Name**: `catalog_product_attribute_media.items`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `store` (string|int, optional) - Store ID or code
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (array) - Array of product images with the following structure:
  - `file` (string) - Image file path
  - `label` (string) - Image label
  - `position` (int) - Image position
  - `exclude` (boolean) - Whether the image is excluded
  - `url` (string) - Image URL
  - `types` (array) - Array of image types (image, small_image, thumbnail)

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_media.items",
    ["product1", "default", "sku"]
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
      "file": "/p/r/product1.jpg",
      "label": "Product 1",
      "position": 1,
      "exclude": false,
      "url": "http://example.com/media/catalog/product/p/r/product1.jpg",
      "types": ["image", "small_image", "thumbnail"]
    },
    {
      "file": "/p/r/product1_2.jpg",
      "label": "Product 1 - 2",
      "position": 2,
      "exclude": false,
      "url": "http://example.com/media/catalog/product/p/r/product1_2.jpg",
      "types": []
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist

### info

Retrieve image data.

**Method Name**: `catalog_product_attribute_media.info`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `file` (string, required) - Image file path
- `store` (string|int, optional) - Store ID or code
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (array) - Image data with the same structure as in items method

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_media.info",
    ["product1", "/p/r/product1.jpg", "default", "sku"]
  ],
  "id": 1
}
```

**Example Response**:
```json
{
  "jsonrpc": "2.0",
  "result": {
    "file": "/p/r/product1.jpg",
    "label": "Product 1",
    "position": 1,
    "exclude": false,
    "url": "http://example.com/media/catalog/product/p/r/product1.jpg",
    "types": ["image", "small_image", "thumbnail"]
  },
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist
- `not_exists` - Image does not exist

### create

Create new image for product and return image filename.

**Method Name**: `catalog_product_attribute_media.create`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `data` (array, required) - Image data:
  - `file` (array, required) - Image file data:
    - `content` (string, required) - Base64-encoded image content
    - `mime` (string, required) - Image MIME type
    - `name` (string, optional) - Image name
  - `label` (string, optional) - Image label
  - `position` (int, optional) - Image position
  - `exclude` (boolean, optional) - Whether the image is excluded
  - `types` (array, optional) - Array of image types (image, small_image, thumbnail)
- `store` (string|int, optional) - Store ID or code
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (string) - Image file path

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_media.create",
    [
      "product1",
      {
        "file": {
          "content": "base64-encoded-image-content",
          "mime": "image/jpeg",
          "name": "product1_3.jpg"
        },
        "label": "Product 1 - 3",
        "position": 3,
        "types": ["image"]
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
  "result": "/p/r/product1_3.jpg",
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist
- `data_invalid` - Invalid data provided
- `not_created` - Image could not be created

### update

Update image data.

**Method Name**: `catalog_product_attribute_media.update`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `file` (string, required) - Image file path
- `data` (array, required) - Image data to update (same structure as in create method)
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
    "catalog_product_attribute_media.update",
    [
      "product1",
      "/p/r/product1_3.jpg",
      {
        "label": "Updated Product 1 - 3",
        "position": 4,
        "types": ["small_image"]
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

- `product_not_exists` - Product does not exist
- `not_exists` - Image does not exist
- `data_invalid` - Invalid data provided
- `not_updated` - Image could not be updated

### remove

Remove image from product.

**Method Name**: `catalog_product_attribute_media.remove`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `file` (string, required) - Image file path
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
    "catalog_product_attribute_media.remove",
    ["product1", "/p/r/product1_3.jpg", "sku"]
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
- `not_exists` - Image does not exist
- `not_removed` - Image could not be removed

### types

Retrieve image types (image, small_image, thumbnail, etc...).

**Method Name**: `catalog_product_attribute_media.types`

**Parameters**:

- `setId` (int, required) - Attribute set ID

**Return**:

- (array) - Array of image types with the following structure:
  - `code` (string) - Image type code
  - `scope` (string) - Image type scope (global, website, store)

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_media.types",
    4
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
      "code": "image",
      "scope": "store"
    },
    {
      "code": "small_image",
      "scope": "store"
    },
    {
      "code": "thumbnail",
      "scope": "store"
    }
  ],
  "id": 1
}
```

## Catalog Product Attribute Tier Price

### info

Retrieve tier prices for a product.

**Method Name**: `catalog_product_attribute_tier_price.info`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `identifierType` (string, optional) - Type of product identifier ('sku' or null for ID)

**Return**:

- (array) - Array of tier prices with the following structure:
  - `customer_group_id` (string) - Customer group ID or 'all' for all groups
  - `website` (string) - Website code or 'all' for all websites
  - `qty` (float) - Quantity
  - `price` (float) - Price

**Example Request**:
```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "catalog_product_attribute_tier_price.info",
    ["product1", "sku"]
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
      "customer_group_id": "all",
      "website": "all",
      "qty": 2,
      "price": 19.99
    },
    {
      "customer_group_id": "1",
      "website": "base",
      "qty": 5,
      "price": 17.99
    }
  ],
  "id": 1
}
```

**Possible Errors**:

- `product_not_exists` - Product does not exist

### update

Update tier prices of product.

**Method Name**: `catalog_product_attribute_tier_price.update`

**Parameters**:

- `productId` (int|string, required) - Product ID or SKU
- `tierPrices` (array, required) - Array of tier prices with the following structure:
  - `customer_group_id` (string, optional) - Customer group ID or 'all' for all groups (default: 'all')
  - `website` (string, optional) - Website code or 'all' for all websites (default: 'all')
  - `qty` (float, required) - Quantity
  - `price` (float, required) - Price
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
    "catalog_product_attribute_tier_price.update",
    [
      "product1",
      [
        {
          "customer_group_id": "all",
          "website": "all",
          "qty": 2,
          "price": 19.99
        },
        {
          "customer_group_id": "1",
          "website": "base",
          "qty": 5,
          "price": 17.99
        }
      ],
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
- `not_updated` - Tier prices could not be updated