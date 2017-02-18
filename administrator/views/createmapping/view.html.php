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
class AdvsearchViewCreatemapping extends JViewLegacy
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
		$this->item         = $this->get('DataMapp');
		$this->pagination	= $this->get('Pagination');

		$this->Indexer	= $this->get('Indexer');

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
	protected function addToolBar()
	{
		//JRequest::setVar('hidemainmenu', true);
		//$isNew = ($this->item->id == 0);
		$isNew = 0;
		JToolBarHelper::title($isNew ? JText::_('Add Mapping') : JText::_('Add Mapping'));
		JToolBarHelper::save('createmapping.saveMapping');
		JToolBarHelper::cancel('createmapping.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}
