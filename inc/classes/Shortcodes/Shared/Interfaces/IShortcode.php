<?php

declare(strict_types=1);

namespace J7\PowerElement\Shortcodes\Shared\Interfaces;

interface IShortcode {

	/**
	 * 短碼 callback
	 *
	 * @param array $atts 短碼參數
	 * @return string
	 */
	public static function callback( array $atts = [] ): string;

	/**
	 * 取得短碼資訊
	 *
	 * @return array{0:string, 1:string} 短碼名稱陣列 [$shortcode_name, $label]
	 */
	public static function get_info(): array;
}
