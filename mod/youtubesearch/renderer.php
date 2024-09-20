<?php

/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

class mod_youtubesearch_renderer extends plugin_renderer_base {
    public function render_search_form(mod_youtubesearch\output\search_form $form) {
        return $this->render_from_template('mod_youtubesearch/search_form', $form->export_for_template($this));
    }

    public function render_search_results($results) {
        $data = [
            'videos' => $results,
            'course_id' => $this->page->course->id
        ];
        return $this->render_from_template('mod_youtubesearch/search_results', $data);
    }
}