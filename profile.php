<?php
session_start();

if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'Customer')) {
    header('Location: login.php');
    exit();
}

include_once('lib/function/customerfunction.php');

// Helper — escapes output for XSS protection
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$custObj = new Customer();
$customerJson = $custObj->loaddatabyid($_SESSION['user']);
$customer = $customerJson ? json_decode($customerJson, true) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="container mt-5 mb-5">

        <h2 class="text-center mb-4">My Account</h2>

        <!-- Account nav tabs -->
        <ul class="nav nav-pills justify-content-center mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="profile.php">My Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_orders.php">My Orders</a>
            </li>
        </ul>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="checkout-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="checkout-section-title mb-0">My Profile</h5>
                        <button type="button" id="editProfileBtn" class="btn btn-sm btn-outline-dark">Edit</button>
                    </div>

                    <form id="profileForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" id="profileName" class="form-control" value="<?= e($customer['customerName'] ?? '') ?>" disabled required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="profileEmail" class="form-control" value="<?= e($customer['customerEmail'] ?? '') ?>" disabled required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" id="profilePhone" class="form-control" value="<?= e($customer['customerPhone'] ?? '') ?>" disabled required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIC</label>
                            <input type="text" name="nic" id="profileNic" class="form-control" value="<?= e($customer['customerNIC'] ?? '') ?>" disabled required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="profileGender" class="form-select" disabled>
                                <option value="Male" <?= (($customer['customerGender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= (($customer['customerGender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birthday</label>
                            <input type="date" name="birthday" id="profileBirthday" class="form-control" value="<?= e($customer['customerBirthday'] ?? '') ?>" disabled required>
                        </div>

                        <button type="submit" id="saveProfileBtn" class="btn btn-dark w-100" style="display:none;">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            const profileFields = ['#profileName', '#profileEmail', '#profilePhone', '#profileNic', '#profileGender', '#profileBirthday'];

            // Toggle profile form between view mode and edit mode
            $('#editProfileBtn').on('click', function() {
                profileFields.forEach(id => $(id).prop('disabled', false));
                $('#saveProfileBtn').show();
                $(this).hide();
            });

            // Save profile changes via AJAX
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'lib/routes/customer/updateProfile.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Profile updated successfully.');
                            profileFields.forEach(id => $(id).prop('disabled', true));
                            $('#saveProfileBtn').hide();
                            $('#editProfileBtn').show();
                        } else if (response.status === 'Phone number Exists') {
                            alert('That phone number is already in use.');
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

        });
    </script>
</body>

</html>