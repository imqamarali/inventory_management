<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'User Profile';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <img src="<?= !empty($user['profile_picture']) ? '/' . $user['profile_picture'] : '/assets/images/avatars/default-profile.jpg' ?>"
                                 alt="Profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-info" id="uploadBtn">Change Picture</button>
                                <input type="file" id="profileFile" accept="image/*" style="display:none;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <form id="profileForm">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken(); ?>">

                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['role_name'] ?? 'N/A') ?>" disabled>
                                </div>

                                <button type="submit" class="btn btn-success">Save Changes</button>
                                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('uploadBtn').addEventListener('click', function() {
    document.getElementById('profileFile').click();
});

document.getElementById('profileFile').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('profile_file', file);
        formData.append('<?= Yii::$app->request->csrfParam; ?>', '<?= Yii::$app->request->getCsrfToken(); ?>');

        fetch('<?= \yii\helpers\Url::to(['site/update-profile']) ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                alert(d.message);
                location.reload();
            } else {
                alert('Error: ' + d.message);
            }
        })
        .catch(e => alert('Upload failed: ' + e));
    }
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('<?= \yii\helpers\Url::to(['site/update-profile']) ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message);
        } else {
            alert('Error: ' + d.message);
        }
    })
    .catch(e => alert('Update failed: ' + e));
});
</script>
