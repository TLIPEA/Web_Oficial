<?php
/**
 * @version $Id: candymacro.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class RokCandyTableCandyMacro extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__rokcandy', 'id', $db);
	}

    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }
        return parent::bind($array, $ignore);
    }


    public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}

        $this->macro = html_entity_decode($this->macro);
        $this->html = html_entity_decode($this->html);

        // Verify that the macro is unique
		$table = JTable::getInstance('CandyMacro', 'RokCandyTable');
		if ($table->load(array('macro'=>$this->macro,'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
			$this->setError(JText::_('COM_ROKCANDY_ERROR_UNIQUE_MACRO'));
			return false;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{

		/** check for valid name */
		if (trim($this->macro) == '' or trim($this->html == '')) {
			$this->setError(JText::_('COM_ROKCANDY_WARNING_BOTH'));
			return false;
		}

        // Set ordering
		if ($this->published < 0) {
	    // Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		} elseif (empty($this->ordering)) {
		// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`catid`=' . $this->_db->Quote($this->catid).' AND state>=0');
		}

		return true;
	}
}
