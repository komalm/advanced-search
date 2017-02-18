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

jimport('joomla.application.component.controllerform');

/**
 * Createsearchindexer controller class.
 */
class AdvsearchControllerCreatesearchindexer extends JControllerForm
{

    function __construct() {
        $this->view_list = 'searchindexer';
        parent::__construct();
    }

}