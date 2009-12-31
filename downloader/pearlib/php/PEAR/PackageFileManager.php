<?php
/**
 * PEAR_PackageFileManager is designed to create and manipulate
 * package.xml version 1.0 only.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2003-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: PackageFileManager.php,v 1.59 2007/11/19 22:10:41 farell Exp $
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     File available since Release 0.1
 */

/**
 * PEAR installer
 */
require_once 'PEAR/Common.php';
/**#@+
 * Error Codes
 */
define('PEAR_PACKAGEFILEMANAGER_NOSTATE', 1);
define('PEAR_PACKAGEFILEMANAGER_NOVERSION', 2);
define('PEAR_PACKAGEFILEMANAGER_NOPKGDIR', 3);
define('PEAR_PACKAGEFILEMANAGER_NOBASEDIR', 4);
define('PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND', 5);
define('PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE', 6);
define('PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE', 7);
define('PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE', 8);
define('PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE', 9);
define('PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE', 10);
define('PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST', 11);
define('PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES', 12);
define('PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST', 13);
define('PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS', 14);
define('PEAR_PACKAGEFILEMANAGER_NOPACKAGE', 15);
define('PEAR_PACKAGEFILEMANAGER_WRONG_MROLE', 16);
define('PEAR_PACKAGEFILEMANAGER_NOSUMMARY', 17);
define('PEAR_PACKAGEFILEMANAGER_NODESC', 18);
define('PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS', 19);
define('PEAR_PACKAGEFILEMANAGER_NO_FILES', 20);
define('PEAR_PACKAGEFILEMANAGER_IGNORED_EVERYTHING', 21);
define('PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE', 22);
define('PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE', 23);
define('PEAR_PACKAGEFILEMANAGER_INVALID_ROLE', 24);
define('PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE', 25);
define('PEAR_PACKAGEFILEMANAGER_CVS_PACKAGED', 26);
define('PEAR_PACKAGEFILEMANAGER_NO_PHPCOMPATINFO', 27);
define('PEAR_PACKAGEFILEMANAGER_NONOTES', 28);
define('PEAR_PACKAGEFILEMANAGER_NOLICENSE', 29);
/**#@-*/
/**
 * Error messages
 * @global array $GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS']
 */
$GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS'] =
array(
    'en' =>
    array(
        PEAR_PACKAGEFILEMANAGER_NOSTATE =>
            'Release State (option \'state\') must by specified in PEAR_PackageFileManager ' .
            'setOptions (snapshot|devel|alpha|beta|stable)',
        PEAR_PACKAGEFILEMANAGER_NOVERSION =>
            'Release Version (option \'version\') must be specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_NOPKGDIR =>
            'Package source base directory (option \'packagedirectory\') must be ' .
            'specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_NOBASEDIR =>
            'Package install base directory (option \'baseinstalldir\') must be ' .
            'specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND =>
            'Base class "%s" can\'t be located',
        PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE =>
            'Base class "%s" can\'t be located in default or user-specified directories',
        PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE =>
            'Failed to write package.xml file to destination directory',
        PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE =>
            'Destination directory "%s" is unwritable',
        PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE =>
            'Failed to copy package.xml.tmp file to package.xml',
        PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE =>
            'Failed to open temporary file "%s" for writing',
        PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST =>
            'package.xml file path "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES =>
            'Directory "%s" is not a CVS directory (it must have the CVS/Entries file)',
        PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST =>
            'Package source base directory "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS =>
            'Run $managerclass->setOptions() before any other methods',
        PEAR_PACKAGEFILEMANAGER_NOPACKAGE =>
            'Package Name (option \'package\') must by specified in PEAR_PackageFileManager '.
            'setOptions to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_NOSUMMARY =>
            'Package Summary (option \'summary\') must by specified in PEAR_PackageFileManager' .
            ' setOptions to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_NODESC =>
            'Detailed Package Description (option \'description\') must be' .
            ' specified in PEAR_PackageFileManager setOptions to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_WRONG_MROLE =>
            'Maintainer role must be one of "%s", was "%s"',
        PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS =>
            'Add maintainers to a package before generating the package.xml',
        PEAR_PACKAGEFILEMANAGER_NO_FILES =>
            'No files found, check the path "%s"',
        PEAR_PACKAGEFILEMANAGER_IGNORED_EVERYTHING =>
            'No files left, check the path "%s" and ignore option "%s"',
        PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE =>
            'Package validation failed:%s%s',
        PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE =>
            'Replacement Type must be one of "%s", was passed "%s"',
        PEAR_PACKAGEFILEMANAGER_INVALID_ROLE =>
            'Invalid file role passed to addRole, must be one of "%s", was passed "%s"',
        PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE =>
            'addDependency had PHP as a package, use type="php"',
        PEAR_PACKAGEFILEMANAGER_CVS_PACKAGED =>
            'path "%path%" contains CVS directory',
        PEAR_PACKAGEFILEMANAGER_NO_PHPCOMPATINFO =>
            'PHP_Compat is not installed, cannot detect dependencies',
        PEAR_PACKAGEFILEMANAGER_NONOTES =>
            'Release Notes (option \'notes\') must be specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_NOLICENSE =>
            'Release License (option \'license\') must be specified in PEAR_PackageFileManager setOptions',
        ),
        // other language translations go here
     );
/**
 * PEAR :: PackageFileManager updates the <filelist></filelist> section
 * of a PEAR package.xml file to reflect the current files in
 * preparation for a release.
 *
 * The PEAR_PackageFileManager class uses a plugin system to generate the
 * list of files in a package.  This allows both standard recursive
 * directory parsing (plugin type file) and more intelligent options
 * such as the CVS browser {@link PEAR_PackageFileManager_Cvs}, which
 * grabs all files in a local CVS checkout to create the list, ignoring
 * any other local files.
 *
 * Other options include specifying roles for file extensions (all .php
 * files are role="php", for example), roles for directories (all directories
 * named "tests" are given role="tests" by default), and exceptions.
 * Exceptions are specific pathnames with * and ? wildcards that match
 * a default role, but should have another.  For example, perhaps
 * a debug.tpl template would normally be data, but should be included
 * in the docs role.  Along these lines, to exclude files entirely,
 * use the ignore option.
 *
 * Required options for a release include version, baseinstalldir, state,
 * and packagedirectory (the full path to the local location of the
 * package to create a package.xml file for)
 *
 * Example usage:
 * <code>
 * <?php
 * require_once('PEAR/PackageFileManager.php');
 * $packagexml = new PEAR_PackageFileManager;
 * $e = $packagexml->setOptions(
 * array('baseinstalldir' => 'PhpDocumentor',
 *  'version' => '1.2.1',
 *  'packagedirectory' => 'C:/Web Pages/chiara/phpdoc2/',
 *  'state' => 'stable',
 *  'filelistgenerator' => 'cvs', // generate from cvs, use file for directory
 *  'notes' => 'We\'ve implemented many new and exciting features',
 *  'ignore' => array('TODO', 'tests/'), // ignore TODO, all files in tests/
 *  'installexceptions' => array('phpdoc' => '/*'), // baseinstalldir ="/" for phpdoc
 *  'dir_roles' => array('tutorials' => 'doc'),
 *  'exceptions' => array('README' => 'doc', // README would be data, now is doc
 *                        'PHPLICENSE.txt' => 'doc'))); // same for the license
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     die();
 * }
 * $e = $test->addPlatformException('pear-phpdoc.bat', 'windows');
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     exit;
 * }
 * $packagexml->addRole('pkg', 'doc'); // add a new role mapping
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     exit;
 * }
 * // replace @PHP-BIN@ in this file with the path to php executable!  pretty neat
 * $e = $test->addReplacement('pear-phpdoc', 'pear-config', '@PHP-BIN@', 'php_bin');
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     exit;
 * }
 * $e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     exit;
 * }
 * // note use of {@link debugPackageFile()} - this is VERY important
 * if (isset($_GET['make']) || (isset($_SERVER['argv'][2]) &&
 *       $_SERVER['argv'][2] == 'make')) {
 *     $e = $packagexml->writePackageFile();
 * } else {
 *     $e = $packagexml->debugPackageFile();
 * }
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     die();
 * }
 * ?>
 * </code>
 *
 * In addition, a package.xml file can now be generated from
 * scratch, with the usage of new options package, summary, description, and
 * the use of the {@link addMaintainer()} method
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2003-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.6.3
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     Class available since Release 0.1
 */
class PEAR_PackageFileManager
{
    /**
     * Format: array(array(regexp-ready string to search for whole path,
     * regexp-ready string to search for basename of ignore strings),...)
     * @var false|array
     * @access private
     * @since  0.1
     */
    var $_ignore = false;

    /**
     * Contents of the package.xml file
     * @var string
     * @access private
     * @since  0.1
     */
    var $_packageXml = false;

    /**
     * Contents of the original package.xml file, if any
     * @var string
     * @access private
     * @since  0.9
     */
    var $_oldPackageXml = false;

    /**
     * @access private
     * @var PEAR_Common
     * @since  0.9
     */
    var $_pear;

    /**
     * List of warnings
     * @var array
     * @access private
     * @since  1.1.0
     */
    var $_warningStack = array();

    /**
     * flag used to determine whether to use PHP_CompatInfo to detect deps
     * @var boolean
     * @access private
     * @since  1.3.0
     */
    var $_detectDependencies = false;

    /**
     * @access private
     * @var string
     * @since  0.1
     */
    var $_options = array(
                      'packagefile' => 'package.xml',
                      'doctype' => 'http://pear.php.net/dtd/package-1.0',
                      'filelistgenerator' => 'file',
                      'license' => 'PHP License',
                      'changelogoldtonew' => true,
                      'roles' =>
                        array(
                            'h' => 'src',
                            'c' => 'src',
                            'm4' => 'src',
                            'w32' => 'src',
                            'dll' => 'ext',
                            'php' => 'php',
                            'html' => 'doc',
                            '*' => 'data',
                             ),
                      'dir_roles' =>
                        array(
                            'docs' => 'doc',
                            'examples' => 'doc',
                            'tests' => 'test',
                             ),
                      'exceptions' => array(),
                      'installexceptions' => array(),
                      'installas' => array(),
                      'platformexceptions' => array(),
                      'scriptphaseexceptions' => array(),
                      'ignore' => array(),
                      'include' => false,
                      'deps' => false,
                      'maintainers' => false,
                      'notes' => '',
                      'changelognotes' => false,
                      'outputdirectory' => false,
                      'pathtopackagefile' => false,
                      'lang' => 'en',
                      'configure_options' => array(),
                      'replacements' => array(),
                      'pearcommonclass' => false,
                      'simpleoutput' => false,
                      'addhiddenfiles' => false,
                      'cleardependencies' => false,
                      );

    /**
     * Does nothing, use setOptions
     *
     * The constructor is not used in order to be able to
     * return a PEAR_Error from setOptions
     *
     * @see    setOptions()
     * @access public
     * @since  0.1
     */
    function PEAR_PackageFileManager()
    {
    }

    /**
     * Set package.xml generation options
     *
     * The options array is indexed as follows:
     * <code>
     * $options = array('option_name' => <optionvalue>);
     * </code>
     *
     * The documentation below simplifies this description through
     * the use of option_name without quotes
     *
     * Configuration options:
     * - lang: lang controls the language in which error messages are
     *         displayed.  There are currently only English error messages,
     *         but any contributed will be added over time.<br>
     *         Possible values: en (default)
     * - packagefile: the name of the packagefile, defaults to package.xml
     * - pathtopackagefile: the path to an existing package file to read in,
     *                      if different from the packagedirectory
     * - packagedirectory: the path to the base directory of the package.  For
     *                     package PEAR_PackageFileManager, this path is
     *                     /path/to/pearcvs/pear/PEAR_PackageFileManager where
     *                     /path/to/pearcvs is a local path on your hard drive
     * - outputdirectory: the path in which to place the generated package.xml
     *                    by default, this is ignored, and the package.xml is
     *                    created in the packagedirectory
     * - filelistgenerator: the <filelist> section plugin which will be used.
     *                      In this release, there are two generator plugins,
     *                      file and cvs.  For details, see the docs for these
     *                      plugins
     * - usergeneratordir: For advanced users.  If you write your own filelist
     *                     generator plugin, use this option to tell
     *                     PEAR_PackageFileManager where to find the file that
     *                     contains it.  If the plugin is named foo, the class
     *                     must be named PEAR_PackageFileManager_Foo
     *                     no matter where it is located.  By default, the Foo
     *                     plugin is located in PEAR/PackageFileManager/Foo.php.
     *                     If you pass /path/to/foo in this option, setOptions
     *                     will look for PEAR_PackageFileManager_Foo in
     *                     /path/to/foo/Foo.php
     * - doctype: Specifies the DTD of the package.xml file.  Default is
     *            http://pear.php.net/dtd/package-1.0
     * - pearcommonclass: Specifies the name of the class to instantiate, default
     *                    is PEAR_PackageFileManager_ComplexGenerator or PEAR_Common, but users can
     *                    override this with a custom class that implements
     *                    PEAR_Common's method interface
     * - changelogoldtonew: True if the ChangeLog should list from oldest entry to
     *                      newest.  Set to false if you would like new entries first
     * - simpleoutput: True if the package.xml should not contain md5sum or <provides />
     *                 for readability
     * - addhiddenfiles: True if you wish to add hidden files/directories that begin with .
     *                   like .bashrc.  This is only used by the File generator.  The CVS
     *                   generator will use all files in CVS regardless of format
     *
     * package.xml simple options:
     * - baseinstalldir: The base directory to install this package in.  For
     *                   package PEAR_PackageFileManager, this is "PEAR", for
     *                   package PEAR, this is "/"
     * - license: The license this release is released under.  Default is
     *            PHP License if left unspecified
     * - notes: Release notes, any text describing what makes this release unique
     * - changelognotes: notes for the changelog, this should be more detailed than
     *                   the release notes.  By default, PEAR_PackageFileManager uses
     *                   the notes option for the changelog as well
     * - version: The version number for this release.  Remember the convention for
     *            numbering: initial alpha is between 0 and 1, add b<beta number> for
     *            beta as in 1.0b1, the integer portion of the version should specify
     *            backwards compatibility, as in 1.1 is backwards compatible with 1.0,
     *            but 2.0 is not backwards compatible with 1.10.  Also note that 1.10
     *            is a greater release version than 1.1 (think of it as "one point ten"
     *            and "one point one").  Bugfix releases should be a third decimal as in
     *            1.0.1, 1.0.2
     * - package: [optional] Package name.  Use this to create a new package.xml, or
     *            overwrite an existing one from another package used as a template
     * - summary: [optional] Summary of package purpose
     * - description: [optional] Description of package purpose.  Note that the above
     *                three options are not optional when creating a new package.xml
     *                from scratch
     *
     * <b>WARNING</b>: all complex options that require a file path are case-sensitive
     *
     * package.xml complex options:
     * - cleardependencies: since version 1.3.0, this option will erase any existing
     *                      dependencies in the package.xml if set to true
     * - ignore: an array of filenames, directory names, or wildcard expressions specifying
     *           files to exclude entirely from the package.xml.  Wildcards are operating system
     *           wildcards * and ?.  file*foo.php will exclude filefoo.php, fileabrfoo.php and
     *           filewho_is_thisfoo.php.  file?foo.php will exclude fileafoo.php and will not
     *           exclude fileaafoo.php.  test/ will exclude all directories and subdirectories of
     *           ANY directory named test encountered in directory parsing.  *test* will exclude
     *           all files and directories that contain test in their name
     * - include: an array of filenames, directory names, or wildcard expressions specifying
     *            files to include in the listing.  All other files will be ignored.
     *            Wildcards are in the same format as ignore
     * - roles: this is an array mapping file extension to install role.  This
     *          specifies default behavior that can be overridden by the exceptions
     *          option and dir_roles option.  use {@link addRole()} to add a new
     *          role to the pre-existing array
     * - dir_roles: this is an array mapping directory name to install role.  All
     *              files in a directory whose name matches the directory will be
     *              given the install role specified.  Single files can be excluded
     *              from this using the exceptions option.  The directory should be
     *              a relative path from the baseinstalldir, or "/" for the baseinstalldir
     * - exceptions: specify file role for specific files.  This array maps all files
     *               matching the exact name of a file to a role as in "file.ext" => "role"
     * - deps: dependency array.  Pass in an empty array to clear all dependencies, and use
     *         {@link addDependency()} to add new ones/replace existing ones
     * - maintainers: maintainers array.  Pass in an empty array to clear all maintainers, and
     *                use {@link addMaintainer()} to add a new maintainer/replace existing maintainer
     * - installexceptions: array mapping of specific filenames to baseinstalldir values.  Use
     *                      this to force the installation of a file into another directory,
     *                      such as forcing a script to be in the root scripts directory so that
     *                      it will be in the path.  The filename must be a relative path to the
     *                      packagedirectory
     * - platformexceptions: array mapping of specific filenames to the platform they should be
     *                       installed on.  Use this to specify unix-only files or windows-only
     *                       files.  The format of the platform string must be
     *                       OS-version-cpu-extra if any more specific information is needed,
     *                       and the OS must be in lower case as in "windows."  The match is
     *                       performed using a regular expression, but uses * and ? wildcards
     *                       instead of .* and .?.  Note that hpux/aix/irix/linux are all
     *                       exclusive.  To select non-windows, use (*ix|*ux)
     * - scriptphaseexceptions: array mapping of scripts to their install phase.  This can be
     *                          one of: pre-install, post-install, pre-uninstall, post-uninstall,
     *                          pre-build, post-build, pre-setup, or post-setup
     * - installas: array mapping of specific filenames to the filename they should be installed as.
     *              Use this to specify new filenames for files that should be installed.  This will
     *              often be used in conjunction with platformexceptions if there are two files for
     *              different OSes that must have the same name when installed.
     * - replacements: array mapping of specific filenames to complex text search-and-replace that
     *                 should be performed upon install.  The format is:
     *   <pre>
     *   filename => array('type' => php-const|pear-config|package-info
     *                     'from' => text in file
     *                     'to' => name of variable)
     *   </pre>
     *                 if type is php-const, then 'to' must be the name of a PHP Constant.
     *                 If type is pear-config, then 'to' must be the name of a PEAR config
     *                 variable accessible through a PEAR_Config class->get() method.  If
     *                 type is package-info, then 'to' must be the name of a section from
     *                 the package.xml file used to install this file.
     * - globalreplacements: a list of replacements that should be performed on every single file.
     *                       The format is the same as replacements (since 1.4.0)
     * - configure_options: array specifies build options for PECL packages (you should probably
     *                      use PECL_Gen instead, but it's here for completeness)
     *
     * @param array   $options  (optional) list of generation options
     * @param boolean $internal (optional) private function call
     *
     * @see    PEAR_PackageFileManager_File
     * @see    PEAR_PackageFileManager_CVS
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_NOSTATE
     * @throws PEAR_PACKAGEFILEMANAGER_NOVERSION
     * @throws PEAR_PACKAGEFILEMANAGER_NOPKGDIR
     * @throws PEAR_PACKAGEFILEMANAGER_NOBASEDIR
     * @throws PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE
     * @throws PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND
     * @access public
     * @since  0.1
     */
    function setOptions($options = array(), $internal = false)
    {
        if (!$internal) {
            if (!isset($options['state']) || empty($options['state'])) {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOSTATE);
            }
            if (!isset($options['version']) || empty($options['version'])) {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOVERSION);
            }
        }
        if (!isset($options['packagedirectory']) && !$internal) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOPKGDIR);
        } elseif (isset($options['packagedirectory'])) {
            $options['packagedirectory'] = str_replace(DIRECTORY_SEPARATOR,
                                                     '/',
                                                     realpath($options['packagedirectory']));
            if ($options['packagedirectory']{strlen($options['packagedirectory']) - 1} != '/') {
                $options['packagedirectory'] .= '/';
            }
        }
        if (isset($options['pathtopackagefile'])) {
            $options['pathtopackagefile'] = str_replace(DIRECTORY_SEPARATOR,
                                                     '/',
                                                     realpath($options['pathtopackagefile']));
            if ($options['pathtopackagefile']{strlen($options['pathtopackagefile']) - 1} != '/') {
                $options['pathtopackagefile'] .= '/';
            }
        }
        if (!isset($options['baseinstalldir']) && !$internal) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOBASEDIR);
        }
        $this->_options = array_merge($this->_options, $options);
        if (!isset($this->_options['roles']['*'])) {
            $this->_options['roles']['*'] = 'data';
        }

        if (!class_exists($this->_options['pearcommonclass'])) {
            if ($this->_options['simpleoutput']) {
                if ($this->isIncludeable('PEAR/PackageFile/Generator/v1.php')) {
                    include_once 'PEAR/PackageFileManager/SimpleGenerator.php';
                    $this->_options['pearcommonclass'] = 'PEAR_PackageFileManager_SimpleGenerator';
                } else {
                    include_once 'PEAR/PackageFileManager/XMLOutput.php';
                    $this->_options['pearcommonclass'] = 'PEAR_PackageFileManager_XMLOutput';
                }
            } else {
                if ($this->isIncludeable('PEAR/PackageFile/Generator/v1.php')) {
                    include_once 'PEAR/PackageFileManager/ComplexGenerator.php';
                    $this->_options['pearcommonclass'] = 'PEAR_PackageFileManager_ComplexGenerator';
                } else {
                    $this->_options['pearcommonclass'] = 'PEAR_Common';
                }
            }
        }
        $path = ($this->_options['pathtopackagefile'] ?
                    $this->_options['pathtopackagefile'] : $this->_options['packagedirectory']);

        $this->_options['filelistgenerator'] =
            ucfirst(strtolower($this->_options['filelistgenerator']));
        if (!$internal) {
            if (PEAR::isError($res =
                  $this->_getExistingPackageXML($path, $this->_options['packagefile']))) {
                return $res;
            }
        }

        // file generator resource to load
        $resource = 'PEAR/PackageFileManager/' . $this->_options['filelistgenerator'] . '.php';
        // file generator class name
        $className = substr($resource, 0, -4);
        $className = str_replace('/', '_', $className);

        if (!class_exists($className)) {
            // attempt to load the interface from the standard PEAR location
            if ($this->isIncludeable($resource)) {
                include_once $resource;
            } elseif (isset($this->_options['usergeneratordir'])) {
                // attempt to load from a user-specified directory
                if (is_dir(realpath($this->_options['usergeneratordir']))) {
                    $this->_options['usergeneratordir'] =
                        str_replace(DIRECTORY_SEPARATOR,
                                    '/',
                                    realpath($this->_options['usergeneratordir']));
                    if ($this->_options['usergeneratordir']{strlen($this->_options['usergeneratordir'])
                          - 1} != '/') {
                        $this->_options['usergeneratordir'] .= '/';
                    }
                } else {
                    $this->_options['usergeneratordir'] = '////';
                }
                $usergenerator = $this->_options['usergeneratordir'] .
                    $this->_options['filelistgenerator'] . '.php';
                if (file_exists($usergenerator) && is_readable($usergenerator)) {
                    include_once $usergenerator;
                }
                if (!class_exists($className)) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE,
                        $className);
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND,
                    $className);
            }
        }
    }

    /**
     * Import options from an existing package.xml
     *
     * @param string $packagefile name of package file
     * @param array  $options     (optional) list of generation options
     *
     * @return true|PEAR_Error
     * @access public
     * @since  1.5.0
     */
    function importOptions($packagefile, $options = array())
    {
        $options['deps'] = $options['maintainers'] = false;
        $this->setOptions($options, true);
        if (PEAR::isError($res = $this->_getExistingPackageXML(dirname($packagefile) .
              DIRECTORY_SEPARATOR, basename($packagefile)))) {
            return $res;
        }
        $this->_options['package']     = $this->_oldPackageXml['package'];
        $this->_options['summary']     = $this->_oldPackageXml['summary'];
        $this->_options['description'] = $this->_oldPackageXml['description'];
        $this->_options['date']        = $this->_oldPackageXml['release_date'];
        $this->_options['version']     = $this->_oldPackageXml['version'];
        $this->_options['license']     = $this->_oldPackageXml['release_license'];
        $this->_options['state']       = $this->_oldPackageXml['release_state'];
        $this->_options['notes']       = $this->_oldPackageXml['release_notes'];
        $this->setOptions($options, true);
        if (isset($this->_packageXml['release_deps'])) {
            $this->_options['deps'] = $this->_packageXml['release_deps'];
        }
        $this->_options['maintainers'] = $this->_oldPackageXml['maintainers'];
        return true;
    }

    /**
     * Get the existing options
     *
     * @return array
     * @access public
     * @since  1.5.0
     */
    function getOptions()
    {
        return $this->_options;
    }

    /**
     * Add an extension/role mapping to the role mapping option
     *
     * Roles influence both where a file is installed and how it is installed.
     * Files with role="data" are in a completely different directory hierarchy
     * from the program files of role="php"
     *
     * In PEAR 1.3b2, these roles are
     * - php (most common)
     * - data
     * - doc
     * - test
     * - script (gives the file an executable attribute)
     * - src
     *
     * @param string $extension file extension
     * @param string $role      role
     *
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_ROLE
     * @access public
     * @since  0.1
     */
    function addRole($extension, $role)
    {
        $roles = call_user_func(array($this->_options['pearcommonclass'], 'getfileroles'));
        if (!in_array($role, $roles)) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_INVALID_ROLE, implode($roles, ', '), $role);
        }
        $this->_options['roles'][$extension] = $role;
    }

    /**
     * Add an install-time platform conditional install for a file
     *
     * The format of the platform string must be
     * OS-version-cpu-extra if any more specific information is needed,
     * and the OS must be in lower case as in "windows."  The match is
     * performed using a regular expression, but uses * and ? wildcards
     * instead of .* and .?.  Note that hpux/aix/irix/linux are all
     * exclusive.  To select non-windows, use (*ix|*ux)
     *
     * This information is based on eyeing the source for OS/Guess.php, so
     * if you are unsure of what to do, read that file.
     *
     * @param string $path     relative path of file (relative to packagedirectory option)
     * @param string $platform platform descriptor string
     *
     * @return void
     * @access public
     * @since  0.10
     */
    function addPlatformException($path, $platform)
    {
        if (!isset($this->_options['platformexceptions'])) {
            $this->_options['platformexceptions'] = array();
        }
        $this->_options['platformexceptions'][$path] = $platform;
    }

    /**
     * Add a replacement option for all files
     *
     * This sets an install-time complex search-and-replace function
     * allowing the setting of platform-specific variables in all
     * installed files.
     *
     * if $type is php-const, then $to must be the name of a PHP Constant.
     * If $type is pear-config, then $to must be the name of a PEAR config
     * variable accessible through a {@link PEAR_Config::get()} method.  If
     * type is package-info, then $to must be the name of a section from
     * the package.xml file used to install this file.
     *
     * @param string $type variable type, either php-const, pear-config or package-info
     * @param string $from text to replace in the source file
     * @param string $to   variable name to use for replacement
     *
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE
     * @access public
     * @since  1.4.0
     */
    function addGlobalReplacement($type, $from, $to)
    {
        if (!isset($this->_options['globalreplacements'])) {
            $this->_options['globalreplacements'] = array();
        }
        $types = call_user_func(array($this->_options['pearcommonclass'], 'getreplacementtypes'));
        if (!in_array($type, $types)) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE,
                implode($types, ', '), $type);
        }
        $this->_options['globalreplacements'][] =
            array('type' => $type, 'from' => $from, 'to' => $to);
    }

    /**
     * Add a replacement option for a file
     *
     * This sets an install-time complex search-and-replace function
     * allowing the setting of platform-specific variables in an
     * installed file.
     *
     * if $type is php-const, then $to must be the name of a PHP Constant.
     * If $type is pear-config, then $to must be the name of a PEAR config
     * variable accessible through a {@link PEAR_Config::get()} method.  If
     * type is package-info, then $to must be the name of a section from
     * the package.xml file used to install this file.
     *
     * @param string $path relative path of file (relative to packagedirectory option)
     * @param string $type variable type, either php-const, pear-config or package-info
     * @param string $from text to replace in the source file
     * @param string $to   variable name to use for replacement
     *
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE
     * @access public
     * @since  0.10
     */
    function addReplacement($path, $type, $from, $to)
    {
        if (!isset($this->_options['replacements'])) {
            $this->_options['replacements'] = array();
        }
        $types = call_user_func(array($this->_options['pearcommonclass'], 'getreplacementtypes'));
        if (!in_array($type, $types)) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE,
                implode($types, ', '), $type);
        }
        $this->_options['replacements'][$path][] = array('type' => $type, 'from' => $from, 'to' => $to);
    }

    /**
     * Add a maintainer to the list of maintainers.
     *
     * Every maintainer must have a valid account at pear.php.net.  The
     * first parameter is the account name (for instance, cellog is the
     * handle for Greg Beaver at pear.php.net).  Every maintainer has
     * one of four possible roles:
     * - lead: the primary maintainer
     * - developer: an important developer on the project
     * - contributor: self-explanatory
     * - helper: ditto
     *
     * Finally, specify the name and email of the maintainer
     *
     * @param string $handle username on pear.php.net of maintainer
     * @param string $role   lead|developer|contributor|helper role of maintainer
     * @param string $name   full name of maintainer
     * @param string $email  email address of maintainer
     *
     * @return void|PEAR_Error
     * @access public
     * @since  0.9
     */
    function addMaintainer($handle, $role, $name, $email)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!in_array($role, $GLOBALS['_PEAR_Common_maintainer_roles'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_WRONG_MROLE,
                implode(', ', call_user_func(array($this->_options['pearcommonclass'],
                    'getUserRoles'))),
                $role);
        }
        if (!isset($this->_packageXml['maintainers'])) {
            $this->_packageXml['maintainers'] = array();
        }
        $found = false;
        foreach ($this->_packageXml['maintainers'] as $index => $maintainer) {
            if ($maintainer['handle'] == $handle) {
                $found = $index;
                break;
            }
        }
        $maintainer =
            array('handle' => $handle, 'role' => $role, 'name' => $name, 'email' => $email);
        if ($found !== false) {
            $this->_packageXml['maintainers'][$found] = $maintainer;
        } else {
            $this->_packageXml['maintainers'][] = $maintainer;
        }
    }

    /**
     * Add an install-time configuration option for building of source
     *
     * This option is only useful to PECL projects that are built upon
     * installation
     *
     * @param string $name    name of the option
     * @param string $prompt  prompt to display to the user
     * @param string $default (optional) default value
     *
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @return void|PEAR_Error
     * @access public
     * @since  0.9
     */
    function addConfigureOption($name, $prompt, $default = null)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!isset($this->_packageXml['configure_options'])) {
            $this->_packageXml['configure_options'] = array();
        }
        $found = false;
        foreach ($this->_packageXml['configure_options'] as $index => $option) {
            if ($option['name'] == $name) {
                $found = $index;
                break;
            }
        }
        $option = array('name' => $name, 'prompt' => $prompt);
        if (isset($default)) {
            $option['default'] = $default;
        }
        if ($found !== false) {
            $this->_packageXml['configure_options'][$found] = $option;
        } else {
            $this->_packageXml['configure_options'][] = $option;
        }
    }

    /**
     * Uses PEAR::PHP_CompatInfo package to detect dependencies (extensions, php version)
     *
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @throws PEAR_PACKAGEFILEMANAGER_NO_PHPCOMPATINFO
     * @access public
     * @since  1.3.0
     */
    function detectDependencies()
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!$this->isIncludeable('PHP/CompatInfo.php')) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NO_PHPCOMPATINFO);
        } else {
            include_once 'PHP/CompatInfo.php';
            $this->_detectDependencies = true;
        }
    }

    /**
     * Returns whether or not a file is in the include path.
     *
     * @param string $file path to filename
     *
     * @return boolean true if the file is in the include path, false otherwise
     * @access public
     * @since  1.3.0
     */
    function isIncludeable($file)
    {
        if (!defined('PATH_SEPARATOR')) {
            define('PATH_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? ';' : ':');
        }
        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $file) &&
                  is_readable($path . DIRECTORY_SEPARATOR . $file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add a dependency on another package, or an extension/php
     *
     * This will overwrite an existing dependency if it is found.  In
     * other words, if a dependency on PHP 4.1.0 exists, and
     * addDependency('php', '4.3.0', 'ge', 'php') is called, the existing
     * dependency on PHP 4.1.0 will be overwritten with the new one on PHP 4.3.0
     *
     * @param string  $name     Dependency element name
     * @param string  $version  (optional) Dependency version
     * @param string  $operator A specific operator for the version, this can be one of:
     *   'has', 'not', 'lt', 'le', 'eq', 'ne', 'ge', or 'gt'
     * @param string  $type     (optional) Dependency type.  This can be one of:
     *   'pkg', 'ext', 'php', 'prog', 'os', 'sapi', or 'zend'
     * @param boolean $optional (optional) true if dependency is optional
     *
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @throws PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE
     * @return void|PEAR_Error
     * @access public
     * @since  0.1
     */
    function addDependency($name, $version = false, $operator = 'ge', $type = 'pkg', $optional = false)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if ((strtolower($name) == 'php') && (strtolower($type) == 'pkg')) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE);
        }
        if (!isset($this->_packageXml['release_deps']) || !is_array($this->_packageXml['release_deps'])) {
            $this->_packageXml['release_deps'] = array();
        }
        $found = false;
        foreach ($this->_packageXml['release_deps'] as $index => $dep) {
            if ($type == 'php') {
                if ($dep['type'] == 'php') {
                    $found = $index;
                    break;
                }
            } else {
                if (isset($dep['name']) && $dep['name'] == $name && $dep['type'] == $type) {
                    $found = $index;
                    break;
                }
            }
        }
        $dep =
            array(
                'name' => $name,
                'type' => $type);
        if ($type == 'php') {
            unset($dep['name']);
        }
        if ($operator) {
            $dep['rel'] = $operator;
            if ($dep['rel'] != 'has' && $version) {
                $dep['version'] = $version;
            }
        }

        if ($optional) {
            $dep['optional'] = 'yes';
        } else {
            $dep['optional'] = 'no';
        }

        if ($found !== false) {
            $this->_packageXml['release_deps'][$found] = $dep; // overwrite existing dependency
        } else {
            $this->_packageXml['release_deps'][] = $dep; // add new dependency
        }
    }

    /**
     * Writes the package.xml file out with the newly created <release></release> tag
     *
     * ALWAYS use {@link debugPackageFile} to verify that output is correct before
     * overwriting your package.xml
     *
     * @param boolean $debuginterface (optional) null if no debugging, true if web interface, false if command-line
     *
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @throws PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS
     * @throws PEAR_PACKAGEFILEMANAGER_NONOTES
     * @throws PEAR_PACKAGEFILEMANAGER_NOLICENSE
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE
     * @throws PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE
     * @return true|PEAR_Error
     * @access public
     * @since  0.1
     */
    function writePackageFile($debuginterface = null)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!isset($this->_packageXml['maintainers']) || empty($this->_packageXml['maintainers'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS);
        }
        if (!isset($this->_options['notes']) || empty($this->_options['notes'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NONOTES);
        }
        if (!isset($this->_options['license']) || empty($this->_options['license'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOLICENSE);
        }
        extract($this->_options);
        $date = date('Y-m-d');
        if (isset($package)) {
            $this->_packageXml['package'] = $package;
        }
        if (isset($summary)) {
            $this->_packageXml['summary'] = $summary;
        }
        if (isset($description)) {
            $this->_packageXml['description'] = $description;
        }
        $this->_packageXml['release_date']    = $date;
        $this->_packageXml['version']         = $version;
        $this->_packageXml['release_license'] = $license;
        $this->_packageXml['release_state']   = $state;
        $this->_packageXml['release_notes']   = $notes;

        $PEAR_Common = $this->_options['pearcommonclass'];
        $this->_pear = new $PEAR_Common;
        if (method_exists($this->_pear, 'setPackageFileManager')) {
            $this->_pear->setPackageFileManager($this);
        }
        $this->_packageXml['filelist'] = $this->_getFileList();

        $warnings = $this->getWarnings();
        if (count($warnings)) {
            $nl = (isset($debuginterface) && $debuginterface ? '<br />' : "\n");
            foreach ($warnings as $errmsg) {
                echo 'WARNING: ' . $errmsg['message'] . $nl;
            }
        }
        if (PEAR::isError($this->_packageXml['filelist'])) {
            return $this->_packageXml['filelist'];
        }
        if (isset($this->_pear->pkginfo['provides'])) {
            $this->_packageXml['provides'] = $this->_pear->pkginfo['provides'];
        }
        if ($this->_options['simpleoutput']) {
            unset($this->_packageXml['provides']);
        }
        $this->_packageXml['release_deps'] = $this->_getDependencies();
        $this->_updateChangeLog();

        $common   = &$this->_pear;
        $warnings = $errors = array();
        if (method_exists($common, 'setPackageFileManagerOptions')) {
            $common->setPackageFileManagerOptions($this->_options);
        }
        $packagexml = $common->xmlFromInfo($this->_packageXml);
        if (PEAR::isError($packagexml)) {
            $errs = $packagexml->getUserinfo();
            if (is_array($errs)) {
                foreach ($errs as $error) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
            }
        } else {
            $common->validatePackageInfo($packagexml, $warnings, $errors,
                $this->_options['packagedirectory']);
        }
        if (count($errors)) {
            $ret = '';
            $nl  = (isset($debuginterface) && $debuginterface ? '<br />' : "\n");
            foreach ($errors as $errmsg) {
                $ret .= $errmsg . $nl;
            }
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE, $nl, $ret);
        }
        if (count($warnings)) {
            $nl = (isset($debuginterface) && $debuginterface ? '<br />' : "\n");
            foreach ($warnings as $errmsg) {
                echo $errmsg . $nl;
            }
        }
        if (!strpos($packagexml, '<!DOCTYPE')) {
            // hack to fix pear
            $packagexml = str_replace('<package version="1.0">',
                '<!DOCTYPE package SYSTEM "' . $this->_options['doctype'] .
                "\">\n<package version=\"1.0\">",
                $packagexml);
        }
        if (isset($debuginterface)) {
            if ($debuginterface) {
                echo '<pre>' . htmlentities($packagexml) . '</pre>';
            } else {
                echo $packagexml;
            }
            return true;
        }
        $outputdir = ($this->_options['outputdirectory'] ?
                        $this->_options['outputdirectory'] : $this->_options['packagedirectory']);
        if ((file_exists($outputdir . $this->_options['packagefile']) &&
                is_writable($outputdir . $this->_options['packagefile']))
                ||
                @touch($outputdir . $this->_options['packagefile'])) {
            if ($fp = @fopen($outputdir . $this->_options['packagefile'] . '.tmp', "w")) {
                $written = @fwrite($fp, $packagexml);
                @fclose($fp);
                if ($written === false) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE);
                }
                if (!@copy($outputdir . $this->_options['packagefile'] . '.tmp',
                        $outputdir . $this->_options['packagefile'])) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE);
                } else {
                    @unlink($outputdir . $this->_options['packagefile'] . '.tmp');
                    return true;
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE,
                    $outputdir . $this->_options['packagefile'] . '.tmp');
            }
        } else {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE, $outputdir);
        }
    }

    /**
     * ALWAYS use this to test output before overwriting your package.xml!!
     *
     * This method instructs writePackageFile() to simply print the package.xml
     * to output, either command-line or web-friendly (this is automatic
     * based on the value of php_sapi_name())
     *
     * @uses writePackageFile() calls with the debug parameter set based on
     *       whether it is called from the command-line or web interface
     * @return true|PEAR_Error
     * @access public
     * @since  0.1
     */
    function debugPackageFile()
    {
        $webinterface = php_sapi_name() != 'cli';
        return $this->writePackageFile($webinterface);
    }

    /**
     * Store a warning on the warning stack
     *
     * @param integer $code error code
     * @param array   $info additional specific error info
     *
     * @return void
     * @access public
     * @since  1.1.0
     */
    function pushWarning($code, $info)
    {
        $this->_warningStack[] = array('code' => $code,
                                       'message' => $this->_getMessage($code, $info));
    }

    /**
     * Retrieve the list of warnings
     *
     * @return array
     * @access public
     * @since  1.1.0
     */
    function getWarnings()
    {
        $a = $this->_warningStack;
        $this->_warningStack = array();
        return $a;
    }

    /**
     * Retrieve an error message from a code
     *
     * @param integer $code error code
     * @param array   $info additional specific error info
     *
     * @return string Error message
     * @access private
     * @since  1.1.0
     */
    function _getMessage($code, $info)
    {
        $msg = $GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS'][$this->_options['lang']][$code];
        foreach ($info as $name => $value) {
            $msg = str_replace('%' . $name . '%', $value, $msg);
        }
        return $msg;
    }

    /**
     * Utility function to shorten error generation code
     *
     * {@source}
     *
     * @param integer $code error code
     * @param string  $i1   (optional) additional specific error info #1
     * @param string  $i2   (optional) additional specific error info #2
     *
     * @return PEAR_Error
     * @static
     * @access public
     * @since  0.9
     */
    function raiseError($code, $i1 = '', $i2 = '')
    {
        return PEAR::raiseError('PEAR_PackageFileManager Error: ' .
                    sprintf($GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS'][$this->_options['lang']][$code],
                    $i1, $i2), $code);
    }

    /**
     * Uses {@link PEAR_Common::analyzeSourceCode()} and {@link PEAR_Common::buildProvidesArray()}
     * to create the <provides></provides> section of the package.xml
     *
     * @param object &$pear PEAR_Common
     * @param string $file  path to source file
     *
     * @return void
     * @access private
     * @since  0.9
     */
    function _addProvides(&$pear, $file)
    {
        if (!($a = $pear->analyzeSourceCode($file))) {
            return;
        } else {
            $pear->buildProvidesArray($a);
        }
    }

    /**
     * Generates the xml from the file list
     *
     * @uses   getDirTag() generate the xml from the array
     * @return string
     * @access private
     * @since  0.1
     */
    function _getFileList()
    {
        $generatorclass = 'PEAR_PackageFileManager_' . $this->_options['filelistgenerator'];
        $generator      = new $generatorclass($this, $this->_options);
        if ($this->_options['simpleoutput'] && is_a($this->_pear, 'PEAR_Common')) {
            return $this->_getSimpleDirTag($this->_struc = $generator->getFileList());
        }
        return $this->_getDirTag($this->_struc = $generator->getFileList());
    }

    /**
     * Recursively generate the <filelist> section's <dir> and <file> tags, but with
     * simple human-readable output
     *
     * @param array|PEAR_Error $struc   the sorted directory structure, or an error
     *                        from filelist generation
     * @param false|string     $role    whether the parent directory has a role this should
     *                         inherit
     * @param string           $_curdir indentation level
     *
     * @return array|PEAR_Error
     * @access private
     * @since  1.2.0
     */
    function _getSimpleDirTag($struc, $role = false, $_curdir = '')
    {
        if (PEAR::isError($struc)) {
            return $struc;
        }
        extract($this->_options);
        $ret = array();
        foreach ($struc as $dir => $files) {
            if (false && $dir === '/') {
                // global directory role? overrides all exceptions except file exceptions
                if (isset($dir_roles['/'])) {
                    $role = $dir_roles['/'];
                }
                return array(
                    'baseinstalldir' => $this->_options['baseinstalldir'],
                    '##files' => $this->_getSimpleDirTag($struc[$dir], $role, ''),
                    'name' => '/');
            } else {
                if (!isset($files['file']) || is_array($files['file'])) {
                    if (isset($dir_roles[$_curdir . $dir])) {
                        $myrole = $dir_roles[$_curdir . $dir];
                    } else {
                        $myrole = $role;
                    }
                    $ret[$dir] = array();
                    if ($dir == '/') {
                        $ret[$dir]['baseinstalldir'] = $this->_options['baseinstalldir'];
                    }
                    $ret[$dir]['name'] = $dir;

                    $recurdir = ($_curdir == '') ? $dir . '/' : $_curdir . $dir . '/';
                    if ($recurdir == '//') {
                        $recurdir = '';
                    }
                    $ret[$dir]['##files'] = $this->_getSimpleDirTag($files, $myrole, $recurdir);
                } else {
                    $myrole = '';
                    if (!$role) {
                        $myrole = false;
                        if (isset($exceptions[$files['path']])) {
                            $myrole = $exceptions[$files['path']];
                        } elseif (isset($roles[$files['ext']])) {
                            $myrole = $roles[$files['ext']];
                        } else {
                            $myrole = $roles['*'];
                        }
                    } else {
                        $myrole = $role;
                        if (isset($exceptions[$files['path']])) {
                            $myrole = $exceptions[$files['path']];
                        }
                    }
                    $test = explode('/', $files['path']);
                    foreach ($test as $subpath) {
                        if ($subpath == 'CVS') {
                            $this->pushWarning(PEAR_PACKAGEFILEMANAGER_CVS_PACKAGED,
                                array('path' => $files['path']));
                        }
                    }
                    $ret[$files['file']] = array('role' => $myrole);
                    if (isset($installexceptions[$files['path']])) {
                        $ret[$files['file']]['baseinstalldir'] =
                            $installexceptions[$files['path']];
                    }
                    if (isset($platformexceptions[$files['path']])) {
                        $ret[$files['file']]['platform'] = $platformexceptions[$files['path']];
                    }
                    if (isset($installas[$files['path']])) {
                        $ret[$files['file']]['install-as'] = $installas[$files['path']];
                    }
                    if (isset($replacements[$files['path']])) {
                        $ret[$files['file']]['replacements'] = $replacements[$files['path']];
                    }
                    if (isset($globalreplacements)) {
                        if (!isset($ret[$files['file']]['replacements'])) {
                            $ret[$files['file']]['replacements'] = array();
                        }
                        $ret[$files['file']]['replacements'] = array_merge(
                            $ret[$files['file']]['replacements'], $globalreplacements);
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Recursively generate the <filelist> section's <dir> and <file> tags
     *
     * @param array|PEAR_Error $struc   the sorted directory structure, or an error
     *                         from filelist generation
     * @param false|string     $role    whether the parent directory has a role this should
     *                         inherit
     * @param string           $_curdir indentation level
     *
     * @return array|PEAR_Error
     * @access private
     * @since  0.1
     */
    function _getDirTag($struc, $role = false, $_curdir = '')
    {
        if (PEAR::isError($struc)) {
            return $struc;
        }
        extract($this->_options);
        $ret = array();
        foreach ($struc as $dir => $files) {
            if ($dir === '/') {
                // global directory role? overrides all exceptions except file exceptions
                if (isset($dir_roles['/'])) {
                    $role = $dir_roles['/'];
                }
                return $this->_getDirTag($struc[$dir], $role, '');
            } else {
                if (!isset($files['file']) || is_array($files['file'])) {
                    $myrole = '';
                    if (isset($dir_roles[$_curdir . $dir])) {
                        $myrole = $dir_roles[$_curdir . $dir];
                    } elseif ($role) {
                        $myrole = $role;
                    }
                    $ret = array_merge($ret, $this->_getDirTag($files, $myrole, $_curdir . $dir . '/'));
                } else {
                    $myrole = '';
                    if (!$role) {
                        $myrole = false;
                        if (isset($exceptions[$files['path']])) {
                            $myrole = $exceptions[$files['path']];
                        } elseif (isset($roles[$files['ext']])) {
                            $myrole = $roles[$files['ext']];
                        } else {
                            $myrole = $roles['*'];
                        }
                    } else {
                        $myrole = $role;
                        if (isset($exceptions[$files['path']])) {
                            $myrole = $exceptions[$files['path']];
                        }
                    }
                    if (isset($installexceptions[$files['path']])) {
                        $bi = $installexceptions[$files['path']];
                    } else {
                        $bi = $this->_options['baseinstalldir'];
                    }
                    $test = explode('/', $files['path']);
                    foreach ($test as $subpath) {
                        if ($subpath == 'CVS') {
                            $this->pushWarning(PEAR_PACKAGEFILEMANAGER_CVS_PACKAGED, array('path' => $files['path']));
                        }
                    }
                    $ret[$files['path']] =
                        array('role' => $myrole,
                              'baseinstalldir' => $bi,
                              );
                    if (!isset($this->_options['simpleoutput'])) {
                        $md5sum = @md5_file($this->_options['packagedirectory'] . $files['path']);
                        if (!empty($md5sum)) {
                            $ret[$files['path']]['md5sum'] = $md5sum;
                        }
                    } elseif (isset($ret[$files['path']]['md5sum'])) {
                        unset($ret[$files['path']]['md5sum']);
                    }
                    if (isset($platformexceptions[$files['path']])) {
                        $ret[$files['path']]['platform'] = $platformexceptions[$files['path']];
                    }
                    if (isset($installas[$files['path']])) {
                        $ret[$files['path']]['install-as'] = $installas[$files['path']];
                    }
                    if (isset($replacements[$files['path']])) {
                        $ret[$files['path']]['replacements'] = $replacements[$files['path']];
                    }
                    if (isset($globalreplacements) && is_array($globalreplacements)) {
                        if (!isset($ret[$files['path']]['replacements'])) {
                            $ret[$files['path']]['replacements'] = array();
                        }
                        $ret[$files['path']]['replacements'] = array_merge(
                            $ret[$files['path']]['replacements'], $globalreplacements);
                    }
                    if ($myrole == 'php' && !$this->_options['simpleoutput']) {
                        $this->_addProvides($this->_pear, $files['fullpath']);
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * @param array $files
     * @param array &$ret
     *
     * @return array
     * @access private
     * @since  1.3.0
     */
    function _traverseFileArray($files, &$ret)
    {
        foreach ($files as $file) {
            if (!isset($file['fullpath'])) {
                $this->_traverseFileArray($file, $ret);
            } else {
                $ret[] = $file['fullpath'];
            }
        }
    }

    /**
     * Retrieve the 'deps' option passed to the constructor
     *
     * @return array|PEAR_Error
     * @access private
     * @since  0.1
     */
    function _getDependencies()
    {
        if ($this->_detectDependencies) {
            $this->_traverseFileArray($this->_struc, $ret);
            $compatinfo = new PHP_CompatInfo();
            $info       = $compatinfo->parseArray($ret);

            $ret = $this->addDependency('php', $info['version'], 'ge', 'php', false);
            if (is_a($ret, 'PEAR_Error')) {
                return $ret;
            }
            foreach ($info['extensions'] as $ext) {
                $this->addDependency($ext, '', 'has', 'ext', false);
            }
        }
        if (isset($this->_packageXml['release_deps']) &&
              is_array($this->_packageXml['release_deps'])) {
            return $this->_packageXml['release_deps'];
        } else {
            return array();
        }
    }

    /**
     * Creates a changelog entry with the current release
     * notes and dates, or overwrites a previous creation
     *
     * @return void
     * @access private
     * @since  0.1
     */
    function _updateChangeLog()
    {
        $curlog = $oldchangelog = false;
        if (!isset($this->_packageXml['changelog'])) {
            $changelog = array();
            if (isset($this->_oldPackageXml['release_notes'])) {
                $changelog['release_notes'] = $this->_oldPackageXml['release_notes'];
            }
            if (isset($this->_oldPackageXml['version'])) {
                $changelog['version'] = $this->_oldPackageXml['version'];
            }
            if (isset($this->_oldPackageXml['release_date'])) {
                $changelog['release_date'] = $this->_oldPackageXml['release_date'];
            }
            if (isset($this->_oldPackageXml['release_license'])) {
                $changelog['release_license'] = $this->_oldPackageXml['release_license'];
            }
            if (isset($this->_oldPackageXml['release_state'])) {
                $changelog['release_state'] = $this->_oldPackageXml['release_state'];
            }
            if (count($changelog)) {
                $this->_packageXml['changelog'] = array($changelog);
            } else {
                $this->_packageXml['changelog'] = array();
            }
        } else {
            if (isset($this->_oldPackageXml['release_notes'])) {
                $oldchangelog['release_notes'] = $this->_oldPackageXml['release_notes'];
            }
            if (isset($this->_oldPackageXml['version'])) {
                $oldchangelog['version'] = $this->_oldPackageXml['version'];
            }
            if (isset($this->_oldPackageXml['release_date'])) {
                $oldchangelog['release_date'] = $this->_oldPackageXml['release_date'];
            }
            if (isset($this->_oldPackageXml['release_license'])) {
                $oldchangelog['release_license'] = $this->_oldPackageXml['release_license'];
            }
            if (isset($this->_oldPackageXml['release_state'])) {
                $oldchangelog['release_state'] = $this->_oldPackageXml['release_state'];
            }
        }
        $hasoldversion = false;
        foreach ($this->_packageXml['changelog'] as $index => $changelog) {
            if ($oldchangelog && isset($oldchangelog['version'])
                    && strnatcasecmp($oldchangelog['version'], $changelog['version']) == 0) {
                $hasoldversion = true;
            }
            if (isset($changelog['version']) && strnatcasecmp($changelog['version'], $this->_options['version']) == 0) {
                $curlog = $index;
            }
            if (isset($this->_packageXml['changelog'][$index]['release_notes'])) {
                $this->_packageXml['changelog'][$index]['release_notes'] = trim($changelog['release_notes']);
            }
            // the parsing of the release notes adds a \n for some reason
        }
        if (!$hasoldversion && $oldchangelog && count($oldchangelog)
              && $oldchangelog['version'] != $this->_options['version']) {
            $this->_packageXml['changelog'][] = $oldchangelog;
        }
        $notes = ($this->_options['changelognotes'] ?
                    $this->_options['changelognotes'] : $this->_options['notes']);
        $changelog = array('version' => $this->_options['version'],
                           'release_date' => date('Y-m-d'),
                           'release_license' => $this->_options['license'],
                           'release_state' => $this->_options['state'],
                           'release_notes' => $notes,
                           );
        if ($curlog !== false) {
            $this->_packageXml['changelog'][$curlog] = $changelog;
        } else {
            $this->_packageXml['changelog'][] = $changelog;
        }
        usort($this->_packageXml['changelog'], array($this, '_changelogsort'));
    }

    /**
     * User-defined comparison function to sort changelog array
     *
     * @return integer sort comparaison result (-1, 0, +1) of two elements $a and $b
     * @access private
     * @since  0.12
     */
    function _changelogsort($a, $b)
    {
        if ($this->_options['changelogoldtonew']) {
            $c  = strtotime($a['release_date']);
            $d  = strtotime($b['release_date']);
            $v1 = $a['version'];
            $v2 = $b['version'];
        } else {
            $d  = strtotime($a['release_date']);
            $c  = strtotime($b['release_date']);
            $v2 = $a['version'];
            $v1 = $b['version'];
        }
        if ($c - $d > 0) {
            return 1;
        } elseif ($c - $d < 0) {
            return -1;
        }
        return version_compare($v1, $v2);
    }

    /**
     * @param string $path        full path to package file
     * @param string $packagefile (optional) name of package file
     *
     * @return true|PEAR_Error
     * @uses   _generateNewPackageXML() if no package.xml is found, it
     *          calls this to create a new one
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @throws PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST
     * @access private
     * @since  0.1
     */
    function _getExistingPackageXML($path, $packagefile = 'package.xml')
    {
        if (is_string($path) && is_dir($path)) {
            $contents = false;
            if (file_exists($path . $packagefile)) {
                $contents = file_get_contents($path . $packagefile);
            }
            if (!$contents) {
                return $this->_generateNewPackageXML();
            } else {
                $PEAR_Common = $this->_options['pearcommonclass'];
                if (!class_exists($PEAR_Common)) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
                }
                $common = new $PEAR_Common;
                if (is_a($common, 'PEAR_Common')) {
                    $this->_oldPackageXml =
                    $this->_packageXml = $common->infoFromString($contents);
                } else { // new way
                    include_once 'PEAR/PackageFile.php';
                    $z = &PEAR_Config::singleton();
                    $pkg = &new PEAR_PackageFile($z);
                    $pf = &$pkg->fromXmlString($contents, PEAR_VALIDATE_DOWNLOADING, $path . $packagefile);
                    if (PEAR::isError($pf)) {
                        return $pf;
                    }
                    if ($pf->getPackagexmlVersion() != '1.0') {
                        return PEAR::raiseError('PEAR_PackageFileManager can only manage ' .
                            'package.xml version 1.0, use PEAR_PackageFileManager_v2 for newer' .
                            ' package files');
                    }
                    $this->_oldPackageXml =
                    $this->_packageXml = $pf->toArray();
                }
                if (PEAR::isError($this->_packageXml)) {
                    return $this->_packageXml;
                }
                if ($this->_options['cleardependencies']) {
                    $this->_packageXml['release_deps'] = $this->_options['deps'];
                }
                if ($this->_options['deps'] !== false) {
                    $this->_packageXml['release_deps'] = $this->_options['deps'];
                } else {
                    if (isset($this->_packageXml['release_deps'])) {
                        $this->_options['deps'] = $this->_packageXml['release_deps'];
                    }
                }
                if ($this->_options['maintainers'] !== false) {
                    $this->_packageXml['maintainers'] = $this->_options['maintainers'];
                } else {
                    $this->_options['maintainers'] = $this->_packageXml['maintainers'];
                }
                unset($this->_packageXml['filelist']);
                unset($this->_packageXml['provides']);
            }
            return true;
        } else {
            if (!is_string($path)) {
                $path = gettype($path);
            }
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST,
                $path);
        }
    }

    /**
     * Create the structure for a new package.xml
     *
     * @uses   $_packageXml emulates reading in a package.xml
     *           by using the package, summary and description
     *           options
     * @return true|PEAR_Error
     * @access private
     * @since  0.9
     */
    function _generateNewPackageXML()
    {
        $this->_oldPackageXml = false;
        if (!isset($this->_options['package']) || empty($this->_options['package'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOPACKAGE);
        }
        if (!isset($this->_options['summary']) || empty($this->_options['summary'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOSUMMARY);
        }
        if (!isset($this->_options['description']) || empty($this->_options['description'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NODESC);
        }
        $this->_packageXml                = array();
        $this->_packageXml['package']     = $this->_options['package'];
        $this->_packageXml['summary']     = $this->_options['summary'];
        $this->_packageXml['description'] = $this->_options['description'];
        $this->_packageXml['changelog']   = array();
        if ($this->_options['deps'] !== false) {
            $this->_packageXml['release_deps'] = $this->_options['deps'];
        } else {
            $this->_packageXml['release_deps'] = $this->_options['deps'] = array();
        }
        if ($this->_options['maintainers'] !== false) {
            $this->_packageXml['maintainers'] = $this->_options['maintainers'];
        } else {
            $this->_packageXml['maintainers'] = $this->_options['maintainers'] = array();
        }
        return true;
    }
}
?>