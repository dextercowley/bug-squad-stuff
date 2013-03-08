<?php
/**
 * @version		$Id: view.html.php 418 2010-06-25 01:27:48Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.view');

/**
 * The HTML Joomla Code branch view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewBranch extends JView
{
	/**
	 * Display the view
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display($tpl = null)
	{
		$state	= $this->get('State');
		$item	= $this->get('Item');
		$builds	= $this->get('Items');
		$page	= $this->get('Pagination');
		$build	= $this->get('LatestBuild');
		$user  = JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the title to the breadcrumbs.
		JFactory::getApplication()->getPathWay()->addItem($item->title, JRoute::_('index.php?option=com_code&view=branch&branch_path='.$item->path));

		// Add the feed links to the document head.
		$this->document->addHeadLink(JRoute::_('index.php?option=com_code&view=branch&branch_path='.$item->path.'&format=feed'), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
//		$this->document->addHeadLink(JRoute::_('index.php?option=com_code&view=branch&branch_path='.$item->path.'&format=feed&type=atom'), 'alternate', 'rel', array('type' => 'application/atom+xml', 'title' => 'Atom 1.0'));

		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('build', $build);
		$this->assignRef('pagination', $page);
		$this->assignRef('builds', $builds);
		$this->assignRef('user', $user);

		parent::display($tpl);
	}
}