<?php
	// This is a template for the Studiopress Genesis Framework.
    // we are adding our own click-area link in this template.
    add_filter( 'genesis_link_post_title', '__return_false' );
	add_filter( 'genesis_entry_title_wrap', function(){ return 'h3'; } );
	$hub_image = get_the_post_thumbnail(get_the_ID(), 'hub');
	$details = '';
	if(!empty($hub_image)){
		$hub_image = '<div class="hub-image-holder">'. $hub_image . '</div>';
    }else{
		$excerpt = get_the_excerpt();
		$excerpt = substr($excerpt, 0, 260);
		$result = substr($excerpt, 0, strrpos($excerpt, ' '));
		$details = '<div class="results-description"><p>'. $result . '</p><a href="'. get_permalink().'" class="button">'.__('Read more','team-post-type').'</a></div>';
    }
	$class = 'col-md-4 col-sm-6';

	$meta = get_the_category_list(' / ');
	if(!empty($meta)){
		$meta = '<p class="post-meta">'.$meta .'</p>';
	}
	?>
	<div class="<?php echo $class; ?>">
		<article class="expert-hub">
		<a href="<?php the_permalink(); ?>" class="click-area" title="<?php the_title_attribute(); ?>">
			<?php echo $hub_image; ?>
			<header>
			<?php genesis_do_post_title(); ?>
			</header>
			<?php echo $details; ?>
		</a>
            <?php echo $meta; ?>
		</article>
	</div>
	