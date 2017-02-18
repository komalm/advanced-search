<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_advsearch', JPATH_ADMINISTRATOR);


?>

<!-- Styling for making front end forms look OK -->
<!-- This should probably be moved to the template CSS file -->
<style>
    .front-end-edit ul {
        padding: 0 !important;
    }
    .front-end-edit li {
        list-style: none;
        margin-bottom: 6px !important;
    }
    .front-end-edit label {
        margin-right: 10px;
        display: block;
        float: left;
        width: 200px !important;
    }
    .front-end-edit .radio label {
        display: inline;
        float: none;
    }
    .front-end-edit .readonly {
        border: none !important;
        color: #666;
    }    
    .front-end-edit #editor-xtd-buttons {
        height: 50px;
        width: 600px;
        float: left;
    }
    .front-end-edit .toggle-editor {
        height: 50px;
        width: 120px;
        float: right;
        
    }
</style>

<div class="createsearchindexer-edit front-end-edit">
    <h1>Edit <?php echo $this->item->id; ?></h1>

    <form id="form-createsearchindexer" action="<?php echo JRoute::_('index.php?option=com_advsearch&task=createsearchindexer.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <ul>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('client'); ?>
				<?php echo $this->form->getInput('client'); ?></li>
				<li><?php echo $this->form->getLabel('content_type'); ?>
				<?php echo $this->form->getInput('content_type'); ?></li>
				<li><?php echo $this->form->getLabel('field_code'); ?>
				<?php echo $this->form->getInput('field_code'); ?></li>
				<li><?php echo $this->form->getLabel('field_type'); ?>
				<?php echo $this->form->getInput('field_type'); ?></li>
				<li><?php echo $this->form->getLabel('map_table'); ?>
				<?php echo $this->form->getInput('map_table'); ?></li>
				<li><?php echo $this->form->getLabel('mapping_field'); ?>
				<?php echo $this->form->getInput('mapping_field'); ?></li>
				<li><?php echo $this->form->getLabel('mapping_label'); ?>
				<?php echo $this->form->getInput('mapping_label'); ?></li>
				<li><?php echo $this->form->getLabel('options'); ?>
				<?php echo $this->form->getInput('options'); ?></li>
				<li><?php echo $this->form->getLabel('category'); ?>
				<?php echo $this->form->getInput('category'); ?></li>
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>

        </ul>
		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<?php echo JText::_('or'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_advsearch&task=createsearchindexer.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_advsearch" />
			<input type="hidden" name="task" value="createsearchindexer.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
