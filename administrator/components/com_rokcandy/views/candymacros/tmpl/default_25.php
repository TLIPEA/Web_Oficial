<?php
/**
 * @version $Id: default_25.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == -2 ? true : false;
$canOrder = $user->authorise('core.edit.state', 'com_rokcandy.category');
$saveOrder = $listOrder == 'a.ordering';
?>

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
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search"
                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                   title="<?php echo JText::_('COM_ROKCANDY_SEARCH_IN_NAME'); ?>"/>
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button"
                    onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">

            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>

            <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_rokcandy'), 'value', 'text', $this->state->get('filter.category_id'));?>
            </select>
        </div>
    </fieldset>
    <div class="clr"></div>

    <table class="adminlist">
        <thead>
        <tr>
            <th width="1%">
                <input type="checkbox" name="checkall-toggle" value=""
                       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
            </th>
            <th>
                <?php echo JHtml::_('grid.sort', 'COM_ROKCANDY_MACRO', 'a.macro', $listDirn, $listOrder); ?>
            </th>
            <th>
                <?php echo JHtml::_('grid.sort', 'COM_ROKCANDY_HTML', 'a.html', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                <?php if ($canOrder && $saveOrder) : ?>
                <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'candymacros.saveorder'); ?>
                <?php endif; ?>
            </th>
            <th width="1%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="10">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
            <?php
            $n = count($this->items);
            foreach ($this->items as $i => $item) :
                $ordering = $listOrder == 'a.ordering';
                $canCreate = $user->authorise('core.create', 'com_rokcandy.category.' . $item->catid);
                $canEdit = $user->authorise('core.edit', 'com_rokcandy.category.' . $item->catid);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                $canEditOwn = $user->authorise('core.edit.own', 'com_rokcandy.category.' . $item->catid);
                $canChange = $user->authorise('core.edit.state', 'com_rokcandy.category.' . $item->catid) && $canCheckin;

                $item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_rokcandy&task=edit&type=other&id=' . $item->catid);
                ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                    <?php if ($item->checked_out) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'candymacros.', $canCheckin); ?>
                    <?php endif; ?>
                    <?php if ($canEdit || $canEditOwn) : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_rokcandy&task=candymacro.edit&id=' . (int)$item->id); ?>">
                        <?php echo $this->escape($item->macro); ?></a>
                    <?php else : ?>
                    <?php echo $this->escape($item->macro); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $this->escape($item->html);?>
                </td>
                <td align="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'candymacros.', $canChange, 'cb'); ?>
                </td>
                <td align="center">
                    <?php echo $item->category_title; ?>
                </td>
                <td class="order">
                    <?php if ($canChange) : ?>
                    <?php if ($saveOrder) : ?>
                        <?php if ($listDirn == 'asc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'candymacros.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i + 1]->catid), 'candymacros.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'candymacros.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i + 1]->catid), 'candymacros.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                    <input type="text" name="order[]" size="5"
                           value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order"/>
                    <?php else : ?>
                    <?php echo $item->ordering; ?>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <?php echo $item->id; ?>
                </td>
            </tr>
                <?php endforeach; ?>

            <?php if ((count($this->overrides) > 0) && ($this->showOverrides)): ?>
            <?php $i = 0;
            $i++;
            $n = 0; ?>
            <?php foreach ($this->overrides as $macro => $html) : ?>
            <tr class="<?php echo $i % 2; ?>">
                <td align="center"><input type="checkbox" disabled="disabled"></td>
                <td class="macro"><?php echo $macro; ?></td>
                <td><?php echo htmlentities($html); ?></td>
                <td align="center"><?php echo JHtml::_('image', 'admin/tick.png', '', array('border' => 0), true);?></td>
                <td align="center" class="macro">Template Overrides</td>
                <td class="order"><input type="text" name="order[]" size="5" value="<?php echo 'n/a';?>"
                                         disabled="disabled" class="text-area-order"/></td>
                <td align="center"><?php echo "t-" . $n++; ?>
            </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>