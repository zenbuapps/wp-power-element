<?php

declare (strict_types = 1);

namespace J7\PowerElement;

/** Class Bootstrap */
final class Bootstrap {
	use \J7\WpUtils\Traits\SingletonTrait;

	/** @var array<array<string>> 元件名稱 => 依賴  */
	private static array $components = [
		'ButtonA' => [ 'react', 'react-dom', 'react-jsx-runtime', 'wp-element' ],
		'ButtonB' => [ 'react', 'react-dom', 'react-jsx-runtime', 'wp-element' ],
		'pe_interactive_image' => [ 'react', 'react-dom', 'react-jsx-runtime', 'wp-element' ],
	];

	/** Constructor */
	public function __construct() {
		Shortcodes\Register::register_hooks();
		ElementorWidgets\Register::register_hooks();

		\add_action('wp_enqueue_scripts', [ __CLASS__, 'frontend_register_script' ], 200);
    }

	/**
	 * Enqueue script
	 * You can load the script on demand
	 *
	 * @return void
	 */
	public static function frontend_register_script(): void {
		foreach (self::$components as $name => $deps) {
			\wp_register_script(
				self::get_handle($name),
				Plugin::$url . "/js/dist/components/{$name}/index.js",
				$deps,
				Plugin::$version,
				[
					'in_footer' => true,
					'strategy'  => 'async',
				]
			);

			Plugin::instance()->add_module_handle(self::get_handle($name));

			\wp_register_style(
				self::get_handle($name),
				Plugin::$url . "/js/dist/components/{$name}/index.css",
				[],
				Plugin::$version
			);
		}
	}

	/**
	 * 取得 handle
	 *
	 * @param string $name component name
	 * @return string
	 */
	public static function get_handle( string $name ): string {
		return Plugin::$kebab . "-{$name}";
	}

	/**
	 * 載入元件資源
	 *
	 * @param string $name component name
	 * @return void
	 * @throws \Exception 如果不是元件
	 */
	public function enqueue( string $name ): void {
		if (!self::is_component($name)) {
			throw new \Exception("{$name} is not a component");
		}
		\wp_enqueue_script(self::get_handle($name));
		\wp_enqueue_style(self::get_handle($name));
	}

	/**
	 * 是否為 components
	 *
	 * @param string $name component name
	 * @return bool
	 */
	public static function is_component( string $name ): bool {
		return isset(self::$components[ $name ]);
	}
}
