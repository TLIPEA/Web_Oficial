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
<li>
	<span class="sprocket-headlines-item<?php echo (!$index) ? ' active' : ''; ?>" data-headlines-item>
		<?php if ( $item->getPrimaryImage()) :?>
		<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" class="sprocket-headlines-image" />
		<?php endif; ?>
		<?php if ($item->getPrimaryLink()) : ?>
		<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="sprocket-headlines-text">
		<?php else : ?>
		<span class="sprocket-headlines-text">
		<?php endif; ?>
			<?php echo $item->getText(); ?>
		<?php if ($item->getPrimaryLink()) : ?>
		</a>
		<?php else : ?>
		</span>
		<?php endif; ?>
	</span>
</li>
