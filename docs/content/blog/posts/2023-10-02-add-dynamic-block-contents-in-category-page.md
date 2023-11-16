---
draft: false
date: 2023-10-02
authors:
   - kiatng
categories:
   - Categories
   - Templates
comments: true
hide:
   - toc
---

# Add dynamic block contents in category page

   In backend, we can configure a category page and put it on the main menu. (1)
   { .annotate }

   1.  Admin / Catalog / Manage Categories
 
The page contents are rendered in
```
app\design\frontend\base\default\template\catalog\category\view.phtml
```

If we want to render an HTML table in which its data are taken from the database, we would follow these steps:

<!-- more -->

   1. Create a custom block `mymodule/mytable` with template `mymodule/mytable.phtml`. (1)
   { .annotate }
      1.  Admin / Cms / Static Blocks

   2. Whitelist our block for rendering in the frontend (1)
   { .annotate }
      1.  Admin / System / Permissions / Blocks

   3. Create a CMS static block: backend > CMS > Static Blocks and set the _Content_ to render from our block with this directive:
      ```
       {{block type="mymodule/mytable" template="mymodule/mytable.phtml"}}
      ```
   4. Create a subcategory: backend > Catalog > Manage Categories > Add a subcategory and in the _Display Setings_ tab, set the category attribute _Display Mode_ to _Static block only_ and _CMS Block_ pointing to our block.

Voila, the HTML table is rendered under the menu we just created. However, every time the table in the database is updated, and because CMS blocks rendering are taken from the cache, we would need to refresh the cache.

What if the table is constantly being updated, or there is an expiry condition on some data which shouldn't be included? In which case, we would want to render the HTML table dynamically. It's actually quite easy to do:

1. In the subcategory page in backend, set the _Description_ to this:
    ```
    {{block type="mymodule/mytable" template="mymodule/mytable.phtml"}}
    ```
2. Continue on to the _Display Setings_ tab and set the _CMS Block_ to _Please select a static block ..._.
3. In our config file, either in the module `etc/config.xml` or in the `local.xml`, insert the following:

```xml
<config>
    <!-- other config -->
    <global>
    <!-- other config -->
        <!-- copy the following and insert in your etc/config.xml or local.xml -->
        <catalog>
            <content>
                <tempate_filter>cms/template_filter</tempate_filter> <!-- Note the typo on template must remain as "tempate". -->
            </content>
        </catalog>
    </global>
</config>
```

That's it, the table is now rendered dynamically. There 's no need to create the CMS static block.