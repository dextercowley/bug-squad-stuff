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

	<div id="header">
		<h2>Stacking</h2>
	</div>

	<div id="header">
		<h2>Categories</h2>
	</div>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" style="height: 400px; width: 700px;" class="demo-placeholder"></div>
		</div>

		<p>With the categories plugin you can plot categories/textual data easily.</p>

	</div>

	<div id="footer">
		Copyright &copy; 2007 - 2013 IOLA and Ole Laursen
	</div>