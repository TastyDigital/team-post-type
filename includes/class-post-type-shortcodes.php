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
        if($atts['group'] !== 'all'){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'team-category',
                    'field'    => 'slug',
                    'terms'    => $atts['group']
                )
            );
        }
        $col = round(12/$atts['cols']); // assuming 12 col bootstrap grid
        $members = get_posts($args);
        $str = '';
        if(count($members)>0){
            $social = array(
                'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.448 15.936c0 2.661-.029 4.502-.087 5.525-.116 2.416-.836 4.288-2.161 5.613s-3.195 2.045-5.613 2.161c-1.023.057-2.864.087-5.525.087s-4.502-.029-5.525-.087c-2.416-.116-4.287-.836-5.612-2.161s-2.045-3.195-2.161-5.613c-.059-1.021-.087-2.864-.087-5.525s.029-4.502.087-5.525c.116-2.416.836-4.287 2.161-5.612s3.195-2.045 5.612-2.161c1.021-.057 2.864-.087 5.525-.087s4.502.029 5.525.087c2.416.116 4.288.836 5.613 2.161s2.045 3.195 2.161 5.612c.059 1.023.087 2.864.087 5.525zM17.396 4.948c-.807.005-1.252.009-1.334.009s-.525-.004-1.334-.009c-.807-.005-1.42-.005-1.839 0-.418.005-.979.023-1.682.052s-1.302.088-1.795.175c-.495.088-.909.195-1.246.323-.58.232-1.093.57-1.534 1.011s-.779.954-1.011 1.534c-.129.338-.236.752-.323 1.246s-.145 1.093-.175 1.795c-.029.704-.046 1.264-.052 1.682s-.005 1.032 0 1.839c.005.807.009 1.252.009 1.334s-.004.525-.009 1.334c-.005.807-.005 1.42 0 1.839.005.418.023.979.052 1.682s.088 1.302.175 1.795c.088.495.195.909.323 1.246.232.58.57 1.093 1.011 1.534s.952.779 1.534 1.011c.338.129.752.236 1.246.323.493.087 1.093.145 1.795.175.704.029 1.264.046 1.682.052s1.03.005 1.839 0c.807-.005 1.252-.009 1.334-.009.08 0 .525.004 1.334.009.807.005 1.42.005 1.839 0 .418-.005.979-.023 1.682-.052s1.302-.087 1.795-.175c.493-.087.909-.195 1.246-.323.58-.232 1.093-.57 1.534-1.011s.779-.952 1.011-1.534c.129-.337.236-.752.323-1.246.087-.493.145-1.093.175-1.795.029-.704.046-1.264.052-1.682s.005-1.03 0-1.839c-.005-.807-.009-1.252-.009-1.334 0-.08.004-.525.009-1.334.005-.807.005-1.42 0-1.839-.005-.418-.023-.979-.052-1.682s-.087-1.302-.175-1.795c-.087-.493-.195-.909-.323-1.246-.232-.58-.57-1.093-1.011-1.534s-.954-.779-1.534-1.011c-.337-.129-.752-.236-1.246-.323S21.619 5.03 20.917 5c-.704-.029-1.264-.046-1.682-.052-.418-.007-1.03-.007-1.839 0zm3.531 6.125c1.336 1.336 2.004 2.957 2.004 4.862s-.668 3.527-2.004 4.863c-1.336 1.336-2.957 2.004-4.863 2.004s-3.527-.668-4.863-2.004c-1.338-1.336-2.005-2.957-2.005-4.863s.668-3.527 2.004-4.863c1.336-1.336 2.957-2.004 4.863-2.004 1.907 0 3.527.668 4.864 2.004zm-1.709 8.018c.871-.871 1.307-1.923 1.307-3.155s-.436-2.284-1.307-3.155-1.923-1.307-3.155-1.307-2.284.436-3.155 1.307-1.307 1.923-1.307 3.155.436 2.284 1.307 3.155 1.923 1.307 3.155 1.307 2.284-.436 3.155-1.307zm5.125-11.434c.314.314.471.691.471 1.132s-.157.82-.471 1.132c-.314.314-.691.471-1.132.471s-.82-.157-1.132-.471c-.314-.314-.471-.691-.471-1.132s.157-.82.471-1.132c.314-.314.691-.471 1.132-.471.441.002.818.159 1.132.471z"/></svg>',
                'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M8.518 11.161v17.696H2.625V11.161h5.893zm.375-5.465Q8.911 7 7.991 7.875t-2.42.875h-.036q-1.464 0-2.357-.875t-.893-2.179q0-1.321.92-2.188t2.402-.866 2.375.866.911 2.188zm20.821 13.018v10.143h-5.875v-9.464q0-1.875-.723-2.938t-2.259-1.063q-1.125 0-1.884.616t-1.134 1.527q-.196.536-.196 1.446v9.875h-5.875q.036-7.125.036-11.554t-.018-5.286l-.018-.857h5.875v2.571h-.036q.357-.571.732-1t1.009-.929 1.554-.777 2.045-.277q3.054 0 4.911 2.027t1.857 5.938z"/></svg>',
                'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M30.071 7.286q-1.196 1.75-2.893 2.982.018.25.018.75 0 2.321-.679 4.634t-2.063 4.437-3.295 3.759-4.607 2.607-5.768.973q-4.839 0-8.857-2.589.625.071 1.393.071 4.018 0 7.161-2.464-1.875-.036-3.357-1.152t-2.036-2.848q.589.089 1.089.089.768 0 1.518-.196-2-.411-3.313-1.991t-1.313-3.67v-.071q1.214.679 2.607.732-1.179-.786-1.875-2.054t-.696-2.75q0-1.571.786-2.911Q6.052 8.285 9.15 9.883t6.634 1.777q-.143-.679-.143-1.321 0-2.393 1.688-4.08t4.08-1.688q2.5 0 4.214 1.821 1.946-.375 3.661-1.393-.661 2.054-2.536 3.179 1.661-.179 3.321-.893z"/></svg>',
                'facebook'=> '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M23.738.214v4.714h-2.804c-1.023 0-1.714.214-2.071.643s-.536 1.071-.536 1.929v3.375h5.232l-.696 5.286h-4.536v13.554h-5.464V16.161H8.309v-5.286h4.554V6.982c0-2.214.62-3.932 1.857-5.152S17.607 0 19.666 0c1.75 0 3.107.071 4.071.214z"/></svg>'
            );

            $str = '<div class="'.$atts['class'].'"><div class="row team-grid">';
            $box = '<div class="col-xs-%s">
                        <div class="block-%s hover-container">
                            %s                                         
                            <div class="hover-content">
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
                if (has_post_thumbnail($member->ID)) {
                    $pic = '<div class="team-media">';
                    $pic .= get_the_post_thumbnail($member->ID, 'mugshots', array( 'class' => 'media-object img-fluid' ));
                    $pic .= '</div>';
                }
                $str .= sprintf($box, $col, $count, $pic, $name, $custom_fields['profile_title'][0], wpautop($biocontent), $links);
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