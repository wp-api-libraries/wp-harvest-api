<?php
/**
 * WP Harvest API
 *
 * @link http://help.getharvest.com/api/ API Docs
 * @package WP-API-Libraries\WP-Harvest-API
 */

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Check if class exists. */
if ( ! class_exists( 'HarvestAPI' ) ) {

	/**
	 * Harvest API Class.
	 *
	 * @link http://help.getharvest.com/api/ API Docs
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
		 * @param mixed  $account Account.
		 * @param mixed  $email Email.
		 * @param mixed  $password Password.
		 * @param string $output (default: 'json') Output.
		 * @return void
		 */
		public function __construct( $account, $email, $password, $output = 'json' ) {

			static::$account = $account;
			static::$output = $account;
			$this->base_uri = 'https://' . $account . '.harvestapp.com';

			$secret_key = base64_encode( $email . ':' . $password );

			$this->args['headers'] = array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'Authorization' => 'Basic ' . $secret_key,
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

			$code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'wp-harvest-api' ), $code ) );
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
		 * Get Rate Limit Status.
		 *
		 * @access public
		 * @return void
		 */
		public function get_rate_limit_status() {

			$request = $this->base_uri . '/account/rate_limit_status?output=' . static::$output;
			return $this->fetch( $request );
		}


		/* CLIENTS. */

		/**
		 * Get all Clients
		 *
		 * @access public
		 * @return void
		 */
		public function get_all_clients() {
			$request = $this->base_uri . '/clients?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get Client.
		 *
		 * @access public
		 * @param mixed $client_id Client ID.
		 * @return void
		 */
		public function get_client( $client_id ) {
			$request = $this->base_uri . '/clients/' . $client_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Create Client.
		 *
		 * @access public
		 * @param mixed $name Name.
		 * @param mixed $currency Currency.
		 * @param mixed $curreny_symbol Currency Symbol.
		 * @param mixed $active Active.
		 * @param mixed $details Details.
		 * @param mixed $highrise_id HighRise ID.
		 * @return void
		 */
		public function create_client( $name, $currency, $curreny_symbol, $active, $details, $highrise_id ) {

			$request = $this->base_uri . '/clients?name=' . $name . '&active=' . $active . '&currency=' . $currency . '&currency_symbol=' . $curreny_symbol . '&detail=' . $details . '&highrise_id=' . $highrise_id . 'output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Update Client.
		 *
		 * @access public
		 * @param mixed $name Name.
		 * @param mixed $currency Currency.
		 * @param mixed $curreny_symbol Currency Symbol.
		 * @param mixed $active Active.
		 * @param mixed $details Details.
		 * @param mixed $highrise_id HighRise ID.
		 * @return void
		 */
		public function update_client( $name, $currency, $curreny_symbol, $active, $details, $highrise_id ) {

			$request = $this->base_uri . '/clients?name=' . $name . '&active=' . $active . '&currency=' . $currency . '&currency_symbol=' . $curreny_symbol . '&detail=' . $details . '&highrise_id=' . $highrise_id . 'output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Toggle Client.
		 *
		 * @access public
		 * @param mixed $client_id
		 * @return void
		 */
		public function toggle_client( $client_id ) {

			$request = $this->base_uri . '/clients/' . $client_id . '/toggle?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Delete Client.
		 *
		 * @access public
		 * @param mixed $client_id Client ID.
		 * @return void
		 */
		public function delete_client( $client_id ) {
			  $request = $this->base_uri . '/clients/' . $client_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get All Contacts.
		 *
		 * @access public
		 * @param string $updated_since (default: '') Updated Since.
		 * @return void
		 */
		public function get_all_contacts( $updated_since = '' ) {

			$request = $this->base_uri . '/contacts?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get All Contacts For A Client.
		 *
		 * @access public
		 * @param mixed  $client_id Client ID.
		 * @param string $updated_since (default: '') Updated Since.
		 * @return void
		 */
		public function get_all_client_contacts( $client_id, $updated_since = '' ) {

			  $request = $this->base_uri . '/clients/contacts?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get Client Contact.
		 *
		 * @access public
		 * @param mixed $contact_id Contact ID.
		 * @return void
		 */
		public function get_client_contact( $contact_id ) {

			$request = $this->base_uri . '/contacts/' . $contact_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Add Contact.
		 *
		 * @access public
		 * @param mixed $client_id Client ID.
		 * @param mixed $first_name First Name.
		 * @param mixed $last_name Last Name.
		 * @return void
		 */
		public function add_contact( $client_id, $first_name, $last_name ) {

			$request = $this->base_uri . '/contacts/' . $contact_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Update Contact.
		 *
		 * @access public
		 * @param mixed $client_id Client ID.
		 * @param mixed $first_name First Name.
		 * @param mixed $last_name Last Name.
		 * @return void
		 */
		public function update_contact( $client_id, $first_name, $last_name ) {

			$request = $this->base_uri . '/contacts/' . $contact_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Delete Contact.
		 *
		 * @access public
		 * @param mixed $contact_id Contact ID.
		 * @return void
		 */
		public function delete_contact( $contact_id ) {

			$request = $this->base_uri . '/contacts/' . $contact_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}

		/* INVOICES & ESTIMATES. */

		/**
		 * Add Invoice.
		 *
		 * @access public
		 * @param mixed $due_at_human_format Due at Human Format.
		 * @param mixed $client_id Client ID.
		 * @param mixed $currency Currency.
		 * @param mixed $discount Discount.
		 * @param mixed $issued_at Issued At.
		 * @param mixed $subject Subject.
		 * @param mixed $notes Notes.
		 * @param mixed $number Number.
		 * @param mixed $kind Kind.
		 * @param mixed $projects_to_invoice Projects to Invoice.
		 * @param mixed $import_hours Import Hours.
		 * @param mixed $import_expenses Import Expenses.
		 * @param mixed $expense_summary_type Expense Summary Type.
		 * @param mixed $period_start Period Start.
		 * @param mixed $period_end Period End.
		 * @param mixed $expense_period_start Expense Period Start.
		 * @param mixed $expense_period_end Expense Period End.
		 * @param mixed $csv_line_items CSV Line Items.
		 * @param mixed $tax Tax.
		 * @param mixed $tax2 Tax 2.
		 * @param mixed $purchase_order Purchase Order.
		 * @return void
		 */
		public function add_invoice( $due_at_human_format, $client_id, $currency, $discount, $issued_at, $subject, $notes, $number, $kind, $projects_to_invoice, $import_hours, $import_expenses, $expense_summary_type, $period_start, $period_end, $expense_period_start, $expense_period_end, $csv_line_items, $tax, $tax2, $purchase_order ) {

			$request = $this->base_uri . '/invoices?output=' . static::$output;
			return $this->fetch( $request );

		}

		/* EXPENSES. */

		/**
		 * Get Expense.
		 *
		 * @access public
		 * @param mixed  $expense_id Expense ID.
		 * @param string $of_user (default: '') Of User.
		 * @return void
		 */
		public function get_expense( $expense_id, $of_user = '' ) {
			$request = $this->base_uri . '/expenses?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Delete Expense.
		 *
		 * @access public
		 * @param mixed $expense_id Expense ID.
		 * @return void
		 */
		public function delete_expense( $expense_id ) {
			$request = $this->base_uri . '/expenses/' . $expense_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Attach Receipt Image to Expense.
		 *
		 * @access public
		 * @param mixed $expense_id Expense ID.
		 * @return void
		 */
		public function attach_receipt_image_to_expense( $expense_id ) {
			$request = $this->base_uri . '/expenses/' . $expense_id . '/receipt?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get Receipt Image from Expense.
		 *
		 * @access public
		 * @param mixed $expense_id Expense ID.
		 * @return void
		 */
		public function get_receipt_image_from_expense( $expense_id ) {
			$request = $this->base_uri . '/expenses/' . $expense_id . '/receipt?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Add Expense.
		 *
		 * @access public
		 * @param mixed $notes Notes.
		 * @param mixed $total_cost Total Cost.
		 * @param mixed $project_id Project ID.
		 * @param mixed $expense_category_id Expense Category ID.
		 * @param mixed $billable Billable.
		 * @param mixed $spent_at Spent At.
		 * @param mixed $units Units.
		 * @return void
		 */
		public function add_expense( $notes, $total_cost, $project_id, $expense_category_id, $billable, $spent_at, $units ) {

		}

		/**
		 * Update Expense.
		 *
		 * @access public
		 * @param mixed $notes Notes.
		 * @param mixed $total_cost Total Cost.
		 * @param mixed $project_id Project ID.
		 * @param mixed $expense_category_id Expense Category ID.
		 * @param mixed $billable Billable.
		 * @param mixed $spent_at Spent At.
		 * @param mixed $units Units.
		 * @return void
		 */
		public function update_expense( $notes, $total_cost, $project_id, $expense_category_id, $billable, $spent_at, $units ) {

		}

		/* TIMESHEETS. */

		/**
		 * Add Time Entry.
		 *
		 * @access public
		 * @param mixed      $hours Hours.
		 * @param mixed      $notes Notes.
		 * @param mixed      $project_id Project ID.
		 * @param mixed      $task_id Task ID.
		 * @param mixed      $spent_at Spent At.
		 * @param mixed      $client_id Client ID.
		 * @param mixed      $user_id User ID.
		 * @param mixed      $is_billed Is Billed.
		 * @param mixed      $is_closed Is closed.
		 * @param mixed      $timer_started_at Timer Started At.
		 * @param mixed      $hours_with_timer Hours with Timer.
		 * @param mixed      $hours_without_timer Hours without Timer.
		 * @param mixed      $started_at Started At.
		 * @param mixed      $ended_at Ended At.
		 * @param mixed      $updated_at Updated At.
		 * @param $created_at $of_user Of User.
		 * @return void
		 */
		public function add_time_entry( $project_id, $task_id, $hours = null, $notes, $spent_at, $started_at = null, $ended_at = null, $client_id = null, $user_id = null, $is_billed = null, $is_closed = null, $timer_started_at = null, $hours_with_timer = null, $hours_without_timer = null, $updated_at = null, $of_user = null  ) {

			$request = $this->base_uri . '/daily/add?project_id=' . $project_id . '&task_id=' . $task_id . '&hours=' . $hours . '&notes=' . $notes . '&spent_at=' . $spent_at;

			return $this->fetch( $request );
		}

		/**
		 * Toggle Timer.
		 *
		 * @access public
		 * @param mixed $day_entry_id Day Entry ID.
		 * @param mixed $of_user Of User.
		 * @return void
		 */
		public function toggle_timer( $day_entry_id, $of_user ) {

		}

		/**
		 * Delete Time Entry.
		 *
		 * @access public
		 * @param mixed $day_entry_id Day Entry ID.
		 * @param mixed $of_user Of User.
		 * @return void
		 */
		public function delete_time_entry( $day_entry_id, $of_user ) {

		}

		/**
		 * Update Time Entry.
		 *
		 * @access public
		 * @param mixed $day_entry_id Day Entry ID.
		 * @param mixed $of_user Of User.
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
			$request = $this->base_uri . '/projects?client=' . $client_id . '&updated_since=' . $updated_since . '&output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Get a Project.
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
		 * Create Project.
		 *
		 * @access public
		 * @param mixed $name Name.
		 * @param mixed $active Active.
		 * @param mixed $bill_by Bill By.
		 * @param mixed $client_id Client ID.
		 * @return void
		 */
		public function create_project( $name, $active, $bill_by, $client_id ) {
			$request = $this->base_uri . '/projects?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Update Project.
		 *
		 * @access public
		 * @param mixed $project_id Project ID.
		 * @return void
		 */
		public function update_project( $project_id ) {
			$request = $this->base_uri . '/projects/' . $project_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Toggle Project.
		 *
		 * @access public
		 * @param mixed $project_id Project ID.
		 * @return void
		 */
		public function toggle_project( $project_id ) {
			$request = $this->base_uri . '/projects/' . $project_id . '/toggle?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Delete Project.
		 *
		 * @access public
		 * @param mixed $project_id Project ID.
		 * @return void
		 */
		public function delete_project( $project_id ) {
			$request = $this->base_uri . '/projects/' . $project_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/* REPORTS. */

		/**
		 * Get Entries for project timeline.
		 *
		 * @access public
		 * @param mixed $project_id Project ID.
		 * @param mixed $tf_start_date Time Frame Start Date.
		 * @param mixed $tf_end_date Time Frame End Date.
		 * @return void
		 */
		public function get_entries_for_project_timeframe( $project_id, $tf_start_date, $tf_end_date ) {

		}


		/**
		 * Get Entries by user for Timeline.
		 *
		 * @access public
		 * @param mixed $user_id User ID.
		 * @param mixed $billable Billable.
		 * @param mixed $only_billed Only Billed.
		 * @param mixed $only_unbilled Only Unbilled.
		 * @param mixed $is_closed Is Closed.
		 * @param mixed $updated_since Updated Since.
		 * @return void
		 */
		public function get_entries_by_user_for_timeframe( $user_id, $billable, $only_billed, $only_unbilled, $is_closed, $updated_since ) {

		}

		/* TASKS. */

		/**
		 * Get all Tasks.
		 *
		 * @access public
		 * @param string $updated_since (default: '') Updated Since.
		 * @return void
		 */
		public function get_all_tasks( $updated_since = '' ) {
			$request = $this->base_uri . '/tasks/?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Get Task.
		 *
		 * @access public
		 * @param mixed $task_id Task ID.
		 * @return void
		 */
		public function get_task( $task_id ) {
			$request = $this->base_uri . '/tasks/' . $task_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Add Task.
		 *
		 * @access public
		 * @return void
		 */
		public function add_task() {
			$request = $this->base_uri . '/tasks/?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Archive Delete Task.
		 *
		 * @access public
		 * @param mixed $task_id Task ID.
		 * @return void
		 */
		public function archive_delete_task( $task_id ) {
			$request = $this->base_uri . '/tasks/' . $task_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Update Task.
		 *
		 * @access public
		 * @param mixed $task_id Task ID.
		 * @return void
		 */
		public function update_task( $task_id ) {
			$request = $this->base_uri . '/tasks/' . $task_id . '?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Reactivate Task.
		 *
		 * @access public
		 * @param mixed $task_id Task ID.
		 * @return void
		 */
		public function reactivate_task( $task_id ) {

			$request = $this->base_uri . '/tasks/' . $task_id . '/activate?output=' . static::$output;
			return $this->fetch( $request );

		}

		/* USERS. */

		/**
		 * Get All Users.
		 *
		 * @access public
		 * @return void
		 */
		public function get_all_users() {
			$request = $this->base_uri . '/people?output=' . static::$output;
			return $this->fetch( $request );
		}

		/**
		 * Add User.
		 *
		 * @access public
		 * @param mixed  $email Email.
		 * @param mixed  $first_name First Name.
		 * @param mixed  $last_name Last Name.
		 * @param string $is_admin (default: '') Is Admin.
		 * @param mixed  $timezone TimeZone.
		 * @param mixed  $is_contractor Is Contractor.
		 * @param mixed  $telephone Telephone.
		 * @param mixed  $is_active Is Active.
		 * @param mixed  $has_access_to_all_future_projects Has access to all future Projects.
		 * @param mixed  $default_hourly_rate Default Hourly Rate.
		 * @param mixed  $department Department.
		 * @param mixed  $cost_rate Cost Rate.
		 * @return void
		 */
		public function add_user( $email, $first_name, $last_name, $is_admin = '', $timezone, $is_contractor, $telephone, $is_active, $has_access_to_all_future_projects, $default_hourly_rate, $department, $cost_rate ) {

			$request = $this->base_uri . '/people?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Update User.
		 *
		 * @access public
		 * @param mixed  $email Email.
		 * @param mixed  $first_name First Name.
		 * @param mixed  $last_name Last Name.
		 * @param string $is_admin (default: '') Is Admin.
		 * @param mixed  $timezone TimeZone.
		 * @param mixed  $is_contractor Is Contractor.
		 * @param mixed  $telephone Telephone.
		 * @param mixed  $is_active Is Active.
		 * @param mixed  $has_access_to_all_future_projects Has access to all future Projects.
		 * @param mixed  $default_hourly_rate Default Hourly Rate.
		 * @param mixed  $department Department.
		 * @param mixed  $cost_rate Cost Rate.
		 * @return void
		 */
		public function update_user( $email, $first_name, $last_name, $is_admin = '', $timezone, $is_contractor, $telephone, $is_active, $has_access_to_all_future_projects, $default_hourly_rate, $department, $cost_rate ) {

			$request = $this->base_uri . '/people/' . $user_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}


		/**
		 * Delete A User.
		 *
		 * @access public
		 * @param mixed $user_id User ID.
		 * @return void
		 */
		public function delete_user( $user_id ) {

			$request = $this->base_uri . '/people/' . $user_id . '?output=' . static::$output;
			return $this->fetch( $request );

		}

		/**
		 * Toggle An Existing User.
		 *
		 * @access public
		 * @param mixed $user_id User ID.
		 * @return void
		 */
		public function toggle_user( $user_id ) {

			$request = $this->base_uri . '/people/' . $user_id . '/toggle?output=' . static::$output;
			return $this->fetch( $request );

		}

	}
}
