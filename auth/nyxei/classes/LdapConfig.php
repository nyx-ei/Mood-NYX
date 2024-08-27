<?php

/**
 * @package   auth_nyxei
 * @author    NYX-EI <help@nyx-ei.tech>
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 */

declare(strict_types=1);

namespace auth_nyxei;

defined('MOODLE_INTERNAL') || die();

class LdapConfig
{
    const LDAP_PROTOCOL_VERSION = 3;
    const LDAP_PORT = 636;
    const LDAP_REFERRALS = 0;
    const LOGIN_ATTEMPTS = 3;

    public $host;
    public $port;
    public $protocol_version;
    public $referrals;
    public $login_attempts;
    public $bind_user;
    public $bind_password;
    public $dc_base;
    public $dc_domain;
    public $ad_group_role_mappings;

    public function __construct($config)
    {
        $this->host = $config->host ?? '';
        $this->port = self::LDAP_PORT;
        $this->protocol_version = self::LDAP_PROTOCOL_VERSION;
        $this->referrals = self::LDAP_REFERRALS;
        $this->login_attempts = $config->login_attempts ?? self::LOGIN_ATTEMPTS;
        $this->bind_user = $config->bind_user ?? '';
        $this->bind_password = $config->bind_password ?? '';
        $this->dc_base = $config->dc_base ?? '';
        $this->dc_domain = $config->dc_domain ?? '';
        $this->ad_group_role_mappings = $config->ad_group_role_mappings ?? '';
    }

    public function process_config($config)
    {
        set_config('host', $config->host, 'auth_nyxei');
        set_config('login_attempts', $config->login_attempts, 'auth_nyxei');
        set_config('bind_user', $config->bind_user, 'auth_nyxei');
        set_config('bind_password', $config->bind_password, 'auth_nyxei');
        set_config('dc_base', $config->dc_base, 'auth_nyxei');
        set_config('dc_domain', $config->dc_domain, 'auth_nyxei');
        set_config('ad_group_role_mappings', $config->ad_group_role_mappings, 'auth_nyxei');

        return true;
    }
}
