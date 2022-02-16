<?php

namespace TinyVideoGallery\Widgets;

class Tiny_Video_Gallery extends Widget_Base {

    /**
     * Get widget name.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'tiny-video-gallery';
    }

    /**
     * Get widget title.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return 'Tiny Video Gallery';
    }

    /**
     * Get widget icon.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    /**
     * Get widget categories.
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['basic'];
    }

    /**
     * Register widget controls.
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {

        /**
         *  Here you can add your controls. The controls below are only examples.
         *  Check this: https://developers.elementor.com/elementor-controls/
         *
         **/


        $this->end_controls_section();
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Gallery Settings', 'tiny-video-gallery'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'column_count',
            [
                'label' => __('Column Count', 'tiny-video-gallery'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 4,
                'step' => 1,
                'default' => '3'
            ]
        );
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        tpvg_render($settings['column_count']);
    }
}
