<?php
/**
 * @version   $Id: item.php 13419 2013-09-11 17:23:09Z arifin $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 */
?>
<?php if($parameters->get('lists_enable_accordion')): ?>
	<li <?php if (!$parameters->get('lists_enable_accordion') || $index == 0): ?>class="active" <?php endif;?>data-lists-item>
		<?php if ($item->custom_can_show_title): ?>
		<h4 class="sprocket-lists-title<?php if ($parameters->get('lists_enable_accordion')): ?> padding<?php endif; ?>" data-lists-toggler>
			<?php if ($item->custom_can_have_link): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
				<?php echo $item->getTitle();?>
			<?php if ($item->custom_can_have_link): ?></a><?php endif; ?>
			<span class="indicator"><span>+</span></span>
		</h4>
		<?php endif; ?>
		<span class="sprocket-lists-item" data-lists-content>
			<span class="sprocket-padding">
				<?php if ($item->getPrimaryImage()) :?>
				<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" class="sprocket-lists-image" alt="" />
				<?php endif; ?>
				<span class="sprocket-lists-desc <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
					<?php echo $item->getText(); ?>
				</span>
				<?php if ($item->getPrimaryLink()) : ?>
				<span class="readon-wrapper <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
					<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
				</span>
				<?php endif; ?>
			</span>
		</span>
	</li>
<?php endif; ?>

<?php if (!$parameters->get('lists_enable_accordion')): ?>
	<li <?php if (!$parameters->get('lists_enable_accordion') || $index == 0): ?>class="active" <?php endif;?>data-lists-item>
		<?php if ($item->custom_can_show_title): ?>
		<h4 class="sprocket-lists-title" data-lists-toggler>
			<?php if ($item->custom_can_have_link): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
				<?php echo $item->getTitle();?>
			<?php if ($item->custom_can_have_link): ?></a><?php endif; ?>
		</h4>
		<?php endif; ?>
		<span class="sprocket-lists-item" data-lists-content>
			<span class="sprocket-padding">
				<?php if ($item->getPrimaryImage()) :?>
				<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" class="sprocket-lists-image" alt="" />
				<?php endif; ?>
				<span class="sprocket-lists-desc <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
					<?php echo $item->getText(); ?>
				</span>
				<?php if ($item->getPrimaryLink()) : ?>
				<span class="readon-wrapper <?php if (!($item->getPrimaryImage())) : ?>img-disabled<?php endif; ?>">
					<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
				</span>
				<?php endif; ?>
			</span>
		</span>
	</li>
<?php endif; ?>