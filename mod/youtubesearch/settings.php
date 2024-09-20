<?php

/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'mod_youtubesearch/api_key',
        get_string('api_key', 'mod_youtubesearch'),
        get_string('api_key_desc', 'mod_youtubesearch'),
        '',
        PARAM_TEXT
    ));
}