<?php
/**
 * @version   $Id: item.php 10885 2013-05-30 06:31:41Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 */
?>
<li <?php if (!$parameters->get('lists_enable_accordion') || $index == 0): ?>class="active" <?php endif;?>data-lists-item>
	<?php if ($item->custom_can_show_title): ?>
	<h4 class="sprocket-lists-title<?php if ($parameters->get('lists_enable_accordion')): ?> padding<?php endif; ?>" data-lists-toggler>
		<?php if ($item->custom_can_have_link): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
			<?php echo $item->getTitle();?>
		<?php if ($item->custom_can_have_link): ?></a><?php endif; ?>
		<?php if ($parameters->get('lists_enable_accordion')): ?><span class="indicator"><span>+</span></span><?php endif; ?>
	</h4>
	<?php endif; ?>
	<span class="sprocket-lists-item" data-lists-content>
		<span class="sprocket-padding">
			<?php if ($item->getPrimaryImage()) :?>
			<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" class="sprocket-lists-image" alt="" />
			<?php endif; ?>
			<?php echo $item->getText(); ?>
			<?php if ($item->getPrimaryLink()) : ?>
			<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
			<?php endif; ?>
		</span>
	</span>
</li>
