<?php

class FormHook extends HookBase {
    // Check global submissions limit
    public static function globalLimit(array $params = []): array {
        $form = $params['form'];

        $global_limit = json_decode($form->data()->global_limit, true) ?? [];
        if (isset($global_limit['limit']) && $global_limit['limit'] > 0) {
            // Check if period is used
            if (isset($global_limit['period']) && $global_limit['period'] != 'no_period' && isset($global_limit['interval']) && $global_limit['interval'] > 0) {
                $limit = DB::getInstance()->query('SELECT count(*) as c FROM nl2_forms_replies WHERE form_id = ? AND created > ?', [$form->data()->id, strtotime('-'.$global_limit['interval'].' ' . $global_limit['period'])]);
            } else {
                $limit = DB::getInstance()->query('SELECT count(*) as c FROM nl2_forms_replies WHERE form_id = ?', [$form->data()->id]);
            }

            if ($limit->first()->c >= $global_limit['limit']) {
                $params['errors'][] = Forms::getLanguage()->get('forms', 'form_global_limit_reached');
            }
        }

        return $params;
    }

    // Check user submission limit
    public static function userLimit(array $params = []): array {
        $user = $params['user'];

        if ($user->isLoggedIn()) {
            $form = $params['form'];

            $user_limit = json_decode($form->data()->user_limit, true) ?? [];
            if (isset($user_limit['limit']) && $user_limit['limit'] > 0) {
                // Check if period is used
                if (isset($user_limit['period']) && $user_limit['period'] != 'no_period' && isset($user_limit['interval']) && $user_limit['interval'] > 0) {
                    $limit = DB::getInstance()->query('SELECT count(*) as c FROM nl2_forms_replies WHERE form_id = ? AND user_id = ? AND created > ?', [$form->data()->id, $user->data()->id, strtotime('-'.$user_limit['interval'].' ' . $user_limit['period'])]);
                } else {
                    $limit = DB::getInstance()->query('SELECT count(*) as c FROM nl2_forms_replies WHERE form_id = ? AND user_id = ?', [$form->data()->id, $user->data()->id]);
                }

                if ($limit->first()->c >= $user_limit['limit']) {
                    $params['errors'][] = Forms::getLanguage()->get('forms', 'form_user_limit_reached');
                }
            }
        }

        return $params;
    }

    // Check for any required integrations
    public static function requiredIntegrations(array $params = []): array {
        $user = $params['user'];

        if ($user->isLoggedIn()) {
            $form = $params['form'];

            $required_integrations_list = [];
            $integrations = Integrations::getInstance();
            $enabled_integrations = $integrations->getEnabledIntegrations();
            $required_integrations = json_decode($form->data()->required_integrations, true) ?? [];
            foreach ($required_integrations as $item) {
                foreach ($enabled_integrations as $integration) {
                    if ($integration->data()->id == $item) {
                        $required_integrations_list[$integration->data()->id] = $integration;
                    }
                }
            }

            foreach ($required_integrations_list as $integration) {
                $integrationUser = $user->getIntegration($integration->getName());
                if ($integrationUser == null || $integrationUser->data()->username == null || $integrationUser->data()->identifier == null) {
                    $params['errors'][] = Forms::getLanguage()->get('forms', 'form_requires_integration', [
                        'integration' => Output::getClean($integration->getName()),
                        'linkStart' => '<a href="' . URL::build('/user/connections') . '">',
                        'linkEnd' => '</a>'
                    ]);
                }
            }
        }

        return $params;
    }
}