<?php
/**
 * @package		PhocaCheckIP content plugin
 * @author		ConseilGouz
 * @copyright	Copyright (C) 2025 ConseilGouz. All rights reserved.
 * @license		GNU/GPL v3; see LICENSE.php
 **/

namespace ConseilGouz\Plugin\Content\Phocacheckip\Extension;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use ConseilGouz\CGSecure\Helper\Cgipcheck;

if (!ComponentHelper::isEnabled('com_phocadownload', true)) {
    return Factory::getApplication()->enqueueMessage(Text::_('CG_PHOCADOWNLOAD_NOT_INSTALLED_ON_YOUR_SYSTEM'), Text::_('CG_PHOCADOWNLOAD_ERROR'));
}

class Phocacheckip extends CMSPlugin implements SubscriberInterface
{
    public $myname = 'PhocaDownloadCGSecure';
    public $mymessage = 'PhocaDownload : hide to spammer...';
    public $cgsecure_params;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare'   => 'onContentPrepare',
        ];
    }

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
        $this->cgsecure_params = Cgipcheck::getParams();

    }
    public function onContentPrepare($event) //($context, &$article, &$params, $page = 0)
    {
        $context = $event[0];
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }
        $article = $event[1];
        // check phocadownload tags
        $regex_all		= '/{phocadownload\s*.*?}/si';
        $matches 		= array();
        $count_matches	= preg_match_all($regex_all, $article->text, $matches, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
        if ($count_matches != 0) {
            $spammer = Cgipcheck::check_spammer($this, $this->myname.' : hide links');
            if (!$spammer) {
                return;
            } // everything OK => exit
            for ($i = 0; $i < $count_matches; $i++) { // spammer : replace shortcodes
                $article->text = preg_replace($regex_all, Text::_('CG_DOWNLOAD_NOT_ALLOWED'), $article->text, 1);
            }
        }
        return true;
    }
}
