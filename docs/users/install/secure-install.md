# Secure installation

Don't use common paths like `admin` for OpenMage Backend URL. Don't use the path in _robots.txt_ and keep it secret. You can change it from Backend (System / Configuration / Admin / Admin Base Url) or by editing _app/etc/local.xml_:

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

Don't use common file names like api.php for OpenMage API URLs to prevent attacks. Don't use the new file name in _robots.txt_ and keep it secret with your partners. After renaming the file you must update the webserver configuration as follows:

## Apache .htaccess
```
RewriteRule ^api/rest api.php?type=rest [QSA,L]
```

## Nginx
```
rewrite ^/api/(\w+).*$ /api.php?type=$1 last;`
```
