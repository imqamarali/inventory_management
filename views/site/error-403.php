<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li>
                    <a href="index.php">Dashboard</a>
                </li>
                <li class="active">Access Denied</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="error-container">
                        <div class="well" style="text-align: center; padding: 60px 40px;">
                            <h1 class="grey lighter smaller">
                                <span class="blue bigger-125">
                                    <i class="ace-icon fa fa-lock" style="color: #FF6B6B; font-size: 72px;"></i>
                                </span>
                            </h1>

                            <h2 class="grey lighter" style="margin-top: 20px;">
                                <span style="color: #FF6B6B;">Access Denied</span>
                            </h2>

                            <hr />

                            <h3 class="lighter smaller" style="margin-top: 30px;">
                                <i class="ace-icon fa fa-exclamation-triangle" style="color: #FF6B6B;"></i>
                                Permission Restricted
                            </h3>

                            <div style="margin-top: 30px; margin-bottom: 40px; font-size: 16px; line-height: 1.8;">
                                <p class="text-muted">
                                    You don't have permission to access this module or resource.
                                </p>
                                <p class="text-muted">
                                    Your current role does not have the necessary privileges to view this content.
                                </p>

                                <div class="space"></div>

                                <div style="background-color: #FFF3CD; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #FFC107;">
                                    <p style="margin: 0;">
                                        <i class="ace-icon fa fa-info-circle"></i>
                                        <strong>If you believe this is a mistake,</strong> please contact your System Administrator to request access.
                                    </p>
                                </div>

                                <h4 class="smaller" style="margin-top: 40px; text-align: left;">
                                    What you can do:
                                </h4>

                                <ul class="list-unstyled spaced inline margin-15" style="text-align: left;">
                                    <li>
                                        <i class="ace-icon fa fa-check-circle" style="color: #4CAF50;"></i>
                                        Return to the dashboard
                                    </li>
                                    <li>
                                        <i class="ace-icon fa fa-check-circle" style="color: #4CAF50;"></i>
                                        Request access from your administrator
                                    </li>
                                    <li>
                                        <i class="ace-icon fa fa-check-circle" style="color: #4CAF50;"></i>
                                        Check your user account settings
                                    </li>
                                </ul>
                            </div>

                            <hr />
                            <div class="space"></div>

                            <div class="center">
                                <a href="javascript:history.back()" class="btn btn-grey">
                                    <i class="ace-icon fa fa-arrow-left"></i>
                                    Go Back
                                </a>

                                <a href="index.php?r=inventory/dashboard" class="btn btn-primary">
                                    <i class="ace-icon fa fa-tachometer"></i>
                                    Dashboard
                                </a>

                                <a href="index.php?r=user/profile" class="btn btn-info">
                                    <i class="ace-icon fa fa-user"></i>
                                    My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    padding: 20px;
}

.error-container .well {
    background-color: #FFFFFF;
    border: 1px solid #DDDDDD;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.error-container .btn {
    margin: 0 5px;
    min-width: 120px;
}
</style>
