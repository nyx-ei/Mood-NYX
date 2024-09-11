<?php

/**
 * @package youtubevideo
 * 
 * @copyright 2024 NYX-EI {@link http://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

// use core\output\plugin_renderer_base;

defined('MOODLE_INTERNAL') || die();

class mod_youtubevideo_renderer extends plugin_renderer_base
{

    /**
     * Render page header
     *
     * @return string HTML
     */
    public function header()
    {
        return $this->output->header();
    }

    /**
     * Render page title
     *
     * @param string $title
     * @return string HTML
     */
    public function heading($title)
    {
        return $this->output->heading($title);
    }

    /**
     * Return the module introduction box
     *
     * @param string $intro
     * @return string HTML
     */
    public function module_intro($intro)
    {
        if (!empty($intro)) {
            return html_writer::tag('div', $intro, array('class' => 'generalbox mod_introbox', 'id' => 'youtubevideorintro'));
        }
        return '';
    }

    /**
     * Render YouTube video player
     *
     * @param string $youtube_id
     * @return string HTML
     */
    public function youtube_video($youtube_id)
    {
        $video_container = html_writer::start_div('youtubevideo-container');
        $video_container .= html_writer::empty_tag('iframe', array(
            'width' => '560',
            'height' => '315',
            'src' => "https://www.youtube.com/embed/$youtube_id",
            'frameborder' => '0',
            'allow' => 'autoplay; encrypted-media',
            'allowfullscreen' => 'allowfullscreen'
        ));
        $video_container .= html_writer::end_div();

        return $video_container;
    }

    /**
     * Render the footer
     *
     * @return string HTML
     */
    public function footer()
    {
        return $this->output->footer();
    }

    /**
     * Render The Manage Link
     * 
     * @param int $courseid
     * @param context $context
     * @return string HTML
     */
    public function manage_link($courseid, $context)
    {
        if (!has_capability('mod/youtubevideo:manage', $context)) {
            return '';
        }
    
        $buttonAttributes = [
            'class' => 'btn btn-primary youtubevideo-manage-btn',
            'role' => 'button'
        ];
    
        $buttonContent = $this->get_icon('fa-cog') . ' ' . $this->get_string('manageyoutubevideos');
        $manageUrl = $this->get_manage_url($courseid);
    
        $button = $this->create_button($manageUrl, $buttonContent, $buttonAttributes);
    
        return $this->wrap_in_div($button, 'youtubevideo-manage-button');
    }

    private function get_icon($iconClass)
    {
        $icon = html_writer::tag('i', '', ['class' => "fa $iconClass", 'aria-hidden' => 'true']);

        return $icon;
    }

    private function get_string($identified)
    {
        return get_string($identified, 'youtubevideo');
    }

    private function get_manage_url($courseid)
    {
        $manageUrl = new moodle_url('/mod/youtubevideo/manage.php', ['id' => $courseid]);

        return $manageUrl;
    }

    private function create_button($url, $content, $attributes)
    {
        $button = html_writer::link($url, $content, $attributes);

        return $button;
    }

    private function wrap_in_div($content, $class)
    {
        return html_writer::div($content, $class);
    }
}
