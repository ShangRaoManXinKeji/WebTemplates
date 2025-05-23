<?php
session_start();
$config = require '../config/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = '用户名和密码不能为空';
    } else {
        $conn = new mysqli('localhost', 'root', 'your_password', 'your_db');
        if ($conn->connect_error) {
            die('数据库连接失败: ' . $conn->connect_error);
        }
        $stmt = $conn->prepare('SELECT id, password, role FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hash_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = '用户名或密码错误';
            }
        } else {
            $error = '用户名或密码错误';
        }

        $stmt->close();
        $conn->close();
    }
}

include '../../public/login.php';