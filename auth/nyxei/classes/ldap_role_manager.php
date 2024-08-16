<?php

/**
 * @package   auth_nyxei
 * @author    NYX-EI <help@nyx-ei.tech>
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 */

declare(strict_types=1);

namespace auth_nyxei;

defined('MOODLE_INTERNAL') || die();

class LdapRoleManager
{
    private $ldapConfig;
    private $ldapConnectionManager;

    public function __construct(LdapConfig $ldapConfig, LdapConnectionManager $ldapConnectionManager)
    {
        $this->ldapConnectionManager = $ldapConnectionManager;
        $this->ldapConfig = $ldapConfig;
    }

    public function sync_ad_groups_to_roles()
    {
        global $DB;
        
        $mappings = explode("\n", $this->ldapConfig->ad_group_role_mappings);
        $mappings = array_filter(array_map('trim', $mappings));

        $ldap_connection = $this->ldapConnectionManager->ldap_connection();
        if (!$this->ldapConnectionManager->bind($ldap_connection)) {
            throw new \Exception('LDAP bind error');
        }

        foreach ($mappings as $mapping) {
            list($ad_group, $moodle_role) = explode(':', $mapping);

            $base_dn = "{$this->ldapConfig->dc_base},{$this->ldapConfig->dc_domain}";
            $search_filter = "(memberOf=cn=$ad_group,{$this->ldapConfig->dc_domain})";

            $search = ldap_search($ldap_connection, $base_dn, $search_filter);
            $entries = ldap_get_entries($ldap_connection, $search);

            if ($entries['count'] > 0) {
                $this->assign_role_to_users($entries, $moodle_role);
            }
        }

        $this->ldapConnectionManager->closeLdapConnection($ldap_connection);
        return true;
    }

    private function assign_role_to_users($entries, $moodle_role)
    {
        global $DB;

        $roleid = $DB->get_field('role', 'id', ['shortname' => $moodle_role]);

        if ($roleid) {
            foreach ($entries as $entry) {
                if (isset($entry['samaccountname'][0])) {
                    $username = $entry['samaccountname'][0];
                    $user = $DB->get_record('user', ['username' => $username]);

                    if ($user) {
                        role_assign($roleid, $user->id, context_system::instance());
                    }
                }
            }
        } else {
            error_log("The Moodle role '$moodle_role' does not exist.");
        }
    }
}