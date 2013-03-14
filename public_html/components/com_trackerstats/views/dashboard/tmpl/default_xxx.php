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
?>

<table id="chart">
    <thead>
        <tr>
            <th>Column A</th>
        </tr>
    </thead>
    <tbody>
    	<tr><td>0</td></tr>
        <tr><td>8.3</td></tr>
        <tr><td>8.6</td></tr>
        <tr><td>8.8</td></tr>
        <tr><td>10.5</td></tr>
        <tr><td>11.1</td></tr>

    </tbody>
    <tfoot>
        <tr>
            <td>Row 1</td><td>Row 2</td><td>Row 3</td><td>Row 4</td><td>Row 5</td><td></td>
        </tr>
    </tfoot>
</table>