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
//require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/advsearch.php';
require_once ( JPATH_SITE.'/administrator/components/com_advsearch/helpers/advsearch.php');
require_once JPATH_SITE . '/components/com_osian/classes/build_hierarchy.php';

/**
 * Methods supporting a list of Advsearch records.
 */
class AdvsearchModelAdvsearch extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->db 	= JFactory::getDBO();
    }

	function getClassification()
	{
		$user	= JFactory::getUser();
		$client = "advanced_search_zoo";
		$query =  $this->db->getQuery(true);
		$query	->select('type_name, name')
				->from('#__advanced_search_indexer');
		/*$query->where("name NOT IN ('masterlist-for-people', 'auto-personality-master', 'marque-master',
						'econ-sublots', 'film-personalities', 'institution-master', 'photographers', 'add-film',
						'auctions', 'masterlist-for-company', 'studio','personality-masterlist')");
						*/
		// Amol added this chaprigiri
		$query->order("FIELD( mapped_table, 'advanced_search_zoo_video-masterlist', 'advanced_search_zoo_photographers', 'advanced_search_zoo_personality-masterlist', 'advanced_search_zoo_masterlist-for-company', 'advanced_search_zoo_marque-master', 'advanced_search_zoo_institution-master', 'advanced_search_zoo_film-personalities', 'advanced_search_zoo_events_27-4', 'advanced_search_zoo_econ-sublots', 'advanced_search_zoo_auto-personality-master', 'advanced_search_zoo_auto-model-masterlist', 'advanced_search_zoo_auctions', 'advanced_search_zoo_masterlist-for-people', 'article-masterlist', 'advanced_search_zoo_add-film', 'advanced_search_zoo_antiquarian-printmaking', 'advanced_search_zoo_photography', 'advanced_search_zoo_economic-databases-for-classifications', 'advanced_search_zoo_crafts-games-toys-non-antq', 'advanced_search_zoo_film-publicity-memorabilia', 'advanced_search_zoo_books-catalogues-publications', 'advanced_search_zoo_classic-vintage-automobiles', 'advanced_search_zoo_modern-contemporary-fine-arts', 'advanced_search_zoo_antiquities' ) DESC");
		$this->db->setQuery($query);
		$search_indexer =  $this->db->loadObjectList();

		$options = array();

		$selectClassification = $user->id ? 'COM_ADVANCED_SEARCH_CLASSIFICATIONS' : 'COM_ADVANCED_SEARCH_SELECT_CLASSIFICATION';
		//~ $options[] = JHTML::_('select.option', JTEXT::_($selectClassification), '1');
		$options[] = JHTML::_('select.option', '', '');
		foreach($search_indexer as $key=>$val)	{
			$options[] = JHTML::_('select.option', strtoupper($val->name), $val->type_name);
		}

		$dropdown = JHTML::_('select.genericlist', $options,  'classification', 'class="required"', 'text', 'value');

		return $dropdown;
	}

    /*
     * Added by Amol
     * This returns the list of attributes of selected type along with related types like masterlist.
     * @Parameter - $type - indexer type - cine, antq etc.
     * */

    function getAttributes($type)
	{
		if($type == 1)
		{

			$query_field 	= $this->db->getQuery(true);
			$query_field	->select('name, type_name')
							->from('#__advanced_search_indexer');
							//->order("FIELD( name, 'prnt', 'phto', 'econ', 'crft', 'cine', 'book', 'auto', 'arts', 'antq' ) DESC");

			$this->db->setQuery($query_field);
			$types 	= $this->db->loadObjectList();
			foreach($types as $k => $data)
			{
				$types[] = $data->name;
			}
			/*$path = JPATH_SITE . '/media/zoo/applications/blog/types';

			if (JFolder::exists($path))
			{
				$files  = JFolder::files($path, $filter = '.', $recurse, $fullpath , $exclude);

				foreach($files as $data)
				{

					$zooType = explode('.', $data);
					$types[] = $zooType[0];

				}
			} */
			//print_r($types); die('Amol here!1');
			/*$types = array('antq', 'arts', 'auto', 'book', 'cine', 'crft', 'econ', 'phto', 'prnt','personality-masterlist', 'photographers',
							'film-personalities', 'masterlist-for-company', 'auto-personality-master', 'institution-master',
							'marque-master', 'masterlist-for-people', 'auctions', 'add-film'
							);	*/
			$i=0;
			foreach($types as $type)
			{
				if($i == 0)
				{
					$Content .= '<div class="ad1">
									<div class="ad2">'.JText::_('COM_ADVANCED_INCLUDE_RECORDS').'</div>';
					$Content .=	AdvsearchHelper::getIncludeRecords();
					$Content .=	AdvsearchHelper::getFieldColumType('3', 'secondary_cat').'</div>';

					$Content .= '<div class="ad1">
									<div class="ad2">'.JText::_('COM_ADVANCED_GENERAL_SEARCH').'</div>';
					//$Content .=	AdvsearchHelper::getIncludeRecords();
					$Content .=	AdvsearchHelper::getFieldColumType('2', 'general_search').'</div>';

				// Added by Amol for Range demo. Delete this later
				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('WNN').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'wnn').'</div>';

				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('CDT Id').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'secondary_cat').'</div>';


				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('Created Year').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'osian_created').'</div>';

				// Added by Amol for Range demo ends here

					$CachedFilters .= $Content;


				}
				$CachedFilters 	.="<div class='ad2' style='font-weight:bold'>". strtoupper($type->name) ."</div>";
				$CachedFilters 	.= $this->getSearchFilters($type->type_name, $i, 'allclassification');
			$i++;
			}
			return $CachedFilters;
		}
		else
		{
			if(!$type)
				return false;

			$cache 			= JFactory::getCache();
			$CachedFilters 	= $this->getSearchFilters($type, $i=0);

			if(!$CachedFilters)
				return false;

			do
			{
				// Get the related fileds type by calling common function.
				$RIPrpFiledsx = CommonFunctions::relateditemsprofieldsx(CommonFunctions::get_config_type_name($type));
				if(empty($RIPrpFiledsx))
					return $CachedFilters;

				// Sushan added this return to display fields of buggy masterlists
				return $CachedFilters;

				$query_field 	= $this->db->getQuery(true);
				$query_field	->select('id')
								->from('#__advanced_search_indexer')
								->where('type_name = '.$this->db->quote($type));
				$this->db->setQuery($query_field);
				$typeId 	= $this->db->loadResult();
				foreach($RIPrpFiledsx as $k=>$v)
				{
					$query_field 	= $this->db->getQuery(true);
					$query_field	->select('*')
									->from('#__advanced_search_indexer_fields AS fil')
									->leftJoin('#__advanced_search_indexer as ind ON fil.indexer_id = ind.id')
									->where('ind.id = '.$typeId.' AND fil.field_code = '.$this->db->quote($v));
					$this->db				->setQuery($query_field);
					$classFields 	= $this->db->loadResult();

					if($classFields)	{
						$types[$k] = $k;
						$type = $k;
					}
					else
						$type = "";
				}

			} while($type);

			// $type contains the types of all related fields of a classification like cine - add-film, masterlist-of-company etc.
			// Get the fields of each type & appead it.
			if(isset($types))	{
				foreach($types as $RI)	{
					$CachedFilters .= $this->getSearchFilters($RI, $i=1);
					//$CachedFilters = $cache->call(array('AdvsearchModelAdvsearch', 'getSearchFilters'), $RI, $i=1);
				}
			}
		}

		return $CachedFilters;

	}
	//getAttributes ends here

	/*
	 *  Added by Amol
	 *	This return the list of search fields based on passed parameter type.
	 *  @Para - $type = type of search indexer like cine, add-film etc
	 *  $i has used to add div for html design.
	 * */
	public static function getSearchFilters($type, $i, $allClassification = '')
	{

		// Query to get selected fields information of respective Indexer type
		$db 			= JFactory::getDBO();
		$query_field 	= $db->getQuery(true);
		$KeyPairs 		= CommonFunctions::getZooFields($type);

		// Added by Amol get the CDT data based on classification id.
		$classificationId = CommonFunctions::Get_Zoo_Cat_Id($type);
		$fieldsOrder 	= implode($KeyPairs);
		$fieldsOrder 	= substr( $fieldsOrder, 0, -2);
		$query_field	->select('*')
						->from('#__advanced_search_indexer_fields AS fil')
						->leftJoin('#__advanced_search_indexer as ind ON fil.indexer_id = ind.id')
						->where('display_search = 1 AND ind.type_name = '.$db->quote($type));
		if($fieldsOrder)
			$query_field->order('FIELD(fil.field_code ,'.$fieldsOrder.')');

		$db				->setQuery($query_field);
		$fields 		= $db->loadObjectList();

		$options 		= array();
		$options[] 		= JHTML::_('select.option', 'Parameter', '0');
		$Content 		= "";

		// Added by Amol for Document type field
/*
		$CategoryContent =	AdvsearchHelper::getFieldColumType('10', $classificationId);

		if($CategoryContent)
		{
			$Content .= '<div class="ad1">
						<div class="ad2">'.JText::_('Document Type').'</div>';
			$Content .= $CategoryContent . '</div>';
		}
*/
		// Build the html of search filters
		foreach($fields as $key=>$val)	{
			if($i == 0 && !$allClassification)	{

				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('COM_ADVANCED_INCLUDE_RECORDS').'</div>';
				$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('3', 'secondary_cat').'</div>';

				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('COM_ADVANCED_GENERAL_SEARCH').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('2', 'general_search').'</div>';

				// Added by Amol for Range demo. Delete this later
				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('WNN').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'wnn').'</div>';

				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('CDT Id').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'secondary_cat').'</div>';

				$Content .= '<div class="ad1">
								<div class="ad2">'.JText::_('Created Year').'</div>';
				//$Content .=	AdvsearchHelper::getIncludeRecords();
				$Content .=	AdvsearchHelper::getFieldColumType('5', 'osian_created').'</div>';

				// Added by Amol for Document type field

				$cols = AdvsearchHelper::getFieldColumType('10', $classificationId);

				if ($cols)
				{
					$Content .= '<div class="ad1">
									<div class="ad2">'.JText::_('Document Type').'</div>';
					$Content .=	AdvsearchHelper::getFieldColumType('10', $classificationId).'</div>';
				}

				// Added by Amol for Range demo ends here

			}

			$Content .= '<div class="ad1">
					<div class="ad2">' . $val->mapping_label . '</div>';
			// Chindigiri
			if($val->mapping_field == "2" || $val->mapping_field == "6")
 			{
				$Content .=	AdvsearchHelper::getMatch('fieldid_'.$val->field_code);
				$Content .=	AdvsearchHelper::getFieldColumType($val->mapping_field, $val->field_code, '0');
			}
			else
				$Content .=	AdvsearchHelper::getFieldColumType($val->mapping_field, $val->field_code, '0');
			$Content .= '</div>';
			$i++;
		}

		return $Content;
	}
	// getSearchFilters ends here
	/*We dont use this, you can delete this later*/
	function getSearchAttributes()
	{

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$type = JRequest::getVar('type');
		if(!$type)
		//return false;

		$search_id = JRequest::getvar('searchid');
		$field_val = array();
		if($search_id)
		{

			$get_client_query = "SELECT * FROM #__advanced_search_saved_searches WHERE id = ".$search_id;
			$db->setQuery($get_client_query);
			$saved_data = $db->loadObject();
			$type = $saved_data->type;
			$field_values = $saved_data->fieldval;
			$field_values_decode =  json_decode($field_values);
			//print_r($field_values_decode);


			function parseObjectToArray($object)
			{
				$array = array();
				if (is_object($object))
				{
					$array = get_object_vars($object);
					foreach ($array as $name=>$value)
					{

						if (is_object($value))
							$array[$name] = parseObjectToArray($value);
					}
				}
				return $array;
			}
			$field_val = parseObjectToArray($field_values_decode);
			//echo count($field_val);

		}

		$i = 1;
		$dropdowns = array();
		if($field_val)
		{
				foreach($field_val as $key=>$value)
				{
					$query = $db->getQuery(true);
					// Query to get selected fields of respective Indexer type
					$query->select('*');
					$query->from('#__advanced_search_index');
					$query->where("content_type = '$type'");

					$db->setQuery($query);
					$fields = $db->loadObjectList();
					$selected = $key;
					$options = array();

					foreach($fields as $key=>$val)
					{

						$options[] = JHTML::_('select.option', $val->mapping_label, $val->field_code);
					}
					$std_opt = 'class="makeup required"';
					$dropdown = '<div class="advsearch">'.JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value', $selected).'</div>';
					//$dropdown = JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value');
					// Return attributes dropdown

					$query_filed_type = "SELECT mapping_field FROM #__advanced_search_index WHERE field_code = '$selected'";
					$db->setQuery($query_filed_type);
					$mapping_field = $db->loadResult();
					$field = AdvsearchHelper::getFieldColumType($mapping_field, $selected, $value);
					// respective form field of first attributes which we have to return with attributes dropdown
					$dropdowns[$dropdown] = $field;
					/*echo $text = '<div class="newcontainer">

										<div class="remove"><span class="remove"></span></div>
											<div class="fields" id="'.$i.'">'.$dropdown.$field.'</div>
											<div style="clear:both"></div>

									</div>'; 	*/
					$i++;
				}
			}
					return $dropdowns;
	}


	/*
	 * Added by Amol
	 * Following 2 functions returns grid fields on grid view.
	 * */
	function getIndexerGridFields($id)
	{
		$cache = JFactory::getCache();
		//$cache->setCaching(1);
		//$cache->cleanCache();
		$Data = $cache->call(array('AdvsearchModelAdvsearch', 'cachegetIndexerGridFields'), $id);
		//$Data = AdvsearchModelAdvsearch::cachegetIndexerGridFields($id);

		return $Data;
	}

	function cachegetIndexerGridFields($id)
	{
		$ch 			= curl_init();
		$db 			= JFactory::getDBO();
		$query_field 	= $db->getQuery(true);
		$query_field	->select('ind.type_name')
						->from('#__advanced_search_indexer_fields AS fil')
						->rightJoin('#__advanced_search_indexer as ind ON fil.indexer_id = ind.id')
						->where('fil.category = '.$id.' AND grid_filter = 1');
		$db				->setQuery($query_field);
		$type 			= $db->loadResult();
		$mainclass 		= "'".$db->loadResult()."'";


		do
		{
			$RIPrpFileds = CommonFunctions::relateditemsprofields(CommonFunctions::get_config_type_name($type));
			if(isset($RIPrpFileds[0]))
			{
				foreach($RIPrpFileds as $Field)
				{
					$type = $Field;
					$types[$Field] = "'".$Field."'";
				}
				$j++;
			}
			else
				$j = 10;

		} while($j < 10);
		if($types)
			$indexers = ','.implode(', ', $types);

		$query = "SELECT * FROM #__advanced_search_indexer_fields as fil LEFT JOIN
					#__advanced_search_indexer as ind ON fil.indexer_id = ind.id
					WHERE ind.type_name IN (".$mainclass.$indexers.") AND fil.grid_filter = 1
					ORDER BY fil.field_order ASC";
		$db->setQuery($query);
		$fieldss = $db->loadObjectList();
		foreach($fieldss as $key=>$val)
		{
			$solr_hostname = JComponentHelper::getParams("com_osian")->get('solr_hostname'); //solr hostname

			$solr_path = JComponentHelper::getParams("com_osian")->get('solr_path'); //solr hostname
			$SolrFieldId = "fieldid_".$val->field_code;
			$solrurl = "http://$solr_hostname/$solr_path/select?q=*%3A*&group=false&group.field=$SolrFieldId&group.main=true&fl=$SolrFieldId&rows=100000000&wt=json";

			//echo $solrurl = "http://$solr_hostname/solr/select?q=*%3A*&group=true&group.field=fieldid_".$val->field_code."&group.main=true&fl=fieldid_".$val->field_code."&rows=-1&wt=json";
			$strFieldCode = 'fieldid_'.$val->field_code;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $solrurl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$Resultset = "";
			if($output)
				$JArray = json_decode($output);

			$MainArray = array();
			$ProcessedArry = $JArray->response->docs;

			if(isset($ProcessedArry))
			{
				foreach($ProcessedArry as $vals)
				{
					if(isset($vals->$SolrFieldId))
					{
						foreach($vals->$SolrFieldId as $k=>$row)
						{
							if($row)
								$MainArray[$row] = $row;
						}
					}
				}
			}

			natcasesort($MainArray);
			$options = array();
			if($MainArray)
			{
				$options[] = JHTML::_('select.option', '0' , $val->mapping_label);
				foreach($MainArray as $values)
				{
					$options[] = JHTML::_('select.option', $values, $values);
				}
				//$std_opt = 'class="search_filter" ';
				$std_opt = 'class="search_filter inputbox" ';

				$fields[$key] = JHTML::_('select.genericlist',$options, 'fieldid_'.$val->field_code, $std_opt, 'value', 'text');
			}
		}

		return $fields;

	}


    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
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
        return $query;
    }

	/*We are not using following all methods at all*/
	function getsavedata()
	{
		// pass search indexer type in hidden field n get it n find table name for search result.

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$post = JRequest::get('post');

		$range = $post['range'];

		$attributes = $post['attributes'];
		$values = $post['value'];
		foreach($attributes as $key=>$value)
		{
			$Field_Values[$value] = $values[$key];
		}

		$get = JRequest::get('get');
		$search_table = '#__advanced_search_zoo_'.$post['classification']; 	// Table Name
		$start = 0;
		$limit = 50;
		$i=0;
		foreach($Field_Values as $key=>$val)
		{
			if($i==0)
			{
				$query->select('*');
				$query->from($db->nameQuote($search_table));
				if($val)
				$query->where($db->nameQuote($key)." LIKE '%$val%'");

			}
			else
			{
				if($val)
					$and .= " AND ".$db->nameQuote($key)." LIKE '%$val%'";
			}
			$i++;
		}

		$count = count($Field_Values);
		if($count>1 && $range)
		{
			$between .= " AND ".$db->nameQuote($range[2])." BETWEEN $range[0] AND $range[1]";
		}
		if($count<=1 && $range)
		{

			$between .= " WHERE ".$db->nameQuote($range[2])." BETWEEN $range[0] AND $range[1]";
		}


		$limit = " LIMIT $start, $limit";
		echo $Final_Query = $query.$and.$between.$limit;
		if($query.$and.$between)
		{
		$db->setQuery($Final_Query);
		$result = $db->loadObjectList();
		return $result;
		}
	}


	function getAttributeList()
	{

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if(JRequest::getInt('bk'))
		{
			$session = JFactory::getSession();
			$type = $session->get('classification');
		}

		// Query to get selected fields of respective Indexer type
		$query->select('*');
		$query->from('#__advanced_search_index');
		$query->where("content_type = '$type'");
		$query->order("ordering DESC");

		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 'Parameter', '0');
		foreach($fields as $key=>$val)
		{

			$options[] = JHTML::_('select.option', $val->mapping_label, $val->field_code);
		}

		$std_opt = 'class="makeup required"';
		$dropdown = JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value');

		//$dropdown = JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value');
		// Return attributes dropdown
		//$field = AdvsearchHelper::getFieldColumType($fields[0]->mapping_field, $fields[0]->field_code);
		// respective form field of first attributes which we have to return with attributes dropdown
		return $dropdown.$field;

	}


	function getParametersDropdown()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$session = JFactory::getSession();
		$type = $session->get('classification');


		// Query to get selected fields of respective Indexer type
		$query->select('*');
		$query->from('#__advanced_search_index');
		$query->where("content_type = '$type'");
		$query->order("ordering DESC");

		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 'Parameter', '0');
		foreach($fields as $key=>$val)
		{

			$options[] = JHTML::_('select.option', $val->mapping_label, $val->field_code);
		}

		$std_opt = 'class="makeup required"';
		$dropdown = JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value', $id);
		return $dropdown;
	}

	/*
	 * This method returns the list of previous parameters searched by user */
	function getSearchedParameters()
	{

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$session = JFactory::getSession();
		$type = $session->get('classification');
		$Adv_Fields = $session->get('Adv_Fields');

		$query->select('*');
		$query->from('#__advanced_search_index');
		$query->where("content_type = '$type'");
		$query->order("ordering DESC");

		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 'Parameter', '0');
		foreach($fields as $key=>$val)
		{

			$options[] = JHTML::_('select.option', $val->mapping_label, $val->field_code);
		}

		$std_opt = 'class="makeup required"';
		$dropdown = array();
		foreach($Adv_Fields as $id=>$value)
		{

			$dropdown[] = JHTML::_('select.genericlist', $options,  'attributes[]', $std_opt,'text', 'value', $id);

		}
		return $dropdown;
	} // getSearchedParameters ends

	/**
	 * Method to get auto-suggest options from solr written by Sushan
	 *
	 * @return  options array
	 *
	 * @since   16.01.18
	 */
	public function getRelatedFieldOptions()
	{
		$input 	=	JFactory::getApplication()->input;
		$text1	=	trim($input->get('term', '', 'STRING'));
		$text2	=	" " . $text1;

		$text	=	str_replace(' ', '*', trim($input->get('term', '', 'STRING')));
		$name	=	$input->get('name', '', 'STRING');

		$selected 		= $input->get('selected', '', 'STRING');
		$selectedArray	= array();

		if($selected)
		{
			$selectedArray	= explode('||', trim($selected, '||'));
		}


		// Get solr client data
		$solr_hostname	= JComponentHelper::getParams("com_osian")->get('solr_hostname');
		$solr_port		= JComponentHelper::getParams("com_osian")->get('solr_port');
		$solr_path		= JComponentHelper::getParams("com_osian")->get('solr_path');
		$selectCat = '';

		$solrSort		= "&sort=wnn+asc&fl={$name}";
		$solrGroup		= "&group=true&group.field=title&wt=json&indent=true&rows=300";
		$solrSelect		= "/select?q=*%3A*&fq={$name}:*{$text}*";
		$solrurl		= "http://" . $solr_hostname . ":" . $solr_port . "/" . $solr_path . $solrSelect . $solrSort . $solrGroup . $solrLimit;

		/*
		 * Sample solr url to get options
		 * http://staging.osianama.com/index.php?option=com_advsearch&task=getRelatedFieldOptions&term=fir&name=fieldid_e01ebd65-5f06-4bd1-9e47-ab3a13b8547c
		*/

		// CURL call
		$ch		= curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $solrurl);
		$output = curl_exec($ch);
		curl_close($ch);

		// Get cURL output
		$arry		= json_decode($output, 1);
		$matches	= $arry['grouped']['title']['matches'];
		$listOption = '';

		// If matches found, build a unique array i.e. $liste
		if ($matches)
		{
			$groupeditems		= $arry['grouped']['title']['groups'];
			$demoOption = '';
			$liste		= array();

			foreach ($groupeditems as $item)
			{
				$arrItem = $item['doclist']['docs'][0][$name];

				foreach ($arrItem as $valItem)
				{
					if (!in_array($valItem, $liste, true) && !in_array($valItem, $selectedArray, true))
					{
						if((stripos($valItem, $text1) == 0) || (stripos($valItem, $text2) !== false))
						{
							array_push($liste, $valItem);

							if (count($liste) == 12)
							{
								break;
							}
						}
					}
				}
			}

			$liste = array_unique(array_map('trim', $liste));

			if (trim($text, "*"))
			{
				// Sort the array according to value length
				usort(
					$liste, function($a, $b)
					{
						return strlen($a) - strlen($b);
					}
				);
			}
			else
			{
				$liste = array_slice($liste, 0, 12);
				sort($liste);
			}

			if(!empty($liste))
			{
				// Return sorted array containing only 12 unique values
				return array_slice($liste, 0, 12);
			}
			else
			{
				// Return null if matches not found
				return null;
			}
		}
		else
		{
			// Return null if matches not found
			return null;
		}
	}
}
