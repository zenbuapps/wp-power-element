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
				'label' => \esc_html__('Layout 設定', 'power_element'),
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

		$this->add_control(
			'background_image_width',
			[
				'label'     => '圖片寬度，剩餘部分會分配給列表',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 67,
				],
				'selectors' => [
					'{{WRAPPER}} .pe_interactive_image__left' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// region float_images 圖片設定

		$this->start_controls_section(
			'float_images_section',
			[
				'label' => \esc_html__('圖片設定', 'power_element'),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$float_image_repeater = new \Elementor\Repeater();

		$float_image_repeater->add_control(
			'title',
			[
				'label' => '名稱(不顯示)',
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
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__image' => 'background-image: url({{URL}});',
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
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__image:hover' => 'background-image: url({{URL}});',
				],
			]
		);

		$float_image_repeater->add_control(
			'position_top',
			[
				'label'     => '絕對位置(上)(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__image' => 'top: {{SIZE}}{{UNIT}};',
				],
			],
		);

		$float_image_repeater->add_control(
			'position_left',
			[
				'label'     => '絕對位置(左)(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__image' => 'left: {{SIZE}}{{UNIT}};',
				],
			],
		);

		$float_image_repeater->add_control(
			'image_width',
			[
				'label'     => '圖片寬度(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__image' => 'width: {{SIZE}}{{UNIT}};margin-top: calc({{SIZE}}{{UNIT}}/2 * -1);margin-left: calc({{SIZE}}{{UNIT}}/2 * -1);',
				],
			],
		);

		$this->add_control(
			'float_images',
			[
				'label'         => '圖片設定',
				'show_label'    => false,
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        =>$float_image_repeater->get_controls(),
				'default'       => [],
				'prevent_empty' => false,
				'title_field'   => '{{{title}}}',
			]
		);

		$this->end_controls_section();
		// endregion

		// region items (icons)

		$this->start_controls_section(
			'items_section',
			[
				'label' => \esc_html__('Icons 設定', 'power_element'),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_alert',
			[
				'type'       => \Elementor\Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'heading'    => '只允許圖片類型 Icon',
				'content'    => \sprintf(
					'只能使用圖片類型 Icon (.png, jpg, gif, svg)，字體種類 Icon 無法調整寬度，如果要使用 Icon 圖示庫 請確保 <a target="_blank" href="%s">[ Elementor > 設定 > Features > Inline Font Icons ]</a>，的 Inline Font Icons 功能 <span style="color: green">已啟用</span> ',
					\admin_url('admin.php?page=elementor-settings#tab-experiments')
				),
			]
		);

		$items_repeater = new \Elementor\Repeater();

		$items_repeater->add_control(
			'title',
			[
				'label' => '名稱(不顯示)',
				'type'  => \Elementor\Controls_Manager::TEXT,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$items_repeater->add_control(
			'post_id',
			[
				'label'      => '綁定的文章 id',
				'type'       => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'number',
				'ai'         => [
					'active' => false,
				],
			]
		);

		$items_repeater->add_control(
			'icon',
			[
				'type'        => \Elementor\Controls_Manager::ICONS,
				'label'       => 'ICON',
				'description' => ControlUtils::get_icon() . ' 只允許圖片類型 Icon',
				'skin'        => 'inline',
				'recommended' => [
					'fa-solid' => [
						'map-marker-alt',
					],
				],
			],
		);

		$items_repeater->add_control(
			'position_top',
			[
				'label'     => '絕對位置(上)(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			],
		);

		$items_repeater->add_control(
			'position_left',
			[
				'label'     => '絕對位置(左)(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__icon' => 'left: {{SIZE}}{{UNIT}};',
				],
			],
		);

		$items_repeater->add_control(
			'icon_width',
			[
				'label'     => 'Icon 寬度(%)',
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => '%',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__icon' => 'width: {{SIZE}}{{UNIT}};margin-top: calc({{SIZE}}{{UNIT}}/2 * -1);margin-left: calc({{SIZE}}{{UNIT}}/2 * -1);',
				],
			],
		);

		$items_repeater->add_control(
			'icon_color',
			[
				'label'     => '顏色',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#d5758d',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.pe_interactive_image__icon svg' => 'fill: {{VALUE}};',
				],
			],
		);

		$this->add_control(
			'items',
			[
				'label'         => '跳動 Icon',
				'show_label'    => false,
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        =>$items_repeater->get_controls(),
				'default'       => [],
				'prevent_empty' => false,
				'title_field'   => '{{{title}}}',
			]
		);

		$this->add_control(
			'card_display_items',
			[
				'label'       => '卡片要顯示的內容',
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

		$this->add_control(
			'card_meta_key',
			[
				'label'       => '額外要顯示的 meta_key',
				'description' => ControlUtils::get_icon() . ' 可以用逗號 , 隔開多個 meta_key',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'card_content',
			[
				'label' => '更多自訂內容',
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();
		// endregion

		// region 列表設定

		$this->start_controls_section(
			'list_items_section',
			[
				'label' => \esc_html__('列表設定', 'power_element'),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'is_unique',
			[
				'label'       => '隱藏列表中，綁定相同的 post_id 項目',
				'description' => ControlUtils::get_icon() . ' 例如有 3 個 icon 都是綁定同一個 post_id 列表中只會出現一個',
				'type'        => \Elementor\Controls_Manager::SWITCHER ,
				'default'     => 'yes',
			]
		);

		$this->add_control(
			'list_item_display_items',
			[
				'label'       => '列表要顯示的內容',
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

		$this->add_control(
			'list_item_meta_key',
			[
				'label'       => '額外要顯示的 meta_key',
				'description' => ControlUtils::get_icon() . ' 可以用逗號 , 隔開多個 meta_key',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'list_item_content',
			[
				'label' => '更多自訂內容',
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
				'ai'    => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();
		// endregion 列表設定

		Styles::register_controls($this);
	}

	/** @return string 取得小工具圖示 */
	public function get_icon(): string {
		return 'eicon-image-hotspot';
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

		$settings  = $this->get_settings_for_display();
		$is_unique = ( $settings['is_unique'] ?? 'yes' ) === 'yes';

		\printf('<div class="pe_interactive_image" data-is-unique="%1$s">', $settings['is_unique'] ?? 'yes');
		// region 開始左半邊
		echo '<div class="pe_interactive_image__left">';

		// region 開始 background_image
		echo '<div class="relative">';

		\printf(
			'<img class="pe_interactive_image__bg_image" src="%1$s">',
			$settings['background_image']['url']
		);

		// -- 其他 float 圖片 -- //
		foreach ($settings['float_images'] as $item) :
			\printf(
				'<div class="pe_interactive_image__image %1$s"></div>',
				ControlUtils::get_current_item_id($item),
			);
		endforeach;

		// -- 圖標 -- //
		foreach ($settings['items'] as $index => $item) :
			$bound_post_id = $item['post_id'] ?? '';
			\printf(
				'<div class="pe_interactive_image__icon %1$s" data-card-id="%2$s" data-card-post-id="%3$s">',
				ControlUtils::get_current_item_id($item),
				$item['_id'],
				$bound_post_id
			);

			\Elementor\Icons_Manager::render_icon(
				$item['icon'],
				[
					'aria-hidden' => 'true',
				]
				);
			echo '</div>';
		endforeach;

		// region 縮圖卡片
		$handled_post_ids = [];
		foreach ($settings['items'] as $index => $item) :
			$bound_post_id = $item['post_id'] ?? '';

			$is_preview = \is_admin() && $_GET['action'] === 'elementor';

			if (\is_numeric($bound_post_id)) {
				$render_card = $is_unique ? !\in_array($bound_post_id, $handled_post_ids) : true;

				if (!$render_card) {
					continue;
				}
				$content                 = self::get_card_content($item, $settings);
				[$calc_top, $calc_left ] = self::calc_top_left($item, $settings);
				$position_css            = "left:{$calc_left}%;top:calc({$calc_top}% - 1rem);";
				if (!$is_unique) {
					$icon_top     = $item['position_top']['size'] ?? 0;
					$icon_width   = $item['icon_width']['size'] ?? 5;
					$calc_top     = $icon_top - $icon_width;
					$calc_left    = $item['position_left']['size'] ?? 0;
					$position_css = "left:{$calc_left}%;top:calc({$calc_top}% - 1rem);";
				}

				\printf(
						'
                   <a href="%1$s" target="_blank">
                        <div class="pe_interactive_image__card" style="%2$s" data-card-id="%3$s" data-card-post-id="%4$s">
                            %5$s
                        </div>
                  </a>
                ',
						\esc_url(\get_permalink($bound_post_id)),
						$is_preview ? "display:block;{$position_css}" : $position_css,
						$item['_id'],
						$bound_post_id,
						$content,
					);
				$handled_post_ids[] = $bound_post_id;
			}
		endforeach;
		// endregion 縮圖卡片

		echo '</div>';
		// endregion 結束 background_image -- //

		echo '</div>';
		// endregion  結束左半邊

		// region 開始右半邊
		if (100 != $settings['background_image_width']['size']) :
			echo '<div class="pe_interactive_image__right">';
			echo '<div class="p-4">';

			$items = $settings['items'];
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
                <div data-list-item-id="%2$s" class="pe_interactive_image__list_item %3$s">
                    %4$s
                </div>
            </a>
            ',
				\esc_url(\get_permalink($bound_post_id)),
				$is_unique ? $item['post_id'] : $item['_id'],
				ControlUtils::get_current_item_id($item),
				$content,
					);

			endforeach;

			echo '</div>';
			echo '</div>';
		endif;
		// endregion 結束右半邊

		echo '</div>';

		// echo '<pre>';
		// var_dump($settings);
		// echo '</pre>';
	}

	/**
	 * 取得動態內容
	 *
	 * @param array $item icon 項目
	 * @param array $settings 設定
	 * @return string
	 */
	private static function get_card_content( array $item, array $settings ): string {
		$bound_post_id = $item['post_id'] ?? null;
		if (!\is_numeric($bound_post_id)) {
			return '';
		}
		$display_items           = $settings['card_display_items'] ?? [];
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

		$meta_key = $settings['card_meta_key'] ?? '';
		if (\is_string($meta_key) && $meta_key) {
			$meta_keys = self::parse_comma_string($meta_key);
			foreach ($meta_keys as $meta_key) {
				$value    = \get_post_meta($bound_post_id, $meta_key, true);
				$content .= "<p>{$value}</p>";
			}
		}

		$description = $settings['card_content'] ?? '';
		if ($description) {
			$content .= $description;
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
	 * @param array $item icon 項目
	 * @param array $settings 設定
	 * @return array [top, left]
	 */
	private static function calc_top_left( array $item, array $settings ): array {
		$is_unique  = ( $settings['is_unique'] ?? 'yes' ) === 'yes';
		$icon_width = $item['icon_width']['size'] ?? 5;

		if ($is_unique) {
			$items = $settings['items'];

			$sum_left = 0;
			$min_top  = 100;
            $count = 0;
			foreach ($items as $i) {
				if ($item['post_id'] != $i['post_id']) {
					continue;
				}
				$icon_left = $i['position_left']['size'] ?? 0;
				$sum_left += $icon_left;
				$icon_top  = $i['position_top']['size'] ?? 0;
				if ($icon_top < $min_top) {
					$min_top = $icon_top;
				}
                $count++;
			}

			$calc_top  = $min_top - $icon_width;
			$calc_left = ( $sum_left / $count );
			return [ $calc_top, $calc_left ];
		}

		$icon_top = $item['position_top']['size'] ?? 0;

		$calc_top  = $icon_top - $icon_width;
		$calc_left = $item['position_left']['size'] ?? 0;

		return [ $calc_top, $calc_left ];
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
		$display_items           = $settings['list_item_display_items'] ?? [];
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

		$meta_key = $settings['list_item_meta_key'] ?? '';
		if (\is_string($meta_key) && $meta_key) {
			$meta_keys = self::parse_comma_string($meta_key);
			foreach ($meta_keys as $meta_key) {
				$value    = \get_post_meta($bound_post_id, $meta_key, true);
				$content .= "<p>{$value}</p>";
			}
		}

		$description = $settings['list_item_content'] ?? '';
		if ($description) {
			$content .= $description;
		}

		return $content;
	}
}
