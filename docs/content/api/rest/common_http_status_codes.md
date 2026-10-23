# Common HTTP status codes

HTTP status codes are an essential part of the REST concept. You can get familiar with all of them on [Wikipedia](http://en.wikipedia.org/wiki/List_of_http_status_codes).

The Magento API attempts to return appropriate HTTP status codes for all requests. Any information is returned in the form of a standard HTTP response with an HTTP status code describing the error and the body message.

## HTTP Status Codes

The following table contains possible common HTTP status codes:

| Status Code            | Message                                                                                                                                                                                                                                                                                                                             |
|------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 200 OK                 | -                                                                                                                                                                                                                                                                                                                                   |
| 201 Created            | Resource was partially created                                                                                                                                                                                                                                                                                                      |
| 207 Multi-Status       | -                                                                                                                                                                                                                                                                                                                                   |
| 400 Bad Request        | Resource data before validation error.<br>Resource data invalid.<br>The request data is invalid.<br>Resource collection paging error.<br>The paging limit exceeds the allowed number.<br>Resource collection ordering error.<br>Resource collection filtering error.<br>Resource collection including additional attributes error. |  
| 403 Forbidden          | Access denied.                                                                                                                                                                                                                                                                                                                      |  
| 404 Not Found          | Resource not found.                                                                                                                                                                                                                                                                                                                 |  
| 405 Method Not Allowed | Resource does not support method.<br>Resource method not implemented yet.                                                                                                                                                                                                                                                           |  
| 500 Internal Error     | Not handled simple errors.<br>Resource internal error.                                                                                                                                                                                                                                                                              |  

## Error Messages

When the Magento API returns an error message, it returns it in your requested format. For example, an error in the XML format might look like the following:

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

An error in the JSON format might look like the following:

```json
{"messages":{"error":[{"code":404,"message":"Resource not found."}]}}
```
