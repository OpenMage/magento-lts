<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Uploader
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * File Helper
 *
 * @category    Mage
 * @package     Mage_Uploader
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Uploader_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * List of pre-defined MIME types
     *
     * @var array
     */
    protected $_mimeTypes =
        array(
            'x123' => 'application/vnd.lotus-1-2-3',
            'x3dml' => 'text/vnd.in3d.3dml',
            'x3g2' => 'video/3gpp2',
            'x3gp' => 'video/3gpp',
            'xace' => 'application/x-ace-compressed',
            'xacu' => 'application/vnd.acucobol',
            'xaep' => 'application/vnd.audiograph',
            'xai' => 'application/postscript',
            'xaif' => 'audio/x-aiff',

            'xaifc' => 'audio/x-aiff',
            'xaiff' => 'audio/x-aiff',
            'xami' => 'application/vnd.amiga.ami',
            'xapr' => 'application/vnd.lotus-approach',
            'xasf' => 'video/x-ms-asf',
            'xaso' => 'application/vnd.accpac.simply.aso',
            'xasx' => 'video/x-ms-asf',
            'xatom' => 'application/atom+xml',
            'xatomcat' => 'application/atomcat+xml',

            'xatomsvc' => 'application/atomsvc+xml',
            'xatx' => 'application/vnd.antix.game-component',
            'xau' => 'audio/basic',
            'xavi' => 'video/x-msvideo',
            'xbat' => 'application/x-msdownload',
            'xbcpio' => 'application/x-bcpio',
            'xbdm' => 'application/vnd.syncml.dm+wbxml',
            'xbh2' => 'application/vnd.fujitsu.oasysprs',
            'xbmi' => 'application/vnd.bmi',

            'xbmp' => 'image/bmp',
            'xbox' => 'application/vnd.previewsystems.box',
            'xboz' => 'application/x-bzip2',
            'xbtif' => 'image/prs.btif',
            'xbz' => 'application/x-bzip',
            'xbz2' => 'application/x-bzip2',
            'xcab' => 'application/vnd.ms-cab-compressed',
            'xccxml' => 'application/ccxml+xml',
            'xcdbcmsg' => 'application/vnd.contact.cmsg',

            'xcdkey' => 'application/vnd.mediastation.cdkey',
            'xcdx' => 'chemical/x-cdx',
            'xcdxml' => 'application/vnd.chemdraw+xml',
            'xcdy' => 'application/vnd.cinderella',
            'xcer' => 'application/pkix-cert',
            'xcgm' => 'image/cgm',
            'xchat' => 'application/x-chat',
            'xchm' => 'application/vnd.ms-htmlhelp',
            'xchrt' => 'application/vnd.kde.kchart',

            'xcif' => 'chemical/x-cif',
            'xcii' => 'application/vnd.anser-web-certificate-issue-initiation',
            'xcil' => 'application/vnd.ms-artgalry',
            'xcla' => 'application/vnd.claymore',
            'xclkk' => 'application/vnd.crick.clicker.keyboard',
            'xclkp' => 'application/vnd.crick.clicker.palette',
            'xclkt' => 'application/vnd.crick.clicker.template',
            'xclkw' => 'application/vnd.crick.clicker.wordbank',
            'xclkx' => 'application/vnd.crick.clicker',

            'xclp' => 'application/x-msclip',
            'xcmc' => 'application/vnd.cosmocaller',
            'xcmdf' => 'chemical/x-cmdf',
            'xcml' => 'chemical/x-cml',
            'xcmp' => 'application/vnd.yellowriver-custom-menu',
            'xcmx' => 'image/x-cmx',
            'xcom' => 'application/x-msdownload',
            'xconf' => 'text/plain',
            'xcpio' => 'application/x-cpio',

            'xcpt' => 'application/mac-compactpro',
            'xcrd' => 'application/x-mscardfile',
            'xcrl' => 'application/pkix-crl',
            'xcrt' => 'application/x-x509-ca-cert',
            'xcsh' => 'application/x-csh',
            'xcsml' => 'chemical/x-csml',
            'xcss' => 'text/css',
            'xcsv' => 'text/csv',
            'xcurl' => 'application/vnd.curl',

            'xcww' => 'application/prs.cww',
            'xdaf' => 'application/vnd.mobius.daf',
            'xdavmount' => 'application/davmount+xml',
            'xdd2' => 'application/vnd.oma.dd2+xml',
            'xddd' => 'application/vnd.fujixerox.ddd',
            'xdef' => 'text/plain',
            'xder' => 'application/x-x509-ca-cert',
            'xdfac' => 'application/vnd.dreamfactory',
            'xdis' => 'application/vnd.mobius.dis',

            'xdjv' => 'image/vnd.djvu',
            'xdjvu' => 'image/vnd.djvu',
            'xdll' => 'application/x-msdownload',
            'xdna' => 'application/vnd.dna',
            'xdoc' => 'application/msword',
            'xdot' => 'application/msword',
            'xdp' => 'application/vnd.osgi.dp',
            'xdpg' => 'application/vnd.dpgraph',
            'xdsc' => 'text/prs.lines.tag',

            'xdtd' => 'application/xml-dtd',
            'xdvi' => 'application/x-dvi',
            'xdwf' => 'model/vnd.dwf',
            'xdwg' => 'image/vnd.dwg',
            'xdxf' => 'image/vnd.dxf',
            'xdxp' => 'application/vnd.spotfire.dxp',
            'xecelp4800' => 'audio/vnd.nuera.ecelp4800',
            'xecelp7470' => 'audio/vnd.nuera.ecelp7470',
            'xecelp9600' => 'audio/vnd.nuera.ecelp9600',

            'xecma' => 'application/ecmascript',
            'xedm' => 'application/vnd.novadigm.edm',
            'xedx' => 'application/vnd.novadigm.edx',
            'xefif' => 'application/vnd.picsel',
            'xei6' => 'application/vnd.pg.osasli',
            'xeml' => 'message/rfc822',
            'xeol' => 'audio/vnd.digital-winds',
            'xeot' => 'application/vnd.ms-fontobject',
            'xeps' => 'application/postscript',

            'xesf' => 'application/vnd.epson.esf',
            'xetx' => 'text/x-setext',
            'xexe' => 'application/x-msdownload',
            'xext' => 'application/vnd.novadigm.ext',
            'xez' => 'application/andrew-inset',
            'xez2' => 'application/vnd.ezpix-album',
            'xez3' => 'application/vnd.ezpix-package',
            'xfbs' => 'image/vnd.fastbidsheet',
            'xfdf' => 'application/vnd.fdf',

            'xfe_launch' => 'application/vnd.denovo.fcselayout-link',
            'xfg5' => 'application/vnd.fujitsu.oasysgp',
            'xfli' => 'video/x-fli',
            'xflo' => 'application/vnd.micrografx.flo',
            'xflw' => 'application/vnd.kde.kivio',
            'xflx' => 'text/vnd.fmi.flexstor',
            'xfly' => 'text/vnd.fly',
            'xfnc' => 'application/vnd.frogans.fnc',
            'xfpx' => 'image/vnd.fpx',

            'xfsc' => 'application/vnd.fsc.weblaunch',
            'xfst' => 'image/vnd.fst',
            'xftc' => 'application/vnd.fluxtime.clip',
            'xfti' => 'application/vnd.anser-web-funds-transfer-initiation',
            'xfvt' => 'video/vnd.fvt',
            'xfzs' => 'application/vnd.fuzzysheet',
            'xg3' => 'image/g3fax',
            'xgac' => 'application/vnd.groove-account',
            'xgdl' => 'model/vnd.gdl',

            'xghf' => 'application/vnd.groove-help',
            'xgif' => 'image/gif',
            'xgim' => 'application/vnd.groove-identity-message',
            'xgph' => 'application/vnd.flographit',
            'xgram' => 'application/srgs',
            'xgrv' => 'application/vnd.groove-injector',
            'xgrxml' => 'application/srgs+xml',
            'xgtar' => 'application/x-gtar',
            'xgtm' => 'application/vnd.groove-tool-message',

            'xsvg' => 'image/svg+xml',

            'xgtw' => 'model/vnd.gtw',
            'xh261' => 'video/h261',
            'xh263' => 'video/h263',
            'xh264' => 'video/h264',
            'xhbci' => 'application/vnd.hbci',
            'xhdf' => 'application/x-hdf',
            'xhlp' => 'application/winhlp',
            'xhpgl' => 'application/vnd.hp-hpgl',
            'xhpid' => 'application/vnd.hp-hpid',

            'xhps' => 'application/vnd.hp-hps',
            'xhqx' => 'application/mac-binhex40',
            'xhtke' => 'application/vnd.kenameaapp',
            'xhtm' => 'text/html',
            'xhtml' => 'text/html',
            'xhvd' => 'application/vnd.yamaha.hv-dic',
            'xhvp' => 'application/vnd.yamaha.hv-voice',
            'xhvs' => 'application/vnd.yamaha.hv-script',
            'xice' => '#x-conference/x-cooltalk',

            'xico' => 'image/x-icon',
            'xics' => 'text/calendar',
            'xief' => 'image/ief',
            'xifb' => 'text/calendar',
            'xifm' => 'application/vnd.shana.informed.formdata',
            'xigl' => 'application/vnd.igloader',
            'xigx' => 'application/vnd.micrografx.igx',
            'xiif' => 'application/vnd.shana.informed.interchange',
            'ximp' => 'application/vnd.accpac.simply.imp',

            'xims' => 'application/vnd.ms-ims',
            'xin' => 'text/plain',
            'xipk' => 'application/vnd.shana.informed.package',
            'xirm' => 'application/vnd.ibm.rights-management',
            'xirp' => 'application/vnd.irepository.package+xml',
            'xitp' => 'application/vnd.shana.informed.formtemplate',
            'xivp' => 'application/vnd.immervision-ivp',
            'xivu' => 'application/vnd.immervision-ivu',
            'xjad' => 'text/vnd.sun.j2me.app-descriptor',

            'xjam' => 'application/vnd.jam',
            'xjava' => 'text/x-java-source',
            'xjisp' => 'application/vnd.jisp',
            'xjlt' => 'application/vnd.hp-jlyt',
            'xjoda' => 'application/vnd.joost.joda-archive',
            'xjpe' => 'image/jpeg',
            'xjpeg' => 'image/jpeg',
            'xjpg' => 'image/jpeg',
            'xjpgm' => 'video/jpm',

            'xjpgv' => 'video/jpeg',
            'xjpm' => 'video/jpm',
            'xjs' => 'application/javascript',
            'xjson' => 'application/json',
            'xkar' => 'audio/midi',
            'xkarbon' => 'application/vnd.kde.karbon',
            'xkfo' => 'application/vnd.kde.kformula',
            'xkia' => 'application/vnd.kidspiration',
            'xkml' => 'application/vnd.google-earth.kml+xml',

            'xkmz' => 'application/vnd.google-earth.kmz',
            'xkon' => 'application/vnd.kde.kontour',
            'xksp' => 'application/vnd.kde.kspread',
            'xlatex' => 'application/x-latex',
            'xlbd' => 'application/vnd.llamagraphics.life-balance.desktop',
            'xlbe' => 'application/vnd.llamagraphics.life-balance.exchange+xml',
            'xles' => 'application/vnd.hhe.lesson-player',
            'xlist' => 'text/plain',
            'xlog' => 'text/plain',

            'xlrm' => 'application/vnd.ms-lrm',
            'xltf' => 'application/vnd.frogans.ltf',
            'xlvp' => 'audio/vnd.lucent.voice',
            'xlwp' => 'application/vnd.lotus-wordpro',
            'xm13' => 'application/x-msmediaview',
            'xm14' => 'application/x-msmediaview',
            'xm1v' => 'video/mpeg',
            'xm2a' => 'audio/mpeg',
            'xm3a' => 'audio/mpeg',

            'xm3u' => 'audio/x-mpegurl',
            'xm4u' => 'video/vnd.mpegurl',
            'xmag' => 'application/vnd.ecowin.chart',
            'xmathml' => 'application/mathml+xml',
            'xmbk' => 'application/vnd.mobius.mbk',
            'xmbox' => 'application/mbox',
            'xmc1' => 'application/vnd.medcalcdata',
            'xmcd' => 'application/vnd.mcd',
            'xmdb' => 'application/x-msaccess',

            'xmdi' => 'image/vnd.ms-modi',
            'xmesh' => 'model/mesh',
            'xmfm' => 'application/vnd.mfmp',
            'xmgz' => 'application/vnd.proteus.magazine',
            'xmid' => 'audio/midi',
            'xmidi' => 'audio/midi',
            'xmif' => 'application/vnd.mif',
            'xmime' => 'message/rfc822',
            'xmj2' => 'video/mj2',

            'xmjp2' => 'video/mj2',
            'xmlp' => 'application/vnd.dolby.mlp',
            'xmmd' => 'application/vnd.chipnuts.karaoke-mmd',
            'xmmf' => 'application/vnd.smaf',
            'xmmr' => 'image/vnd.fujixerox.edmics-mmr',
            'xmny' => 'application/x-msmoney',
            'xmov' => 'video/quicktime',
            'xmovie' => 'video/x-sgi-movie',
            'xmp2' => 'audio/mpeg',

            'xmp2a' => 'audio/mpeg',
            'xmp3' => 'audio/mpeg',
            'xmp4' => 'video/mp4',
            'xmp4a' => 'audio/mp4',
            'xmp4s' => 'application/mp4',
            'xmp4v' => 'video/mp4',
            'xmpc' => 'application/vnd.mophun.certificate',
            'xmpe' => 'video/mpeg',
            'xmpeg' => 'video/mpeg',

            'xmpg' => 'video/mpeg',
            'xmpg4' => 'video/mp4',
            'xmpga' => 'audio/mpeg',
            'xmpkg' => 'application/vnd.apple.installer+xml',
            'xmpm' => 'application/vnd.blueice.multipass',
            'xmpn' => 'application/vnd.mophun.application',
            'xmpp' => 'application/vnd.ms-project',
            'xmpt' => 'application/vnd.ms-project',
            'xmpy' => 'application/vnd.ibm.minipay',

            'xmqy' => 'application/vnd.mobius.mqy',
            'xmrc' => 'application/marc',
            'xmscml' => 'application/mediaservercontrol+xml',
            'xmseq' => 'application/vnd.mseq',
            'xmsf' => 'application/vnd.epson.msf',
            'xmsh' => 'model/mesh',
            'xmsi' => 'application/x-msdownload',
            'xmsl' => 'application/vnd.mobius.msl',
            'xmsty' => 'application/vnd.muvee.style',

            'xmts' => 'model/vnd.mts',
            'xmus' => 'application/vnd.musician',
            'xmvb' => 'application/x-msmediaview',
            'xmwf' => 'application/vnd.mfer',
            'xmxf' => 'application/mxf',
            'xmxl' => 'application/vnd.recordare.musicxml',
            'xmxml' => 'application/xv+xml',
            'xmxs' => 'application/vnd.triscape.mxs',
            'xmxu' => 'video/vnd.mpegurl',

            'xn-gage' => 'application/vnd.nokia.n-gage.symbian.install',
            'xngdat' => 'application/vnd.nokia.n-gage.data',
            'xnlu' => 'application/vnd.neurolanguage.nlu',
            'xnml' => 'application/vnd.enliven',
            'xnnd' => 'application/vnd.noblenet-directory',
            'xnns' => 'application/vnd.noblenet-sealer',
            'xnnw' => 'application/vnd.noblenet-web',
            'xnpx' => 'image/vnd.net-fpx',
            'xnsf' => 'application/vnd.lotus-notes',

            'xoa2' => 'application/vnd.fujitsu.oasys2',
            'xoa3' => 'application/vnd.fujitsu.oasys3',
            'xoas' => 'application/vnd.fujitsu.oasys',
            'xobd' => 'application/x-msbinder',
            'xoda' => 'application/oda',
            'xodc' => 'application/vnd.oasis.opendocument.chart',
            'xodf' => 'application/vnd.oasis.opendocument.formula',
            'xodg' => 'application/vnd.oasis.opendocument.graphics',
            'xodi' => 'application/vnd.oasis.opendocument.image',

            'xodp' => 'application/vnd.oasis.opendocument.presentation',
            'xods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'xodt' => 'application/vnd.oasis.opendocument.text',
            'xogg' => 'application/ogg',
            'xoprc' => 'application/vnd.palm',
            'xorg' => 'application/vnd.lotus-organizer',
            'xotc' => 'application/vnd.oasis.opendocument.chart-template',
            'xotf' => 'application/vnd.oasis.opendocument.formula-template',
            'xotg' => 'application/vnd.oasis.opendocument.graphics-template',

            'xoth' => 'application/vnd.oasis.opendocument.text-web',
            'xoti' => 'application/vnd.oasis.opendocument.image-template',
            'xotm' => 'application/vnd.oasis.opendocument.text-master',
            'xots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'xott' => 'application/vnd.oasis.opendocument.text-template',
            'xoxt' => 'application/vnd.openofficeorg.extension',
            'xp10' => 'application/pkcs10',
            'xp7r' => 'application/x-pkcs7-certreqresp',
            'xp7s' => 'application/pkcs7-signature',

            'xpbd' => 'application/vnd.powerbuilder6',
            'xpbm' => 'image/x-portable-bitmap',
            'xpcl' => 'application/vnd.hp-pcl',
            'xpclxl' => 'application/vnd.hp-pclxl',
            'xpct' => 'image/x-pict',
            'xpcx' => 'image/x-pcx',
            'xpdb' => 'chemical/x-pdb',
            'xpdf' => 'application/pdf',
            'xpfr' => 'application/font-tdpfr',

            'xpgm' => 'image/x-portable-graymap',
            'xpgn' => 'application/x-chess-pgn',
            'xpgp' => 'application/pgp-encrypted',
            'xpic' => 'image/x-pict',
            'xpki' => 'application/pkixcmp',
            'xpkipath' => 'application/pkix-pkipath',
            'xplb' => 'application/vnd.3gpp.pic-bw-large',
            'xplc' => 'application/vnd.mobius.plc',
            'xplf' => 'application/vnd.pocketlearn',

            'xpls' => 'application/pls+xml',
            'xpml' => 'application/vnd.ctc-posml',
            'xpng' => 'image/png',
            'xpnm' => 'image/x-portable-anymap',
            'xportpkg' => 'application/vnd.macports.portpkg',
            'xpot' => 'application/vnd.ms-powerpoint',
            'xppd' => 'application/vnd.cups-ppd',
            'xppm' => 'image/x-portable-pixmap',
            'xpps' => 'application/vnd.ms-powerpoint',

            'xppt' => 'application/vnd.ms-powerpoint',
            'xpqa' => 'application/vnd.palm',
            'xprc' => 'application/vnd.palm',
            'xpre' => 'application/vnd.lotus-freelance',
            'xprf' => 'application/pics-rules',
            'xps' => 'application/postscript',
            'xpsb' => 'application/vnd.3gpp.pic-bw-small',
            'xpsd' => 'image/vnd.adobe.photoshop',
            'xptid' => 'application/vnd.pvi.ptid1',

            'xpub' => 'application/x-mspublisher',
            'xpvb' => 'application/vnd.3gpp.pic-bw-var',
            'xpwn' => 'application/vnd.3m.post-it-notes',
            'xqam' => 'application/vnd.epson.quickanime',
            'xqbo' => 'application/vnd.intu.qbo',
            'xqfx' => 'application/vnd.intu.qfx',
            'xqps' => 'application/vnd.publishare-delta-tree',
            'xqt' => 'video/quicktime',
            'xra' => 'audio/x-pn-realaudio',

            'xram' => 'audio/x-pn-realaudio',
            'xrar' => 'application/x-rar-compressed',
            'xras' => 'image/x-cmu-raster',
            'xrcprofile' => 'application/vnd.ipunplugged.rcprofile',
            'xrdf' => 'application/rdf+xml',
            'xrdz' => 'application/vnd.data-vision.rdz',
            'xrep' => 'application/vnd.businessobjects',
            'xrgb' => 'image/x-rgb',
            'xrif' => 'application/reginfo+xml',

            'xrl' => 'application/resource-lists+xml',
            'xrlc' => 'image/vnd.fujixerox.edmics-rlc',
            'xrm' => 'application/vnd.rn-realmedia',
            'xrmi' => 'audio/midi',
            'xrmp' => 'audio/x-pn-realaudio-plugin',
            'xrms' => 'application/vnd.jcp.javame.midlet-rms',
            'xrnc' => 'application/relax-ng-compact-syntax',
            'xrpss' => 'application/vnd.nokia.radio-presets',
            'xrpst' => 'application/vnd.nokia.radio-preset',

            'xrq' => 'application/sparql-query',
            'xrs' => 'application/rls-services+xml',
            'xrsd' => 'application/rsd+xml',
            'xrss' => 'application/rss+xml',
            'xrtf' => 'application/rtf',
            'xrtx' => 'text/richtext',
            'xsaf' => 'application/vnd.yamaha.smaf-audio',
            'xsbml' => 'application/sbml+xml',
            'xsc' => 'application/vnd.ibm.secure-container',

            'xscd' => 'application/x-msschedule',
            'xscm' => 'application/vnd.lotus-screencam',
            'xscq' => 'application/scvp-cv-request',
            'xscs' => 'application/scvp-cv-response',
            'xsdp' => 'application/sdp',
            'xsee' => 'application/vnd.seemail',
            'xsema' => 'application/vnd.sema',
            'xsemd' => 'application/vnd.semd',
            'xsemf' => 'application/vnd.semf',

            'xsetpay' => 'application/set-payment-initiation',
            'xsetreg' => 'application/set-registration-initiation',
            'xsfs' => 'application/vnd.spotfire.sfs',
            'xsgm' => 'text/sgml',
            'xsgml' => 'text/sgml',
            'xsh' => 'application/x-sh',
            'xshar' => 'application/x-shar',
            'xshf' => 'application/shf+xml',
            'xsilo' => 'model/mesh',

            'xsit' => 'application/x-stuffit',
            'xsitx' => 'application/x-stuffitx',
            'xslt' => 'application/vnd.epson.salt',
            'xsnd' => 'audio/basic',
            'xspf' => 'application/vnd.yamaha.smaf-phrase',
            'xspl' => 'application/x-futuresplash',
            'xspot' => 'text/vnd.in3d.spot',
            'xspp' => 'application/scvp-vp-response',
            'xspq' => 'application/scvp-vp-request',

            'xsrc' => 'application/x-wais-source',
            'xsrx' => 'application/sparql-results+xml',
            'xssf' => 'application/vnd.epson.ssf',
            'xssml' => 'application/ssml+xml',
            'xstf' => 'application/vnd.wt.stf',
            'xstk' => 'application/hyperstudio',
            'xstr' => 'application/vnd.pg.format',
            'xsus' => 'application/vnd.sus-calendar',
            'xsusp' => 'application/vnd.sus-calendar',

            'xsv4cpio' => 'application/x-sv4cpio',
            'xsv4crc' => 'application/x-sv4crc',
            'xsvd' => 'application/vnd.svd',
            'xswf' => 'application/x-shockwave-flash',
            'xtao' => 'application/vnd.tao.intent-module-archive',
            'xtar' => 'application/x-tar',
            'xtcap' => 'application/vnd.3gpp2.tcap',
            'xtcl' => 'application/x-tcl',
            'xtex' => 'application/x-tex',

            'xtext' => 'text/plain',
            'xtif' => 'image/tiff',
            'xtiff' => 'image/tiff',
            'xtmo' => 'application/vnd.tmobile-livetv',
            'xtorrent' => 'application/x-bittorrent',
            'xtpl' => 'application/vnd.groove-tool-template',
            'xtpt' => 'application/vnd.trid.tpt',
            'xtra' => 'application/vnd.trueapp',
            'xtrm' => 'application/x-msterminal',

            'xtsv' => 'text/tab-separated-values',
            'xtxd' => 'application/vnd.genomatix.tuxedo',
            'xtxf' => 'application/vnd.mobius.txf',
            'xtxt' => 'text/plain',
            'xumj' => 'application/vnd.umajin',
            'xunityweb' => 'application/vnd.unity',
            'xuoml' => 'application/vnd.uoml+xml',
            'xuri' => 'text/uri-list',
            'xuris' => 'text/uri-list',

            'xurls' => 'text/uri-list',
            'xustar' => 'application/x-ustar',
            'xutz' => 'application/vnd.uiq.theme',
            'xuu' => 'text/x-uuencode',
            'xvcd' => 'application/x-cdlink',
            'xvcf' => 'text/x-vcard',
            'xvcg' => 'application/vnd.groove-vcard',
            'xvcs' => 'text/x-vcalendar',
            'xvcx' => 'application/vnd.vcx',

            'xvis' => 'application/vnd.visionary',
            'xviv' => 'video/vnd.vivo',
            'xvrml' => 'model/vrml',
            'xvsd' => 'application/vnd.visio',
            'xvsf' => 'application/vnd.vsf',
            'xvss' => 'application/vnd.visio',
            'xvst' => 'application/vnd.visio',
            'xvsw' => 'application/vnd.visio',
            'xvtu' => 'model/vnd.vtu',

            'xvxml' => 'application/voicexml+xml',
            'xwav' => 'audio/x-wav',
            'xwax' => 'audio/x-ms-wax',
            'xwbmp' => 'image/vnd.wap.wbmp',
            'xwbs' => 'application/vnd.criticaltools.wbs+xml',
            'xwbxml' => 'application/vnd.wap.wbxml',
            'xwcm' => 'application/vnd.ms-works',
            'xwdb' => 'application/vnd.ms-works',
            'xwks' => 'application/vnd.ms-works',

            'xwm' => 'video/x-ms-wm',
            'xwma' => 'audio/x-ms-wma',
            'xwmd' => 'application/x-ms-wmd',
            'xwmf' => 'application/x-msmetafile',
            'xwml' => 'text/vnd.wap.wml',
            'xwmlc' => 'application/vnd.wap.wmlc',
            'xwmls' => 'text/vnd.wap.wmlscript',
            'xwmlsc' => 'application/vnd.wap.wmlscriptc',
            'xwmv' => 'video/x-ms-wmv',

            'xwmx' => 'video/x-ms-wmx',
            'xwmz' => 'application/x-ms-wmz',
            'xwpd' => 'application/vnd.wordperfect',
            'xwpl' => 'application/vnd.ms-wpl',
            'xwps' => 'application/vnd.ms-works',
            'xwqd' => 'application/vnd.wqd',
            'xwri' => 'application/x-mswrite',
            'xwrl' => 'model/vrml',
            'xwsdl' => 'application/wsdl+xml',

            'xwspolicy' => 'application/wspolicy+xml',
            'xwtb' => 'application/vnd.webturbo',
            'xwvx' => 'video/x-ms-wvx',
            'xx3d' => 'application/vnd.hzn-3d-crossword',
            'xxar' => 'application/vnd.xara',
            'xxbd' => 'application/vnd.fujixerox.docuworks.binder',
            'xxbm' => 'image/x-xbitmap',
            'xxdm' => 'application/vnd.syncml.dm+xml',
            'xxdp' => 'application/vnd.adobe.xdp+xml',

            'xxdw' => 'application/vnd.fujixerox.docuworks',
            'xxenc' => 'application/xenc+xml',
            'xxfdf' => 'application/vnd.adobe.xfdf',
            'xxfdl' => 'application/vnd.xfdl',
            'xxht' => 'application/xhtml+xml',
            'xxhtml' => 'application/xhtml+xml',
            'xxhvml' => 'application/xv+xml',
            'xxif' => 'image/vnd.xiff',
            'xxla' => 'application/vnd.ms-excel',

            'xxlc' => 'application/vnd.ms-excel',
            'xxlm' => 'application/vnd.ms-excel',
            'xxls' => 'application/vnd.ms-excel',
            'xxlt' => 'application/vnd.ms-excel',
            'xxlw' => 'application/vnd.ms-excel',
            'xxml' => 'application/xml',
            'xxo' => 'application/vnd.olpc-sugar',
            'xxop' => 'application/xop+xml',
            'xxpm' => 'image/x-xpixmap',

            'xxpr' => 'application/vnd.is-xpr',
            'xxps' => 'application/vnd.ms-xpsdocument',
            'xxsl' => 'application/xml',
            'xxslt' => 'application/xslt+xml',
            'xxsm' => 'application/vnd.syncml+xml',
            'xxspf' => 'application/xspf+xml',
            'xxul' => 'application/vnd.mozilla.xul+xml',
            'xxvm' => 'application/xv+xml',
            'xxvml' => 'application/xv+xml',

            'xxwd' => 'image/x-xwindowdump',
            'xxyz' => 'chemical/x-xyz',
            'xzaz' => 'application/vnd.zzazz.deck+xml',
            'xzip' => 'application/zip',
            'xzmm' => 'application/vnd.handheld-entertainment+xml',
        );

    /**
     * Extend list of MIME types if needed from config
     */
    public function __construct()
    {
        $nodes = Mage::getConfig()->getNode('global/mime/types');
        if ($nodes) {
            $nodes = (array)$nodes;
            foreach ($nodes as $key => $value) {
                $this->_mimeTypes[$key] = $value;
            }
        }
    }

    /**
     * Get MIME type by file extension from list of pre-defined MIME types
     *
     * @param $ext
     * @return string
     */
    public function getMimeTypeByExtension($ext)
    {
        $type = 'x' . $ext;
        if (isset($this->_mimeTypes[$type])) {
            return $this->_mimeTypes[$type];
        }
        return 'application/octet-stream';
    }

    /**
     * Get all MIME Types
     *
     * @return array
     */
    public function getMimeTypes()
    {
        return $this->_mimeTypes;
    }

    /**
     * Get array of MIME types associated with given file extension
     *
     * @param array|string $extensionsList
     * @return array
     */
    public function getMimeTypeFromExtensionList($extensionsList)
    {
        if (is_string($extensionsList)) {
            $extensionsList = array_map('trim', explode(',', $extensionsList));
        }

        return array_map(array($this, 'getMimeTypeByExtension'), $extensionsList);
    }

    /**
     * Get post_max_size server setting
     *
     * @return string
     */
    public function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }

    /**
     * Get upload_max_filesize server setting
     *
     * @return string
     */
    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    /**
     * Get max upload size
     *
     * @return mixed
     */
    public function getDataMaxSize()
    {
        return min($this->getPostMaxSize(), $this->getUploadMaxSize());
    }

    /**
     * Get maximum upload size in bytes
     *
     * @return int
     */
    public function getDataMaxSizeInBytes()
    {
        $iniSize = $this->getDataMaxSize();
        $size = substr($iniSize, 0, strlen($iniSize)-1);
        $parsedSize = 0;
        switch (strtolower(substr($iniSize, strlen($iniSize)-1))) {
            case 't':
                $parsedSize = $size*(1024*1024*1024*1024);
                break;
            case 'g':
                $parsedSize = $size*(1024*1024*1024);
                break;
            case 'm':
                $parsedSize = $size*(1024*1024);
                break;
            case 'k':
                $parsedSize = $size*1024;
                break;
            case 'b':
            default:
                $parsedSize = $size;
                break;
        }
        return (int)$parsedSize;
    }

}
