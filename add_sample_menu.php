<?php
require_once 'config/database.php';

echo "<h2>Adding Sample Menu Items...</h2>";

try {
    $conn = getDBConnection();
    
    // Clear existing menu items
    $conn->query("DELETE FROM menu_items");
    
    // Sample menu items
    $menu_items = [
        // Breakfast
        ['Ful Medames Sudanese', 'breakfast', 'Traditional South Sudanese fava beans cooked with onions, tomatoes, and spices, served with fresh bread and tahini', 5500, 'available', 'breakfast1.jpg'],
        ['Kisra with Stew', 'breakfast', 'Authentic South Sudanese fermented sorghum flatbread served with rich meat and vegetable stew', 6200, 'available', 'breakfast2.jpg'],
        ['Asida with Honey', 'breakfast', 'Traditional South Sudanese porridge made from wheat flour, served with local honey and fresh milk', 4800, 'available', 'breakfast3.jpg'],
        ['Sudanese Pancakes', 'breakfast', 'Light and fluffy pancakes South Sudanese style, served with date syrup and fresh butter', 5800, 'available', 'breakfast4.jpg'],
        
        // Lunch
        ['Grilled Chicken Salad', 'lunch', 'Fresh mixed greens with grilled chicken, cherry tomatoes, and vinaigrette', 7200, 'available'],
        ['Club Sandwich', 'lunch', 'Triple-decker sandwich with turkey, bacon, lettuce, and tomato', 6800, 'available'],
        ['Fish & Chips', 'lunch', 'Beer-battered cod with crispy fries and mushy peas', 8500, 'available'],
        ['Caesar Wrap', 'lunch', 'Grilled chicken Caesar salad wrapped in a flour tortilla', 6200, 'available'],
        
        // Dinner
        ['Grilled Salmon', 'dinner', 'Atlantic salmon with lemon herb butter and seasonal vegetables', 12500, 'available'],
        ['Ribeye Steak', 'dinner', 'Premium ribeye steak with garlic mashed potatoes', 15800, 'available'],
        ['Chicken Parmesan', 'dinner', 'Breaded chicken breast with marinara sauce and mozzarella', 9800, 'available'],
        ['Lamb Chops', 'dinner', 'Herb-crusted lamb chops with mint sauce and roasted vegetables', 14200, 'available'],
        
        // Drinks
        ['Fresh Orange Juice', 'drinks', 'Freshly squeezed orange juice', 2800, 'available'],
        ['Iced Coffee', 'drinks', 'Cold brew coffee served over ice', 2200, 'available'],
        ['Smoothie Bowl', 'drinks', 'Mixed berry smoothie with granola and fresh fruit', 4500, 'available'],
        ['Lemonade', 'drinks', 'Fresh homemade lemonade', 1800, 'available'],
        
        // Desserts
        ['Chocolate Lava Cake', 'desserts', 'Warm chocolate cake with molten center and vanilla ice cream', 5200, 'available'],
        ['Tiramisu', 'desserts', 'Classic Italian dessert with coffee-soaked ladyfingers', 4800, 'available'],
        ['Cheesecake', 'desserts', 'New York style cheesecake with berry compote', 4200, 'available'],
        ['Ice Cream Sundae', 'desserts', 'Three scoops of ice cream with toppings and whipped cream', 3500, 'available']
    ];
    
    $stmt = $conn->prepare("INSERT INTO menu_items (name, category, description, price, is_available, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($menu_items as $item) {
        $is_available = 1; // Convert 'available' to boolean
        $stmt->bind_param("sssdis", $item[0], $item[1], $item[2], $item[3], $is_available, $item[5]);
        if ($stmt->execute()) {
            echo "✅ Added: " . $item[0] . " - " . $item[3] . " SSP<br>";
        } else {
            echo "❌ Failed to add: " . $item[0] . "<br>";
        }
    }
    
    echo "<br><h3>✅ Sample menu items added successfully!</h3>";
    echo "<p><a href='customer/menu.php' style='background: #ffd700; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Menu</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<p><a href="index.php">← Back to Website</a></p>