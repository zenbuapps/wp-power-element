<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums;

use J7\PowerElement\ElementorWidgets\InteractiveImage\InteractiveImage;

enum EField: string {


	case CARD_TITLE         = 'card_title';
	case CARD_TITLE_COLOR   = 'card_title_color';
	case CARD_EXCERPT       = 'card_excerpt';
	case CARD_EXCERPT_COLOR = 'card_excerpt_color';
	case LIST_TITLE         = 'list_title';
	case LIST_TITLE_COLOR   = 'list_title_color';
	case LIST_EXCERPT       = 'list_excerpt';
	case LIST_EXCERPT_COLOR = 'list_excerpt_color';

	/** @return bool 是否為列表 */
	public function is_list(): bool {
		return match ($this) {
			self::LIST_TITLE,
			self::LIST_TITLE_COLOR,
			self::LIST_EXCERPT,
			self::LIST_EXCERPT_COLOR => true,
			default => false
		};
	}

	/** @return void 添加控制項 */
	public function add_control( InteractiveImage $widget ) {
		if ($this->is_color()) {
			$widget->add_control(
				$this->value,
				[
					'label'     =>$this->label(),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						$this->selectors() => 'color: {{VALUE}};',
					],
				],
			);
			return;
		}

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => $this->value,
				'label'    => $this->label(),
				'selector' => $this->selectors(),
			]
		);
	}

	/** @return bool 是否為顏色控件 */
	public function is_color(): bool {
		return \str_ends_with($this->value, '_color');
	}

	/** @return string 標題 */
	public function label(): string {
		return match ($this) {
			self::CARD_TITLE => '卡片標題',
			self::CARD_TITLE_COLOR => '卡片標題顏色',
			self::CARD_EXCERPT => '卡片描述',
			self::CARD_EXCERPT_COLOR => '卡片描述顏色',
			self::LIST_TITLE => '列表標題',
			self::LIST_TITLE_COLOR => '列表標題顏色',
			self::LIST_EXCERPT => '列表描述',
			self::LIST_EXCERPT_COLOR => '列表描述顏色',
		};
	}

	/** @return string selectors */
	public function selectors(): string {
		$prefix = '{{WRAPPER}} .pe_interactive_image';
		return match ($this) {
			self::CARD_TITLE_COLOR,
			self::CARD_TITLE => "{$prefix}__card h4",
			self::CARD_EXCERPT_COLOR,
			self::CARD_EXCERPT => "{$prefix}__card p",
			self::LIST_TITLE_COLOR,
			self::LIST_TITLE => "{$prefix}__list_item h4",
			self::LIST_EXCERPT_COLOR,
			self::LIST_EXCERPT => "{$prefix}__list_item p",
		};
	}
}
