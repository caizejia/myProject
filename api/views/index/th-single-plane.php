<?php

use yii\helpers\Url;

$order = \app\models\Orders::findOne($id);
$weight = Yii::$app->db->createCommand("select weight from order_package_wz where order_id = '{$id}'")->queryOne();
$weight = $weight['weight'];
?>

<!DOCTYPE html>
<html>
<style>

</style>

<head>
  <meta charset="UTF-8">
</head>
<!--<table style="width: 100px;height: 150px">-->

<table width="100%">
    <tr>
        <td colspan="2" style="padding-top: 50px"></td>
    </tr>
    <tr>
        <td width="50%" style="padding-left: 40px"><img src="http://admin.orkotech.com/images/wowmall.jpg" width="100%"></td>
        <td width="50%" style="padding-left: 40px"><img width="100%" src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=30&text=<?php echo $order->lc_number; ?>&thickness=40&start=C&code=BCGcode128"></td>
    </tr>
    <tr>
        <td colspan="2"><hr style="height:3px;border:none;border-top:3px solid"></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 40px;">
            <table>
                <tr>
                    <td><p style="font-size: 40px;">From:<b> wowmall</b></p></td>
                </tr>
                <tr>
                    <td style="text-align: right;font-size: 30px;"><b>ECOM-TH-<?php echo $order->channel_type;?></b></td>
                </tr>
            </table>

        </td>
        <td rowspan="3">
            <table>
                <tr>
                    <td style="border: 2px solid #000 ;height: 100%;text-align: center;font-size: 50px;width: 500px">
                        <b>C.O.D</b>
                        <br>
                        <b>THB <?php echo sprintf('%.2f',$order->price - $order->prepayment_amount);?></b>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <tr>
        <td style="height: 80px"></td>
    </tr>
    <tr style="margin-top: 20px">
        <td style="padding-left: 50px;font-size: 30px;" colspan="2">
            <span style="padding:0px; margin:0px;display: inline;">C/N No:   </span>
            <span style="padding:0px; margin:0px;display: inline;font-size: 40px"><b>  <?php echo $order->lc_number;?></b>
            </span>
        </td>
    </tr>
    <tr>
        <td style="padding-left: 50px ;font-size: 30px"><span style="padding:0px; margin:0px;display: inline;">Ref No:   </span><span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b>  </b></span></td>
        <td style="padding-left: 50px;font-size: 30px"><span style="padding:0px; margin:0px;display: inline;">Contact:   </span><span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b>  wowmall</b></span></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 30px"><span style="font-size: 30px;padding:0px; margin:0px;display: inline;">Order No.: </span><span style="padding:0px; margin:0px;display: inline;"><b> <?php echo $order->id;?></b></span></td>
        <td style="padding-left: 50px;font-size: 30px"><span style="font-size: 30px;padding:0px; margin:0px;display: inline;">Date: </span><span style="padding:0px; margin:0px;display: inline;"><b> <?php echo date('Y-m-d');?></b></span></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-left: 50px"><hr style="height:3px;border:none;border-top:3px dotted"></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 30px"></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-left: 50px;font-size: 30px;">To: <span style="padding:0px; margin:0px;display: inline;"><?php echo $order->name;?></span></td>
    </tr>
    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <td style="padding-left: 50px;font-size: 30px;">Add:</td>
                    <td style="padding-left: 50px;font-size: 30px;"><?php echo $order->address;?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 30px"></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 30px;">City: <span style="padding:0px; margin:0px;display: inline;"><?php echo $order->district;?></span></td>
        <td style="font-size: 30px;">Zipcode: <span style="padding:0px; margin:0px;display: inline;"><?php echo $order->post_code;?></span></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 30px;">State: <span style="padding:0px; margin:0px;display: inline;"><?php echo $order->city?></span></td>
        <td style="font-size: 30px;">Country: <span style="padding:0px; margin:0px;display: inline;"><?php echo $order->county;?></span></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 30px;">Contact: <span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b><?php echo $order->name;?></b></span></td>
        <td colspan="2" style="padding-left:100px;padding-top: 50px;font-size: 30px;">WT(kg): <span style="display: inline;font-size: 35px"><b><?php echo $weight;?></b></span></td>
    </tr>
    <tr>
        <td style="padding-left: 50px;font-size: 30px;">Phone: <span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b><?php echo $order->mobile;?></b></span></td>
    </tr>
    <tr>
        <td style="padding-left: 50px"></td>
        <td style="padding-left: 100px"><span style="font-size: 30px;padding:0px; margin:0px 0px 0px 100px ;display: inline;">Pcs: </span><span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b> 1/1</b></span></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-left: 50px"><hr style="height:1px;border:none;border-top:1px solid"></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 30px"></td>
    </tr>
    <tr>
        <td rowspan="2" width="50%" style="padding-left: 40px"><img width="100%" src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=30&text=<?php echo $order->lc_number; ?>&thickness=40&start=C&code=BCGcode128"></td>
        <td style="padding-left: 50px;font-size: 30px;">Pcs: <span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b>1/1</b></span>&nbsp;&nbsp;&nbsp;WT(kg): <span style="padding:0px; margin:0px;display: inline;font-size: 35px"><b><?php echo $weight;?></b></span></td>
    </tr>
    <tr>
        <td style="padding-left: 100px"><span style="padding:0px; margin:0px 0px 0px 30px;display: inline;font-size: 30px"><b> <?php echo date('Y-m-d');?></b></span></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 20px"></td>
    </tr>
    <tr>
        <td colspan="2" style="padding-left: 80px;font-size: 30px;"><b style="font-size: 40px">Thailand</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->name;?><b style="font-size: 35px">(<?php echo $order->mobile;?>)</b></td>
    </tr>
</table>

</html>
