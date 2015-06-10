<article <?php echo genesis_attr( 'entry' ); ?>>
	<div class="related-entry-image">
		<?php $this->do_post_image(); ?>
	</div>
	<header <?php echo  genesis_attr( 'entry-header' ); ?>>
		<h4 class="entry-title">
			<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php the_title(); ?>
			</a>
		</h4>
		<h5 class="entry-subtitle"><?php esc_html_e( $this->render_subtitle() ); ?></h5>
		<?php $this->render_post_info(); ?>
	</header>
	<div <?php echo genesis_attr( 'entry-content' ); ?>>
		<?php $this->render_tldr(); ?>
	</div>
</article>