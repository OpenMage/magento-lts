---
title: Do you (really) need more?
draft: false
date: 2023-01-03
comments: true
authors:
  - real34
categories:
  - Community
---

# Do you (really) need more?

The eCommerce ecosystem is continuously moving.
Every year, new software and ways to sell online appear.
In this article by [Front-Commerce](https://www.front-commerce.com/), we’ll try to take a step back and see how the Magento ecosystem and merchants managed to go over these changes in the past 15 years, and what may be needed today.

<!-- more -->

## Selling online is teamwork

Merchants who have been selling online for years have developed their own company culture and organization around their online store.

Over the years, their eCommerce platform configuration and features have been refined to adapt to the way people work in the company.
Depending on its size, one or several people interact with the online store on a regular basis (not just visitors and customers).

These “admin” tasks involve:

- adding new products and ensure they’re easy to find by customers (search, attributes…)
- updating inventory
- communicating new products and offers to customers (in the store, with CMS pages and blocks, or elsewhere online – social media, newsletters, forums, videos…)
- preparing and shipping orders
- handling returns and customer support
- accounting…

With a low volume of orders, one person can handle this with almost any system. However, as the turnover increase, merchants need more people and better tools to handle the volume of their activity.

## 2007: enter Magento

When Magento first launched in 2007, selling online was vastly different than it is today. Smartphones, marketplaces and social media were not yet used as sales channels - **yet merchants still had to do all the same tasks.**

Over the past 15 years, Magento has evolved. It now offers more features and better user interfaces, so merchants can provide a quality service to their customers and better tools and processes to their team.

The modular approach of the software also enabled a vibrant ecosystem to grow. Third-party services provided official modules to integrate their services into the user journey, such as shipping or payment methods, ERPs, CRMs, and marketing automation. Developers published and/or sold modules for recurring features merchants and teams needed, like rules for shipment pricing, data feed for external services, admin logs, performance optimization, developer productivity, and software quality.

The success of the OpenMage LTS fork shows that many merchants selling online were satisfied with their platform. They were not willing to invest in a full replatforming after Magento announced its new major version in 2015, or when Magento1 reached its End Of Life in 2020.

After many years, their online store had become more than a default install of a software - it was like a house fully equipped for the needs of its owners, with a subtle mix of features adapted to their context and habits. Relocating meant reconsidering most of the choices and rebuilding many of the subtle, convenient features.

Developers were also satisfied with Magento. The expertise they had acquired over the years allowed them to develop almost any feature efficiently and quickly. That's why [there is still a community contributing to the OpenMage project](https://github.com/OpenMage/magento-lts/pulse/monthly) with bugfixes and improvements today.

## Selling online is tough

We know that eCommerce is difficult. Merchants sell online in their own way, with various methods for organizing catalogs, setting prices, and offering promotions. Some merchants now sell internationally, having adapted to their customers and ecosystem to grow their business.

Customer habits have also changed: user behavior, smartphone usage, user experience standards, etc. People are more likely to place orders from their phones, and merchants use social media to become visible to their ideal customers and bring more qualified traffic to their storefront.

Today's user experience must meet the standards set by the most popular online services. Design, interactions, and performance expectations from users are completely different than they were 10 years ago. This change also affects SEO; search engines prioritize websites that meet performance and user experience standards, especially on mobile.

**For this reason, I believe merchants who are still selling online today are those who embraced uncertainty and continually challenged themselves.**

## Technology has continued to evolve

On the web, technology moves fast and new improvements or new standards appear continuously.

As an example, Magento store owners have to deal with:

- new PHP versions (PHP is in version 8.2 as I write these lines)
- libraries getting deprecated (did you know that several libraries used by Magento are unmaintained by their initial owners, and are now part of the Magento core?)
- software projects die (do you remember OSCommerce? Also, the “Magento 1” project was declared dead… but open-source enables projects such as OpenMage to write a new story on top of this legacy)

Some of these technology changes are welcome (i.e: new PHP versions bring better performance and code maintainability for PHP projects) but others are seen as a burden (i.e: a JS dependency is so entangled in many different parts of the frontend that replacing it with another is a huge task to undertake).

**This is where I see the benefits of a solution such as OpenMage LTS.**
It is a lighthouse that remains stable (in terms of features) while following the needed technology changes from the underlying ecosystem.
Thanks to the OpenMage community, merchants can maintain their fully tailored online shop up-to-date without having to recreate their custom features on a new foundation.

This is perfect for features that are *just done*!
But for features that must evolve with users standards and usage, I think it often is not satisfying enough.

## The rise of 3rd-party services

External services rise and disappear. Embracing this fact is a key factor allowing merchants to innovate and grow faster than their competitors.

Do you want to grow internationally? Support new payment and shipping methods used by customers from different countries.<br>
Do you want to sell on a marketplace? Export your catalog to the marketplace your targeted customers visit daily.<br>
Do you want to engage your community? Embed widgets from your main social media platforms on your website, and optimize your pages for sharing and reposting.

Plus, there are many more features that may be relevant in a specific context but are impossible to have in a single platform by default:

- Reduce checkout friction with one-click payments.
- Provide personalized product suggestions.
- Unify content and commerce search results.
- Offer top-level customer service with dedicated agents.
- Allow customers to return their products for free without friction.
- Accept crypto-payments.
- And more.

External services come and go, but platforms are extensible. Popular services began to create and maintain integrations with popular platforms. The variety of platforms to support grew so much that all these services now prefer to invest in robust APIs that can be easily consumed by developers, no matter the platform they use.

Merchants can innovate faster by integrating these APIs into their existing workflow. Being one of the first to leverage a new service before it is successful enough to have integrations with the most popular eCommerce platforms should be considered a competitive advantage.

In terms of software, this means integrating these APIs into the technology merchants have spent years customizing to their needs should be easy (and cost-effective).

That's where traditional systems and platforms can reach a limit. Developers may face technical difficulties integrating a new service into the existing monolithic commerce platform.

This is the use-case that a new generation of solutions can solve.

## Headless commerce and composable storefronts

Headless commerce and composable storefronts offer **an alternative way to move forward**. Merchants can begin by relocating parts of their monolith to external services for specific tasks.

For example, merchants can use a headless CMS to manage their store's content and use a composable storefront to build a custom user interface with modern technologies, while keeping the same underlying platform (Magento since 2007 and now OpenMage LTS) for the tasks it already handles.

**This approach allows merchants to keep their existing features while gaining access to features provided by external services.** It also makes it easier and faster for developers to integrate new services.

We have seen in recent years that projects have experienced **a "second life" by using their Magento instance upgraded to OpenMage LTS as a headless commerce backend**. In a few months, they were able to deliver a new frontend based on more maintainable technologies (allowing them to more easily hire web developers).

For some of them, OpenMage's responsibilities were reduced to the essentials: catalog management, user management, and transactional commerce. Search and content management were migrated to other best-of-breed services, and unused features were removed. This resulted in a simpler and more stable codebase!

Later, some projects continued this journey even further: they replatformed their commerce platform to Magento2 (or other solutions).

These examples all shared **one key enabler: the addition of a REST API adapted to the use of the storefront**. It made this transition faster and cheaper and therefore less risky. We believed in this approach very early in the Front-Commerce team, and **our partner [PH2M](https://www.ph2m.com/) managed to make it a reality** by adding a wide range of REST APIs to bring headless feature coverage at a higher level than what even Magento 2 supports as of today!

## Conclusion

Do you need to go through all these additional steps?
It depends on your context, your customers and their usage.

You could keep investing in incremental improvements of your current frontend theme.

But if you aim at differentiating your brand from competitors thanks to innovative commerce practices (adapted to *your* customers and *your* company), it may be worth it!