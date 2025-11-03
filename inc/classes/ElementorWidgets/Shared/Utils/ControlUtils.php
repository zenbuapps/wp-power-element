<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\Utils;

abstract class ControlUtils {

	public static function get_url_from_media( array $image ) {
	}

	/**
	 * @param array{
	 *     unit: string,
	 *     top: string|int,
	 *     right: string|int,
	 *     bottom: string|int,
	 *     left: string|int,
	 *     isLinked: bool
	 * } $dimension 尺寸控制
	 * @return string style string
	 */
	public static function get_position_styles_from_dimension( array $dimension ): string {
		$styles = '';
		$unit   = $dimension['unit'] ?? '%';
		foreach ($dimension as $key => $value) {
			if (\is_numeric($value)) {
				if (!$value && \in_array($key, [ 'right', 'bottom' ], true) ) {
					continue;
				}
				$styles .= "{$key}:{$value}{$unit};";
			}
		}

		return $styles;
	}


	/**
	 * @param string|int|float $image_width 尺寸控制
	 * @param bool       $center 是否置中
	 * @return string style string
	 */
	public static function get_styles_from_image_width( string|int|float $image_width, bool $center = true ): string {
		$styles = "width:{$image_width}%;";
		if ($center) {
			$styles .= 'transform: translate(-50%, -50%);';
		}
		return $styles;
	}
}
