<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\InteractiveImage\Shared\Enums;

enum EField: string {


	case CARD_TITLE_FONT_SIZE       = 'card_title_font_size';
	case CARD_TITLE_COLOR           = 'card_title_color';
	case CARD_EXCERPT_FONT_SIZE = 'card_excerpt_font_size';
	case CARD_EXCERPT_COLOR     = 'card_excerpt_color';
	case LIST_TITLE_FONT_SIZE       = 'list_title_font_size';
	case LIST_TITLE_COLOR           = 'list_title_color';
	case LIST_EXCERPT_FONT_SIZE = 'list_excerpt_font_size';
	case LIST_EXCERPT_COLOR     = 'list_excerpt_color';


    /**
     * @param array<string, mixed> $settings
     * @return string
     */
    public static function get_styles(array $settings):string
    {
        $output = "<style>";
        $output .= ".pe_interactive_image{";
        foreach (self::cases() as $field) {

            if(!isset($settings[$field->value])){
               continue;
            }
            $output .= $field->get_style($settings);
        }
        $output .= "}";
        $output .= "</style>";

      return $output;
    }

    /**
     * @param array<string, mixed> $settings
     * @return string
     */
    public function get_style(array $settings):string
    {
            if(!isset($settings[$this->value])){
                return '';
            }
            return match ($this) {
                self::CARD_TITLE_FONT_SIZE,
                self::CARD_EXCERPT_FONT_SIZE,
                self::LIST_TITLE_FONT_SIZE,
                self::LIST_EXCERPT_FONT_SIZE => "--pe-{$this->value}: {$settings[$this->value]}px;",
                self::CARD_TITLE_COLOR,
                self::CARD_EXCERPT_COLOR,
                self::LIST_TITLE_COLOR,
                self::LIST_EXCERPT_COLOR => "--pe-{$this->value}: {$settings[$this->value]};",
            };
    }

	/**
	 * 取得控制項設定參數
	 *
	 * @return array<string, mixed> 控制項參數陣列
	 */
	public function get_control_args(): array {

		$args          = [];
		$args['label'] = $this->label();
		$args['type']  = match ($this) {
			self::CARD_TITLE_FONT_SIZE,
			self::CARD_EXCERPT_FONT_SIZE,
			self::LIST_TITLE_FONT_SIZE,
			self::LIST_EXCERPT_FONT_SIZE => \Elementor\Controls_Manager::NUMBER,
			self::CARD_TITLE_COLOR,
			self::CARD_EXCERPT_COLOR,
			self::LIST_TITLE_COLOR,
			self::LIST_EXCERPT_COLOR => \Elementor\Controls_Manager::COLOR,
		};

		switch ($this) {
			case self::CARD_TITLE_FONT_SIZE:
			case self::CARD_EXCERPT_FONT_SIZE:
			case self::LIST_TITLE_FONT_SIZE:
			case self::LIST_EXCERPT_FONT_SIZE:
				$args['min'] = 4;
				$args['max'] = 100;
				break;
		}

		switch ($this) {
			case self::CARD_TITLE_FONT_SIZE:
			case self::LIST_TITLE_FONT_SIZE:
				$args['default'] = 16;
				break;
			case self::CARD_EXCERPT_FONT_SIZE:
			case self::LIST_EXCERPT_FONT_SIZE:
				$args['default'] = 14;
				break;
		}

		return $args;
	}

	/** @return string 標題 */
	public function label(): string {
		return match ($this) {
			self::CARD_TITLE_FONT_SIZE => '卡片標題字體大小',
			self::CARD_TITLE_COLOR => '卡片標題顏色',
			self::CARD_EXCERPT_FONT_SIZE => '卡片描述字體大小',
			self::CARD_EXCERPT_COLOR => '卡片描述顏色',
			self::LIST_TITLE_FONT_SIZE => '列表標題字體大小',
			self::LIST_TITLE_COLOR => '列表標題顏色',
			self::LIST_EXCERPT_FONT_SIZE => '列表描述字體大小',
			self::LIST_EXCERPT_COLOR => '列表描述顏色',
		};
	}
}
