---
hide:
  - toc
---

# `php.ini`

```ini
; This file is for CGI/FastCGI installations
; Try copying it to php7.ini or php8 if it doesn't work

; Adjust memory limit

memory_limit = 64M
max_execution_time = 18000

; Disable magic quotes for PHP request vars

magic_quotes_gpc = off

; Disable automatic session start before autoload was initialized

flag session.auto_start = off

; Enable resulting html compression

zlib.output_compression = on

; Disable user agent verification to not break multiple image upload

suhosin.session.cryptua = off

; If this line is missing in local php.ini for some reason PHP ignores this setting in system php.ini and disables mcrypt 

extension=mcrypt.so

; Disable PHP errors, notices and warnings output in production mode to prevent exposing sensitive information

display_errors = Off
```
