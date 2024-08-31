<?php

use auth_nyxei\LdapConfig;
use auth_nyxei\LdapRoleManager;
use auth_nyxei\LdapUserManager;
use auth_nyxei\LdapConnectionManager;


/**
 * Active directory Authentification plugin
 * Authentification using LDAPS (Lightweight Directory Access Protocol)
 * This plugin uses the ldaps protocol for secure connection
 * Please make sure you have configured ldaps connections on your Active Directory server
 * 
 * @package   auth_nyxei
 * @author    NYX-EI <help@nyx-ei.tech>
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/setuplib.php');
require_once($CFG->libdir . '/moodlelib.php');

class auth_plugin_nyxei extends auth_plugin_base
{
    private $ldapConnectionManager;
    private $ldapUserManager;
    private $ldapRoleManager;
    private $ldapConfig;

    public function __construct()
    {
        $this->authtype = 'nyxei';
        $this->config = get_config('auth_nyxei');
        $this->ldapConfig = new LdapConfig($this->config);
        $this->ldapConnectionManager = new LdapConnectionManager($this->ldapConfig);
        $this->ldapUserManager = new LdapUserManager($this->ldapConfig, $this->ldapConnectionManager);
        $this->ldapRoleManager = new LdapRoleManager($this->ldapConfig, $this->ldapConnectionManager);
    }

    public function user_login($username, $password)
    {
        return $this->ldapUserManager->user_login($username, $password);
    }

    public function is_internal()
    {
        return false;
    }

    public function can_change_password()
    {
        return false;
    }

    public function config_form($config, $err, $user_fields)
    {
        include 'settings.php';
    }

    public function process_config($config)
    {
        return $this->ldapConfig->process_config($config);
    }

    public function sync_users()
    {
        return $this->ldapUserManager->sync_users();
    }

    public function sync_ad_groups_to_roles()
    {
        return $this->ldapRoleManager->sync_ad_groups_to_roles();
    }
}
