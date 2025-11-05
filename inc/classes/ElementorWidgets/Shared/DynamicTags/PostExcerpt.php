<?php

declare(strict_types=1);

namespace J7\PowerElement\ElementorWidgets\Shared\DynamicTags;
use Elementor\Controls_Manager;
use ElementorPro\Core\Utils;
use ElementorPro\Modules\DynamicTags\Module;

class PostExcerpt extends BaseBoundPostTag {

    public function get_name() {
        return 'power-post-excerpt';
    }

    public function get_title() {
        return esc_html__( 'Post Excerpt', 'elementor-pro' );
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    public function render() {
        $bound_post_id = $this->get_settings( 'bound_post_id');

        // Allow only a real `post_excerpt` and not the trimmed `post_content` from the `get_the_excerpt` filter
        $post = get_post($bound_post_id);
        $settings = $this->get_settings_for_display();

        if ( ! $this->is_post_excerpt_valid( $settings, $post ) ) {
            return;
        }

        $max_length = (int) $settings['max_length'];
        $excerpt = $this->get_post_excerpt( $settings, $post );

        $excerpt = Utils::trim_words( $excerpt, $max_length );

        echo wp_kses_post( $excerpt );
    }

    public function is_post_excerpt_valid( $settings, $post ) {
        if ( ! $post ) {
            return false;
        }

        if ( empty( $post->post_excerpt ) && ! $this->should_get_excerpt_from_post_content( $settings ) ) {
            return false;
        }

        if ( empty( $post->post_excerpt ) && empty( $post->post_content ) && $this->should_get_excerpt_from_post_content( $settings ) ) {
            return false;
        }

        if ( empty( $post->post_excerpt ) && empty( $post->post_content ) ) {
            return false;
        }

        return true;
    }

    public function should_get_excerpt_from_post_content( $settings ) {
        return 'yes' === $settings['apply_to_post_content'];
    }

    public function get_post_excerpt( $settings, $post ) {
        $post_excerpt = $post->post_excerpt ?? '';

        if ( empty( $post_excerpt ) && ! empty( $post->post_content ) && $this->should_get_excerpt_from_post_content( $settings ) ) {
            $post_excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $post ) );
        }

        return $post_excerpt;
    }


    protected function register_controls() {

        $this->add_control(
            'max_length',
            [
                'label' => esc_html__( 'Excerpt Length', 'elementor-pro' ),
                'type' => Controls_Manager::NUMBER,
            ]
        );

        $this->add_control(
            'apply_to_post_content',
            [
                'label' => esc_html__( 'Apply to post content', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementor-pro' ),
                'label_off' => esc_html__( 'No', 'elementor-pro' ),
                'default' => 'no',
            ]
        );

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