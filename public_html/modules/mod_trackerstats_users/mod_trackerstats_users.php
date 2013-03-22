<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_category
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the helper functions only once
require_once dirname(__FILE__).'/helper.php';
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$names = modTrackerstatsUsersHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_trackerstats_users', $params->get('layout', 'default'));

