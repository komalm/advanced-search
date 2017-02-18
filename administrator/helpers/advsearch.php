<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Advsearch helper.
 */
class AdvsearchHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_TITLE_STAT'),
			'index.php?option=com_advsearch&view=stat',
			$vName == 'stat'
		);

		if(JRequest::getVar('layout') == "edit")
		{
			JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_EDIT_SEARCH_INDEXER'),
			'index.php?option=com_advsearch&view=createindexer',
			$vName == 'createindexer'
			);

		}
		else
		{
			JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_TITLE_CREATEINDEXER'),
			'index.php?option=com_advsearch&view=createindexer',
			$vName == 'createindexer'
			);
		}
		JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_INDEXER_LIST'),
			'index.php?option=com_advsearch&view=searchindexer',
			$vName == 'searchindexer'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_CREATE_MAPPING'),
			'index.php?option=com_advsearch&view=createmapping',
			$vName == 'createmapping'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_MAPPING_LIST'),
			'index.php?option=com_advsearch&view=mapping',
			$vName == 'mapping'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_ADVSEARCH_TITLE_PLGUINFO'),
			'index.php?option=com_advsearch&view=pluginfo',
			$vName == 'pluginfo'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_advsearch';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Function to get data
	 *
	 * @param   STRING  $table         Name of database table
	 * @param   STRING  $selectList    Selected value colume name
	 * @param   STRING  $where         Query where condition
	 * @param   STRING  $returnObject  Selecting data using JDatabase - link https://docs.joomla.org/Selecting_data_using_JDatabase
	 *
	 * @return  true
	 *
	 * @since 1.0.0
	 */
	public function getDataValues($table, $selectList, $where, $returnObject, $order="")
	{
		// Ref - link https://docs.joomla.org/Selecting_data_using_JDatabase

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($selectList);
		$query->from($table);

		if ($where)
		{
			$query->where($where);
		}

		if ($order)
		{
			$query->order($order);
		}

		$db->setQuery($query);


		return $db->$returnObject();
	}

	function UseAsDropdown($i, $field_type, $selected_value)
	{

		/*$category_prefix = array(
										0=> 'Don\'t Use',
										1 => 'Title',
										2 => 'Image',
										3 => 'Description',
										4 => 'Numeric'
									);
				*/
		$category_prefix = array(
										0=> 'No',
										1 => 'Yes'
									);


			foreach($category_prefix as $key=>$field)
			{
				$options[] = JHTML::_('select.option',$key,$field);
			}
			if($type) $selected = $type; else $selected = '';
			$std_opt = 'class="inputbox"  ';
			return JHTML::_('select.genericlist',$options, 'useas['.$i.']', $std_opt, 'value', 'text', $selected_value);
		}


	function categoryDropdownListAdd($i, $field_type, $selected_value)
	{

		$client = JRequest::getvar('client');
		JPluginHelper::importPlugin('advsearch', $client);
		$dispatcher =& JDispatcher::getInstance();
		$fields = $dispatcher->trigger('getFieldType', array($field_type));
		if($fields[0])
		{

			$category_prefix = array(
										1=> 'No',
										6 => 'Yes',
									);

			foreach($category_prefix as $key=>$field)
			{
				$options[] = JHTML::_('select.option',$key,$field);
			}
			if($type) $selected = $type; else $selected = '';
			$std_opt = 'class="inputbox"  ';
			return JHTML::_('select.genericlist',$options, 'mapping_field['.$i.']', $std_opt, 'value', 'text', $selected_value);
		}
		else
		{

			$category_prefix = array(
									0 => 'Select Type',
									9 => 'Date',
									2 => 'Text',
									4 => 'Multi Select',
									10 => 'Numerical',
									7 => 'Radio',
									3=> 'Single Select'
									);

			foreach($category_prefix as $key=>$field)
			{
				$options[] = JHTML::_('select.option',$key,$field);
			}
			if($type) $selected = $type; else $selected = '';
			$std_opt = 'class="inputbox"  ';
			return '<div class="advsearch">'.JHTML::_('select.genericlist',$options, 'mapping_field['.$i.']', $std_opt, 'value', 'text', $selected_value).'</div>';
		}
	}

	function getTableName($string)
	{

		$table_string = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $string);
		$t = trim($table_string);
		$table = str_replace(" ", "_", $table_string);
		return strtolower($table);
		// Have to modify code as it generates extra _ for each special charater. So string contains un-necessary '_'.
	}

	function getFieldType($fiel_type)
	{
		$FieldArray = array(
							2=>'text',
							3=>'varchar(400)',
							4=>'text',
							5=>'varchar(100)',
							6=>'text',
							7=>'varchar(100)',
							8=>'varchar(100)',
							9=>'DATE NOT NULL',
							10 => 'int'
							);
		return $FieldArray[$fiel_type];
		// Have to modify code as it generates extra _ for each special charater. So string contains un-necessary '_'.
	}

	public static function getNumericOperators($FieldCode)
	{
		$Dropdown =	'<div class="select-bg">
						<select class="search_filters inputbox" name="operators['.$FieldCode.']" id="operators['.$FieldCode.']">
							<option value="e">Equal</option>
							<option value="l">(<) Less Than	</option>
							<option value="g">(>) Greater Than</option>
						</select>
					</div>';
		return $Dropdown;
	}

	public static function getIncludeRecords()
	{
		$Dropdown =	'<div class="select-bg">
						<select class="search_filters inputbox" name="include" id="include">
							<option value="all">'.JText::_('COM_ADVANCED_SEARCH_ONLY_MATCH_ALL').'</option>
							<option value="any">'.JText::_('COM_ADVANCED_SEARCH_ANY_PARAMETER_HAS_RESULT').'</option>
						</select>
					</div>';
		return $Dropdown;
	}


	public static function getMatch($FieldCode)
	{
		$Dropdown =	'<div class="select-bg">
						<select class="search_filters inputbox" name="match['.$FieldCode.']" id="match['.$FieldCode.']">
							<option value="0">'.JText::_('COM_ADVANCED_SEARCH_EXACT_MATCH').'</option>
							<option value="pm">'.JText::_('COM_ADVANCED_SEARCH_PARTIAL_MATCH').'</option>
						</select>
					</div>';
		return $Dropdown;
	}

	/*
	 *  Added by Amol
	 *  This returns the html form field based on passed params.
	 *  @Param - $field_type - Type of html form field 2 for text etc
	 * $field_id - field id like zoo element id
	 * */
	public static function getFieldColumType($field_type, $field_id, $value=0)
	{
		if($value == "0")
			$value = "";

		$db = JFactory::getDBO();
		$prefix = 'fieldid_';
		$field = "";
		switch($field_type)
		{

			case '2' :
					$field = '<div class="">
							<input type="text" class="text-input required" id="fq['.$prefix.$field_id.']" name="fq['.$prefix.$field_id.']" value="'.$value.'"/>
							</div>';
					break;
			case '3' :
			case '6' :
					$Data = array();
					$query = "SELECT * FROM #__advanced_search_indexer_fields WHERE field_code = '$field_id'";
					$db->setQuery($query);
					$final = array();
					$Data = $db->loadObject();

					if($Data)	{
						if($Data->options)	{
							foreach (explode(',', $Data->options) as $pair)	{
								list($keys, $values) = explode('|', $pair);
								$final[$keys] = $values;
							}
						}
						if(count($final) > 1)	{
							$options[] = JHTML::_('select.option','0',$Data->mapping_label);
							foreach($final as $key=>$field)	{
								$options[] = JHTML::_('select.option',$field,$key);
							}
							$std_opt = 'class="search_filters inputbox" ';
							$field = '<div class="select-bg">'.JHTML::_('select.genericlist',$options, 'fq['.$prefix.$field_id.']', $std_opt, 'value', 'text').'</div>';
						}
						else
						{
							//~ $field = '<div class="checking-class"><input type="text" class="text-input required" id="fq['.$prefix.$field_id.']" name="fq['.$prefix.$field_id.']" value="'.$value.'"/></div>';
							$fieldUpdatsss	= str_replace('-', '_', $field_id);
							$field			= '<div class=""><select id="'. $prefix.$field_id . '" multiple="" name="fq[' . $prefix.$field_id . ']" class="chosen-select ' . $fieldUpdatsss . '" tabindex="-1"></select></div>';
						}
					}
						break;

			case '4' :

					$query = "SELECT options FROM #__advanced_search_indexer_fields WHERE field_code = '$field_id'";
					$db->setQuery($query);
					$Data = $db->loadResult();
					$final = array();
					$seleted = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $value[0]);

					foreach (explode(',', $Data) as $pair)
					{
						list($key, $value) = explode('|', $pair);
						$final[$key] = $value;
					}
					if(count($final) > 1)
					{
						foreach($final as $key=>$field)
						{
							$options[] = JHTML::_('select.option',$field,$key);
						}

						$std_opt = 'class="multiple" multiple';
						$field = '<div class="">'.JHTML::_('select.genericlist',$options, 'fq['.$field_id.'][]', $std_opt, 'value', 'text', $seleted).'</div>';
					}
					else
						$field = '<div class=""><input type="text" class="text-input required" id="fq['.$prefix.$field_id.']" name="fq['.$prefix.$field_id.']" value="'.$value.'"/></div>';

					break;

			case '5' :
					if($post['range'][$field_id])
					{
							$value[0] = $post['range'][$field_id][1];
							$value[1] = $post['range'][$field_id][2];
					}
					$field = '<div>
					<input class = "range" style="width:100px !important" type="text" id="range['.$field_id.'][1]" name="range['.$field_id.'][1]" size="5" value = "'.$value[0].'" /> &nbsp; - &nbsp;
					<input class = "range" style="width:100px !important" type="text" id="range['.$field_id.'][2]" name="range['.$field_id.'][2]" size="5" value = "'.$value[1].'" />
					<!--input type="hidden" id="range3[]" name="range[]" value="'.$field_id.'"/-->

					</div>';
					break;


			case '7' :

					$query = "SELECT options FROM #__advanced_search_indexer_fields WHERE field_code = '$field_id'";
					$db->setQuery($query);
					$Data = $db->loadResult();
					$final = array();
					$seleted = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $value[0]);

					if($post['radio'][$field_id][0])
						$seleted = $post['radio'][$field_id][0];

					foreach (explode(',', $Data) as $pair)
					{
						list($key, $value) = explode('|', $pair);
						$final[$key] = $value;
					}

					foreach($final as $key=>$field)
					{
						$options[] = JHTML::_('select.option',$field,$key);
					}

					$std_opt = 'class="multiple" multiple';
					$field = '<div class="">'.JHTML::_('select.radiolist', $options, 'radio['.$field_id.'][]', $std_opt, 'value', 'text', $seleted).'</div>';
					break;

		case '8' :

					foreach($post['checkbox'] as $k=>$val)
					{
						if($val == $field_id)
						{
							$vals[] = $k;
						}
					}

					$query = "SELECT options FROM #__advanced_search_indexer_fields WHERE field_code = '$field_id'";
					$db->setQuery($query);
					$Data = $db->loadResult();
					$final = array();
					$seleted = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $value[0]);

					foreach (explode(',', $Data) as $pair)
					{
						list($key, $value) = explode('|', $pair);
						$final[$key] = $value;
					}

					$i = 0;
					foreach($final as $key=>$field)
					{

						if(in_array($field, $vals))
							$checked = "checked = checked";

						if($i != 0)
							$style = "style='padding-left:15px'";

						$checkbox .= '<span '.$style.'><input type="checkbox" '.$checked.'  value = "'.$field_id.'" id="checkbox['.$field.']" name="checkbox['.$field.']">
									<label for="checkbox['.$field.']" style="padding-left: 2px; vertical-align: 3px;">'. $key.'</label></span>';
						$i++;
						$checked = "";
					}

					return $checkbox;
					break;
			case '9' :

					$field_name = 'date['.$field_id.'][0]';

					$field = '<div>
					<input style="width:100px !important" type="text" id="date['.$field_id.'][1]" name="date['.$field_id.'][1]" size="5" value = "'.$value[0].'" /> &nbsp; &nbsp;
					<input style="width:100px !important" type="text" id="date['.$field_id.'][2]" name="date['.$field_id.'][2]" size="5" value = "'.$value[1].'" />
					<!--input type="hidden" id="range3[]" name="date[]" value="'.$field_id.'"/-->

					</div>';

					/*$field1 = JHTML::calendar($post['date'][$field_id][0], $field_name, $field_name, '%Y-%m-%d',
								array('size'=>'12',
								'maxlength'=>'10',
								'class'=>' validate[\'required\']',
								));

					$field_name = 'date['.$field_id.'][1]';
					$field2 = '<span style="padding-left:10px">'.JHTML::calendar($post['date'][$field_id][1], $field_name, $field_name, '%Y-%m-%d',
								array('size'=>'12',
								'maxlength'=>'10',
								'class'=>' validate[\'required\']',
								)).'</span>';				*/
					//$field = $field1.$field2;
					break;
			case '10' :

					if ($field_id)
					{
						$query 	= $db->getQuery(true);
						$query 	->select('id, name')
								->from('#__zoo_category')
								->where("parent = " . $field_id);
						$db->setQuery($query);
						$cdtData = $db->loadObjectList();
					}

					if($cdtData)
					{
						foreach($cdtData as $cdt)
						{
							$options[] = JHTML::_('select.option',$cdt->id, $cdt->name);
						}

						$std_opt = 'class="multiple" multiple';
						$field = '<div class="">'.JHTML::_('select.genericlist',$options, 'fq[secondary_cat][]', $std_opt, 'value', 'text', $seleted).'</div>';
					}

					break;


			default :
					$field = '<div class=""><input type="text" class="text-input required" id="fq['.$prefix.$field_id.']" name="fq['.$prefix.$field_id.']" value="'.$value.'"/></div>';
					break;

		}
		return $field;
	}
	//getFieldColumType ends here

	function getFooterNote()
	{
			$footer = '<table style="margin-bottom: 5px; width: 100%; border-top: thin solid #e5e5e5; table-layout: fixed;">
						<tbody>
							<tr>
								<td style="text-align: left; width: 33%;">
									<a href="http://techjoomla.com/index.php?option=com_billets&amp;view=tickets&amp;layout=form&amp;Itemid=18" target="_blank">TechJoomla Support Center</a>
									<br>
									<a href="http://twitter.com/techjoomla" target="_blank">Follow Us on Twitter</a>
									<br>
									<a href="http://www.facebook.com/techjoomla" target="_blank">Follow Us on FaceBook</a>
									<br>
									<a href="http://extensions.joomla.org/extensions/communication/instant-messaging/9344" target="_blank">Leave JED Feedback </a>
								</td>
								<td style="text-align: center; width: 50%;">Advanced Search.<br>
									Copyright (C) 2010-2011 <a href="http://techjoomla.com/" taget="_blank">TechJoomla</a>. All rights reserved.
									<br>
									Your Current Version is 2.7.6
									<br>
									<span class="latestbutton" onclick="vercheck();"> Check Latest Version</span>
									<span id="NewVersion" style="padding-top: 5px; color: #000000; font-weight: bold; padding-left: 5px;"></span>
								</td>
								<td style="text-align: right; width: 33%;">
									<a href="http://techjoomla.com/" taget="_blank"> <img src="" alt="TechJoomla" style="vertical-align:text-top;"></a>
								</td>
							</tr>
						</tbody>
					</table>';
			return $footer;

	}


}
?>
