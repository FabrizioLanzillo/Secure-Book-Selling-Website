<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();
// Check if a normal user tries to access this page
$accessControlManager->redirectIfNormalUser();

$result = getAllCustomersData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <title>Book Selling - Home Admin</title>
</head>
<body>
<?php
include "./../layout/header.php";
?>

<div class="d-flex">

    <aside class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 20rem;">
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/homeAdmin.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-book"></i>
                    Books
                </a>
            </li>
            <li>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/orderList.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-list"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/customerList.php'); ?>"
                   class="nav-link active" aria-current="page">
                    <i class="fas fa-users"></i>
                    Customers
                </a>
            </li>
            <li class="nav-item">
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/profile.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-user"></i>
                    Admin
                </a>
            </li>
        </ul>
        <hr>
    </aside>

    <main class="container bg-secondary mt-4 p-4">

        <h1 class="text-white">Customers</h1>

        <table class="table table-light table-striped mt-4">
            <thead>
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Username</th>
                <th>Email</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result) {
                if ($result->num_rows >= 1) {
                    while ($customer = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['surname']); ?></td>
                            <td><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars('./deleteCustomer.php?user_id=' . $customer['id']); ?>">
                                    <button class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash"></i></button>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <div class='alert alert-danger mt-4'>Users not found in the database</div>
                    <?php
                }
            } else {
                ?>
                <div class='alert alert-danger mt-4'>Error retrieving book details</div>
                <?php
            }
            ?>
            </tbody>
        </table>

    </main>
</div>
</body>
</html>