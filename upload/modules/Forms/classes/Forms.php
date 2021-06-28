<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 */

class Forms {
    private $_db;
    
    // Constructor, connect to database
    public function __construct(){
        $this->_db = DB::getInstance();
    }
    
    // Can the user post a submission in the given form?
    public function canPostSubmission($group_ids, $form_id) {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `post` FROM nl2_forms_permissions WHERE form_id = ? AND `post` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canViewOwnSubmission($group_ids, $form_id) {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `view_own` FROM nl2_forms_permissions WHERE form_id = ? AND `view_own` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canViewSubmission($group_ids, $form_id) {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `view` FROM nl2_forms_permissions WHERE form_id = ? AND `view` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canDeleteSubmission($group_ids, $form_id) {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `can_delete` FROM nl2_forms_permissions WHERE form_id = ? AND `can_delete` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    /*
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    public static function updateCheck($current_version = null) {
        $queries = new Queries();

        // Check for updates
        if (!$current_version) {
            $current_version = $queries->getWhere('settings', array('name', '=', 'nameless_version'));
            $current_version = $current_version[0]->value;
        }

        $uid = $queries->getWhere('settings', array('name', '=', 'unique_id'));
        $uid = $uid[0]->value;
        
        $enabled_modules = Module::getModules();
        foreach($enabled_modules as $enabled_item){
            if($enabled_item->getName() == 'Forms'){
                $module = $enabled_item;
                break;
            }
        }
        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=Forms&module_version='.$module->getVersion() . '&domain='. Util::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($update_check);
        if (isset($info->message)) {
            die($info->message);
        }
        
        return $update_check;
    }
}