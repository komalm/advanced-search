<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Advsearch
 * @author     Amol Patil <example@example.com>
 * @copyright  2017 Amol Patil.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Advance search Model searchindexer
 *
 * @since  1.6
 */
class AdvsearchModelSearchindexer extends JModelList
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if (empty($ordering))
		{
			$ordering = 'a.ordering';
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'a.*'
			)
		);

		$query->from($db->nameQuote('#__advanced_search_index') . ' AS a');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
			}
		}

		return $query;
	}

	/**
	 * This method calls advsearch zoo plugin & returns the processed data
	 *
	 * @param   string  $tablename  name of the advsearch table like antq, arts.
	 * @param   string  $recordate  modified_date to get the records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function cronjob($tablename, $recordate)
	{
		$date 			= JFactory::getDate();
		$db 			= JFactory::getDBO();
		$input 			= JFactory::getApplication()->input;
		$search_indexer = $this->getIndexersData($tablename);

		// Get the indexer Data based on the get passed $tablename now.

		if ($search_indexer)
		{
			// Prepare the parameters like fields, type, limit, data to call getData method
			$queryIndexer_Fields = $db->getQuery(true);
			$queryIndexer_Fields->select("*")
								->from("#__advanced_search_indexer_fields")
								->where("indexer_id = " . $search_indexer->id . " AND basic_search = 1");
			$db->setQuery($queryIndexer_Fields);
			$indexer_records = $db->loadObjectList();

			foreach ($indexer_records as $k => $v)
			{
				if ($v->field_code)
				{
					$fields[]	= $v->field_code;
				}
			}

			$type		= $search_indexer->type_name;
			$client 	= $search_indexer->client;
			$end_dates 	= $recordate;

			$queryCron 	= $db->getQuery(true);
			$queryCron->select("*")
					->from("#__advanced_search_cronjob")
					->where("type = " . $db->quote($type))
					->order("#__advanced_search_cronjob.limit DESC");
			$db->setQuery($queryCron);
			$cron_obj 	= $db->loadObject();
			$limit 		= $cron_obj->limit ?: '0';
			$end_dates = $cron_obj->end_date ?: '0000-00-00 00:00:00';

			// Get the batch size
			if ($search_indexer->batch_size)
			{
				$param_cron_limit 	= $search_indexer->batch_size;
			}
			else
			{
				$param_cron_limit 	= JComponentHelper::getParams("com_advsearch")->get("global_batch_size") ?: '200';
			}

			// Call Advsearch plugin & get the data items based on passed parameters
			JPluginHelper::importPlugin('advsearch', $client);
			$dispatcher = JDispatcher::getInstance();
			$field_data = $dispatcher->trigger('getData', array($type, $fields, $end_dates, $limit, $param_cron_limit ));

			// #echo "<pre>"; print_r($field_data);echo"</pre>";

			// #die('In Advance search tjucm plug-in Line number 264');

			$queryCronLimit = $db->getQuery(true);
			$queryCronLimit->select("*")
					->from("#__advanced_search_cronjob")
					->where("type = " . $db->quote($type));
			$db->setQuery($queryCronLimit);
			$cron_obj = $db->loadObject();

			if ($cron_obj)
			{
				// Just update limit so that we can get next records slot
				if ($field_data[0])
				{
					$cron_obj->limit += count($field_data[0]);
				}

				$cron_obj->start_date = $date->toSql(true);
				$cron_obj->end_date   = $date->toSql(true);

				if (!$db->updateObject('#__advanced_search_cronjob', $cron_obj, 'id'))
				{
					echo "Cron entry didn't updated. Please check in model file!!";
				}
			}
			else
			{
				$CronData				= new stdclass;
				$CronData->limit		= count($field_data[0]);
				$CronData->type			= $type;
				$CronData->start_date	= $date->tosql(true);
				$CronData->end_date		= $date->tosql(true);

				if (!$db->insertObject('#__advanced_search_cronjob', $CronData, 'id'))
				{
					echo "Cron entry didn't inserted. Please check in model!!";
				}
			}

			// Insert data into Adv search type table.
			if ($search_indexer->mapped_table)
			{
				$recordData = $field_data[0];

				foreach ($recordData as $key => $data)
				{
					if ($data->id)
					{
						$rExists = $db->getQuery(true);
						$rExists->select("id")
								->from('#__' . $search_indexer->mapped_table)
								->where("id = " . $data->id);
						$db->setQuery($rExists);
						$indexer_records = $db->loadResult();

						if ($indexer_records)
						{
							if (!$db->updateObject('#__' . $search_indexer->mapped_table, $data, 'id'))
							{
								echo 'Error while inserting into DB';
							}
						}
						else
						{
							if (!$db->insertObject('#__' . $search_indexer->mapped_table, $data, 'id'))
							{
								echo 'Error while inserting into DB';
							}
						}
					}
				}
			}

			// #die('Done!!');

			if (isset($field_data[0]))
			{
				return $field_data[0];
			}
		}

		// Plug if ends
	}

	/**
	 *  This returns the advanced search indexer information based on passed parameter.
	 *  Now its straight forward query but haven't removed old eles conditions.
	 *  We may extend this in future
	 *
	 * @param   string  $tablename  Name of databse table.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function getIndexersData($tablename)
	{
		$db 		= JFactory::getDBO();
		$input 		= JFactory::getApplication()->input;
		$type 		= $input->get('type');
		$plun_name 	= $input->get('plg');

		if ($tablename)
		{
			$type = $tablename;
		}

		if ($type)
		{
			$query = "SELECT * FROM #__advanced_search_indexer where type_name = '$type'";
			$db->setQuery($query);
			$search_indexer = $db->loadObject();
		}
		elseif ($plun_name)
		{
			$mapped_table = 'advanced_search_' . $plun_name;
			$query = "SELECT * FROM #__advanced_search_indexer where mapped_table like '%$mapped_table%'";
			$db->setQuery($query);
			$search_indexer = $db->loadObjectList();
		}
		else
		{
			$query = "SELECT * FROM paai_advanced_search_indexer WHERE type_name NOT IN (
						'econ-sublots', 'auctions', 'add-film', 'photographers', 'studio', 'masterlist-for-people',
						'masterlist-for-company', 'film-personalities', 'auto-personality-master', 'institution-master',
						'marque-master', 'personality-masterlist' ) ORDER BY type_name ASC ";

			$db->setQuery($query);
			$search_indexer = $db->loadObjectList();
		}

		return $search_indexer;
	}
}
