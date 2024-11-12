# HTTP methods

Accessing API is performed via HTTP. When you enter a URL into a web browser address bar, the browser performs an HTTP GET request to the URL. This usually returns a web page in the form of an HTTP response that the browser displays. But the GET method is one of several HTTP request methods. Magento REST API uses the four main HTTP methods: GET, POST, PUT, and DELETE. The most widespread methods are GET and POST. The other methods are less known but they became widely known due to the popularity of REST web services. An important concept of the REST architecture is that different HTTP request methods perform different actions when applied to the same URL.

For example:

```
GET https://om.ddev.site/rest/customers/123
```

will retrieve information about the specified customer;

```
DELETE https://om.ddev.site/rest/customers/123
```

will delete the specified customer.

## GET

**Retrieving Resources with the HTTP GET Method**

The HTTP GET method is defined in section 9.3 of the [RFC2616](http://www.ietf.org/rfc/rfc2616.txt) document:

> The GET method means retrieve whatever information (in the form of an entity) is identified by the Request-URI. If the Request-URI refers to a data-producing process, it is the produced data which shall be returned as the entity in the response and not the source text of the process, unless that text happens to be the output of the process.

You can retrieve a representation of a resource by getting its URL.

## POST and PUT

**Creating or Updating Resources with the HTTP POST and PUT Methods**

The POST method is defined in section 9.5 of the [RFC2616](http://www.ietf.org/rfc/rfc2616.txt) document:

> The POST method is used to request that the origin server accept the entity enclosed in the request as a new subordinate of the resource identified by the Request-URI in the Request-Line. POST is designed to allow a uniform method to cover the following functions:
>
> *   Annotation of existing resources;
>
> *   Posting a message to a bulletin board, newsgroup, mailing list, or similar group of articles;
>
> *   Providing a block of data, such as the result of submitting a form, to a data-handling process;
>
> *   Extending a database through an append operation.

The PUT method is defined in section 9.6 of the [RFC2616](http://www.ietf.org/rfc/rfc2616.txt) document:

> The PUT method requests that the enclosed entity be stored under the supplied Request-URI. If the Request-URI refers to an already existing resource, the enclosed entity SHOULD be considered as a modified version of the one residing on the origin server.

Creating or updating a resource involves performing an HTTP POST or HTTP PUT to a resource URL.

## DELETE

**Deleting Resources with the HTTP DELETE Method**

The DELETE method is defined in section 9.7 of the [RFC2616](http://www.ietf.org/rfc/rfc2616.txt) document:

> The DELETE method requests that the origin server delete the resource identified by the Request-URI. This method MAY be overridden by human intervention (or other means) on the origin server.

Deleting a resource is performed by means of making an HTTP DELETE request to the resource URL.