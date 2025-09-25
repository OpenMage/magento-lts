---
title: Captcha
tags:
- Modules
---

# Captcha modules

!!! warning "Built-in Captcha Module Removed"
    
    The built-in `Mage_Captcha` module has been removed from OpenMage LTS. This legacy captcha implementation was outdated and provided limited security benefits compared to modern alternatives.

    **Migration Required**: If your store was using the built-in captcha functionality, you need to:
    1. Remove any custom configurations related to `admin/captcha` and `customer/captcha`
    2. Clean up the `captcha_log` database table if no longer needed
    3. Implement one of the modern alternatives listed below

## Recommended Modern Alternatives

## `fballiano/openmage-cloudflare-turnstile`
Turnstile is a Cloudflare CAPTCHA alternative that provides excellent user experience with strong bot protection.

- GitHub [repository](https://github.com/fballiano/openmage-cloudflare-turnstile)

```bash
composer require fballiano/openmage-cloudflare-turnstile
```

## `magento-hackathon/HoneySpam`
Spam protection module for various forms using honey pots. This provides invisible protection without user interaction.

- GitHub [repository](https://github.com/magento-hackathon/HoneySpam)

```bash
composer require magento-hackathon/honeyspam
```

## `empiricompany/reCaptcha`
Clean integration of Google reCaptcha to OpenMage with modern reCaptcha v2 and v3 support.

__Attention__:
This is a maintained fork compatible with the latest versions of OpenMage LTS.

- GitHub [repository](https://github.com/empiricompany/reCaptcha)

!!! warning ""

    No composer install via packagist.org available.

!!! tip ""

    Add this repository to your composer.json or its forked repository. See https://github.com/ProxiBlue/reCaptcha.

```bash
composer require proxiblue/recaptcha
```
