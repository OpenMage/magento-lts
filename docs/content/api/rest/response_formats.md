# Response Formats

If you make a Magento API call, you are guaranteed to receive some kind of a response. If you make a successful call, you will receive an HTTP response with a 200 OK status.

You can view the response data from any Magento API call in one of the following two formats:

*   XML
*   JSON

The format of returned data is defined in the request header. The format you choose depends on what you are familiar with most or tools available to you.

## XML Format

The XML response format is a simple XML block.  
To set the response format to XML, add the Accept request header with the `text/xml` value.

A successful call will return the following response (example of retrieving information about stock items):

```xml
<?xml version="1.0"?>
<magento_api>
    <data_item>
        <item_id>1</item_id>
        <product_id>1</product_id>
        <stock_id>1</stock_id>
        <qty>99.0000</qty>
        <low_stock_date></low_stock_date>
    </data_item>
    <data_item>
        <item_id>2</item_id>
        <product_id>2</product_id>
        <stock_id>1</stock_id>
        <qty>100.0000</qty>
        <low_stock_date></low_stock_date>
    </data_item>
</magento_api>
```

If an error occurs, the call may return the following response:

```xml
<?xml version="1.0"?>
<magento_api>
    <messages>
        <error>
            <data_item>
                <code>404</code>
                <message>Resource not found.</message>
            </data_item>
        </error>
    </messages>
</magento_api>
```

## JSON Format

To set the response format to JSON, add the Accept request header with the `application/json` value.

### Response Structure

The JSON objects represent a direct mapping of the XML block from the XML response format.

A simple XML error

```xml
<messages>
    <error>
        <data_item>
            <code>404</code>
            <message>Resource not found.</message>
        </data_item>
    </error>
</messages>
```

will be transformed to

```json
{"messages":{"error":[{"code":404,"message":"Resource not found."}]}}
```

### JSON Responses

A successful API call to the Stock Items resource will return the following XML response:

```xml
<?xml version="1.0"?>
<magento_api>
    <data_item>
        <item_id>1</item_id>
        <product_id>1</product_id>
        <stock_id>1</stock_id>
        <qty>99.0000</qty>
        <low_stock_date></low_stock_date>
    </data_item>
    <data_item>
        <item_id>2</item_id>
        <product_id>2</product_id>
        <stock_id>1</stock_id>
        <qty>100.0000</qty>
        <low_stock_date></low_stock_date>
    </data_item>
</magento_api>
```

The JSON equivalent will be as follows:

```json
[{"item_id":"1","product_id":"1","stock_id":"1","qty":"99.0000","low_stock_date":null},{"item_id":"2","product_id":"2","stock_id":"1","qty":"100.0000","low_stock_date":null}]
```

The list of HTTP status codes that are returned in the API response is described in the [Common HTTP Status Codes](common_http_status_codes.md "Common HTTP Status Codes") part of the documentation. There, you can find the list of codes themselves together with their description.