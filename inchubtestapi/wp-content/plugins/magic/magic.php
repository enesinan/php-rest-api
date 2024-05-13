<?php
/*
 * Plugin Name: Magic API
 * Plugin URI: http://localhost/inchubtestapi
 * Description: just basic rest api, tested with postman
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Enes
 * Author URI: http://ornek.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: http://localhost/inchubtestapi
 * Text Domain: magic
 * Domain Path: /languages
 */

// for safety, prevent directly access to plugin 
if (!defined('WPINC')) {
    exit("should not access");
}


register_activation_hook(__FILE__, 'setup_table');

function setup_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    $sql = "CREATE TABLE `$table_name`(
        id INT(9) NOT NULL AUTO_INCREMENT,
        name varchar(99) NOT NULL,
        email varchar(99) NOT NULL,
        PRIMARY KEY (id)
    )";

    require (ABSPATH . "wp-admin/includes/upgrade.php");
    dbDelta($sql);
}

add_action("rest_api_init", "register_routes");
function register_routes()
{
    // GET ALL
    register_rest_route(
        "/api/v1",
        '/form-submissions',
        array(
            "methods" => WP_REST_SERVER::READABLE,
            "callback" => 'get_form_submissions_ens',
            'permission_callback' => '__return_true',
        )
    );
    // GET ONE
    register_rest_route(
        "api/v1",
        "/form-submission/(?P<id>\d+)",
        array(
            "methods" => WP_REST_SERVER::READABLE,
            "callback" => 'get_form_submission_ens',
            'permission_callback' => '__return_true',
        )
    );
    // POST DATA
    register_rest_route(
        "api/v1",
        '/form-submissions',
        array(
            "methods" => WP_REST_SERVER::CREATABLE,
            "callback" => 'create_form_submission_ens',
            'permission_callback' => '__return_true',
        )
    );
    // UPDATE DATA
    register_rest_route(
        "api/v1",
        '/form-submission/(?P<id>\d+)',
        array(
            "methods" => WP_REST_SERVER::EDITABLE,
            "callback" => 'update_form_submission_ens',
            'permission_callback' => '__return_true',
        )
    );
    // DELETE DATA
    register_rest_route(
        "api/v1",
        '/form-submission/(?P<id>\d+)',
        array(
            "methods" => WP_REST_SERVER::DELETABLE,
            "callback" => 'delete_form_submission_ens',
            'permission_callback' => '__return_true',
        )
    );
}
// set unique identifier to all functions with _ens, for prevent the plugin conflictions 
function get_form_submissions_ens()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    if (empty($results)) {
        return new WP_Error('not_found', 'not found any', array('status' => 404));
    }
    return $results;
}

function create_form_submission_ens($request)
{
    if (!name_validation($request["name"]) || !email_validation($request["email"])) {
        return throw new Exception("not valid data");
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    $rows = $wpdb->insert(
        $table_name,
        array(
            'name' => $request['name'],
            'email' => $request['email'],
        )
    );

    if ($rows) {
        $new_data = array(
            'id' => $wpdb->insert_id,
            'name' => esc_sql($request['name']),
            'email' => sanitize_email($request['email'])
        );
        return $new_data;
    } else {
        return new WP_Error('insert_failed', 'can not post data', array('status' => 500));
    }
}

function get_form_submission_ens($request)
{
    $id = intval($request['id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $table_name . ' WHERE id=%d', $id));

    if (empty($results)) {
        return new WP_Error('not_found', 'not found any', array('status' => 404));
    }

    return $results[0];
}

function update_form_submission_ens($request)
{
    if (!name_validation($request["name"]) || !email_validation($request["email"])) {
        return throw new Exception("not valid data");
    }

    $id = intval($request['id']);
    $name = $request["name"];
    $email = $request["email"];
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    if (empty($email) && empty($name)) {
        return "nothing changed";
    } else if (empty($email)) {
        $data = array('name' => $name);
    } else if (empty($name)) {
        $data = array('email' => $email);
    } else {
        $data = array('name' => $name, 'email' => $email);
    }

    $results = $wpdb->update(
        $table_name,
        $data,
        array('id' => $id)
    );

    if ($results === false) {
        return new WP_Error('not_found', 'not found any', array('status' => 404));
    }

    $updated_data = array(
        'id' => intval($id),
        'name' => esc_sql($name),
        'email' => sanitize_email($email)
    );

    return $updated_data;
}

function delete_form_submission_ens($request)
{
    $id = intval($request['id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    $result = $wpdb->delete($table_name, array('id' => $id));

    if ($result === 0) {
        return new WP_Error('not_found', 'not found any', array('status' => 404));
    }

    $response = array(
        'status' => 'success',
        'message' => 'Successfully deleted'
    );

    return $response;
}

function name_validation($param)
{
    $clear_param = esc_sql($param);
    if (preg_match('/^[a-zA-Z\s]+$/u', $clear_param)) {
        return true;
    }

    return false;
}

function email_validation($param)
{
    $clear_param = sanitize_email($param);
    if ((filter_var($clear_param, FILTER_VALIDATE_EMAIL))) {
        return true;
    }

    return false;
}