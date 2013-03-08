<?php
/**
 * @version		$Id: gforgelegacy.php 439 2010-06-29 21:12:25Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Connector class to a GForge Advanced Server Legacy SOAP API.
 *
 * @see      http://joomlacode.org/gf/xmlcompatibility/soap/
 *
 * @package  Joomla.Code
 * @since    1.0
 */
class GForgeLegacy
{
	/**
	 * @var    SoapClient  The client object connected to the GForge instance.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    string  The session hash for the SOAP session.
	 * @since  1.0
	 */
	protected $sessionhash;

	/**
	 * @var    string  The username for the signed in session.
	 * @since  1.0
	 */
	protected $username;

	/**
	 * Object constructor.  Creates the connection to the GForge site instance.
	 *
	 * @param   string  $site     The URL to the gforge instance.
	 * @param   array   $options  The SOAP options for the connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __construct($site, $options = array())
	{
		// Attempt to connect to the SOAP gateway.
		$this->client = new SoapClient($site.'/xmlcompatibility/soap/?wsdl', $options);

		// Check for an error.
		if (!$this->client) {
			exit('Unable to connect to GForge instance at '.$site);
		}
	}

	/**
	 * Object destructor.  Signs out and closes the connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __destruct()
	{
		// Check to see if the connection is live.
		if ($this->client) {

			// Check to see if we are signed in.
			if ($this->sessionhash) {
				$this->logout();
			}

			// Kill the connection.
			unset($this->client);
		}
	}

	/**
	 * Method to sign into GForge using password authentication.
	 *
	 * @param   string   $username  The username for the account to login.
	 * @param   string   $password  The password for the account to login.
	 *
	 * @return	boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function login($username, $password)
	{
		try {
			// Attempt to sign into the account and get the session hash.
			$sessionhash = $this->client->login($username, $password);

			// Cache the session hash and username for later use.
			$this->sessionhash = $sessionhash;
			$this->username = $username;

			return true;
		}
		catch (SoapFault $e)
		{
			die ('Login Failed: '.$e->faultstring);
			return false;
		}
	}

	/**
	 * Method to sign out of GForge.
	 *
	 * @return	boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function logout()
	{
		try {
			// Attempt to sign out.
			$this->client->logout($this->sessionhash);
			$this->sessionhash = null;
			$this->username = null;

			return true;
		}
		catch (SoapFault $e)
		{
			echo 'Logout Failed: '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get user data by username.
	 *
	 * @param   string  $username  The optional username to get user data for, defaults to the user
	 *                             signed into the current session.
	 *
	 * @return  mixed   Boolean false on failure, user data object on success.
	 *
	 * @since   1.0
	 */
	public function getUser($username = null)
	{
		try {
			// Attempt to get the user object by the username or "unix name" in GForge speak.
			$user = $this->client->getUserByUnixName($this->sessionhash, $username ? $username : $this->username);

			return $user;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get user '.($username ? $username : $this->username).': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get an array of tracker file changes by id.
	 *
	 * @param   int     $itemId     The tracker item id for which to get the files array.
	 * @param   int     $trackerId  The tracker id in which the item resides.
	 * @param   int     $projectId  The project id in which the tracker resides.
	 *
	 * @return  mixed   Boolean false on failure, tracker item files data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItemFiles($itemId, $trackerId, $projectId)
	{
		try {
			// Attempt to get the files data array by the tracker item id.
			$changes = $this->client->getArtifactFiles($this->sessionhash, $projectId, $trackerId, $itemId);

			return $changes;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get files for tracker item '.$itemId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a file data object by id.
	 *
	 * @param   int     $fileId     The file id for which to get the files array.
	 * @param   int     $itemId     The tracker item id to which the file is attached.
	 * @param   int     $trackerId  The tracker id in which the item resides.
	 * @param   int     $projectId  The project id in which the tracker resides.
	 *
	 * @return  mixed   Boolean false on failure, tracker item files data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItemFile($fileId, $itemId, $trackerId, $projectId)
	{
		try {
			// Attempt to get the file data object by the file id.
			$changes = $this->client->getArtifactFileData($this->sessionhash, $projectId, $trackerId, $itemId, $fileId);

			return $changes;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get  '.$fileId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of client functions.
	 *
	 * @return  mixed   Boolean false on failure, array of functions on success.
	 *
	 * @since   1.0
	 */
	protected function getClientFunctions()
	{
		try {
			// Attempt to get the client functions.
			$functions = $this->client->__getFunctions();

			return $functions;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get functions: '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of client types.
	 *
	 * @return  mixed   Boolean false on failure, array of types on success.
	 *
	 * @since   1.0
	 */
	protected function getClientTypes()
	{
		try {
			// Attempt to get the client types.
			$functions = $this->client->__getTypes();

			return $functions;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get types: '.$e->faultstring."\n";
			return false;
		}
	}
}