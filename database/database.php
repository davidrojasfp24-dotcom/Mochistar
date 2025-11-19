<?php
class Database
{
    public static function connect($host = 'localhost', $user = 'root', $pass = '', $db = 'mochistar')
    {
        $conn = new mysqli($host, $user, $pass, $db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}
