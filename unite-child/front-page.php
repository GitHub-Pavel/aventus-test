<?php
if ( 'page' == get_option( 'show_on_front' ) ) {
	$post_type = 'real_estate';
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => -1, 
		'orderby' => 'post_title', 
		'order' => 'ASC' 
	);


	if ( isset($_GET['re_type']) && $_GET['re_type'] !== '' ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'real_estate_type',
				'field'    => 'term_id',
				'terms'    => $_GET['re_type'],
			)
		);
	}

	$posts = count( $_GET ) ? (new WP_Query($args))->posts : unity_child_get_transient_posts( $post_type, false, $args );

	get_header(); ?>

	<div id="primary" class="content-area col-sm-12 col-md-12">
		<main id="main" class="site-main" role="main">
			<div class="row">
				<div class="col-12 col-md-9">
					<?php foreach( $posts as $key => $post ) { ?>

						<?php 
							$re_types = get_the_terms($post->ID, 'real_estate_type');
							$post_args = array();

							if ( unity_child_get_transient_field( 'price', $post->ID ) ) {
								$post_args['Price'] = unity_child_get_transient_field( 'price', $post->ID );
							}
							if ( unity_child_get_transient_field( 'space', $post->ID ) ) {
								$post_args['Space'] = unity_child_get_transient_field( 'space', $post->ID );
							}
							if ( unity_child_get_transient_field( 'living_space', $post->ID ) ) {
								$post_args['Living Space'] = unity_child_get_transient_field( 'living_space', $post->ID );
							}
							if ( unity_child_get_transient_field( 'address', $post->ID ) ) {
								$post_args['Address'] = unity_child_get_transient_field( 'address', $post->ID );
							}
							if ( unity_child_get_transient_field( 'floor', $post->ID ) ) {
								$post_args['Floor'] = unity_child_get_transient_field( 'floor', $post->ID );
							}
						?>

						<article 
							<?php post_class('', $post->ID); ?>
							id="post-<?php echo esc_attr($post->ID); ?>"
						>
							<div class="card">
								<div class="card-body">

									<h3 class="card-title"><?php echo esc_html($post->post_title); ?></h3>

									<?php if ( count($re_types) ) { ?>
										<nav class="nav">
											<?php foreach( $re_types as $re_type ) { ?>
												<?php
													$url = http_build_query(array_merge($_GET, array('re_type' => $re_type->term_id)));
												?>
												<a 
													class="nav-link" 
													href="<?php echo esc_url('?'.$url); ?>"
												>
													<?php echo esc_html($re_type->name); ?>
												</a>
											<?php } ?>
										</nav>
									<?php } ?>

									<?php if ( count( $post_args ) ) { ?>
										<table class="table">
											<thead>
												<tr>
													<?php foreach( $post_args as $key => $post_arg ) { ?>
														<th><?php esc_html_e( $key, CHILD_TEXT_DOMAIN ); ?></th>
													<?php } ?>
												</tr>
											</thead>
											<tbody>
												<tr>
													<?php foreach( $post_args as $key => $post_arg ) { ?>
														<?php if( isset( $post_arg ) && $post_arg !== '' ) { ?>
															<td><?php echo esc_html( $post_arg ); ?></td>
														<?php } ?>
													<?php } ?>
												</tr>
											</tbody>
										</table>
									<?php } ?>

									<a href="<?php echo get_permalink($post->ID); ?>" class="btn btn-primary"><?php esc_html_e('More', CHILD_TEXT_DOMAIN); ?></a>

								</div>
							</div>
						</article>

						<?php } ?>
				</div>

				<div class="col-12 col-md-3">
					<div class="home-widget-area">
						<div class="home-widget">
							<?php if( is_active_sidebar('home1') ) dynamic_sidebar( 'home1' ); ?>
						</div>

						<div class="home-widget">
							<?php if( is_active_sidebar('home2') ) dynamic_sidebar( 'home2' ); ?>
						</div>

						<div class="home-widget">
							<?php if( is_active_sidebar('home3') ) dynamic_sidebar( 'home3' ); ?>
						</div>
					</div>
				</div>

			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	get_footer();
}
?>