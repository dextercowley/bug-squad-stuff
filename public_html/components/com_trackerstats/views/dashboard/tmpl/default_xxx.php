<?php
/**
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for joomaprosubs
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on trackerstats permissions.
$canEdit = $user->authorise('core.edit', 'com_trackerstats');

$listOrder	= '';
$listDirn	= '';
$listFilter = '';
$jsonSource = $this->baseurl . "/components/com_trackerstats/views/dashboard/tmpl/getgraphdata.php";
?>

 <h1>Test Title</h1>
 <div id="chart1" style="width:600px; height:250px;"></div>