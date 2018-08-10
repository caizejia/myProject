<?php

use yii\helpers\Url;

$order = \app\models\Orders::findOne($id);
$weight = Yii::$app->db->createCommand("select weight from order_package_wz where order_id = '{$id}'")->queryOne();
$weight = $weight['weight'];
if ($order->area) {
    $ads = $order->address . ',' . $order->area;
} else {
    $ads = $order->address;
}
if ($order->district) {
    $ads .= ',' . $order->district;
} else {
    $ads .= '';
}
if ($order->city) {
    $ads .= ',' . $order->city;
} else {
    $ads .= '';
}
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
        <td colspan="2" style="padding-top: 10px"></td>
    </tr>
    <tr style="width: 100%;height: 15%">
        <td colspan="2" style="padding-left: 20%"><img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=16&text=<?php echo $order->lc_number; ?>&thickness=30&start=C&code=BCGcode128"></td>
    </tr>
    <tr style="margin-top: 10px">
        <td style="font-size: 18px;padding-left: 10px"><?php echo $order->id;?></td>
        <td style="font-size: 18px;">COD:<?php echo sprintf('%.2f',$order->price - $order->prepayment_amount);?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: 3px solid #000000"><b>To: &nbsp;&nbsp;</b><?php echo $ads;?></td>
    </tr>
    <tr>
        <td><b>Zip code:</b> <?php echo $order->post_code;?></td>
        <td> <b>Country: </b><?php echo $order->county;?></td>
    </tr>
    <tr>
        <td><b>Tel: </b><?php echo $order->mobile;?></td>
        <td><b>Contact:</b> <?php echo $order->name;?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: 3px solid #000000"></td>
    </tr>
    <tr>
        <td></td>
        <td><b>Product Name:</b> <?php echo (\app\models\Products::findOne($order->website))->declaration_ename;?></td>
    </tr>
    <tr>
        <td width="50%"><img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=15&text=<?php echo $order->lc_number; ?>&thickness=20&start=C&code=BCGcode128"></td>
        <td></td>
    </tr>
    <tr>
        <td style="font-size: 18px">PCS1</td>
        <td style="font-size: 16px">SKU:<?php echo (\app\models\Products::findOne($order->website))->sku;?></td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: 3px solid #000000"><b>Help service 9:00-17:00</b></td>
    </tr>
    <tr>
        <td colspan="2"><b>6625088418/0994311861/0994311889/0994311844</b></td>
    </tr>
</table>




</html>
