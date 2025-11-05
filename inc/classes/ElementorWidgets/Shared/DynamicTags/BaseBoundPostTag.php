<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\DynamicTags;


abstract class BaseBoundPostTag extends \Elementor\Core\DynamicTags\Tag {

    public function get_group() {
        return ['power-element'];
    }



    /**
     * Register dynamic tag controls.
     *
     * Add input fields to allow the user to customize the ACF average tag settings.
     *
     * @since 1.0.0
     * @access protected
     * @return void
     */
    protected function register_controls() {
        parent::register_controls();
        $this->add_control(
            'bound_post_id',
            [
                'label' => '綁定的 post_id',
                'type' => 'text',
                'ai' => [
                    'active' => false,
                ]
            ]
        );
    }
}