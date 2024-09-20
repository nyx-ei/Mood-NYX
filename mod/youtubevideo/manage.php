<?php

/**
 * Manage Youtube Video
 *
 * @package    mod_youtubevideo
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/youtubevideo/lib.php');
require_once($CFG->libdir . '/formslib.php');

$id = optional_param('id', 0, PARAM_INT); // Course ID

if (!$id) {
    print_error('missingcourseid', 'youtubevideo');
}

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);
require_capability('mod/youtubevideo:manage', $context);

$PAGE->set_url('/mod/youtubevideo/manage.php', array('id' => $course->id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('incourse');

class mod_youtubevideo_manage_form extends moodleform
{
    protected function definition()
    {
        global $DB, $COURSE;

        $mform = $this->_form;
        $videos = $DB->get_records('youtubevideo', array('course' => $COURSE->id));

        $mform->addElement('header', 'videoslist', get_string('videoslist', 'youtubevideo'));

        if (empty($videos)) {
            $mform->addElement('static', 'novideos', '', get_string('novideos', 'youtubevideo'));
        } else {
            foreach ($videos as $video) {
                $mform->addElement('text', 'video_' . $video->id, $video->name, array('size' => '60'));
                $mform->setType('video_' . $video->id, PARAM_URL);
                $mform->setDefault('video_' . $video->id, $video->youtubeurl);
                $mform->addElement('checkbox', 'delete_' . $video->id, get_string('delete'));
            }
        }

        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

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

/**
 * Process changes to YouTube videos
 *
 * @param object $data Form data
 * @param int $courseid Course ID
 */
function youtubevideo_process_changes($data, $courseid)
{
    global $DB;

    foreach ($data as $key => $value) {
        if (strpos($key, 'video_') === 0) {
            $id = substr($key, 6);
            $video = $DB->get_record('youtubevideo', array('id' => $id, 'course' => $courseid));

            if ($video) {
                if ($video->youtubeurl !== $value) {
                    // URL has been modified
                    $DB->update_record('youtubevideo', (object) array(
                        'id' => $id,
                        'youtubeurl' => $value,
                        'timemodified' => time()
                    ));
                }
            }
        } elseif (strpos($key, 'delete_') === 0 && $value == 1) {
            $id = substr($key, 7);
            $DB->delete_records('youtubevideo', array('id' => $id, 'course' => $courseid));
        }
    }
}

$mform = new mod_youtubevideo_manage_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
} else if ($fromform = $mform->get_data()) {
    youtubevideo_process_changes($fromform, $course->id);
    redirect(
        new moodle_url('/mod/youtubevideo/manage.php', array('id' => $course->id)),
        get_string('changessaved', 'youtubevideo'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageyoutubevideos', 'youtubevideo'));
$mform->display();
echo $OUTPUT->footer();
