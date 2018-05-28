<?php

/**
 * The plugin  file
 * @link              exposyour.com
 * @since             1.0.0
 * @package           Wpgraphsocial
 *
 * @wordpress-plugin
 * Plugin Name:       wpgraphsocial
 * Plugin URI:        exposyour.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            dd
 * Author URI:        exposyour.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpgraphsocial
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/* function for registering case studies */


function register_casetudy()
	
	{

	
			$args = array(
				'public'                => true,
				'label'                 => __( 'Case Studies', 'textdomain' ),
				'supports'     => array( 'title', 'editor' ),
			   'taxonomies'            => array( 'category', 'post_tag' ),
				'show_in_graphql'       => true,
				'graphql_name'          => 'CaseStudy',
				'graphql_single_name'   => 'CaseStudy',
				'graphql_plural_name'   => 'CaseStudies',
				'graphql_singular_type' => 'CaseStudy',
				'graphql_plural_type'   => 'CaseStudies','exclude_from_search'   => false,
						'publicly_queryable'    => true,
						'capability_type'       => 'page',
						 'show_in_rest'          => true
			);
		
			register_post_type( 'casestudy', $args );
		
		
		

	}
	add_action( 'init', 'register_casetudy' );

/*  adding extra profile links *///

	add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="twitter"><?php _e("Twitter"); ?></label></th>
        <td>
            <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your twitter Username."); ?></span>
        </td>
    </tr>
    <tr>
        <th><label for="facebook"><?php _e("Facebook"); ?></label></th>
        <td>
            <input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your Facebook username."); ?></span>
        </td>
    </tr>
    <tr>
    <th><label for="instagram"><?php _e("instagram "); ?></label></th>
        <td>
            <input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_the_author_meta( 'instagram', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your instagram ."); ?></span>
        </td>
    </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );


/** saving profile fields in database  */

function save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }


    $socialLinks=array("twitter" => $_POST['twitter'] , "facebook" => $_POST['facebook'],"instagram" => $_POST['instagram']);

  
    update_user_meta(
        $user_id,
        'social_links',
        $socialLinks
    );
}


	add_action( 'graphql_init', function() {
		//Bind with wpgraph QL init code
	 
	 flush_rewrite_rules(); 


	add_filter( 'graphql_user_fields', function( $fields ) {

		$fields['socialLinks'] = [
			'type' => new \WPGraphQL\Type\WPObjectType([
				'name' => 'UserProfileSocialLinks',
				'fields' => [
					'twitter' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Twitter url for the user', 'your-textdomain' )
					],
					'facebook' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Facebook url for the user', 'your-textdomain' )
					],
					'instagram' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Instagram url for the user', 'your-textdomain' )
					],
				],
			]),
			'description' => __( 'Social links for the user', 'your-textdomain' ),
			'resolve' => function( \WP_User $user, $args, $context, $info ) {
	
				$social_links = get_user_meta( $user->ID, 'social_links', true );
				return ! empty( $social_links ) && is_array( $social_links ) ? $social_links : null;
	
			},
		];
	
		return $fields;
	
	} );
	
	//expose input fields for the User Input to mutate the user
	
	function graphql_user_social_links_input( $fields ) {
	
		$fields['socialLinks'] = [
			'type' => new \WPGraphQL\Type\WPInputObjectType([
				'name' => 'UserProfileSocialLinksInput',
				'fields' => [
					'twitter' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Twitter url for the user', 'your-textdomain' )
					],
					'facebook' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Facebook url for the user', 'your-textdomain' )
					],
					'instagram' => [
						'type' => \WPGraphQL\Types::string(),
						'description' => __( 'Instagram url for the user', 'your-textdomain' )
					],
				],
			]),
			'description' => __( 'Social links for the user', 'your-textdomain' ),
		];
	
		return $fields;
	
	}
	
	add_filter( 'graphql_user_mutation_input_fields', 'graphql_user_social_links_input', 10, 1 );
	
	
	add_action( 'graphql_user_object_mutation_update_additional_data', function( $user_id, $input, $mutation_name, $context, $info ) {
	
		$social_link_input = ! empty( $input['socialLinks'] ) ? $input['socialLinks'] : [];
	
		if ( empty( $social_link_input ) ) {
			return;
		}
	
		$social_links = get_user_meta( $user_id, 'social_links', true );
		$social_links = ! empty( $social_links ) && is_array( $social_links ) ? $social_links : [];
	
		if ( ! empty( $social_link_input['twitter'] ) ) {
			$social_links['twitter'] = sanitize_text_field( $social_link_input['twitter'] );
		}
	
		if ( ! empty( $social_link_input['facebook'] ) ) {
			$social_links['facebook'] = sanitize_text_field( $social_link_input['facebook'] );
		}
	
		if ( ! empty( $social_link_input['instagram'] ) ) {
			$social_links['instagram'] = sanitize_text_field( $social_link_input['instagram'] );
		}
	
		update_user_meta( $user_id, 'social_links', $social_links );
	
	}, 10, 5 );
	
	} );


