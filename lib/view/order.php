<?php
session_start();
if (isset($_SESSION['user'])) {
    if (isset($_SESSION['usertype'])) {
        $usertype = $_SESSION['usertype'];
        if ($usertype != "Admin") {
            header('Location:../../index.php');
        }
    } else {
        header('Location:../../index.php');
    }
} else {
    header('Location:../../index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Order Management</title>
    <?php include_once('common.php') ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Order Management</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Order Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- <h4 class="mb-3">All Orders</h4> -->

                        <!-- Status Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#allOrders" role="tab">All Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#billing" role="tab">Billing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#readyDelivery" role="tab">Ready to Delivery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#delivery" role="tab">Delivery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#delivered" role="tab">Delivered</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#hold" role="tab">Hold</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cancelled" role="tab">Cancelled</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <div id="allOrders" class="tab-pane fade show active">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer Name</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allOrdersTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="billing" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="billingTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="readyDelivery" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="readyDeliveryTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="delivery" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="deliveryTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="delivered" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="deliveredTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="hold" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="holdTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div id="cancelled" class="tab-pane fade">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Mathod</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cancelledTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- View Order Details Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsContent">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="statusOrderId">
                    <div>
                        <label for="statusSelect" class="form-label">New Status</label>
                        <select class="form-select" id="statusSelect">
                            <option value="billing">Billing</option>
                            <option value="ready_to_delivery">Ready to Delivery</option>
                            <option value="delivery">Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="hold">Hold</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusBtn">Change Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify Payment Modal -->
    <div class="modal fade" id="verifyPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Bank Transfer Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="verifyOrderId">
                    <input type="hidden" id="verifySlipId">
                    <div id="slipContent">Loading...</div>

                    <div class="mt-3">
                        <label class="form-label">Rejection Reason (only if rejecting)</label>
                        <textarea class="form-control" id="rejectReason" rows="2" placeholder="E.g. amount mismatch, unclear slip"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="rejectSlipBtn">Reject</button>
                    <button type="button" class="btn btn-success" id="approveSlipBtn">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery.js"></script>
    <script>
        $(document).ready(function() {
            loadAllOrders();
            loadOrdersByStatus('billing');
            loadOrdersByStatus('ready_to_delivery');
            loadOrdersByStatus('delivery');
            loadOrdersByStatus('delivered');
            loadOrdersByStatus('hold');
            loadOrdersByStatus('cancelled');

            function buildActionButtons(order) {
                var needsVerification = (order.payment_method === 'bank_transfer' && parseInt(order.payment_verified) === 0);

                var viewBtn = '<button class="btn btn-sm btn-info btn-view" data-id="' + order.orderid + '">View</button> ';

                if (needsVerification) {
                    return viewBtn +
                        '<button class="btn btn-sm btn-secondary" disabled title="Approve payment slip first">Change Status</button> ' +
                        '<button class="btn btn-sm btn-success btn-verify-payment" data-id="' + order.orderid + '">Verify Payment</button>';
                } else {
                    return viewBtn +
                        '<button class="btn btn-sm btn-warning btn-change-status" data-id="' + order.orderid + '">Change Status</button>';
                }
            }

            function buildRow(order) {
                var statusColor = getStatusColor(order.order_status);
                var paymentBadge = order.payment_method === 'bank_transfer' ?
                    (parseInt(order.payment_verified) === 0 ?
                        '<span class="badge bg-danger">Bank Transfer (Unverified)</span>' :
                        '<span class="badge bg-success">Bank Transfer (Verified)</span>') :
                    '<span class="badge bg-secondary">Cash on Delivery</span>';

                return '<tr><td>' + order.orderid + '</td>' +
                    '<td>' + order.customer_name + '</td>' +
                    '<td>Rs.' + parseFloat(order.total).toFixed(2) + '</td>' +
                    '<td><span class="badge ' + statusColor + '">' + formatStatus(order.order_status) + '</span></td>' +
                    '<td>' + paymentBadge + '</td>' +
                    '<td>' + new Date(order.created_at).toLocaleDateString() + '</td>' +
                    '<td>' + buildActionButtons(order) + '</td></tr>';
            }

            function loadAllOrders() {
                $.ajax({
                    url: "../routes/order/vieworder.php",
                    type: 'GET',
                    dataType: 'json',
                    success: function(orders) {
                        var rows = '';
                        if (!orders || orders.length === 0) {
                            rows = '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
                        } else {
                            $.each(orders, function(index, order) {
                                rows += buildRow(order);
                            });
                        }
                        $('#allOrdersTableBody').html(rows);
                    }
                });
            }

            function loadOrdersByStatus(status) {
                $.ajax({
                    url: "../routes/order/vieworder.php?status=" + status,
                    type: 'GET',
                    dataType: 'json',
                    success: function(orders) {
                        var rows = '';
                        if (!orders || orders.length === 0) {
                            rows = '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
                        } else {
                            $.each(orders, function(index, order) {
                                rows += buildRow(order);
                            });
                        }
                        var tableId = '#' + status + 'TableBody';
                        $(tableId).html(rows);
                    }
                });
            }

            function formatStatus(status) {
                var statusMap = {
                    'billing': 'Billing',
                    'ready_to_delivery': 'Ready to Delivery',
                    'delivery': 'Delivery',
                    'delivered': 'Delivered',
                    'hold': 'Hold',
                    'cancelled': 'Cancelled',
                    'pending': 'Pending'
                };
                return statusMap[status] || status;
            }

            function getStatusColor(status) {
                var colorMap = {
                    'pending': 'bg-secondary',
                    'billing': 'bg-warning',
                    'ready_to_delivery': 'bg-info',
                    'delivery': 'bg-primary',
                    'delivered': 'bg-success',
                    'hold': 'bg-danger',
                    'cancelled': 'bg-dark'
                };
                return colorMap[status] || 'bg-secondary';
            }

            function reloadAllTables() {
                loadAllOrders();
                loadOrdersByStatus('billing');
                loadOrdersByStatus('ready_to_delivery');
                loadOrdersByStatus('delivery');
                loadOrdersByStatus('delivered');
                loadOrdersByStatus('hold');
                loadOrdersByStatus('cancelled');
            }

            // ===== View Order Details =====
            $(document).on('click', '.btn-view', function() {
                var orderId = $(this).data('id');
                $.ajax({
                    url: "../routes/order/vieworder.php?orderId=" + orderId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(order) {
                        var html = '<div><strong>Order ID:</strong> ' + order.orderid + '</div>';
                        html += '<div><strong>Customer:</strong> ' + order.customer_name + '</div>';
                        html += '<div><strong>Email:</strong> ' + order.email + '</div>';
                        html += '<div><strong>Phone:</strong> ' + order.phone + '</div>';
                        html += '<div><strong>Address:</strong> ' + order.address + ', ' + order.city + ', ' + order.postal_code + '</div>';
                        html += '<div><strong>Payment Method:</strong> ' + order.payment_method + '</div>';
                        html += '<div><strong>Status:</strong> <span class="badge ' + getStatusColor(order.order_status) + '">' + formatStatus(order.order_status) + '</span></div>';
                        html += '<div><strong>Subtotal:</strong> Rs.' + parseFloat(order.subtotal).toFixed(2) + '</div>';
                        html += '<div><strong>Delivery Fee:</strong> Rs.' + parseFloat(order.delivery_fee).toFixed(2) + '</div>';
                        html += '<div><strong>Total:</strong> Rs.' + parseFloat(order.total).toFixed(2) + '</div>';

                        if (order.items && order.items.length > 0) {
                            html += '<h6 class="mt-3">Order Items:</h6><table class="table table-sm"><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead><tbody>';
                            $.each(order.items, function(index, item) {
                                html += '<tr><td>' + item.product_name + '</td><td>Rs.' + parseFloat(item.price).toFixed(2) + '</td><td>' + item.qty + '</td><td>Rs.' + parseFloat(item.line_total).toFixed(2) + '</td></tr>';
                            });
                            html += '</tbody></table>';
                        }

                        $('#orderDetailsContent').html(html);
                        $('#viewOrderModal').modal('show');
                    }
                });
            });

            // ===== Change Status =====
            $(document).on('click', '.btn-change-status', function() {
                var orderId = $(this).data('id');
                $('#statusOrderId').val(orderId);
                $('#changeStatusModal').modal('show');
            });

            $('#confirmStatusBtn').on('click', function() {
                var orderId = $('#statusOrderId').val();
                var newStatus = $('#statusSelect').val();

                $.ajax({
                    url: "../routes/order/updateorderstatus.php",
                    type: 'POST',
                    data: {
                        orderId: orderId,
                        status: newStatus
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Order status updated successfully.');
                            $('#changeStatusModal').modal('hide');
                            reloadAllTables();
                        } else if (response.status === 'unverified') {
                            alert('Please approve the payment slip before changing status.');
                            $('#changeStatusModal').modal('hide');
                        } else {
                            alert('Could not update order status.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // ===== Verify Payment =====
            $(document).on('click', '.btn-verify-payment', function() {
                var orderId = $(this).data('id');
                $('#verifyOrderId').val(orderId);
                $('#slipContent').html('Loading...');
                $('#rejectReason').val('');

                $.ajax({
                    url: "../routes/order/getslipbyorder.php?orderId=" + orderId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(slip) {
                        if (!slip || !slip.id) {
                            $('#slipContent').html('<p class="text-danger">No slip uploaded for this order.</p>');
                            return;
                        }
                        $('#verifySlipId').val(slip.id);

                        var previewHtml = '';
                        if (slip.file_type === 'pdf') {
                            previewHtml = '<a href="../../' + slip.file_path + '" target="_blank" class="btn btn-outline-dark">Open PDF Slip</a>';
                        } else {
                            previewHtml = '<img src="../../' + slip.file_path + '" class="img-fluid rounded border" style="max-height:400px;">';
                        }

                        $('#slipContent').html(
                            '<div><strong>Order:</strong> ' + slip.orderid + '</div>' +
                            '<div><strong>Uploaded:</strong> ' + new Date(slip.uploaded_at).toLocaleString() + '</div>' +
                            '<div class="mt-3">' + previewHtml + '</div>'
                        );
                    }
                });

                $('#verifyPaymentModal').modal('show');
            });

            $('#approveSlipBtn').on('click', function() {
                reviewSlip('approved');
            });

            $('#rejectSlipBtn').on('click', function() {
                var reason = $('#rejectReason').val().trim();
                if (!reason) {
                    alert('Please provide a rejection reason.');
                    return;
                }
                reviewSlip('rejected');
            });

            function reviewSlip(decision) {
                var slipId = $('#verifySlipId').val();
                var reason = $('#rejectReason').val();

                $.ajax({
                    url: "../routes/order/reviewpaymentslip.php",
                    type: 'POST',
                    data: {
                        slipId: slipId,
                        decision: decision,
                        reason: reason
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(decision === 'approved' ? 'Payment approved.' : 'Payment rejected.');
                            $('#verifyPaymentModal').modal('hide');
                            reloadAllTables();
                        } else {
                            alert('Could not update payment status.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            }
        });
    </script>

    <?php include_once('footer.php') ?>