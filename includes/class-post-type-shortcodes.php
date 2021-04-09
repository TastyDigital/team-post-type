<?php
/**
 * Team Post Type
 *
 * @package   Team_Post_Type
 * @license   GPL-2.0+
 */

/**
 * Register shortcodes.
 *
 * @package Team_Post_Type
 */
class Team_Post_Type_Shortcodes {

	public function init() {
		add_shortcode( 'team-grid', array( $this, 'team_grid' ) );
		add_shortcode( 'team-member', array( $this, 'team_member' ) );
        add_image_size( 'mugshots', 310, 310, true);
	}

	/**
	 * Register the shortcodes to be used for the team post type
	 *
	 * @since 0.1.0
	 */
	public function team_grid( $atts = array()) {
		$atts = shortcode_atts( array(
            'colclass' => '4',
            'class' => 'default baz',
            'group' => 'all'
        ), $atts, 'team-grid' );
        
        $args = array(
            'post_status' => 'publish',
            'numberposts' => -1,
            'post_type'   => 'team',
            'orderby'    => 'menu_order',
            'sort_order' => 'asc'
        );
        if($atts['group'] !== 'all'){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'team-category',
                    'field'    => 'slug',
                    'terms'    => $atts['group']
                )
            );
        }
        $members = get_posts($args);
        $str = '';
        if(count($members)>0){

            $str = '<div class="row team-grid">';
            foreach($members as $member){
                $name = $member->post_title;
                $biocontent = $member->post_content;
                if (has_post_thumbnail($member->ID)) {
                    $pic = '<div class="team-media">';
                    $pic .= get_the_post_thumbnail($member->ID, 'mugshots', array( 'class' => 'media-object img-thumbnail' ));
                    $pic .= '</div>';
                }
                $str .= '<div class="col">'.$pic.'<h2>'.$name.'</h2>'.$biocontent.'</div>';
            }
            $str .= '</div>';
        }else{
            $str = '<div class="row"><div class="col">'._('No team members found', 'team-post-type').'</div></div>';
        }
        return $str;
	}

   /**
	* Display single team member
	*
	* @since 0.1.0
	*/
	function team_member( $atts = array() ) {

		$atts = shortcode_atts( array(
            'id' => 0,
            'baz' => 'default baz'
        ), $atts, 'team-member' );

        if (!empty($atts['id'])) {
            $args = array(
              'ID'        => $atts['id'],
              'post_type'   => 'team_member',
              'post_status' => 'publish',
              'numberposts' => 1
            );
            $member = get_posts($args);
            $member = $member[0];
        }
        return "team_member baz = {$atts['baz']}";
     }


}