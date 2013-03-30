<?php
/**
 * @version		$Id: user.php 404 2010-06-17 01:48:45Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.database.table');

/**
 * Code tracker issue table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableUser extends JTable
{
	/**
	 * Primary key for the users table.
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $id = 0;

	/**
	 * The users real name (or nickname).
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $name;

	/**
	 * The login name.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $username;

	/**
	 * The email.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $email;

	/**
	 * MD5 encrypted password
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $password;

	/**
	 * Description
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $usertype;

	/**
	 * Description
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $block = 0;

	/**
	 * Description
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $sendEmail;

	/**
	 * Date of user registration.
	 *
	 * @var		datetime
	 * @since	1.0
	 */
	public $registerDate;

	/**
	 * Date of the last site visit.
	 *
	 * @var		datetime
	 * @since	1.0
	 */
	public $lastvisitDate;

	/**
	 * Activation hash.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $activation;

	/**
	 * The user's settings.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $params;

	/**
	 * User's first name.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $first_name;

	/**
	 * User's last name.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $last_name;

	/**
	 * User's address - line 1.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $address;

	/**
	 * User's address - line 2.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $address2;

	/**
	 * User's city.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $city;

	/**
	 * User's region.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $region;

	/**
	 * User's country.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $country;

	/**
	 * User's postal code.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $postal_code;

	/**
	 * User's longitude.
	 *
	 * @var		float
	 * @since	1.0
	 */
	public $longitude = 0.0;

	/**
	 * User's latitude.
	 *
	 * @var		float
	 * @since	1.0
	 */
	public $latitude = 0.0;

	/**
	 * User's phone number.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $phone;

	/**
	 * User's agreed to terms of service flag.
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $agreed_tos = 0;

	/**
	 * User's signed CLA flag.
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $signed_jca = 0;

	/**
	 * User's signed CLA flag.
	 *
	 * @var		string
	 * @since	1.0
	 */
	public $jca_document_id;

	/**
	 * JoomlaCode legacy user id.
	 *
	 * @var		integer
	 * @since	1.0
	 */
	public $jc_user_id = 0;

	/**
	 * Constructor.
	 *
	 * @param	object	A database connector object.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__users', 'id', $db);
	}

	/**
	 * Method to bind an object or array to the object.
	 *
	 * @param	mixed	Object or associative array.
	 * @param	mixed	Space delimited string or array of fields to ignore when binding.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function bind($source, $ignore = '')
	{
		// If the params field of the source array is an array, convert it to an INI string.
		if (is_array($source) && array_key_exists('params', $source) && is_array($source['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($source['params']);
			$source['params'] = $registry->toString();
		}

		// Optionally combine the first and last names into the name.
		if (!empty($source['first_name']) && !empty($source['last_name'])) {
			$source['name'] = $source['first_name'].' '.$source['last_name'];
		}

		// Special casees for the agreement flags.
		if (empty($source['agreed_tos'])) {
			$source['agreed_tos'] = 0;
		}
		if (empty($source['signed_jca'])) {
			$source['signed_jca'] = 0;
		}

		// Execute the parent bind method.
		return parent::bind($source, $ignore);
	}

	/**
	 * Method to perform data validation and sanitization before storage.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function check()
	{
		// Ensure there is a name.
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('Please enter your name.'));
			return false;
		}

		// Ensure there is a login name.
		if (trim($this->username) == '')
		{
			$this->setError(JText::_('Please enter a user name.'));
			return false;
		}

		// Ensure the login name is valid.
		if (eregi("[<>\"'%;()&]", $this->username) || (strlen(utf8_decode($this->username )) < 2))
		{
			$this->setError(JText::sprintf('VALID_AZ09', JText::_( 'Username' ), 2));
			return false;
		}

		// Ensure the email address is valid.
		jimport('joomla.mail.helper');
		if ((trim($this->email) == "") || !JMailHelper::isEmailAddress($this->email))
		{
			$this->setError(JText::_('WARNREG_MAIL'));
			return false;
		}

		// Set the registration timestamp if necessary.
		if ($this->registerDate == null)
		{
			$now = JFactory::getDate();
			$this->registerDate = $now->toMySQL();
		}

		// Ensure the login name is not already being used.
		$this->_db->setQuery(
			'SELECT id' .
			' FROM #__users' .
			' WHERE username = '.$this->_db->quote($this->username) .
			' AND id <> '.(int) $this->id
		);
		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval( $this->id ))
		{
			$this->setError(JText::_('WARNREG_INUSE'));
			return false;
		}

		// Ensure the email is not already being used.
		$this->_db->setQuery(
			'SELECT id' .
			' FROM #__users' .
			' WHERE email = '.$this->_db->quote($this->email) .
			' AND id <> '.(int) $this->id
		);
		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('WARNREG_EMAIL_INUSE'));
			return false;
		}

		return true;
	}

	public function loadByLegacyId($legacyId)
	{
		// Look up the user id based on the legacy id.
		$this->_db->setQuery(
			'SELECT user_id' .
			' FROM #__code_users' .
			' WHERE jc_user_id = '.(int) $legacyId
		);
		$userId = (int) $this->_db->loadResult();

		if ($userId) {
			return $this->load($userId);
		}
		else {
			return false;
		}
	}

	public function loadByEmail($email)
	{
		// Look up the user id based on the email.
		$this->_db->setQuery(
			'SELECT id' .
			' FROM #__users' .
			' WHERE email = '.$this->_db->quote($email)
		);
		$userId = (int) $this->_db->loadResult();

		if ($userId) {
			return $this->load($userId);
		}
		else {
			return false;
		}
	}

	/**
	 * Method to load the user data from the database and bind it to the object.
	 *
	 * @param	integer	The primary key of the user record to load.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function load($userId = null)
	{
		// Get the primary key.
		$k = $this->_tbl_key;
		if ($userId !== null) {
			$this->$k = $userId;
		}
		$userId = $this->$k;

		// If no primary key is set return false.
		if ($userId === null) {
			return false;
		}

		// Reset the object.
		$this->reset();

		// Load the core data fields.
		$this->_db->setQuery(
			'SELECT *' .
			' FROM '.$this->_tbl .
			' WHERE '.$this->_tbl_key.' = '.(int) $userId
		);
		if ($result = $this->_db->loadAssoc())
		{
			if ($this->bind($result))
			{
				// Load the extended data fields.
				$this->_db->setQuery(
					'SELECT *' .
					' FROM #__code_users' .
					' WHERE user_id = '.(int) $userId
				);
				if ($result = $this->_db->loadAssoc()) {
					return $this->bind($result);
				}
				else
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			else {
				return false;
			}
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Method to store the object data to the database.
	 *
	 * @param	boolean	True to update null fields.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function store($updateNulls = false)
	{
		// Get an ACL object.
		$acl = JFactory::getACL();

		// Get the core and extended data objects.
		$core = $this->_getCoreObject();
		$extd = $this->_getExtendedObject($core);

		$k = $this->_tbl_key;
		$key =  $this->$k;

		if ($key)
		{
			// Only process extended table, not #__users
			// Determine if the extended table has a row.
			$this->_db->setQuery(
				'SELECT user_id' .
				' FROM #__code_users' .
				' WHERE user_id = '.(int) $key
			);
			// If the extended record exists update it.
			if ($this->_db->loadResult()) {
				$ret = $this->_db->updateObject('#__code_users', $extd, 'user_id', $updateNulls);
			}
			// If the extended record does not exist insert it.
			else {
				$extd->user_id = 0;
				$ret = $this->_db->insertObject('#__code_users', $extd, 'user_id');
			}

		}
		else
		{
			$this->$k = 0;
			// Only process the #__code_users table
			// Set the primary key and insert the extended data record.
			$extd->user_id = 0;
			$ret = $this->_db->insertObject('#__code_users', $extd, 'user_id');
		}

		if(!$ret)
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Method to delete the user data from the database.
	 *
	 * @param	integer	The primary key of the user record to delete.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function delete($userId = null)
	{
		// Get an ACL object.
		$acl = JFactory::getACL();

		// Set the primary key if passed as an argument.
		$k = $this->_tbl_key;
		if ($userId) {
			$this->$k = (int) $userId;
		}

		// Remove the record from the users table.
		$this->_db->setQuery(
			'DELETE FROM '.$this->_tbl .
			' WHERE '.$this->_tbl_key.' = '.(int) $this->$k
		);
		if ($this->_db->query())
		{
			// Remove the extended user data.
			$this->_db->setQuery(
				'DELETE FROM #__code_users' .
				' WHERE user_id = '.(int) $this->$k
			);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Remove the user group mappings.
			$this->_db->setQuery(
				'DELETE FROM #__user_usergroup_map' .
				' WHERE user_id = '.(int) $userId
			);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Remove any message information from the database for the user.
			$this->_db->setQuery(
				'DELETE FROM #__messages_cfg' .
				' WHERE user_id = '.(int) $this->$k
			);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$this->_db->setQuery(
				'DELETE FROM #__messages' .
				' WHERE user_id_to = '.(int) $this->$k
			);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Updates last visit time of a user record.
	 *
	 * @param	integer	The timestamp to set for the last visit date.
	 * @param	integer	The primary key value of the user to update.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function setLastVisit($time = null, $id = null)
	{
		// Get the primary key value to update.
		if (is_null($id))
		{
			if (isset($this)) {
				$id = $this->id;
			}
			else {
				jexit('WARNMOSUSER');
			}
		}

		// Get a JDate object from the time value (defaults to current time).
		$date = JFactory::getDate($time);

		// Update the last visit date for the user record.
		$this->_db->setQuery(
			'UPDATE '.$this->_tbl .
			' SET lastvisitDate = '.$this->_db->quote($date->toMySQL()) .
			' WHERE id = '.(int) $id
		);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to get a data object for the core users table.
	 *
	 * @return	object	Data object for the core users table.
	 * @since	1.0
	 */
	protected function _getCoreObject()
	{
		$obj = new stdClass;
		$obj->id			= $this->id;
		$obj->name			= $this->name;
		$obj->username		= $this->username;
		$obj->email			= $this->email;
		$obj->password		= $this->password;
		$obj->usertype		= $this->usertype;
		$obj->block			= $this->block;
		$obj->sendEmail		= $this->sendEmail;
		$obj->registerDate	= $this->registerDate;
		$obj->lastvisitDate	= $this->lastvisitDate;
		$obj->activation	= $this->activation;
		$obj->params		= $this->params;

		return $obj;
	}

	/**
	 * Method to get a data object for the extended users table.
	 *
	 * @return	object	Data object for the extended users table.
	 * @since	1.0
	 */
	protected function _getExtendedObject($core)
	{
		// Get the array diff of the table object properties excluding the core table properties.
		$extended = array_diff_assoc($this->getProperties(), get_object_vars($core));

		// Set the primary key.
		$k = $this->_tbl_key;
		$extended['user_id'] = $this->$k;

		return (object) $extended;
	}
}
