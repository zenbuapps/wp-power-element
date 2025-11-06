<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums;

enum EField: string {


	case CARD_TITLE         = 'card_title';
	case CARD_TITLE_COLOR   = 'card_title_color';
	case CARD_EXCERPT       = 'card_excerpt';
	case CARD_EXCERPT_COLOR = 'card_excerpt_color';
	case LIST_TITLE         = 'list_title';
	case LIST_TITLE_COLOR   = 'list_title_color';
	case LIST_EXCERPT       = 'list_excerpt';
	case LIST_EXCERPT_COLOR = 'list_excerpt_color';



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
		return match ($this) {
			self::CARD_TITLE_COLOR,
			self::CARD_TITLE => '{{WRAPPER}} .pe_interactive_image__card h4',
			self::CARD_EXCERPT_COLOR,
			self::CARD_EXCERPT => '{{WRAPPER}} .pe_interactive_image__card p',
			self::LIST_TITLE_COLOR,
			self::LIST_TITLE => '{{WRAPPER}} .pe_interactive_image__list_item h4',
			self::LIST_EXCERPT_COLOR,
			self::LIST_EXCERPT => '{{WRAPPER}} .pe_interactive_image__list_item p',
		};
	}
}
