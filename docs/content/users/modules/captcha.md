---
title: Captcha
tags:
- Modules
---

---
title: Captcha
tags:
- Modules
---

# Captcha modules

!!! info "Mage_Captcha Module Removed"

    As of OpenMage LTS, the legacy `Mage_Captcha` module has been removed due to security and maintenance concerns. We recommend using one of the modern alternatives listed below for better security and user experience.

    The legacy module code is preserved in the [openmage/module-mage-captcha](https://github.com/openmage/module-mage-captcha) repository for reference purposes.

## Recommended Modern Alternatives

## `fballiano/openmage-cloudflare-turnstile`
Turnstile is a Cloudflare CAPTCHA alternative.

- GitHub [repository](https://github.com/fballiano/openmage-cloudflare-turnstile)

```bash
composer require fballiano/openmage-cloudflare-turnstile
```

## `magento-hackathon/HoneySpam`
Spam protection module for various forms using honey pots.

- GitHub [repository](https://github.com/magento-hackathon/HoneySpam)

```bash
composer require magento-hackathon/honeyspam
```

## `empiricompany/reCaptcha`
Clean integration of Google reCaptcha to OpenMage.

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
