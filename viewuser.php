<?php
session_start();

include_once "./controllers/db_connection.php";

$sql = "SELECT * FROM user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" type="text/css" href="./custom_css/manageuser.css">
    <style>
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .btn, .action-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .btn:hover, .action-btn:hover {
            background-color: #3276b1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions .action-btn {
            flex: 1;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Management</h1>
    <a class="btn" href="adduser.php">Add User</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><?php echo $row['user_fullName']; ?></td>
            <td><?php echo $row['user_phone']; ?></td>
            <td class="actions">
                <a class="action-btn" href="edituser.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="action-btn" href="deleteuser.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
	
        <?php endwhile; ?>

    </table>
<div class="actions">
            <a class="action-btn" href="profile.php" >Back to Profile</a>
        </div>
</div>

</body>
</html>
