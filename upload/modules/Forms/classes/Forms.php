<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 */

class Forms extends Instanceable {

    private DB $_db;

    /**
     * @var SubmissionBase[] $submission_sources The array of submission sources
     */
    private array $_submission_sources = [];

    /**
     * @var Language Instance of Language class for translations
     */
    private static Language $_forms_language;

    // Constructor, connect to database
    public function __construct() {
        $this->_db = DB::getInstance();
    }
    
    // Can the user post a submission in the given form?
    public function canPostSubmission($group_ids, $form_id): bool {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `post` FROM nl2_forms_permissions WHERE form_id = ? AND `post` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canViewOwnSubmission($group_ids, $form_id): bool {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `view_own` FROM nl2_forms_permissions WHERE form_id = ? AND `view_own` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canViewSubmission($group_ids, $form_id): bool {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `view` FROM nl2_forms_permissions WHERE form_id = ? AND `view` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }
    
    // Can the user view a submission in the given form?
    public function canDeleteSubmission($group_ids, $form_id): bool {
        if (is_array($group_ids)) {
            $group_ids = implode(',', $group_ids);
        }
        
        return $this->_db->query('SELECT `can_delete` FROM nl2_forms_permissions WHERE form_id = ? AND `can_delete` = 1 AND group_id IN (' . $group_ids . ')', array($form_id))->count() ? true : false;
    }

    /**
     * @return Language The current language instance for translations
     */
    public static function getLanguage(): Language {
        if (!isset(self::$_forms_language)) {
            self::$_forms_language = new Language(ROOT_PATH . '/modules/Forms/language');
        }

        return self::$_forms_language;
    }

    /**
     * Register a submission source to the sources list.
     *
     * @param SubmissionBase $submission Instance of SubmissionSource to register.
     */
    public function registerSubmissionSource(SubmissionBase $source): void {
        $this->_submission_sources[$source->getName()] = $source;
    }

    /**
     * List all submission sources.
     *
     * @return SubmissionBase[] List of submission sources.
     */
    public function getSubmissionSources(): array {
        return $this->_submission_sources;
    }

    /**
     * Get submission source by name.
     *
     * @param string $name Name of submission source to get.
     *
     * @return SubmissionBase|null Instance of submission source with same name, null if it doesn't exist.
     */
    public function getSubmissionSource(string $name): ?SubmissionBase {
        foreach ($this->_submission_sources as $source) {
            if (strcasecmp($name, $source->getName()) == 0) {
                return $source;
            }
        }

        return null;
    }

    /**
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    public static function updateCheck() {
        $current_version = Util::getSetting('nameless_version');
        $uid = Util::getSetting('unique_id');

        $enabled_modules = Module::getModules();
        foreach ($enabled_modules as $enabled_item) {
            if ($enabled_item->getName() == 'Forms') {
                $module = $enabled_item;
                break;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=Forms&module_version='.$module->getVersion() . '&domain='. URL::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($update_check);
        if (isset($info->message)) {
            die($info->message);
        }

        return $update_check;
    }
}