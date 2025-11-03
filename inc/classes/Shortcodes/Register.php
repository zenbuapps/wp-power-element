<?php

declare(strict_types=1);

namespace J7\PowerElement\Shortcodes;

use J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage;

/** 註冊類 */
final class Register {

	/** 註冊鉤子 */
	public static function register_hooks(): void {
		$shortcode_classes = [
			InteractiveImage::class,
		];
		foreach ($shortcode_classes as $shortcode_class) {
			[$shortcode_name] = $shortcode_class::get_info();
			\add_shortcode($shortcode_name, [ $shortcode_class, 'callback' ]);
		}
	}
}
