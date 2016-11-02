<?php
namespace DanielPost\CPTT\WordPress;

class Admin {

    const TEXT_DOMAIN = 'dp-cppt-admin';

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_cpt_template_metabox' ) );
        add_action( 'admin_menu' , array( $this, 'remove_cpt_template_metabox' ) );
        add_action( 'save_post', array( $this, 'save_cpt_template_metabox' ) );
    }

    // Add our own custom Page Attributes metabox
    public function add_cpt_template_metabox() {
        $post_types = get_post_types();
        $other_post_types = array( 'post', 'attachment', 'revision', 'nav_menu_item' );

        foreach( $post_types as $ptype ) {
            // Make sure we only add metabox for CPTs and the Page screen
            if ( !in_array( $ptype, $other_post_types ) ) {
                add_meta_box( 'cpt-selector', __('Page Attributes', Plugin::TEXT_DOMAIN), array( $this, 'create_cpt_template_metabox' ), $ptype, 'side', 'core' );
            }
        }
    }

    // Remove standard Page Attributes metabox
    public function remove_cpt_template_metabox() {
        $post_types = get_post_types();
        $other_post_types = array( 'post', 'attachment', 'revision', 'nav_menu_item' );

        foreach( $post_types as $ptype ) {
            // Make sure we only remove standard metabox for CPTs and the Page screen
            if ( !in_array( $ptype, $other_post_types ) ) {
                remove_meta_box( 'pageparentdiv', $ptype, 'normal' );
            }
        }
    }

    // Create custom Page Attributes metabox
    public function create_cpt_template_metabox( $post ) {
        $post_meta = get_post_meta( $post->ID );
        $templates = wp_get_theme()->get_page_templates();
        $post_type_object = get_post_type_object( $post->post_type );
        $custom_post_types = array_slice( get_post_types(), 5 );

        // Parent Dropdown
        if ( $post_type_object->hierarchical ) {
            $dropdown_args = array(
                'post_type'        => $post->post_type,
                'exclude_tree'     => $post->ID,
                'selected'         => $post->post_parent,
                'name'             => 'parent_id',
                'show_option_none' => __('(no parent)'),
                'sort_column'      => 'menu_order, post_title',
                'echo'             => 0,
            );

            $dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
            $pages = wp_dropdown_pages( $dropdown_args );

            if ( $pages ) { 
                echo '<p><strong>' . __('Parent', Plugin::TEXT_DOMAIN) . '</strong></p>';
                echo '<label class="screen-reader-text" for="parent_id">' .  __('Parent', Plugin::TEXT_DOMAIN) . '</label>';
                echo $pages;
            }
        }

        // Template Selector
        echo '<p><strong>' . __('Template', Plugin::TEXT_DOMAIN) . '</strong></p>';
        echo '<select id="cpt-selector" name="_wp_page_template"><option value="default">' . __('Default Template', Plugin::TEXT_DOMAIN) . '</option>';
        
        // Loop through the different page templates
        foreach ( $templates as $template_filename => $template_name ) {

            // Removes subfolders and template name from template filename. Example: page-templates/event-recurring.php -> event
            if ( strpos( $template_filename, '/' ) !== false ) {
                $template_slug = strstr( substr( $template_filename, strrpos( $template_filename, '/' ) + 1), '-', true );
            } else {
                $template_slug = strstr( $template_filename, '-', true );
            }

            // If the current post type is a CPT and it matches the template slug, show the template since it's a template for that particular CPT. Also, if the current post type is not a CPT and the template slug isn't for a CPT either, show template since it's a standard template and we're on a normal page.
            if ( ( $post->post_type == $template_slug && in_array( $post->post_type, $custom_post_types ) ) || count( array_intersect( array( $post->post_type, $template_slug), $custom_post_types ) ) == 0 ) {
                if ( isset($post_meta['_wp_page_template'][0]) && ($post_meta['_wp_page_template'][0] == $template_filename) ) {
                    echo '<option value="' . $template_filename. '" selected="selected">' . $template_name . '</option>';
                } else {
                    echo '<option value="' . $template_filename. '">' . $template_name . '</option>';
                }
            }
        }
        echo '</select>';

        // Page order
        echo '<p><strong>' . __('Order', Plugin::TEXT_DOMAIN) . '</strong></p>';
        echo '<p><label class="screen-reader-text" for="menu_order">' . __('Order', Plugin::TEXT_DOMAIN) . '</label><input name="menu_order" type="text" size="4" id="menu_order" value="' . esc_attr($post->menu_order) . '"/></p>';
    }

    public function save_cpt_template_metabox( $post_id ) {
        if ( isset( $_REQUEST['_wp_page_template'] ) ) {
            update_post_meta( $post_id, '_wp_page_template', $_REQUEST['_wp_page_template'] );
        }
    }

}