#!/bin/bash
# Patch apllying tool template
# v0.1.2
# (c) Copyright 2013. Magento Inc.
#
# DO NOT CHANGE ANY LINE IN THIS FILE.

# 1. Check required system tools
_check_installed_tools() {
    local missed=""

    until [ -z "$1" ]; do
        type -t $1 >/dev/null 2>/dev/null
        if (( $? != 0 )); then
            missed="$missed $1"
        fi
        shift
    done

    echo $missed
}

REQUIRED_UTILS='sed patch'
MISSED_REQUIRED_TOOLS=`_check_installed_tools $REQUIRED_UTILS`
if (( `echo $MISSED_REQUIRED_TOOLS | wc -w` > 0 ));
then
    echo -e "Error! Some required system tools, that are utilized in this sh script, are not installed:\nTool(s) \"$MISSED_REQUIRED_TOOLS\" is(are) missed, please install it(them)."
    exit 1
fi

# 2. Determine bin path for system tools
CAT_BIN=`which cat`
PATCH_BIN=`which patch`
SED_BIN=`which sed`
PWD_BIN=`which pwd`
BASENAME_BIN=`which basename`

BASE_NAME=`$BASENAME_BIN "$0"`

# 3. Help menu
if [ "$1" = "-?" -o "$1" = "-h" -o "$1" = "--help" ]
then
    $CAT_BIN << EOFH
Usage: sh $BASE_NAME [--help] [-R|--revert] [--list]
Apply embedded patch.

-R, --revert    Revert previously applied embedded patch
--list          Show list of applied patches
--help          Show this help message
EOFH
    exit 0
fi

# 4. Get "revert" flag and "list applied patches" flag
REVERT_FLAG=
SHOW_APPLIED_LIST=0
if [ "$1" = "-R" -o "$1" = "--revert" ]
then
    REVERT_FLAG=-R
fi
if [ "$1" = "--list" ]
then
    SHOW_APPLIED_LIST=1
fi

# 5. File pathes
CURRENT_DIR=`$PWD_BIN`/
APP_ETC_DIR=`echo "$CURRENT_DIR""app/etc/"`
APPLIED_PATCHES_LIST_FILE=`echo "$APP_ETC_DIR""applied.patches.list"`

# 6. Show applied patches list if requested
if [ "$SHOW_APPLIED_LIST" -eq 1 ] ; then
    echo -e "Applied/reverted patches list:"
    if [ -e "$APPLIED_PATCHES_LIST_FILE" ]
    then
        if [ ! -r "$APPLIED_PATCHES_LIST_FILE" ]
        then
            echo "ERROR: \"$APPLIED_PATCHES_LIST_FILE\" must be readable so applied patches list can be shown."
            exit 1
        else
            $SED_BIN -n "/SUP-\|SUPEE-/p" $APPLIED_PATCHES_LIST_FILE
        fi
    else
        echo "<empty>"
    fi
    exit 0
fi

# 7. Check applied patches track file and its directory
_check_files() {
    if [ ! -e "$APP_ETC_DIR" ]
    then
        echo "ERROR: \"$APP_ETC_DIR\" must exist for proper tool work."
        exit 1
    fi

    if [ ! -w "$APP_ETC_DIR" ]
    then
        echo "ERROR: \"$APP_ETC_DIR\" must be writeable for proper tool work."
        exit 1
    fi

    if [ -e "$APPLIED_PATCHES_LIST_FILE" ]
    then
        if [ ! -w "$APPLIED_PATCHES_LIST_FILE" ]
        then
            echo "ERROR: \"$APPLIED_PATCHES_LIST_FILE\" must be writeable for proper tool work."
            exit 1
        fi
    fi
}

_check_files

# 8. Apply/revert patch
# Note: there is no need to check files permissions for files to be patched.
# "patch" tool will not modify any file if there is not enough permissions for all files to be modified.
# Get start points for additional information and patch data
SKIP_LINES=$((`$SED_BIN -n "/^__PATCHFILE_FOLLOWS__$/=" "$CURRENT_DIR""$BASE_NAME"` + 1))
ADDITIONAL_INFO_LINE=$(($SKIP_LINES - 3))p

_apply_revert_patch() {
    DRY_RUN_FLAG=
    if [ "$1" = "dry-run" ]
    then
        DRY_RUN_FLAG=" --dry-run"
        echo "Checking if patch can be applied/reverted successfully..."
    fi
    PATCH_APPLY_REVERT_RESULT=`$SED_BIN -e '1,/^__PATCHFILE_FOLLOWS__$/d' "$CURRENT_DIR""$BASE_NAME" | $PATCH_BIN $DRY_RUN_FLAG $REVERT_FLAG -p0`
    PATCH_APPLY_REVERT_STATUS=$?
    if [ $PATCH_APPLY_REVERT_STATUS -eq 1 ] ; then
        echo -e "ERROR: Patch can't be applied/reverted successfully.\n\n$PATCH_APPLY_REVERT_RESULT"
        exit 1
    fi
    if [ $PATCH_APPLY_REVERT_STATUS -eq 2 ] ; then
        echo -e "ERROR: Patch can't be applied/reverted successfully."
        exit 2
    fi
}

REVERTED_PATCH_MARK=
if [ -n "$REVERT_FLAG" ]
then
    REVERTED_PATCH_MARK=" | REVERTED"
fi

_apply_revert_patch dry-run
_apply_revert_patch

# 9. Track patch applying result
echo "Patch was applied/reverted successfully."
ADDITIONAL_INFO=`$SED_BIN -n ""$ADDITIONAL_INFO_LINE"" "$CURRENT_DIR""$BASE_NAME"`
APPLIED_REVERTED_ON_DATE=`date -u +"%F %T UTC"`
APPLIED_REVERTED_PATCH_INFO=`echo -n "$APPLIED_REVERTED_ON_DATE"" | ""$ADDITIONAL_INFO""$REVERTED_PATCH_MARK"`
echo -e "$APPLIED_REVERTED_PATCH_INFO\n$PATCH_APPLY_REVERT_RESULT\n\n" >> "$APPLIED_PATCHES_LIST_FILE"

exit 0


SUPEE-5998 | EE_1.14.1.0 | v1 | 9324d922a64fac99ceb3725062eb498d634401dc | Thu May 14 13:46:45 2015 +0300 | v1.14.1.0..HEAD

__PATCHFILE_FOLLOWS__
diff --git app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php
index 9a1710d..04ba86b 100644
--- app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php
+++ app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php
@@ -68,6 +68,8 @@ class Mage_Authorizenet_Directpost_PaymentController extends Mage_Core_Controlle
     public function responseAction()
     {
         $data = $this->getRequest()->getPost();
+        unset($data['redirect_parent']);
+        unset($data['redirect']);
         /* @var $paymentMethod Mage_Authorizenet_Model_DirectPost */
         $paymentMethod = Mage::getModel('authorizenet/directpost');
 
@@ -113,6 +115,8 @@ class Mage_Authorizenet_Directpost_PaymentController extends Mage_Core_Controlle
     public function redirectAction()
     {
         $redirectParams = $this->getRequest()->getParams();
+        unset($redirectParams['redirect_parent']);
+        unset($redirectParams['redirect']);
         $params = array();
         if (!empty($redirectParams['success'])
             && isset($redirectParams['x_invoice_num'])
diff --git app/code/core/Mage/Core/Controller/Varien/Router/Admin.php app/code/core/Mage/Core/Controller/Varien/Router/Admin.php
index 96f06bb..54e634c 100644
--- app/code/core/Mage/Core/Controller/Varien/Router/Admin.php
+++ app/code/core/Mage/Core/Controller/Varien/Router/Admin.php
@@ -129,4 +129,15 @@ class Mage_Core_Controller_Varien_Router_Admin extends Mage_Core_Controller_Vari
         }
         parent::collectRoutes($configArea, $useRouterName);
     }
+
+    /**
+     * Check if current controller instance is allowed in current router.
+     * 
+     * @param Mage_Core_Controller_Varien_Action $controllerInstance
+     * @return boolean
+     */
+    protected function _validateControllerInstance($controllerInstance)
+    {
+        return true;
+    }
 }
diff --git app/code/core/Mage/Core/Controller/Varien/Router/Standard.php app/code/core/Mage/Core/Controller/Varien/Router/Standard.php
index 548af59..1695b66 100644
--- app/code/core/Mage/Core/Controller/Varien/Router/Standard.php
+++ app/code/core/Mage/Core/Controller/Varien/Router/Standard.php
@@ -201,6 +201,10 @@ class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_V
             // instantiate controller class
             $controllerInstance = Mage::getControllerInstance($controllerClassName, $request, $front->getResponse());
 
+            if (!$this->_validateControllerInstance($controllerInstance)) {
+                continue;
+            }
+
             if (!$controllerInstance->hasAction($action)) {
                 continue;
             }
@@ -272,6 +276,17 @@ class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_V
     }
 
     /**
+     * Check if current controller instance is allowed in current router.
+     * 
+     * @param Mage_Core_Controller_Varien_Action $controllerInstance
+     * @return boolean
+     */
+    protected function _validateControllerInstance($controllerInstance)
+    {
+        return $controllerInstance instanceof Mage_Core_Controller_Front_Action;
+    }
+
+    /**
      * Generating and validating class file name,
      * class and if evrything ok do include if needed and return of class name
      *
@@ -297,7 +312,6 @@ class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_V
         return $controllerClassName;
     }
 
-
     /**
      * @deprecated
      * @see _includeControllerClass()
diff --git app/code/core/Mage/Customer/Model/Customer.php app/code/core/Mage/Customer/Model/Customer.php
index 83e1c28..57a6af1 100644
--- app/code/core/Mage/Customer/Model/Customer.php
+++ app/code/core/Mage/Customer/Model/Customer.php
@@ -273,8 +273,11 @@ class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
      */
     public function getAddressById($addressId)
     {
-        return Mage::getModel('customer/address')
-            ->load($addressId);
+        $address = Mage::getModel('customer/address')->load($addressId);
+        if ($this->getId() == $address->getParentId()) {
+            return $address;
+        }
+        return Mage::getModel('customer/address');
     }
 
     /**
diff --git app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php
index 5e383df..1b26969 100644
--- app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php
+++ app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php
@@ -266,6 +266,10 @@ class Mage_Dataflow_Model_Convert_Parser_Csv extends Mage_Dataflow_Model_Convert
         $str = '';
 
         foreach ($fields as $value) {
+            if (substr($value, 0, 1) === '=') {
+                $value = ' ' . $value;
+            }
+
             if (strpos($value, $delimiter) !== false ||
                 empty($enclosure) ||
                 strpos($value, $enclosure) !== false ||
diff --git app/code/core/Mage/ImportExport/Model/Export/Adapter/Csv.php app/code/core/Mage/ImportExport/Model/Export/Adapter/Csv.php
index f067f6c..0a74118 100644
--- app/code/core/Mage/ImportExport/Model/Export/Adapter/Csv.php
+++ app/code/core/Mage/ImportExport/Model/Export/Adapter/Csv.php
@@ -109,9 +109,21 @@ class Mage_ImportExport_Model_Export_Adapter_Csv extends Mage_ImportExport_Model
         if (null === $this->_headerCols) {
             $this->setHeaderCols(array_keys($rowData));
         }
+
+        /**
+         * Security enchancement for CSV data processing by Excel-like applications.
+         * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
+         */
+        $data = array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols));
+        foreach ($data as $key => $value) {
+            if (substr($value, 0, 1) === '=') {
+                $data[$key] = ' ' . $value;
+            }
+        }
+
         fputcsv(
             $this->_fileHandler,
-            array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols)),
+            $data,
             $this->_delimiter,
             $this->_enclosure
         );
diff --git app/code/core/Mage/Install/Controller/Router/Install.php app/code/core/Mage/Install/Controller/Router/Install.php
new file mode 100644
index 0000000..9bc5cf6
--- /dev/null
+++ app/code/core/Mage/Install/Controller/Router/Install.php
@@ -0,0 +1,39 @@
+<?php
+/**
+ * Magento Enterprise Edition
+ *
+ * NOTICE OF LICENSE
+ *
+ * This source file is subject to the Magento Enterprise Edition End User License Agreement
+ * that is bundled with this package in the file LICENSE_EE.txt.
+ * It is also available through the world-wide-web at this URL:
+ * http://www.magento.com/license/enterprise-edition
+ * If you did not receive a copy of the license and are unable to
+ * obtain it through the world-wide-web, please send an email
+ * to license@magento.com so we can send you a copy immediately.
+ *
+ * DISCLAIMER
+ *
+ * Do not edit or add to this file if you wish to upgrade Magento to newer
+ * versions in the future. If you wish to customize Magento for your
+ * needs please refer to http://www.magento.com for more information.
+ *
+ * @category    Mage
+ * @package     Mage_Install
+ * @copyright Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
+ * @license http://www.magento.com/license/enterprise-edition
+ */
+
+class Mage_Install_Controller_Router_Install extends Mage_Core_Controller_Varien_Router_Standard
+{
+    /**
+     * Check if current controller instance is allowed in current router.
+     * 
+     * @param Mage_Core_Controller_Varien_Action $controllerInstance
+     * @return boolean
+     */
+    protected function _validateControllerInstance($controllerInstance)
+    {
+        return $controllerInstance instanceof Mage_Install_Controller_Action;
+    }
+}
diff --git app/code/core/Mage/Install/etc/config.xml app/code/core/Mage/Install/etc/config.xml
index c79f62c..a3a3b20 100644
--- app/code/core/Mage/Install/etc/config.xml
+++ app/code/core/Mage/Install/etc/config.xml
@@ -48,13 +48,35 @@
             </install>
         </blocks>
     </global>
+    <default>
+        <web>
+            <routers>
+                <install>
+                    <area>frontend</area>
+                    <class>Mage_Install_Controller_Router_Install</class>
+                </install>
+            </routers>
+        </web>
+    </default>
+    <stores>
+        <default>
+            <web>
+                <routers>
+                    <install>
+                        <area>frontend</area>
+                        <class>Mage_Install_Controller_Router_Install</class>
+                    </install>
+                </routers>
+            </web>
+        </default>
+    </stores>
     <frontend>
         <secure_url>
             <install>/install/wizard/checkSecureHost</install>
         </secure_url>
         <routers>
             <install>
-                <use>standard</use>
+                <use>install</use>
                 <args>
                     <module>Mage_Install</module>
                     <frontName>install</frontName>
diff --git app/code/core/Mage/Sales/controllers/Recurring/ProfileController.php app/code/core/Mage/Sales/controllers/Recurring/ProfileController.php
index 2df14f9..c93343d1 100644
--- app/code/core/Mage/Sales/controllers/Recurring/ProfileController.php
+++ app/code/core/Mage/Sales/controllers/Recurring/ProfileController.php
@@ -190,8 +190,9 @@ class Mage_Sales_Recurring_ProfileController extends Mage_Core_Controller_Front_
      */
     protected function _initProfile()
     {
+        /** @var Mage_Sales_Model_Recurring_Profile $profile */
         $profile = Mage::getModel('sales/recurring_profile')->load($this->getRequest()->getParam('profile'));
-        if (!$profile->getId()) {
+        if (!$profile->getId() || $this->_session->getCustomerId() != $profile->getCustomerId()) {
             Mage::throwException($this->__('Specified profile does not exist.'));
         }
         Mage::register('current_recurring_profile', $profile);
diff --git downloader/Maged/Model/Connect.php downloader/Maged/Model/Connect.php
index fdd95c9..80df37f 100644
--- downloader/Maged/Model/Connect.php
+++ downloader/Maged/Model/Connect.php
@@ -100,8 +100,11 @@ class Maged_Model_Connect extends Maged_Model
     {
         $match = array();
         if (!$this->checkExtensionKey($id, $match)) {
-            echo('Invalid package identifier provided: '.$id);
-            exit;
+            $errorMessage[] = sprintf('Invalid package identifier provided: %s', $id);
+            $packages = array(
+                'errors' => array('error'=> $errorMessage)
+            );
+            return $packages;
         }
 
         $channel = $match[1];
diff --git downloader/Maged/View.php downloader/Maged/View.php
index bb289e8..dd38927 100755
--- downloader/Maged/View.php
+++ downloader/Maged/View.php
@@ -162,4 +162,36 @@ class Maged_View
     {
         return $this->controller()->getFormKey();
     }
+
+    /**
+     * Escape html entities
+     *
+     * @param   mixed $data
+     * @param   array $allowedTags
+     * @return  mixed
+     */
+    public function escapeHtml($data, $allowedTags = null)
+    {
+        if (is_array($data)) {
+            $result = array();
+            foreach ($data as $item) {
+                $result[] = $this->escapeHtml($item);
+            }
+        } else {
+            // process single item
+            if (strlen($data)) {
+                if (is_array($allowedTags) and !empty($allowedTags)) {
+                    $allowed = implode('|', $allowedTags);
+                    $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
+                    $result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
+                    $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
+                } else {
+                    $result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
+                }
+            } else {
+                $result = $data;
+            }
+        }
+        return $result;
+    }
 }
diff --git downloader/template/connect/packages_prepare.phtml downloader/template/connect/packages_prepare.phtml
index 1e5a7e0..90562f5 100644
--- downloader/template/connect/packages_prepare.phtml
+++ downloader/template/connect/packages_prepare.phtml
@@ -33,7 +33,7 @@
 Extension dependencies
 <form action="<?php
     echo $this->url('connectInstallPackagePost')?>" method="post" target="connect_iframe" onsubmit="onSubmit(this)">
-    <input type="hidden" name="install_package_id" value="<?php echo $this->get('package_id'); ?>">
+    <input type="hidden" name="install_package_id" value="<?php echo $this->escapeHtml($this->get('package_id')); ?>">
     <table cellspacing="0" cellpadding="0" width="100%">
         <col width="150" />
         <col width="250" />
diff --git downloader/template/messages.phtml downloader/template/messages.phtml
index 211bc22..c8f8d86 100755
--- downloader/template/messages.phtml
+++ downloader/template/messages.phtml
@@ -30,7 +30,7 @@
     <li>
         <ul class="<?php echo $type ?>-msg">
         <?php foreach ($msgs as $msg): ?>
-            <li><?php echo $msg ?></li>
+            <li><?php echo $this->escapeHtml($msg) ?></li>
         <?php endforeach; ?>
         </ul>
     </li>
diff --git get.php get.php
index a7fe802..71ab535 100644
--- get.php
+++ get.php
@@ -37,7 +37,7 @@ $start = microtime(true);
  * Error reporting
  */
 error_reporting(E_ALL | E_STRICT);
-ini_set('display_errors', 1);
+ini_set('display_errors', 0);
 
 $ds = DIRECTORY_SEPARATOR;
 $ps = PATH_SEPARATOR;
diff --git lib/PEAR/PEAR/PEAR.php lib/PEAR/PEAR/PEAR.php
index b4633bf..e6f8edc 100644
--- lib/PEAR/PEAR/PEAR.php
+++ lib/PEAR/PEAR/PEAR.php
@@ -6,21 +6,15 @@
  *
  * PHP versions 4 and 5
  *
- * LICENSE: This source file is subject to version 3.0 of the PHP license
- * that is available through the world-wide-web at the following URI:
- * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
- * the PHP License and are unable to obtain it through the web, please
- * send a note to license@php.net so we can mail you a copy immediately.
- *
  * @category   pear
  * @package    PEAR
  * @author     Sterling Hughes <sterling@php.net>
  * @author     Stig Bakken <ssb@php.net>
  * @author     Tomas V.V.Cox <cox@idecnet.com>
  * @author     Greg Beaver <cellog@php.net>
- * @copyright  1997-2008 The PHP Group
- * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
- * @version    CVS: $Id: PEAR.php,v 1.104 2008/01/03 20:26:34 cellog Exp $
+ * @copyright  1997-2010 The Authors
+ * @license    http://opensource.org/licenses/bsd-license.php New BSD License
+ * @version    CVS: $Id$
  * @link       http://pear.php.net/package/PEAR
  * @since      File available since Release 0.1
  */
@@ -52,15 +46,6 @@ if (substr(PHP_OS, 0, 3) == 'WIN') {
     define('PEAR_OS',    'Unix'); // blatant assumption
 }
 
-// instant backwards compatibility
-if (!defined('PATH_SEPARATOR')) {
-    if (OS_WINDOWS) {
-        define('PATH_SEPARATOR', ';');
-    } else {
-        define('PATH_SEPARATOR', ':');
-    }
-}
-
 $GLOBALS['_PEAR_default_error_mode']     = PEAR_ERROR_RETURN;
 $GLOBALS['_PEAR_default_error_options']  = E_USER_NOTICE;
 $GLOBALS['_PEAR_destructor_object_list'] = array();
@@ -92,8 +77,8 @@ $GLOBALS['_PEAR_error_handler_stack']    = array();
  * @author     Tomas V.V. Cox <cox@idecnet.com>
  * @author     Greg Beaver <cellog@php.net>
  * @copyright  1997-2006 The PHP Group
- * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
- * @version    Release: 1.7.2
+ * @license    http://opensource.org/licenses/bsd-license.php New BSD License
+ * @version    Release: 1.9.5
  * @link       http://pear.php.net/package/PEAR
  * @see        PEAR_Error
  * @since      Class available since PHP 4.0.2
@@ -101,8 +86,6 @@ $GLOBALS['_PEAR_error_handler_stack']    = array();
  */
 class PEAR
 {
-    // {{{ properties
-
     /**
      * Whether to enable internal debug messages.
      *
@@ -153,10 +136,6 @@ class PEAR
      */
     var $_expected_errors = array();
 
-    // }}}
-
-    // {{{ constructor
-
     /**
      * Constructor.  Registers this object in
      * $_PEAR_destructor_object_list for destructor emulation if a
@@ -173,9 +152,11 @@ class PEAR
         if ($this->_debug) {
             print "PEAR constructor called, class=$classname\n";
         }
+
         if ($error_class !== null) {
             $this->_error_class = $error_class;
         }
+
         while ($classname && strcasecmp($classname, "pear")) {
             $destructor = "_$classname";
             if (method_exists($this, $destructor)) {
@@ -192,9 +173,6 @@ class PEAR
         }
     }
 
-    // }}}
-    // {{{ destructor
-
     /**
      * Destructor (the emulated type of...).  Does nothing right now,
      * but is included for forward compatibility, so subclass
@@ -212,9 +190,6 @@ class PEAR
         }
     }
 
-    // }}}
-    // {{{ getStaticProperty()
-
     /**
     * If you have a class that's mostly/entirely static, and you need static
     * properties, you can use this method to simulate them. Eg. in your method(s)
@@ -227,21 +202,20 @@ class PEAR
     * @return mixed   A reference to the variable. If not set it will be
     *                 auto initialised to NULL.
     */
-    public static function &getStaticProperty($class, $var)
+    function &getStaticProperty($class, $var)
     {
         static $properties;
         if (!isset($properties[$class])) {
             $properties[$class] = array();
         }
+
         if (!array_key_exists($var, $properties[$class])) {
             $properties[$class][$var] = null;
         }
+
         return $properties[$class][$var];
     }
 
-    // }}}
-    // {{{ registerShutdownFunc()
-
     /**
     * Use this function to register a shutdown method for static
     * classes.
@@ -262,9 +236,6 @@ class PEAR
         $GLOBALS['_PEAR_shutdown_funcs'][] = array($func, $args);
     }
 
-    // }}}
-    // {{{ isError()
-
     /**
      * Tell whether a value is a PEAR error.
      *
@@ -276,22 +247,20 @@ class PEAR
      * @access  public
      * @return  bool    true if parameter is an error
      */
-    public static function isError($data, $code = null)
+    function isError($data, $code = null)
     {
-        if ($data instanceof PEAR_Error) {
-            if (is_null($code)) {
-                return true;
-            } elseif (is_string($code)) {
-                return $data->getMessage() == $code;
-            } else {
-                return $data->getCode() == $code;
-            }
+        if (!is_a($data, 'PEAR_Error')) {
+            return false;
         }
-        return false;
-    }
 
-    // }}}
-    // {{{ setErrorHandling()
+        if (is_null($code)) {
+            return true;
+        } elseif (is_string($code)) {
+            return $data->getMessage() == $code;
+        }
+
+        return $data->getCode() == $code;
+    }
 
     /**
      * Sets how errors generated by this object should be handled.
@@ -331,7 +300,6 @@ class PEAR
      *
      * @since PHP 4.0.5
      */
-
     function setErrorHandling($mode = null, $options = null)
     {
         if (isset($this) && is_a($this, 'PEAR')) {
@@ -369,9 +337,6 @@ class PEAR
         }
     }
 
-    // }}}
-    // {{{ expectError()
-
     /**
      * This method is used to tell which errors you expect to get.
      * Expected errors are always returned with error mode
@@ -394,12 +359,9 @@ class PEAR
         } else {
             array_push($this->_expected_errors, array($code));
         }
-        return sizeof($this->_expected_errors);
+        return count($this->_expected_errors);
     }
 
-    // }}}
-    // {{{ popExpect()
-
     /**
      * This method pops one element off the expected error codes
      * stack.
@@ -411,9 +373,6 @@ class PEAR
         return array_pop($this->_expected_errors);
     }
 
-    // }}}
-    // {{{ _checkDelExpect()
-
     /**
      * This method checks unsets an error code if available
      *
@@ -425,8 +384,7 @@ class PEAR
     function _checkDelExpect($error_code)
     {
         $deleted = false;
-
-        foreach ($this->_expected_errors AS $key => $error_array) {
+        foreach ($this->_expected_errors as $key => $error_array) {
             if (in_array($error_code, $error_array)) {
                 unset($this->_expected_errors[$key][array_search($error_code, $error_array)]);
                 $deleted = true;
@@ -437,12 +395,10 @@ class PEAR
                 unset($this->_expected_errors[$key]);
             }
         }
+
         return $deleted;
     }
 
-    // }}}
-    // {{{ delExpect()
-
     /**
      * This method deletes all occurences of the specified element from
      * the expected error codes stack.
@@ -455,34 +411,26 @@ class PEAR
     function delExpect($error_code)
     {
         $deleted = false;
-
         if ((is_array($error_code) && (0 != count($error_code)))) {
-            // $error_code is a non-empty array here;
-            // we walk through it trying to unset all
-            // values
-            foreach($error_code as $key => $error) {
-                if ($this->_checkDelExpect($error)) {
-                    $deleted =  true;
-                } else {
-                    $deleted = false;
-                }
+            // $error_code is a non-empty array here; we walk through it trying
+            // to unset all values
+            foreach ($error_code as $key => $error) {
+                $deleted =  $this->_checkDelExpect($error) ? true : false;
             }
+
             return $deleted ? true : PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
         } elseif (!empty($error_code)) {
             // $error_code comes alone, trying to unset it
             if ($this->_checkDelExpect($error_code)) {
                 return true;
-            } else {
-                return PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
             }
-        } else {
-            // $error_code is empty
-            return PEAR::raiseError("The expected error you submitted is empty"); // IMPROVE ME
+
+            return PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
         }
-    }
 
-    // }}}
-    // {{{ raiseError()
+        // $error_code is empty
+        return PEAR::raiseError("The expected error you submitted is empty"); // IMPROVE ME
+    }
 
     /**
      * This method is a wrapper that returns an instance of the
@@ -521,7 +469,7 @@ class PEAR
      * @see PEAR::setErrorHandling
      * @since PHP 4.0.5
      */
-    public static function raiseError($message = null,
+    function &raiseError($message = null,
                          $code = null,
                          $mode = null,
                          $options = null,
@@ -538,13 +486,20 @@ class PEAR
             $message     = $message->getMessage();
         }
 
-        if (isset($this) && isset($this->_expected_errors) && sizeof($this->_expected_errors) > 0 && sizeof($exp = end($this->_expected_errors))) {
+        if (
+            isset($this) &&
+            isset($this->_expected_errors) &&
+            count($this->_expected_errors) > 0 &&
+            count($exp = end($this->_expected_errors))
+        ) {
             if ($exp[0] == "*" ||
                 (is_int(reset($exp)) && in_array($code, $exp)) ||
-                (is_string(reset($exp)) && in_array($message, $exp))) {
+                (is_string(reset($exp)) && in_array($message, $exp))
+            ) {
                 $mode = PEAR_ERROR_RETURN;
             }
         }
+
         // No mode given, try global ones
         if ($mode === null) {
             // Class error handler
@@ -565,46 +520,52 @@ class PEAR
         } else {
             $ec = 'PEAR_Error';
         }
+
         if (intval(PHP_VERSION) < 5) {
             // little non-eval hack to fix bug #12147
             include 'PEAR/FixPHP5PEARWarnings.php';
             return $a;
         }
+
         if ($skipmsg) {
             $a = new $ec($code, $mode, $options, $userinfo);
         } else {
             $a = new $ec($message, $code, $mode, $options, $userinfo);
         }
+
         return $a;
     }
 
-    // }}}
-    // {{{ throwError()
-
     /**
      * Simpler form of raiseError with fewer options.  In most cases
      * message, code and userinfo are enough.
      *
-     * @param string $message
+     * @param mixed $message a text error message or a PEAR error object
      *
+     * @param int $code      a numeric error code (it is up to your class
+     *                  to define these if you want to use codes)
+     *
+     * @param string $userinfo If you need to pass along for example debug
+     *                  information, this parameter is meant for that.
+     *
+     * @access public
+     * @return object   a PEAR error object
+     * @see PEAR::raiseError
      */
-    function &throwError($message = null,
-                         $code = null,
-                         $userinfo = null)
+    function &throwError($message = null, $code = null, $userinfo = null)
     {
         if (isset($this) && is_a($this, 'PEAR')) {
             $a = &$this->raiseError($message, $code, null, null, $userinfo);
             return $a;
-        } else {
-            $a = &PEAR::raiseError($message, $code, null, null, $userinfo);
-            return $a;
         }
+
+        $a = &PEAR::raiseError($message, $code, null, null, $userinfo);
+        return $a;
     }
 
-    // }}}
     function staticPushErrorHandling($mode, $options = null)
     {
-        $stack = &$GLOBALS['_PEAR_error_handler_stack'];
+        $stack       = &$GLOBALS['_PEAR_error_handler_stack'];
         $def_mode    = &$GLOBALS['_PEAR_default_error_mode'];
         $def_options = &$GLOBALS['_PEAR_default_error_options'];
         $stack[] = array($def_mode, $def_options);
@@ -673,8 +634,6 @@ class PEAR
         return true;
     }
 
-    // {{{ pushErrorHandling()
-
     /**
      * Push a new error handler on top of the error handler options stack. With this
      * you can easily override the actual error handler for some code and restore
@@ -708,9 +667,6 @@ class PEAR
         return true;
     }
 
-    // }}}
-    // {{{ popErrorHandling()
-
     /**
     * Pop the last error handler used
     *
@@ -732,11 +688,8 @@ class PEAR
         return true;
     }
 
-    // }}}
-    // {{{ loadExtension()
-
     /**
-    * OS independant PHP extension load. Remember to take care
+    * OS independent PHP extension load. Remember to take care
     * on the correct extension name for case sensitive OSes.
     *
     * @param string $ext The extension name
@@ -744,31 +697,38 @@ class PEAR
     */
     function loadExtension($ext)
     {
-        if (!extension_loaded($ext)) {
-            // if either returns true dl() will produce a FATAL error, stop that
-            if ((ini_get('enable_dl') != 1) || (ini_get('safe_mode') == 1)) {
-                return false;
-            }
-            if (OS_WINDOWS) {
-                $suffix = '.dll';
-            } elseif (PHP_OS == 'HP-UX') {
-                $suffix = '.sl';
-            } elseif (PHP_OS == 'AIX') {
-                $suffix = '.a';
-            } elseif (PHP_OS == 'OSX') {
-                $suffix = '.bundle';
-            } else {
-                $suffix = '.so';
-            }
-            return @dl('php_'.$ext.$suffix) || @dl($ext.$suffix);
+        if (extension_loaded($ext)) {
+            return true;
+        }
+
+        // if either returns true dl() will produce a FATAL error, stop that
+        if (
+            function_exists('dl') === false ||
+            ini_get('enable_dl') != 1 ||
+            ini_get('safe_mode') == 1
+        ) {
+            return false;
         }
-        return true;
-    }
 
-    // }}}
+        if (OS_WINDOWS) {
+            $suffix = '.dll';
+        } elseif (PHP_OS == 'HP-UX') {
+            $suffix = '.sl';
+        } elseif (PHP_OS == 'AIX') {
+            $suffix = '.a';
+        } elseif (PHP_OS == 'OSX') {
+            $suffix = '.bundle';
+        } else {
+            $suffix = '.so';
+        }
+
+        return @dl('php_'.$ext.$suffix) || @dl($ext.$suffix);
+    }
 }
 
-// {{{ _PEAR_call_destructors()
+if (PEAR_ZE2) {
+    include_once 'PEAR5.php';
+}
 
 function _PEAR_call_destructors()
 {
@@ -777,9 +737,16 @@ function _PEAR_call_destructors()
         sizeof($_PEAR_destructor_object_list))
     {
         reset($_PEAR_destructor_object_list);
-        if (PEAR::getStaticProperty('PEAR', 'destructlifo')) {
+        if (PEAR_ZE2) {
+            $destructLifoExists = PEAR5::getStaticProperty('PEAR', 'destructlifo');
+        } else {
+            $destructLifoExists = PEAR::getStaticProperty('PEAR', 'destructlifo');
+        }
+
+        if ($destructLifoExists) {
             $_PEAR_destructor_object_list = array_reverse($_PEAR_destructor_object_list);
         }
+
         while (list($k, $objref) = each($_PEAR_destructor_object_list)) {
             $classname = get_class($objref);
             while ($classname) {
@@ -798,14 +765,17 @@ function _PEAR_call_destructors()
     }
 
     // Now call the shutdown functions
-    if (is_array($GLOBALS['_PEAR_shutdown_funcs']) AND !empty($GLOBALS['_PEAR_shutdown_funcs'])) {
+    if (
+        isset($GLOBALS['_PEAR_shutdown_funcs']) &&
+        is_array($GLOBALS['_PEAR_shutdown_funcs']) &&
+        !empty($GLOBALS['_PEAR_shutdown_funcs'])
+    ) {
         foreach ($GLOBALS['_PEAR_shutdown_funcs'] as $value) {
             call_user_func_array($value[0], $value[1]);
         }
     }
 }
 
-// }}}
 /**
  * Standard PEAR error class for PHP 4
  *
@@ -817,16 +787,14 @@ function _PEAR_call_destructors()
  * @author     Tomas V.V. Cox <cox@idecnet.com>
  * @author     Gregory Beaver <cellog@php.net>
  * @copyright  1997-2006 The PHP Group
- * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
- * @version    Release: 1.7.2
+ * @license    http://opensource.org/licenses/bsd-license.php New BSD License
+ * @version    Release: 1.9.5
  * @link       http://pear.php.net/manual/en/core.pear.pear-error.php
  * @see        PEAR::raiseError(), PEAR::throwError()
  * @since      Class available since PHP 4.0.2
  */
 class PEAR_Error
 {
-    // {{{ properties
-
     var $error_message_prefix = '';
     var $mode                 = PEAR_ERROR_RETURN;
     var $level                = E_USER_NOTICE;
@@ -835,9 +803,6 @@ class PEAR_Error
     var $userinfo             = '';
     var $backtrace            = null;
 
-    // }}}
-    // {{{ constructor
-
     /**
      * PEAR_Error constructor
      *
@@ -868,12 +833,20 @@ class PEAR_Error
         $this->code      = $code;
         $this->mode      = $mode;
         $this->userinfo  = $userinfo;
-        if (!PEAR::getStaticProperty('PEAR_Error', 'skiptrace')) {
+
+        if (PEAR_ZE2) {
+            $skiptrace = PEAR5::getStaticProperty('PEAR_Error', 'skiptrace');
+        } else {
+            $skiptrace = PEAR::getStaticProperty('PEAR_Error', 'skiptrace');
+        }
+
+        if (!$skiptrace) {
             $this->backtrace = debug_backtrace();
             if (isset($this->backtrace[0]) && isset($this->backtrace[0]['object'])) {
                 unset($this->backtrace[0]['object']);
             }
         }
+
         if ($mode & PEAR_ERROR_CALLBACK) {
             $this->level = E_USER_NOTICE;
             $this->callback = $options;
@@ -881,20 +854,25 @@ class PEAR_Error
             if ($options === null) {
                 $options = E_USER_NOTICE;
             }
+
             $this->level = $options;
             $this->callback = null;
         }
+
         if ($this->mode & PEAR_ERROR_PRINT) {
             if (is_null($options) || is_int($options)) {
                 $format = "%s";
             } else {
                 $format = $options;
             }
+
             printf($format, $this->getMessage());
         }
+
         if ($this->mode & PEAR_ERROR_TRIGGER) {
             trigger_error($this->getMessage(), $this->level);
         }
+
         if ($this->mode & PEAR_ERROR_DIE) {
             $msg = $this->getMessage();
             if (is_null($options) || is_int($options)) {
@@ -907,47 +885,39 @@ class PEAR_Error
             }
             die(sprintf($format, $msg));
         }
-        if ($this->mode & PEAR_ERROR_CALLBACK) {
-            if (is_callable($this->callback)) {
-                call_user_func($this->callback, $this);
-            }
+
+        if ($this->mode & PEAR_ERROR_CALLBACK && is_callable($this->callback)) {
+            call_user_func($this->callback, $this);
         }
+
         if ($this->mode & PEAR_ERROR_EXCEPTION) {
             trigger_error("PEAR_ERROR_EXCEPTION is obsolete, use class PEAR_Exception for exceptions", E_USER_WARNING);
             eval('$e = new Exception($this->message, $this->code);throw($e);');
         }
     }
 
-    // }}}
-    // {{{ getMode()
-
     /**
      * Get the error mode from an error object.
      *
      * @return int error mode
      * @access public
      */
-    function getMode() {
+    function getMode()
+    {
         return $this->mode;
     }
 
-    // }}}
-    // {{{ getCallback()
-
     /**
      * Get the callback function/method from an error object.
      *
      * @return mixed callback function or object/method array
      * @access public
      */
-    function getCallback() {
+    function getCallback()
+    {
         return $this->callback;
     }
 
-    // }}}
-    // {{{ getMessage()
-
-
     /**
      * Get the error message from an error object.
      *
@@ -959,10 +929,6 @@ class PEAR_Error
         return ($this->error_message_prefix . $this->message);
     }
 
-
-    // }}}
-    // {{{ getCode()
-
     /**
      * Get error code from an error object
      *
@@ -974,9 +940,6 @@ class PEAR_Error
         return $this->code;
      }
 
-    // }}}
-    // {{{ getType()
-
     /**
      * Get the name of this error/exception.
      *
@@ -988,9 +951,6 @@ class PEAR_Error
         return get_class($this);
     }
 
-    // }}}
-    // {{{ getUserInfo()
-
     /**
      * Get additional user-supplied information.
      *
@@ -1002,9 +962,6 @@ class PEAR_Error
         return $this->userinfo;
     }
 
-    // }}}
-    // {{{ getDebugInfo()
-
     /**
      * Get additional debug information supplied by the application.
      *
@@ -1016,9 +973,6 @@ class PEAR_Error
         return $this->getUserInfo();
     }
 
-    // }}}
-    // {{{ getBacktrace()
-
     /**
      * Get the call backtrace from where the error was generated.
      * Supported with PHP 4.3.0 or newer.
@@ -1038,9 +992,6 @@ class PEAR_Error
         return $this->backtrace[$frame];
     }
 
-    // }}}
-    // {{{ addUserInfo()
-
     function addUserInfo($info)
     {
         if (empty($this->userinfo)) {
@@ -1050,14 +1001,10 @@ class PEAR_Error
         }
     }
 
-    // }}}
-    // {{{ toString()
     function __toString()
     {
         return $this->getMessage();
     }
-    // }}}
-    // {{{ toString()
 
     /**
      * Make a string representation of this object.
@@ -1065,7 +1012,8 @@ class PEAR_Error
      * @return string a string with an object summary
      * @access public
      */
-    function toString() {
+    function toString()
+    {
         $modes = array();
         $levels = array(E_USER_NOTICE  => 'notice',
                         E_USER_WARNING => 'warning',
@@ -1104,8 +1052,6 @@ class PEAR_Error
                        $this->error_message_prefix,
                        $this->userinfo);
     }
-
-    // }}}
 }
 
 /*
@@ -1115,4 +1061,3 @@ class PEAR_Error
  * c-basic-offset: 4
  * End:
  */
-?>
diff --git lib/PEAR/PEAR/PEAR5.php lib/PEAR/PEAR/PEAR5.php
new file mode 100644
index 0000000..4286067
--- /dev/null
+++ lib/PEAR/PEAR/PEAR5.php
@@ -0,0 +1,33 @@
+<?php
+/**
+ * This is only meant for PHP 5 to get rid of certain strict warning
+ * that doesn't get hidden since it's in the shutdown function
+ */
+class PEAR5
+{
+    /**
+    * If you have a class that's mostly/entirely static, and you need static
+    * properties, you can use this method to simulate them. Eg. in your method(s)
+    * do this: $myVar = &PEAR5::getStaticProperty('myclass', 'myVar');
+    * You MUST use a reference, or they will not persist!
+    *
+    * @access public
+    * @param  string $class  The calling classname, to prevent clashes
+    * @param  string $var    The variable to retrieve.
+    * @return mixed   A reference to the variable. If not set it will be
+    *                 auto initialised to NULL.
+    */
+    static function &getStaticProperty($class, $var)
+    {
+        static $properties;
+        if (!isset($properties[$class])) {
+            $properties[$class] = array();
+        }
+
+        if (!array_key_exists($var, $properties[$class])) {
+            $properties[$class][$var] = null;
+        }
+
+        return $properties[$class][$var];
+    }
+}
\ No newline at end of file
diff --git lib/Varien/Io/File.php lib/Varien/Io/File.php
index 23be977..329bf53 100644
--- lib/Varien/Io/File.php
+++ lib/Varien/Io/File.php
@@ -226,6 +226,17 @@ class Varien_Io_File extends Varien_Io_Abstract
         if (!$this->_streamHandler) {
             return false;
         }
+
+        /**
+         * Security enchancement for CSV data processing by Excel-like applications.
+         * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
+         */
+        foreach ($row as $key => $value) {
+            if (substr($value, 0, 1) === '=') {
+                $row[$key] = ' ' . $value;
+            }
+        }
+
         return @fputcsv($this->_streamHandler, $row, $delimiter, $enclosure);
     }
 
