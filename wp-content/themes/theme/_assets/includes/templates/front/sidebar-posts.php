	<?php if( $posts->have_posts()): while ( $posts->have_posts() ) : $thePost = $posts->the_post();?>
		
		<a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>" alt="<?php echo get_the_title(); ?>">

			<div class="sidebar post clearing">
				<figure class="half col">
					<?php if ( has_post_thumbnail() ) : ?>
						<img src="<?php echo get_site_url().wp_get_attachment_url( get_post_thumbnail_id(get_the_id()) ); ?>" />
					<?php endif; ?>
				</figure>

				<article class="half push-right col">
					<?php the_title( '<p><strong>', '</strong></p>' ); ?>
					<?php the_excerpt(); ?>
					<span class="meta location"><?php $cat = get_the_category(); echo $cat[0]->name; ?></span>
				</article>
			</div>

		</a>

	<?php endwhile; endif; ?>