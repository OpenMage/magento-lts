# Releases and Versioning

## Semantic Versioning

This project more strictly adheres to [Semantic Versioning](http://semver.org/) compared to the original Magento version numbering system where the "1"
was essentially a fixed number. See the [Terminology](https://github.com/OpenMage/rfcs/blob/main/accepted/0002-release-schedule.md#terminology)
section of [RFC 0002 - Release Schedule](https://github.com/OpenMage/rfcs/blob/main/accepted/0002-release-schedule.md) for more information on how the terms MAJOR, MINOR and PATCH are defined and applied.

The OpenMage team and community maintains OpenMage LTS versions as follows:

- The latest `MAJOR.MINOR` version always receives `PATCH` updates.
- The latest `MAJOR version` always receives `MINOR` updates.
- The latest `MAJOR.MINOR` branch for each `MAJOR` version receives `PATCH` updates for at least 2 years from the time of inception of the initial `MAJOR` version release.

## In a nutshell

- If you want to stay on the cutting edge with the latest improvements use the latest `MAJOR` version.
- If you want maximum backwards compatibility and minimal upgrade hassle use the next-latest `MAJOR` version so that you can still receive important security/stability/regression fixes.
