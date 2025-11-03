<?php

declare(strict_types=1);

namespace J7\PowerElement\Shortcodes\InteractiveImage;

use J7\PowerElement\Shortcodes\Shared\Interfaces\IShortcode;

final class InteractiveImage implements IShortcode {

	private const SHORTCODE = 'pe_interactive_image';

	/**
	 * 短碼 callback
	 *
	 * @param array $atts 短碼參數
	 * @return string
	 */
	public static function callback(array $atts = []): string {
        $bg_id = 135;
        $bg_url = \wp_get_attachment_image_url($bg_id, 'full');
		return '123456489498156286484';
	}

	/**
	 * 取得短碼資訊
	 *
	 * @return array{0:string, 1:string} 短碼名稱陣列 [$shortcode_name, $label]
	 */
	public static function get_info(): array {
		return [ self::SHORTCODE, "互動圖片" ];
	}
}
