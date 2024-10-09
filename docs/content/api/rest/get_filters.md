# Use filters

_JSON responses on this page contributed by Tim Reynolds._

Some requests use GET parameters in the URL. These are as follows:

## filter

Specifies the filters for returned data.

## page

Specifies the page number which items will be returned.

!!! example
    ```
    https://om.ddev.site/api/rest/products?page=1
    ```
## order, dir

Specifies the sort order of returned items and the order direction: `asc` - returns items in the ascending order; `dsc` - returns items in the descending order.

!!! example
    ```
    https://om.ddev.site/api/rest/products?order=name&dir=dsc
    https://om.ddev.site/api/rest/products?order=name&dir=asc
    ```
## limit

Limits the number of returned items in the response. Note that by default, 10 items are returned in the response. The maximum number is 100 items.

!!! example
    ```
    https://om.ddev.site/api/rest/products?limit=2
    ```

## neq

"not equal to" - returns items with the specified attribute that is not equal to the defined value.

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][neq]=3
    ```

## in

"equals any of" - returns items that are equal to the item(s) with the specified attribute(s).

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][in]=3
    ```

## nin

"not equals any of" - returns items excluding the item with the specified attribute.

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][nin]=3
    ```

## gt

"greater than" - returns items with the specified attribute that is greater than the defined value.

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][gt]=3
    https://om.ddev.site/api/rest/products?filter[1][attribute]=price&filter[1][gt]=300
    ```

## lt

"less than" - returns items with the specified attribute that is less than the defined value.

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][lt]=4
    ```

## from, to

Specifies the range of attributes according to which items will be returned.

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=entity_id&filter[1][from]=1&filter[1][to]=3
    https://om.ddev.site/api/rest/products?filter[1][attribute]=price&filter[1][from]=150&filter[1][to]=350
    ```

## Whitespaces

If the attribute value consists of several words separated by a whitespace, the '%20' sign is used:

!!! example
    ```
    https://om.ddev.site/api/rest/products?filter[1][attribute]=name&filter[1][in]=BlackBerry%208100%20Pearl
    ```

## Example 1

For example, to filter products with the description equal to simple01:

!!! example
    ```
    https://om.ddev.site/api/rest/products/?order=entity_id&filter[0][attribute]=description&filter[0][in][0]=simple01
    ```

## Example 2

To filter customers by email address:

!!! example
    ```
    https://om.ddev.site/api/rest/customers?filter[1][attribute]=email&filter[1][in][0]=ryan@test.com
    ```
