<?php
/**
 * @version   $Id: ExternalOnTop.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_Sort_ExternalOnTop implements RokBooster_Compressor_ISort
{

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $groups = array();

	/**
	 * @var string
	 */
	protected $last_type;


	/**
	 * @var RokBooster_Compressor_FileGroup
	 */
	protected $current_group;

	/**
	 * @var RokBooster_Compressor_FileGroup
	 */
	protected $current_external_group;

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $external_groups = array();

	/**
	 * @param RokBooster_Compressor_File $file
	 *
	 * @return mixed
	 */
	public function addFile(RokBooster_Compressor_File $file)
	{

		if ($file->isExternal()) {

			if (!isset($this->current_external_group) || (!isset($this->current_group) || ($this->current_group->getMime() != $file->getMime() || $file->getAttributes() != $this->current_group->getAttributes()))) {
				$group                        = new RokBooster_Compressor_FileGroup(RokBooster_Compressor_FileGroup::STATE_IGNORE, $file->getMime(), $file->getAttributes());
				$this->external_groups[]      =& $group;
				$this->current_external_group =& $group;
			}
			$this->current_external_group->addItem($file);
		} else {
			if ($file->isIgnored()) {
				$current_type = RokBooster_Compressor_FileGroup::STATE_IGNORE;
			} else {
				$current_type = RokBooster_Compressor_FileGroup::STATE_INCLUDE;
			}
			if (!isset($this->current_group) || ($current_type != $this->current_group->getStatus() || $this->current_group->getMime() != $file->getMime() || $file->getAttributes() != $this->current_group->getAttributes())) {
				$this->last_type     = $current_type;
				$group               = new RokBooster_Compressor_FileGroup($current_type, $file->getMime(), $file->getAttributes());
				$this->groups[]      =& $group;
				$this->current_group =& $group;
			}
			$this->current_group->addItem($file);
		}
	}

	/**
	 * @return RokBooster_Compressor_FileGroup[]
	 */
	public function getGroups()
	{
		return array_merge($this->external_groups, $this->groups);
	}
}
