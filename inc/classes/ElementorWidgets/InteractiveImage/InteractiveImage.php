<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage;

use J7\PowerElement\Bootstrap;
use J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums\EField;
use J7\PowerElement\ElementorWidgets\Shared\Utils\ControlUtils;
use J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage as Shortcode;

final class InteractiveImage extends \Elementor\Widget_Base {

	/** @var string name */
	private string $name = 'pe_interactive_image';

	/** @return string 取得小工具名稱 */
	public function get_name(): string {
		return $this->name;
	}

	/** @return string 取得小工具標題 */
	public function get_title(): string {
		[, $label] = Shortcode::get_info();
		return $label;
	}

	/** @return string 取得小工具圖示 */
	public function get_icon(): string {
		return 'eicon-image-hotspot';
	}

	/** @return array 取得小工具分類 */
	public function get_categories(): array {
		return [ 'general' ];
	}

	/** @return array 取得小工具關鍵字 */
	public function get_keywords(): array {
		return [ 'image' ];
	}

	/**
	 * @return string 取得自訂說明文件網址
	 * @see https://developers.elementor.com/docs/widgets/widget-promotions/#widget-promotion
	 */
	public function get_custom_help_url(): string {
		return 'https://github.com/j7-dev/wp-power-element';
	}

	public function get_script_depends(): array {
		return [ Bootstrap::get_handle($this->name) ];
	}

	public function get_style_depends(): array {
		return [ Bootstrap::get_handle($this->name) ];
	}

	protected function register_controls(): void {

		$this->start_controls_section(
			'content_section',
			[
				'label' => \esc_html__('Content', 'power_element'),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'background_image',
			[
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'label'       => '背景圖片',
				'media_types' => [ 'image' ],
			]
		);

		// region float_images

		$float_image_repeater = new \Elementor\Repeater();

		$float_image_repeater->add_control(
			'title',
			[
				'label' => '圖片名稱(不顯示)',
				'type'  => \Elementor\Controls_Manager::TEXT,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$float_image_repeater->add_control(
			'image',
			[
				'label'       => '圖片',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => [ 'image' ], // 可選：限制媒體類型
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$float_image_repeater->add_control(
			'hover_image',
			[
				'label'       => 'hover 後替換圖片',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => [ 'image' ], // 可選：限制媒體類型
				'default'     => [],
			]
		);

		$float_image_repeater->add_control(
			'position',
			[
				'label'      => '絕對位置(%)',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ '%' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => '%',
					'isLinked' => false,
				],
			],
		);

		$float_image_repeater->add_control(
			'image_width',
			[
				'label'   => '圖片寬度尺寸(%)',
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'default' => 20,
			],
		);

		$this->add_control(
			'float_images',
			[
				'label'       => '圖片',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      =>$float_image_repeater->get_controls(),
				'default'     => [],
				'title_field' => '{{{title}}}',
			]
		);

		// endregion

		// region items (icons)

		$items_repeater = new \Elementor\Repeater();

		$items_repeater->add_control(
			'title',
			[
				'label' => 'Icon 名稱(不顯示)',
				'type'  => \Elementor\Controls_Manager::TEXT,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$items_repeater->add_control(
			'post_id',
			[
				'label'   => '綁定的文章 id',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai'      => [
					'active' => false,
				],
			]
		);

		$items_repeater->add_control(
			'display_items',
			[
				'label'       => '要顯示的內容',
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => [
					'featured_image'      => '文章縮圖',
					'title'               => '文章標題',
					'excerpt'             => '文章摘要',
					'author_display_name' => '作者名稱',
				],
				'default'     => [ 'featured_image', 'title', 'excerpt', 'author_display_name' ],
			]
		);

		$items_repeater->add_control(
			'meta_key',
			[
				'label' => '額外要顯示的 meta_key',
                'description' => '可以用逗號 , 隔開多個 meta_key',
				'type'  => \Elementor\Controls_Manager::TEXT,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$items_repeater->add_control(
			'icon',
			[
				'type'        => \Elementor\Controls_Manager::ICONS,
				'label'       => 'ICON',
                'description' => '限制 svg',
				'skin'        => 'inline',
				'recommended' => [
					'fa-solid' => [
						'map-marker-alt',
					],
				],
			],
		);

		$items_repeater->add_control(
			'position',
			[
				'label'      => '絕對位置(%)',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ '%' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => '%',
					'isLinked' => false,
				],
			],
		);

		$items_repeater->add_control(
			'icon_width',
			[
				'label'   => 'icon 寬度尺寸(%)',
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'default' => 5,
			],
		);

		$items_repeater->add_control(
			'icon_color',
			[
				'label'   => '顏色',
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#d5758d',
			],
		);

		$this->add_control(
			'items',
			[
				'label'       => '跳動 Icon',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      =>$items_repeater->get_controls(),
				'default'     => [],
				'title_field' => '{{{title}}}',
			]
		);

		// endregion

		$this->add_control(
			'is_unique',
			[
				'label'   => '隱藏列表中，綁定相同的 post_id 項目',
				'type'    => \Elementor\Controls_Manager::SWITCHER ,
				'default' => 'yes',
		// 'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		Styles::register_controls($this);
	}

	/** @return array 顯示促銷資訊 在小工具面板的下方 */
	protected function get_upsale_data(): array {
		return [];
	}

	/**
	 * 渲染小工具內容
	 *
	 * @return void
	 * @throws \Exception 當渲染失敗時拋出例外
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

        echo EField::get_styles($settings);

		\printf('<div class="pe_interactive_image" data-is-unique="%1$s">', $settings['is_unique'] ?? 'yes');
		// region 開始左半邊
		echo '<div class="pe_interactive_image__left">';

		// region 開始 background_image
		echo '<div class="relative">';

		\printf(
			'<img class="pe_interactive_image__bg_image w-full" src="%1$s">',
			$settings['background_image']['url']
		);

		// -- 其他 float 圖片 -- //
		foreach ($settings['float_images'] as $item) :
			$image       = $item['image'];
			$hover_image = $item['hover_image'] ?? [];
			$image_width = $item['image_width'];
			$position    = $item['position'];

			\printf(
			'<img src="%1$s" class="pe_interactive_image__image" style="%2$s %3$s" />
							<img src="%4$s" class="pe_interactive_image__image--hover" style="%2$s %3$s" />
							',
			$image['url'],
			ControlUtils::get_position_styles_from_dimension($position),
			ControlUtils::get_styles_from_image_width($image_width),
				$hover_image['url'] ?? ''
			);

		endforeach;

		// -- 圖標 -- //
		foreach ($settings['items'] as $item) :
			\printf(
				'<div class="pe_interactive_image__icon" style="%s">',
				ControlUtils::get_position_styles_from_dimension($item['position']) . ControlUtils::get_styles_from_image_width($item['icon_width'])
			);
			\Elementor\Icons_Manager::render_icon(
				$item['icon'],
				[
					'aria-hidden' => 'true',
					'class'       => ' ',
					'style'       => "color:{$item['icon_color']};fill:{$item['icon_color']};",
				],
				'svg'
				);

			// region 縮圖卡片
			$bound_post_id = $item['post_id'] ?? null;

			if (\is_numeric($bound_post_id)) {
				$content = self::get_card_content($item, $settings);

				\printf(
					'
                   <a href="%1$s" target="_blank">
                        <div data-card-id="%2$s" data-card-post-id="%3$s" class="pe_interactive_image__card">
                            %4$s
                        </div>
                  </a>
                ',
					\esc_url(\get_permalink($bound_post_id)),
					$item['_id'],
					$item['post_id'],
					$content,
					);
			}

			// endregion

			echo '</div>';
		endforeach;

		echo '</div>';
		// endregion 結束 background_image -- //

		echo '</div>';
		// endregion  結束左半邊

		// region 開始右半邊
		echo '<div class="pe_interactive_image__right">';
		echo '<div class="p-4">';

		$is_unique = ( $settings['is_unique'] ?? 'yes' ) === 'yes';
		$items     = $settings['items'];
		if ( $is_unique) {
			// 移除 items 裡面 item['post_id'] 重複的項目
			$items = \array_filter(
				$items,
				function ( $item ) use ( &$seen ) {
					if (isset($seen[ $item['post_id'] ])) {
						return false;
					}
					$seen[ $item['post_id'] ] = true;
					return true;
				}
				);
		}

		foreach ( $items as $item) :
			$bound_post_id = $item['post_id'] ?? null;
			if (!\is_numeric($bound_post_id)) {
				continue;
			}
			$content = self::get_list_content($item, $settings);
			\printf(
				'
            <a href="%1$s" target="_blank">
                <div data-list-item-id="%2$s" class="pe_interactive_image__list_item">
                    %3$s
                </div>
            </a>
            ',
				\esc_url(\get_permalink($bound_post_id)),
				$is_unique ? $item['post_id'] : $item['_id'],
				$content,
				);

			endforeach;

		echo '</div>';
		echo '</div>';
		// endregion 結束右半邊

		echo '</div>';
	}

	/**
	 * 取得動態內容
	 *
	 * @param array $item
	 * @return string
	 */
	private static function get_card_content( array $item, array $settings ): string {
		$bound_post_id = $item['post_id'] ?? null;
		if (!\is_numeric($bound_post_id)) {
			return '';
		}
		$display_items           = $item['display_items'] ?? [];
		$has_featured_image      = \in_array('featured_image', $display_items, true);
		$has_title               = \in_array('title', $display_items, true);
		$has_excerpt             = \in_array('excerpt', $display_items, true);
		$has_author_display_name = \in_array('author_display_name', $display_items, true);

		$content = '';
		if ($has_featured_image) {
			$content .= sprintf('<img src="%1$s" class="pe_interactive_image__card__image"/>', \esc_url(\get_the_post_thumbnail_url($bound_post_id, 'medium')));
		}

		if ($has_title ) {
			$content .= sprintf( '<h4>%1$s</h4>', \esc_html(\get_the_title($bound_post_id)) );
		}

		if ($has_excerpt ) {
			$content .= sprintf( '<p>%1$s</p>', \esc_html(\get_the_excerpt($bound_post_id)) );
		}

		if ($has_author_display_name ) {
			$post_author_id = \get_post_field('post_author', $bound_post_id);
			$author_name    = \get_the_author_meta('display_name', $post_author_id);
			$content       .= sprintf('<p>%1$s</p>', $author_name);
		}

		$meta_key = $item['meta_key'] ?? '';
		if (\is_string($meta_key) && $meta_key) {
			$meta_keys = self::parse_comma_string($meta_key);
			foreach ($meta_keys as $meta_key) {
				$value    = \get_post_meta($bound_post_id, $meta_key, true);
				$content .= "<p>{$value}</p>";
			}
		}

		return $content;
	}

	public static function parse_comma_string( string $str ): array {
		$arr = \explode(',', $str);
		$arr = \array_map('trim', $arr);
		$arr = \array_filter($arr);
		return $arr;
	}

	/**
	 * 取得動態內容
	 *
	 * @param array $item
	 * @return string
	 */
	private static function get_list_content( array $item, array $settings ): string {
		$bound_post_id = $item['post_id'] ?? null;
		if (!\is_numeric($bound_post_id)) {
			return '';
		}
		$display_items           = $item['display_items'] ?? [];
		$has_title               = \in_array('title', $display_items, true);
		$has_excerpt             = \in_array('excerpt', $display_items, true);
		$has_author_display_name = \in_array('author_display_name', $display_items, true);

		$content = '';

		if ($has_title ) {
			$content .= \sprintf( '<h4>%1$s</h4>', \esc_html(\get_the_title($bound_post_id)) );
		}

		if ($has_excerpt ) {
			$content .= \sprintf( '<p>%1$s</p>', \esc_html(\get_the_excerpt($bound_post_id)) );
		}

		if ($has_author_display_name ) {
			$post_author_id = \get_post_field('post_author', $bound_post_id);
			$author_name    = \get_the_author_meta('display_name', $post_author_id);
			$content       .= sprintf('<p>%1$s</p>', $author_name);
		}

		$meta_key = $item['meta_key'] ?? '';
		if (\is_string($meta_key) && $meta_key) {
			$meta_keys = self::parse_comma_string($meta_key);
			foreach ($meta_keys as $meta_key) {
				$value    = \get_post_meta($bound_post_id, $meta_key, true);
				$content .= "<p>{$value}</p>";
			}
		}

		return $content;
	}
}
