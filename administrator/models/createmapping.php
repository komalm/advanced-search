<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_advsearch
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT . '/helpers/advsearch.php';

/**
 * Methods create mapping.
 *
 * @since  1.6
 */
class AdvsearchModelcreatemapping extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
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
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_advsearch');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * A public method to get a set of Indexer list.
	 *
	 * @param   integer  $id  Indexer Id.
	 *
	 * @return  array  Select list of indexer.
	 *
	 * @since   1.6
	 */
	public function getIndexer($id = 0)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__advanced_search_indexer');
		$db->setQuery($query);
		$plg_name = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 'Select Indexer', '0');

		foreach ($plg_name as $key => $val)
		{
			$options[] = JHTML::_('select.option', $val->name, $val->id);
		}

		return $options;
	}

	/**
	 * A public method to get a set of Indexer list.
	 *
	 * @param   integer  $indexer  Indexer Id.
	 *
	 * @return  array  Select list of indexer.
	 *
	 * @since   1.6
	 */
	public function getIndexerFieldsData($indexer)
	{
		if ($indexer)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__advanced_search_indexer_fields');
			$query->where('indexer_id=' . $indexer);
			$db->setQuery($query);
			$IndexerFieldsData = $db->loadObjectList();

			return $IndexerFieldsData;
		}

		return 0;
	}

	/**
	 * Method to save the form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function saveMapping()
	{
		$input           = JFactory::getApplication()->input;
		$post            = $input->post->getArray();
		$db              = JFactory::getDBO();

		$extravalue      = $post['primaryIndexField'];
		$extralabel      = $post['secondaryIndexField'];

		$primaryIndexField   = $post['primaryIndexField'];
		$secondaryIndexField = $post['secondaryIndexField'];
		$ordering            = $post['ordering'];
		$type                = $post['type'];
		$xref_id             = $post['xref_id'];

		// Serialize the extra data

		for ($i = 0; $i < count($primaryIndexField); $i++)
		{
			if (($secondaryIndexField[$i] != '') && ($primaryIndexField[$i] != ''))
			{
				$newextra[$i]->secondaryIndexField = $secondaryIndexField[$i];
				$newextra[$i]->primaryIndexField   = $primaryIndexField[$i];
				$newextra[$i]->ordering            = $ordering[$i];
				$newextra[$i]->type                = $type[$i];
				$newextra[$i]->xref_id             = $xref_id[$i];
			}
		}

		$obj = new stdClass;
		$obj->mapping_name       = $post['mapping_name'];
		$obj->primary_index_id   = $post['indexer_1'];
		$obj->secondary_index_id = $post['indexer_2'];
		$obj->state              = 1;
		$obj->created_by         = JFactory::getUser()->id;
		$obj->created_date       = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
		$obj->modified_by        = '';
		$obj->modified_date      = '';

		if ($post['id'])
		{
			$obj->id             = $post['id'];
			$obj->modified_by    = JFactory::getUser()->id;
			$obj->modified_date  = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);

			$result = $db->updateObject('#__tjrecommendations_mapping', $obj, 'id');

			foreach ($newextra as $val)
			{
				$mappingObj                     = new stdClass;
				$mappingObj->id                 = $val->xref_id;
				$mappingObj->mapping_id         = $post['id'];
				$mappingObj->primary_field_id   = $val->primaryIndexField;
				$mappingObj->secondary_field_id = $val->secondaryIndexField;
				$mappingObj->ordering           = $val->ordering;
				$mappingObj->weight             = '';
				$mappingObj->type               = $val->type;
				$mappingObj->range_up           = '';
				$mappingObj->range_down         = '';
				$mappingObj->params             = '';

				$db->updateObject('#__tjrecommendations_mapping_xref', $mappingObj, 'id');
			}
		}
		else
		{
			if ($db->insertObject('#__tjrecommendations_mapping', $obj))
			{
				$mapping_id = $db->insertid();

				foreach ($newextra as $val)
				{
					// if ($val->secondaryIndexField != 'NaN')
					{
						$mappingObj                     = new stdClass;
						$mappingObj->mapping_id         = $mapping_id;
						$mappingObj->primary_field_id   = $val->primaryIndexField;
						$mappingObj->secondary_field_id = $val->secondaryIndexField;
						$mappingObj->ordering           = $val->ordering;
						$mappingObj->weight             = '';
						$mappingObj->type               = $val->type;
						$mappingObj->range_up           = '';
						$mappingObj->range_down         = '';
						$mappingObj->params             = '';

						$db->insertObject('#__tjrecommendations_mapping_xref', $mappingObj);
					}
				}
			}
		}

		return;
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
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getDataMapp($id = null)
	{
		$jinput    = JFactory::getApplication()->input;
		$id = $jinput->get('id', '', 'int');

		if ($id)
		{
			// Create a new query object.
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);

			// Select the required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'a.*, x.*, x.id AS xref_id'
				)
			);
			$query->from('`#__tjrecommendations_mapping` AS a');
			$query->join('LEFT', '`#__tjrecommendations_mapping_xref` AS x ON x.mapping_id = a.id');

			$query->where("a.primary_index_id = " . $id );

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

					// $query->where("a.name LIKE $search"); // added by amol
				}
			}

			$db->setQuery($query);
			return $db->loadObjectList();
		}

		return 0;
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

		$AdvsearchHelper  = new AdvsearchHelper;

		$type 	= JRequest::getVar('type');
		$client = JRequest::getVar('client');
		$table 	= $client.'_'.$type;
		$db 	= JFactory::getDBO();

		$adv_search_table_name = $type;
		$table_string 			= $AdvsearchHelper->getTableName($client.'_'.$adv_search_table_name);

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
		$plg_name = '';
		$disabled = '';
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
}
