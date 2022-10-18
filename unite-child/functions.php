<?php
define('CHILD_TEXT_DOMAIN', 'unity_child');

add_action( 'wp_enqueue_scripts', 'unity_child_enqueue_styles' );
function unity_child_enqueue_styles() {
    $parenthandle = 'unity-style';
    $theme = wp_get_theme();

    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', array(), $theme->parent()->get('Version'));
    wp_enqueue_style( 'unity-child-style', get_stylesheet_uri(), array( $parenthandle ), $theme->get('Version'));
}

add_action( 'init', 'unity_child_post_types' );
function unity_child_post_types(){
	register_taxonomy( 'real_estate_type', [ 'real_estate' ], [
		'label'                 => __('Real estate types', CHILD_TEXT_DOMAIN),
		'labels'                => [
			'name'              => 'Real estate types',
			'singular_name'     => 'Real estate type',
			'search_items'      => 'Search Real estate type',
			'all_items'         => 'All Real estate types',
			'view_item '        => 'View Real estate type',
			'parent_item'       => 'Parent Real estate type',
			'parent_item_colon' => 'Parent Real estate type:',
			'edit_item'         => 'Edit Real estate type',
			'update_item'       => 'Update Real estate type',
			'add_new_item'      => 'Add New Real estate type',
			'new_item_name'     => 'New Real estate type Name',
			'menu_name'         => 'Real estate types',
			'back_to_items'     => '← Back to Real estate type',
		],
		'description'           => 'Real estate types',
		'public'                => true,
		'hierarchical'          => false,
		'rewrite'               => true,
		'capabilities'          => array(),
		'meta_box_cb'           => null,
		'show_admin_column'     => false,
		'show_in_rest'          => null,
		'rest_base'             => null,
	] );
}


if( function_exists('acf_add_local_field_group') ) {
	acf_add_local_field_group(array(
			'key' => 'real_estate_fields',
			'title' => 'Real estate',
			'fields' => array (
					array (
							'key' => 'space',
							'label' => __('Space', CHILD_TEXT_DOMAIN),
							'name' => 'space',
							'type' => 'number',
					),
					array (
							'key' => 'living_space',
							'label' => __('Living space', CHILD_TEXT_DOMAIN),
							'name' => 'living_space',
							'type' => 'number',
					),
					array (
						'key' => 'price',
						'label' => __('Price', CHILD_TEXT_DOMAIN),
						'name' => 'price',
						'type' => 'number',
					),
					array (
						'key' => 'address',
						'label' => __('Address', CHILD_TEXT_DOMAIN),
						'name' => 'address',
						'type' => 'text',
					),
					array (
							'key' => 'floor',
							'label' => __('Floor', CHILD_TEXT_DOMAIN),
							'name' => 'floor',
							'type' => 'number',
					),
			),
			'location' => array (
					array (
							array (
									'param' => 'post_type',
									'operator' => '==',
									'value' => 'real_estate',
							),
					),
			),
	));
	
}

add_action('add_meta_boxes', function () {
	add_meta_box( 'agency', __('Real estate agency', CHILD_TEXT_DOMAIN), 'agency_metabox', 'real_estate', 'side', 'low'  );
}, 1);

function agency_metabox( $post ){
    $agencies = unity_child_get_transient_posts('agency_type', true);

	if( $agencies ){
		echo '
		<div style="max-height:200px; overflow-y:auto;">
			<ul>
		';

		foreach( $agencies as $agency ){
			echo '
				<li>
					<label>
							<input 
									type="radio" 
									name="post_parent" 
									value="'. $agency->ID .'" '. 
									checked($agency->ID, $post->post_parent, 0) .
							'>'. 
							esc_html($agency->post_title) .
					'</label>
				</li>
			';
		}

		echo '
			</ul>
		</div>';
	}
	else
		echo __('Agencies not found...', CHILD_TEXT_DOMAIN);
}

add_action( 'save_post', 'unity_child_save_cache_post', 10, 3 );
function unity_child_save_cache_post( $post_id, $post, $update ){
    $post_types = ['agency_type'];

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return;

    if( ! current_user_can( 'edit_post', $post_id ) )
        return;

    foreach( $post_types as $post_type ) {
        if($post->post_type === $post_type) {
            $posts = get_posts(array( 'post_type'=>$post_type, 'posts_per_page'=>-1, 'orderby'=>'post_title', 'order'=>'ASC' ));
            set_transient( 'admin_'.$post_type, $posts, DAY_IN_SECONDS );
        }
    }

    if ( ! isset( $_POST['post_parent'] ) ) {
        $data = sanitize_text_field( $_POST['post_parent'] );
        update_post_meta( $post_id, 'agency', $data );
    }
}

add_action( 'widgets_init', 'unity_child_widget_agencies' );
function unity_child_widget_agencies() {
	register_widget( 'Unity_Child_Widget_Agencies' );
}


function unity_child_get_transient_posts($post_type, $is_admin = false, $args = false, $time = DAY_IN_SECONDS ) {
    $transient_name = $is_admin ? 'admin_'.$post_type : $post_type;
    $posts = get_transient( $transient_name );
    $posts_args = $args ? $args : array(
        'posts_per_page' => -1, 
        'orderby' => 'post_title', 
        'order' => 'ASC' 
    );
    $posts_args['post_type'] = $post_type;
    
    if ($posts === false) {
	    $posts = new WP_Query($posts_args);
			set_transient( $transient_name, $posts, $time );
    }

    return $posts;
}

function unity_child_get_transient_field( $field_name, $post_id, $time = HOUR_IN_SECONDS ) {
    $transient_name = $post_id.'_'.$field_name;
    $value = get_transient( $transient_name );

    if ($value === false) {
        $value = get_field($field_name, $post_id);
        set_transient( $transient_name, $value, $time );
    }

    return $value;
}

// Класс виджета
class Unity_Child_Widget_Agencies extends WP_Widget {

	function __construct() {
		parent::__construct(
			'unity_child_widget_agencies_parent',
			__('Agencies', CHILD_TEXT_DOMAIN),
			array('description' => __('Output agencies to front page', CHILD_TEXT_DOMAIN))
		);
	}

	function widget( $args, $instance ){
		$posts = unity_child_get_transient_posts('agency_type');

		echo $args['before_widget'];

		if( count($posts->posts) ) {
			?>
				<ul>
					<?php foreach($posts->posts as $post) { ?>
						<?php 
							$url = http_build_query(array_merge($_GET, array('agency' => $post->ID)));
						?>

						<li>
							<a href="<?php echo esc_url('?'.$url); ?>"><?php echo esc_html($post->post_title); ?></a>
						</li>
					<?php } ?>
				</ul>
			<?php
		} else {
			?>
				<p><?php echo __('Agencies not found...', CHILD_TEXT_DOMAIN); ?></p>
			<?php
		}

		echo $args['after_widget'];
	}
}