<!DOCTYPE html>
<html>

<head>
    <title>Order Confirmation</title>
</head>

<body>

    <h1>Thank You for Your Order!</h1>

    <p>Hello,</p>

    <p>We are pleased to inform you that your order has been received and is now being processed.</p>

    <h3>Order Details:</h3>
    <ul>
        <li><strong>Order ID:</strong> {{ $orderId }}</li>
        <li><strong>Status:</strong> {{ $status }}</li>
    </ul>

    <p>If you have any questions or concerns regarding your order, please feel free to reply to this email.</p>

</body>

</html>