<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Advsearch
 * @author     Amol Patil <example@example.com>
 * @copyright  2017 Amol Patil.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/helpers/advsearch.php';

/**
 * AdvsearchController class.
 *
 * @since  1.0.0
 */
class AdvsearchController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return JController  This object to support chaining.
	 *
	 * @since  1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/advsearch.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'searchindexer');

		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	/**
	 *  This method returns the list of client types of certain types from client plugins
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function get_types()
	{
		$client = JRequest::getVar('client');
		$id     = JRequest::getVar('id');

		if ($id && $id != "undefined")
		{
			$model       = $this->getModel('createindexer');
			$IndexerData = $model->getIndexer($id);
			$disabled    = 'disabled="disabled"';
		}

		JPluginHelper::importPlugin('advsearch', $client);
		$dispatcher = & JDispatcher::getInstance();
		$types      = $dispatcher->trigger('getType', array());

		if (!(empty($types)))
		{
			echo '
				<div class="span2" style= "font-weight:bold;">' . JText::_('COM_ADVSEARCH_SELECT_TYPES') . '</div>
				<div  class="span6">';
					$options = array();
					$options[] = JHTML::_('select.option', 'Select Classification', '0');

					foreach ($types[0] as $row)
					{
						$options[] = JHTML::_('select.option', $row->name, $row->alias);
					}

					echo JHTML::_('select.genericlist', $options, 'select_types', 'class="required"' . $disabled . '', 'text', 'value', $IndexerData->type_name);

					if ($id)
					{
						echo '<span style="display:none">';
							echo JHTML::_('select.genericlist', $options, 'select_types', 'class="required" ', 'text', 'value', $IndexerData->type_name);
						echo '</span>';
					}

			echo '</div> ';
		}
		else
		{
			echo '<td style="font-weight:bold;">' . JText::_('COM_ADVSEARCH_SELECT_TYPES') . '</td>
				  <td>Fields not found</td>';
		}

		jexit();
	}

	/**
	 *  This methods checks whether indexer is created. If its not, trigger plugin event which returns the fields.
	 *  We may extend this in future
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function getFields()
	{
		//  Check whether Indexer is created for the type of the field.
		$model  = $this->getModel('createindexer');
		$result = $model->getFields();
		$id     = JRequest::getVar('id');

		// If indexer found then tell the user that you have already created indexer
		// Else fetch fields by triggering getFields plugin event.

		if ($result && $id == "undefined")
		{
			echo $id;
			die('In Controller Line No 106');
			echo '0';
			Jexit();
		}
		else
		{
			$type = JRequest::getVar('type');
			$id   = JRequest::getVar('id');

			if ($id && $id != "undefined")
			{
				$model       = $this->getModel('createindexer');
				$IndexerData = $model->getIndexer($id);

				$IndexerFieldsData = $model->getIndexerFields($id);
				$link              = JURI::Root() . 'index.php?option=com_advsearch&task=cronjob&pkey=abcd1234&type=' . $IndexerData->type_name;

				$cronurl = '<div class="span12">';
					$cronurl .= '<div class="span2"><strong>' . JText::_('COM_ADVSEARCH_CRON_URL') . '</strong></div>';
					$cronurl .= '<div class="span9"><a target="_blank" href="' . $link . '">' . $link . '</a></div>';
				$cronurl .= '</div>';
			}

			JPluginHelper::importPlugin('advsearch', $client);
			$dispatcher =& JDispatcher::getInstance();
			$fields     = $dispatcher->trigger('getFields', array($type));

			if ($fields[0])
			{
				if ($IndexerData->name)
				{
					$type = $IndexerData->name;
				}

				if ($IndexerData->batch_size > 0)
				{
					$batch_size = $IndexerData->batch_size;
				}

				echo '<div>
						<div class="span12" style="margin-left:15px;">
							<div class="span2"><strong>' . JText::_('COM_ADVSEARCH_SEARCH_INDEXER_NAME') . '</strong></div>
							<div class="span6"> <input type="text" name="name-for-type" value="' . $type . '" /></div>
						</div>
						<div class="span12">
							<div class="span2"><strong>' . JText::_('COM_ADVSEARCH_SEARCH_BATCH_SIZE') . '</strong></div>
							<div class="span6"><input type="text" name="batch_size" size="8" value="' . $batch_size . '"></div>
						</div>';

						echo $cronurl;

						echo '<div class="table-responsive table-condensed" style="width:100%;height:400px;">
								<table class="table">
									<thead style="background:#F5F5F5;">
										<tr>
											<th><div>' . JText::_('COM_ADVSEARCH_SEARCH_FIELD_LABEL') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_SEARCH_FIELD_LABEL_FOR_SEARCH') . '</div></th>
											<!--th class="second"><div>' . JText::_('COM_ADVSEARCH_SEARCH_ZOO_DATA_TYPE') . '</div></th-->
											<th><div>' . JText::_('COM_ADVSEARCH_SEARCH_MAP_WITH_DATA_TYPE') . '</div></th>
											<th style="display:none"><div>' . JText::_('COM_ADVSEARCH_ORDERING') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_MAP_BASIC_SEARCH') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_SEARCH_TERM') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_GRID_FILTER') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_LANDING_PAGE') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_USE_AS') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_CATEGORY_SEARCH') . '</div></th>
											<th><div>' . JText::_('COM_ADVSEARCH_DISPLAY_IN_SEARCH') . '</div></th>
											<th></th>
										</tr>
									</thead>
									<tbody>';
			}
			else
			{
				echo JText::_('COM_ADVSEARCH_FIELD_NOT_FOUND');
			}

			$i  = 0;
			$db = JFactory::getDBO();

			foreach ($fields[0] as $k => $ele)
			{
				if ($id && $id != "undefined")
				{
					$query = "select * FROM #__advanced_search_indexer_fields where field_code = '$k' AND indexer_id = $id";
					$db->setQuery($query);
					$DataResult = $db->loadObject();
				}

				$val = $i % 2;

				if ($DataResult)
				{
					echo '<tr id="row' . $i . '" class="row' . $val . '" >';
							echo '<td style="width:200px">' . $ele['name'] . '</td>';
							echo '<td>
								<input type="text" name="mapping_label[' . $i . ']" id="mapping_label[' . $i . ']" value="' . $DataResult->mapping_label . '" size="40" />
								<input type="hidden" name="field_code[' . $i . ']" id="field_code[' . $i . ']" value="' . $k . '"/>
								<input type="hidden" name="field_type[' . $i . ']" id="field_type[' . $i . ']" value="' . $ele['type'] . '"/>
								<input type="hidden" name="field_name[' . $i . ']" id="field_name[' . $i . ']" value="' . $ele['name'] . '"/>
								<input type="hidden" name="field_options[' . $i . ']" id="field_name[' . $i . ']" value="' . $ele['option'] . '"/>
							</td>';

							echo '<td>';
								echo AdvsearchHelper::categoryDropdownListAdd($i, $ele['type'], $DataResult->mapping_field);
							echo '</td>';

							if ($DataResult->ordering > 0)
							{
								$ordering = $DataResult->ordering;
							}

							if ($DataResult->basic_search == 1)
							{
								$checked = "checked='checked'";
							}
							else
							{
								$checked = "";
							}

							echo '<td><input type="checkbox" ' . $checked . ' name="basic_search[' . $i . ']" id="basic_search[' . $i . ']" size="40" /></td>';

							if ($DataResult->search_term == 1)
							{
								$checked = "checked='checked'";
							}
							else
							{
								$checked = "";
							}

							echo '<td><input type="checkbox" ' . $checked . ' name="search_term[' . $i . ']" id="search_term[' . $i . ']" size="40" /></td>';

							if ($DataResult->grid_filter == 1)
							{
								$checkeds = "checked='checked'";
							}
							else
							{
								$checkeds = "";
							}

							echo '<td><input type="checkbox" ' . $checkeds . ' name="grid_filter[' . $i . ']" id="grid_filter[' . $i . ']" size="40" /></td>';

							if ($DataResult->landing_page == 1)
							{
								$checkedss = "checked='checked'";
							}
							else
							{
								$checkedss = "";
							}

							echo '<td><input type="checkbox" ' . $checkedss . ' name="landing_page[' . $i . ']" id="landing_page[' . $i . ']" size="40" /></td>';

							echo '<td>';
								echo AdvsearchHelper::UseAsDropdown($i, $ele['type'], $DataResult->useas);
							echo '</td>';

							if ($DataResult->category_search == 1)
							{
								$checkedCS = "checked='checked'";
							}
							else
							{
								$checkedCS = "";
							}

							echo '<td><input type="checkbox" ' . $checkedCS . ' name="category_search[' . $i . ']" id="category_search[' . $i . ']" size="40" /></td>';

							if ($DataResult->display_search == 1)
							{
								$checkedCS = "checked='checked'";
							}
							else
							{
								$checkedCS = "";
							}

							echo '<td><input type="checkbox" ' . $checkedCS . ' name="display_search[' . $i . ']" id="display_search[' . $i . ']" size="40" /></td>';

					echo '</tr>';
				}
				else
				{
					echo '<tr id="row' . $i . '" class="row' . $val . '" >';
						echo '<td>' . $ele['name'] . '<br>';
						echo '<input type="text" name="field_type[' . $i . ']" readonly="readonly" id="field_type[' . $i . ']" value="' . $ele['type'] . '"/></td>';
						echo '<td style="">
							<input type="text" name="mapping_label[' . $i . ']" id="mapping_label[' . $i . ']" value="' . $ele['label'] . '" size="40" />
							<input type="hidden" name="field_code[' . $i . ']" id="field_code[' . $i . ']" value="' . $k . '"/>

							<input type="hidden" name="field_name[' . $i . ']" id="field_name[' . $i . ']" value="' . $ele['name'] . '"/>
							<input type="hidden" name="field_options[' . $i . ']" id="field_name[' . $i . ']" value="' . $ele['option'] . '"/>
						</td>';

						echo '<td>';
							echo AdvsearchHelper::categoryDropdownListAdd($i, $ele['type'], 0);
						echo '</td>';

						echo '<td><input type="checkbox" name="basic_search[' . $i . ']" id="basic_search[' . $i . ']" size="40" /></td>';
						echo '<td><input type="checkbox" name="search_term[' . $i . ']" id="search_term[' . $i . ']" size="40" /></td>';
						echo '<td><input type="checkbox" name="grid_filter[' . $i . ']" id="grid_filter[' . $i . ']" size="40" /></td>';
						echo '<td><input type="checkbox" name="landing_page[' . $i . ']" id="landing_page[' . $i . ']" size="40" /></td>';

						echo '<td>';
							echo AdvsearchHelper::UseAsDropdown($i, $ele['type'], 0);
						echo '</td>';

						echo '<td><input type="checkbox" name="category_search[' . $i . ']" id="category_search[' . $i . ']" size="40" /></td>';
						echo '<td><input type="checkbox" name="display_search[' . $i . ']" id="display_search[' . $i . ']" size="40" /></td>';
					echo '</tr>';
				}

				$i++;
			}

			echo ' </tbody> </table> </div>';

			jexit();
		}
	}
}
