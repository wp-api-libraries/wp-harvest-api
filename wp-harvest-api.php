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
if ( ! class_exists( 'HarvestAPI' ) ) {

	/**
	 * Harvest API Class.
	 */
	class HarvestAPI {

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
		 * Output.
		 *
		 * @var string
		 */
		static private $output;

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
		protected $base_uri;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct(  $account, $email, $password, $output = 'json' ) {

			static::$account = $account;
			static::$output = $account;
			$this->base_uri = 'https://'.$account . '.harvestapp.com';

			$secret_key = base64_encode($email.':'.$password);

			$this->args['headers'] = array(
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic '.$secret_key,
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

			$response = wp_remote_request( $request, $this->args );

			$code = wp_remote_retrieve_response_code($response );
			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'text-domain' ), $code ) );
			}
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body );
		}

		/* ACCOUNT. */

		/**
		 * Who Am I?
		 *
		 * @access public
		 * @return void
		 */
		public function who_am_i() {
			$request = $this->base_uri . '/account/who_am_i?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * get_rate_limit_status function.
		 *
		 * @access public
		 * @return void
		 */
		public function get_rate_limit_status() {

			// /account/rate_limit_status
		}


		/* CLIENTS. */

		/**
		 * get_all_clients function.
		 *
		 * @access public
		 * @return void
		 */
		public function get_all_clients() {
			$request = $this->base_uri . '/clients?output='. static::$output;
			return $this->fetch( $request );
		}

		/**
		 * get_client function.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @return void
		 */
		public function get_client( $client_id ) {
			$request = $this->base_uri . '/clients/'.$client_id.'?output='. static::$output;
			return $this->fetch( $request );
		}

		/**
		 * create_client function.
		 *
		 * @access public
		 * @param mixed $name
		 * @param mixed $currency
		 * @param mixed $curreny_symbol
		 * @param mixed $active
		 * @param mixed $details
		 * @param mixed $highrise_id
		 * @return void
		 */
		public function create_client( $name, $currency, $curreny_symbol, $active, $details, $highrise_id ) {
		}

		/**
		 * update_client function.
		 *
		 * @access public
		 * @param mixed $name
		 * @param mixed $currency
		 * @param mixed $curreny_symbol
		 * @param mixed $active
		 * @param mixed $details
		 * @param mixed $highrise_id
		 * @return void
		 */
		public function update_client( $name, $currency, $curreny_symbol, $active, $details, $highrise_id ) {
		}

		/**
		 * toggle_client function.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @return void
		 */
		public function toggle_client( $client_id ) {

		}

		/**
		 * delete_client function.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @return void
		 */
		public function delete_client( $client_id ) {
		}

		/**
		 * Get All Contacts.
		 *
		 * @access public
		 * @param string $updated_since (default: '')
		 * @return void
		 */
		public function get_all_contacts( $updated_since = '' ) {
		}

		/**
		 * Get All Contacts For A Client.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @param string $updated_since (default: '')
		 * @return void
		 */
		public function get_all_client_contacts( $client_id, $updated_since = '' ) {
		}

		/**
		 * get_client_contact function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @return void
		 */
		public function get_client_contact( $contact_id ) {

		}

		/**
		 * add_contact function.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @param mixed $first_name
		 * @param mixed $last_name
		 * @return void
		 */
		public function add_contact( $client_id, $first_name, $last_name ) {

		}

		/**
		 * update_contact function.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @param mixed $first_name
		 * @param mixed $last_name
		 * @return void
		 */
		public function update_contact( $client_id, $first_name, $last_name ) {

		}

		/**
		 * delete_contact function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @return void
		 */
		public function delete_contact( $contact_id ) {

		}

		/* INVOICES & ESTIMATES. */

		/**
		 * add_invoice function.
		 *
		 * @access public
		 * @param mixed $due_at_human_format
		 * @param mixed $client_id
		 * @param mixed $currency
		 * @param mixed $discount
		 * @param mixed $issued_at
		 * @param mixed $subject
		 * @param mixed $notes
		 * @param mixed $number
		 * @param mixed $kind
		 * @param mixed $projects_to_invoice
		 * @param mixed $import_hours
		 * @param mixed $import_expenses
		 * @param mixed $expense_summary_type
		 * @param mixed $period_start
		 * @param mixed $period_end
		 * @param mixed $expense_period_start
		 * @param mixed $expense_period_end
		 * @param mixed $csv_line_items
		 * @param mixed $tax
		 * @param mixed $tax2
		 * @param mixed $purchase_order
		 * @return void
		 */
		public function add_invoice( $due_at_human_format, $client_id, $currency, $discount, $issued_at, $subject, $notes, $number, $kind, $projects_to_invoice, $import_hours, $import_expenses, $expense_summary_type, $period_start, $period_end, $expense_period_start, $expense_period_end, $csv_line_items, $tax, $tax2, $purchase_order ) {

		}

		/* EXPENSES. */

		/**
		 * get_expense function.
		 *
		 * @access public
		 * @param mixed $expense_id
		 * @param string $of_user (default: '')
		 * @return void
		 */
		public function get_expense( $expense_id, $of_user = '' ) {

		}

		/**
		 * delete_expense function.
		 *
		 * @access public
		 * @param mixed $expense_id
		 * @return void
		 */
		public function delete_expense( $expense_id ) {

		}

		/**
		 * attach_receipt_image_to_expense function.
		 *
		 * @access public
		 * @param mixed $expense_id
		 * @return void
		 */
		public function attach_receipt_image_to_expense( $expense_id ) {

		}

		/**
		 * get_receipt_image_from_expense function.
		 *
		 * @access public
		 * @param mixed $expense_id
		 * @return void
		 */
		public function get_receipt_image_from_expense( $expense_id ) {

		}

		/**
		 * add_expense function.
		 *
		 * @access public
		 * @param mixed $notes
		 * @param mixed $total_cost
		 * @param mixed $project_id
		 * @param mixed $expense_category_id
		 * @param mixed $billable
		 * @param mixed $spent_at
		 * @param mixed $units
		 * @return void
		 */
		public function add_expense( $notes, $total_cost, $project_id, $expense_category_id, $billable, $spent_at, $units ) {

		}

		/**
		 * update_expense function.
		 *
		 * @access public
		 * @param mixed $notes
		 * @param mixed $total_cost
		 * @param mixed $project_id
		 * @param mixed $expense_category_id
		 * @param mixed $billable
		 * @param mixed $spent_at
		 * @param mixed $units
		 * @return void
		 */
		public function update_expense( $notes, $total_cost, $project_id, $expense_category_id, $billable, $spent_at, $units ) {

		}

		/* TIMESHEETS. */



		/**
		 * add_time_entry function.
		 *
		 * @access public
		 * @param mixed $hours
		 * @param mixed $notes
		 * @param mixed $project_id
		 * @param mixed $task_id
		 * @param mixed $spent_at
		 * @param mixed $client_id
		 * @param mixed $user_id
		 * @param mixed $is_billed
		 * @param mixed $is_closed
		 * @param mixed $timer_started_at
		 * @param mixed $hours_with_timer
		 * @param mixed $hours_without_timer
		 * @param mixed $started_at
		 * @param mixed $ended_at
		 * @param mixed $updated_at
		 * @param $created_at $of_user
		 * @return void
		 */
		public function add_time_entry( $project_id, $task_id , $hours = null, $notes, $spent_at, $started_at = null, $ended_at = null, $client_id = null, $user_id = null, $is_billed = null, $is_closed = null, $timer_started_at = null, $hours_with_timer = null, $hours_without_timer = null, $updated_at = null, $of_user = null  ) {

			$request = $this->base_uri . '/daily/add?project_id=' . $project_id . '&task_id='. $task_id .'&hours='.$hours.'&notes='.$notes.'&spent_at='.$spent_at;

			return $this->fetch( $request );
		}

		/**
		 * toggle_timer function.
		 *
		 * @access public
		 * @param mixed $day_entry_id
		 * @param mixed $of_user
		 * @return void
		 */
		public function toggle_timer( $day_entry_id, $of_user ) {

		}

		/**
		 * delete_time_entry function.
		 *
		 * @access public
		 * @param mixed $day_entry_id
		 * @param mixed $of_user
		 * @return void
		 */
		public function delete_time_entry( $day_entry_id, $of_user ) {

		}

		/**
		 * update_time_entry function.
		 *
		 * @access public
		 * @param mixed $day_entry_id
		 * @param mixed $of_user
		 * @return void
		 */
		public function update_time_entry( $day_entry_id, $of_user ) {

		}

		/* PROJECTS. */

		/**
		 * Get All Projects.
		 *
		 * @access public
		 * @return void
		 */
		public function get_projects( $client_id = '', $updated_since = '' ) {
			$request = $this->base_uri . '/projects?client='.$client_id .'&updated_since='.$updated_since.'&output=' . static::$output;
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

		/**
		 * create_project function.
		 *
		 * @access public
		 * @param mixed $name
		 * @param mixed $active
		 * @param mixed $bill_by
		 * @param mixed $client_id
		 * @return void
		 */
		public function create_project( $name, $active, $bill_by, $client_id ) {
		}

		/**
		 * update_project function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @return void
		 */
		public function update_project( $project_id ) {
		}

		/**
		 * toggle_project function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @return void
		 */
		public function toggle_project( $project_id ) {
		}

		/**
		 * delete_project function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @return void
		 */
		public function delete_project( $project_id ) {
		}

		/* REPORTS. */

		/**
		 * get_entries_for_project_timeframe function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param mixed $tf_start_date
		 * @param mixed $tf_end_date
		 * @return void
		 */
		public function get_entries_for_project_timeframe( $project_id, $tf_start_date, $tf_end_date ) {

		}


		/**
		 * get_entries_by_user_for_timeframe function.
		 *
		 * @access public
		 * @param mixed $user_id
		 * @param mixed $billable
		 * @param mixed $only_billed
		 * @param mixed $only_unbilled
		 * @param mixed $is_closed
		 * @param mixed $updated_since
		 * @return void
		 */
		public function get_entries_by_user_for_timeframe( $user_id, $billable, $only_billed, $only_unbilled, $is_closed, $updated_since ) {

		}

		/* TASKS. */

		/**
		 * get_all_tasks function.
		 *
		 * @access public
		 * @param string $updated_since (default: '')
		 * @return void
		 */
		public function get_all_tasks( $updated_since = '' ) {

		}

		/**
		 * get_task function.
		 *
		 * @access public
		 * @param mixed $task_id
		 * @return void
		 */
		public function get_task( $task_id ) {

		}

		/**
		 * add_task function.
		 *
		 * @access public
		 * @return void
		 */
		public function add_task() {

		}

		/**
		 * archive_delete_task function.
		 *
		 * @access public
		 * @param mixed $task_id
		 * @return void
		 */
		public function archive_delete_task( $task_id ) {

		}

		/**
		 * update_task function.
		 *
		 * @access public
		 * @param mixed $task_id
		 * @return void
		 */
		public function update_task( $task_id ) {

		}

		/**
		 * reactivate_task function.
		 *
		 * @access public
		 * @param mixed $task_id
		 * @return void
		 */
		public function reactivate_task( $task_id ) {

		}

		/* USERS. */

		/**
		 * get_all_users function.
		 *
		 * @access public
		 * @return void
		 */
		public function get_all_users() {

		}

		/**
		 * add_user function.
		 *
		 * @access public
		 * @param mixed $email
		 * @param mixed $first_name
		 * @param mixed $last_name
		 * @param string $is_admin (default: '')
		 * @param mixed $timezone
		 * @param mixed $is_contractor
		 * @param mixed $telephone
		 * @param mixed $is_active
		 * @param mixed $has_access_to_all_future_projects
		 * @param mixed $default_hourly_rate
		 * @param mixed $department
		 * @param mixed $cost_rate
		 * @return void
		 */
		public function add_user( $email, $first_name, $last_name, $is_admin = '', $timezone, $is_contractor, $telephone, $is_active, $has_access_to_all_future_projects, $default_hourly_rate, $department, $cost_rate ) {

		}

		/**
		 * update_user function.
		 *
		 * @access public
		 * @param mixed $email
		 * @param mixed $first_name
		 * @param mixed $last_name
		 * @param string $is_admin (default: '')
		 * @param mixed $timezone
		 * @param mixed $is_contractor
		 * @param mixed $telephone
		 * @param mixed $is_active
		 * @param mixed $has_access_to_all_future_projects
		 * @param mixed $default_hourly_rate
		 * @param mixed $department
		 * @param mixed $cost_rate
		 * @return void
		 */
		public function update_user( $email, $first_name, $last_name, $is_admin = '', $timezone, $is_contractor, $telephone, $is_active, $has_access_to_all_future_projects, $default_hourly_rate, $department, $cost_rate ) {

		}

		/**
		 * delete_user function.
		 *
		 * @access public
		 * @return void
		 */
		public function delete_user() {

		}

		/**
		 * toggle_user function.
		 *
		 * @access public
		 * @param mixed $user_id
		 * @return void
		 */
		public function toggle_user( $user_id ) {

		}

	}
}
