<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get menu items with error handling
try {
    $query = "SELECT * FROM menu_items ORDER BY category, name";
    $menu_items = $conn->query($query);
    
    if (!$menu_items) {
        throw new Exception("Error fetching menu items: " . $conn->error);
    }
} catch (Exception $e) {
    error_log("Menu Management Error: " . $e->getMessage());
    $menu_items = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Menu Management</h1>
                <p class="body-2">Manage restaurant menu items</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($_GET['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Add New Item Button -->
            <div class="page-actions">
                <button class="btn btn-primary" onclick="openAddModal()">
                    <ion-icon name="add-outline"></ion-icon>
                    Add New Item
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($menu_items && $menu_items->num_rows > 0): ?>
                            <?php while ($item = $menu_items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td>SSP <?php echo number_format($item['price']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $item['is_available'] ? 'available' : 'unavailable'; ?>">
                                            <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editMenuItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', '<?php echo $item['category']; ?>', <?php echo $item['price']; ?>, '<?php echo addslashes($item['description'] ?? ''); ?>', <?php echo $item['is_available'] ? 'true' : 'false'; ?>)">
                                            <ion-icon name="create-outline"></ion-icon>
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteMenuItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')">
                                            <ion-icon name="trash-outline"></ion-icon>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">No menu items found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Menu Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Menu Item</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" method="POST" action="update_menu_item.php">
                <input type="hidden" id="editItemId" name="item_id">
                
                <div class="form-group">
                    <label for="editName">Item Name</label>
                    <input type="text" id="editName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="editCategory">Category</label>
                    <select id="editCategory" name="category" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="drinks">Drinks</option>
                        <option value="desserts">Desserts</option>
                        <option value="appetizers">Appetizers</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editPrice">Price (SSP)</label>
                    <input type="number" id="editPrice" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription">Description</label>
                    <textarea id="editDescription" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editAvailable">Availability</label>
                    <select id="editAvailable" name="is_available">
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Menu Item Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Menu Item</h2>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <form id="addForm" method="POST" action="add_menu_item.php">
                <div class="form-group">
                    <label for="addName">Item Name</label>
                    <input type="text" id="addName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="addCategory">Category</label>
                    <select id="addCategory" name="category" required>
                        <option value="">Select Category</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="drinks">Drinks</option>
                        <option value="desserts">Desserts</option>
                        <option value="appetizers">Appetizers</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="addPrice">Price (SSP)</label>
                    <input type="number" id="addPrice" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="addDescription">Description</label>
                    <textarea id="addDescription" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="addAvailable">Availability</label>
                    <select id="addAvailable" name="is_available">
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <style>
        /* Page Actions */
        .page-actions {
            margin-bottom: 25px;
            display: flex;
            justify-content: flex-end;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid #27ae60;
            color: #27ae60;
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-available {
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
        }
        
        .status-unavailable {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .btn-primary {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
        }
        
        .btn-primary:hover {
            background: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: var(--smoky-black-1);
            color: var(--white);
            border: 1px solid var(--white-alpha-10);
        }
        
        .btn-secondary:hover {
            background: var(--white-alpha-10);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .modal-content {
            background: var(--eerie-black-2);
            margin: 0 auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            border: 1px solid var(--white-alpha-10);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            max-height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--white-alpha-10);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        
        .modal-header h2 {
            color: var(--gold-crayola);
            margin: 0;
            font-size: 1.8rem;
        }
        
        .close {
            color: var(--quick-silver);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: var(--gold-crayola);
        }
        
        .modal form {
            padding: 25px;
            overflow-y: auto;
            flex: 1;
            max-height: calc(100vh - 140px);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gold-crayola);
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: var(--smoky-black-1);
            border: 1px solid var(--white-alpha-10);
            border-radius: 8px;
            color: var(--white);
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--gold-crayola);
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--white-alpha-10);
            flex-shrink: 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .modal {
                padding: 10px;
            }
            
            .modal-content {
                width: 100%;
                max-height: calc(100vh - 20px);
            }
            
            .modal form {
                max-height: calc(100vh - 160px);
            }
            
            .modal-actions {
                flex-direction: column;
            }
            
            .modal-header {
                padding: 15px 20px;
            }
            
            .modal form {
                padding: 20px;
            }
        }
        
        /* Ensure textarea is properly sized */
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        /* Custom scrollbar for modal form */
        .modal form::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal form::-webkit-scrollbar-track {
            background: var(--smoky-black-1);
            border-radius: 3px;
        }
        
        .modal form::-webkit-scrollbar-thumb {
            background: var(--gold-crayola);
            border-radius: 3px;
        }
        
        .modal form::-webkit-scrollbar-thumb:hover {
            background: var(--white);
        }
    </style>
    
    <script>
        // Edit Menu Item
        function editMenuItem(id, name, category, price, description, isAvailable) {
            document.getElementById('editItemId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editCategory').value = category;
            document.getElementById('editPrice').value = price;
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editAvailable').value = isAvailable ? '1' : '0';
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Delete Menu Item
        function deleteMenuItem(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                window.location.href = `delete_menu_item.php?id=${id}`;
            }
        }
        
        // Open Add Modal
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        // Close Modals
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const addModal = document.getElementById('addModal');
            
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === addModal) {
                addModal.style.display = 'none';
            }
        }
        
        // Form submission handling
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update_menu_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Menu item updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the item.');
            });
        });
        
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('add_menu_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Menu item added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item.');
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>