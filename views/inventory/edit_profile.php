<?php

use yii\helpers\Html;

$this->title = 'Edit Profile';

$student = $student ?? [];
$permissions = $permissions ?? ['can_view' => 0, 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0];
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-content">
    <div class="main-content-inner">

        <div>
            <form id="student-profile-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                    value="<?= Yii::$app->request->csrfToken ?>" />
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" />

                <!-- Personal Information -->
                <div class="widget-box">
                    <div class="widget-header widget-header-blue widget-header-flat">
                        <h4 class="widget-title lighter">
                            <i class="ace-icon fa fa-user"></i> Personal Information
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <!-- Row 1: Name -->
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>First Name <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="first_name" required
                                        placeholder="Enter First Name"
                                        value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name"
                                        placeholder="Enter Last Name"
                                        value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Gender <span style="color: red">*</span></label>
                                    <select class="form-control" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male"
                                            <?= ($student['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female"
                                            <?= ($student['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Date of Birth <span style="color: red">*</span></label>
                                    <input type="date" class="form-control" name="dob" required
                                        value="<?= htmlspecialchars($student['dob'] ?? '') ?>" />
                                </div>
                            </div>

                            <!-- Row 3: Contact Details -->
                            <div class="row" style="margin-top: 15px;">
                                <div class="col-sm-3">
                                    <label>Mobile Number</label>
                                    <input type="tel" class="form-control" name="mobile_number"
                                        placeholder="Enter Mobile Number"
                                        value="<?= htmlspecialchars($student['mobile_number'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter Email"
                                        value="<?= htmlspecialchars($student['email'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Religion</label>
                                    <input type="text" class="form-control" name="religion" placeholder="Enter Religion"
                                        value="<?= htmlspecialchars($student['religion'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Caste</label>
                                    <input type="text" class="form-control" name="caste" placeholder="Enter Caste"
                                        value="<?= htmlspecialchars($student['caste'] ?? '') ?>" />
                                </div>
                            </div>

                            <!-- Row 5: Physical Details -->
                            <div class="row" style="margin-top: 15px;">
                                <div class="col-sm-3">
                                    <label>Height (cm)</label>
                                    <input type="text" class="form-control" name="height" placeholder="Enter Height"
                                        value="<?= htmlspecialchars($student['height'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Weight (kg)</label>
                                    <input type="text" class="form-control" name="weight" placeholder="Enter Weight"
                                        value="<?= htmlspecialchars($student['weight'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Blood Group</label>
                                    <select class="form-control" name="blood_group_id">
                                        <option value="">Select Blood Group</option>
                                        <?php foreach ($blood_groups ?? [] as $bg): ?>
                                            <option value="<?= htmlspecialchars($bg['id']) ?>"
                                                <?= ($student['blood_group_id'] ?? '') == $bg['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($bg['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Category</label>
                                    <select class="form-control" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($student_categories ?? [] as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat['id']) ?>"
                                                <?= ($student['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent/Guardian Information -->
                <div class="widget-box">
                    <div class="widget-header widget-header-green widget-header-flat">
                        <h4 class="widget-title lighter">
                            <i class="ace-icon fa fa-users"></i> Parent/Guardian Information
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <!-- Father's Information -->
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Father's Name</label>
                                    <input type="text" class="form-control" name="father_name"
                                        placeholder="Enter Father's Name"
                                        value="<?= htmlspecialchars($student['father_name'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Father's Phone</label>
                                    <input type="tel" class="form-control" name="father_phone"
                                        placeholder="Enter Father's Phone"
                                        value="<?= htmlspecialchars($student['father_phone'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Father's Occupation</label>
                                    <input type="text" class="form-control" name="father_occupation"
                                        placeholder="Enter Father's Occupation"
                                        value="<?= htmlspecialchars($student['father_occupation'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Mother's Name</label>
                                    <input type="text" class="form-control" name="mother_name"
                                        placeholder="Enter Mother's Name"
                                        value="<?= htmlspecialchars($student['mother_name'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Mother's Phone</label>
                                    <input type="tel" class="form-control" name="mother_phone"
                                        placeholder="Enter Mother's Phone"
                                        value="<?= htmlspecialchars($student['mother_phone'] ?? '') ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label>Mother's Occupation</label>
                                    <input type="text" class="form-control" name="mother_occupation"
                                        placeholder="Enter Mother's Occupation"
                                        value="<?= htmlspecialchars($student['mother_occupation'] ?? '') ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-info">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            Update Profile
                        </button>
                        &nbsp; &nbsp; &nbsp;
                        <a href="<?= \yii\helpers\Url::to(['inventory/profile']) ?>" class="btn">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle form submission
        $('#student-profile-form').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Updating Profile...',
                text: 'Please wait while we update your profile',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(this);

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['inventory/update-profile']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response && response.success !== undefined) {
                        // AJAX response
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message ||
                                    'Profile updated successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href =
                                    '<?= \yii\helpers\Url::to(['inventory/profile']) ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update profile'
                            });
                        }
                    } else {
                        // Regular form submission response (redirect)
                        window.location.href =
                            '<?= \yii\helpers\Url::to(['inventory/profile']) ?>';
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMsg = 'An error occurred while updating profile';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            // Not JSON, use default message
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        });

        // Check for flash messages
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?= Yii::$app->session->getFlash('success') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= Yii::$app->session->getFlash('error') ?>',
                confirmButtonColor: '#d9534f'
            });
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('toast')): ?>
            Swal.fire({
                icon: 'info',
                title: 'Notification',
                text: '<?= Yii::$app->session->getFlash('toast') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
    });
</script>