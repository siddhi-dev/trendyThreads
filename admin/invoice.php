<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/Session.php';
require_once '../classes/Utils.php';

if (!Session::isAdminLoggedIn()) {
    Session::redirect('admin/login.php');
}

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) die("Order ID required");

$orderObj = new Order();
$order = $orderObj->getById($id);
if (!$order) die("Order not found");

// Fetch User details
$db = new Database();
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $order['user_id']);
$user = $db->single();

$items = $orderObj->getDetails($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order['order_number']; ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #555; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #f8fafc; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; font-size: 20px; color: #4f46e5; }
        
        .btn-print { background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
        
        @media print {
            .btn-print { display: none; }
            .invoice-box { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 20px;">
        <button class="btn-print" onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <span style="color: #4f46e5">NAMRATA</span>
                            </td>
                            <td>
                                Invoice #: <?php echo $order['order_number']; ?><br>
                                Created: <?php echo date('M d, Y', strtotime($order['created_at'])); ?><br>
                                Status: <?php echo ucfirst($order['status']); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Shipping Address:</strong><br>
                                <?php echo $user['full_name']; ?><br>
                                <?php echo nl2br($order['shipping_address']); ?><br>
                                Phone: <?php echo $order['phone']; ?>
                            </td>
                            <td>
                                <strong>NAMRATA CLOTHING</strong><br>
                                123 Fashion Street<br>
                                Mumbai, MH 400001<br>
                                support@namrata.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>

            <?php foreach($items as $item): ?>
            <tr class="item">
                <td><?php echo $item['product_name']; ?> x <?php echo $item['quantity']; ?></td>
                <td><?php echo Utils::formatPrice($item['price'] * $item['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>

            <tr class="total">
                <td></td>
                <td>Total: <?php echo Utils::formatPrice($order['total_amount']); ?></td>
            </tr>
        </table>
        
        <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
            Thank you for shopping with Namrata!
        </div>
    </div>
</body>
</html>
