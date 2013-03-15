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
		<h2>AJAX</h2>
	</div>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" style="width:500px; height:300px;" class="demo-placeholder"></div>
		</div>

		<p>Example of loading data dynamically with AJAX. Percentage change in GDP (source: <a href="http://epp.eurostat.ec.europa.eu/tgm/table.do?tab=table&init=1&plugin=1&language=en&pcode=tsieb020">Eurostat</a>). Click the buttons below:</p>

		<p>The data is fetched over HTTP, in this case directly from text files. Usually the URL would point to some web server handler (e.g. a PHP page or Java/.NET/Python/Ruby on Rails handler) that extracts it from a database and serializes it to JSON.</p>

		<p>
			<button class="fetchSeries">First dataset</button>
			[ <a href="<?php echo $jsonSource;?>">see data</a> ]
			<span></span>
		</p>

		<p>
			<button class="fetchSeries">Second dataset</button>
			[ <a href="data-japan-gdp-growth.json">see data</a> ]
			<span></span>
		</p>

		<p>
			<button class="fetchSeries">Third dataset</button>
			[ <a href="data-usa-gdp-growth.json">see data</a> ]
			<span></span>
		</p>

		<p>If you combine AJAX with setTimeout, you can poll the server for new data.</p>

		<p>
			<button class="dataUpdate">Poll for data</button>
		</p>

	</div>

	<div id="footer">
		Copyright &copy; 2007 - 2013 IOLA and Ole Laursen
	</div>