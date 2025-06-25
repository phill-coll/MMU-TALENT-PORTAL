<?php
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = "";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle purchase and clear cart
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
    $_SESSION['cart'] = [];
    $message = "Payment successful! Your cart has been cleared.";
    $showPopup = true;
}

// Fetch item list from shopping_cart table for this user
$items = [];
$sql = "SELECT item_name, item_price FROM shopping_cart";
$result = mysqli_query($conn, $sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[$row['item_name']] = (float)$row['item_price'];
    }
}

// Handle item addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_name'], $_POST['item_quantity'])) {
    $name = trim($_POST['item_name']);
    $quantity = intval($_POST['item_quantity']);
    $price = 0;

    if (isset($items[$name])) {
        $price = $items[$name];
    } else {
        $message = "<div class='error'>Invalid item selected.</div>";
    }

    if ($name !== "" && $quantity > 0 && $price > 0) {
        $_SESSION['cart'][] = [
            'name' => $name,
            'quantity' => $quantity,
            'price' => $price
        ];
        $message = "Item added successfully!";
    } else {
        $message = "<div class='error'>Please fill out all fields correctly.</div>";
    }
}

// Handle removal
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/cart.css">
    <script>
        const itemPrices = <?= json_encode($items) ?>;
    </script>
</head>

<body>
    <div class="page-wrapper">
        <div class="container">
            <h1>Shopping Cart</h1>

            <?php if (!empty($message)) echo "<div class='success'>$message</div>"; ?>

            <form method="post" class="add-form" id="cartForm">
                <label for="item_name">Choose Item:</label>
                <input list="itemList" name="item_name" id="item_name" placeholder="Start typing or select" required>
                <datalist id="itemList">
                    <?php foreach ($items as $item => $price): ?>
                        <option value="<?= htmlspecialchars($item) ?>"><?= htmlspecialchars($item) ?></option>
                    <?php endforeach; ?>
                </datalist>

                <input type="number" name="item_quantity" placeholder="Quantity" min="1" value="1" required>
                <input type="number" step="0.01" name="item_price" id="item_price" placeholder="Price (RM)" readonly required>

                <button type="submit">Add Item</button>
            </form>

            <div class="cart-items">
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <div class="cart-item">
                            <div class="item-info">
                                <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="type">Qty: <?= htmlspecialchars($item['quantity']) ?></div>
                            </div>
                            <div class="price">RM <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            <div class="actions">
                                <a href="?remove=<?= $index ?>" class="delete-btn">
                                    <img src="images/trash-icon.png" alt="Delete" class="trash-icon">
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                    ?>
                    <div class="total-purchase-box">
                        <div class="total-price">
                            <strong>Total: RM <?= number_format($total, 2) ?></strong>
                        </div>
                        <div class="actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="purchase" value="1">
                                <button type="submit" class="purchase-btn">Purchase</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Purchase Confirmation Popup -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <p>Payment Successful!</p>
            <button onclick="closePopup()">OK</button>
        </div>
    </div>

    <script src="js/cart.js"></script>
    <script>
        // Autofill price when item name is typed or selected
        document.getElementById("item_name").addEventListener("input", function() {
            const name = this.value.trim();
            if (itemPrices[name] !== undefined) {
                document.getElementById("item_price").value = itemPrices[name];
            } else {
                document.getElementById("item_price").value = "";
            }
        });

        <?php if ($showPopup): ?>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("popup").style.display = "flex";
            });
        <?php endif; ?>

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>


    <?php include 'footer.inc.php'; ?>
</body>

</html>