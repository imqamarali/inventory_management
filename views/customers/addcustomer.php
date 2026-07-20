<?php use yii\helpers\Html; ?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li><a href="index.php?r=customers/customerlist">Customer List</a></li>
                <li class="active">Add Customer</li>
            </ul>
        </div>

        <div class="widget-main">
            <h4>Add New Customer</h4>
            <form id="customerForm" style="padding:20px;">

                <div class="row">
                    <div class="col-md-6">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" placeholder="First name">
                    </div>
                    <div class="col-md-6">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Last name">
                    </div>
                </div>

                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" class="form-control" name="company_name" placeholder="Company name">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="Phone">
                    </div>
                </div>

                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" class="form-control" name="mobile" placeholder="Mobile">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Customer Type</label>
                        <select class="form-control" name="customer_type">
                            <option value="Individual">Individual</option>
                            <option value="Company">Company</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Tax Number</label>
                        <input type="text" class="form-control" name="tax_number" placeholder="Tax number">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="2" placeholder="Address"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" placeholder="City">
                    </div>
                    <div class="col-md-6">
                        <label>Province</label>
                        <input type="text" class="form-control" name="province" placeholder="Province">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" placeholder="Country">
                    </div>
                    <div class="col-md-6">
                        <label>Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" placeholder="Postal code">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Credit Limit</label>
                        <input type="number" class="form-control" name="credit_limit" value="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label>Payment Terms (Days)</label>
                        <input type="number" class="form-control" name="payment_terms" value="0" placeholder="0">
                    </div>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" name="remarks" rows="2" placeholder="Remarks"></textarea>
                </div>

                <div style="text-align:right;margin-top:20px;">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
    function saveCustomer() {
        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('first_name', $('input[name="first_name"]').val());
        data.append('last_name', $('input[name="last_name"]').val());
        data.append('company_name', $('input[name="company_name"]').val());
        data.append('email', $('input[name="email"]').val());
        data.append('phone', $('input[name="phone"]').val());
        data.append('mobile', $('input[name="mobile"]').val());
        data.append('customer_type', $('select[name="customer_type"]').val());
        data.append('tax_number', $('input[name="tax_number"]').val());
        data.append('credit_limit', $('input[name="credit_limit"]').val());
        data.append('payment_terms', $('input[name="payment_terms"]').val());
        data.append('address', $('textarea[name="address"]').val());
        data.append('city', $('input[name="city"]').val());
        data.append('province', $('input[name="province"]').val());
        data.append('country', $('input[name="country"]').val());
        data.append('postal_code', $('input[name="postal_code"]').val());
        data.append('remarks', $('textarea[name="remarks"]').val());

        Swal.fire({
            title: 'Saving...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('index.php?r=customers/addcustomer', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        location.href = 'index.php?r=customers/customerlist';
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to save!', 'error');
            });
    }
</script>
