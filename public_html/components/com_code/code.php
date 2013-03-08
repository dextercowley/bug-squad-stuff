<?php
/**
 * @version		$Id: code.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.controller');
//require_once JPATH_COMPONENT.'/helpers/route.php';
//require_once JPATH_COMPONENT.'/helpers/query.php';

$controller = JController::getInstance('Code');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
