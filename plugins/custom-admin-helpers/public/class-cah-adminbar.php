<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CAH_Adminbar
{

    public function remove_dashicons( $blacklist )
    {
        $show = $this->is_show_adminbar();
        if ( !$show )
            $blacklist[]='dashicons';
        return $blacklist;
    }

    /* Disable admin bar for all users except admin */
    public function admin_bar_visibility() {
        $show = $this->is_show_adminbar();
        show_admin_bar($show);
    }

    public function is_show_adminbar()
    {
        $show=true;
        switch (CAH_Assets::get_option('adminbar_visibility')) {
            case "admin":
            if (!current_user_can('administrator') && !is_admin())
                $show=false;
            break;
            case "loggedin":
                if (!is_user_logged_in() && !is_admin())
                $show=false;
            break;
            case "all":
                $show=true;
            break;
        }
        return $show;
    }

    public function filter_admin_bar_visibility($show) {
        switch (CAH_Assets::get_option('adminbar_visibility')) {
            case "admin":
                if (!current_user_can('administrator') && !is_admin())
                    $show=false;
                break;
            case "loggedin":
                if (!is_user_logged_in())
                    $show=false;
                break;
            case "all":
                $show=true;
                break;
        }
        return $show;
    }

    /* Disable dashboard for non admin */
    public function blockusers_init() {
        if ( is_admin() && !current_user_can('edit_others_pages') && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }


    public function add_toolbar_items($wp_admin_bar)
    {
        $menu_id = 'foodiepro';
        $wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => 'Foodiepro', 'href' => '/'));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('FoodiePro Settings'), 'id' => 'foodiepro_colortheme', 'href' => get_site_url(null, 'wp-admin/themes.php?page=foodiepro-options'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Users'), 'id' => 'foodiepro_users', 'href' => get_site_url(null, 'wp-admin/users.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Ingredients'), 'id' => 'foodiepro_ingredients', 'href' => get_site_url(null, 'wp-admin/edit-tags.php?taxonomy=ingredient&post_type=recipe'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Peepso'), 'id' => 'foodiepro_peepso', 'href' => get_site_url(null, 'wp-admin/admin.php?page=peepso'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Plugins'), 'id' => 'foodiepro_plugins', 'href' => get_site_url(null, 'wp-admin/plugins.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Contact Forms'), 'id' => 'foodiepro_contactforms', 'href' => get_site_url(null, 'wp-admin/edit.php?post_type=contact'), 'meta' => array('target' => '_blank')));
        // $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Drafts'), 'id' => 'dwb-drafts', 'href' => 'edit.php?post_status=draft&post_type=post'));
    }

}
