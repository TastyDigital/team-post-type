<?php
/**
 * Team Post Type
 *
 * @package   Team_Post_Type
 * @license   GPL-2.0+
 */

/**
 * Register post types and taxonomies.
 *
 * @package Team_Post_Type
 */
class Team_Post_Type_Registrations {

	public $post_type = 'team';

	public $taxonomies = array( 'team-category' );

	public function init() {
		// Add the team post type and taxonomies
		add_action( 'init', array( $this, 'register' ));
		add_action( 'init', array( $this, 'format' ), 30);
		add_action( 'pre_get_posts', array( $this, 'sort_team_by_menu_order') );
	}

	/**
	 * Initiate registrations of post type and taxonomies.
	 *
	 * @uses Team_Post_Type_Registrations::register_post_type()
	 * @uses Team_Post_Type_Registrations::register_taxonomy_category()
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomy_category();
	}
	public function format() {
		$theme = wp_get_theme()->parent();
		if($theme == 'Genesis'){
			remove_post_type_support( $this->post_type, 'genesis-entry-meta-before-content' );
			add_filter( 'genesis_post_title_output',  array( $this, 'genesis_team_add_subtitle'), 10, 3);
			add_action( 'genesis_entry_footer', array( $this, 'genesis_output_team_socials') );
			add_action('genesis_entry_header', array( $this, 'genesis_output_mugshot'), 2 );
		}else{
			// generic wordpress title filter.
			add_filter('the_title',  array( $this, 'team_add_subtitle'), 10, 2);
			add_action('the_content', array( $this, 'generic_output_team_socials') );
		}
	}
	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	protected function register_post_type() {
		$labels = array(
			'name'               => __( 'Team', 'team-post-type' ),
			'singular_name'      => __( 'Team Member', 'team-post-type' ),
			'add_new'            => __( 'Add Profile', 'team-post-type' ),
			'add_new_item'       => __( 'Add Profile', 'team-post-type' ),
			'edit_item'          => __( 'Edit Profile', 'team-post-type' ),
			'new_item'           => __( 'New Team Member', 'team-post-type' ),
			'view_item'          => __( 'View Profile', 'team-post-type' ),
			'search_items'       => __( 'Search Team', 'team-post-type' ),
			'not_found'          => __( 'No profiles found', 'team-post-type' ),
			'not_found_in_trash' => __( 'No profiles in the trash', 'team-post-type' ),
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail',
			'page-attributes',
			'revisions',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'team', 'with_front' => false ), // Permalinks format
			'menu_position'   => 30,
			'menu_icon'       => 'dashicons-id',
			'has_archive'      => true  // change to enable team archive page
		);

		$args = apply_filters( 'team_post_type_args', $args );

		register_post_type( $this->post_type, $args );


	}

	/**
	 * Register a taxonomy for Team Categories.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	protected function register_taxonomy_category() {
		$labels = array(
			'name'                       => __( 'Team Categories', 'team-post-type' ),
			'singular_name'              => __( 'Team Category', 'team-post-type' ),
			'menu_name'                  => __( 'Team Categories', 'team-post-type' ),
			'edit_item'                  => __( 'Edit Team Category', 'team-post-type' ),
			'update_item'                => __( 'Update Team Category', 'team-post-type' ),
			'add_new_item'               => __( 'Add New Team Category', 'team-post-type' ),
			'new_item_name'              => __( 'New Team Category Name', 'team-post-type' ),
			'parent_item'                => __( 'Parent Team Category', 'team-post-type' ),
			'parent_item_colon'          => __( 'Parent Team Category:', 'team-post-type' ),
			'all_items'                  => __( 'All Team Categories', 'team-post-type' ),
			'search_items'               => __( 'Search Team Categories', 'team-post-type' ),
			'popular_items'              => __( 'Popular Team Categories', 'team-post-type' ),
			'separate_items_with_commas' => __( 'Separate team categories with commas', 'team-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove team categories', 'team-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used team categories', 'team-post-type' ),
			'not_found'                  => __( 'No team categories found.', 'team-post-type' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'team-category' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'team_post_type_category_args', $args );

		register_taxonomy( $this->taxonomies[0], $this->post_type, $args );
	}

	public function sort_team_by_menu_order($wp_query){
		if ( $wp_query->is_main_query() && ( is_tax('team-category') || is_post_type_archive('team') ) && !is_admin()) {
			$wp_query->set('orderby', 'menu_order');
			$wp_query->set('order', 'ASC');
		}
	}

	public function team_add_subtitle($title, $id){
		if( get_post_type( $id ) === $this->post_type && !is_admin() ){
			$subtitle = get_post_meta($id, 'profile_title',true);
			if($subtitle != '') {
				if ( is_singular( 'team' ) && $id == get_queried_object_id() ) {
					$title = '<h1 class=""entry-title">' . $title . '</h1><h3 class="team-role">' . $subtitle . '</h3>';
				}else{
					$title = '<h2 class=""entry-title">' . $title . '</h2><h4 class="team-role">' . $subtitle . '</h4>';
				}
			}
		}
		return $title;
	}
	public function genesis_team_add_subtitle($output, $wrap, $title){
		if( get_post_type( get_the_ID() ) === $this->post_type && !is_admin() ){
			$subtitle = get_post_meta(get_the_ID(), 'profile_title',true);
			if($subtitle != '') {
				if ( is_singular( 'team' ) ) {
					$output .= '<h3 class="team-role">' . $subtitle . '</h3>';
				}else{
					$output .= '<h4 class="team-role">' . $subtitle . '</h4>';
				}
			}
		}
		return $output;

	}
	public function genesis_output_team_socials(){
		if( get_post_type( get_the_ID() ) === $this->post_type ) {
			echo $this->output_team_socials();
		}
	}
	public function generic_output_team_socials($content){
		if( get_post_type( get_the_ID() ) === $this->post_type ) {
			return $content . $this->output_team_socials();
		}
		return $content;
	}
	public function output_team_socials(){
		$social = include 'social-icons.php';
		$custom_fields = get_post_custom(get_the_ID());
		$links = '';
		foreach ( $social as $key => $value ) {
			if( isset( $custom_fields['profile_'.$key][0] ) && $custom_fields['profile_'.$key][0] !== ''){
				$links .= '<li class="list-inline-item"><a href="'.$custom_fields['profile_'.$key][0].'" title="'.$key. '">' .  $value. '</a></li>';
			}
		}
		if($links !== ''){
			$links = '<ul class="list-inline team-social">'.$links.'</ul>';
		}

		return $links;
	}
	public function genesis_output_mugshot(){
		if( get_post_type( get_the_ID() ) === $this->post_type ) {

			$pic = '';
			if (has_post_thumbnail(get_the_ID())) {
				$pic = '<div class="team-portrait">';
				$pic .= get_the_post_thumbnail(get_the_ID(), 'portraits', array( 'class' => 'team-portrait' ));
				$pic .= '</div>';
				$pic .= '<div class="team-info">';
				add_action( 'genesis_entry_footer', array( $this, 'genesis_close_team_info'), 30 );

			}
			echo $pic;

		}
	}
	public function genesis_close_team_info(){
		echo '</div>';
	}
}