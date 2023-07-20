## For Multistore setups

If you want to setup multiple stores with a different layout and localized content for each store, this is done by passing the `$_GET['skin']` parameter to the intended processor In the example below, we are using a `503` type error template file, which requires localized content.

The constructor of the `Error_Processor` class accepts a `skin` GET parameter to change layout:

```php
if (isset($_GET['skin'])) {
    $this->_setSkin($_GET['skin']);
}
```

This can also be added a rewrite rule in the `.htaccess` file that will append a `skin` parameter to the URL.

### $_GET['skin'] parameter

To use the `skin` parameter:

1. Check if the `maintenance.flag` exists
1. Note the host address, that refers to the `HTTP_HOST`, or any other variable such as ENV variables
1. Check if the `skin` parameter exists
1. Set the parameter by using the rewrite rules below.

This is what it looks like as a rewrite rule:

```
RewriteCond `%{DOCUMENT_ROOT}/maintenance.flag -f
RewriteCond `%{HTTP_HOST} ^sub.example.com$`
RewriteCond `%{QUERY_STRING} !(^|&)skin=sub(&|$)` [NC]
RewriteRule `^ %{REQUEST_URI}?skin=sub` [L]
```

Copy the following files:

*  `errors/default/503.phtml` to `errors/sub/503.phtml`
*  `errors/default/css/styles.css` to `errors/sub/styles.css`

Edit these files to provide localized content in the `503.phtml` file and custom styling in the `styles.css` file.

Ensure your paths point to your `errors` directory. The directory name must match the URL parameter indicated in the RewriteRule. In the example above, the `sub` directory is used, which is specified as a parameter in the RewriteRule (`skin=sub`)

### ToDo: nginx
The nginx setting should be added for multistore setups.