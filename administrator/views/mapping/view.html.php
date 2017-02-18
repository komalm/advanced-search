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

jimport('joomla.application.component.view');

/**
 * View class for a list of Advsearch.
 */
class AdvsearchViewMapping extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        AdvsearchHelper::addSubmenu($view);

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/advsearch.php';

		$state	= $this->get('State');
		$canDo	= AdvsearchHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_ADVSEARCH_TITLE_SEARCHINDEXER'), 'searchindexer.png');

        //Check if the form exists before showing the add/edit buttons
		JToolBarHelper::addNew('createmapping.add');
          JToolBarHelper::editList('createindexer.edit','JTOOLBAR_EDIT');

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'mapping.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			     JToolBarHelper::deleteList('', 'mapping.delete','JTOOLBAR_DELETE');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_advsearch');
		}


	}
}
