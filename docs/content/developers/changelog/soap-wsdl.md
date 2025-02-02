---
tags:
- WSDL
---


# SOAP/WSDL

Since `19.4.17`/`20.0.15` we changed the `targetNamespace` of all the WSDL files (used in the API modules), from `Magento` to `OpenMage`.
If your custom modules extends OpenMage's APIs with a custom WSDL file and there are some hardcoded `targetNamespace="urn:Magento"` strings, your APIs may stop working.

Please replace all occurrences of

```
targetNamespace="urn:Magento"
```
with
```
targetNamespace="urn:OpenMage"
```
or alternatively
```
targetNamespace="urn:{{var wsdl.name}}"
```
to avoid any problem.

To find which files need the modification you can run this command from the root directory of your project.
```bash
grep -rn 'urn:Magento' --include \*.xml
```
