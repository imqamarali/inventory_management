<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Transactiontype;

$this->title = 'Demand';

$connection = Yii::$app->getDb();
$int = "SELECT * from item where inventory_type!=4 and type =8";
$inventory = $connection->createCommand($int)->queryAll();


$int1 = "SELECT * from item where inventory_type!=4";
$inventory1 = $connection->createCommand($int1)->queryAll();

$dep = "SELECT * from department";
$depr = $connection->createCommand($dep)->queryAll();

// $project = "SELECT * from projects";
// $projects = $connection->createCommand($project)->queryAll();


$demand = "SELECT MAX(id) from demand";
$demandr = $connection->createCommand($demand)->queryOne();

$btn = "Create";
$name = $_SESSION['user_array']['id'];
$currentEmpID = $_SESSION['user_array']['id'];
$dno = ($demandr['MAX(id)'] + 1);
$date = "";
$dep = "";
$up = 0;
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $main = "SELECT *,employee.name as ename,demand.department as dep_id,demand.id as demand_id from demand
    Left Join employee ON (employee.id=demand.name)
    where demand.id='" . $_REQUEST['id'] . "'";
    $mainr = $connection->createCommand($main)->queryOne();

    $btn = "Update";
    $dep = $mainr['dep_id'];
    $name = $mainr['ename'];
    $date = date("d-M-Y", strtotime($mainr['date']));
    $dno = $mainr['dno'];
    $up = $mainr['demand_id'];
}
?>
<div class="trans-form">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li> <i class="ace-icon fa fa-home home-icon"></i> <a class="ajaxlink" href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=">Home</a> </li>
            <li> <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/trans/index" class="ajaxlink">Transaction </a> </li>
            <li class="active">
                <?= Html::encode($this->title); ?>
            </li>
        </ul>
    </div>
    <div class="page-content">
        <div class="page-header">
            <h1>
                <?= Html::encode($this->title) ?>

                <small> <i class="ace-icon fa fa-angle-double-right"></i> </small> <b style="float:right;font-size: 16px;"> </b>
            </h1>
            <h4 class="widget-title" style="float: right; margin-top:-10px; margin-right: 30px;">
                <div class="col-xs-3" style="margin-top: -10px;">
                    <button class="btn btn-sm btn-primary">
                        <a href="index.php?r=/trans/demands/" style="color: inherit; text-decoration: none;">View Demand List</a>
                    </button>
                </div>
            </h4>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <form method="post" action="index.php?r=/trans/demand">
                    <div class="col-xs-12">
                        <div class="col-xs-12 widget-container-col ui-sortable" style="border-right: 1px solid #ffffff;">
                            <div class="widget-box ui-sortable-handle">
                                <div class="widget-header" style="padding: 0px; padding-left: 25px;">
                                    <h5 class="widget-title">
                                        <?= Html::encode($this->title) ?>
                                    </h5>

                                </div>
                                <input type="hidden" name="update" id="update" value="<?php echo $up; ?>">
                                <div class="widget-body">
                                    <div class="widget-main" style="padding: 2px; padding-left: 10px;">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="col-xs-2">
                                                    <label>Demand #</label>
                                                    </br>
                                                    <input type="hidden" required readonly name="name" placeholder="Name" value="<?php echo $name; ?>" />
                                                    <input type="text" required readonly name="demand_no" placeholder="Demand #" value="<?php echo $dno; ?>" />
                                                </div>
                                                <div class="col-xs-2">
                                                    <label>Department</label>
                                                    </br>
                                                    <select required name="department" class="chzn-select" style="width:100%;">
                                                        <?php
                                                        foreach ($depr as $row) {
                                                            $chk = '';
                                                            if (!empty($dep) && $dep == $row['id']) {
                                                                $chk = 'selected';
                                                            }
                                                        ?>
                                                            <option <?php echo $chk; ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-2">
                                                    <label>Date</label>
                                                    </br>
                                                    <input type="text" required class="date-picker" name="date" placeholder="Date" value="<?php echo $date; ?>" />
                                                </div>


                                                <?php if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) { ?>
                                                    <div class="col-xs-2">
                                                        <label>Requisitioner Name </label>
                                                        </br>
                                                        <input type="text" readonly name="name" value="<?php echo $name ?>" />
                                                    </div>
                                                <?php } else { ?>

                                                    <div class="col-xs-2">
                                                        <label>Requisitioner Name</label>
                                                        </br>
                                                        <input type="text" readonly value="<?php echo $_SESSION['user_array']['name']; ?>" />
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-12 widget-container-col ui-sortable" id="widget-container-col-2" style="min-height: 263px; padding: 20px;padding-right: 3%;">
                                                <div class="widget-box widget-color-blue ui-sortable-handle" id="widget-box-2" style="opacity: 1; z-index: 0;">
                                                    <div class="widget-header" style="padding: 0px; padding-left: 25px;">
                                                        <h5 class="widget-title bigger lighter"> <i class="ace-icon fa fa-table"></i>
                                                            <?= Html::encode($this->title) ?>
                                                            Details </h5>
                                                    </div>
                                                    <div class="widget-body" style=" background-color: #EDF3F4;">
                                                        <div class="widget-main">
                                                            <div class="row">
                                                                <div class="col-xs-12" style="display: block;margin-bottom:12px;" id="accounts">
                                                                    <table class="table table-bordered table-striped" id="expt" style="margin: 2px; width: 100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Item</th>
                                                                                <th hidden>item balance</th>
                                                                                <th>Uints</th>
                                                                                <th>Remarks</th>
                                                                                <th>Quantity</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodyy">
                                                                            <?php
                                                                            $up_total = 0;
                                                                            if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                                                                                $mains = "SELECT * from demand_sub where mid='" . $_REQUEST['id'] . "'";
                                                                                $mainsr = $connection->createCommand($mains)->queryAll();
                                                                                foreach ($mainsr as $row) {
                                                                                    $up_total++;
                                                                            ?>
                                                                                    <tr>
                                                                                        <td style="width:30%;">
                                                                                            <select style="width:50%;" name="item<?php echo $up_total; ?>" class="chzn-select" required id="item<?php echo $up_total; ?>" onchange="get_units(this.value, <?php echo $up_total; ?>)">
                                                                                                <?php
                                                                                                foreach ($inventory1 as $in) {
                                                                                                    $chk1 = '';
                                                                                                    if ($in['id'] == $row['item']) {
                                                                                                        $chk1 = 'selected';
                                                                                                    }
                                                                                                ?>
                                                                                                    <option <?php echo $chk1; ?> value="<?php echo $in['id']; ?>"><?php echo $in['name']; ?> </option>
                                                                                                <?php } ?>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td hidden><input id="balance_id<?php echo $up_total; ?>" name="balance_id<?php echo $up_total; ?>" type="text" value="<?php echo $row['item_balance']; ?>" /></td>
                                                                                        <td><input type="text" name="units<?php echo $up_total; ?>" value="<?php echo $row['units']; ?>" id="units<?php echo $up_total; ?>" /></td>
                                                                                        <td><input type="text" name="remarks<?php echo $up_total; ?>" value="<?php echo $row['remarks']; ?>" placeholder="Remarks" /></td>
                                                                                        <td style="width:30%;">
                                                                                            <input type="text" name="qty<?php echo $up_total; ?>" value="<?php echo $row['qty']; ?>" required placeholder="Qty" />
                                                                                            <input type="hidden" name="update_id<?php echo $up_total; ?>" value="<?php echo $row['id']; ?>">

                                                                                        </td>
                                                                                    </tr>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <input type="hidden" name="demand_id" value="<?php echo $_REQUEST['id']; ?>">
                                                                    <input type="hidden" name="totalexp" id="totalexp" value="<?php echo $up_total; ?>">

                                                                    <div style="float:right;"> <input type="button" name="add" id="add" value="Add" class="btn btn-success">
                                                                        <input type="button" name="remove" id="remove" value="Remove" class="btn btn-danger">
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <button type="submit" name="sub" style="float: right;margin-top: 20px;" class="btn btn-success clickable"><?php echo $btn; ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/chosen.jquery.min.js"></script>
<script>
    $(function() {
        $(".chzn-select").chosen();
    });
    var i = parseInt(document.getElementById("totalexp").value);
    jQuery("#add").on("click", function(e) {
        i = i + 1;
        document.getElementById("totalexp").value = i;
        var innermyspan = document.getElementById("tbodyy").innerHTML;
        $('#tbodyy').append('<tr><td style="width:30%;"><select style="width:40%;" name="item' + i + '" class="chzn-select" onchange = "balance(this.value, ' + i + '); get_units(this.value, ' + i + ')" required id="item' + i + '"> <?php foreach ($inventory as $in) { ?><option value="<?php echo $in['id']; ?>"><?php echo $in['name']; ?> </option> <?php } ?> </select></td><td hidden ><input id="balance_id' + i + '" name ="balance_id' + i + '" type="text" /></td><td style="width:30%;"><input type="text" name="units' + i + '" id ="units' + i + '"/></td><td style="width:30%;"><input type="text" name="remarks' + i + '"  placeholder="Enter Remarks"/></td><td style="width:30%;"><input type="number" name="qty' + i + '" required placeholder="Qty"/></td>/tr>');
        $(".chzn-select").chosen();
    });
    // jQuery("#remove").on("click",function(e)
    // {
    // 	document.getElementById("tbodyy").deleteRow((i-1));
    // 	if(i>0)
    // 	{
    // 		i=i-1;
    // 	}
    // 	document.getElementById("totalexp").value=i;
    // });

    jQuery("#remove").on("click", function(e) {
        document.getElementById("tbodyy").deleteRow((i - 1));
        if (i > 0) {
            i = i - 1;
        }
        document.getElementById("totalexp").value = i;
    });

    function get_units(id, index) {

        /*console.log("nice",id , index);*/

        $.ajax({
            type: "POST",
            url: "index.php?r=trans/get_units",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                console.log("Response from server:", response);
                var units = response;
                document.getElementById("units" + index).value = units;

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }


    function balance(id, index) {

        /*console.log("nice",id , index);*/

        $.ajax({
            type: "POST",
            url: "index.php?r=trans/get_item_balance",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                console.log("Response from server:", response); // Verify the response format
                var balance1 = response;
                document.getElementById("balance_id" + index).value = balance1;

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>