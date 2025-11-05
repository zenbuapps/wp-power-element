<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\DynamicTags;

use ElementorPro\Modules\DynamicTags\Module;

class AuthorName extends BaseBoundPostTag {

    public function get_name() {
        return 'power-author-name';
    }

    public function get_title() {
        return esc_html__( 'Author Name', 'elementor-pro' );
    }


    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    public function render(): void {
        $bound_post_id = $this->get_settings( 'bound_post_id');
        $post = get_post($bound_post_id);
        if ( ! $post ) {
            echo '沒有綁定文章 id';
            return;
        }
        echo \wp_kses_post( \get_the_author_meta( 'display_name', $post->post_author ) );
    }
}
