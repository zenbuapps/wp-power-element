<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\DynamicTags;
use ElementorPro\Modules\DynamicTags\Module;

class PostTitle extends BaseBoundPostTag {

    public function get_name() {
        return 'power-post-title';
    }

    public function get_title() {
        return esc_html__( 'Post Title', 'elementor-pro' );
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    public function render(): void {
        $bound_post_id = $this->get_settings( 'bound_post_id');
        echo  \get_the_title($bound_post_id);
    }

}