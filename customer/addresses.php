<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user's addresses
$query = "SELECT * FROM customer_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Addresses - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header_new.php'; ?>

    <div class="customer-container">
        <div class="addresses-wrapper">
            <div class="addresses-header">
                <h1><i class="fas fa-map-marker-alt"></i> My Addresses</h1>
                <button class="btn-primary" onclick="showAddAddressModal()">
                    <i class="fas fa-plus"></i> Add New Address
                </button>
            </div>

            <div class="addresses-grid">
                <?php if (count($addresses) > 0): ?>
                    <?php foreach ($addresses as $address): ?>
                        <div class="address-card">
                            <?php if ($address['is_default']): ?>
                                <span class="default-badge"><i class="fas fa-star"></i> Default</span>
                            <?php endif; ?>
                            
                            <h3><?php echo htmlspecialchars($address['label']); ?></h3>
                            <p class="address-line"><?php echo htmlspecialchars($address['address_line']); ?></p>
                            <p class="address-city"><?php echo htmlspecialchars($address['city'] . ', ' . $address['state']); ?></p>
                            <p class="address-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($address['phone']); ?></p>

                            <div class="address-actions">
                                <?php if (!$address['is_default']): ?>
                                    <button class="btn-secondary" onclick="setDefault(<?php echo $address['id']; ?>)">
                                        <i class="fas fa-star"></i> Set Default
                                    </button>
                                <?php endif; ?>
                                <button class="btn-secondary" onclick="editAddress(<?php echo $address['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-danger" onclick="deleteAddress(<?php echo $address['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>No addresses saved</h3>
                        <p>Add your delivery addresses for faster checkout</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddressModal()">&times;</span>
            <h2 id="modalTitle"><i class="fas fa-map-marker-alt"></i> Add New Address</h2>
            
            <form id="addressForm">
                <input type="hidden" id="address_id">
                
                <div class="form-group">
                    <label>Address Label *</label>
                    <input type="text" id="label" placeholder="e.g., Home, Office" required>
                </div>

                <div class="form-group">
                    <label>Street Address *</label>
                    <input type="text" id="address_line" placeholder="Enter your address" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" id="city" value="Juba" required>
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <input type="text" id="state" value="Central Equatoria" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" id="phone" required>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" id="is_default">
                        Set as default address
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeAddressModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="../assets/js/addresses.js"></script>
</body>
</html>
