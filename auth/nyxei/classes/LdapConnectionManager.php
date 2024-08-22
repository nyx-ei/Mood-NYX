<?php

/**
 * @package   auth_nyxei
 * @author    NYX-EI <help@nyx-ei.tech>
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 */

declare(strict_types=1);

namespace auth_nyxei;


defined('MOODLE_INTERNAL') || die();

class LdapConnectionManager
{
    private $ldapConfig;

    public function __construct(LdapConfig $ldapConfig)
    {
        $this->ldapConfig =  $ldapConfig;        
    }

    public function ldap_connection()
    {
        $ldap_url = "ldaps://{$this->ldapConfig->host}:{$this->ldapConfig->port}";

        $ldap_connection = ldap_connect($ldap_url);

        $error_message = NotificationMessages::getMessage('ldap_connection_failed');

        if (!$ldap_connection) {
            error_log($error_message);
            return false;
        }

        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, $this->ldapConfig->protocol_version);
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, $this->ldapConfig->referrals);

        return $ldap_connection;
    }

    public function bind($ldap_connection, $username = null, $password = null)
    {
        $bind_user = $username ?? $this->ldapConfig->bind_user;
        $bind_password = $password ?? $this->ldapConfig->bind_password;

        return ldap_bind($ldap_connection, $bind_user, $bind_password);
    }

    public function closeLdapConnection($ldap_connection)
    {
        ldap_unbind($ldap_connection);
    }
}