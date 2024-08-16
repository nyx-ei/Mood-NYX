<?php

/**
 * @package   auth_nyxei
 * @author    NYX-EI <help@nyx-ei.tech>
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 */

declare(strict_types=1);

namespace auth_nyxei;

use auth_nyxei\NotificationMessages;

defined('MOODLE_INTERNAL') || die();

class LdapUserManager
{
    private $ldapConfig;
    private $ldapConnectionManager;

    public function __construct(LdapConfig $ldapConfig, LdapConnectionManager $ldapConnectionManager)
    {
        $this->ldapConfig = $ldapConfig;
        $this->ldapConnectionManager = $ldapConnectionManager;
    }

    public function user_login($username, $password)
    {
        $ldap_connection = $this->ldapConnectionManager->ldap_connection();

        $errorMessage = NotificationMessages::getMessage('ldap_connection_failed');

        if (!$ldap_connection) {
            $this->failed_login_log($username, $errorMessage);
            return false;
        }

        $ldap_bind = $this->ldapConnectionManager->bind($ldap_connection, $username, $password);

        $invalidCredentials = NotificationMessages::getMessage('invalid_credentials');

        if ($ldap_bind) {
            $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
            return true;
        } else {
            $this->failed_login_log($username, $invalidCredentials);
            $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
            return false;
        }
    }

    public function sync_users()
    {
        global $DB, $CFG;

        $ldap_connection = $this->ldapConnectionManager->ldap_connection();

        $error_message = NotificationMessages::getMessage('ldap_connection_failed');

        if (!$ldap_connection) {
            error_log($error_message);
            return false;
        }

        $ldap_bind = $this->ldapConnectionManager->bind($ldap_connection);

        if (!$ldap_bind) {
            $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
            return false;
        }

        $base_search = "{$this->ldapConfig->dc_base},{$this->ldapConfig->dc_domain}";
        $search = ldap_search($ldap_connection, $base_search, "(objectClass=*)");
        $entries = ldap_get_entries($ldap_connection, $search);

        if ($entries === false) {
            $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
            return false;
        }

        $ad_usernames = [];
        foreach ($entries as $entry) {
            if (!empty($entry['samaccountname'][0])) {
                $username = $entry['samaccountname'][0];
                $ad_usernames[] = $username;

                if (!$DB->record_exists('user', ['username' => $username])) {
                    $this->create_user($entry);
                }

                $this->update_user_status($entry);
            }
        }

        $this->suspend_non_existent_users($ad_usernames);

        $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
        return true;
    }

    private function failed_login_log($username, $error)
    {
        global $DB;

        $record = new \stdClass();
        $record->username = $username;
        $record->timestamp = time();
        $record->error = $error;

        $DB->insert_record('auth_nyxei_failed_logins', $record);

        $this->check_failed_attempts($username);
    }

    private function check_failed_attempts($username)
    {
        global $DB;

        $username = $this->sanitize_username($username);

        $attempts = $DB->get_records_select('auth_nyxei_failed_logins', 'username = :username', array('username' => $username));
        $attempt_count = count($attempts);

        if ($attempt_count >= $this->ldapConfig->login_attempts) {
            $this->send_admin_notification($username, $attempt_count);
        }
    }

    private function send_admin_notification($username, $attempt_count)
    {

        $admin = get_admin();
        $subject = NotificationMessages::getMessage('alert_message_login_failed');
        $message = NotificationMessages::getMessage('failed_login_attempts_message', [
            $username,
            $attempt_count
        ]);

        email_to_user($admin, $admin, $subject, $message);
    }

    private function sanitize_username($username)
    {
        return trim($username);
    }

    private function create_user($entry)
    {
        global $DB, $CFG;

        $user = new \stdClass();
        $user->username = $entry['samaccountname'][0];
        $user->firstname = $entry['givenname'][0] ?? '';
        $user->lastname = $entry['sn'][0] ?? '';
        $user->email = $entry['mail'][0] ?? '';
        $user->auth = 'auth_nyxei';
        $user->confirmed = 1;
        $user->mnethostid = $CFG->mnet_localhost_id;

        $DB->insert_record('user', $user);
    }

    private function update_user_status($entry)
    {
        global $DB;

        if (isset($entry['useraccountcontrol'][0]) && ($entry['useraccountcontrol'][0] & 2)) {
            $user = $DB->get_record('user', ['username' => $entry['samaccountname'][0]]);
            $user->suspended = 1;
            $DB->update_record('user', $user);
        }
    }

    private function suspend_non_existent_users($ad_usernames)
    {
        global $DB;

        $users = $DB->get_records('user', ['auth' => 'auth_nyxei']);
        foreach ($users as $user) {
            if (!in_array($user->username, $ad_usernames)) {
                $user->suspended = 1;
                $DB->update_record('user', $user);
            }
        }
    }
}
