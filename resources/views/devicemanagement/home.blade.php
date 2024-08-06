<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Management System</title>
    <link rel="stylesheet" href="app.css">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .sidebar {
        position: fixed;
        width: 200px;
        height: 100%;
        background-color: #333;
        color: white;
        padding-top: 20px;
    }

    .sidebar .logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 10px 20px;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .sidebar ul li a:hover {
        background-color: #575757;
    }

    .main-content {
        margin-left: 200px;
        padding: 20px;
    }


    .header {
        display: flex;
        justify-content: flex-end;
        padding: 10px;
    }

    .header .user {
        display: flex;
        align-items: center;
    }

    .header .user span {
        margin-right: 10px;
    }

    .header .user-icon {
        width: 30px;
        height: 30px; 
        background-color: grey;
        border-radius: 50%;
    }

    .content {
        text-align: center;
        margin-top: 50px;
    }

    .title {
        font-size: 24px;
        margin-bottom: 40px;
    }

    .buttons button {
        background-color: #d3d3d3;
        border: none;
        padding: 15px 30px;
        margin: 10px;
        cursor: pointer;
        font-size: 16px;
    }
    
    .buttons button:hover {
        background-color: #bbb;
    }

        </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">LOGO</div>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="register">Register a device</a></li>
            <li><a href="update">Update a device</a></li>
            <li><a href="checkdevice">Check a device</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <div class="user">
                <span>username</span>
                <div class="user-icon"></div>
            </div>
        </div>
        <div class="content">
            <div class="title">
                Device Management System<br>
                Ministry of Home Affairs<br>
                Sri Lanka
            </div>
            <div class="buttons">
                <button onclick="window.location.href='register'">Register a device</button>
                <button onclick="window.location.href='checkdevice'">Check a device</button>
            </div>
        </div>
    </div>
</body>
</html>
