<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\Utils;

class ControlUtils {

    /**
     * 取得 repeater item 的 id
     * @param array $item Repeater 內的 item
     * @return string
     */
    public static function get_current_item_id(array $item):string
    {
        return  'elementor-repeater-item-' . \esc_attr( $item['_id']);
    }

    /**
     * @param string $icon icon 名稱
     * @param string $class_name css class
     * @return string
     * @see https://elementor.github.io/elementor-icons/
     */
    public static function get_icon( string $icon = 'eicon-info-circle', string $class_name = ''):string
    {
        return \sprintf('<i class="%1$s %2$s" aria-hidden="true"></i>', $icon, $class_name);
    }
}