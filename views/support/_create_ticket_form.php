<?php $ticket = $ticket ?? null; ?>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="modal fade in" id="lmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-backdrop="static" data-keyboard="false" style="display: none;">
    <div class="modal-dialog" style="width:45% !important;" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0); border: 0;">
            <div class="modal-body" style="height:400px;">
                <div id="loader" style="text-align: center;">
                    <i class="ace-icon fa fa-spinner fa-spin blue" style="font-size: 100px"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content" style="margin: -30px;">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php">Ticketing</a></li>
                <li class="active"><?= $ticket ? 'Update Ticket' : 'Create Ticket' ?></li>
            </ul>
        </div>

        <div class="page-content">
            <form id="ticket-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                <input type="hidden" name="created_by" value="<?= $_SESSION['user_array']['id'] ?>" />
                <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?? '' ?>" />

                <fieldset style="padding: 17px;">
                    <div class="widget-box">
                        <div class="widget-header widget-header-blue widget-header-flat toggle-header"
                            style="cursor:pointer;">
                            <h4 class="widget-title lighter">
                                <?= $ticket ? 'Update Ticket Information' : 'Ticket Information' ?></h4>
                            <div class="widget-toolbar">
                                Ticket No. <span><?= $ticket['id'] ?? $tk_no ?? 'N/A' ?></span>
                            </div>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Title (Subject)</label>
                                        <input type="text" class="form-control" name="title" required
                                            placeholder="Enter Ticket Title"
                                            value="<?= htmlspecialchars($ticket['title'] ?? '') ?>" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Assigned To</label>
                                        <select class="form-control chosen-select" name="assigned_to"
                                            data-placeholder="Select Employee">
                                            <option value=""></option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['id'] ?>"
                                                    <?= (isset($ticket['assigned_to']) && $ticket['assigned_to'] == $employee['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($employee['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="help-block">Assign to a specific agent</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Priority</label>
                                        <select class="form-control" name="priority">
                                            <option value="Low"
                                                <?= (($ticket['priority'] ?? '') === 'Low') ? 'selected' : '' ?>>Low
                                            </option>
                                            <option value="Medium"
                                                <?= (($ticket['priority'] ?? 'Medium') === 'Medium') ? 'selected' : '' ?>>
                                                Medium</option>
                                            <option value="High"
                                                <?= (($ticket['priority'] ?? '') === 'High') ? 'selected' : '' ?>>High
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-sm-4">
                                        <label>Requester Name</label>
                                        <input type="text" class="form-control" name="requester_name"
                                            placeholder="Full Name"
                                            value="<?= htmlspecialchars($ticket['requester_name'] ?? '') ?>" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Requester Email</label>
                                        <input type="email" class="form-control" name="requester_email"
                                            placeholder="Email Address"
                                            value="<?= htmlspecialchars($ticket['requester_email'] ?? '') ?>" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <?php
                                            $statusOptions = ['Open', 'Pending', 'On hold', 'Solved', 'Closed'];
                                            foreach ($statusOptions as $status): ?>
                                                <option value="<?= $status ?>"
                                                    <?= (($ticket['status'] ?? 'Open') === $status) ? 'selected' : '' ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-sm-6">
                                        <label>Category</label>
                                        <select class="form-control" name="category">
                                            <?php
                                            $categories = ['General', 'Technical', 'Academic', 'Fee Related'];
                                            foreach ($categories as $cat): ?>
                                                <option value="<?= $cat ?>"
                                                    <?= (($ticket['category'] ?? '') === $cat) ? 'selected' : '' ?>>
                                                    <?= $cat ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Attachments</label>
                                        <input type="file" class="form-control" name="attachments[]" multiple />
                                    </div>
                                </div>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-sm-12">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="5"
                                            placeholder="Describe the issue in detail..."><?= htmlspecialchars($ticket['description'] ?? '') ?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions center" style="margin-top:20px;">
                    <button type="submit" class="btn btn-sm btn-success">
                        <?= $ticket ? 'Update' : 'Submit' ?> <i class="ace-icon fa fa-check"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#ticket-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            // Show loader modal
            $('#lmyModal').show();

            $.ajax({
                url: 'index.php?r=support/create',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#lmyModal').hide();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ticket <?= $ticket ? "Updated" : "Created" ?> Successfully!',
                            text: response.message || 'Your ticket has been saved.',
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 4000
                        });

                        // Redirect after short delay
                        setTimeout(() => {
                            window.location.href = 'index.php?r=support/index';
                        }, 2000);

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Failed',
                            text: response.message ||
                                'Please fill all required fields.',
                            toast: true,
                            position: 'top',
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#lmyModal').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: error || 'Something went wrong!',
                        toast: true,
                        position: 'top',
                        showConfirmButton: true
                    });
                }
            });
        });

        $('.toggle-header').on('click', function() {
            $(this).next('.widget-body').slideToggle();
            $(this).toggleClass('active');
        });

        $('.chosen-select').chosen({
            width: '100%'
        });
    });
</script>

<!-- Chosen CSS and JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>