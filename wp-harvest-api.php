<?php
/**
* WP Harvest API (http://help.getharvest.com/api/)
*
* @package WP-Harvest-API
*/

/*
* Plugin Name: WP Harvest API
* Plugin URI: https://github.com/wp-api-libraries/wp-harvest-api
* Description: Perform API requests to Harvest in WordPress.
* Author: WP API Libraries
* Version: 1.0.0
* Author URI: https://wp-api-libraries.com
* GitHub Plugin URI: https://github.com/wp-api-libraries/wp-harvest-api
* GitHub Branch: master
*/

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Check if class exists. */
if( ! class_exists( 'HarvestAPI' ) ) {

	/**
	* Harvest API Class.
 	*/
 	class HarvestAPI {

   	/**
		 * API Key
		 *
		 * @var string
		 */
		static private $api_key;


		/**
		 * HTTP request arguments.
		 *
		 * (default value: array())
		 *
		 * @var array
		 * @access protected
		 */
		private $args = array();

		/**
		 * Account
		 *
		 * @var string
		 */
		static private $account;


		/**
		 * BaseAPI Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		protected $base_uri = 'https://'. $account .'.harvestapp.com';

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct( $api_key, $account ) {

			static::$api_key = $api_key;
			static::$account = $account;

			$this->args['headers'] = array(
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
			);

		}

		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @param mixed $request Request URL.
		 * @return $body Body.
		 */
		private function fetch( $request ) {

			$response = wp_remote_get( $request );
			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'text-domain' ), $code ) );
			}

			$body = wp_remote_retrieve_body( $response );

			return json_decode( $body );

		}

    /* CLIENTS. */

   	public function get_all_clients() {

   	}

   	public function get_client() {

   	}

   	public function create_client() {

   	}

   	public function update_client() {

   	}

   	public function delete_client() {

   	}

   	public function get_all_contacts() {

   	}

   	public function get_all_contacts_for_client() {

   	}

   	/* PROJECTS. */

   	/**
   	 * Get All Projects.
   	 *
   	 * @access public
   	 * @return void
   	 */
   	public function get_projects( $client_id = '', $updated_since = '' ) {

     	$request = $this->base_uri . '/projects';

			return $this->fetch( $request );

   	}

   	/**
   	 * Get a Project
   	 *
   	 * @access public
   	 * @param mixed $project_id Project ID.
   	 * @return void
   	 */
   	public function get_project( $project_id ) {

     	$request = $this->base_uri . '/projects/' . $project_id;

			return $this->fetch( $request );

   	}

   	public function create_project( $name, $active, $bill_by, $client_id ) {

   	}

    public function update_project() {

    }

    public function deactivate_project() {

    }

    public function delete_project() {

    }

	}

}
