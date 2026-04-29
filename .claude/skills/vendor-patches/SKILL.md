---
name: vendor-patches
description: OpenMage vendor patches — patches.json, patches.lock.json, .vendor-patches/, symplify/vendor-patches workflow (edit vendor file → composer run vendor:patch → commit). Use when patching a composer dependency, working with patches.json, or deciding patch-vs-fork-vs-upstream-PR.
---

# Vendor Patches

Patches against composer dependencies live in `patches.json` (declarative) + `patches.lock.json` (integrity hashes) + `.vendor-patches/*.patch` (the actual diffs). Apply via `cweagans/composer-patches`; (re)generate via `symplify/vendor-patches`.

## Layout

- `patches.json` — author-edited, top-level. Lists patches per package, each as `description => URL`.
- `patches.lock.json` — generated. Carries `_hash` + per-patch `sha256`. **Commit it.**
- `.vendor-patches/<TICKET>.patch` — the unified diffs themselves. Naming: short ticket/issue ID (`OM-2050`, `MAG-1.9.3.7`, `ECG-72`).

## `patches.json` shape

Real snippet from this repo:

```json
{
    "patches": {
        "shardj/zf1-future": {
            "MAG-1.9.3.0": "https://raw.githubusercontent.com/OpenMage/magento-lts/de83e28a673e3c1a249b51433abf6dc93e59063c/.vendor-patches/MAG-1.9.3.0.patch",
            "OM-918 - Add runtime cache to Zend_Locale_Data": "https://raw.githubusercontent.com/OpenMage/magento-lts/f28ab75b1df7d1497ed775e33b77d7957c9b85c4/.vendor-patches/OM-918.patch",
            "OM-2050 - Prevent checking known date codes": "https://raw.githubusercontent.com/OpenMage/magento-lts/de83e28a673e3c1a249b51433abf6dc93e59063c/.vendor-patches/OM-2050.patch"
        },
        "magento-ecg/coding-standard": {
            "ECG-72 - Fix LoopSniff": "https://raw.githubusercontent.com/OpenMage/magento-lts/de83e28a673e3c1a249b51433abf6dc93e59063c/.vendor-patches/ECG-72.patch"
        },
        "react/promise": {
            "PR-264 - PHP 8.5 syntax": "https://github.com/reactphp/promise/commit/501b4aa15121cd015d9f6f548cbda4c27bc16ced.patch"
        }
    }
}
```

Notes:
- Key = composer package name. Inner key = human description (becomes patch label). Value = absolute URL.
- URLs are pinned to a specific commit SHA on `OpenMage/magento-lts` so other consumers can fetch the exact bytes. Don't use `main`/branch names.
- An external URL (e.g. an upstream PR `.patch`) is allowed when the fix is upstreamed but not yet released — see `react/promise` above.

## `patches.lock.json` shape

Generated. Per-patch entries carry `package`, `description`, `url`, `sha256`, `depth`, and `extra.provenance: "patches-file:patches.json"`. Plus a top-level `_hash` over the input. **Always commit alongside `patches.json`** so installs verify integrity.

## Workflow — adding/updating a patch

1. **Install the dep first** (`composer install`) so `vendor/<pkg>/...` is populated and any existing patches are applied.
2. **Edit the vendor file in place** under `vendor/<package>/<path>`. Make the smallest possible change.
3. **Run the generator:**
   ```bash
   composer run vendor:patch
   ```
   Wraps `vendor/bin/vendor-patches generate --patches-file=patches.json --patches-folder=.vendor-patches`. It diffs each modified vendor file against the original and writes a `.patch` under `.vendor-patches/`.
4. **For a new patch:** `vendor-patches` will need an entry in `patches.json`. Add a row under the package with `"<TICKET> - <short desc>": "<raw URL>"`. Use a stable filename (`<TICKET>.patch`) matching what the generator wrote.
5. **Pin the URL.** Once committed and pushed, update the URL in `patches.json` to a `raw.githubusercontent.com/OpenMage/magento-lts/<commit-sha>/.vendor-patches/<file>.patch` URL pointing at the commit that introduced the patch (look at sibling entries for the format). Re-run `composer run vendor:patch` to refresh `patches.lock.json`.
6. **Verify it re-applies cleanly.** Remove `vendor/<pkg>` (or run `composer install --no-cache`) and confirm `cweagans/composer-patches` applies the patch on a fresh install — no fuzz, no hunk failures.
7. **Commit** all three artifacts together: `patches.json`, `patches.lock.json`, `.vendor-patches/<TICKET>.patch`.

## Workflow — removing a patch

1. Delete the entry from `patches.json`.
2. Delete the `.patch` file under `.vendor-patches/`.
3. Run `composer run vendor:patch` so `patches.lock.json` regenerates without the entry (`_hash` will change).
4. Reinstall the affected package (`composer install` or `rm -rf vendor/<pkg> && composer install`) to confirm the unpatched code works.
5. Commit `patches.json`, `patches.lock.json`, and the patch-file deletion in one commit.

## Pinned-version skips

`symplify/vendor-patches` is constrained `^12.0, !=12.0.5, !=12.0.6` in `composer.json` — those releases are known-broken for this repo's workflow. Don't relax that constraint without verifying `composer run vendor:patch` produces byte-identical output to the existing patches.

The plugins enabled in `composer.json` `config.allow-plugins` include `cweagans/composer-patches: true` — patches won't apply on install if that's removed.

## Patch vs fork vs upstream PR

**Patch (this workflow)** when:
- Small, mechanical fix (a few lines, one file).
- Upstream is slow or unmaintained but the project is otherwise fine to depend on.
- The fix is trivially auditable in a unified diff.

**File an upstream PR first** when:
- Security fix — get a CVE/advisory issued; don't sit on a private patch.
- Behavior change of broad scope (touches multiple files / public API).
- Long-lived patch (you'd carry it across multiple upstream releases). A merged PR is cheaper than rebasing the patch every release.
- The dep is actively maintained and responsive (e.g. `react/promise` — note the example uses an upstream commit `.patch` URL directly).

**Fork the package** when:
- The patch is large, ongoing, and divergent enough that re-applying becomes a maintenance burden.
- Upstream is dead and won't merge anything.
- You need to publish a different release cadence than upstream.

## Common pitfalls

- **Editing under `vendor/` after `composer install` runs again wipes your changes.** Always run `composer run vendor:patch` *before* the next `composer install`, or your edits are gone.
- **Patches must be reproducible.** If `vendor:patch` produces a diff with line-ending or whitespace noise, fix the source file (don't commit a noisy patch). Verify by running `vendor:patch` twice and confirming the second run is a no-op.
- **The URL in `patches.json` must be reachable from CI / fresh installs.** A raw GitHub URL pointing at an unmerged branch will break installs once the branch is force-pushed or deleted — pin to a commit SHA on `main`.
- **`patches.lock.json` mismatch** = `cweagans/composer-patches` will refuse to apply. Re-run `composer run vendor:patch` to regenerate after any edit to `patches.json` or any `.vendor-patches/*.patch` file.
- **Don't hand-edit `patches.lock.json`.** It's generated; the `_hash` and per-patch `sha256` will go stale.
- **Don't hand-edit `.vendor-patches/*.patch`.** Edit the vendor source and regenerate.

## Quick reference

| File | Owner | Commit? |
|---|---|---|
| `patches.json` | hand-edited | yes |
| `patches.lock.json` | generated by `vendor:patch` | yes |
| `.vendor-patches/*.patch` | generated by `vendor:patch` | yes |
| `vendor/**` | `composer install` | no (gitignored) |

Generator command: `composer run vendor:patch`
Applier (runs on `composer install`): `cweagans/composer-patches` plugin.
