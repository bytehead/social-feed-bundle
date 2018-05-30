<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 pdir / digital agentur
 * @package social-feed-bundle
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace Pdir\SocialFeedBundle;

class ListingElement extends \ContentElement
{
    /**
     * List Template
     * @var string
     */
    protected $strTemplate = 'ce_socialfeed_list';

    /**
     * Item Template
     * @var string
     */
    protected $strItemTemplate = 'ce_socialfeed_item';

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Social Feed LIST ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
            return $objTemplate->parse();
        }

        // Return if there is no facebook id
        /*if (!$this->pdir_sf_facebook_id) {
            return 'Kein Netzwerk angegeben!';
        }*/

        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        if (version_compare(VERSION, '4', '>='))
        {
            $assetsDir = 'web/bundles/pdirsocialfeed';
        } else {
            $assetsDir = 'system/modules/socialFeed/assets';
        }

        // Assets
        if(!$this->pdir_md_removeModuleJs)
        {
            $GLOBALS['TL_BODY'][] = '<script src="assets/moment/min/moment.min.js"></script>';
            $GLOBALS['TL_BODY'][] = '<script src="assets/moment/locale/de.js"></script>';
            $combiner = new \Combiner();
            $combiner->add('/vendor/pdir/codebird-js/codebird.js');
            $combiner->add('/vendor/pdir/do-t/doT.min.js');
            $combiner->add('/vendor/pdir/social-feed/js/jquery.socialfeed.js');
            if($this->pdir_sf_enableMasonry) $combiner->add('/vendor/pdir/social-feed/js/masonry.pkgd.min.js');
            $GLOBALS['TL_BODY'][] = '<script src="'.$combiner->getCombinedFile().'"></script>';
        }
        if(!$this->pdir_md_removeModuleCss)
        {
            $combiner = new \Combiner();
            $combiner->add('/vendor/pdir/social-feed/css/jquery.socialfeed.css');
            $GLOBALS['TL_CSS'][] = $combiner->getCombinedFile();
            $GLOBALS['TL_CSS'][] = '/vendor/pdir/social-feed/font-awesome/css/font-awesome.min.css';
        }

        // Parameters
        $this->Template->textLength = $this->pdir_sf_text_length ? $this->pdir_sf_text_length : 400;
        $this->Template->mediaMinWidth = $this->pdir_sf_media_min_width;
        $this->Template->showMedia = ($this->pdir_sf_show_media == '1') ? 'true' : 'false';
        $this->Template->updatePeriod = $this->pdir_sf_update_period ? $this->pdir_sf_update_period : 5000;
        $this->Template->dateFormat = $this->pdir_sf_date_format ? $this->pdir_sf_date_format : 'll';
        $this->Template->dateLocale = $this->pdir_sf_date_locale ? $this->pdir_sf_date_locale : 'de';
        $this->Template->hideFilters = $this->pdir_sf_hideFilters ? $this->pdir_sf_hideFilters : false;
        $this->Template->cacheTime = $this->pdir_sf_cacheTime ? $this->pdir_sf_cacheTime : 0;
        $this->Template->masonryWidth = $this->pdir_sf_masonryWidth;
        $this->Template->enableMasonry = $this->pdir_sf_enableMasonry;

		// Shuffle
        $this->Template->listShuffle = ($this->pdir_sf_list_shuffle) ? 'true' : 'false';

        // Facebook
        $this->Template->fbStatus = $this->pdir_sf_facebook_status;
        $this->Template->fbToken = $this->pdir_sf_facebook_token;
        $this->Template->fbAccounts = '[\'' . str_replace(',', '\',\'', $this->pdir_sf_facebook_accounts) . '\']';
        $this->Template->fbLimit = $this->pdir_sf_facebook_limit;

        // Google
        $this->Template->gpStatus = $this->pdir_sf_google_plus_status;
        $this->Template->gpToken = $this->pdir_sf_google_plus_token;
        $this->Template->gpAccounts = $this->pdir_sf_google_plus_accounts;
        $this->Template->gpLimit = $this->pdir_sf_google_plus_limit;

        // set item template
        $itemTpl = 'share/' . $this->strItemTemplate . '.html5';

        $this->Template->itemTemplate = $itemTpl;

        // Debug mode
		if($this->pdir_sf_enableDebugMode)
		{
			$this->Template->debug = true;
			$this->Template->version = SocialFeedSetup::VERSION;
		}
    }
}