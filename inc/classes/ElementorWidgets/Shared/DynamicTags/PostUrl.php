<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\DynamicTags;
use ElementorPro\Modules\DynamicTags\Module;

class PostUrl extends BaseBoundPostTag {

    public function get_name() {
        return 'power-post-url';
    }

    public function get_title() {
        return esc_html__( 'Post URL', 'elementor-pro' );
    }

    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    public function get_value( array $options = [] ) {
        $bound_post_id = $this->get_settings( 'bound_post_id');
        return  \get_permalink($bound_post_id);
    }

    public function render(): void {
        $bound_post_id = $this->get_settings( 'bound_post_id');
        echo  \get_permalink($bound_post_id);
    }

}