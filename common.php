<?php
require_once 'config.php';

session_start();

$translation = require_once 'lang/en.php';

function __($key)
{
    global $translation;
    if (!empty($translation[$key])) {
        return $translation[$key];
    }
    return $key;
}

function dd($param)
{
    var_dump($param);
    die;
}


function conn($query, $params = [], $get_list = false)
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        dynamicBindVariables($stmt, $params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $items = [];
    if ($result) {
        if ($get_list) {
            while ($obj = $result->fetch_object()) {
                array_push($items, $obj);
            }
        } else {
            $items = $result->fetch_object();
        }
        $result->close();
    }

    return $items;
}

function bindTypes($param)
{
    $types = '';
    if (is_int($param)) {
        $types .= 'i';
    } elseif (is_float($param)) {
        $types .= 'd';
    } elseif (is_string($param)) {
        $types .= 's';
    } else {
        $types .= 'b';
    }
    return $types;
}

function dynamicBindVariables($stmt, $params)
{
    if ($params != null && !is_array($params)) {
        $param = $params;
        $type = '';
        $type .= bindTypes($params);
        $stmt->bind_param($type, $param);
    } else {
        $bind_names = [];
        $types = '';
        foreach ($params as $param) {
            $types .= bindTypes($param);
        }
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    }
    return $stmt;
}

function flash($name = '', $message = '')
{
    if (!empty($name)) {

        if (!empty($message) && empty($_SESSION['errors'][$name])) {
            if (!empty($_SESSION['errors'][$name])) {
                unset($_SESSION['errors'][$name]);
            }
            $_SESSION['errors'][$name] = $message;
        } elseif (!empty($_SESSION['errors'][$name]) && empty($message)) {
            echo $_SESSION['errors'][$name];
            unset($_SESSION['errors'][$name]);
        }
    }
}

function old($name = '', $message = '')
{
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION['old'][$name])) {
            if (!empty($_SESSION['old'][$name])) {
                unset($_SESSION['old'][$name]);
            }
            $_SESSION['old'][$name] = $message;
        } elseif (!empty($_SESSION['old'][$name]) && empty($message)) {
            $result = $_SESSION['old'][$name];
            unset($_SESSION['old'][$name]);
            return $result;
        }
    }
}

function redirect($location)
{
    header('Location: ' . $location);
    exit();
}

function url($location, $params = [])
{
    $url = BASE_URL . '/' . $location;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}
