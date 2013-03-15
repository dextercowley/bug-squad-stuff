<?php
/**
 * version $Id: view.html.php 287 2011-11-11 23:13:33Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the JoomproSubs component
 *
 */
class TrackerstatsViewDashboard extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Check whether category access level allows access.
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();

		$this->state = $state;
		$this->items = $items;
		$this->params = $params;
		$this->pagination = $pagination;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu()->getActive();
		$pathway = $app->getPathway();
		$title = null;

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_TRACKERSTATS_DASHBOARD_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		// Add graphing js
		JHtml::_('behavior.framework');
		$this->document->addScript($this->baseurl . '/components/com_trackerstats/js/jquery-1.9.1.min.js', 'text/javascript', false);
		$this->document->addScript($this->baseurl . '/components/com_trackerstats/js/noconflict.js', 'text/javascript', false);
		$this->document->addScript($this->baseurl . '/components/com_trackerstats/js/jquery.flot.js', 'text/javascript', true);
		$this->document->addScript($this->baseurl . '/components/com_trackerstats/js/getflotdata.js', 'text/javascript', true);

		$drawGraph = "
			var data,data1,options,chart;
			data1 = [ [1,4],[2,5],[3,6],[4,9],[5,7],[6,6],[7,2],[8,1],[9,3] ];
			data = [data1];
			options = {};
			jQuery(document).ready(function($){
				chart1 = $.plot($('#placeholder'),data,options);
			});
		";
// 		$this->document->addScriptDeclaration($drawGraph);

	}
} // end of class
