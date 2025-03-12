---
title: Test Environment for OpenMage in Windows 10 Based on DDEV
draft: false
date: 2024-08-17
authors:
  - addison74
categories:
  - Guides
tags:
  - DDEV
  - Windows 10
---

# Test Environment for OpenMage in Windows 10 Based on DDEV

This guide will be updated frequently.

<!-- more -->

### IMPORTANT
If you run Windows OS in a virtual machine, it is mandatory to activate the virtualization option. For example, in VMware with the virtual machine off, access `Edit virtual machine settings`. In the `Hardware` tab select `Processors` and in the `Virtualization engine` section check the `Virtualize Intel VT-x/EPT or AMD-V/RVI` option.

You need at least 8 GB of memory to run (Docker + PhpStorm) decent. I recommend a machine with 16 GB.

![windows_1909](https://github.com/OpenMage/magento-lts/assets/8360474/33c7605e-b9ff-46a5-b960-0e2aabb4dc77)

### (Windows) Installing the Windows Terminal Application
1. Open the `Microsoft Store` application and search for `Windows Terminal`
2. Choose the first result then press the `Get` button

![Windows terminal](https://github.com/OpenMage/magento-lts/assets/8360474/fbcb31fa-3582-4372-8acd-48f4956d6d30)

If you use PhpStorm, at the bottom there is a tab called `Terminal`. Here you can run commands in the Linux distribution too.

![PhpStorm terminal](https://github.com/OpenMage/magento-lts/assets/8360474/cca5b48d-dd5b-4d80-8664-a4f99ee9f960)

### (Windows) Installing WSL2 (Windows Subsystem for Linux 2)
**The Installation Tutorial**

Read: [How to install WSL on Windows 10 (2024)](https://pureinfotech.com/install-windows-subsystem-linux-2-windows-10/)

**Useful Commands**

See: [Windows Subsystem for Linux Documentation](https://learn.microsoft.com/en-us/windows/wsl/)

```
wsl --install
wsl --version
wsl --update
wsl --list --online
wsl --install -d DISTRO-NAME
wsl --set-version <distro name> 2
```

**Advanced Settings**

See: [Advanced settings configuration in WSL](https://learn.microsoft.com/en-us/windows/wsl/wsl-config#configure-global-options-with-wslconfig)

You can configure limits on the memory, CPU and swap size allocated to WSL 2 in a `.wslconfig` file⁠. For example, create a file named `.wslconfig` in the `C:\Users\<User Name>` directory with the following content

```
[wsl2]
memory=4GB   # Limits VM memory in WSL 2 up to 4 GB
processors=6 
```

### (Windows) Installing a Linux Distribution
**Variant 1**
1.  Open the `Command Prompt` application as Administrator
2. Get the distributions list `wsl --list --online`
3. Install a distribution `wsl --install -d Ubuntu-20.04`
4. Reboot the system

**Variant 2**
1.  Open the `Microsoft Store` application and search for Ubuntu
2. Install an LTS version, for example Ubuntu 20.04.6 LTS
3. Reboot the system

Open the `Terminal` application then choose a new tab with Ubuntu. Follow the steps to complete the installation, setting the `username` and `password` (e.g. `admin` / `veryl0ngpassw0rd`).

![Terminal](https://github.com/OpenMage/magento-lts/assets/8360474/b88fe7ae-4d86-4cba-a239-01c06a685bec)

### (Windows) Installing Docker

See: [Install Docker Desktop on Windows](https://docs.docker.com/desktop/install/windows-install/)

1. Download the installation file `Docker Desktop Installer.exe` and run it as Administrator
2. During installation check the option `Use WSL 2 instead of Hyper-V (recommended)`
3. Reboot the system

### (Windows) Installing `mkcert` for Secured Connections

- GitHub [repository](https://github.com/FiloSottile/mkcert)

1. Download the latest Windows release
2. Open the `Terminal` application as Administrator
3. Go to the directory where you downloaded the executable file named `mkcert-vX.X.X-windows-amd64.exe`
4. Run `mkcert-vX.X.X-windows-amd64.exe --install`
5. In the popup window, where you are asked if you want to install the certificate, press `Yes`

### (Windows) Linux distribution drive mapping

1. Open the `Windows Explorer` application
2. On the left side open the `Linux` path and select `Ubuntu-20.04`
3. Right-click and select `Map network drive...` from the menu. Choose a letter, for example Z:

From now on it appears in the `This PC` section under `Network locations`. If you want to disconnect it, right-click and select `Disconnect` from the menu.

![Windows Explorer](https://github.com/OpenMage/magento-lts/assets/8360474/97dacd28-a316-4312-8f5a-1eb67b796d07)

### (Windows) PhpStorm

Create a new project in PhpStorm that has the location where you cloned the OpenMage repository. If you log in to your GitHub account, you will see in the `Pull Request` tab on the left the open pull-requests from OpenMage. Open one and checkout to start testing. When you are done, at the bottom of the PhpStorm window you will see the `Git` tab. Click on it, then right click on `Local > main` and select `Checkout`. You can update the repositories, locals and remotes, from time to time and more.

![New Project](https://github.com/OpenMage/magento-lts/assets/8360474/b408b23c-0128-4887-abc8-30e7133e4fb3)

### (Linux) Installing DDEV

1. Open the `Terminal` application and choose a new tab with Ubuntu.
2. Bring all the packages up to date Ubuntu `sudo apt update && sudo apt upgrade -y`.
3. Install DDEV according to the [instructions](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/).

### (Linux) Copying mkcert Certificates from Windows to Linux

**Variant 1**
1. Open the `Terminal` application and choose a new tab with Ubuntu
2. Create the path  `mkdir -p /home/<user_name/.local/share/mkcert`
3. Run `cp /mnt/c/Users/<User Name>/AppData/Local/mkcert/* /home/<user_name>/.local//share/mkcert/`

**Variant 2**
1. Run the `Windows Explorer` application as administrator
2. Copy the files `rootCA.pem` and `rootCA-key.pem` from `C:\Users\<User Name>\AppData\Local\mkcert`
3. Create the path in Linux > Ubuntu-20.04 `/home/<user_name/.local/share/mkcert`
4. Paste them in the `mkcert` directory

### (Linux) Installing OpenMage
First make sure that the `Docker Desktop` application is running in Windows.

1. Open the `Terminal` application and choose a new tab with Ubuntu
2. Create the path `mkdir -p /home/<user_name>/openmage`
3. Clone the OpenMage repository `git clone https://github.com/OpenMage/magento-lts.git /home/<user_name>/openmage`
4. Go to the directory `/home/<user_name>/openmage`
5. Run the following DDEV commands. The first command configures the project, the second installs OpenMage dependencies, the third installs Magento Sample Data.

```
ddev config
ddev composer install
ddev openmage-install -s -k
```

6. Edit the `.ddev/config.yaml` file to change the web-server and PHP version as you want. I am using Apache and PHP 8.3

```
php_version: "8.3"
webserver_type: apache-fpm
```

7. Run the following DDEV commands to load the project in the browser window with a secured connection

```
ddev start
ddev launch
```

For more information about using DDEV please visit [help](/developers/tools/ddev) page. It is a fantastic tool!

### CONCLUSION
As you can see, the more complicated part is the initial configuration of WSL, Docker, DDEV. Once done, it doesn't take more than 3-5 minutes to get an instance of OpenMage ready for testing. Forget about XAMPP, WAMP in Windows.

You can use a test environment in Windows without WSL2, but I do not recommend this configuration because DDEV must be installed in Windows and not inside the Linux distribution. It depends on Mutagen and it is very very slow. Testing in a Linux distribution has many advantages and it is close to moving the project into production.