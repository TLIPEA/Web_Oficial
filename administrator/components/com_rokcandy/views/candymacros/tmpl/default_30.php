<?php
/**
 * @version $Id: default_30.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == -2 ? true : false;
$canOrder = $user->authorise('core.edit.state', 'com_rokcandy.category');
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_contact&task=contacts.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();?>

<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rokcandy'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty($this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
    	<div id="j-main-container" class="span10">
    <?php else : ?>
    	<div id="j-main-container">
    <?php endif;?>
<div id="filter-bar" class="btn-toolbar">
    <div class="filter-search btn-group pull-left">
        <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></label>
        <input type="text" name="filter_search" id="filter_search"
               placeholder="<?php echo JText::_('COM_ROKCANDY_SEARCH_IN_NAME'); ?>"
               value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
               title="<?php echo JText::_('COM_ROKCANDY_SEARCH_IN_NAME'); ?>"/>
    </div>
    <div class="btn-group pull-left">
        <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i
            class="icon-search"></i></button>
        <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i>
        </button>
    </div>
    <div class="btn-group pull-right hidden-phone">
        <label for="limit"
               class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
        <?php echo $this->pagination->getLimitBox(); ?>
    </div>
    <div class="btn-group pull-right hidden-phone">
        <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
        <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
            <option
                value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
            <option
                value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
        </select>
    </div>
    <div class="btn-group pull-right">
        <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
        <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
        </select>
    </div>
</div>
<div class="clearfix"></div>

<table class="table table-striped" id="articleList">
    <thead>
    <tr>
        <th width="1%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
        <th width="1%" class="hidden-phone">
            <input type="checkbox" name="checkall-toggle" value=""
                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                   onclick="Joomla.checkAll(this)"/>
        </th>
        <th width="1%" style="min-width:55px" class="nowrap center">
            <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
        </th>
        <th>
            <?php echo JHtml::_('grid.sort', 'COM_ROKCANDY_MACRO', 'a.name', $listDirn, $listOrder); ?>
        </th>
        <th>
            <?php echo JHtml::_('grid.sort', 'COM_ROKCANDY_HTML', 'a.html', $listDirn, $listOrder); ?>
        </th>
        <th>
            <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $n = count($this->items);
    foreach ($this->items as $i => $item) :
        $ordering = $listOrder == 'a.ordering';
        $canCreate = $user->authorise('core.create', 'com_candymacro.category.' . $item->catid);
        $canEdit = $user->authorise('core.edit', 'com_candymacro.category.' . $item->catid);
        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
        $canEditOwn = $user->authorise('core.edit.own', 'com_candymacro.category.' . $item->catid);
        $canChange = $user->authorise('core.edit.state', 'com_candymacro.category.' . $item->catid) && $canCheckin;

        ?>
    <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
        <td class="order nowrap center hidden-phone">
            <?php if ($canChange) :
            $disableClassName = '';
            $disabledLabel = '';
            if (!$saveOrder) :
                $disabledLabel = JText::_('JORDERINGDISABLED');
                $disableClassName = 'inactive tip-top';
            endif; ?>
            <span class="sortable-handler hasTooltip<?php echo $disableClassName?>"
                  title="<?php echo $disabledLabel?>">
        								<i class="icon-menu"></i>
        							</span>
            <input type="text" style="display:none" name="order[]" size="5"
                   value="<?php echo $item->ordering;?>" class="width-20 text-area-order "/>
            <?php else : ?>
            <span class="sortable-handler inactive">
        								<i class="icon-menu"></i>
        							</span>
            <?php endif; ?>
        </td>
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'candymacros.', $canChange, 'cb'); ?>
        </td>
        <td class="nowrap has-context">
            <div class="pull-left">
                <?php if ($item->checked_out) : ?>
                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'candymacros.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit || $canEditOwn) : ?>
                <a href="<?php echo JRoute::_('index.php?option=com_rokcandy&task=candymacro.edit&id=' . (int)$item->id); ?>">
                    <?php echo $this->escape($item->macro); ?></a>
                <?php else : ?>
                <?php echo $this->escape($item->macro); ?>
                <?php endif; ?>
            </div>
            <div class="pull-left">
                <?php
                // Create dropdown items
                JHtml::_('dropdown.edit', $item->id, 'candymacro.');
                JHtml::_('dropdown.divider');
                if ($item->published) :
                    JHtml::_('dropdown.unpublish', 'cb' . $i, 'candymacros.'); else :
                    JHtml::_('dropdown.publish', 'cb' . $i, 'candymacros.');
                endif;

                JHtml::_('dropdown.divider');

                if ($archived) :
                    JHtml::_('dropdown.unarchive', 'cb' . $i, 'candymacros.'); else :
                    JHtml::_('dropdown.archive', 'cb' . $i, 'candymacros.');
                endif;

                if ($item->checked_out) :
                    JHtml::_('dropdown.checkin', 'cb' . $i, 'candymacros.');
                endif;

                if ($trashed) :
                    JHtml::_('dropdown.untrash', 'cb' . $i, 'candymacros.'); else :
                    JHtml::_('dropdown.trash', 'cb' . $i, 'candymacros.');
                endif;

                // render dropdown list
                echo JHtml::_('dropdown.render');
                ?>
            </div>
        </td>
        <td class="nowrap has-context">
            <?php echo $this->escape($item->html);?>
        </td>
        <td class="nowrap has-context">
            <?php if ($canEdit || $canEditOwn) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_categories&extension=com_rokcandy&task=edit&type=other&id=' . $item->catid); ?>">
                <?php echo $this->escape($item->category_title); ?></a>
            <?php else : ?>
            <?php echo $this->escape($item->category_title); ?>
            <?php endif; ?>
        </td>
        <td align="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
        <?php endforeach; ?>

    <?php if ((count($this->overrides) > 0) && ($this->showOverrides)): ?>
        <?php $i = 0;
        $i++;
        $n = 0; ?>
        <?php foreach ($this->overrides as $macro => $html) :
        $disabledLabel = JText::_('JORDERINGDISABLED');
        $disableClassName = 'inactive tip-top';?>
        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="-1">
            <td class="order nowrap center hidden-phone">
                <i class="icon-menu" style="opacity:0.4;"></i>
            </td>
            <td class="center hidden-phone"><input type="checkbox" disabled="disabled"></td>
            <td class="center"><span class="btn btn-micro inactive" title="Unpublish Item">
                <i class="icon-publish"></i></span></td>
            <td class="nowrap has-context">
                <div class="pull-left macro">
                    <?php echo $macro; ?>
                </div>
                <div class="pull-left"></div>
            </td>
            <td class="nowrap has-context"><?php echo htmlentities($html); ?></td>
            <td align="nowrap has-context" class="macro"><?php echo ucfirst(RokCandyHelper::getCurrentTemplate());?> Overrides</td>
            <td align="center"><?php echo strtolower(RokCandyHelper::getCurrentTemplate());?>
        </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="10">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
    </tfoot>
</table>
<?php //Load the batch processing form. ?>
<?php echo $this->loadTemplate('batch'); ?>

<input type="hidden" name="task" value=""/>
<input type="hidden" name="boxchecked" value="0"/>
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
<?php echo JHtml::_('form.token'); ?>
</div>
</form>
