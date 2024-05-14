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

    $sql = "CREATE TABLE $table_name(
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

function pdo_connection()
{
    global $wpdb;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        return new WP_Error('database_connection_failed', 'Database connection failed: ' . $e->getMessage());
    }
}

function get_form_submissions_ens()
{
    $pdo = pdo_connection();
    if (is_wp_error($pdo)) {
        return $pdo;
    }
    global $wpdb;
    try {
        $stmt = $pdo->query("SELECT * FROM {$wpdb->prefix}form_submissions");
        $results = $stmt->fetchAll();

        if (empty($results)) {
            return new WP_Error('not_found', 'not found any', array('status' => 404));
        }
        return $results;
    } catch (PDOException $e) {
        return new WP_Error('database_query_failed', 'Database query failed: ' . $e->getMessage());
    }
}

function create_form_submission_ens($request)
{
    if (!name_validation($request["name"]) || !email_validation($request["email"])) {
        return throw new Exception("not valid data");
    }

    $pdo = pdo_connection();
    if (is_wp_error($pdo)) {
        return $pdo;
    }
    global $wpdb;
    try {
        $stmt = $pdo->prepare("INSERT INTO {$wpdb->prefix}form_submissions (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $request['name']);
        $stmt->bindParam(':email', $request['email']);
        $stmt->execute();

        $new_data = array(
            'id' => $pdo->lastInsertId(),
            'name' => esc_sql($request['name']),
            'email' => sanitize_email($request['email'])
        );
        return $new_data;
    } catch (PDOException $e) {
        return new WP_Error('insert_failed', 'can not post data: ' . $e->getMessage(), array('status' => 500));
    }
}

function get_form_submission_ens($request)
{
    $id = intval($request['id']);
    $pdo = pdo_connection();
    if (is_wp_error($pdo)) {
        return $pdo;
    }
    global $wpdb;
    try {
        $stmt = $pdo->prepare("SELECT * FROM {$wpdb->prefix}form_submissions WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if (empty($result)) {
            return new WP_Error('not_found', 'not found any', array('status' => 404));
        }

        return $result;
    } catch (PDOException $e) {
        return new WP_Error('database_query_failed', 'Database query failed: ' . $e->getMessage());
    }
}

function update_form_submission_ens($request)
{
    if (!name_validation($request["name"]) || !email_validation($request["email"])) {
        return throw new Exception("not valid data");
    }

    $id = intval($request['id']);
    $name = $request["name"];
    $email = $request["email"];
    $pdo = pdo_connection();
    if (is_wp_error($pdo)) {
        return $pdo;
    }
    global $wpdb;
    try {
        if (empty($email) && empty($name)) {
            return "nothing changed";
        } else if (empty($email)) {
            $stmt = $pdo->prepare("UPDATE {$wpdb->prefix}form_submissions SET name = :name WHERE id = :id");
            $stmt->bindParam(':name', $name);
        } else if (empty($name)) {
            $stmt = $pdo->prepare("UPDATE {$wpdb->prefix}form_submissions SET email = :email WHERE id = :id");
            $stmt->bindParam(':email', $email);
        } else {
            $stmt = $pdo->prepare("UPDATE {$wpdb->prefix}form_submissions SET name = :name, email = :email WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $updated_data = array(
            'id' => intval($id),
            'name' => esc_sql($name),
            'email' => sanitize_email($email)
        );

        return $updated_data;
    } catch (PDOException $e) {
        return new WP_Error('database_query_failed', 'Database query failed: ' . $e->getMessage());
    }
}

function delete_form_submission_ens($request)
{
    $id = intval($request['id']);
    $pdo = pdo_connection();
    if (is_wp_error($pdo)) {
        return $pdo;
    }
    global $wpdb;
    try {
        $stmt = $pdo->prepare("DELETE FROM {$wpdb->prefix}form_submissions WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result === 0) {
            return new WP_Error('not_found', 'not found any', array('status' => 404));
        }

        $response = array(
            'status' => 'success',
            'message' => 'Successfully deleted'
        );

        return $response;
    } catch (PDOException $e) {
        return new WP_Error('database_query_failed', 'Database query failed: ' . $e->getMessage());
    }
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