<?php
/**
 * WP Harvest API.
 * @link https://help.getharvest.com/api-v2/ API Docs
 * @package WP-API-Libraries\WP-Harvest-API
 */

 /*
 * Plugin Name: WP Harvest API
 * Plugin URI: https://github.com/wp-api-libraries/wp-harvest-api
 * Description: Perform API requests to Harvest in WordPress.
 * Author: WP API Libraries
 * Version: 2.0.0
 * Author URI: https://wp-api-libraries.com
 * GitHub Plugin URI: https://github.com/wp-api-libraries/wp-harvest-api
 * GitHub Branch: master
 * Text Domain: wp-harvest-api
 */

if( ! defined( 'ABSPATH' ) ){
  exit;
}

include_once( 'wp-harvest-base-api.php' );

if( defined( 'HarvestAPI' ) ){
  return;
}

/**
 * HarvestAPI class.
 *
 * ASCII block letters brought to you by http://patorjk.com/software/taag/#p=display&f=Roman&t=Sample%20Text
 */
class HarvestAPI extends WpHarvestBase {

/*
      .o.       ooooooooo.   ooooo       .oooooo..o oooooooooooo ooooooooooooo ooooo     ooo ooooooooo.
     .888.      `888   `Y88. `888'      d8P'    `Y8 `888'     `8 8'   888   `8 `888'     `8' `888   `Y88.
    .8"888.      888   .d88'  888       Y88bo.       888              888       888       8   888   .d88'
   .8' `888.     888ooo88P'   888        `"Y8888o.   888oooo8         888       888       8   888ooo88P'
  .88ooo8888.    888          888            `"Y88b  888    "         888       888       8   888
 .8'     `888.   888          888       oo     .d8P  888       o      888       `88.    .8'   888
o88o     o8888o o888o        o888o      8""88888P'  o888ooooood8     o888o        `YbodP'    o888o
*/

  protected $base_uri = 'https://api.harvestapp.com/v2/';

  protected $access_token;

  protected $account_id;

  protected $user_agent;

  protected $application;

  protected $content_type;

  /**
   * Constructorinatorino 9000
   *
   * @param string $domain   The domain extension of zendesk (basically org name).
   * @param string $username The username through which requests will be made
   *                         under.
   * @param string $api_key  The API key used for authentication.
   * @param bool   $debug    (Default: false) Whether to return calls even if error,
   *                         or to wrap them in a wp_error object.
   */
  public function __construct( $access_token, $account_id, $user_agent, $application = 'hostops', $content_type = 'application/json', $is_debug = false ) {
    $this->access_token = $access_token;
    $this->account_id   = $account_id;
    $this->user_agent   = $user_agent;
    $this->application  = $application;
    $this->is_debug     = $is_debug;
  }

  /**
   * Abstract extended function that is used to set authorization before each
   * call. $this->args['headers'] are wiped after every fetch call, hence this
   * function is necessary.
   *
   * @return void
   */
  protected function set_headers() {
    $this->args['headers'] = array(
      'Authorization'      => 'Bearer ' . $this->access_token,
      'Harvest-Account-Id' => $this->account_id,
      'User-Agent'         => $this->application . ' (' . $this->user_agent . ')',
      'Content-Type'       => 'application/json'
    );
  }

  /**
   * Clear arguments.
   *
   * Extended just in case you don't want to wipe everything.
   *
   * Recommended at least clearing body.
   *
   * @return void
   */
  protected function clear() {
    $this->args = array();
  }

  private function run( $route, $args = array(), $method = 'GET' ){
    return $this->build_request( $route, $args, $method )->fetch();
  }

  private function parse_args( $args, $merge = array() ){
    $results = array();

    foreach( $args as $key => $val ){
      if( $val !== null ){
        $results[$key] = $val;
      }else if( is_array( $val ) && ! empty( $val ) ){
        $results[$key] = $val;
      }
    }

    return array_merge( $merge, $results );
  }

/*
  ooooooooo.                         o8o                            .    o8o
  `888   `Y88.                       `"'                          .o8    `"'
   888   .d88'  .oooo.    .oooooooo oooo  ooo. .oo.    .oooo.   .o888oo oooo   .ooooo.  ooo. .oo.
   888ooo88P'  `P  )88b  888' `88b  `888  `888P"Y88b  `P  )88b    888   `888  d88' `88b `888P"Y88b
   888          .oP"888  888   888   888   888   888   .oP"888    888    888  888   888  888   888
   888         d8(  888  `88bod8P'   888   888   888  d8(  888    888 .  888  888   888  888   888
  o888o        `Y888""8o `8oooooo.  o888o o888o o888o `Y888""8o   "888" o888o `Y8bod8P' o888o o888o
                         d"     YD
                         "Y88888P'
*/

  /**
   * Function for setting pagination prior to a call (should be a GET only!).
   *
   * Example usage:
   *
   *    $hapi = new HarvestAPI( ... );
   *    $results = $hapi->p( 2, 30, '2018-01-22T14:48:31' )->get_tasks();
   *
   *    // Alternatively
   *    $hapi->set_pagination( 2, 30, '2018-01-22T14:48:31');
   *    $results = $hapi->get_tasks();
   *
   * p() is a wrapper function for set_pagination.
   *
   * TODO: move updated_since to here (since it appears in all other cases as well).
   *
   * @param integer $page          (Default: 1) Page offset to get results from
   *                               (multiplied by $per_page is the final page).
   * @param integer $per_page      (Default: 100) Number of results to display per page.
   * @param string  $updated_since (Default: null) Only retrieve results that have
   *                               been updated after this date.
   * @return HarvestAPI            $this.
   */
  public function set_pagination( $page = 1, $per_page = 100, $updated_since = null ){
    $this->args['body'] = $this->parse_args(array(
      'page'          => $page,
      'per_page'      => $per_page,
      'updated_since' => $updated_since
    ));

    return $this;
  }

  /**
   * Wrapper function for set_pagination().
   *
   * @param integer $page     (Default: 1) Page offset to get results from (multiplied
   *                          by $per_page is the final page).
   * @param integer $per_page (Default: 100) Number of results to display per page.
   * @return HarvestAPI       $this.           [description]
   */
  public function p( $page = 1, $per_page = 100 ){
    return $this->set_pagination( $page, $per_page );
  }


/*
  .oooooo.   oooo   o8o                            .
 d8P'  `Y8b  `888   `"'                          .o8
888           888  oooo   .ooooo.  ooo. .oo.   .o888oo  .oooo.o
888           888  `888  d88' `88b `888P"Y88b    888   d88(  "8
888           888   888  888ooo888  888   888    888   `"Y88b.
`88b    ooo   888   888  888    .o  888   888    888 . o.  )88b
 `Y8bood8P'  o888o o888o `Y8bod8P' o888o o888o   "888" 8""888P'
*/

  /**
   * Returns a list of your clients. The clients are returned sorted by creation date,
   * with the most recently created clients appearing first.
   *
   * The response contains an object with a clients property that contains an array
   * of up to per_page clients. Each entry in the array is a separate client object.
   * If no more clients are available, the resulting array will be empty. Several
   * additional pagination properties are included in the response to simplify paginating
   * your clients.
   *
   * Can be paginated.
   *
   * @param  boolean $is_active     (Default: null) Pass true to only return active clients
   *                                and false to return inactive clients.
   * @return object                 A list of clients, plug pagination properties.
   */
  public function list_clients( $is_active = null ){
    $args = $this->parse_args( array(
      'is_active' => $is_active
    ));

    return $this->run( 'clients', $args );
  }

  /**
   * Retrieves the client with the given ID. Returns a client object and a 200 OK
   * response code if a valid identifier was provided.
   *
   * @param  int $client_id The client's ID.
   * @return object         The client.
   */
  public function retrieve_client( $client_id ){
    return $this->run( "clients/$client_id" );
  }

  /**
   * Creates a new client object. Returns a client object and a 201 Created response
   * code if the call succeeded.
   *
   * @param  string  $name      A textual description of the client.
   * @param  boolean $is_active (Default: null) Whether the client is active, or
   *                            archived. Defaults to true.
   * @param  string  $address   (Default: null) A textual representation of the client’s
   *                            physical address. May include new line characters.
   * @param  string  $currency  (Default: null) The currency used by the client. If
   *                            not provided, the company’s currency will be used.
   *                            <a href="https://help.getharvest.com/api-v2/introduction/overview/supported-currencies/">
   *                            See a list of supported currencies.</a>
   * @return object             The newly created client.
   */
  public function create_client( $name, $is_active = null, $address = null, $currency = null ){
    $args = $this->parse_args(array(
      'name'      => $name,
      'is_active' => $is_active,
      'address'   => $address,
      'currency'  => $currency
    ));

    return $this->run( 'clients', $args, 'POST' );
  }

  /**
   * Updates the specific client by setting the values of the parameters passed.
   * Any parameters not provided will be left unchanged. Returns a client object
   * and a 200 OK response code if the call succeeded.
   *
   * @param  int    $client_id The client's ID.
   * @param  array  $args      Array of optional arguments to update:
   *                             name      => A textual description of the client.
   *                             is_active => Whether the client is active, or archived.
   *                             address   => A textual representation of the client’s
   *                                          physical address. May include new line characters.
   *                             currency  => The currency used by the client. If not provided,
   *                                          the company’s currency will be used. See a
   *                                          list of supported currencies
   * @return object            The updated client (if successful).
   */
  public function update_client( $client_id, $args ){
    return $this->run( "clients/$client_id", $args, 'PATCH' );
  }

  /**
   * Delete a client. Deleting a client is only possible if it has no projects or invoices
   * associated with it. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int $client_id The client ID.
   * @return object         200 if successful.
   */
  public function delete_client( $client_id ){
    return $this->run( "clients/$client_id", array(), 'DELETE' );
  }

/*
  .oooooo.   oooo   o8o                            .          .oooooo.                             .                           .
 d8P'  `Y8b  `888   `"'                          .o8         d8P'  `Y8b                          .o8                         .o8
888           888  oooo   .ooooo.  ooo. .oo.   .o888oo      888           .ooooo.  ooo. .oo.   .o888oo  .oooo.    .ooooo.  .o888oo  .oooo.o
888           888  `888  d88' `88b `888P"Y88b    888        888          d88' `88b `888P"Y88b    888   `P  )88b  d88' `"Y8   888   d88(  "8
888           888   888  888ooo888  888   888    888        888          888   888  888   888    888    .oP"888  888         888   `"Y88b.
`88b    ooo   888   888  888    .o  888   888    888 .      `88b    ooo  888   888  888   888    888 . d8(  888  888   .o8   888 . o.  )88b
 `Y8bood8P'  o888o o888o `Y8bod8P' o888o o888o   "888"       `Y8bood8P'  `Y8bod8P' o888o o888o   "888" `Y888""8o `Y8bod8P'   "888" 8""888P'
*/

  /**
   * Returns a list of your contacts. The contacts are returned sorted by creation date,
   * with the most recently created contacts appearing first.
   *
   * The response contains an object with a contacts property that contains an array
   * of up to per_page contacts. Each entry in the array is a separate contact object.
   * If no more contacts are available, the resulting array will be empty. Several
   * additional pagination properties are included in the response to simplify paginating
   * your contacts.
   *
   * Can be paginated.
   *
   * @param  integer $client_id     (Default: null) Only return contacts belonging to
   *                                the client with the given ID.
   * @return array                  List of contacts, along with pagination properties.
   */
  public function list_contacts( $client_id = null ){
    $args = $this->parse_args( array(
      'client_id' => $client_id
    ));

    return $this->run( 'contacts', $args );
  }

  /**
   * Retrieves the contact with the given ID. Returns a contact object and a 200 OK
   * response code if a valid identifier was provided.
   *
   * @param  integer $contact_id The contact ID.
   * @return object              The contact.
   */
  public function retrieve_contact( $contact_id ){
    return $this->run( "contacts/$contact_id" );
  }

  /**
   * Creates a new contact object. Returns a contact object and a 201 Created response
   * code if the call succeeded.
   *
   * Has various required arguments, along with various optional arguments.
   *
   * @param  int    $client_id  The ID of the contact.
   * @param  string $title      The title of the contact.
   * @param  string $first_name The first name of the contact.
   * @param  array  $args       Other arguments, including (all strings and optional, by key):
   *                            last_name    => The last name of the contact.
   *                            email        => The contact’s email address.
   *                            phone_office => The contact’s office phone number.
   *                            phone_mobile => The contact’s mobile phone number.
   *                            fax          => The contact’s fax number.
   * @return object             201, and the contact if created.
   */
  public function create_contact( $client_id, $title, $first_name, $args = array() ){
    $args = $this->parse_args(array(
      'client_id'  => $client_id,
      'title'      => $title,
      'first_name' => $first_name
    ), $args );

    return $this->run( 'contacts', $args, 'POST' );
  }

  /**
   * Updates the specific contact by setting the values of the parameters passed.
   * Any parameters not provided will be left unchanged. Returns a contact object
   * and a 200 OK response code if the call succeeded.
   *
   * @param  int    $contact_id The ID of the contact.
   * @param  array  $args       Array of optional arguments:
   *                            client_id    => The ID of the client associated with this contact.
   *                            title        => The title of the contact.
   *                            first_name   => The first name of the contact.
   *                            last_name    => The last name of the contact.
   *                            email        => The contact’s email address.
   *                            phone_office => The contact’s office phone number.
   *                            phone_mobile => The contact’s mobile phone number.
   *                            fax          => The contact’s fax number.
   * @return object             201, and the updated contact if successful.
   */
  public function update_contact( $contact_id, $args ){
    return $this->run( "contacts/$contact_id", $args, 'PATCH' );
  }

  /**
   * Delete a contact. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int    $contact_id The ID of the contact.
   * @return object             200 if successful.
   */
  public function delete_contact( $contact_id ){
    return $this->run( "contacts/$contact_id", array(), 'DELETE' );
  }

/*
  .oooooo.                                                                 o8o
 d8P'  `Y8b                                                                `"'
888           .ooooo.  ooo. .oo.  .oo.   oo.ooooo.   .oooo.   ooo. .oo.   oooo   .ooooo.   .oooo.o
888          d88' `88b `888P"Y88bP"Y88b   888' `88b `P  )88b  `888P"Y88b  `888  d88' `88b d88(  "8
888          888   888  888   888   888   888   888  .oP"888   888   888   888  888ooo888 `"Y88b.
`88b    ooo  888   888  888   888   888   888   888 d8(  888   888   888   888  888    .o o.  )88b
 `Y8bood8P'  `Y8bod8P' o888o o888o o888o  888bod8P' `Y888""8o o888o o888o o888o `Y8bod8P' 8""888P'
                                          888
                                         o888o
*/

  /**
   * Retrieves the company for the currently authenticated user. Returns a company
   * object and a 200 OK response code.
   *
   * @return object The company.
   */
  public function retrieve_company(){
    return $this->run( 'company' );
  }

/*
ooooo                                    o8o
`888'                                    `"'
 888  ooo. .oo.   oooo    ooo  .ooooo.  oooo   .ooooo.   .ooooo.   .oooo.o
 888  `888P"Y88b   `88.  .8'  d88' `88b `888  d88' `"Y8 d88' `88b d88(  "8
 888   888   888    `88..8'   888   888  888  888       888ooo888 `"Y88b.
 888   888   888     `888'    888   888  888  888   .o8 888    .o o.  )88b
o888o o888o o888o     `8'     `Y8bod8P' o888o `Y8bod8P' `Y8bod8P' 8""888P'
*/

  /**
   * Returns a list of your invoices. The invoices are returned sorted by issue date,
   * with the most recently issued invoices appearing first.
   *
   * The response contains an object with a invoices property that contains an array
   * of up to per_page invoices. Each entry in the array is a separate invoice object.
   * If no more invoices are available, the resulting array will be empty. Several
   * additional pagination properties are included in the response to simplify paginating your invoices.
   *
   * Can be paginated.
   *
   * @param  int    $client_id     (Default: null) Only return invoices belonging
   *                               to the client with the given ID.
   * @param  int    $project_id    (Default: null) Only return invoices associated
   *                               with the project with the given ID.
   * @return [type]                [description]
   */
  public function list_invoices( $client_id = null, $project_id = null ){
    $args = $this->parse_args(array(
      'client_id'     => $client_id,
      'project_id'    => $project_id
    ));

    return $this->run( 'invoices', $args );
  }

  /**
   * Retrieves the invoice with the given ID. Returns an invoice object and a 200
   * OK response code if a valid identifier was provided.
   *
   * @param  int $invoice_id The ID of the invoice.
   * @return object          The invoice.
   */
  public function retrieve_invoice( $invoice_id ){
    return $this->run( "invoices/$invoice_id" );
  }

  // TODO: other invoices.

/*
ooooo                                    o8o                           ooo        ooooo
`888'                                    `"'                           `88.       .888'
 888  ooo. .oo.   oooo    ooo  .ooooo.  oooo   .ooooo.   .ooooo.        888b     d'888   .ooooo.   .oooo.o  .oooo.o  .oooo.    .oooooooo  .ooooo.   .oooo.o
 888  `888P"Y88b   `88.  .8'  d88' `88b `888  d88' `"Y8 d88' `88b       8 Y88. .P  888  d88' `88b d88(  "8 d88(  "8 `P  )88b  888' `88b  d88' `88b d88(  "8
 888   888   888    `88..8'   888   888  888  888       888ooo888       8  `888'   888  888ooo888 `"Y88b.  `"Y88b.   .oP"888  888   888  888ooo888 `"Y88b.
 888   888   888     `888'    888   888  888  888   .o8 888    .o       8    Y     888  888    .o o.  )88b o.  )88b d8(  888  `88bod8P'  888    .o o.  )88b
o888o o888o o888o     `8'     `Y8bod8P' o888o `Y8bod8P' `Y8bod8P'      o8o        o888o `Y8bod8P' 8""888P' 8""888P' `Y888""8o `8oooooo.  `Y8bod8P' 8""888P'
                                                                                                                              d"     YD
                                                                                                                              "Y88888P'
*/

/*
ooooo                                    o8o                           ooooooooo.                                                                     .
`888'                                    `"'                           `888   `Y88.                                                                 .o8
 888  ooo. .oo.   oooo    ooo  .ooooo.  oooo   .ooooo.   .ooooo.        888   .d88'  .oooo.   oooo    ooo ooo. .oo.  .oo.    .ooooo.  ooo. .oo.   .o888oo  .oooo.o
 888  `888P"Y88b   `88.  .8'  d88' `88b `888  d88' `"Y8 d88' `88b       888ooo88P'  `P  )88b   `88.  .8'  `888P"Y88bP"Y88b  d88' `88b `888P"Y88b    888   d88(  "8
 888   888   888    `88..8'   888   888  888  888       888ooo888       888          .oP"888    `88..8'    888   888   888  888ooo888  888   888    888   `"Y88b.
 888   888   888     `888'    888   888  888  888   .o8 888    .o       888         d8(  888     `888'     888   888   888  888    .o  888   888    888 . o.  )88b
o888o o888o o888o     `8'     `Y8bod8P' o888o `Y8bod8P' `Y8bod8P'      o888o        `Y888""8o     .8'     o888o o888o o888o `Y8bod8P' o888o o888o   "888" 8""888P'
                                                                                              .o..P'
                                                                                              `Y8P'
*/

/*
ooooo                                    o8o                           ooooo     .
`888'                                    `"'                           `888'   .o8
 888  ooo. .oo.   oooo    ooo  .ooooo.  oooo   .ooooo.   .ooooo.        888  .o888oo  .ooooo.  ooo. .oo.  .oo.    .oooo.o
 888  `888P"Y88b   `88.  .8'  d88' `88b `888  d88' `"Y8 d88' `88b       888    888   d88' `88b `888P"Y88bP"Y88b  d88(  "8
 888   888   888    `88..8'   888   888  888  888       888ooo888       888    888   888ooo888  888   888   888  `"Y88b.
 888   888   888     `888'    888   888  888  888   .o8 888    .o       888    888 . 888    .o  888   888   888  o.  )88b
o888o o888o o888o     `8'     `Y8bod8P' o888o `Y8bod8P' `Y8bod8P'      o888o   "888" `Y8bod8P' o888o o888o o888o 8""888P
  */

/*
oooooooooooo              .    o8o                                  .
`888'     `8            .o8    `"'                                .o8
 888          .oooo.o .o888oo oooo  ooo. .oo.  .oo.    .oooo.   .o888oo  .ooooo.   .oooo.o
 888oooo8    d88(  "8   888   `888  `888P"Y88bP"Y88b  `P  )88b    888   d88' `88b d88(  "8
 888    "    `"Y88b.    888    888   888   888   888   .oP"888    888   888ooo888 `"Y88b.
 888       o o.  )88b   888 .  888   888   888   888  d8(  888    888 . 888    .o o.  )88b
o888ooooood8 8""888P'   "888" o888o o888o o888o o888o `Y888""8o   "888" `Y8bod8P' 8""888P'
*/

/*
oooooooooooo              .    o8o                                  .                  ooo        ooooo
`888'     `8            .o8    `"'                                .o8                  `88.       .888'
 888          .oooo.o .o888oo oooo  ooo. .oo.  .oo.    .oooo.   .o888oo  .ooooo.        888b     d'888   .ooooo.   .oooo.o  .oooo.o  .oooo.    .oooooooo  .ooooo.   .oooo.o
 888oooo8    d88(  "8   888   `888  `888P"Y88bP"Y88b  `P  )88b    888   d88' `88b       8 Y88. .P  888  d88' `88b d88(  "8 d88(  "8 `P  )88b  888' `88b  d88' `88b d88(  "8
 888    "    `"Y88b.    888    888   888   888   888   .oP"888    888   888ooo888       8  `888'   888  888ooo888 `"Y88b.  `"Y88b.   .oP"888  888   888  888ooo888 `"Y88b.
 888       o o.  )88b   888 .  888   888   888   888  d8(  888    888 . 888    .o       8    Y     888  888    .o o.  )88b o.  )88b d8(  888  `88bod8P'  888    .o o.  )88b
o888ooooood8 8""888P'   "888" o888o o888o o888o o888o `Y888""8o   "888" `Y8bod8P'      o8o        o888o `Y8bod8P' 8""888P' 8""888P' `Y888""8o `8oooooo.  `Y8bod8P' 8""888P'
                                                                                                                                              d"     YD
                                                                                                                                              "Y88888P'
*/



/*
oooooooooooo              .    o8o                                  .                  ooooo     .
`888'     `8            .o8    `"'                                .o8                  `888'   .o8
 888          .oooo.o .o888oo oooo  ooo. .oo.  .oo.    .oooo.   .o888oo  .ooooo.        888  .o888oo  .ooooo.  ooo. .oo.  .oo.    .oooo.o
 888oooo8    d88(  "8   888   `888  `888P"Y88bP"Y88b  `P  )88b    888   d88' `88b       888    888   d88' `88b `888P"Y88bP"Y88b  d88(  "8
 888    "    `"Y88b.    888    888   888   888   888   .oP"888    888   888ooo888       888    888   888ooo888  888   888   888  `"Y88b.
 888       o o.  )88b   888 .  888   888   888   888  d8(  888    888 . 888    .o       888    888 . 888    .o  888   888   888  o.  )88b
o888ooooood8 8""888P'   "888" o888o o888o o888o o888o `Y888""8o   "888" `Y8bod8P'      o888o   "888" `Y8bod8P' o888o o888o o888o 8""888P'
*/

/*
oooooooooooo
`888'     `8
 888         oooo    ooo oo.ooooo.   .ooooo.  ooo. .oo.    .oooo.o  .ooooo.   .oooo.o
 888oooo8     `88b..8P'   888' `88b d88' `88b `888P"Y88b  d88(  "8 d88' `88b d88(  "8
 888    "       Y888'     888   888 888ooo888  888   888  `"Y88b.  888ooo888 `"Y88b.
 888       o  .o8"'88b    888   888 888    .o  888   888  o.  )88b 888    .o o.  )88b
o888ooooood8 o88'   888o  888bod8P' `Y8bod8P' o888o o888o 8""888P' `Y8bod8P' 8""888P'
                          888
                         o888o
*/

/*
oooooooooooo                                                                        .oooooo.                 .                                            o8o
`888'     `8                                                                       d8P'  `Y8b              .o8                                            `"'
 888         oooo    ooo oo.ooooo.   .ooooo.  ooo. .oo.    .oooo.o  .ooooo.       888           .oooo.   .o888oo  .ooooo.   .oooooooo  .ooooo.  oooo d8b oooo   .ooooo.   .oooo.o
 888oooo8     `88b..8P'   888' `88b d88' `88b `888P"Y88b  d88(  "8 d88' `88b      888          `P  )88b    888   d88' `88b 888' `88b  d88' `88b `888""8P `888  d88' `88b d88(  "8
 888    "       Y888'     888   888 888ooo888  888   888  `"Y88b.  888ooo888      888           .oP"888    888   888ooo888 888   888  888   888  888      888  888ooo888 `"Y88b.
 888       o  .o8"'88b    888   888 888    .o  888   888  o.  )88b 888    .o      `88b    ooo  d8(  888    888 . 888    .o `88bod8P'  888   888  888      888  888    .o o.  )88b
o888ooooood8 o88'   888o  888bod8P' `Y8bod8P' o888o o888o 8""888P' `Y8bod8P'       `Y8bood8P'  `Y888""8o   "888" `Y8bod8P' `8oooooo.  `Y8bod8P' d888b    o888o `Y8bod8P' 8""888P'
                          888                                                                                              d"     YD
                         o888o                                                                                             "Y88888P'
*/

/*
ooooooooooooo                    oooo
8'   888   `8                    `888
     888       .oooo.    .oooo.o  888  oooo   .oooo.o
     888      `P  )88b  d88(  "8  888 .8P'   d88(  "8
     888       .oP"888  `"Y88b.   888888.    `"Y88b.
     888      d8(  888  o.  )88b  888 `88b.  o.  )88b
    o888o     `Y888""8o 8""888P' o888o o888o 8""888P'
*/

  /**
   * Returns a list of your tasks. The tasks are returned sorted by creation date,
   * with the most recently created tasks appearing first.
   *
   * The response contains an object with a tasks property that contains an array of
   * up to per_page tasks. Each entry in the array is a separate task object. If no
   * more tasks are available, the resulting array will be empty. Several additional
   * pagination properties are included in the response to simplify paginating your tasks.
   *
   * Can be paginated.
   *
   * @param  bool $is_active       (Default: null) Whether to only return active/inactive
   *                               tasks. (null returns all tasks).
   * @return object                A list of tasks, along with some pagination properties.
   */
  public function list_tasks( $is_active = null ){
    $args = $this->parse_args(array(
      'is_active' => $is_active
    ));

    return $this->run( 'tasks', $args );
  }

  /**
   * Retrieves the task with the given ID. Returns a task object and a 200 OK
   * response code if a valid identifier was provided.
   *
   * @param  int $task_id The task's ID.
   * @return object       The task.
   */
  public function retrieve_task( $task_id ){
    return $this->run( "tasks/$task_id" );
  }

  /**
   * Creates a new task object. Returns a task object and a 201 Created response
   * code if the call succeeded.
   *
   * @param  string $name The name of the task.
   * @param  array  $args An additional array of optional args, all are string except
   *                      defauly_hourly_rate, which is a double:
   *                        billable_by_default => Used in determining whether default tasks
   *                                               should be marked billable when creating a
   *                                               new project. Defaults to true.
   *                        default_hourly_rate => The default hourly rate to use for this task
   *                                               when it is added to a project. Defaults to 0.
   *                        is_default          => Whether this task should be automatically
   *                                               added to future projects. Defaults to false.
   *                        is_active           => Whether this task is active or archived. Defaults to true.
   * @return object       The newly created task.
   */
  public function create_task( $name, $args = array() ){
    $args['name'] = $name;

    return $this->run( 'tasks', $args, 'POST' );
  }

  /**
   * Updates the specific task by setting the values of the parameters passed. Any
   * parameters not provided will be left unchanged. Returns a task object and a 200
   * OK response code if the call succeeded.
   *
   * @param  string $name The name of the task.
   * @param  array  $args An additional array of optional args, all are string except
   *                      defauly_hourly_rate, which is a double:
   *                        name                => The name of the task.
   *                        billable_by_default => Used in determining whether default tasks
   *                                               should be marked billable when creating a
   *                                               new project. Defaults to true.
   *                        default_hourly_rate => The default hourly rate to use for this task
   *                                               when it is added to a project. Defaults to 0.
   *                        is_default          => Whether this task should be automatically
   *                                               added to future projects. Defaults to false.
   *                        is_active           => Whether this task is active or archived. Defaults to true.
   * @return object       The updated task.
   */
  public function update_task( $task_id, $args = array() ){
    return $this->run( "tasks/$task_id", $args, 'PATCH' );
  }

  /**
   * Delete a task. Deleting a task is only possible if it has no time entries associated
   * with it. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int $task_id The task's ID.
   * @return object       The response code, 200 if success.
   */
  public function delete_task( $task_id ){
    return $this->run( "tasks/$task_id", array(), 'DELETE' );
  }

/*
ooooooooooooo  o8o                                       oooo                                .
8'   888   `8  `"'                                       `888                              .o8
     888      oooo  ooo. .oo.  .oo.    .ooooo.   .oooo.o  888 .oo.    .ooooo.   .ooooo.  .o888oo  .oooo.o
     888      `888  `888P"Y88bP"Y88b  d88' `88b d88(  "8  888P"Y88b  d88' `88b d88' `88b   888   d88(  "8
     888       888   888   888   888  888ooo888 `"Y88b.   888   888  888ooo888 888ooo888   888   `"Y88b.
     888       888   888   888   888  888    .o o.  )88b  888   888  888    .o 888    .o   888 . o.  )88b
    o888o     o888o o888o o888o o888o `Y8bod8P' 8""888P' o888o o888o `Y8bod8P' `Y8bod8P'   "888" 8""888P'
*/

  /**
   * Returns a list of your time entries. The time entries are returned sorted by
   * creation date, with the most recently created time entries appearing first.
   *
   * The response contains an object with a time_entries property that contains an
   * array of up to per_page time entries. Each entry in the array is a separate
   * time entry object. If no more time entries are available, the resulting array
   * will be empty. Several additional pagination properties are included in the
   * response to simplify paginating your time entries.
   *
   * Can be paginated.
   *
   * Possible TODO: make an internal function (->set_arguments( ... )->func?) that allows
   * the program to pass arguments in a more comfortable manner.
   *
   * Arguments supported within args (by key => value):
   *   user_id       integer  Only return time entries belonging to the user with the given ID.
   *   client_id     integer  Only return time entries belonging to the client with the given ID.
   *   project_id    integer  Only return time entries belonging to the project with the given ID.
   *   is_billed     boolean  Pass true to only return time entries that have been invoiced and
   *                          false to return time entries that have not been invoiced.
   *   is_running    boolean  Pass true to only return running time entries and false to return
   *                          non-running time entries.
   *   updated_since datetime Only return time entries that have been updated since the
   *                          given date and time.
   *   from          date     Only return time entries with a spent_date on or after
   *                          the given date.
   *   to            date     Only return time entries with a spent_date on or before
   *                          the given date.
   *
   * @param  array  $args (Default: array()) An array of arguments.
   * @return object       The time entries.
   */
  public function list_entries( $args = array() ){
    return $this->run( 'time_entries', $args );
  }

  /**
   * Retrieves the time entry with the given ID. Returns a time entry object and a 200
   * OK response code if a valid identifier was provided.
   *
   * @param  int $entry_id The entry's ID.
   * @return object        The entry.
   */
  public function retrieve_entry( $entry_id ){
    return $this->run( "time_entries/$entry_id" );
  }

  /**
   * Creates a new time entry object (duration or timestamp). Returns a time entry
   * object and a 201 Created response code if the call succeeded.
   *
   * Arguments supported by $args, all optional:
   *   user_id            integer  The ID of the user to associate with the time entry.
   *                               Defaults to the currently authenticated user’s ID.
   *   timer_started_at   datetime The ISO 8601 formatted date and time the timer was
   *                               started. Defaults to the current time.
   *   hours              decimal  The current amount of time tracked. Defaults to 0.0.
   *   notes              string   Any notes to be associated with the time entry.
   *   external_reference object   An object containing the id, group_id, and permalink
   *                               of the external reference.
   *
   * This creates a time entry object duration.
   *
   * If you'd instead like to create a timestamp, rather than using timer_started_at and
   * hours, you can instead pass:
   *   started_time       time     The time the entry started. Defaults to the current
   *                               time. Example: “8:00am”.
   *   ended_time         time     The time the entry ended.
   *
   * @param  int    $project_id The ID of the project to associate with the time entry.
   * @param  int    $task_id    The ID of the task to associate with the time entry.
   * @param  string $spent_date The ISO 8601 formatted date the time entry was spent.
   * @param  array  $args       (Default: array()) Alternate arguments to include.
   * @return [type]             [description]
   */
  public function create_entry( $project_id, $task_id, $spent_date, $args = array() ){
    $args = $this->parse_args(array(
      'project_id' => $project_id,
      'task_id'    => $task_id,
      'spent_date' => $spent_date
    ), $args );

    return $this->run( 'time_entries', $args, 'POST' );
  }

  /**
   * Updates the specific time entry by setting the values of the parameters passed.
   * Any parameters not provided will be left unchanged. Returns a time entry object
   * and a 200 OK response code if the call succeeded.
   *
   * The possible arguments for args include (all optional):
   *   project_id         integer The ID of the project to associate with the time entry.
   *   task_id            integer The ID of the task to associate with the time entry.
   *   spent_date         date    The ISO 8601 formatted date the time entry was spent.
   *   started_time       time    The time the entry started. Defaults to the current time. Example: “8:00am”.
   *   ended_time         time    The time the entry ended.
   *   hours              decimal The current amount of time tracked.
   *   notes              string  Any notes to be associated with the time entry.
   *   external_reference object  An object containing the id, group_id, and permalink of the external reference.
   *
   * @param  int $entry_id The ID of the entry.
   * @param  array $args   (Default: array()) Optional arguments to change.
   * @return object        The updated entry.
   */
  public function update_entry( $entry_id, $args ){
    return $this->run( "time_entries/$entry_id", $args, 'PATCH' );
  }

  /**
   * Delete a time entry. Deleting a time entry is only possible if it’s not closed
   * and the associated project and task haven’t been archived. However, Admins can
   * delete closed entries. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int $entry_id The entry's ID.
   * @return [type]        A 200 OK if successful.
   */
  public function delete_entry( $entry_id ){
    return $this->run( "time_entries/$entry_id", array(), 'DELETE' );
  }

  /**
   * Restarting a time entry is only possible if it isn’t currently running. Returns
   * a 200 OK response code if the call succeeded.
   *
   * @param  int $entry_id The entry's ID.
   * @return [type]        A 200 OK if successful.
   */
  public function restart_entry( $entry_id ){
    return $this->run( "time_entries/$entry_id/restart", array(), 'PATCH' );
  }

  /**
   * Stopping a time entry is only possible if it’s currently running. Returns a
   * 200 OK response code if the call succeeded.
   * @param  int $entry_id The entry's ID.
   * @return [type]        A 200 OK if successful.
   */
  public function stop_entry( $entry_id ){
    return $this->run( "time_entries/$entry_id/stop", array(), 'PATCH' );
  }


/*
ooooooooo.                          o8o                         .
`888   `Y88.                        `"'                       .o8
 888   .d88' oooo d8b  .ooooo.     oooo  .ooooo.   .ooooo.  .o888oo  .oooo.o
 888ooo88P'  `888""8P d88' `88b    `888 d88' `88b d88' `"Y8   888   d88(  "8
 888          888     888   888     888 888ooo888 888         888   `"Y88b.
 888          888     888   888     888 888    .o 888   .o8   888 . o.  )88b
o888o        d888b    `Y8bod8P'     888 `Y8bod8P' `Y8bod8P'   "888" 8""888P'
                                    888
                                .o. 88P
                                `Y888P
*/

  /**
   * Returns a list of your projects. The projects are returned sorted by creation date,
   * with the most recently created projects appearing first.
   *
   * The response contains an object with a projects property that contains an array
   * of up to per_page projects. Each entry in the array is a separate project object.
   * If no more projects are available, the resulting array will be empty. Several additional
   * pagination properties are included in the response to simplify paginating your projects.
   *
   * Can be paginated.
   *
   * @param  boolean $is_active     (Default: null) Pass true to only return active
   *                                projects and false to return inactive projects.
   * @param  int     $cliend_id     (Default: null) Only return projects belonging to
   *                                the client with the given ID.
   * @return [type]                 [description]
   */
  public function list_projects( $client_id = null, $is_active = null ){
    $args = $this->parse_args(array(
      'is_active'     => $is_active,
      'client_id'     => $client_id
    ));

    return $this->run( 'projects', $args );
  }

  /**
   * Retrieves the project with the given ID. Returns a project object and a 200
   * OK response code if a valid identifier was provided.
   *
   * @param  int $project_id The project's ID.
   * @return object          A list of projects, along with some pagination properties.
   */
  public function retrieve_project( $project_id ){
    return $this->run( "projects/$project_id" );
  }

  /**
   * Creates a new project object. Returns a project object and a 201 Created response
   * code if the call succeeded.
   *
   * Additional optional arguments may be passed into $args by key => val:
   *   code                                string  The code associated with the project.
   *   is_active                           boolean Whether the project is active or archived. Defaults to true.
   *   is_fixed_fee                        boolean Whether the project is a fixed-fee project or not.
   *   hourly_rate                         decimal Rate for projects billed by Project Hourly Rate.
   *   budget                              decimal The budget in hours for the project when budgeting by time.
   *   notify_when_over_budget             boolean Whether project managers should be notified when the project
   *                                               goes over budget. Defaults to false.
   *   over_budget_notification_percentage decimal Percentage value used to trigger over budget email alerts.
   *                                               Example: use 10.0 for 10.0%.
   *   show_budget_to_all                  boolean Option to show project budget to all employees. Does not apply
   *                                               to Total Project Fee projects. Defaults to false.
   *   cost_budget                         decimal The monetary budget for the project when budgeting by money.
   *   cost_budget_include_expenses        boolean Option for budget of Total Project Fees projects to include
   *                                               tracked expenses. Defaults to false.
   *   fee                                 decimal The amount you plan to invoice for the project. Only used by
   *                                               fixed-fee projects.
   *   notes                               string  Project notes.
   *   starts_on                           date    Date the project was started.
   *   ends_on                             date    Date the project will end.
   *
   * @param  int     $client_id   The ID of the client to associate this project with.
   * @param  string  $name        The name of the project.
   * @param  boolean $is_billable Whether the project is billable or not.
   * @param  string  $bill_by     The method by which the project is invoiced.
   *                              Options: Project, Tasks, People, or none.
   * @param  string  $budget_by   The method by which the project is budgeted.
   *                              Options: project (Hours Per Project), project_cost
   *                              (Total Project Fees), task (Hours Per Task), person
   *                              (Hours Per Person), none (No Budget).
   * @param  array   $args        Additional arguments as specific above.
   * @return object               The newly created project.
   */
  public function create_project( $client_id, $name, $is_billable = false, $bill_by = 'none', $budget_by = 'none', $args = array() ){
    $args = $this->parse_args(array(
      'client_id'   => $client_id,
      'name'        => $name,
      'is_billable' => $is_billable,
      'bill_by'     => $bill_by,
      'budget_by'   => $budget_by
    ), $args );

    return $this->run( 'projects', $args, 'POST' );
  }

  /**
   * Updates the specific project by setting the values of the parameters passed.
   * Any parameters not provided will be left unchanged. Returns a project object
   * and a 200 OK response code if the call succeeded.
   *
   * Additional optional arguments may be passed into $args by key => val:
   *   code                                string  The code associated with the project.
   *   is_active                           boolean Whether the project is active or archived. Defaults to true.
   *   is_fixed_fee                        boolean Whether the project is a fixed-fee project or not.
   *   hourly_rate                         decimal Rate for projects billed by Project Hourly Rate.
   *   budget                              decimal The budget in hours for the project when budgeting by time.
   *   notify_when_over_budget             boolean Whether project managers should be notified when the project
   *                                               goes over budget. Defaults to false.
   *   over_budget_notification_percentage decimal Percentage value used to trigger over budget email alerts.
   *                                               Example: use 10.0 for 10.0%.
   *   show_budget_to_all                  boolean Option to show project budget to all employees. Does not apply
   *                                               to Total Project Fee projects. Defaults to false.
   *   cost_budget                         decimal The monetary budget for the project when budgeting by money.
   *   cost_budget_include_expenses        boolean Option for budget of Total Project Fees projects to include
   *                                               tracked expenses. Defaults to false.
   *   fee                                 decimal The amount you plan to invoice for the project. Only used by
   *                                               fixed-fee projects.
   *   notes                               string  Project notes.
   *   starts_on                           date    Date the project was started.
   *   ends_on                             date    Date the project will end.
   *
   * @param  int   $project_id The project's ID.
   * @param  array $args       Fields to update.
   * @return object            The updates project.
   */
  public function update_project( $project_id, $args = array() ){
    return $this->run( "projects/$project_id", $args, 'PATCH' );
  }

  /**
   * Deletes a project and any time entries or expenses tracked to it. However,
   * invoices associated with the project will not be deleted. If you don’t want
   * the project’s time entries and expenses to be deleted, you should archive
   * the project instead. This can be done with update_project().
   *
   * @param  int $project_id The project's ID.
   * @return object          200 OK code on success.
   */
  public function delete_project( $project_id ){
    return $this->run( "projects/$project_id", array(), 'DELETE' );
  }

/*
ooooooooo.                          o8o                         .        ooooo     ooo                                        .o.                          o8o                                                                     .
`888   `Y88.                        `"'                       .o8        `888'     `8'                                       .888.                         `"'                                                                   .o8
 888   .d88' oooo d8b  .ooooo.     oooo  .ooooo.   .ooooo.  .o888oo       888       8   .oooo.o  .ooooo.  oooo d8b          .8"888.      .oooo.o  .oooo.o oooo   .oooooooo ooo. .oo.   ooo. .oo.  .oo.    .ooooo.  ooo. .oo.   .o888oo  .oooo.o
 888ooo88P'  `888""8P d88' `88b    `888 d88' `88b d88' `"Y8   888         888       8  d88(  "8 d88' `88b `888""8P         .8' `888.    d88(  "8 d88(  "8 `888  888' `88b  `888P"Y88b  `888P"Y88bP"Y88b  d88' `88b `888P"Y88b    888   d88(  "8
 888          888     888   888     888 888ooo888 888         888         888       8  `"Y88b.  888ooo888  888            .88ooo8888.   `"Y88b.  `"Y88b.   888  888   888   888   888   888   888   888  888ooo888  888   888    888   `"Y88b.
 888          888     888   888     888 888    .o 888   .o8   888 .       `88.    .8'  o.  )88b 888    .o  888           .8'     `888.  o.  )88b o.  )88b  888  `88bod8P'   888   888   888   888   888  888    .o  888   888    888 . o.  )88b
o888o        d888b    `Y8bod8P'     888 `Y8bod8P' `Y8bod8P'   "888"         `YbodP'    8""888P' `Y8bod8P' d888b         o88o     o8888o 8""888P' 8""888P' o888o `8oooooo.  o888o o888o o888o o888o o888o `Y8bod8P' o888o o888o   "888" 8""888P'
                                    888                                                                                                                         d"     YD
                                .o. 88P                                                                                                                         "Y88888P'
                                `Y888P
*/

  /**
   * Returns a list of your user assignments for the project identified by $project_id.
   * The user assignments are returned sorted by creation date, with the most recently
   * created user assignments appearing first.
   *
   * The response contains an object with a user_assignments property that contains
   * an array of up to per_page user assignments. Each entry in the array is a separate
   * user assignment object. If no more user assignments are available, the resulting
   * array will be empty. Several additional pagination properties are included in
   * the response to simplify paginating your user assignments.
   *
   * Can be paginated.
   *
   * @param  int     $project_id    The project's ID.
   * @param  boolean $is_active     Pass true to only return active user assignments and
   *                                false to return inactive user assignments.
   * @return object                 A list of project user assignments, along with some
   *                                pagination properties.
   */
  public function list_user_assignments( $project_id, $is_active = null ){
    $args = $this->parse_args(array(
      'is_active'     => $is_active
    ));

    return $this->run( "projects/$project_id/user_assignments", $args );
  }

  /**
   * Retrieves the user assignment with the given ID. Returns a user assignment object
   * and a 200 OK response code if a valid identifier was provided.
   * @param  int $project_id    The project's ID.
   * @param  int $assignment_id The assignment's ID.
   * @return object             The user assignment.
   */
  public function retrieve_user_assignment( $project_id, $assignment_id ){
    return $this->run( "projects/$project_id/user_assignments/$assignment_id" );
  }

  /**
   * Creates a new user assignment object. Returns a user assignment object and a
   * 201 Created response code if the call succeeded.
   *
   * Additional arguments can be passed into $args, all optional:
   *   is_active          boolean Whether the user assignment is active or archived.
   *                              Defaults to true.
   *   is_project_manager boolean Determines if the user has project manager permissions
   *                              for the project. Defaults to false.
   *   hourly_rate        decimal Rate used when the project’s bill_by is People.
   *                              Defaults to 0.
   *   budget             decimal Budget used when the project’s budget_by is person.
   *
   * @param  int    $project_id The project's ID.
   * @param  int    $user_id    The user's ID.
   * @param  array  $args       (Default: array()) Additional arguments to pass.
   * @return object             THe newly created user assignment
   */
  public function create_user_assignment( $project_id, $user_id, $args = array() ){
    return $this->run( "projects/$project_id/user_assignments", $args, 'POST' );
  }

  /**
   * Updates the specific user assignment by setting the values of the parameters
   * passed. Any parameters not provided will be left unchanged. Returns a user
   * assignment object and a 200 OK response code if the call succeeded.
   *
   * Additional arguments can be passed into $args, all optional:
   *   is_active          boolean Whether the user assignment is active or archived.
   *   is_project_manager boolean Determines if the user has project manager permissions
   *                              for the project.
   *   hourly_rate        decimal Rate used when the project’s bill_by is People.
   *   budget             decimal Budget used when the project’s budget_by is person.
   *
   * @param  int    $project_id    The project's ID.
   * @param  int    $assignment_id The user assignment's ID.
   * @param  array  $args          (Default: array()) Additional arguments to add.
   * @return object                The updated user assignment.
   */
  public function update_user_assignment( $project_id, $assignment_id, $args = array() ){
    return $this->run( "projects/$project_id/user_assignments/$assignment_id", $args, 'PATCH' );
  }

  /**
   * Delete a user assignment. Deleting a user assignment is only possible if it
   * has no time entries or expenses associated with it. Returns a 200 OK response
   * code if the call succeeded.
   *
   * @param  int $project_id    The project's ID.
   * @param  int $assignment_id The user assignment's ID.
   * @return object             200 OK if successful.
   */
  public function delete_user_assignment( $project_id, $assignment_id ){
    return $this->run( "projects/$project_id/user_assignments/$assignment_id", array(), 'DELETE' );
  }

/*
ooooooooo.                          o8o                         .        ooooooooooooo                    oooo                   .o.                          o8o                                                                     .
`888   `Y88.                        `"'                       .o8        8'   888   `8                    `888                  .888.                         `"'                                                                   .o8
 888   .d88' oooo d8b  .ooooo.     oooo  .ooooo.   .ooooo.  .o888oo           888       .oooo.    .oooo.o  888  oooo           .8"888.      .oooo.o  .oooo.o oooo   .oooooooo ooo. .oo.   ooo. .oo.  .oo.    .ooooo.  ooo. .oo.   .o888oo  .oooo.o
 888ooo88P'  `888""8P d88' `88b    `888 d88' `88b d88' `"Y8   888             888      `P  )88b  d88(  "8  888 .8P'           .8' `888.    d88(  "8 d88(  "8 `888  888' `88b  `888P"Y88b  `888P"Y88bP"Y88b  d88' `88b `888P"Y88b    888   d88(  "8
 888          888     888   888     888 888ooo888 888         888             888       .oP"888  `"Y88b.   888888.           .88ooo8888.   `"Y88b.  `"Y88b.   888  888   888   888   888   888   888   888  888ooo888  888   888    888   `"Y88b.
 888          888     888   888     888 888    .o 888   .o8   888 .           888      d8(  888  o.  )88b  888 `88b.        .8'     `888.  o.  )88b o.  )88b  888  `88bod8P'   888   888   888   888   888  888    .o  888   888    888 . o.  )88b
o888o        d888b    `Y8bod8P'     888 `Y8bod8P' `Y8bod8P'   "888"          o888o     `Y888""8o 8""888P' o888o o888o      o88o     o8888o 8""888P' 8""888P' o888o `8oooooo.  o888o o888o o888o o888o o888o `Y8bod8P' o888o o888o   "888" 8""888P'
                                    888                                                                                                                            d"     YD
                                .o. 88P                                                                                                                            "Y88888P'
                                `Y888P
*/

   /**
    * Returns a list of your task assignments for the project identified by $project_id.
    * The task assignments are returned sorted by creation date, with the most recently
    * created task assignments appearing first.
    *
    * The response contains an object with a task_assignments property that contains
    * an array of up to per_page task assignments. Each entry in the array is a separate
    * task assignment object. If no more task assignments are available, the resulting
    * array will be empty. Several additional pagination properties are included in
    * the response to simplify paginating your task assignments.
    *
    * Can be paginated.
    *
    * @param  int     $project_id    The project's ID.
    * @param  boolean $is_active     Pass true to only return active task assignments and
    *                                false to return inactive task assignments.
    * @return object                 A list of project task assignments, along with some
    *                                pagination properties.
    */
   public function list_task_assignments( $project_id, $is_active = null ){
     $args = $this->parse_args(array(
       'is_active'     => $is_active
     ));

     return $this->run( "projects/$project_id/task_assignments", $args );
   }

   /**
    * Creates a new task assignment object. Returns a task assignment object and a
    * 201 Created response code if the call succeeded.
    *
    * Additional arguments can be passed into $args, all optional:
    *   is_active   boolean Whether the task assignment is active or archived.
    *                       Defaults to true.
    *   billable    boolean Whether the task assignment is billable or not.
    *                       Defaults to false.
    *   hourly_rate decimal Rate used when the project’s bill_by is Tasks. Defaults to
    *                       null when billing by task hourly rate, otherwise 0.
    *   budget      decimal Budget used when the project’s budget_by is task or task_fees.
    *
    * @param  int    $project_id The project's ID.
    * @param  int    $task_id    The task's ID.
    * @param  array  $args       (Default: array()) Additional arguments to pass.
    * @return object             THe newly created task assignment
    */
   public function create_task_assignment( $project_id, $task_id, $args = array() ){
     $args['task_id'] = $task_id;
     return $this->run( "projects/$project_id/task_assignments", $args, 'POST' );
   }

   /**
    * Updates the specific task assignment by setting the values of the parameters
    * passed. Any parameters not provided will be left unchanged. Returns a task
    * assignment object and a 200 OK response code if the call succeeded.
    *
    * Additional arguments can be passed into $args, all optional:
    *   is_active   boolean Whether the task assignment is active or archived.
    *   billable    boolean Whether the task assignment is billable or not.
    *   hourly_rate decimal Rate used when the project’s bill_by is Tasks.
    *   budget      decimal Budget used when the project’s budget_by is task or task_fees.
    *
    * @param  int    $project_id    The project's ID.
    * @param  int    $assignment_id The task assignment's ID.
    * @param  array  $args          (Default: array()) Additional arguments to add.
    * @return object                The updated task assignment.
    */
   public function update_task_assignment( $project_id, $assignment_id, $args = array() ){
     return $this->run( "projects/$project_id/task_assignments/$assignment_id", $args, 'PATCH' );
   }

   /**
    * Delete a task assignment. Deleting a task assignment is only possible if it
    * has no time entries or expenses associated with it. Returns a 200 OK response
    * code if the call succeeded.
    *
    * @param  int $project_id    The project's ID.
    * @param  int $assignment_id The task assignment's ID.
    * @return object             200 OK if successful.
    */
   public function delete_task_assignment( $project_id, $assignment_id ){
     return $this->run( "projects/$project_id/user_assignments/$assignment_id", array(), 'DELETE' );
   }

/*
ooooooooo.             oooo
`888   `Y88.           `888
 888   .d88'  .ooooo.   888   .ooooo.   .oooo.o
 888ooo88P'  d88' `88b  888  d88' `88b d88(  "8
 888`88b.    888   888  888  888ooo888 `"Y88b.
 888  `88b.  888   888  888  888    .o o.  )88b
o888o  o888o `Y8bod8P' o888o `Y8bod8P' 8""888P'
*/

  /**
   * Returns a list of roles in the account. The roles are returned sorted by
   * creation date, with the most recently created roles appearing first.
   *
   * The response contains an object with a roles property that contains an array
   * of up to per_page roles. Each entry in the array is a separate role object.
   * If no more roles are available, the resulting array will be empty. Several
   * additional pagination properties are included in the response to simplify
   * paginating your roles.
   *
   * Can be paginated.
   *
   * @return object A list of roles, along with various pagination properties.
   */
  public function list_roles(){
    return $this->run( 'roles' );
  }

  /**
   * Retrieves the role with the given ID. Returns a role object and a 200 OK
   * response code if a valid identifier was provided.
   *
   * @param  int $role_id The role's ID.
   * @return object       The role.
   */
  public function retrieve_role( $role_id ){
    return $this->run( "roles/$role_id" );
  }

  /**
   * Creates a new role object. Returns a role object and a 201 Created response
   * code if the call succeeded.
   *
   * @param  string $name     The name of the role.
   * @param  array  $user_ids (Default: array()) The IDs of the users assigned to this role.
   * @return object           The newly created role.
   */
  public function create_role( $name, $user_ids = array() ){
    $args = $this->parse_args(array(
      'name'     => $name,
      'user_ids' => $user_ids
    ));

    return $this->run( 'roles', $args, 'POST' );
  }

  /**
   * Updates the specific role by setting the values of the parameters passed. Any
   * parameters not provided will be left unchanged. Returns a role object and a
   * 200 OK response code if the call succeeded.
   *
   * @param  int    $role_id  The role's ID.
   * @param  string $name     (Default: null) The name of the role.
   * @param  array  $user_ids (Default: null) An array of IDs to be passed placed in this role.
   * @return object           The updated role.
   */
  public function update_role( $role_id, $name = null, $user_ids = array() ){
    $args = $this->parse_args(array(
      'name' => $name,
      'user_ids' => $user_ids
    ));

    return $this->run( "roles/$role_id", $args, 'PATCH' );
  }

  /**
   * Delete a role. Deleting a role will unlink it from any users it was assigned
   * to. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int $role_id The role's ID.
   * @return object       A 200 OK response if it was successful.
   */
  public function delete_role( $role_id ){
    return $this->run( "roles/$role_id", array(), 'DELETE' );
  }

/*
ooooo     ooo
`888'     `8'
 888       8   .oooo.o  .ooooo.  oooo d8b  .oooo.o
 888       8  d88(  "8 d88' `88b `888""8P d88(  "8
 888       8  `"Y88b.  888ooo888  888     `"Y88b.
 `88.    .8'  o.  )88b 888    .o  888     o.  )88b
   `YbodP'    8""888P' `Y8bod8P' d888b    8""888P'
*/

  /**
   * Returns a list of your users. The users are returned sorted by creation date,
   * with the most recently created users appearing first.
   *
   * The response contains an object with a users property that contains an array
   * of up to per_page users. Each entry in the array is a separate user object.
   * If no more users are available, the resulting array will be empty. Several
   * additional pagination properties are included in the response to simplify
   * paginating your users.
   *
   * Can be paginated.
   *
   * @param  boolean $is_active     (Default: null) Pass true to only return active users
   *                                and false to return inactive users.
   * @return object                 A list of users, along with pagination properties.
   */
  public function list_users( $is_active = null ){
    $args = $this->parse_args(array(
      'is_active'     => $is_active
    ));

    return $this->run( 'users', $args );
  }

  /**
   * Retrieves the currently authenticated user. Returns a user object and a
   * 200 OK response code.
   *
   * @return object The user object for myself.
   */
  public function retrieve_me(){
    return $this->run( 'users/me' );
  }

  /**
   * Retrieves the user with the given ID. Returns a user object and a 200 OK
   * response code if a valid identifier was provided.
   *
   * @param  int $user_id The ID to grab. Alternatievly, 'me' grabs the current user.
   * @return object       The user.
   */
  public function retrieve_user( $user_id ){
    return $this->run( "users/$user_id" );
  }

  /**
   * Creates a new user object. Returns a user object and a 201 Created response
   * code if the call succeeded.
   *
   * Additional arguments may be passed into $args (all optional) by key => val:
   *   telephone                         string  The telephone number for the user.
   *   timezone                          string  The user’s timezone. Defaults to the company’s
   *                                             timezone. See a list of supported time zones.
   *   has_access_to_all_future_projects boolean Whether the user should be automatically added
   *                                             to future projects. Defaults to false.
   *   is_contractor                     boolean Whether the user is a contractor or an
   *                                             employee. Defaults to false.
   *   is_admin                          boolean Whether the user has admin permissions.
   *                                             Defaults to false.
   *   is_project_manager                boolean Whether the user has project manager
   *                                             permissions. Defaults to false.
   *   can_see_rates                     boolean Whether the user can see billable rates
   *                                             on projects. Only applicable to project
   *                                             managers. Defaults to false.
   *   can_create_projects               boolean Whether the user can create projects.
   *                                             Only applicable to project managers.
   *                                             Defaults to false.
   *   can_create_invoices               boolean Whether the user can create invoices
   *                                             Only applicable to project managers.
   *                                             Defaults to false.
   *   is_active                         boolean Whether the user is active or archived.
   *                                             Defaults to true.
   *   weekly_capacity                   integer The number of hours per week this person is
   *                                             available to work in seconds. Defaults
   *                                             to 126000 seconds (35 hours).
   *   default_hourly_rate               decimal The billable rate to use for this user
   *                                             when they are added to a project. Defaults to 0.
   *   cost_rate                         decimal The cost rate to use for this user
   *                                             when calculating a project’s costs vs
   *                                             billable amount. Defaults to 0.
   *   roles                             array   The role names (STRINGS) assigned to this person.
   *
   * If you want to add a new administrator, set is_admin to true. If you want to
   * add a PM, set is_admin to false, is_project_manager to true, and then set any
   * of the optional permissions to true that you’d like. If you want to add a regular
   * user, set both is_admin and is_project_manager to false.
   *
   * @param  string $first_name The user's first name.
   * @param  string $last_name  The user's last name.
   * @param  string $email      The user's email.
   * @param  array  $args       (Default: array()) Additional arguments.
   * @return object             The newly created user.
   */
  public function create_user( $first_name, $last_name, $email, $args = array() ){
    $args = $this->parse_args(array(
      'first_name' => $first_name,
      'last_name'  => $last_name,
      'email'      => $email
    ), $args );

    return $this->run( 'users', $args, 'POST' );
  }

  /**
   * Updates the specific user by setting the values of the parameters passed. Any
   * parameters not provided will be left unchanged. Returns a user object and a
   * 200 OK response code if the call succeeded.
   *
   * Arguments may be passed into $args (all optional) by key => val:
   *   first_name                        string  The first name of the user. Can’t
   *                                             be updated if the user is inactive.
   *   last_name                         string  The last name of the user. Can’t be
   *                                             updated if the user is inactive.
   *   email                             string  The email address of the user. Can’t
   *                                             be updated if the user is inactive.
   *   telephone                         string  The telephone number for the user.
   *   timezone                          string  The user’s timezone. Defaults to the company’s
   *                                             timezone. See a list of supported time zones.
   *   has_access_to_all_future_projects boolean Whether the user should be automatically added
   *                                             to future projects. Defaults to false.
   *   is_contractor                     boolean Whether the user is a contractor or an
   *                                             employee. Defaults to false.
   *   is_admin                          boolean Whether the user has admin permissions.
   *                                             Defaults to false.
   *   is_project_manager                boolean Whether the user has project manager
   *                                             permissions. Defaults to false.
   *   can_see_rates                     boolean Whether the user can see billable rates
   *                                             on projects. Only applicable to project
   *                                             managers. Defaults to false.
   *   can_create_projects               boolean Whether the user can create projects.
   *                                             Only applicable to project managers.
   *                                             Defaults to false.
   *   can_create_invoices               boolean Whether the user can create invoices
   *                                             Only applicable to project managers.
   *                                             Defaults to false.
   *   is_active                         boolean Whether the user is active or archived.
   *                                             Defaults to true.
   *   weekly_capacity                   integer The number of hours per week this person is
   *                                             available to work in seconds. Defaults
   *                                             to 126000 seconds (35 hours).
   *   default_hourly_rate               decimal The billable rate to use for this user
   *                                             when they are added to a project. Defaults to 0.
   *   cost_rate                         decimal The cost rate to use for this user
   *                                             when calculating a project’s costs vs
   *                                             billable amount. Defaults to 0.
   *   roles                             array   The role names (STRINGS) assigned to this person.
   *
   * @param  int   $user_id The user ID.
   * @param  array $args    (Default: array()) Settings to change (probably should not be ommitted).
   * @return object         The updated user.
   */
  public function update_user( $user_id, $args = array() ){
    return $this->run( "users/$user_id", $args, 'PATCH' );
  }

  /**
   * Delete a user. Deleting a user is only possible if they have no time entries or
   * expenses associated with them. Returns a 200 OK response code if the call succeeded.
   *
   * @param  int $user_id The user's ID.
   * @return object       200 OK if successful.
   */
  public function delete_user( $user_id ){
    return $this->run( "users/$user_id", array(), 'DELETE' );
  }

/*
ooooo     ooo                                  ooooooooo.                          o8o                         .              .o.                          o8o                                                                     .
`888'     `8'                                  `888   `Y88.                        `"'                       .o8             .888.                         `"'                                                                   .o8
 888       8   .oooo.o  .ooooo.  oooo d8b       888   .d88' oooo d8b  .ooooo.     oooo  .ooooo.   .ooooo.  .o888oo          .8"888.      .oooo.o  .oooo.o oooo   .oooooooo ooo. .oo.   ooo. .oo.  .oo.    .ooooo.  ooo. .oo.   .o888oo  .oooo.o
 888       8  d88(  "8 d88' `88b `888""8P       888ooo88P'  `888""8P d88' `88b    `888 d88' `88b d88' `"Y8   888           .8' `888.    d88(  "8 d88(  "8 `888  888' `88b  `888P"Y88b  `888P"Y88bP"Y88b  d88' `88b `888P"Y88b    888   d88(  "8
 888       8  `"Y88b.  888ooo888  888           888          888     888   888     888 888ooo888 888         888          .88ooo8888.   `"Y88b.  `"Y88b.   888  888   888   888   888   888   888   888  888ooo888  888   888    888   `"Y88b.
 `88.    .8'  o.  )88b 888    .o  888           888          888     888   888     888 888    .o 888   .o8   888 .       .8'     `888.  o.  )88b o.  )88b  888  `88bod8P'   888   888   888   888   888  888    .o  888   888    888 . o.  )88b
   `YbodP'    8""888P' `Y8bod8P' d888b         o888o        d888b    `Y8bod8P'     888 `Y8bod8P' `Y8bod8P'   "888"      o88o     o8888o 8""888P' 8""888P' o888o `8oooooo.  o888o o888o o888o o888o o888o `Y8bod8P' o888o o888o   "888" 8""888P'
                                                                                   888                                                                          d"     YD
                                                                               .o. 88P                                                                          "Y88888P'
                                                                               `Y888P
*/

  /**
   * Returns a list of your project assignments for the user identified by USER_ID.
   * The project assignments are returned sorted by creation date, with the most
   * recently created project assignments appearing first.
   *
   * The response contains an object with a project_assignments property that contains
   * an array of up to per_page project assignments. Each entry in the array is a
   * separate project assignment object. If no more project assignments are available,
   * the resulting array will be empty. Several additional pagination properties are
   * included in the response to simplify paginating your project assignments.
   *
   * Can be paginated.
   *
   * @param  [type] $user_id [description]
   * @return [type]          [description]
   */
  public function list_project_assignments( $user_id ){
    return $this->run( "users/$user_id/project_assignments" );
  }

  /**
   * Returns a list of your project assignments for the currently authenticated user.
   * The project assignments are returned sorted by creation date, with the most
   * recently created project assignments appearing first.
   *
   * The response contains an object with a project_assignments property that
   * contains an array of up to per_page project assignments. Each entry in the
   * array is a separate project assignment object. If no more project assignments
   * are available, the resulting array will be empty. Several additional
   * pagination properties are included in the response to simplify
   * paginating your project assignments.
   *
   * Can be paginated, but it's possible that $updated_since is not supported.
   *
   * @return object A list of the currently authenticated user's project assignments,
   *                along with some pagination properties.
   */
  public function list_my_project_assignments(){
    return $this->run( "users/me/project_assignments" );
  }

}
