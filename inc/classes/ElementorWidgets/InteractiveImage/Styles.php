<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage;

use J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums\EField;

class Styles {


	/**
	 * @param InteractiveImage $widget widget
	 * @return void
	 */
	public static function register_controls( InteractiveImage $widget ): void {

		$widget->start_controls_section(
			'card_style',
			[
				'label' => '文字樣式',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);




		foreach ( EField::cases() as $field ) {

            $widget->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => $field->value,
                    'label'     => $field->label(),
                    'selector' => $field->selectors(),
                ]
            );
		}

		$widget->end_controls_section();
	}
}
