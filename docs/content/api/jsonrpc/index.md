# OpenMage JSON-RPC API Overview

## Introduction

The OpenMage API provides a standardized interface for third-party applications to interact with your OpenMage store. Using the API, developers can create applications that can:

- Manage products, categories, and inventory
- Process orders and shipments
- Handle customer data
- Access sales and catalog information
- And much more

This document focuses on the JSON-RPC implementation of the OpenMage API, which provides a lightweight, language-agnostic way to interact with your OpenMage store programmatically.

## API Architecture Overview

The OpenMage API is built on a modular architecture that supports multiple protocols:

- **JSON-RPC**: A lightweight protocol that uses JSON for data encoding
- **XML-RPC**: Similar to JSON-RPC but uses XML for data encoding
- **SOAP**: A comprehensive web service protocol with formal WSDL definitions
- **REST**: See [OpenMage REST API](../rest/common_http_status_codes.md) for more information

The JSON-RPC adapter is implemented in `Mage_Api_Model_Server_Adapter_Jsonrpc` and uses the `Zend_Json_Server` component to handle requests and responses. The API follows a resource-based architecture where functionality is organized into logical resources (like catalog, customer, sales) with methods that operate on those resources.

### Key Components

1. **API Entry Point**: The `api.php` file serves as the entry point for all API requests, routed to by the web server using the `/api/jsonrpc` URL
2. **Server Adapter**: Handles protocol-specific details (JSON-RPC, XML-RPC, SOAP)
3. **Handler**: Processes API requests and maps them to the appropriate resource models
4. **Resource Models**: Implement the actual business logic for API operations
5. **ACL (Access Control List)**: Controls access to resources based on user permissions

## Authentication

To use the OpenMage API, you must either authenticate with the login method to obtain a session ID and include this session ID with all subsequent API calls, or use the HTTP Basic Authentication method. The HTTP Basic Authentication method allows you to skip the login step and use your API credentials directly in the request which is simpler for many use cases, but does expose your credentials in each request whereas the session ID expires and needs to be refreshed periodically.

### Login Authentication Process

1. **Create an API User**: In the OpenMage admin panel, go to System > Web Services > SOAP/XML-RPC - Users to create a user with appropriate role permissions
2. **Login Request**: Send a login request with your username and API key
3. **Session ID**: Receive a session ID that will be used for all subsequent requests
4. **Session Expiration**: Sessions expire after a period of inactivity (configurable in OpenMage settings)

#### Login Authentication Example

```json
// Login Request
{
  "jsonrpc": "2.0",
  "method": "login",
  "params": ["apiuser", "apikey123"],
  "id": 1
}

// Login Response
{
  "jsonrpc": "2.0",
  "result": "8b98a77a37f50d3d472302981e86aab2",
  "id": 1
}
```

### HTTP Basic Authentication

As an alternative to session-based authentication, OpenMage also supports HTTP Basic Authentication for API requests. This method simplifies the authentication process by eliminating the need for a separate login step.

#### How HTTP Basic Authorization Works

1. **Use API Credentials**: The API username and API key (created in admin panel) are used as the username and password for HTTP Basic Authorization
2. **Skip Login Call**: When using HTTP Basic Authorization, the "login" call is not required
3. **Null Session ID**: The session ID parameter in the "call" method can be set to "null"
4. **Include Authorization Header**: Include the Authorization header with each request

#### HTTP Basic Authorization Example

```php
<?php
// PHP example using HTTP Basic Authorization
$jsonRpcUrl = 'https://your-magento-store.com/api/jsonrpc';
$apiUser = 'apiuser';
$apiKey = 'apikey123';

// Request data with null session ID
$requestData = array(
    'jsonrpc' => '2.0',
    'method' => 'call',
    'params' => [
        null, // Null session ID when using HTTP Basic Authorization
        'catalog_product.info',
        'product_sku_123'
    ],
    'id' => 1
);

$ch = curl_init($jsonRpcUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, "$apiUser:$apiKey"); // HTTP Basic Authorization
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result['result']);
?>
```

## JSON-RPC 2.0 Request/Response Format

The OpenMage API implements the JSON-RPC 2.0 specification, which defines a stateless (aside from the session id), light-weight remote procedure call (RPC) protocol using JSON as the data format.

### Request Format

```json
{
  "jsonrpc": "2.0",
  "method": "call",
  "params": [
    "session_id",
    "resource.method",
    [param1, param2, ...]
  ],
  "id": request_id
}
```

- `jsonrpc`: Must be exactly "2.0"
- `method`: For most API calls, this will be "call"
- `params`: An array containing:
  1. The session ID obtained from login or `null` for HTTP Basic Authorization
  2. The resource and method name in the format "resource.method"
  3. An array of parameters for the method
- `id`: A unique identifier for the request (can be a string or number)

### Response Format

```json
{
  "jsonrpc": "2.0",
  "result": response_data,
  "id": request_id
}
```

- `jsonrpc`: Always "2.0"
- `result`: The data returned by the method call
- `id`: The same ID that was sent in the request

### Error Response Format

```json
{
  "jsonrpc": "2.0",
  "error": {
    "code": error_code,
    "message": "Error message"
  },
  "id": request_id
}
```

## Error Handling

The OpenMage API uses standardized error codes and messages to communicate issues with API requests.

### Common Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 0 | Unknown Error | A general error occurred |
| 1 | Internal Error | An internal server error occurred |
| 2 | Access denied | Authentication failed or insufficient permissions |
| 3 | Invalid API path | The requested resource or method doesn't exist |
| 4 | Resource path is not callable | The requested method cannot be called |
| 5 | Session expired | The session has expired, need to login again |
| 6 | Invalid request parameter | A required parameter is missing or invalid |

### HTTP Status Codes

The API also uses HTTP status codes to indicate the status of requests:

- **200 OK**: Request successful
- **400 Bad Request**: Invalid request parameters
- **401 Unauthorized**: Authentication required
- **403 Forbidden**: Access denied
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server-side error

## Common Usage Patterns

### Basic Workflow

1. **Authentication**: Login to obtain a session ID
2. **API Calls**: Make one or more API calls using the session ID
3. **End Session**: Optionally end the session when finished

### Batch Operations with `multiCall`

For improved performance when making multiple API calls, use the `multiCall` method to batch requests:

```json
{
  "jsonrpc": "2.0",
  "method": "multiCall",
  "params": [
    "session_id",
    [
      ["catalog_product.list", [filter_parameters]],
      ["catalog_product.info", [product_id]]
    ]
  ],
  "id": 1
}
```

### Error Handling Best Practices

1. **Always check for errors** in API responses
2. **Implement retry logic** for transient errors
3. **Handle session expiration** by re-authenticating
4. **Log detailed error information** for troubleshooting

## Code Examples

### Authentication

```php
<?php
// PHP example of authentication
$jsonRpcUrl = 'https://your-magento-store.com/api/jsonrpc';
$userData = array(
    'jsonrpc' => '2.0',
    'method' => 'login',
    'params' => ['apiuser', 'apikey123'],
    'id' => 1
);

$ch = curl_init($jsonRpcUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$sessionId = $result['result'];
echo "Session ID: " . $sessionId;
?>
```

### Retrieving Product Information

```php
<?php
// PHP example of retrieving product information
$sessionId = '8b98a77a37f50d3d472302981e86aab2'; // From login response
$jsonRpcUrl = 'https://your-magento-store.com/api/jsonrpc';

$requestData = array(
    'jsonrpc' => '2.0',
    'method' => 'call',
    'params' => [
        $sessionId,
        'catalog_product.info',
        'product_sku_123'
    ],
    'id' => 2
);

$ch = curl_init($jsonRpcUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result['result']);
?>
```

### Creating a New Product

```php
<?php
// PHP example of creating a new product
$sessionId = '8b98a77a37f50d3d472302981e86aab2'; // From login response
$jsonRpcUrl = 'https://your-magento-store.com/api/jsonrpc';

$productData = array(
    'type_id' => 'simple',
    'attribute_set_id' => 4,
    'sku' => 'new_product_123',
    'name' => 'New Test Product',
    'price' => 99.99,
    'status' => 1,
    'weight' => 1.0,
    'visibility' => 4,
    'description' => 'Product description here',
    'short_description' => 'Short description',
    'stock_data' => array(
        'qty' => 100,
        'is_in_stock' => 1
    )
);

$requestData = array(
    'jsonrpc' => '2.0',
    'method' => 'call',
    'params' => [
        $sessionId,
        'catalog_product.create',
        ['simple', 4, 'new_product_123', $productData]
    ],
    'id' => 3
);

$ch = curl_init($jsonRpcUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
echo "Product ID: " . $result['result'];
?>
```

## Available Resources

The OpenMage API provides access to numerous resources and third-party extensions may also provide additional resources. Please refer to the individual resource documentation pages in the left sidebar for more information on available methods and parameters.

## Best Practices

1. **Use HTTPS**: Always use HTTPS for API calls to ensure secure communication
2. **Implement Rate Limiting**: Avoid overwhelming the server with too many requests
3. **Cache Responses**: Cache responses when appropriate to reduce API calls
4. **Handle Errors Gracefully**: Implement proper error handling in your applications
5. **Use Batch Operations**: Use `multiCall` for better performance when making multiple requests
6. **Validate Input**: Always validate input data before sending it to the API
7. **Monitor API Usage**: Keep track of API usage to identify potential issues
8. **Keep API Keys Secure**: Never expose API keys in client-side code
