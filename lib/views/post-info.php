<span <?php echo genesis_attr( 'entry-author' ); ?>>
	<a href="<?php echo $this->author_url; ?>" <?php echo $this->link_attr; ?>>
		<?php echo get_avatar( $this->author_id, $this->config['avatar_size'], '', $this->author_name ); ?>
	</a>
	<a href="<?php echo $this->author_url; ?>" <?php echo $this->link_attr; ?>>
		<span <?php echo genesis_attr( 'entry-author-name' ); ?>>
			<?php esc_html_e( $this->author_name ); ?>
		</span>
	</a>
</span>

<?php printf( '[post_date before="%s"]', "<i class='fa fa-calendar'></i>" ); ?>

<?php echo $this->do_comments(); ?>