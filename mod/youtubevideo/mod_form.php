<?php

/**
 * @package youtubevideo
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_youtubevideo_mod_form extends moodleform_mod
{

    function definition()
    {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $mform->addElement('text', 'youtubeurl', get_string('youtubeurl', 'youtubevideo'), array('size' => '64'));
        $mform->setType('youtubeurl', PARAM_URL);
        $mform->addRule('youtubeurl', null, 'required', null, 'client');
        $mform->addHelpButton('youtubeurl', 'youtubeurl', 'youtubevideo');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    function validated_url($data, $files)
    {
        $errors = parent::validation($data, $files);
        if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $data['youtubeurl'])) {
            $errors['youtubeurl'] = get_string('invalidyoutubeurl', 'youtubevideo');
        }
        return $errors;
    }
}
