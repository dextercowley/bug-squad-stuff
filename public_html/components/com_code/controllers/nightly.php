<?php
/**
 * @version		$Id: nightly.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.controller');

/**
 * The Joomla Code Nightly Controller
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeControllerNightly extends JController
{
	protected $_params;

	/**
	 * Constructor
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct()
	{
		// Execute the parent constructor.
		parent::__construct();

		// Get the component/page parameters.
		$app = JFactory::getApplication();
		$this->_params = $app->getParams('com_code');
	}

	/**
	 * The display method should never be requested from the extended
	 * controller.  Throw an error page and exit gracefully.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display()
	{
		JError::raiseError(404, 'Resource Not Found');
	}

	public function scan()
	{
		// Verify the request token.
		$token = JRequest::getString('token', null, 'method');
		if ($token != '1q2w3e4r') {
			JError::raiseError(403, 'Access Forbidden');
		}

		// Get the branch model.
		$model = $this->getModel('Nightly');

		// Attempt to scan for available builds.
		if (!($success = $model->scanBuilds())) {
			JError::raiseError(500, JText::sprintf('COM_CODE_SCAN_NIGHTLY_BUILDS_FAILURE', $model->getError()));
		}

		echo JText::_('COM_CODE_SCAN_NIGHTLY_BUILDS_SUCCESS');
	}

	/**
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function download()
	{
		$file = JRequest::getCmd('file');

		// Build the path to the nightly packages.
		$path = dirname(JPATH_ROOT).'/packages/nightly';

		// Verify the file exists.
		if (!is_file($path.'/'.$file)) {
			JError::raiseError(404, 'Resource Not Found');
		}

		/*
		 * LOG DOWNLOAD
		 */

		// Send file to browser.
		header('Content-type: application/force-download');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-length: '.filesize($path.'/'.$file));
	    header('Content-disposition: attachment; filename="'.basename($path.'/'.$file).'"');
		header('Pragma: no-cache' );
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
	    readfile($path.'/'.$file);

		// Close the application.
		JFactory::getApplication()->close();
	}
}
