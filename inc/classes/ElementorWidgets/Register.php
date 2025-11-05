<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets;

use Elementor\Widgets_Manager;
use J7\PowerElement\ElementorWidgets\InteractiveImage\InteractiveImage;
use J7\PowerElement\ElementorWidgets\Shared\DynamicTags\AuthorName;
use J7\PowerElement\ElementorWidgets\Shared\DynamicTags\PostExcerpt;
use J7\PowerElement\ElementorWidgets\Shared\DynamicTags\PostTitle;
use J7\PowerElement\ElementorWidgets\Shared\DynamicTags\PostUrl;

/** 註冊類 */
final class Register {

	/** 註冊鉤子 */
	public static function register_hooks(): void {
		\add_action( 'elementor/widgets/register', [ __CLASS__, 'register_widgets' ] );
		\add_action( 'elementor/dynamic_tags/register', [ __CLASS__, 'register_dynamic_tags' ], 200 );
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

	public static function register_dynamic_tags( $dynamic_tags_manager ): void {
		if (\class_exists('\ElementorPro\Plugin')) {
            $dynamic_tags_manager->register_group(
                'power-element',
                [
                    'title' => "Power Element",
                ]
            );


			$dynamic_tags_manager->register( new PostTitle() );
			$dynamic_tags_manager->register( new PostExcerpt() );
			$dynamic_tags_manager->register( new PostUrl() );
			$dynamic_tags_manager->register( new AuthorName() );
		}
	}
}
