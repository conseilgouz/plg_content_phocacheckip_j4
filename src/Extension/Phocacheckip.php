<?php 
/**
 * @version		3.0.0
 * @package		PhocaCheckIP content plugin
 * @author		ConseilGouz
 * @copyright	Copyright (C) 2023 ConseilGouz. All rights reserved.
 * @license		GNU/GPL v2; see LICENSE.php
 **/
namespace ConseilGouz\Plugin\Content\Phocacheckip\Extension; 
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use ConseilGouz\CGSecure\Helper\Cgipcheck;

if (!ComponentHelper::isEnabled('com_phocadownload', true)) {
    return Factory::getApplication()->enqueueMessage(Text::_('CG_PHOCADOWNLOAD_NOT_INSTALLED_ON_YOUR_SYSTEM'),JText::_('CG_PHOCADOWNLOAD_ERROR'));
}

class Phocacheckip extends CMSPlugin
{	
    public $myname='PhocaDownloadCGSecure';
	public $mymessage='PhocaDownload : hide to spammer...';
	public $cgsecure_params;

	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->cgsecure_params = Cgipcheck::getParams();
		
	}
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		// check phocadownload tags
		$regex_one		= '/({phocadownload\s*)(.*?)(})/si';
		$regex_all		= '/{phocadownload\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		if ($count_matches != 0) {
			$spammer = Cgipcheck::check_spammer($this,$this->myname.' : hide links');
			if (!$spammer) return; // everything OK => exit
			for($i = 0; $i < $count_matches; $i++) { // spammer : replace shortcodes
			$article->text = preg_replace($regex_all, JText::_('CG_DOWNLOAD_NOT_ALLOWED'), $article->text, 1);
			}
		}
		return true;
	}
}
?>