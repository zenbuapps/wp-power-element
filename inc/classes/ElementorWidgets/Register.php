<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets;

use Elementor\Widgets_Manager;
use J7\PowerElement\ElementorWidgets\InteractiveImage\InteractiveImage;

/** 註冊類 */
final class Register {

	/** 註冊鉤子 */
	public static function register_hooks(): void {
		\add_action( 'elementor/widgets/register', [ __CLASS__, 'register_widgets' ] );
	}

	/**
	 * 註冊 Elementor Widget
	 *
	 * @param Widgets_Manager $widgets_manager Widget Manager
	 * @return void
	 */
	public static function register_widgets( Widgets_Manager $widgets_manager ): void {
		$widget_classes = [
			InteractiveImage::class,
		];

		foreach ($widget_classes as $widget_class) {
			$widgets_manager->register( new $widget_class() );
		}
	}
}
