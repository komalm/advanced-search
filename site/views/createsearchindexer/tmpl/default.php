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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_advsearch', JPATH_ADMINISTRATOR);
?>

<?php if( $this->item ) : ?>

    <div class="item_fields">
        
        <ul class="fields_list">

			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_CLIENT'); ?>:
			<?php echo $this->item->client; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_CONTENT_TYPE'); ?>:
			<?php echo $this->item->content_type; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_FIELD_CODE'); ?>:
			<?php echo $this->item->field_code; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_FIELD_TYPE'); ?>:
			<?php echo $this->item->field_type; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_MAP_TABLE'); ?>:
			<?php echo $this->item->map_table; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_MAPPING_FIELD'); ?>:
			<?php echo $this->item->mapping_field; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_MAPPING_LABEL'); ?>:
			<?php echo $this->item->mapping_label; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_OPTIONS'); ?>:
			<?php echo $this->item->options; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_CATEGORY'); ?>:
			<?php echo $this->item->category; ?></li>
			<li><?php echo JText::_('COM_ADVSEARCH_FORM_LBL_CREATESEARCHINDEXER_PUBLISHED'); ?>:
			<?php echo $this->item->published; ?></li>


        </ul>
        
    </div>
    <?php if(JFactory::getUser()->authorise('core.edit', 'com_advsearch.createsearchindexer'.$this->item->id)): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_advsearch&task=createsearchindexer.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
