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
}
