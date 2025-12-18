<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get tables
$query = "SELECT * FROM tables ORDER BY table_number";
$tables = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tables Management - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Tables Management</h1>
                <p class="body-2">Manage restaurant tables and seating</p>
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

            <!-- Add New Table Button -->
            <div class="page-actions">
                <button class="btn btn-primary" onclick="openAddModal()">
                    <ion-icon name="add-outline"></ion-icon>
                    Add New Table
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Table Number</th>
                            <th>Capacity</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tables->num_rows > 0): ?>
                            <?php while ($table = $tables->fetch_assoc()): ?>
                                <tr>
                                    <td>Table <?php echo htmlspecialchars($table['table_number']); ?></td>
                                    <td><?php echo $table['capacity']; ?> people</td>
                                    <td><?php echo ucfirst($table['location']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $table['status']; ?>">
                                            <?php echo ucfirst($table['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editTable(<?php echo $table['id']; ?>, '<?php echo addslashes($table['table_number']); ?>', <?php echo $table['capacity']; ?>, '<?php echo $table['location']; ?>', '<?php echo $table['status']; ?>')">
                                            <ion-icon name="create-outline"></ion-icon>
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteTable(<?php echo $table['id']; ?>, '<?php echo addslashes($table['table_number']); ?>')">
                                            <ion-icon name="trash-outline"></ion-icon>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">No tables found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Table Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Table</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" method="POST" action="update_table.php">
                <input type="hidden" id="editTableId" name="table_id">
                
                <div class="form-group">
                    <label for="editTableNumber">Table Number</label>
                    <input type="text" id="editTableNumber" name="table_number" required>
                </div>
                
                <div class="form-group">
                    <label for="editCapacity">Capacity (People)</label>
                    <input type="number" id="editCapacity" name="capacity" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label for="editLocation">Location</label>
                    <select id="editLocation" name="location" required>
                        <option value="indoor">Indoor</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editStatus">Status</label>
                    <select id="editStatus" name="status" required>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Table</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Table Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Table</h2>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <form id="addForm" method="POST" action="add_table.php">
                <div class="form-group">
                    <label for="addTableNumber">Table Number</label>
                    <input type="text" id="addTableNumber" name="table_number" placeholder="e.g., T01, A1, etc." required>
                </div>
                
                <div class="form-group">
                    <label for="addCapacity">Capacity (People)</label>
                    <input type="number" id="addCapacity" name="capacity" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label for="addLocation">Location</label>
                    <select id="addLocation" name="location" required>
                        <option value="">Select Location</option>
                        <option value="indoor">Indoor</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="addStatus">Status</label>
                    <select id="addStatus" name="status">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Table</button>
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
        
        .status-occupied {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .status-reserved {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-maintenance {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
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
        .form-group select {
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
        .form-group select:focus {
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
    </style>

    <script>
        // Edit Table
        function editTable(id, tableNumber, capacity, location, status) {
            document.getElementById('editTableId').value = id;
            document.getElementById('editTableNumber').value = tableNumber;
            document.getElementById('editCapacity').value = capacity;
            document.getElementById('editLocation').value = location;
            document.getElementById('editStatus').value = status;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Delete Table
        function deleteTable(id, tableNumber) {
            if (confirm(`Are you sure you want to delete "${tableNumber}"?`)) {
                window.location.href = `delete_table.php?id=${id}`;
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
            
            fetch('update_table.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Table updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the table.');
            });
        });
        
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('add_table.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Table added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the table.');
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>