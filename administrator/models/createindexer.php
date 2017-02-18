<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Advsearch records.
 */

require_once JPATH_COMPONENT.'/helpers/advsearch.php';
class AdvsearchModelcreateindexer extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'client', 'a.client',
                'content_type', 'a.content_type',
                'field_code', 'a.field_code',
                'field_type', 'a.field_type',
                'ordering', 'a.ordering',
                'map_table', 'a.map_table',
                'mapping_field', 'a.mapping_field',
                'mapping_label', 'a.mapping_label',
                'options', 'a.options',
                'category', 'a.category',
                'published', 'a.published',

            );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);



		// Load the parameters.
		$params = JComponentHelper::getParams('com_advsearch');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__advanced_search_index` AS a');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');

			}
		}


		// Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }

		return $query;
	}

	/*This method checks whether search indexer is already created
	 * So that we user can't create indexer of same type of same field	*/
	public function getFields()
	{

		$type 	= JRequest::getVar('type');
		$client = JRequest::getVar('client');
		$table 	= $client.'_'.$type;
		$db 	= JFactory::getDBO();

		$adv_search_table_name = $type;
		$table_string 			= AdvsearchHelper::getTableName($client.'_'.$adv_search_table_name);

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__advanced_search_indexer');
		$query->where("mapped_table = '$table_string'");

		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}


	// This method returns the combo of plugins installed.
	function getPlugins()
	{

		if(JRequest::getInt('id'))
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('client');
			$query->from('#__advanced_search_indexer');
			$query->where('id='.JRequest::getInt('id'));
			$db->setQuery($query);
			$plg_name = $db->loadResult();
			$disabled = 'disabled="disabled"';
		}
		$plug_types = JPluginHelper::getPlugin('advsearch');
		$options = array();
		$options[] = JHTML::_('select.option', 'Select Client', '0');
		foreach($plug_types as $key=>$val)
		{
			$options[] = JHTML::_('select.option', $val->name, $val->name);
		}

		$dropdown = JHTML::_('select.genericlist', $options,  'client_name', 'class="required" '.$disabled.'', 'text', 'value', $plg_name );
		$dropdown1 = '<span style="display:none">'.JHTML::_('select.genericlist', $options,  'client_name', 'class="required"', 'text', 'value', $plg_name ).'</span>';

		if(JRequest::getInt('id'))
			return $dropdown.$dropdown1;
		else
			return $dropdown;
	}

	function getIndexer($id)
	{

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__advanced_search_indexer');
		$query->where('id='.$id);
		$db->setQuery($query);
		$plg_name = $db->loadObject();
		return $plg_name;
	}

	function getIndexerFields($id)
	{

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__advanced_search_indexer_fields');
		$query->where('indexer_id='.$id);
		$db->setQuery($query);
		$plg_name = $db->loadObjectList();
		return $plg_name;
	}



	// Save Indexer details
	function saveData()
	{
		$post = JRequest::get('post');
		$db = JFactory::getDBO();
		$componentParams	= JComponentHelper::getParams('com_advsearch');
		$adaptorName		= $componentParams->get('adaptor');

		$content_type = $post['select_types'];
		$grid_filter = $post['grid_filter'];
		$landing_page = $post['landing_page'];
		$basic_search = $post['basic_search'];
		$date =JFactory::getDate();
		$category_search = $post['category_search'];

		$Indexer_id = JRequest::getInt('id');

		if ($Indexer_id)
		{

			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__advanced_search_indexer'));
			$query->where($db->quoteName('id').'='.$Indexer_id);
			//echo $query; die;
			$db->setQuery($query);

			$IndexerData = $db->loadObject();

			// Update adv search records table so that indexing starts again. Added by Amol, code starts.

			if ($IndexerData->type_name)
			{
				$advRecordsQuery = $db->getQuery(true);
				$advRecordsQuery->update('#__advanced_search_records')
								->set('modified = 0')
								->where('type = ' . $db->quote($IndexerData->type_name));
				$db->setQuery($advRecordsQuery);
				$db->execute();
			}

			// Adv search records code ends here.

			if($adaptorName == 'mysql')
			{
				$query = "DROP TABLE ".$db->quoteName('#__'.$IndexerData->mapped_table);
				$db->setQuery($query);
				$db->query();
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__advanced_search_cronjob_update'));
				$query->where($db->quoteName('type').'='.$db->quote($IndexerData->type_name));
				$db->setQuery($query);
				$db->query();
			}

			// Delete search indexer's fields
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_indexer'));
			$query->where($db->quoteName('id').'='.$Indexer_id);
			$db->setQuery($query);
			$db->query();

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_indexer_fields'));
			$query->where($db->quoteName('indexer_id').'='.$Indexer_id);
			$db->setQuery($query);
			$db->query();

			// Delete cronjob details
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_cronjob'));
			$query->where($db->quoteName('type').'='.$db->quote($IndexerData->type_name)." OR ".$db->quoteName('type').'='.$db->quote('solr_'.$IndexerData->type_name));
			$db->setQuery($query);
			$db->query();
		}


		// Code to insert extra fields into the indexer table starts here
		foreach($post['field_type'] as $key=>$val)
		{

			if($post['mapping_field'][$key] > 1)
			{
				$client = $post['client_name'];
				JPluginHelper::importPlugin('advsearch', $client);
				$dispatcher =& JDispatcher::getInstance();
				$Mapping_Extra_Fields = $dispatcher->trigger('getMapping'.$post['field_type'][$key],
				array($post['field_code'][$key], $post['select_types']));
			}

			if($Mapping_Extra_Fields)
				$Extra_fields = $Mapping_Extra_Fields[0];

		}

		// Data insertion into the advanced_search_indexer starts

		$Indexer 				= new stdclass;
		$Indexer->client		= $post['client_name'];
		$Indexer->name 			= $post['name-for-type'];
		$Indexer->created_date 	= $date->format();
		$Indexer->state 		= 1;
		$Indexer->type_name		= $content_type;
		$adv_search_table_name 	= $content_type;
		$table_string 			= AdvsearchHelper::getTableName($post['client_name'].'_'.$adv_search_table_name);
		$Indexer->mapped_table 	= $table_string;
		$Indexer->batch_size 	= $post['batch_size'];

		$orderQuery	= $db->getQuery(true);
		$orderQuery->select('ordering');
		$orderQuery->from('#__advanced_search_indexer');
		$orderQuery->order('id DESC');
		$db->setQuery($orderQuery);
		$old_order = $db->loadResult();
		$Indexer->ordering 		= $old_order + 1; //

		if(!($db->insertObject('#__advanced_search_indexer', $Indexer, 'id')))
		echo $db->stderr();

		$indexer_id = $db->insertid();
		// Data insertion into the advanced_search_indexer ends
		foreach($Extra_fields as $key=>$val)
		{

			$Extra_Data 				= new stdclass;
			$Extra_Data->field_code 		= $key;
			$Extra_Data->field_name 		= $val['name'];
			$Extra_Data->field_order 		= 1; // have to think
			$Extra_Data->mapping_field	= 2;
			$Extra_Data->mapping_label	= $val['name'];
			$Extra_Data->options 			= '';

			$query = "SELECT id FROM #__zoo_category WHERE alias = '$content_type'";
			$db->setQuery($query);
			$get_cat_id = $db->loadResult();

			$Extra_Data->category 		= $get_cat_id;
			$Extra_Data->published		= 1;
			$Extra_Data->indexer_id 	= $indexer_id;

			if($post['search_term'][$key])
			{
				$Extra_Data->search_term	= 1; // Field for Search Term
			}
			$extra_fields_array[] = '`'.$key."` text  NOT NULL";
			$Extra_Data->useas 			= $get_cat_id;

			if(!($db->insertObject('#__advanced_search_indexer_fields', $Extra_Data, 'id')))
				echo $db->stderr();

		}

		// Code for extra fields ends
		$extra_fields_column = implode(', ', $extra_fields_array);

		foreach($post['mapping_field'] as $key=>$val)
		{

			if($val != 1)
			{
				$Data = new stdclass;

				$Data->field_code 		= $post['field_code'][$key];
				$Data->field_name 		= $post['field_name'][$key];
				$Data->field_order 		= $post['order_term'][$key];
				$Data->mapping_field	= $post['mapping_field'][$key];
				$Data->mapping_label	= $post['mapping_label'][$key];
				$Data->options 			= $post['field_options'][$key];
				$Data->indexer_id 	= $indexer_id;
				$Data->useas 			= $post['useas'][$key];

				// $query = "SELECT id FROM #__zoo_category WHERE alias = '$content_type'";
				// $db->setQuery($query);
				// $get_cat_id = $db->loadResult();

				// $Data->category 		= $get_cat_id;
				$Data->published		= 1;

				$table_name = $post['field_code'][$key];
				$field_type = AdvsearchHelper::getFieldType($post['mapping_field'][$key]);

				if ($post['basic_search'][$key])
				{
					$tableCols .= $db->quoteName($post['field_name'][$key]) . ' ' . AdvsearchHelper::getFieldType($post['mapping_field'][$key]) .', ';
					$Data->basic_search		= 1; // Field for Search Term
				}
				if($post['search_term'][$key])
					$Data->search_term		= 1; // Field for Search Term

				if($post['grid_filter'][$key])
					$Data->grid_filter		= 1; // Field 	for Search Term

				if($post['landing_page'][$key])
					$Data->landing_page		= 1; // Field 	for Search Term

				if ($post['category_search'][$key])
					$Data->category_search		= 1;

				if($post['display_search'][$key])
					$Data->display_search		= 1; // Field for Search Term

				// Field for Search Term

				if($field_type)
				$new_fields_array[] = '`'.$table_name."` $field_type  NOT NULL";

				if(!($db->insertObject('#__advanced_search_indexer_fields', $Data, 'id')))
				echo $db->stderr();

			}
		}

		if($adaptorName == 'mysql')
		{
			$createTable	= "";
			$tableName = '#__' . AdvsearchHelper::getTableName($post['client_name'].'_'.$adv_search_table_name);

			$mappedField = substr($tableCols, 0, -2);
			$query = "CREATE TABLE " . $tableName . " (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						" . $mappedField .");";
			$db->setQuery($query);
			$db->query();
		}


		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__advanced_search_indexer_fields'));
		$query->where('grid_filter = 1');
		$db->setQuery($query);
		$gridFilterData = $db->loadObjectList();

		// Define OnAfterIndexSave trigger.
		JPluginHelper::importPlugin('advsearch', $client);
		$dispatcher	= JDispatcher::getInstance();
		$dataArray	= $dispatcher->trigger('onAfterIndexSave', array($gridFilterData));

		return;
	}

	// Delete selected Search Indexer details

	function deleteIndexer()
	{

		$post = JRequest::get('post');
		$db = JFactory :: getDBO();
		foreach ($post['cid'] as $key=>$val)
		{

			$query = "select mapped_table, id, type_name FROM #__advanced_search_indexer WHERE id = ".$val;
			$db->setQuery($query);
			$indexer = $db->loadObject();

			// Drop search indexer's table
			$query = "DROP TABLE ".$db->quoteName('#__'.$indexer->mapped_table);
			$db->setQuery($query);
			$db->query();

			// Delete search indexer's fields
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_indexer_fields'));
			$query->where($db->quoteName('indexer_id').'='.$db->quote($indexer->id));
			//echo $query; die;
			$db->setQuery($query);
			$db->query();

			// Delete cronjob details
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_cronjob'));
			$query->where($db->quoteName('type').'='.$db->quote($indexer->type_name));

			$query->where($db->quoteName('type').'='.$db->quote($IndexerData->type_name)." OR ".$db->quoteName('type').'='.$db->quote('solr_'.$IndexerData->type_name));
			//echo $query; die;
			$db->setQuery($query);
			$db->query();

			// Delete cronjob details
			/* Commented by Mukta - Not using it */

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_cronjob_update'));
			$query->where($db->quoteName('type').'='.$db->quote($indexer->type_name));
			//echo $query; die;
			$db->setQuery($query);
			$db->query();

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__advanced_search_indexer'));
			$query->where($db->quoteName('id').'='.$db->quote($indexer->id));
			//echo $query; die;
			$db->setQuery($query);
			$db->query();

			//~ //This removes the records from search term table, based on indexer type editted
			//~ $query = $db->getQuery(true);
			//~ $query->delete($db->quoteName('#__advanced_search_term'));
			//~ $query->where($db->quoteName('type').'='.$db->quote($indexer->type_name));
			//~ $db->setQuery($query);
			//~ $db->query();

		}
		return;
	}


}
