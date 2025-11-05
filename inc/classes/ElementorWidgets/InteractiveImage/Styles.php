<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage;

use J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums\EField;

class Styles {


    /**
     * @param InteractiveImage $widget widget
     * @return void
     */
	public static function register_controls( InteractiveImage $widget ):void {

		$widget->start_controls_section(
			'card_style',
			[
				'label' => '卡片樣式',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		foreach ( EField::cases() as $field ) {
			$widget->add_control($field->value, $field->get_control_args());
		}

		$widget->end_controls_section();
	}
}
