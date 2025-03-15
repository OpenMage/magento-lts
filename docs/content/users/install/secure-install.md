---
tags:
- Install
---

# Secure installation

## Back-end URL

Don't use common paths like `admin` for OpenMage back-end URL. Don't use the path in `robots.txt` and keep it secret.

You can change it from back-end (1) or by editing `app/etc/local.xml`:
{ .annotate }

1.  Admin / System / Configuration / Admin / Admin Base URL

```xml
<config>
    <admin>
        <routers>
            <adminhtml>
                <args>
                    <frontName><![CDATA[admin]]></frontName>
                </args>
            </adminhtml>
        </routers>
    </admin>
</config>
```

## URL rewrites

Don't use common file names like `api.php` for OpenMage API URLs to prevent attacks. Don't use the new file name in `robots.txt` and keep it secret with your partners. After renaming the file you must update the web-server configuration as follows:

=== "Apache"

    Apache .htaccess

    ```
    RewriteRule ^api/rest api.php?type=rest [QSA,L]
    ```

=== "Nginx"

    Nginx configuration

    ```
    rewrite ^/api/(\w+).*$ /api.php?type=$1 last;`
    ```
