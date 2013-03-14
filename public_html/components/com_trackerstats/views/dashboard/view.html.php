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
		$this->document->addScript($this->baseurl . '/js/moobargraph-min.js', 'text/javascript', true);
		$drawGraph = "
			window.addEvent('domready', function() {
 				var myGraph = new mooBarGraph({
					container: $('myGraph'),
					data: 		graphData
				});
			});
			var graphData = Array(
				[100],[125],[80]
			);
		";
		$this->document->addScriptDeclaration($drawGraph);
	}
} // end of class
