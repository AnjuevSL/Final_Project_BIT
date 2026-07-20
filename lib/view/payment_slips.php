<?php
session_start();
if (isset($_SESSION['user'])) {
    if (isset($_SESSION['usertype'])) {
        $usertype = $_SESSION['usertype'];
        if ($usertype != "Admin") {
            header('Location:../../login.php');
        }
    } else {
        header('Location:../../login.php');
    }
} else {
    header('Location:../../login.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Payment Slips</title>
    <?php include_once('common.php') ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Payment Slips</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Payment Slips</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Status Filter Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-filter="all" href="#">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-filter="pending" href="#">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-filter="approved" href="#">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-filter="rejected" href="#">Rejected</a>
                            </li>
                        </ul>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Slip</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                    <th>Reviewed By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="slipsTableBody">
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- View / Review Slip Modal -->
    <div class="modal fade" id="slipModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalSlipId">
                    <div id="slipModalContent">Loading...</div>

                    <div class="mt-3" id="rejectReasonWrap" style="display:none;">
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="modalRejectReason" rows="2" placeholder="E.g. amount mismatch, unclear slip"></textarea>
                    </div>
                </div>
                <div class="modal-footer" id="slipModalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery.js"></script>
    <script>
        $(document).ready(function() {
            var allSlips = [];
            var currentFilter = 'all';

            loadSlips();

            function loadSlips() {
                $.ajax({
                    url: "../routes/order/getallslips.php",
                    type: 'GET',
                    dataType: 'json',
                    success: function(slips) {
                        allSlips = slips || [];
                        renderSlips();
                    }
                });
            }

            function renderSlips() {
                var filtered = currentFilter === 'all' ?
                    allSlips :
                    allSlips.filter(function(s) {
                        return s.status === currentFilter;
                    });

                var rows = '';
                if (filtered.length === 0) {
                    rows = '<tr><td colspan="8" class="text-center">No slips found</td></tr>';
                } else {
                    $.each(filtered, function(index, slip) {
                        rows += buildSlipRow(slip);
                    });
                }
                $('#slipsTableBody').html(rows);
            }

            function statusBadge(status) {
                var map = {
                    pending: '<span class="badge bg-warning text-dark">Pending</span>',
                    approved: '<span class="badge bg-success">Approved</span>',
                    rejected: '<span class="badge bg-danger">Rejected</span>'
                };
                return map[status] || '<span class="badge bg-secondary">' + status + '</span>';
            }

            function buildSlipRow(slip) {
                var thumb = slip.file_type === 'pdf' ?
                    '<span class="badge bg-dark">PDF</span>' :
                    '<img src="../../' + slip.file_path + '" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">';

                var actionBtn = '<button class="btn btn-sm btn-info btn-view-slip" data-id="' + slip.id + '">View</button>';

                return '<tr>' +
                    '<td>' + slip.orderid + '</td>' +
                    '<td>' + slip.customer_name + '</td>' +
                    '<td>Rs.' + parseFloat(slip.total).toFixed(2) + '</td>' +
                    '<td>' + thumb + '</td>' +
                    '<td>' + statusBadge(slip.status) + '</td>' +
                    '<td>' + new Date(slip.uploaded_at).toLocaleString() + '</td>' +
                    '<td>' + (slip.reviewed_by || '-') + '</td>' +
                    '<td>' + actionBtn + '</td></tr>';
            }

            // ===== Filter tabs =====
            $(document).on('click', '.nav-link[data-filter]', function(e) {
                e.preventDefault();
                $('.nav-link[data-filter]').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                renderSlips();
            });

            // ===== View slip modal =====
            $(document).on('click', '.btn-view-slip', function() {
                var slipId = $(this).data('id');
                var slip = allSlips.find(function(s) {
                    return String(s.id) === String(slipId);
                });
                if (!slip) return;

                $('#modalSlipId').val(slip.id);

                var previewHtml = slip.file_type === 'pdf' ?
                    '<a href="../../' + slip.file_path + '" target="_blank" class="btn btn-outline-dark">Open PDF Slip</a>' :
                    '<img src="../../' + slip.file_path + '" class="img-fluid rounded border" style="max-height:400px;">';

                var infoHtml =
                    '<div><strong>Order ID:</strong> ' + slip.orderid + '</div>' +
                    '<div><strong>Customer:</strong> ' + slip.customer_name + '</div>' +
                    '<div><strong>Total:</strong> Rs.' + parseFloat(slip.total).toFixed(2) + '</div>' +
                    '<div><strong>Status:</strong> ' + statusBadge(slip.status) + '</div>' +
                    '<div><strong>Uploaded:</strong> ' + new Date(slip.uploaded_at).toLocaleString() + '</div>';

                if (slip.status !== 'pending') {
                    infoHtml += '<div><strong>Reviewed By:</strong> ' + (slip.reviewed_by || '-') + '</div>';
                    infoHtml += '<div><strong>Reviewed At:</strong> ' + (slip.reviewed_at ? new Date(slip.reviewed_at).toLocaleString() : '-') + '</div>';
                    if (slip.status === 'rejected' && slip.rejection_reason) {
                        infoHtml += '<div><strong>Rejection Reason:</strong> ' + slip.rejection_reason + '</div>';
                    }
                }

                infoHtml += '<div class="mt-3">' + previewHtml + '</div>';

                $('#slipModalContent').html(infoHtml);

                // Only pending slips get Approve/Reject actions
                if (slip.status === 'pending') {
                    $('#rejectReasonWrap').show();
                    $('#modalRejectReason').val('');
                    $('#slipModalFooter').html(
                        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                        '<button type="button" class="btn btn-danger" id="modalRejectBtn">Reject</button>' +
                        '<button type="button" class="btn btn-success" id="modalApproveBtn">Approve</button>'
                    );
                } else {
                    $('#rejectReasonWrap').hide();
                    $('#slipModalFooter').html(
                        '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>'
                    );
                }

                $('#slipModal').modal('show');
            });

            // ===== Approve / Reject (delegated, buttons rebuilt each time) =====
            $(document).on('click', '#modalApproveBtn', function() {
                reviewSlip('approved');
            });

            $(document).on('click', '#modalRejectBtn', function() {
                var reason = $('#modalRejectReason').val().trim();
                if (!reason) {
                    alert('Please provide a rejection reason.');
                    return;
                }
                reviewSlip('rejected');
            });

            function reviewSlip(decision) {
                var slipId = $('#modalSlipId').val();
                var reason = $('#modalRejectReason').val();

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
                            $('#slipModal').modal('hide');
                            loadSlips();
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