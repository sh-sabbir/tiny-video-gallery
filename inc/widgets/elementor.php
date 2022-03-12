<?php

namespace TinyVideoGallery\Widgets;

use Elementor\Widget_Base;

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
        $this->end_controls_section();

        $this->start_controls_section(
            'content_style',
            [
                'label' => __('Gallery Style', 'tiny-video-gallery'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'tvg_item_spacing',
			[
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( 'Item Spacing', 'plugin-name' ),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .tpvg_gallery-wrap .tpvg_gallery-items .tpvg_gallery-item' => 'padding: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'tvg_item_border',
                'label' => esc_html__( 'Item Border', 'tiny-video-gallery' ),
				'selector' => '{{WRAPPER}} .tpvg_gallery-wrap .tpvg_gallery-items .tpvg_gallery-item .inner'
                
			]
		);

        $this->add_responsive_control(
			'tvg_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'eazygrid-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tpvg_gallery-wrap .tpvg_gallery-items .tpvg_gallery-item .inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'caption_text_align',
			[
                'separator' => 'before',
				'label'                => __( 'Alignment', 'eazygrid-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'eazygrid-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'eazygrid-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'eazygrid-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'               => false,
				'selectors'            => [
					'{{WRAPPER}} .tpvg_gallery-wrap .tpvg_gallery-items .tpvg_gallery-item .inner .tpvg-caption' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'    => '
						text-align: left;',
					'center'  => '
						text-align: center;',
					'right'   => '
						text-align: right;',
					'justify' => 'text-align: justify;',
				],
			]
		);

        $this->add_control(
			'caption_bg_color',
			[
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( 'Caption Background Color', 'tiny-video-gallery' ),
				'default' => '#000000',
                'selectors' => [
					'{{WRAPPER}} .tpvg_gallery-item .tpvg-caption' => 'background: {{VALUE}}',
				],
                'separator' => 'before'
			]
		);

        $this->add_control(
			'caption_text_color',
			[
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( 'Caption Text Color', 'tiny-video-gallery' ),
				'default' => '#ffffff',
                'selectors' => [
					'{{WRAPPER}} .tpvg_gallery-item .tpvg-caption' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
                'label' => __('Caption Typograghy', 'tiny-video-gallery'),
				'name' => 'caption_typography',
				'selector' => '{{WRAPPER}} .tpvg_gallery-item .tpvg-caption',
			]
		);

        $this->add_responsive_control(
			'caption_padding',
			[
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'label' => esc_html__( 'Padding', 'plugin-name' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tpvg_gallery-item .tpvg-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
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

        echo tpvg_render($settings['column_count']);
    }
}
