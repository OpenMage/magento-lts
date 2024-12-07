---
hide:
  - toc
---

# robots.txt

```text
############################################
## For more information about the robots.txt standard visit
## http://www.robotstxt.org/orig.html
##
## For syntax checking visit
## http://tool.motoricerca.info/robots-checker.phtml

############################################
## Crawl the Sitemap. Set the correct URL before uncomment

# Sitemap: https://example.com/sitemap.xml

############################################
## Crawlers Setup

User-agent: *

## How many seconds a crawler should wait before loading and crawling page content
## Set a custom crawl rate if you are experiencing traffic issues with your server
## https://www.contentkingapp.com/academy/robotstxt/faq/crawl-delay-10/

Crawl-delay: 10

############################################
## Allow to crawl paging (paging inside a listing with more params are disallowed below)

Allow: /*?p=

############################################
## Do not crawl non-SEF paths and generated content (if you use a store id in URL you must prefix with * or copy for each store)

Disallow: */index.php/
Disallow: */catalog/product_compare/
Disallow: */catalog/category/view/
Disallow: */catalog/product/view/
Disallow: */catalog/product/gallery/
Disallow: */catalogsearch/
#Allow: */catalogsearch/seo_sitemap
#Allow: */catalogsearch/term/popular
Disallow: */checkout/
Disallow: */control/
Disallow: */contacts/
Disallow: */customer/
Disallow: */customize/
Disallow: */newsletter/
Disallow: */poll/
Disallow: */review/
Disallow: */sales/
Disallow: */sendfriend/
Disallow: */tag/
Disallow: */wishlist/

############################################
## Do not crawl dynamic filters. Uncomment what you need or add custom filters

Disallow: /*?dir*
Disallow: /*?limit*
Disallow: /*?mode*
Disallow: /*?price=*
Disallow: /*?___from_store=*
Disallow: /*?___store=*
Disallow: /*?q=*
# Disallow: /*?cat=*
# Disallow: /*?availability=*
# Disallow: /*?brand=*

############################################
## Do not crawl paths that can be safely ignored by search engines (no clean URLs)

Disallow: /*?p=*&
Disallow: /*.php$
Disallow: /*?SID=

############################################
## Do not allow media indexing for the following bots
## Disallow all or add custom paths. For example */media/ or */skin/

# User-agent: baiduspider-image
# Disallow: /
# Disallow: */media/
# Disallow: */skin/

# User-agent: baiduspider-video
# Disallow: /
# Disallow: */media/
# Disallow: */skin/

# User-agent: msnbot-media
# Disallow: /
# Disallow: */media/
# Disallow: */skin/

# User-agent: Googlebot-Image
# Disallow: /
# Disallow: */media/
# Disallow: */skin/

# User-agent: Googlebot-Video
# Disallow: /
# Disallow: */media/
# Disallow: */skin/
```
