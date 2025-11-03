<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage;

use J7\PowerElement\ElementorWidgets\Shared\Utils\ControlUtils;
use J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage as Shortcode;

final class InteractiveImage extends \Elementor\Widget_Base {


	/** @return string 取得小工具名稱 */
	public function get_name(): string {
		[$shortcode_name] = Shortcode::get_info();
		return $shortcode_name;
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
	//
	// public function get_script_depends(): array {
	// return [ 'script-handle' ];
	// }
	//
	// public function get_style_depends(): array {
	// return [ 'style-handle' ];
	// }

	protected function register_controls(): void {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', 'textdomain'),
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
				'title_field' => '圖片',
			]
		);

		// endregion

		// region items (icons)

		$items_repeater = new \Elementor\Repeater();

		$items_repeater->add_control(
			'post_id',
			[
				'label' => '綁定的文章 id',
				'type'  => \Elementor\Controls_Manager::NUMBER,
				'min'   => 0,
			],
		);

		$items_repeater->add_control(
			'icon',
			[
				'type'        => \Elementor\Controls_Manager::ICONS,
				'label'       => 'ICON',
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
				'label'   => '跳動 Icon',
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  =>$items_repeater->get_controls(),
				'default' => [],
				'title_field' => 'Icon',
			]
		);

		// endregion

		$this->end_controls_section();
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

		echo '<div class="pe-interactive-image flex flex-col lg:flex-row shadow-xl">';
		// region 開始左半邊
		echo '<div data-interactive-image-left class="w-full lg:w-2/3">';

		// region 開始 background_image
		echo '<div class="relative">';

		\printf(
			'<img data-bg-image class="w-full" src="%1$s">',
			$settings['background_image']['url']
		);

		// -- 其他 float 圖片 -- //
		foreach ($settings['float_images'] as $item) :
			$image       = $item['image'];
			$hover_image = $item['hover_image'] ?? [];
			$image_width = $item['image_width'];
			$position    = $item['position'];

			\printf(
			'<img src="%1$s" data-float-image class="absolute transition duration-300 z-10" style="%2$s %3$s" />
							<img src="%4$s" data-float-hover-image class="absolute transition duration-300 z-20 tw-hidden" style="%2$s %3$s" />
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
				'<div data-icon class="absolute animate-bounce z-30 cursor-pointer " style="%s">',
				ControlUtils::get_position_styles_from_dimension($item['position']) . ControlUtils::get_styles_from_image_width($item['icon_width'])
			);
			\Elementor\Icons_Manager::render_icon(
				$item['icon'],
				[
					'aria-hidden' => 'true',
					'class'       => ' ',
					'style'       => "fill:{$item['icon_color']};",
				]
				);

			// region 縮圖卡片
			$post_id = $item['post_id'] ?? null;
			if (\is_numeric($post_id)) {
				\printf(
					'
 									<a href="%5$s" target="_blank" style="text-decoration: none;color: unset">
										<div data-card-id="%4$s" class="w-40 p-2 rounded-md bg-white shadow-md absolute left-1/2" style="top:-1rem;transform: translate(-50%%, -100%%);display: none;">
														<img src="%1$s" class="w-full h-full object-cover rounded-md mb-2"/>
														<h3 class="text-sm m-0 font-normal">%2$s</h3>
														<p class="text-xs m-0 font-thin">%3$s</p>
										</div>
                  </a>
                ',
					\esc_url(\get_the_post_thumbnail_url($post_id, 'medium')),
					\esc_html(\get_the_title($post_id)),
					\esc_html(\get_the_excerpt($post_id)),
					$item['_id'],
					\esc_url(\get_permalink($post_id))
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
		echo '<div data-interactive-image-right class="w-full lg:w-1/3 bg-white overflow-y-auto">';
		echo '<div class="p-4">';

		foreach ($settings['items'] as $item) :
			$post_id = $item['post_id'] ?? null;
			if (!\is_numeric($post_id)) {
				continue;
			}
			\printf(
				'
            <a href="%1$s" target="_blank" style="text-decoration: none;color: unset">
                <div data-list-item-id="%4$s" class="rounded-md hover:bg-gray-100 bg-white transition duration-300 p-4 mb-1">
                    <h3 class="text-lg m-0 font-semibold">%2$s</h3>
                    <p class="text-sm m-0 font-normal">%3$s</p>
                </div>
            </a>
            ',
				\esc_url(\get_permalink($post_id)),
				\esc_html(\get_the_title($post_id)),
				\esc_html(\get_the_excerpt($post_id)),
				$item['_id']
				);

			endforeach;

		echo '</div>';
		echo '</div>';
		// endregion 結束右半邊

		echo '</div>';

//		echo '<pre>';
//		var_dump($settings['float_images'][0]);
//		echo '</pre>';

		// [$shortcode_name] = Shortcode::get_info();
		// echo \do_shortcode("[{$shortcode_name}]");

		?>

		<script async>
					(function($){
						$(document).ready(function(){

							// 互動效果，滑鼠 hover list item 時顯示卡片
							$('.pe-interactive-image').on('mouseenter', 'div[data-list-item-id]', function(){
								const id = $(this).data('list-item-id')
								$(`div[data-card-id]:not([data-card-id=${id}])`).fadeOut()
								$(`div[data-card-id=${id}]`).fadeIn();
							});

							// 滑鼠移出 card div 時隱藏卡片
							$('.pe-interactive-image').on('mouseleave', 'div[data-card-id]', function(){
								$(this).fadeOut();
							});

							// hover 建築物時，替換為 hover-src
							$('.pe-interactive-image').on('mouseenter', 'img[data-float-image]', function(){
								const $nextHoverImg = $(this).next('img[data-float-hover-image]');
								$nextHoverImg.fadeIn();
							});

							// 滑鼠移出建築物時，替換為 original-src
							$('.pe-interactive-image').on('mouseleave', 'img[data-float-hover-image]', function(){
								$(this).fadeOut()
							});

							// 點擊 icon 時，顯示卡片
							$('.pe-interactive-image').on('click', 'div[data-icon]', function(){
								const card = $(this).find('div[data-card-id]')
								const id = card.data('card-id]');
								$(`div[data-card-id]:not([data-card-id=${id}])`).fadeOut()
								card.fadeIn()
							});

							setListHeight();

							// $(window).resize(function(){
							// debounce
							// 	setListHeight();
							// })

							// 將列表高度與圖片等高，超過就顯示 scroll
							function setListHeight(){
								const imgH = $('img[data-bg-image]').height();
								$('div[data-interactive-image-right]').height(imgH)
							}

						})
					})(jQuery)

		</script>

		<?php
	}
}
