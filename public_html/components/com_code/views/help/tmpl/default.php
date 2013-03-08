<?php
/**
 * @version		$Id: default.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the JavaScript behaviors.
JHtml::_('behavior.mootools');
JHtml::script('status.js', 'components/com_code/media/js/');

// Load the CSS stylesheets.
JHtml::stylesheet('default.css', 'components/com_code/media/css/');
?>
<h1>
	Joomla! Code Snippets Help
</h1>

<h2>
	What is Joomla! Code Snippets?
</h2>
<p>
	Joomla! Code Snippets is a tool to help you collaborate on developing and debugging code with other people.  It is not always
	convenient to paste code snippets into instant messenger clients, IRC clients or even emails to share with others.  Doing so
	often results in a loss of formatting, confusing line wrapping and character conversion that makes life difficult.
</p>

<h2>
	How do I use it?
</h2>
<p>
	Most people use it like:

	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_snippets&view=submit'); ?>">Post</a> a code snippet and get the
			resulting URL like <strong>http://developer.joomla.org/snippets/1234</strong>.
		</li>
		<li>
			Paste the URL into an IRC, instant messenger, or email conversation.
		</li>
		<li>
			Someone responds by reading and perhaps submitting a modification of your snippet.
		</li>
		<li>
			You then view the modification, maybe using the built in diff tool to help locate the changes.
		</li>
	</ul>
</p>

<h2>
	How can I view the differences between two snippets?
</h2>
<p>
	When you view a snippet, if it is based on another snippet, you will see a link titled <strong>"View Diff"</strong>.  Clicking
	this link will allow you to view the differences between the current snippet and the snippet upon which it is based.
</p>
<p>
	This is a powerful feature, great for seeing exactly what lines someone changed.
</p>

<h2>
	What software is Joomla! Code Snippets based on?
</h2>
<p>
	Joomla! Code Snippets is based on <a href="http://pastebin.org">pastebin</a>.  You can find news about pastebin by visiting
	<a title="View pastebin blog" href="http://blog.dixo.net/category/pastebin/">Paul Dixon's blog</a>.
</p>

<h2>
	Can I get the source?
</h2>
<p>
	The source code is available under a GPL licence.  You can
	<a title="Pastebin source code, 245Kb" href="<?php echo $CONF['this_script']; ?>pastebin.tar.gz">download it here</a>.
</p>
