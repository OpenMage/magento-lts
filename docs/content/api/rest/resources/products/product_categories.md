# Product Categories

## URI: `/products/productid/categories`

Allows you to retrieve information about assigned categories, assign and remove assigned a category from/to a product.

**URL Structure**: `https://om.ddev.site/api/rest/products/productid/categories<br>`
**Version**: 1

### HTTP Method: GET

**Description**: Allows you to retrieve information about categories assigned to the specified product.

**Authentication**: Admin, Customer<br>
**Default Format**: JSON<br>
**Parameters**: _No Parameters_

!!! Example
    ```
    GET https://om.ddev.site/api/rest/products/8/categories
    ```

#### Response Body

```
{
    category_id: 8
}
```

### HTTP Method: POST

**Description**: Allows you to assign a category to a specified product.

**Authentication**: Admin<br>
**Default Format**: JSON<br>
**Parameters**:

| Name         | Description     | Required | Type | Example Value |
|--------------|-----------------|:---------|:-----|:--------------|
| category_id  | The category ID | required | int  | 2             |


!!! Example
    ```
    POST https://om.ddev.site/api/rest/products/8/categories
    ```

#### Request Body

```json
{
    "category_id":"2"
}
```

As a result, the category with ID equal to 2 will be assigned to the specified product.

## URI: `/products/productid/categories/categoryid`

### HTTP Method: DELETE

**Description**: Allows you to remove an assigned category from a specified product.

**Authentication**: Admin<br>
**Default Format**: JSON<br>
**Parameters**: _No Parameters_

!!! Example
    ```
    DELETE https://om.ddev.site/api/rest/products/8/categories/2
    ```

## HTTP Status Codes

| Status Code | Message                                                            | Description                                                                           |
|-------------|--------------------------------------------------------------------|---------------------------------------------------------------------------------------|
| 400         | Product <product ID> is already assigned to category <category ID> | The message is returned when the required category is already assigned to the product |
| 400         | Category not found                                                 | The specified category is not found                                                   |
| 405         | Resource method not implemented yet                                | The specified method is not implemented yet                                           |
