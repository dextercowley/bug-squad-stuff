#! /usr/local/bin/php -c /usr/local/lib/php-no-xcache -v
<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__DIR__) . '/public_html');
//define('JPATH_BASE', '/Users/louis/Sites/joomla/developer');
define( 'DS', DIRECTORY_SEPARATOR );

// Joomla framework path definitions
define('JPATH_ROOT',                    JPATH_BASE);
define('JPATH_SITE',                    JPATH_ROOT);
define('JPATH_CONFIGURATION',   		JPATH_ROOT);
define('JPATH_THEMES',                  JPATH_ROOT.DS.'templates');
define('JPATH_LIBRARIES',               JPATH_ROOT.DS.'libraries');
define('JPATH_PLUGINS',                 JPATH_ROOT.DS.'plugins');

// Needed to deal with the JApplicationHelper::getClientInfo() hijack.
define('JPATH_ADMINISTRATOR',   JPATH_ROOT.DS.'administrator');
define('JPATH_INSTALLATION',    JPATH_ROOT.DS.'installation');

// System Checks
@set_magic_quotes_runtime(0);
@ini_set('zend.ze1_compatibility_mode', '0');

// System includes
require_once(JPATH_LIBRARIES.DS.'import.php');

// Joomla! library imports
jimport('joomla.application.menu');
jimport('joomla.user.user');
jimport('joomla.environment.uri');
jimport('joomla.html.html');
jimport('joomla.utilities.utility');
jimport('joomla.event.event');
jimport('joomla.event.dispatcher');
jimport('joomla.language.language');
jimport('joomla.utilities.string');
jimport('joomla.plugin.helper');
jimport('joomla.utilities.date');
jimport('joomla.log.log');
jimport('legacy.error.error');

// Load the configuration file
require_once(JPATH_CONFIGURATION.DS.'configuration.php');

// Instantiate the configuration object and set the error reporting.
$config = JFactory::getConfig();
$config->loadObject(new JConfig);
if ($config->get('error_reporting') == 0) {
        error_reporting(0);
}
elseif ($config->get('error_reporting') > 0) {
        // Verbose error reporting.
        error_reporting($config->get('error_reporting'));
}
        ini_set('display_errors', 1);


// Set error handling levels
JError::setErrorHandling( E_ERROR, 'echo');
JError::setErrorHandling( E_WARNING, 'echo' );
JError::setErrorHandling( E_NOTICE, 'echo' );

/*
 * Handle the arguments
 */
$args = $_SERVER['argv'];

// Remove the file
array_shift($args);

// Get the command
$command = array_shift($args);
switch (strtolower($command))
{
    case 'sync' :

		// Get a tracker sync method object.
        require ('methods/sync.php');
        $method = new TrackerSyncMethod();

		// Unused code to get the id of the tracker to sync.
        $trackerId = null;
        if (!empty($args[0]) and (strpos($args[0], '--tracker=') !== false)) {
                $trackerId = array_shift($args);
                $trackerId = intval(str_replace('--tracker=', '', $trackerId));
        }

		// Run the method.
        $method->run($trackerId);
        break;

    case 'filefix' :

		// Get a file fix method object.
        require ('methods/filefix.php');
        $method = new FileFixMethod();

		// Unused code to get the id of the tracker to sync.
        $trackerId = null;
        if (!empty($args[0]) and (strpos($args[0], '--tracker=') !== false)) {
                $trackerId = array_shift($args);
                $trackerId = intval(str_replace('--tracker=', '', $trackerId));
        }

		// Run the method.
        $method->run($trackerId);
        break;

    case 'help' :
    default :

            $subcmd = 'main';
            if (isset($args[0]) and in_array($args[0], $commands)) {
                    $subcmd = array_shift($args);
            }

            include 'help/'.$subcmd.'.txt';
            break;
}

exit();
