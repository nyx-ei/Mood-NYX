<?php

/**
 * @package    youtubesearch
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @author     NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // Vérifiez les permissions
    $settings = new admin_settingpage('local_youtubesearch', get_string('pluginname', 'local_youtubesearch'));

    // Ajoutez la page de paramètres à la catégorie des plugins locaux
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_youtubesearch/api_key',
        get_string('api_key', 'local_youtubesearch'),
        get_string('api_key_desc', 'local_youtubesearch'),
        '',
        PARAM_TEXT
    ));
}
