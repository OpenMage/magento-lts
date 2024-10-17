\--- layout: v1x\_rest title: Customers --- JSON responses on this page contributed by Tim Reynolds

*   [REST API: Customers](#RESTAPI-Resource-Customers-RESTAPI-Customers)
    *   [URI: /customers](#RESTAPI-Resource-Customers-URI--customers)
        *   [HTTP Method: GET /customers](#RESTAPI-Resource-Customers-HTTPMethod-GET-customers)
        *   [HTTP Method: POST /customers](#RESTAPI-Resource-Customers-HTTPMethod-POST-customers)
        *   [HTTP Method: PUT /customers](#RESTAPI-Resource-Customers-HTTPMethod-PUT-customers)
        *   [HTTP Method: DELETE /customers](#RESTAPI-Resource-Customers-HTTPMethod-DELETE-customers)
*   [REST API: Customer](#RESTAPI-Resource-Customers-RESTAPI-Customer)
    *   [URI: /customers/:customerId](#RESTAPI-Resource-Customers-URI--customers--customerId)
        *   [HTTP Method: GET /customers/:customerId](#RESTAPI-Resource-Customers-HTTPMethod-GET-customers--customerId)
        *   [HTTP Method: POST /customers/:customerId](#RESTAPI-Resource-Customers-HTTPMethod-POST-customers--customerId)
        *   [HTTP Method: PUT /customers/:customerId](#RESTAPI-Resource-Customers-HTTPMethod-PUT-customers--customerId)
        *   [HTTP Method: DELETE /customers/:customerId](#RESTAPI-Resource-Customers-HTTPMethod-DELETE-customers--customerId)

## Customers

#### URI: /customers

Allows you to create and retrieve customers.

**URL Structure**: http://om.ddev.site/api/rest/customers  
**Version**: 1

### HTTP Method GET

**Description**: Allows you to retrieve the list of existing customers.  
**Notes:**: Only Admin user can retrieve the list of customers with all their attributes.

**Authentication**: Admin<br>
**Default Format**: XML<br>
**Parameters**: _No Parameters_

!!! Example
    ```
    GET http://om.ddev.site/api/rest/customers
    ```

#### Response body: XML

```xml
<?xml version="1.0"?>
<magento_api>
    <data_item>
        <entity_id>2</entity_id>
        <website_id>1</website_id>
        <email>test@example.com</email>
        <group_id>1</group_id>
        <created_at>2012-03-22 14:15:54</created_at>
        <disable_auto_group_change>1</disable_auto_group_change>
        <firstname>john</firstname>
        <lastname>Doe</lastname>
        <created_in>Default Store View</created_in>
    </data_item>
    <data_item>
        <entity_id>4</entity_id>
        <website_id>1</website_id>
        <email>earl@example.com</email>
        <group_id>1</group_id>
        <created_at>2012-03-28 13:54:04</created_at>
        <disable_auto_group_change>0</disable_auto_group_change>
        <firstname>Earl</firstname>
        <lastname>Hickey</lastname>
        <created_in>Admin</created_in>
    </data_item>
</magento_api>
```

#### Response body: JSON

```json
{
    "2": {
        "entity_id": "2",
        "website_id": "1",
        "email": "test@example.com",
        "group_id": "1",
        "created_at": "2012-03-22 14:15:54",
        "disable_auto_group_change": "1",
        "firstname": "john",
        "lastname": "Doe",
        "created_in": "Admin",
        "prefix": null,
        "suffix": null,
        "taxvat": null,
        "dob": "2001-01-03 00:00:00",
        "reward_update_notification": "1",
        "reward_warning_notification": "1",
        "gender": "1"
    },
    "4": {
        "entity_id": "4",
        "website_id": "1",
        "email": "earl@example.com",
        "group_id": "1",
        "created_at": "2013-03-28 18:59:41",
        "disable_auto_group_change": "0",
        "firstname": "Earl",
        "lastname": "Hickey",
        "created_in": "Admin",
        "prefix": null,
        "suffix": null,
        "taxvat": null,
        "dob": "2012-03-28 13:54:04",
        "reward_update_notification": "1",
        "reward_warning_notification": "1",
        "gender": "1"
    }
}
```

### HTTP Method POST

**Description**: Allows you to create a new customer.

**Authentication**: Admin<br>
**Default Format**: XML<br>
**Parameters**:

| Name                      | Description                                                                  | Required | Type   | Example Value     |
|---------------------------|------------------------------------------------------------------------------|----------|--------|-------------------|
| firstname                 | The customer first name                                                      | required | string | John              |
| lastname                  | The customer last name                                                       | required | string | Doe               |
| email                     | The customer email address                                                   | required | string | johny@example.com |
| password                  | The customer password. The password must contain minimum 7 characters        | required | string | 123123q           |
| website_id                | Website ID                                                                   | required | int    | 1                 |
| group_id                  | Customer group ID                                                            | required | int    | 1                 |
| disable_auto_group_change | Defines whether the automatic group change for the customer will be disabled | optional | int    | 0                 |
| prefix                    | Customer prefix                                                              | optional | string | Mr.               |
| middlename                | Customer middle name or initial                                              | optional | string | R.                |
| suffix                    | Customer suffix                                                              | optional | string | Sr.               |
| taxvat                    | Customer Tax or VAT number                                                   | optional | string | GB999 9999 73     |

**Notes**: The list of parameters may change depending on the attributes settings in **Customers** > **Attributes** > **Manage Customer Attributes** page in Magento Admin Panel. For example, a required status of the **middlename** attribute (Middle Name/Initial) may be changed to 'YES". Please note that managing customer attributes is available only in Magento Enterprise Edition.

!!! Example
    ```
    POST http://om.ddev.site/api/rest/customers
    ```

#### Request body

```xml
<?xml version="1.0"?>
<magento_api>
    <firstname>Earl</firstname>
    <lastname>Hickey</lastname>
    <password>123123q</password>
    <email>earl@example.com</email>
    <website_id>1</website_id>
    <group_id>1</group_id>
</magento_api>
```

#### Response

If the customer was created successfully, we receive **Response HTTP Code** = 200, empty **Response Body** and **Location** header like `/api/rest/customers/555` where `555` - an entity id of the new customer.

### HTTP Method PUT

**Description**: Not allowed

### HTTP Method DELETE

**Description**: Not allowed

## Customer

#### URI: /customers/:customerId

Allows you to manage existing customers.

**URL Structure**: http://om.ddev.site/api/rest/customers/:customerId  
**Version**: 1

### HTTP Method GET

**Description**: Allows you to retrieve information on an existing customer.  
**Notes:**: The list of attributes that will be returned for customers is configured in the Magento Admin Panel. The Customer user type has access only to his/her own information. Also, Admin can add additional non-system customer attributes by selecting **Customers** > **Attributes** > **Manage Customer Attributes**. If these attributes are set as visible on frontend, they will be returned in the response. Also, custom attributes will be returned in the response only after the customer information is updated in the Magento Admin Panel or the specified custom attribute is updated via API (see the PUT method below). Please note that managing customer attributes is available only in Magento Enterprise Edition.

**Authentication**: Admin, Customer<br>
**Default Format**: XML<br>
**Parameters**: _No Parameters_

!!! Example
    ```
    GET http://om.ddev.site/api/rest/customers/2
    ```

**Response Body**:

<?xml version="1.0"?>
<magento\_api>
<entity\_id>2</entity\_id>
<website\_id>1</website\_id>
<email>test@example.com</email>
<group\_id>1</group\_id>
<created\_at>2012-03-22 14:15:54</created\_at>
<disable\_auto\_group\_change>1</disable\_auto\_group\_change>
<created\_in>Default Store View</created\_in>
<firstname>john</firstname>
<lastname>Doe</lastname>
<last\_logged\_in>2012-03-22 14:15:56</last\_logged\_in>
</magento\_api>

**response example: json**

get [http://om.ddev.site/api/rest/customers/141](http://om.ddev.site/api/rest/customers)

**response body**:

{
"entity\_id": "2",
"website\_id": "1",
"email": "test@example.com",
"group\_id": "1",
"created\_at": "2012-03-22 14:15:54",
"disable\_auto\_group\_change": "1",
"created\_in": "English",
"firstname": "john",
"lastname": "Doe"
}


### HTTP Method POST

**Description**: Not allowed.

### HTTP Method PUT

**Description**: Allows you to update an existing customer.<br>
**Notes**: The list of attributes that will be updated for customer is configured in the Magento Admin Panel. The Customer user type has access only to his/her own information.

**Authentication**: Admin, Customer<br>
**Default Format**: XML<br>
**Parameters**: You must specify only those parameters which you want to update. Parameters that are not defined in the request body will preserve the previous values. The `website_id` and `created_in` attributes are not allowed for updating.


!!! Example
    ```
    PUT http://om.ddev.site/api/rest/customers/2
    ```

#### Request Body

```xml
<?xml version="1.0"?>
<magento_api>
    <firstname>Earl</firstname>
    <lastname>Hickey</lastname>
    <email>customerss@example.com</email>
    <group_id>1</group_id>
</magento_api>
```

### HTTP Method DELETE

**Description**: Allows you to delete an existing customer.<br>
**Notes**: Admin only can delete a customer.

**Authentication**: Admin<br>
**Default Format**: XML<br>
**Parameters**: _No Parameters_

!!! Example
    ```
    DELETE https://om.ddev.site/api/rest/customers/2
    ```
