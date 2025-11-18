<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage;

use J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums\EField;
use J7\PowerElement\ElementorWidgets\Shared\Utils\ControlUtils;

class Styles {


	/**
	 * @param InteractiveImage $widget widget
	 * @return void
	 */
	public static function register_controls( InteractiveImage $widget ): void {

		self::layout_controls_section( $widget );
		self::card_controls_section( $widget );
		self::icon_controls_section( $widget );
		self::list_controls_section( $widget );
	}

    /**
     * @param InteractiveImage $widget widget
     * @return void
     */
    public static function layout_controls_section( InteractiveImage $widget ): void {

        $widget->start_controls_section(
            'layout_style',
            [
                'label' => 'Layout 樣式',
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $widget->add_responsive_control(
            'bg_image_width',
            [
                'type' => \Elementor\Controls_Manager::SLIDER,
                'label' => '背景圖片寬度',
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pe_interactive_image__bg_image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_responsive_control(
            'segment_gap',
            [
                'type' => \Elementor\Controls_Manager::SLIDER,
                'label' => '圖片與列表的間距',
                'size_units' => [ 'px', 'rem' ],
                'range' => [
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 160,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'rem',
                ],
                'tablet_default' => [
                    'size' => 0,
                    'unit' => 'rem',
                ],
                'mobile_default' => [
                    'size' => 0,
                    'unit' => 'rem',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pe_interactive_image' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]);

        $widget->end_controls_section();
    }


    /**
	 * @param InteractiveImage $widget widget
	 * @return void
	 */
	public static function card_controls_section( InteractiveImage $widget ): void {

		$widget->start_controls_section(
			'card_style',
			[
				'label' => '卡片樣式',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		foreach ( EField::cases() as $field ) {
			if ($field->is_list()) {
				continue;
			}
            $field->add_control($widget);
		}

		$widget->end_controls_section();
	}

	/**
	 * @param InteractiveImage $widget widget
	 * @return void
	 */
	public static function icon_controls_section( InteractiveImage $widget ): void {

		$widget->start_controls_section(
			'icon_style',
			[
				'label' => 'Icon 樣式',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$widget->add_control(
			'icon_opacity',
			[
				'label'     => 'Icon 預設透明度 (hover 時會變不透明)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 75,
				],
				'selectors' => [
					'{{WRAPPER}} .pe_interactive_image__icon svg' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * @param InteractiveImage $widget widget
	 * @return void
	 */
	public static function list_controls_section( InteractiveImage $widget ): void {

		$widget->start_controls_section(
			'list_style',
			[
				'label' => '列表樣式',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$widget->add_responsive_control(
			'list_item_padding',
			[
				'label'      => \esc_html__( 'Padding', 'power_element' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'rem', 'px', '%' ],
				'default'    => [
					'top'      => 1,
					'right'    => 1,
					'bottom'   => 1,
					'left'     => 1,
					'unit'     => 'rem',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .pe_interactive_image__list_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'list_item_margin',
			[
				'label'      => \esc_html__( 'Margin', 'power_element' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0.25,
					'left'     => 0,
					'unit'     => 'rem',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .pe_interactive_image__list_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		foreach ( EField::cases() as $field ) {
			if (!$field->is_list()) {
				continue;
			}

            $field->add_control($widget);
		}

		$widget->end_controls_section();
	}
}
