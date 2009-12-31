<?php
/**
  +----------------------------------------------------------------------+
  | PHP Version 4                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2007 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available at through the world-wide-web at                           |
  | http://www.php.net/license/2_02.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Christian Dickmann <dickmann@php.net>                        |
  |         Tias Guns <tias@ulyssis.org>                                 |
  +----------------------------------------------------------------------+

 * @category   pear
 * @package    PEAR_Frontend_Web
 * @author     Christian Dickmann <dickmann@php.net>
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @version    CVS: $Id: Web.php,v 1.79 2007/06/26 21:50:46 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.1
 */

/**
 * base class
 */
require_once "PEAR/Frontend.php";
require_once "PEAR/Remote.php";
require_once "HTML/Template/IT.php";

/**
 * PEAR_Frontend_Web is a HTML based Webfrontend for the PEAR Installer
 *
 * The Webfrontend provides basic functionality of the Installer, such as
 * a package list grouped by categories, a search mask, the possibility
 * to install/upgrade/uninstall packages and some minor things.
 * PEAR_Frontend_Web makes use of the PEAR::HTML_IT Template engine which
 * provides the possibillity to skin the Installer.
 *
 * @category   pear
 * @package    PEAR_Frontend_Web
 * @author     Christian Dickmann <dickmann@php.net>
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @version    CVS: $Id: Web.php,v 1.79 2007/06/26 21:50:46 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.1
 */
class PEAR_Frontend_Web extends PEAR_Frontend
{
    // {{{ properties

    /**
     * What type of user interface this frontend is for.
     * @var string
     * @access public
     */
    var $type = 'Web';

    /**
     * Container, where values can be saved temporary
     * @var array
     */
    var $_data = array();

    /**
     * Used to save output, to display it later
     */
    var $_savedOutput = array();

    /**
     * The config object
     */
    var $config;

    /**
     * List of packages that will not be deletable thourgh the webinterface
     */
    var $_no_delete_pkgs = array(
        'pear.php.net/Archive_Tar',
        'pear.php.net/Console_Getopt',
        'pear.php.net/HTML_Template_IT',
        'pear.php.net/PEAR',
        'pear.php.net/PEAR_Frontend_Web',
        'pear.php.net/Structures_Graph',
    );

    /**
     * List of channels that will not be deletable thourgh the webinterface
     */
    var $_no_delete_chans = array(
        'pear.php.net',
        '__uri',
        );

    /**
     * How many categories to display on one 'list-all' page
     */
    var $_paging_cats = 4;

    /**
     * Flag to determine whether to treat all output as information from a post-install script
     * @var bool
     */
    var $_installScript = false;

    // }}}
    // {{{ constructor

    function PEAR_Frontend_Web()
    {
        parent::PEAR();
        $this->config = &$GLOBALS['_PEAR_Frontend_Web_config'];
    }

    function setConfig(&$config)
    {
        $this->config = &$config;
    }

    // }}}
    // {{{ _initTemplate()

    /**
     * Initialize a TemplateObject
     *
     * @param string  $file     filename of the template file
     *
     * @return object Object of HTML/IT - Template - Class
     */
    function _initTemplate($file)
    {
        // Errors here can not be displayed using the UI
        PEAR::staticPushErrorHandling(PEAR_ERROR_PRINT);

        $tpl = new HTML_Template_IT($this->config->get('data_dir').'/PEAR_Frontend_Web/data/templates');
        $tpl->loadTemplateFile($file);
        $tpl->setVariable("InstallerURL", $_SERVER["PHP_SELF"]);

        PEAR::staticPopErrorHandling(); // reset error handling
        return $tpl;
    }

    // }}}
    // {{{ displayError()

    /**
     * Display an error page
     *
     * @param mixed   $eobj  PEAR_Error object or string containing the error message
     * @param string  $title (optional) title of the page
     * @param string  $img   (optional) iconhandle for this page
     * @param boolean $popup (optional) popuperror or normal?
     *
     * @access public
     *
     * @return null does not return anything, but exit the script
     */
    function displayError($eobj, $title = 'Error', $img = 'error', $popup = false)
    {
        $msg = '';
        if (PEAR::isError($eobj)) {
            $msg .= trim($eobj->getMessage());
        } else {
            $msg .= trim($eobj);
        }

        $msg = nl2br($msg."\n");

        $tplfile = ($popup ? "error.popup.tpl.html" : "error.tpl.html");
        $tpl = $this->_initTemplate($tplfile);

        $tpl->setVariable("Error", $msg);
        $command_map = array(
            "install"   => "list",
            "uninstall" => "list",
            "upgrade"   => "list",
            );
        if (isset($_GET['command'])) {
            if (isset($command_map[$_GET['command']])) {
                $_GET['command'] = $command_map[$_GET['command']];
            }
            $tpl->setVariable("param", '?command='.$_GET['command']);
        }

        $tpl->show();
        exit;
    }

    // }}}
    // {{{ displayFatalError()

    /**
     * Alias for PEAR_Frontend_Web::displayError()
     *
     * @see PEAR_Frontend_Web::displayError()
     */
    function displayFatalError($eobj, $title = 'Error', $img = 'error')
    {
        $this->displayError($eobj, $title, $img);
    }

    // }}}
    // {{{ _outputListChannels()

    /**
     * Output the list of channels
     */
    function _outputListChannels($data)
    {
        $tpl = $this->_initTemplate('channel.list.tpl.html');

        $tpl->setVariable("Caption", $data['caption']);

        if (!isset($data['data'])) {
            $data['data'] = array();
        }

        $reg = &$this->config->getRegistry();
        foreach($data['data'] as $row) {
            list($channel, $summary) = $row;
            $url = sprintf('%s?command=channel-info&chan=%s',
                    $_SERVER['PHP_SELF'], urlencode($channel));
            $channel_info = sprintf('<a href="%s" class="blue">%s</a>', $url, $channel);

            // detect whether any packages from this channel are installed
            $anyinstalled = $reg->listPackages($channel);
            $id = 'id="'.$channel.'_href"';
            if (in_array($channel, $this->_no_delete_chans) || (is_array($anyinstalled) && count($anyinstalled))) {
                // dont delete
                $del = '&nbsp;';
            } else {
                $img = '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="delete">';
                $url = sprintf('%s?command=channel-delete&chan=%s',
                    $_SERVER["PHP_SELF"], urlencode($channel));
                $del = sprintf('<a href="%s" onClick="return deleteChan(\'%s\');" %s >%s</a>',
                    $url, $channel, $id, $img);
            }

            $tpl->setCurrentBlock("Row");
            $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');
            $tpl->setVariable("UpdateChannelsURL", $_SERVER['PHP_SELF']);
            $tpl->setVariable("Delete", $del);
            $tpl->setVariable("Channel", $channel_info);
            $tpl->setVariable("Summary", nl2br($summary));
            $tpl->parseCurrentBlock();
        }
        $tpl->show();
        return true;
    }

    // }}}
    // {{{ _outputListAll()

    /**
     * Output a list of packages, grouped by categories. Uses Paging
     *
     * @param array   $data     array containing all data to display the list
     * @param boolean $paging   (optional) use Paging or not
     *
     * @return boolean true (yep. i am an optimist)
     *
     * DEPRECATED BY list-categories
     */
    function _outputListAll($data, $paging=true)
    {
        if (!isset($data['data'])) {
            return true;
        }

        $tpl = $this->_initTemplate('package.list.tpl.html');
        $tpl->setVariable('Caption', $data['caption']);

        if (!is_array($data['data'])) {
            $tpl->show();
            print('<p><table><tr><td width="50">&nbsp;</td><td>'.$data['data'].'</td></tr></table></p>');
            return true;
        }

        $command = isset($_GET['command']) ? $_GET['command']:'list-all';
        $mode = isset($_GET['mode'])?$_GET['mode']:'';
        $links = array('back' => '',
                       'next' => '',
                       'current' => '&mode='.$mode,
                       );

        if ($paging) {
            // Generate Linkinformation to redirect to _this_ page after performing an action
            $link_str = '<a href="?command=%s&from=%s&mode=%s" class="paging_link">%s</a>';

            $pageId = isset($_GET['from']) ? $_GET['from'] : 0;
            $paging_data = $this->__getData($pageId, $this->_paging_cats, count($data['data']), false);
            $data['data'] = array_slice($data['data'], $pageId, $this->_paging_cats);
            $from = $paging_data['from'];
            $to = $paging_data['to'];

            if ($paging_data['from']>1) {
                $links['back'] = sprintf($link_str, $command, $paging_data['prev'], $mode, '&lt;&lt;');
            }

            if ( $paging_data['next']) {
                $links['next'] = sprintf($link_str, $command, $paging_data['next'], $mode, '&gt;&gt;');
            }

            $links['current'] = '&from=' . $paging_data['from'];

            $blocks = array('Paging_pre', 'Paging_post');
            foreach ($blocks as $block) {
                $tpl->setCurrentBlock($block);
                $tpl->setVariable('Prev', $links['back']);
                $tpl->setVariable('Next', $links['next']);
                $tpl->setVariable('PagerFrom', $from);
                $tpl->setVariable('PagerTo', $to);
                $tpl->setVariable('PagerCount', $paging_data['numrows']);
                $tpl->parseCurrentBlock();
            }
        }

        $reg = &$this->config->getRegistry();
        foreach($data['data'] as $category => $packages) {
            foreach($packages as $row) {
                list($pkgChannel, $pkgName, $pkgVersionLatest, $pkgVersionInstalled, $pkgSummary) = $row;
                $parsed = $reg->parsePackageName($pkgName, $pkgChannel);
                $pkgChannel = $parsed['channel'];
                $pkgName = $parsed['package'];
                $pkgFull = sprintf('%s/%s-%s',
                            $pkgChannel,
                            $pkgName,
                            substr($pkgVersionLatest, 0, strpos($pkgVersionLatest, ' ')));
                $tpl->setCurrentBlock("Row");
                $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');
                $images = array(
                    'install' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="install">',
                    'uninstall' => '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="uninstall">',
                    'upgrade' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="upgrade">',
                    'info' => '<img src="'.$_SERVER["PHP_SELF"].'?img=info"  width="17" height="19" border="0" alt="info">',
                    'infoExt' => '<img src="'.$_SERVER["PHP_SELF"].'?img=infoplus"  width="18" height="19" border="0" alt="extended info">',
                    );
                $urls   = array(
                    'install' => sprintf('%s?command=install&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgFull, $links['current']),
                    'uninstall' => sprintf('%s?command=uninstall&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgFull, $links['current']),
                    'upgrade' => sprintf('%s?command=upgrade&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgFull, $links['current']),
                    'info' => sprintf('%s?command=info&pkg=%s',
                        $_SERVER["PHP_SELF"], $pkgFull),
                    'remote-info' => sprintf('%s?command=remote-info&pkg=%s',
                        $_SERVER["PHP_SELF"], $pkgFull),
                    'infoExt' => 'http://' . $this->config->get('preferred_mirror')
                         . '/package/' . $row[0],
                    );

                $compare = version_compare($pkgVersionLatest, $pkgVersionInstalled);
                $id = 'id="'.$pkgName.'_href"';
                if (!$pkgVersionInstalled || $pkgVersionInstalled == "- no -") {
                    $inst = sprintf('<a href="%s" onClick="return installPkg(\'%s\');" %s>%s</a>',
                        $urls['install'], $pkgName, $id, $images['install']);
                    $del = '';
                    $info = sprintf('<a href="%s">%s</a>', $urls['remote-info'],    $images['info']);
                } else if ($compare == 1) {
                    $inst = sprintf('<a href="%s" onClick="return installPkg(\'%s\');" %s>%s</a>',
                        $urls['upgrade'], $pkgName, $id, $images['upgrade']);
                    $del = sprintf('<a href="%s" onClick="return uninstallPkg(\'%s\');" %s >%s</a>',
                        $urls['uninstall'], $pkgName, $id, $images['uninstall']);
                    $info = sprintf('<a href="%s">%s</a>', $urls['info'],    $images['info']);
                } else {
                    $inst = '';
                    $del = sprintf('<a href="%s" onClick="return uninstallPkg(\'%s\');" %s >%s</a>',
                        $urls['uninstall'], $pkgName, $id, $images['uninstall']);
                    $info = sprintf('<a href="%s">%s</a>', $urls['info'],    $images['info']);
                }
                $infoExt = sprintf('<a href="%s">%s</a>', $urls['infoExt'], $images['infoExt']);

                if (in_array($pkgChannel.'/'.$pkgName, $this->_no_delete_pkgs)) {
                    $del = '';
                }

                $tpl->setVariable("Latest", $pkgVersionLatest);
                $tpl->setVariable("Installed", $pkgVersionInstalled);
                $tpl->setVariable("Install", $inst);
                $tpl->setVariable("Delete", $del);
                $tpl->setVariable("Info", $info);
                $tpl->setVariable("InfoExt", $infoExt);
                $tpl->setVariable("Package", $pkgName);
                $tpl->setVariable("Channel", $pkgChannel);
                $tpl->setVariable("Summary", nl2br($pkgSummary));
                $tpl->parseCurrentBlock();
            }
            $tpl->setCurrentBlock("Category");
            $tpl->setVariable("categoryName", $category);
            $tpl->setVariable("ImgCategory", $_SERVER["PHP_SELF"].'?img=category');
            $tpl->parseCurrentBlock();
        }
        $tpl->show();

        return true;
    }

    // }}}
    // {{{ _outputListFiles()

    /**
     * Output a list of files of a packagename
     *
     * @param array $data array containing all files of a package
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListFiles($data)
    {
        sort($data['data']);
        return $this->_outputGenericTableVertical($data['caption'], $data['data']);
    }

    // }}}
    // {{{ _outputListDocs()

    /**
     * Output a list of documentation files of a packagename
     *
     * @param array $data array containing all documentation files of a package
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListDocs($data)
    {
        $tpl = $this->_initTemplate('caption.tpl.html');
        $tpl->setVariable('Caption', $data['caption']);
        $tpl->show();

        if (is_array($data['data'])) {
            print $this->_getListDocsDiv($data['channel'].'/'.$data['package'], $data['data']);
        } else {
            print $data['data'];
        }
        return true;
    }

    /**
     * Get list of the docs of a package in a HTML div
     *
     * @param string $pkg full package name (channel/package)
     * @param array $files array of all files and there location
     * @return string HTML
     */
    function _getListDocsDiv($pkg, $files) {
        $out = '<div id="listdocs"><ul>';
        foreach($files as $name => $location) {
            $out .= sprintf('<li><a href="%s?command=doc-show&pkg=%s&file=%s" title="%s">%s</a></li>',
                    $_SERVER['PHP_SELF'],
                    $pkg,
                    urlencode($name),
                    $location,
                    $name);
        }
        $out .= '</ul></div>';

        return $out;
    }
        
    // }}}
    // {{{ _outputDocShow()

    /**
     * Output a documentation file of a packagename
     *
     * @param array $data array containing all documentation files of a packages
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputDocShow($data)
    {
        $tpl = $this->_initTemplate('caption.tpl.html');
        $tpl->setVariable('Caption', $data['caption']);
        $tpl->show();

        print '<div id="docshow">'.nl2br(htmlentities($data['data'])).'</div>';
        return true;
    }

    // }}}
    // {{{ _outputListPackages()

    /**
     * Output packagenames (of a channel or category)
     *
     * @param array $data array containing all information about the packages
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListPackages($data)
    {
        $ROWSPAN=3;

        $caption = sprintf('<a name="%s"><img src="%s?img=category" /> %s (%s)</a>',
                            $data['channel'],
                            $_SERVER['PHP_SELF'],
                            $data['caption'],
                            count($data['data']));

        $newdata = null;
        if (!is_array($data['data'])) {
            $newdata = $data['data'];
        } else {
            $newdata = array(0 => array());
            $row = 0;
            $col = 0;
            $rows = ceil(count($data['data'])/$ROWSPAN);
            foreach ($data['data'] as $package) {
                if ($row == $rows) { // row is full
                    $row = 0;
                    $col++;
                }
                if ($col == 0) { // create clean arrays
                    $newdata[$row] = array();
                }
                $newdata[$row][$col] = sprintf('<img src="%s?img=package" /> <a href="%s?command=info&pkg=%s/%s" class="blue">%s</a>',
                                $_SERVER['PHP_SELF'],
                                $_SERVER['PHP_SELF'],
                                $package[0],
                                $package[1],
                                $package[1]);
                $row++;
            }
            while ($row != $rows) {
                $newdata[$row][$col] = '&nbsp;';
                $row++;
            }
        }
        
        return $this->_outputGenericTableHorizontal($caption, $newdata);
    }

    // }}}
    // {{{ _outputListCategories()

    /**
     * Prepare output per channel/category
     *
     * @param array   $data     array containing caption, channel and headline
     *
     * @return $tpl Template Object
     */
    function _prepareListCategories($data)
    {
        $channel = $data['channel'];

        if (!is_array($data['data']) && $channel == '__uri') {
            // no categories in __uri, don't show this ugly duck !
            return true;
        }

        $tpl = $this->_initTemplate('categories.list.tpl.html');

        $tpl->setVariable('categoryName', $data['caption']);
        $tpl->setVariable('channel', $data['channel']);

        // set headlines
        if (isset($data['headline']) && is_array($data['headline'])) {
            foreach($data['headline'] as $text) {
                $tpl->setCurrentBlock('Headline');
                $tpl->setVariable('Text', $text);
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setCurrentBlock('Headline');
            $tpl->setVariable('Text', $data['data']);
            $tpl->parseCurrentBlock();
            unset($data['data']); //clear
        }

        // set extra title info
        $tpl->setCurrentBlock('Title_info');
        $info = sprintf('<a href="%s?command=list-packages&chan=%s" class="green">List all packagenames of this channel.</a>',
                        $_SERVER['PHP_SELF'],
                        $channel
                            );
        $tpl->setVariable('Text', $info);
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('Title_info');
        $info = sprintf('<a href="%s?command=list-categories&chan=%s&opt=packages" class="green">List all packagenames, by category, of this channel.</a>',
                        $_SERVER['PHP_SELF'],
                        $channel
                            );
        $tpl->setVariable('Text', $info);
        $tpl->parseCurrentBlock();

        return $tpl;
    }

    /**
     * Output the list of categories of a channel
     *
     * @param array   $data     array containing all data to display the list
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListCategories($data)
    {
        $tpl = $this->_prepareListCategories($data);

        if (isset($data['data']) && is_array($data['data'])) {
            foreach($data['data'] as $row) {
                @list($channel, $category, $packages) = $row;

                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $channel);
                $tpl->parseCurrentBlock();

                $tpl->setCurrentBlock('Data_row');
                $info = sprintf('<a href="%s?command=list-category&chan=%s&cat=%s" class="green">%s</a>',
                            $_SERVER['PHP_SELF'],
                            $channel,
                            $category,
                            $category
                                );
                $tpl->setVariable('Text', $info);
                $tpl->parseCurrentBlock();

                if (is_array($packages)) {
                    if (count($packages) == 0) {
                        $info = '<i>(no packages registered)</i>';
                    } else {
                        $info = sprintf('<img src="%s?img=package" />: ',
                                $_SERVER['PHP_SELF']);
                    }
                    foreach($packages as $i => $package) {
                        $info .= sprintf('<a href="%s?command=info&pkg=%s/%s" class="blue">%s</a>',
                                    $_SERVER['PHP_SELF'],
                                    $channel,
                                    $package,
                                    $package
                                        );

                        if ($i+1 != count($packages)) {
                            $info .= ', ';
                        }
                    }
                    $tpl->setCurrentBlock('Data_row');
                    $tpl->setVariable('Text', $info);
                    $tpl->parseCurrentBlock();
                }

                $tpl->setCurrentBlock('Data');
                $tpl->setVariable('Img', 'category');
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();

        return true;
    }

    /**
     * Output the list of packages of a category of a channel
     *
     * @param array   $data     array containing all data to display the list
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListCategory($data)
    {
        if (isset($data['headline'])) {
            // create place for install/uninstall icon:
            $summary = array_pop($data['headline']);
            $data['headline'][] = '&nbsp;'; // icon
            $data['headline'][] = $summary; // restore summary
        }
        $tpl = $this->_prepareListCategories($data);
        $channel = $data['channel'];

        if (isset($data['data']) && is_array($data['data'])) {
            foreach($data['data'] as $row) {
                // output summary after install icon
                $summary = array_pop($row);

                foreach ($row as $i => $col) {
                    if ($i == 1) {
                        $package = $col;
                        // package name, make URL
                        $col = $this->_prepPkgName($package, $channel);
                    }
                    
                    $tpl->setCurrentBlock('Data_row');
                    $tpl->setVariable('Text', $col);
                    $tpl->parseCurrentBlock();
                }

                // install or uninstall icon
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $this->_prepIcons($package, $channel));
                $tpl->parseCurrentBlock();

                // now the summary
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $summary);
                $tpl->parseCurrentBlock();

                // and finish.
                $tpl->setCurrentBlock('Data');
                $tpl->setVariable('Img', 'package');
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();

        return true;
    }

    // }}}
    // {{{ _outputList()

    /**
     * Output the list of installed packages.
     *
     * @param array   $data     array containing all data to display the list
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputList($data)
    {
        $channel = $data['channel'];

        if (!is_array($data['data']) && $channel == '__uri') {
            // no packages in __uri, don't show this ugly duck !
            return true;
        }

        $tpl = $this->_initTemplate('package.list_nocat.tpl.html');

        $tpl->setVariable('categoryName', $data['caption']);
        //$tpl->setVariable('Border', $data['border']);

        // set headlines
        if (isset($data['headline']) && is_array($data['headline'])) {
            // overwrite
            $data['headline'] = array('Channel', 'Package', 'Local', '&nbsp;', 'Summary');
            foreach($data['headline'] as $text) {
                $tpl->setCurrentBlock('Headline');
                $tpl->setVariable('Text', $text);
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setCurrentBlock('Headline');
            $tpl->setVariable('Text', $data['data']);
            $tpl->parseCurrentBlock();
            unset($data['data']); //clear
        }

        if (isset($data['data']) && is_array($data['data'])) {
            foreach($data['data'] as $row) {
                $package = $row[0].'/'.$row[1];
                $package_name = $row[1];
                $local = sprintf('%s (%s)', $row[2], $row[3]);

                // Channel
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $channel);
                $tpl->parseCurrentBlock();

                // Package
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $this->_prepPkgName($package_name, $channel));
                $tpl->parseCurrentBlock();

                // Local
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $local);
                $tpl->parseCurrentBlock();

                // Icons (uninstall)
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Text', $this->_prepIcons($package_name, $channel, true));
                $tpl->parseCurrentBlock();

                // Summary
                $tpl->setCurrentBlock('Data_row');
                $reg = $this->config->getRegistry();
                $tpl->setVariable('Text', $reg->packageInfo($package_name, 'summary', $channel));
                $tpl->parseCurrentBlock();

                // and finish.
                $tpl->setCurrentBlock('Data');
                $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();

        return true;
    }

    // }}}
    // {{{ _outputListUpgrades()

    /**
     * Output the list of available upgrades packages.
     *
     * @param array   $data     array containing all data to display the list
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputListUpgrades($data)
    {
        $tpl = $this->_initTemplate('package.list_nocat.tpl.html');

        $tpl->setVariable('categoryName', $data['caption']);
        //$tpl->setVariable('Border', $data['border']);

        
        $channel = $data['channel'];

        // set headlines
        if (isset($data['headline']) && is_array($data['headline'])) {
            $data['headline'][] = '&nbsp;';
            foreach($data['headline'] as $text) {
                $tpl->setCurrentBlock('Headline');
                $tpl->setVariable('Text', $text);
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setCurrentBlock('Headline');
            $tpl->setVariable('Text', $data['data']);
            $tpl->parseCurrentBlock();
            unset($data['data']); //clear
        }

        if (isset($data['data']) && is_array($data['data'])) {
            foreach($data['data'] as $row) {
                $package = $channel.'/'.$row[1];
                $package_name = $row[1];

                foreach($row as $i => $text) {
                    if ($i == 1) {
                        // package name
                        $text = $this->_prepPkgName($text, $channel);
                    }
                    $tpl->setCurrentBlock('Data_row');
                    $tpl->setVariable('Text', $text);
                    $tpl->parseCurrentBlock();
                }
                // upgrade link
                $tpl->setCurrentBlock('Data_row');
                $img = sprintf('<img src="%s?img=install" width="18" height="17"  border="0" alt="upgrade">', $_SERVER["PHP_SELF"]);
                $url = sprintf('%s?command=upgrade&pkg=%s', $_SERVER["PHP_SELF"], $package);
                $text = sprintf('<a href="%s" onClick="return installPkg(\'%s\');" id="%s">%s</a>', $url, $package, $package.'_href', $img);
                $tpl->setVariable('Text', $text);
                $tpl->parseCurrentBlock();

                // and finish.
                $tpl->setCurrentBlock('Data');
                $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');

                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();

        return true;
    }

    // }}}

    function _getPackageDeps($deps)
    {
        if (count($deps) == 0) {
            return "<i>No dependencies registered.</i>\n";
        } else {
            $rel_trans = array(
                'lt' => 'older than %s',
                'le' => 'version %s or older',
                'eq' => 'version %s',
                'ne' => 'any version but %s',
                'gt' => 'newer than %s',
                'ge' => '%s or newer',
                );
            $dep_type_desc = array(
                'pkg'    => 'PEAR Package',
                'ext'    => 'PHP Extension',
                'php'    => 'PHP Version',
                'prog'   => 'Program',
                'ldlib'  => 'Development Library',
                'rtlib'  => 'Runtime Library',
                'os'     => 'Operating System',
                'websrv' => 'Web Server',
                'sapi'   => 'SAPI Backend',
                );
            $result = "      <dl>\n";
            foreach($deps as $row) {

                // Print link if it's a PEAR package
                if ($row['type'] == 'pkg') {
                    $package = $row['channel'].'/'.$row['name'];
                    $row['name'] = sprintf('<a class="green" href="%s?command=remote-info&pkg=%s">%s</a>',
                        $_SERVER['PHP_SELF'], $package, $package);
                }

                if (isset($rel_trans[$row['rel']])) {
                    $rel = sprintf($rel_trans[$row['rel']], $row['version']);
                    $optional = isset($row['optional']) && $row['optional'] == 'yes';
                    $result .= sprintf("%s: %s %s" . $optional,
                           $dep_type_desc[$row['type']], @$row['name'], $rel);
                } else {
                    $result .= sprintf("%s: %s", $dep_type_desc[$row['type']], $row['name']);
                }
                $result .= '<br>';
            }
            $result .= "      </dl>\n";
        }
        return $result;
    }

    /**
     * Output details of one package, info (local)
     *
     * @param array $data array containing all information about the package
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputPackageInfo($data)
    {
        $data['data'] = $this->htmlentities_recursive($data['data']);
        if (!isset($data['raw']['channel'])) {
            // package1.xml, channel by default pear
            $channel = 'pear.php.net';
            $package_name = $data['raw']['package'];
        } else {
            $channel = $data['raw']['channel'];
            $package_name = $data['raw']['name'];
        }
        $package = $channel.'/'.$package_name;

        // parse extra options
        if (!in_array($package, $this->_no_delete_pkgs)) {
            $image = sprintf('<img src="%s?img=uninstall" width="18" height="17"  border="0" alt="uninstall">', $_SERVER["PHP_SELF"]);
            $output = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s" class="green" %s>%s Uninstall package</a>',
                    $_SERVER["PHP_SELF"],
                    $package,
                    'onClick="return uninstallPkg(\''.$package.'\');"',
                    $image);
            $data['data'][] = array('Options', $output);
        }

        $output = '';
        // More: Local Documentation
        require_once('PEAR/Frontend/Web/Docviewer.php');
        if (count(PEAR_Frontend_Web_Docviewer::getDocFiles($package_name, $channel)) !== 0) {
            $image = sprintf('<img src="%s?img=manual" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                    '<a href="%s?command=list-docs&pkg=%s" class="green">%s Package Documentation</a>',
                    $_SERVER["PHP_SELF"],
                    $package,
                    $image);
            $output .= '<br />';
        }
        $output .= sprintf(
                    '<a href="%s?command=list-files&pkg=%s" class="green">./.. List Files</a>',
                    $_SERVER["PHP_SELF"],
                    $package);
        $output .= '<br />';
        // More: Extended Package Information
        $image = sprintf('<img src="%s?img=infoplus" border="0" alt="extra info">', $_SERVER["PHP_SELF"]);
        if ($channel == 'pear.php.net' || $channel == 'pecl.php.net') {
            $url = 'http://%s/package/%s/download/%s';
        } else {
            // the normal default
            $url = 'http://%s/index.php?package=%s&release=%s';
        }
        $output .= sprintf(
                    '<a href="'.$url.'" class="green" target="_new">%s Extended Package Information</a>',
                    $this->config->get('preferred_mirror', null, $channel),
                    $package_name,
                    $data['raw']['version']['release'],
                    $image);
        // More: Developer Documentation && Package Manual
        if ($channel == 'pear.php.net') {
            $output .= '<br />';
            $image = sprintf('<img src="%s?img=manualplus" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                        '<a href="http://pear.php.net/package/%s/docs/latest" class="green" target="_new">%s pear.php.net Developer Documentation</a>',
                        $package_name,
                        $image);
            $output .= '<br />';
            $image = sprintf('<img src="%s?img=manualplus" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                        '<a href="http://pear.php.net/manual/en/" class="green" target="_new">%s pear.php.net Package Manual </a>',
                        $image);
        }
        $data['data'][] = array('More', $output);

        return $this->_outputGenericTableVertical($data['caption'], $data['data']);
    }

    /**
     * Output details of one package, remote-info
     *
     * @param array $data array containing all information about the package
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputPackageRemoteInfo($data)
    {
        include_once "PEAR/Downloader.php";
        $tpl = $this->_initTemplate('package.info.tpl.html');

        $tpl->setVariable("PreferredMirror", $this->config->get('preferred_mirror'));
        /*
        $dl = &new PEAR_Downloader($this, array(), $this->config);
        // don't call private functions
        // gives error, not gonna fix, but gonna skip
        $info = $dl->_getPackageDownloadUrl(array('package' => $data['name'],
            'channel' => $this->config->get('default_channel'), 'version' => $data['stable']));
        if (isset($info['url'])) {
            $tpl->setVariable("DownloadURL", $info['url']);
        } else {
            $tpl->setVariable("DownloadURL", $_SERVER['PHP_SELF']);
        }
        */
        $channel = $data['channel'];
        $package = $data['channel'].'/'.$data['name'];
        $package_full = $data['channel'].'/'.$data['name'].'-'.$data['stable'];

        $tpl->setVariable("Latest", $data['stable']);
        $tpl->setVariable("Installed", $data['installed']);
        $tpl->setVariable("Package", $data['name']);
        $tpl->setVariable("License", $data['license']);
        $tpl->setVariable("Category", $data['category']);
        $tpl->setVariable("Summary", nl2br($data['summary']));
        $tpl->setVariable("Description", nl2br($data['description']));
        $deps = @$data['releases'][$data['stable']]['deps'];
        $tpl->setVariable("Dependencies", $this->_getPackageDeps($deps));

        $compare = version_compare($data['stable'], $data['installed']);

        $images = array(
            'install' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="install">',
            'uninstall' => '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="uninstall">',
            'upgrade' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="upgrade">',
            );

        $opt_img = array();
        $opt_text = array();
        if (!$data['installed'] || $data['installed'] == "- no -") {
            $opt_img[] = sprintf(
                '<a href="%s?command=install&pkg=%s" %s>%s</a>',
                $_SERVER["PHP_SELF"], $package_full,
                'onClick="return installPkg(\''.$package_full.'\');"',
                $images['install']);
            $opt_text[] = sprintf(
                '<a href="%s?command=install&pkg=%s" class="green" %s>Install package</a>',
                $_SERVER["PHP_SELF"], $package_full,
                'onClick="return installPkg(\''.$package_full.'\');"');
        } else if ($compare == 1) {
            $opt_img[] = sprintf(
                '<a href="%s?command=upgrade&pkg=%s" %s>%s</a><br>',
                $_SERVER["PHP_SELF"], $package,
                'onClick="return installPkg(\''.$package.'\');"',
                $images['install']);
            $opt_text[] = sprintf(
                '<a href="%s?command=upgrade&pkg=%s" class="green" %s>Upgrade package</a>',
                $_SERVER["PHP_SELF"], $package,
                'onClick="return installPkg(\''.$package.'\');"');
            if (!in_array($package, $this->_no_delete_pkgs)) {
                $opt_img[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s" %s>%s</a>',
                    $_SERVER["PHP_SELF"], $package,
                    'onClick="return uninstallPkg(\''.$package.'\');"',
                    $images['uninstall']);
                $opt_text[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s" class="green" %s>Uninstall package</a>',
                    $_SERVER["PHP_SELF"], $package,
                    'onClick="return uninstallPkg(\''.$package.'\');"');
           }
        } else {
            if (!in_array($package, $this->_no_delete_pkgs)) {
                $opt_img[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s" %s>%s</a>',
                    $_SERVER["PHP_SELF"], $package,
                    'onClick="return uninstallPkg(\''.$package.'\');"',
                    $images['uninstall']);
                $opt_text[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s" class="green" %s>Uninstall package</a>',
                    $_SERVER["PHP_SELF"], $package,
                    'onClick="return uninstallPkg(\''.$package.'\');"');
           }
        }

        if (isset($opt_img[0]))
        {
            $tpl->setVariable('Opt_Img_1', $opt_img[0]);
            $tpl->setVariable('Opt_Text_1', $opt_text[0]);
        }
        if (isset($opt_img[1]))
        {
            $tpl->setVariable('Opt_Img_2', $opt_img[1]);
            $tpl->setVariable('Opt_Text_2', $opt_text[1]);
        }

        $tpl->setVariable('More_Title', 'More');
        // More: Extended Package Information
        $image = sprintf('<img src="%s?img=infoplus" border="0" alt="extra info">', $_SERVER["PHP_SELF"]);
        if ($channel == 'pear.php.net' || $channel == 'pecl.php.net') {
            $url = 'http://%s/package/%s/download/%s';
        } else {
            // the normal default
            $url = 'http://%s/index.php?package=%s&release=%s';
        }
        $output = sprintf(
                    '<a href="'.$url.'" class="green" target="_new">%s Extended Package Information</a>',
                    $this->config->get('preferred_mirror', null, $channel),
                    $data['name'],
                    $data['stable'],
                    $image);
        // More: Developer Documentation && Package Manual
        if ($channel == 'pear.php.net') {
            $output .= '<br />';
            $image = sprintf('<img src="%s?img=manualplus" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                        '<a href="http://pear.php.net/package/%s/docs/latest" class="green" target="_new">%s pear.php.net Developer Documentation</a>',
                        $data['name'],
                        $image);
            $output .= '<br />';
            $image = sprintf('<img src="%s?img=manualplus" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                        '<a href="http://pear.php.net/manual/en/" class="green" target="_new">%s pear.php.net Package Manual </a>',
                        $image);
        }
        $tpl->setVariable('More_Data', $output);

        $tpl->show();
        return true;
    }

    /**
     * Output given data in a horizontal generic table:
     * table headers in the top row.
     * Possibly prepend caption
     *
     * @var string $caption possible caption for table
     * @var array $data array of data items
     * @return true optimist etc
     */
    function _outputGenericTableHorizontal($caption, $data) {
        $tpl = $this->_initTemplate('generic_table_horizontal.tpl.html');
        
        if (!is_null($caption) && $caption != '') {
            $tpl->setVariable('Caption', $caption);
        }

        if (!is_array($data)) {
            $tpl->setCurrentBlock('Data_row');
            $tpl->setVariable('Text', nl2br($data));
            $tpl->parseCurrentBlock();
        } else {
            foreach ($data as $row) {
                foreach ($row as $col) {
                    $tpl->setCurrentBlock('Row_item');
                    $tpl->setVariable('Text', nl2br($col));
                    $tpl->parseCurrentBlock();
                }
                $tpl->setCurrentBlock('Data_row');
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();
        return true;
    }

    /**
     * Output given data in a vertical generic table:
     * table headers in the left column.
     * Possibly prepend caption
     *
     * @var string $caption possible caption for table
     * @var array $data array of data items
     * @return true optimist etc
     */
    function _outputGenericTableVertical($caption, $data) {
        $tpl = $this->_initTemplate('generic_table_vertical.tpl.html');
        
        if (!is_null($caption) && $caption != '') {
            $tpl->setVariable("Caption", $caption);
        }

        if (!is_array($data)) {
            $tpl->setCurrentBlock('Data_row');
            $tpl->setVariable('Title', '&nbsp;');
            $tpl->setVariable('Text', nl2br($data));
            $tpl->parseCurrentBlock();
        } else {
            foreach($data as $row) {
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Title', $row[0]);
                $tpl->setVariable('Text', nl2br($row[1]));
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();
        return true;
    }

    /**
     * Output details of one channel
     *
     * @param array $data array containing all information about the channel
     *
     * @return boolean true (yep. i am an optimist)
     */
    function _outputChannelInfo($data)
    {
        $data['main']['data'] = $this->htmlentities_recursive($data['main']['data']);
        $channel = $data['main']['data']['server'][1];
        $output = '';

        if ($channel != '__uri') {
            // add 'More' options
            $image = sprintf('<img src="%s?img=package" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                    '<a href="%s?command=list-packages&chan=%s" class="green">%s List all packagenames of this channel</a>',
                    $_SERVER["PHP_SELF"],
                    $channel,
                    $image);
            $output .= '<br />';
            $image = sprintf('<img src="%s?img=category" border="0" alt="manual">', $_SERVER["PHP_SELF"]);
            $output .= sprintf(
                    '<a href="%s?command=list-categories&chan=%s" class="green">%s List all categories of this channel</a>',
                    $_SERVER["PHP_SELF"],
                    $channel,
                    $image);
            $output .= '<br />';
            $output .= sprintf(
                    '<a href="%s?command=list-categories&chan=%s&opt=packages" class="green">%s List all categories, with packagenames, of this channel</a>',
                    $_SERVER["PHP_SELF"],
                    $channel,
                    $image);
            $data['main']['data']['more'] = array('More', $output);
        }

        return $this->_outputGenericTableVertical($data['main']['caption'], $data['main']['data']);
    }

    /**
     * Output all kinds of data depending on the command which called this method
     *
     * @param mixed  $data    datastructure containing the information to display
     * @param string $command (optional) command from which this method was called
     *
     * @access public
     *
     * @return mixed highly depends on the command
     */
    function outputData($data, $command = '_default')
    {
        switch ($command) {
            case 'config-show':
                $prompt  = array();
                $default = array();
                foreach($data['data'] as $group) {
                    foreach($group as $row) {
                        $prompt[$row[1]]  = $row[0];
                        $default[$row[1]] = $row[2];
                    }
                }
                $title = 'Configuration :: '.$GLOBALS['pear_user_config'];
                $GLOBALS['_PEAR_Frontend_Web_Config'] =
                    $this->userDialog($command, $prompt, array(), $default, $title, 'config');
                return true;
            case 'list-files':
                return $this->_outputListFiles($data);
            case 'list-docs':
                return $this->_outputListDocs($data);
            case 'doc-show':
                return $this->_outputDocShow($data);
            case 'list-all':
                return $this->_outputListAll($data);
            case 'list-packages':
                return $this->_outputListPackages($data);
            case 'list-categories':
                return $this->_outputListCategories($data);
            case 'list-category':
                return $this->_outputListCategory($data);
            case 'list-upgrades':
                return $this->_outputListUpgrades($data);
            case 'list':
                return $this->_outputList($data);
            case 'list-channels':
                return $this->_outputListChannels($data);
            case 'search':
                return $this->_outputListAll($data, false);
            case 'remote-info':
                return $this->_outputPackageRemoteInfo($data);
            case 'package-info': // = 'info' command
                return $this->_outputPackageInfo($data);
            case 'channel-info':
                return $this->_outputChannelInfo($data);
            case 'login':
                if ($_SERVER["REQUEST_METHOD"] != "POST")
                    $this->_data[$command] = $data;
                return true;
            case 'logout':
                $this->displayError($data, 'Logout', 'logout');
                break;
            case 'install':
            case 'upgrade':
            case 'upgrade-all':
            case 'uninstall':
            case 'channel-delete':
            case 'package':
            case 'channel-discover':
            case 'update-channels':
            case 'channel-update':
                if (is_array($data)) {
                    print($data['data'].'<br />');
                } else {
                    print($data.'<br />');
                }
                break;
            default:
                if ($this->_installScript) {
                    $this->_savedOutput[] = $_SESSION['_PEAR_Frontend_Web_SavedOutput'][] = $data;
                    break;
                }
                if (!is_array($data)) {
                    // WARNING: channel "pear.php.net" has updated its protocols, use "channel-update pear.php.net" to update: auto-URL
                    if (preg_match('/use "channel-update ([\S]+)" to update$/', $data, $matches)) {
                        $channel = $matches[1];
                        $url = sprintf('<a href="%s?command=channel-update&chan=%s" class="green">channel-update %s</a>',
                                $_SERVER['PHP_SELF'],
                                $channel,
                                $channel);
                        $data = preg_replace('/channel-update '.$channel.'/',
                                                $url,
                                                $data);
                    }

                    // pearified/Role_Web has post-install scripts: bold
                    if (strpos($data, 'has post-install scripts:') !== false) {
                        $data = '<br /><i>'.$data.'</i>';
                    }
                    // Use "pear run-scripts pearified/Role_Web" to run
                    if (preg_match('/^Use "pear run-scripts ([\S]+)" to run$/', $data, $matches)) {
                        $pkg = $matches[1];
                        $url = sprintf('<a href="%s?command=run-scripts&pkg=%s" class="green">pear run-scripts %s</a>',
                                $_SERVER['PHP_SELF'],
                                $pkg,
                                $pkg);
                        $pkg = str_replace('/', '\/', $pkg);
                        $data = preg_replace('/pear run-scripts '.$pkg.'/',
                                                $url,
                                                $data);
                        $data = '<b>Attention !</b> '.$data.' !';
                    }
                    if (strpos($data, 'DO NOT RUN SCRIPTS FROM UNTRUSTED SOURCES') !== false) {
                        break;
                    }
                                
                    // TODO: div magic, give it a color and a box etc.
                    print('<div>'.$data.'<div>');
                }
        }

        return true;
    }

    /**
     * Output a Table Of Channels:
     * Table of contents like thing for all channels
     * (using <a name= stuff
     */
    function outputTableOfChannels()
    {
        $tpl = $this->_initTemplate('tableofchannels.tpl.html');
        $tpl->setVariable('Caption', 'All available channels:');

        $reg = $this->config->getRegistry();
        $channels = $reg->getChannels();
        foreach ($channels as $channel) {
            if ($channel->getName() != '__uri') {
                $tpl->setCurrentBlock('Data_row');
                $tpl->setVariable('Channel', $channel->getName());
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->show();
    }

    /**
     * Output the 'upgrade-all' page
     */
    function outputUpgradeAll()
    {
        $tpl = $this->_initTemplate('upgrade_all.tpl.html');
        $tpl->setVariable('UpgradeAllURL', $_SERVER['PHP_SELF']);
        $tpl->show();
    }

    /**
     * Output the 'search' page
     */
    function outputSearch()
    {
        $reg = $this->config->getRegistry();
        $channels = $reg->getChannels();
        $channel_select = array('all' => 'All channels');
        foreach ($channels as $channel) {
            if ($channel->getName() != '__uri') {
                $channel_select[$channel->getName()] = $channel->getName();
            }
        }

        // search-types to display
        $arr = array(
              'name' => array('title' => 'Search package by name (fast)',
                              'descr' => 'Package name'),
              'description' => array('title' => 'Search package by name and description (slow)',
                                     'descr' => 'Search:'),
             );

        foreach($arr as $type => $values) {
            $tpl = $this->_initTemplate('search.tpl.html');
            $tpl->setCurrentBlock('Search');
            foreach($channel_select as $key => $value) {
                $tpl->setCurrentBlock('Search_channel');
                $tpl->setVariable('Key', $key);
                $tpl->setVariable('Value', $value);
                $tpl->parseCurrentBlock();
            }
            $tpl->setVariable('InstallerURL', $_SERVER['PHP_SELF']);
            $tpl->setVariable('Search_type', $type);
            $tpl->setVariable('Title', $values['title']);
            $tpl->setVariable('Description', $values['descr']);
            $tpl->parseCurrentBlock();
            $tpl->show();
        }
    }

    /**
     * Start session: starts saving output temporary
     */
    function startSession()
    {
        if ($this->_installScript) {
            if (!isset($_SESSION['_PEAR_Frontend_Web_SavedOutput'])) {
                $_SESSION['_PEAR_Frontend_Web_SavedOutput'] = array();
            }
            $this->_savedOutput = $_SESSION['_PEAR_Frontend_Web_SavedOutput'];
        } else {
            $this->_savedOutput = array();
        }
    }

    /**
     * End session: output all saved output
     */
    function finishOutput($command, $redirectLink = false)
    {
        unset($_SESSION['_PEAR_Frontend_Web_SavedOutput']);
        $tpl = $this->_initTemplate('info.tpl.html');
        foreach($this->_savedOutput as $row) {
            $tpl->setCurrentBlock('Infoloop');
            $tpl->setVariable("Info", $row);
            $tpl->parseCurrentBlock();
        }
        if ($redirectLink) {
            $tpl->setCurrentBlock('Infoloop');
            $tpl->setVariable("Info", '<a href="' . $redirectLink['link'] . '" class="green">' .
                $redirectLink['text'] . '</a>');
            $tpl->parseCurrentBlock();
        }
        $tpl->show();
    }

    /**
     * Run postinstall scripts
     *
     * @param array An array of PEAR_Task_Postinstallscript objects (or related scripts)
     * @param PEAR_PackageFile_v2
     */
    function runPostinstallScripts(&$scripts, $pkg)
    {
        if (!isset($_SESSION['_PEAR_Frontend_Web_Scripts'])) {
            $saves = array();
            foreach ($scripts as $i => $task) {
                $saves[$i] = (array) $task->_obj;
            }
            $_SESSION['_PEAR_Frontend_Web_Scripts'] = $saves;
            $nonsession = true;
        } else {
            $nonsession = false;
        }
        foreach ($scripts as $i => $task) {
            if (!isset($_SESSION['_PEAR_Frontend_Web_ScriptIndex'])) {
                $_SESSION['_PEAR_Frontend_Web_ScriptIndex'] = $i;
            }
            if ($i != $_SESSION['_PEAR_Frontend_Web_ScriptIndex']) {
                continue;
            }
            if (!$nonsession) {
                // restore values from previous sessions to the install script
                foreach ($_SESSION['_PEAR_Frontend_Web_Scripts'][$i] as $name => $val) {
                    if ($name{0} == '_') {
                        // only public variables will be restored
                        continue;
                    }
                    $scripts[$i]->_obj->$name = $val;
                }
            }
            $this->_installScript = true;
            $this->startSession();
            $this->runInstallScript($scripts[$i]->_params, $scripts[$i]->_obj, $pkg);
            $saves = $scripts;
            foreach ($saves as $i => $task) {
                $saves[$i] = (array) $task->_obj;
            }
            $_SESSION['_PEAR_Frontend_Web_Scripts'] = $saves;
            unset($_SESSION['_PEAR_Frontend_Web_ScriptIndex']);
        }
        $this->_installScript = false;
        unset($_SESSION['_PEAR_Frontend_Web_Scripts']);
        $pkg_full = $pkg->getChannel().'/'.$pkg->getPackage();
        $this->finishOutput($pkg_full . ' Install Script',
            array('link' => $_SERVER['PHP_SELF'] .
            '?command=info&pkg='.$pkg_full,
                'text' => 'Click for ' .$pkg_full. ' Information'));
    }

    /**
     * Instruct the runInstallScript method to skip a paramgroup that matches the
     * id value passed in.
     *
     * This method is useful for dynamically configuring which sections of a post-install script
     * will be run based on the user's setup, which is very useful for making flexible
     * post-install scripts without losing the cross-Frontend ability to retrieve user input
     * @param string
     */
    function skipParamgroup($id)
    {
        $_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'][$sectionName] = true;
    }

    /**
     * @param array $xml contents of postinstallscript tag
     *  example: Array (
                [paramgroup] => Array (
                    [id] => webSetup
                    [param] => Array (
                        [name] => webdirpath
                        [prompt] => Where should... ?
                        [default] => '/var/www/htdocs/webpear
                        [type] => string
                        )
                    )
                )
     * @param object $script post-installation script
     * @param PEAR_PackageFile_v1|PEAR_PackageFile_v2 $pkg
     * @param string $contents contents of the install script
     */
    function runInstallScript($xml, &$script, &$pkg)
    {
        if (!isset($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'])) {
            $_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'] = array();
            $_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'] = array();
        }
        if (isset($_SESSION['_PEAR_Frontend_Web_ScriptObj'])) {
            foreach ($_SESSION['_PEAR_Frontend_Web_ScriptObj'] as $name => $val) {
                if ($name{0} == '_') {
                    // only public variables will be restored
                    continue;
                }
                $script->$name = $val;
            }
        } else {
            $_SESSION['_PEAR_Frontend_Web_ScriptObj'] = (array) $script;
        }
        if (!is_array($xml) || !isset($xml['paramgroup'])) {
            $script->run(array(), '_default');
        } else {
            if (!isset($xml['paramgroup'][0])) {
                $xml['paramgroup'] = array($xml['paramgroup']);
            }
            foreach ($xml['paramgroup'] as $i => $group) {
                if (isset($_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'][$group['id']])) {
                    continue;
                }
                if (isset($_SESSION['_PEAR_Frontend_Web_ScriptSection'])) {
                    if ($i < $_SESSION['_PEAR_Frontend_Web_ScriptSection']) {
                        $lastgroup = $group;
                        continue;
                    }
                }
                if (isset($_SESSION['_PEAR_Frontend_Web_answers'])) {
                    $answers = $_SESSION['_PEAR_Frontend_Web_answers'];
                }
                if (isset($group['name'])) {
                    if (isset($answers)) {
                        if (isset($answers[$group['name']])) {
                            switch ($group['conditiontype']) {
                                case '=' :
                                    if ($answers[$group['name']] != $group['value']) {
                                        continue 2;
                                    }
                                break;
                                case '!=' :
                                    if ($answers[$group['name']] == $group['value']) {
                                        continue 2;
                                    }
                                break;
                                case 'preg_match' :
                                    if (!@preg_match('/' . $group['value'] . '/',
                                          $answers[$group['name']])) {
                                        continue 2;
                                    }
                                break;
                                default :
                                    $this->_clearScriptSession();
                                return;
                            }
                        }
                    } else {
                        $this->_clearScriptSession();
                        return;
                    }
                }
                if (!isset($group['param'][0])) {
                    $group['param'] = array($group['param']);
                }
                $_SESSION['_PEAR_Frontend_Web_ScriptSection'] = $i;
                if (!isset($answers)) {
                    $answers = array();
                }
                if (isset($group['param'])) {
                    if (method_exists($script, 'postProcessPrompts')) {
                        $prompts = $script->postProcessPrompts($group['param'], $group['name']);
                        if (!is_array($prompts) || count($prompts) != count($group['param'])) {
                            $this->outputData('postinstall', 'Error: post-install script did not ' .
                                'return proper post-processed prompts');
                            $prompts = $group['param'];
                        } else {
                            foreach ($prompts as $i => $var) {
                                if (!is_array($var) || !isset($var['prompt']) ||
                                      !isset($var['name']) ||
                                      ($var['name'] != $group['param'][$i]['name']) ||
                                      ($var['type'] != $group['param'][$i]['type'])) {
                                    $this->outputData('postinstall', 'Error: post-install script ' .
                                        'modified the variables or prompts, severe security risk. ' .
                                        'Will instead use the defaults from the package.xml');
                                    $prompts = $group['param'];
                                }
                            }
                        }
                        $answers = array_merge($answers,
                            $this->confirmDialog($prompts,
                                $pkg->getChannel().'/'.$pkg->getPackage()));
                    } else {
                        $answers = array_merge($answers,
                            $this->confirmDialog($group['param'],
                                $pkg->getChannel().'/'.$pkg->getPackage()));
                    }
                }
                if ($answers) {
                    array_unshift($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'],
                        $group['id']);
                    if (!$script->run($answers, $group['id'])) {
                        $script->run($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'],
                            '_undoOnError');
                        $this->_clearScriptSession();
                        return;
                    }
                } else {
                    $script->run(array(), '_undoOnError');
                    $this->_clearScriptSession();
                    return;
                }
                $lastgroup = $group;
                foreach ($group['param'] as $param) {
                    // rename the current params to save for future tests
                    $answers[$group['id'] . '::' . $param['name']] = $answers[$param['name']];
                    unset($answers[$param['name']]);
                }
                // save the script's variables and user answers for the next round
                $_SESSION['_PEAR_Frontend_Web_ScriptObj'] = (array) $script;
                $_SESSION['_PEAR_Frontend_Web_answers'] = $answers;
                $_SERVER['REQUEST_METHOD'] = '';
            }
        }
        $this->_clearScriptSession();
    }

    function _clearScriptSession()
    {
        unset($_SESSION['_PEAR_Frontend_Web_ScriptObj']);
        unset($_SESSION['_PEAR_Frontend_Web_answers']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptSection']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptSkipSections']);
    }

    /**
     * Ask for user input, confirm the answers and continue until the user is satisfied
     *
     * @param array an array of arrays, format array('name' => 'paramname', 'prompt' =>
     *              'text to display', 'type' => 'string'[, default => 'default value'])
     * @param string Package Name
     * @return array|false
     */
    function confirmDialog($params, $pkg)
    {
        $answers = array();
        $prompts = $types = array();
        foreach ($params as $param) {
            $prompts[$param['name']] = $param['prompt'];
            $types[$param['name']] = $param['type'];
            if (isset($param['default'])) {
                $answers[$param['name']] = $param['default'];
            } else {
                $answers[$param['name']] = '';
            }
        }
        $attempt = 0;
        do {
            if ($attempt) {
                $_SERVER['REQUEST_METHOD'] = '';
            }
            $title = !$attempt ? $pkg . ' Install Script Input' : 'Please fill in all values';
            $answers = $this->userDialog('run-scripts', $prompts, $types, $answers, $title, '',
                array('pkg' => $pkg));
            if ($answers === false) {
                return false;
            }
            $attempt++;
        } while (count(array_filter($answers)) != count($prompts));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        return $answers;
    }

    /**
     * Useless function that needs to exists for Frontend::setFrontendObject()
     * Reported in bug #10656
     */
    function userConfirm($prompt, $default = 'yes')
    {
        trigger_error("PEAR_Frontend_Web::userConfirm not used", E_USER_ERROR);
    }

    /**
     * Display a formular and return the given input (yes. needs to requests)
     *
     * @param string $command  command from which this method was called
     * @param array  $prompts  associative array. keys are the inputfieldnames
     *                         and values are the description
     * @param array  $types    (optional) array of inputfieldtypes (text, password,
     *                         etc.) keys have to be the same like in $prompts
     * @param array  $defaults (optional) array of defaultvalues. again keys have
     *                         to be the same like in $prompts
     * @param string $title    (optional) title of the page
     * @param string $icon     (optional) iconhandle for this page
     * @param array  $extra    (optional) extra parameters to put in the form action
     *
     * @access public
     *
     * @return array input sended by the user
     */
    function userDialog($command, $prompts, $types = array(), $defaults = array(), $title = '',
                        $icon = '', $extra = array())
    {
        // If this is an POST Request, we can return the userinput
        if (isset($_GET["command"]) && $_GET["command"]==$command
            && $_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['cancel'])) {
                return false;
            }
            $result = array();
            foreach($prompts as $key => $prompt) {
                $result[$key] = $_POST[$key];
            }
            return $result;
        }

        // If this is an Answer GET Request , we can return the userinput
        if (isset($_GET["command"]) && $_GET["command"]==$command
            && isset($_GET["userDialogResult"]) && $_GET["userDialogResult"]=='get') {
            $result = array();
            foreach($prompts as $key => $prompt) {
                $result[$key] = $_GET[$key];
            }
            return $result;
        }

        // Assign title and icon to some commands
        if ($command == 'login') {
            $title = 'Login';
        }

        $tpl = $this->_initTemplate('userDialog.tpl.html');
        $tpl->setVariable("Command", $command);
        $extrap = '';
        if (count($extra)) {
            $extrap = '&';
            foreach ($extra as $name => $value) {
                $extrap .= urlencode($name) . '=' . urlencode($value);
            }
        }
        $tpl->setVariable("extra", $extrap);
        if ($title != '') {
            $tpl->setVariable('Caption', $title);
        } else {
            $tpl->setVariable('Caption', ucfirst($command));
        }

        if (is_array($prompts)) {
            $maxlen = 0;
            foreach($prompts as $key => $prompt) {
                if (strlen($prompt) > $maxlen) {
                    $maxlen = strlen($prompt);
                }
            }

            foreach($prompts as $key => $prompt) {
                $tpl->setCurrentBlock("InputField");
                $type    = (isset($types[$key])    ? $types[$key]    : 'text');
                $default = (isset($defaults[$key]) ? $defaults[$key] : '');
                $tpl->setVariable("prompt", $prompt);
                $tpl->setVariable("name", $key);
                $tpl->setVariable("default", $default);
                $tpl->setVariable("type", $type);
                if ($maxlen > 25) {
                    $tpl->setVariable("width", 'width="275"');
                }
                $tpl->parseCurrentBlock();
            }
        }
        if ($command == 'run-scripts') {
            $tpl->setVariable("cancel", '<input type="submit" value="Cancel" name="cancel">');
        }
        $tpl->show();
        exit;
    }

    /**
     * Write message to log
     *
     * @param string $text message which has to written to log
     *
     * @access public
     *
     * @return boolean true
     */
    function log($text)
    {
        if ($text == '.') {
            print($text);
        } else {
            // filter some log output:
            // color some things, drop some others
            $styled = false;

            // color:warning {Failed to download pear/MDB2_Schema within preferred state "stable", latest release is version 0.7.2, stability "beta", use "channel://pear.php.net/MDB2_Schema-0.7.2" to install}
            // make hyperlink the 'channel://...' part
            $pattern = 'Failed to download';
            if (substr($text, 0, strlen($pattern)) == $pattern) {
                // hyperlink
                if (preg_match('/use "channel:\/\/([\S]+)" to install/', $text, $matches)) {
                    $pkg = $matches[1];
                    $url = sprintf('<a href="%s?command=install&pkg=%s" onClick="return installPkg(\'%s\');" class="green">channel://%s</a>',
                                $_SERVER['PHP_SELF'],
                                urlencode($pkg),
                                $pkg,
                                $pkg);
                    $text = preg_replace('/channel:\/\/'.addcslashes($pkg, '/').'/',
                                         $url,
                                         $text);
                }
                // color
                $text = '<div id="error">'.$text.'</div>';
                $styled = true;
            }

            // color:error {chiara/Chiara_Bugs requires package "chiara/Chiara_PEAR_Server" (version >= 0.18.4) || chiara/Chiara_Bugs requires package "channel://savant.pearified.com/Savant3" (version >= 3.0.0)}
            // make hyperlink the 'ch/pkg || channel://ch/pkg' part
            $pattern = ' requires package "';
            if (!$styled && strpos($text, $pattern) !== false) {
                // hyperlink
                if (preg_match('/ package "([\S]+)" \(version /', $text, $matches)) {
                    $pkg = $matches[1];
                    if (substr($pkg, 0, strlen('channel://')) == 'channel://') {
                        $pkg = substr($pkg, strlen('channel://'));
                    }
                    $url = sprintf('<a href="%s?command=info&pkg=%s" class="green">%s</a>',
                                $_SERVER['PHP_SELF'],
                                urlencode($pkg),
                                $matches[1]);
                    $text = preg_replace('/'.addcslashes($matches[1], '/').'/',
                                         $url,
                                         $text);
                }
                // color
                $text = '<div id="warning">'.$text.'</div>';
                $styled = true;
            }
            if (!$styled) {
                $text = '<div id="log">'.$text.'</div>';
            }

            // and output...
            print($text);
        }

        return true;
    }

    /**
     * Totaly deprecated function
     * Needed to install pearified's role_web : /
     * Don't use this !
     */
    function bold($text)
    {
        print('<b>'.$text.'</b><br />');
    }

    /**
     * Sends the required file along with Headers and exits the script
     *
     * @param string $handle handle of the requested file
     * @param string $group  group of the requested file
     *
     * @access public
     *
     * @return null nothing, because script exits
     */
    function outputFrontendFile($handle, $group)
    {
        $handles = array(
            "css" => array(
                "style" => "style.css",
                ),
            "js" => array(
                "package" => "package.js",
                ),
            "image" => array(
                "logout" => array(
                    "type" => "gif",
                    "file" => "logout.gif",
                    ),
                "login" => array(
                    "type" => "gif",
                    "file" => "login.gif",
                    ),
                "config" => array(
                    "type" => "gif",
                    "file" => "config.gif",
                    ),
                "pkglist" => array(
                    "type" => "png",
                    "file" => "pkglist.png",
                    ),
                "pkgsearch" => array(
                    "type" => "png",
                    "file" => "pkgsearch.png",
                    ),
                "package" => array(
                    "type" => "jpeg",
                    "file" => "package.jpg",
                    ),
                "category" => array(
                    "type" => "jpeg",
                    "file" => "category.jpg",
                    ),
                "install" => array(
                    "type" => "gif",
                    "file" => "install.gif",
                    ),
                "install_wait" => array(
                    "type" => "gif",
                    "file" => "install_wait.gif",
                    ),
                "install_ok" => array(
                    "type" => "gif",
                    "file" => "install_ok.gif",
                    ),
                "install_fail" => array(
                    "type" => "gif",
                    "file" => "install_fail.gif",
                    ),
                "uninstall" => array(
                    "type" => "gif",
                    "file" => "trash.gif",
                    ),
                "info" => array(
                    "type" => "gif",
                    "file" => "info.gif",
                    ),
                "infoplus" => array(
                    "type" => "gif",
                    "file" => "infoplus.gif",
                    ),
                "pear" => array(
                    "type" => "gif",
                    "file" => "pearsmall.gif",
                    ),
                "error" => array(
                    "type" => "gif",
                    "file" => "error.gif",
                    ),
                "manual" => array(
                    "type" => "gif",
                    "file" => "manual.gif",
                    ),
                "manualplus" => array(
                    "type" => "gif",
                    "file" => "manualplus.gif",
                    ),
                "download" => array(
                    "type" => "gif",
                    "file" => "download.gif",
                    ),
                ),
            );

        $file = $handles[$group][$handle];
        switch ($group) {
            case 'css':
                header("Content-Type: text/css");
                readfile($this->config->get('data_dir').'/PEAR_Frontend_Web/data/'.$file);
                exit;
            case 'image':
                $filename = $this->config->get('data_dir').'/PEAR_Frontend_Web/data/images/'.$file['file'];
                header("Content-Type: image/".$file['type']);
                header("Expires: ".gmdate("D, d M Y H:i:s \G\M\T", time() + 60*60*24*100));
                header("Last-Modified: ".gmdate("D, d M Y H:i:s \G\M\T", filemtime($filename)));
                header("Cache-Control: public");
                header("Pragma: ");
                readfile($filename);
                exit;
            case 'js':
                header("Content-Type: text/javascript");
                readfile($this->config->get('data_dir').'/PEAR_Frontend_Web/data/'.$file);
                exit;
        }
    }

    /*
     * From DB::Pager. Removing Pager dependency.
     * @private
     */
    function __getData($from, $limit, $numrows, $maxpages = false)
    {
        if (empty($numrows) || ($numrows < 0)) {
            return null;
        }
        $from = (empty($from)) ? 0 : $from;

        if ($limit <= 0) {
            return false;
        }

        // Total number of pages
        $pages = ceil($numrows/$limit);
        $data['numpages'] = $pages;

        // first & last page
        $data['firstpage'] = 1;
        $data['lastpage']  = $pages;

        // Build pages array
        $data['pages'] = array();
        for ($i=1; $i <= $pages; $i++) {
            $offset = $limit * ($i-1);
            $data['pages'][$i] = $offset;
            // $from must point to one page
            if ($from == $offset) {
                // The current page we are
                $data['current'] = $i;
            }
        }
        if (!isset($data['current'])) {
            return PEAR::raiseError (null, 'wrong "from" param', null,
                                     null, null, 'DB_Error', true);
        }
              // Limit number of pages (Goole algorithm)
        if ($maxpages) {
            $radio = floor($maxpages/2);
            $minpage = $data['current'] - $radio;
            if ($minpage < 1) {
                $minpage = 1;
            }
            $maxpage = $data['current'] + $radio - 1;
            if ($maxpage > $data['numpages']) {
                $maxpage = $data['numpages'];
            }
            foreach (range($minpage, $maxpage) as $page) {
                $tmp[$page] = $data['pages'][$page];
            }
            $data['pages'] = $tmp;
            $data['maxpages'] = $maxpages;
        } else {
            $data['maxpages'] = null;
        }

        // Prev link
        $prev = $from - $limit;
        $data['prev'] = ($prev >= 0) ? $prev : null;

        // Next link
        $next = $from + $limit;
        $data['next'] = ($next < $numrows) ? $next : null;

        // Results remaining in next page & Last row to fetch
        if ($data['current'] == $pages) {
            $data['remain'] = 0;
            $data['to'] = $numrows;
        } else {
            if ($data['current'] == ($pages - 1)) {
                $data['remain'] = $numrows - ($limit*($pages-1));
            } else {
                $data['remain'] = $limit;
            }
            $data['to'] = $data['current'] * $limit;
        }
        $data['numrows'] = $numrows;
        $data['from']    = $from + 1;
        $data['limit']   = $limit;

        return $data;
    }

    // }}}
    // {{{ outputBegin($command)

    /**
     * Start output, HTML header etc
     */
    function outputBegin($command)
    {
        if (is_null($command)) {
            // just the header
            $tpl = $this->_initTemplate('header.inc.tpl.html');
        } else {
            $tpl = $this->_initTemplate('top.inc.tpl.html');
            $tpl->setCurrentBlock('Search');
            $tpl->parseCurrentBlock();

            if (!$this->_isProtected()) {
                $tpl->setCurrentBlock('NotProtected');
                $tpl->setVariable('Filler', ' ');
                $tpl->parseCurrentBlock();
            }
        }

        // Initialise begin vars
        if ($this->config->get('preferred_mirror') != $this->config->get('default_channel')) {
            $mirror = ' (mirror ' .$this->config->get('preferred_mirror') . ')';
        } else {
            $mirror = '';
        }
        $tpl->setVariable('_default_channel', $this->config->get('default_channel') . $mirror);
        $tpl->setVariable('ImgPEAR', $_SERVER['PHP_SELF'].'?img=pear');
        $tpl->setVariable('Title', 'PEAR Package Manager, '.$command);
        $tpl->setVariable('Headline', 'Webbased PEAR Package Manager on '.$_SERVER['SERVER_NAME']);

        $tpl->setCurrentBlock();

        $tpl->show();

        // submenu's for list, list-upgrades and list-all
        if ($command == 'list' ||
            $command == 'list-upgrades' ||
            $command == 'list-all' ||
            $command == 'list-categories' ||
            $command == 'list-category' ||
            $command == 'list-packages') {
            
            $tpl = $this->_initTemplate('package.submenu.tpl.html');

            $menus = array(
                'list'              => 'list installed packages',
                'list-upgrades'     => 'list available upgrades',
                'list-packages'     => 'list all packagenames',
                'list-categories'   => 'list all categories',
            );
            $highlight_map = array(
                'list' => 'list',
                'list-upgrades' => 'list-upgrades',
                'list-all' => 'list-categories',
                'list-categories' => 'list-categories',
                'list-category' => 'list-category',
                'list-packages' => 'list-packages',
                    );
            foreach ($menus as $name => $text) {
                $tpl->setCurrentBlock('Submenu');
                $tpl->setVariable("href", $_SERVER["PHP_SELF"].'?command='.$name);
                $tpl->setVariable("text", $text);
                if ($name == $highlight_map[$command]) {
                    $tpl->setVariable("class", 'red');
                } else {
                    $tpl->setVariable("class", 'green');
                }
                $tpl->parseCurrentBlock();
            }
            $tpl->show();
        }
    }

    // }}}
    // {{{ outputEnd($command)

    /**
     * End output, HTML footer etc
     */
    function outputEnd($command)
    {
        if ($command == 'list') {
            // show 'install package' footer
            $tpl = $this->_initTemplate('package.manually.tpl.html');
            $tpl->show();
        }

        if (is_null($command)) {
            // just the header
            $tpl = $this->_initTemplate('footer.inc.tpl.html');
        } else {
            $tpl = $this->_initTemplate('bottom.inc.tpl.html');
        }
        $tpl->setVariable('Filler', '');
        $tpl->show();
    }

    // }}}

    /**
     * Checks if this webfrontend is protected:
     *  - when the client sais so
     *  - when having .htaccess authentication
     *
     * @return boolean
     */
    function _isProtected()
    {
        if (isset($GLOBALS['_PEAR_Frontend_Web_protected']) &&
              $GLOBALS['_PEAR_Frontend_Web_protected'] === true) {
            return true;
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            return true;
        }
            
        if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
            return true;
        }

        return false;
    }

    /**
     * Prepare packagename for HTML output:
     * make it a link
     *
     * @param $package package name (evt 'chan/pkg')
     * @param $channel channel name (when pkg not 'chan/pkg')
     */
    function _prepPkgName($package, $channel=null)
    {
        if (is_null($channel)) {
            $full = $package;
        } else {
            $full = $channel.'/'.$package;
        }
        
        return sprintf('<a href="%s?command=info&pkg=%s" class="blue">%s</a>',
                            $_SERVER['PHP_SELF'],
                            $full,
                            $package);
    }

    /**
     * Prepare Icons (install/uninstall) for HTML output:
     * make img and url
     *
     * @param $package package name
     * @param $channel channel name
     * @param $installed optional when we already know the package is installed
     */
    function _prepIcons($package_name, $channel, $installed=false)
    {
        $reg = $this->config->getRegistry();
        $package = $channel.'/'.$package_name;

        if ($installed || $reg->packageExists($package_name, $channel)) {
            if (in_array($package, $this->_no_delete_pkgs)) {
                // don't allow to uninstall
                $out = '&nbsp;';
            } else {
                $img = sprintf('<img src="%s?img=uninstall" width="18" height="17"  border="0" alt="uninstall">', $_SERVER["PHP_SELF"]);
                $url = sprintf('%s?command=uninstall&pkg=%s', $_SERVER["PHP_SELF"], $package);
                $out = sprintf('<a href="%s" onClick="return uninstallPkg(\'%s\');" id="%s">%s</a>', $url, $package, $package.'_href', $img);
            }
        } elseif (!$installed) {
            $img = sprintf('<img src="%s?img=install" width="13" height="13"  border="0" alt="install">', $_SERVER["PHP_SELF"]);
            $url = sprintf('%s?command=install&pkg=%s', $_SERVER["PHP_SELF"], $package);
            $out = sprintf('<a href="%s" onClick="return installPkg(\'%s\');" id="%s">%s</a>', $url, $package, $package.'_href', $img);
        }

        return $out;
    }

    /**
     * apply 'htmlentities' to every value of the array
     * array_walk_recursive($array, 'htmlentities') in PHP5
     */
    function htmlentities_recursive($data) {
        foreach($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->htmlentities_recursive($value);
            } else {
                $data[$key] = htmlentities($value);
            }
        }
        return $data;
    }
}

?>
