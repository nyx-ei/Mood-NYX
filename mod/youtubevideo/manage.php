<?php

/**
 * Manage Youtube Video
 * 
 * @package youtubevideo
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/youtubevideo/lib.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);
require_capability('mod/youtubevideo:manage', $context);

$PAGE->set_url('/mod/youtubevideo/manage.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('incourse');

class mod_youtubevideo_manage_form extends moodleform
{

    public function definition()
    {
        global $DB, $COURSE;

        $mform = $this->_form;

        $videos = $DB->get_records('youtubevideo', array('course' => $COURSE->id));

        $mform->addElement('header', 'videoslist', get_string('videoslist', 'youtubevideo'));

        foreach ($videos as $video) {
            $mform->addElement('text', 'video_' . $video->id, $video->name, array('size' => '60'));
            $mform->setType('video_' . $video->id, PARAM_URL);
            $mform->addElement('checkbox', 'delete_' . $video->id, get_string('delete'));
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        foreach ($data as $key => $value) {
            if (strpos($key, 'video_') === 0 && !empty($value)) {
                if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $value)) {
                    $errors[$key] = get_string('invalidyoutubeurl', 'youtubevideo');
                }
            }
        }

        return $errors;
    }
}

function youtubevideo_process_changes($data)
{
    global $DB, $COURSE;

    foreach ($data as $key => $value) {
        if (strpos($key, 'video_') === 0) {
            $id = substr($key, 6);
            $DB->set_field('youtubevideo', 'intro', $value, array('id' => $id, 'course' => $COURSE->id));
        } elseif (strpos($key, 'delete_') === 0 && $value == 1) {
            $id = substr($key, 7);
            $DB->delete_records('youtubevideo', array('id' => $id, 'course' => $COURSE->id));
        }
    }
}

$mform = new mod_youtubevideo_manage_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $COURSE->id)));
} else if ($fromform = $mform->get_data()) {
    youtubevideo_process_changes($fromform);
    redirect(
        new moodle_url('/course/view.php', array('id' => $COURSE->id)),
        get_string('changessaved', 'youtubevideo'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageyoutubevideos', 'youtubevideo'));
$mform->display();
echo $OUTPUT->footer();
