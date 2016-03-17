<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_moodle
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Moodle Model
 *
 * @since  0.0.1
 */
class MoodleModelSessions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name',
				'date',
				'length',
				'city','field.city',
				'country','field.country',
				'language','field.language',
				'provider','field.provider',
				'region','filter.region',
				'solution','filter.solution',
				'access','filter.access'
			);
		}
		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   The field to order on.
	 * @param   string  $direction  The direction to order on.
	 *
	 * @return  void.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'date', $direction = 'asc')
	{
		// Initialise variables.
        $app = JFactory::getApplication();

		$region = $app->getUserStateFromRequest($this->context . '.filter.region', 'filter_region', '', 'string');
    	$this->setState('filter.region', $region);

    	$solution = $app->getUserStateFromRequest($this->context . '.filter.solution', 'filter_solution', '', 'string');
    	$this->setState('filter.solution', $solution);

    	$access = $app->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'string');
    	$this->setState('filter.access', $access);

		//Set list state ordering defaults
		parent::populateState($ordering, $direction);
	}

	/**
	 * Gets the list of users and adds expensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (empty($this->cache[$store]))
		{
			$items = parent::getItems();

			// Bail out on an error or empty list.
			if (empty($items))
			{
				$this->cache[$store] = $items;
				return $items;
			}

			// First pass: get list of the session id's and reset the counts.
			$sessionIds = array();

			foreach ($items as $item)
			{
				$sessionIds[] = (int) $item->id;
				$item->field_count = 0;
				$item->fields = array();
				$item->date_count = 0;
				$item->dates = array();
			}

			// Get the counts from the database only for the users in the list.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			// Join over the group mapping table.
			$query->select('data.sessionid, COUNT(data.fieldid) AS field_count')
				->from('#__moodle_facetoface_session_data AS data')
				->where('data.sessionid IN (' . implode(',', $sessionIds) . ')')
				->group('data.sessionid')
				// Join over the user groups table.
				->join('LEFT', '#__moodle_facetoface_session_field AS field ON field.id = data.fieldid')
				->where('field.showinsummary = 1');
			$db->setQuery($query);
			// Load the counts into an array indexed on the user id field.
			try
			{
				$sessionFields = $db->loadObjectList('sessionid');
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
			$query->clear()
				->select('dates.sessionid, COUNT(dates.id) AS date_count')
				->from('#__moodle_facetoface_sessions_dates AS dates')
				->where('dates.sessionid IN (' . implode(',', $sessionIds) . ')')
				->group('dates.sessionid');
			$db->setQuery($query);
			// Load the counts into an array indexed on the aro.value field (the user id).
			try
			{
				$sessionDates = $db->loadObjectList('sessionid');
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
			// Second pass: collect the group counts into the master items array.
			foreach ($items as &$item)
			{
				if (isset($sessionFields[$item->id]))
				{
					$item->field_count = $sessionFields[$item->id]->field_count;
					// Group_concat in other databases is not supported
					$item->fields = $this->_getSessionFields($item->id);
				}
				if (isset($sessionDates[$item->id]))
				{
					$item->date_count = $sessionDates[$item->id]->date_count;
					// Group_concat in other databases is not supported
					$item->dates = $this->_getSessionDates($item->id);
				}
			}
			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}

		return $this->cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
 
		// Create the base select statement.
		$query->select(
			$this->getState(
				'list.select',
				's.id, s.capacity'
			)
		);

		$query->from('#__moodle_facetoface_sessions AS s');

		$query->select('f.name as name');
		$query->join('INNER', '#__moodle_facetoface AS f ON f.id = s.facetoface');

		$query->select('c.id AS course_id, c.fullname AS course_name');
		$query->join('INNER', '#__moodle_course AS c ON c.id = f.course');

		$query->select('cat.id AS cat_id, cat.name AS cat_name');
		$query->join('LEFT', '#__moodle_course_categories AS cat ON cat.id = c.category');

		$query->select('IF (s.datetimeknown = 0, 0, from_unixtime(min(d.timestart))) as date, count(d.timestart) as length');
		$query->join('LEFT', '#__moodle_facetoface_sessions_dates AS d ON d.sessionid = s.id');

		$query->select('m.availability');
		//TO DO: Change module = 25 to map to facetoface module id
		$query->join('LEFT', '#__moodle_course_modules AS m ON m.instance = f.id AND m.module = 25');

		$listOrder = $db->escape($this->state->get('list.ordering',  'default_sort_column'));
		$listDirn  = $db->escape($this->state->get('list.direction', 'ASC'));

		//ORDER BY CUSTOM FIELD ELSE REGULAR
		$pieces = explode(".", $listOrder);
		if ($pieces[0] == 'field') {
			$query->select('x.data as '.$pieces[1]);
			$query->join('LEFT', '#__moodle_facetoface_session_data AS x ON x.sessionid = s.id');
			$query->join('LEFT', '#__moodle_facetoface_session_field AS z ON z.id = x.fieldid');
			$query->where('z.shortname ="'.$pieces[1].'"');
			$query->order($pieces[1].' '.$listDirn);
		} else {
			$query->order($listOrder.' '.$listDirn);
		}

		// Filter solution
		$solution = $db->escape($this->getState('filter.solution'));
		if (!empty($solution)) {
			$query->select('fs1.data');
			$query->join('LEFT', '#__moodle_facetoface_session_data AS fs1 ON fs1.sessionid = s.id');
			$query->join('LEFT', '#__moodle_facetoface_session_field AS fs2 ON fs2.id = fs1.fieldid');
			$query->where('fs2.shortname ="solution"');
			$query->where('(fs1.data="'.$solution.'")');
		}

		// Filter support
		$access = $db->escape($this->getState('filter.access'));
		if (!empty($access)) {
			$query->select('fs3.data');
			$query->join('LEFT', '#__moodle_facetoface_session_data AS fs3 ON fs3.sessionid = s.id');
			$query->join('LEFT', '#__moodle_facetoface_session_field AS fr4 ON fr4.id = fs3.fieldid');
			$query->where('fr4.shortname ="access"');
			$query->where('(fs3.data="'.$access.'")');
		}

		// Filter region
		$region = $db->escape($this->getState('filter.region'));
		if (!empty($region)) {
			$query->select('fr1.data');
			$query->join('LEFT', '#__moodle_facetoface_session_data AS fr1 ON fr1.sessionid = s.id');
			$query->join('LEFT', '#__moodle_facetoface_session_field AS fr2 ON fr2.id = fr1.fieldid');
			$query->where('fr2.shortname ="region"');
			$query->where('(fr1.data="'.$region.'")');
		}

		//GET FUTURE SESSIONS ONLY
 		$query->having('date >= NOW()');

 		//RESTRICT TO EMPLOYEE USER GROUP 10 ONLY (NEEDS UPDATED!!)
 		$user = JFactory::getUser();
		if (!in_array(10, $user->getAuthorisedGroups())){
			$query->where('m.availability IS NULL');
		}
 		$query->group($db->quoteName('s.id'));

		return $query;
	}

	/**
	 * SQL server change
	 *
	 * @param   integer  $sessionid  Session identifier
	 *
	 * @return  string   Groups titles imploded :$
	 */
	protected function _getSessionFields($sessionid)
	{
		// Load the profile data from the database.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT field.name, field.shortname, data.data FROM #__moodle_facetoface_session_data AS data' .
				' LEFT JOIN #__moodle_facetoface_session_field AS field ON field.id = data.fieldid' .
				' WHERE data.sessionid = ' . (int) $sessionid . ' AND field.showinsummary = 1' .
				' ORDER BY data.fieldid'
		);
		try
		{
			$results = $db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			$result = array();
		}

		return $results;
	}

	/**
	 * SQL server change
	 *
	 * @param   integer  $sessionid  Session identifier
	 *
	 * @return  string   Groups titles imploded :$
	 */
	protected function _getSessionDates($sessionid)
	{
		// Load the profile data from the database.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT from_unixtime(timestart) AS timestart, from_unixtime(timefinish) AS timefinish FROM #__moodle_facetoface_sessions_dates' .
				' WHERE sessionid = ' . (int) $sessionid .
				' ORDER BY sessionid'
		);
		try
		{
			$results = $db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			$result = array();
		}

		return $results;
	}
}