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
            <th>Column A</th><th>Column B</th><th>Column C</th><th>Column D</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>8.3</td><td>70</td><td>10.3</td><td>100</td></tr>
        <tr><td>8.6</td><td>65</td><td>10.3</td><td>125</td></tr>
        <tr><td>8.8</td><td>63</td><td>10.2</td><td>106</td></tr>
        <tr><td>10.5</td><td>72</td><td>16.4</td><td>162</td></tr>
        <tr><td>11.1</td><td>80</td><td>22.6</td><td>89</td></tr>
    </tbody>
    <tfoot>
        <tr>
            <td>Row 1</td><td>Row 2</td><td>Row 3</td><td>Row 4</td><td>Row 5</td>
        </tr>
    </tfoot>
</table>