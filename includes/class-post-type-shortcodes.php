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
		//add_shortcode( 'team-member', array( $this, 'team_member' ) );
        add_image_size( 'mugshots', 310, 310, true);
		add_image_size( 'portraits', 300, 375, true);
        add_action( 'wp_enqueue_scripts', array( $this, 'team_post_enqueue_styles') );
	}
    public function team_post_enqueue_styles(){
        wp_enqueue_style( 'team-styles',
            plugin_dir_url(__DIR__) . '/src/team-styles.css',
            array()
        );
    }

	/**
	 * Register the shortcodes to be used for the team post type
	 *
	 * @since 0.1.0
	 */
	public function team_grid( $atts = array()) {
		$atts = shortcode_atts( array(
            'cols' => '4',
            'class' => 'alignwide',
            'group' => 'all'
        ), $atts, 'team-grid' );
        
        $args = array(
            'post_status' => 'publish',
            'numberposts' => -1,
            'post_type'   => 'team',
            'orderby'    => 'menu_order',
            'sort_order' => 'asc'
        );
        $containerclass = '';
        if($atts['group'] !== 'all'){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'team-category',
                    'field'    => 'slug',
                    'terms'    => $atts['group']
                )
            );
            $containerclass .= ' '.$atts['group'].'-team-container';
        }
        $col = round(12/$atts['cols']); // assuming 12 col bootstrap grid
        $members = get_posts($args);
        $str = '';
        if(count($members)>0){

	        $social = include 'social-icons.php';

            $str = '<div class="'.$atts['class'].'"><div class="row team-grid">';
            $box = '<div class="col-sm-%s">
                        <div class="block-%s team-member-container%s">
                            %s                                         
                            <div class="team-member-content">
                                <div class="team-person">
                                    <div class="team-profile">
                                        <h2>%s</h2>
                                        <h3>%s</h3>
                                        %s
                                    </div>
                                    %s
                                </div>
                            </div>
                        </div>
                    </div>';
            $count=1;
            foreach($members as $member){
                $name = $member->post_title;
                $biocontent = $member->post_content;

                $custom_fields = get_post_custom($member->ID);
                $links = '';
                foreach ( $social as $key => $value ) {
                    if( isset( $custom_fields['profile_'.$key][0] ) && $custom_fields['profile_'.$key][0] !== ''){
                        $links .= '<li class="list-inline-item"><a href="'.$custom_fields['profile_'.$key][0].'" title="'.$key. '">' .  $value. '</a></li>';
                    }
                }
                if($links !== ''){
                    $links = '<ul class="list-inline team-social text-center">'.$links.'</ul>';
                }
                $pic = '';
                $teamclass = $containerclass;
                if (has_post_thumbnail($member->ID)) {
                    $pic = '<div class="team-media">';
                    $pic .= get_the_post_thumbnail($member->ID, 'mugshots', array( 'class' => 'team-portrait' ));
                    $pic .= '</div>';
                    $teamclass .= ' hover-container';
                }
                $str .= sprintf($box, $col, $count, $teamclass, $pic, $name, $custom_fields['profile_title'][0], wpautop($biocontent), $links);
                $count++;
            }
            $str .= '</div></div>';
        }else{
            $str = '<div class="row team-grid"><div class="col">'._('No team members found', 'team-post-type').'</div></div>';
        }
        return $str;
	}

   /**
	* Display single team member
	*
	* @since 0.1.0
	*/
	// function team_member( $atts = array() ) {

	// 	$atts = shortcode_atts( array(
    //         'id' => 0,
    //         'name' => ''
    //     ), $atts, 'team-member' );

    //     if (!empty($atts['id']) && is_int($atts['id'])) {
    //         $args['ID'] = $atts['id'];
    //     }else if (!empty($atts['name'])){
    //         $args['post_title'] = $atts['name'];
    //     }
    //     if(empty($args)){
    //         return '<p>'.__('Team member not found', 'team-post-type').'</p>';
    //     }
    //     $args['post_type']  = 'team_member';
    //     $args['post_status'] = 'publish';
    //     $args['numberposts'] = 1;

    //     //echo '<pre>'.print_r($args,true).'</Pre>';
    //     $member = get_posts($args);
    //     $member = $member[0];
        
          
    //     return '<p>HELLO '.$member->post_title.'</p><pre>'.print_r($args,true).'</pre>';
    //  }


}