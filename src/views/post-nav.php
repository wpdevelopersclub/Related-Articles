<section <?php echo genesis_attr( 'adjacent-entry-pagination' ); ?>>
	<div class="wrap">
		<?php if ( $this->has_adjacent_post() ) : ?>
		<div class="pagination-previous">
			<a href="<?php echo esc_url( $this->model->get_prev_post( 'url' ) ); ?>" rel="prev" title="Previous">
				<i class="fa fa-chevron-left"></i>
				<span><?php _e( 'Previous', 'wpdc' ); ?></span>
			</a>
			<span class="pagination-title"><?php esc_html_e( $this->model->get_prev_post( 'title' ) ) ?></span>
		</div>
		<?php endif; ?>
		<?php if ( $this->has_adjacent_post( false ) ) : ?>
			<div class="pagination-next">
				<a href="<?php echo esc_url( $this->model->get_next_post( 'url' ) ); ?>" rel="prev" title="Next" class="button">
					<span><?php _e( 'Next', 'wpdc' ); ?></span>
					<i class="fa fa-chevron-right"></i>
				</a>
				<p class="pagination-title"><?php esc_html_e( $this->model->get_next_post( 'title' ) ) ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>