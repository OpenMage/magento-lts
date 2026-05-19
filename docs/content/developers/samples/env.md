---
hide:
  - toc
---

# `.env`

Load environment variables from a `.env` file in the project root.

### Supported variables:

- `MAGE_IS_DEVELOPER_MODE` (integer: 0 or 1): Enables developer mode if set to 1.
- `OPENMAGE_CONFIG_OVERRIDE_ALLOWED` (integer: 0 or 1): Allows config override if set to 1.
- `OPENMAGE_CONFIG__*` (string): Any variable prefixed with `OPENMAGE_CONFIG__` will be used as a config override.

### Validation:

- `MAGE_IS_DEVELOPER_MODE` and `OPENMAGE_CONFIG_OVERRIDE_ALLOWED` must be integers if present.

### Integration:

- These variables can be set in the `.env` file, or via environment variables (`$_SERVER`/`$_ENV`).
- `.env` values are loaded first, but can be overridden by actual environment variables.

```ini
MAGE_IS_DEVELOPER_MODE=1
OPENMAGE_CONFIG_OVERRIDE_ALLOWED=1
OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME="My OpenMage Store"
```
