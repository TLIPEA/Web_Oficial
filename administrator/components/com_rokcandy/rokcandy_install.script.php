<?php
/**
 * @version $Id: rokcandy_install.script.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.helper');
jimport('joomla.filesystem.file');

class Com_RokCandyInstallerScript
{

    /**
     * method to run after an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     * @return void
     */
    public function postflight($type, $parent)
    {
         if ($type == 'install') {
            $this->installFromFile($parent, '/sql/install.mysql.utf8.sql');
            $this->installFromFile($parent, '/sql/sampledata.sql');
         }
        
        if ($type == 'uninstall') {
            $this->installFromFile($parent, '/sql/uninstall.mysql.utf8.sql');
        }
    }

    public function install($parent)
    {
        $this->parent = $parent;

        $this->save_category(array(
             'extension' => 'com_rokcandy',
             'title' => 'Basic',
             'alias' => 'basic',
             'published' => 1,
             'id' => 0));

        $this->save_category(array(
              'extension' => 'com_rokcandy',
              'title' => 'Typography',
              'alias' => 'typography',
              'published' => 1,
              'id' => 0));

        $this->save_category(array(
              'extension' => 'com_rokcandy',
              'title' => 'Uncategorised',
              'alias' => 'uncategorised',
              'published' => 1,
              'id' => 0));

        return true;

    }

    protected function save_category($data)
    {
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $table = JTable::getInstance('category');
        $pk = (!empty($data['id'])) ? $data['id'] : 0;
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Load the row if saving an existing category.
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }

        $data['parent_id'] = "";

        // This is a new category
        if ($isNew) {
            $table->setLocation($data['parent_id'], 'last-child');
        }
        //not new but doesn't match
        elseif (!$isNew && $table->parent_id != $data['parent_id']) {
             $table->setLocation($data['parent_id'], 'last-child');
         }

        // Alter the title for save as copy
        if (!$isNew && $data['id'] == 0 && $table->parent_id == $data['parent_id'])
        {
            $m = null;
            $data['alias'] = '';
            if (preg_match('#\((\d+)\)$#', $table->title, $m))
            {
                $data['title'] = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->title);
            }
            else
            {
                $data['title'] .= ' (2)';
            }
        }

        // Bind the data.
        if (!$table->bind($data))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Bind the rules.
        if (isset($data['rules']))
        {
            $rules = new JRules($data['rules']);
            $table->setRules($rules);
        }

        // Check the data.
        if (!$table->check())
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Trigger the onContentBeforeSave event.
        $result = $dispatcher->trigger('onContentBeforeSave', array('com_category.category', &$table, $isNew));
        if (in_array(false, $result, true))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Store the data.
        if (!$table->store())
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Trigger the onContentAfterSave event.
        $dispatcher->trigger('onContentAfterSave', array('com_category.category', &$table, $isNew));

        // Rebuild the tree path.
        if (!$table->rebuildPath($table->id))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        return true;

    }


    /**
     * @param $parent JInstallerComponent
     */
    protected function installFromFile($parent, $path=false)
    {
        if ($path === false) {
            JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_NO_PATH'));

            return false;
        }

        $installer = $parent->getParent();
        $db =JFactory::getDbo();

        // Initialise variables.
        $queries = array();

        $sqlfile = $installer->getPath('extension_root') . $path;

        // Check that sql files exists before reading. Otherwise raise error for rollback
        if (!file_exists($sqlfile)) {
            JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_FILENOTFOUND', $sqlfile));

            return false;
        }

        $buffer = file_get_contents($sqlfile);

        // Graceful exit and rollback if read not successful
        if ($buffer === false) {
            JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));

            return false;
        }

        // Create an array of queries from the sql file
        jimport('joomla.installer.helper');
        $queries = JInstallerHelper::splitSql($buffer);

        if (count($queries) == 0) {
            // No queries to process
            return 0;
        }

        // Process each query in the $queries array (split out of sql file).
        foreach ($queries as $query)
        {
            $query = trim($query);

            if ($query != '' && $query{0} != '#') {
                $db->setQuery($query);

                if (!$db->query()) {
                    JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

                    return false;
                }
            }
        }


        return (int)count($queries);
    }

    function preflight($type, $parent) {


        if ($type == 'install') {

            //do a little cleanup before install
            $db = JFactory::getDBO();

            $db->setQuery("DELETE FROM #__menu WHERE path LIKE 'rokcandy%'");
            $db->query();

            $db->setQuery("DELETE FROM #__assets WHERE id IN (SELECT asset_id as id from #__categories where extension = 'com_rokcandy')");
            $db->query();

            $db->setQuery("DELETE FROM #__categories WHERE extension = 'com_rokcandy'");
            $db->query();
        }
    }

    public function uninstall($parent) {
        
        $dispatcher = JDispatcher::getInstance();
        $table = JTable::getInstance('category');

        // Uninstalls RokCandy system plugin and RokCandy button editor
        jimport('joomla.installer.installer');

        $db1 = JFactory::getDBO();
        $db1->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "rokcandy" AND `folder` = "system"');
        $id1 = $db1->loadResult();
        if($id1){
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id1,1);
        }

        // Uninstalls RokCandy button editor plugin
        $db2 = JFactory::getDBO();
        $db2->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "rokcandy" AND `folder` = "editors-xtd"');
        $id2 = $db2->loadResult();
        if($id2){
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id2,1);
        }
    }
}