<?php

namespace mod_youtubesearch\output;

/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

class search_form implements \renderable, \templatable {
    public function export_for_template(\renderer_base $output) {
        return [
            'action' => new \moodle_url('/mod/youtubesearch/view.php'),
        ];
    }
}