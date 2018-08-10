<?php

use yii\helpers\Url;

$order = \app\models\Orders::findOne($id);
$weight = Yii::$app->db->createCommand("select weight from order_package_wz where order_id = '{$id}'")->queryOne();
$weight = $weight['weight'];
?>

<!DOCTYPE html>
<html>
<style>
    .tt{font-size: 36pt;font-family: "Microsoft YaHei UI"}

    .STYLE1 {font-family: "Arial";font-size:36pt;}


</style>

<head>
  <meta charset="UTF-8">
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="40%" valign="middle" style="height:13mm;line-height:13mm;border-bottom:solid 1px #ccc;"><img src="/img/sf.jpg" height="40" /> </td>
        <td valign="middle" style="height:13mm;line-height:13mm;border-bottom:solid 1px #ccc;">
            <div style="margin-left: 20px;" ><span class="STYLE1">COD</span></div>
        </td>
        <td width="23%" valign="middle" style="height:13mm;line-height:13mm;border-bottom:solid 1px #ccc;"><img src="/img/sf-tel.png" height="30" /></td>
    </tr>
    <tr>
        <td colspan="3" style="height:28mm;">

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td rowspan="2" style="border-right:solid 1px #999;border-bottom:solid 1px #999;">
                        <img style="width: 70%;margin-right: 5px" src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=15pt&text=<?php echo $order->lc_number?>&thickness=25&start=C&code=BCGcode128" />
                    </td>
                    <td style="border-bottom:solid 1px #999;"><div style="font-family:黑体;font-size:14pt;text-align:center;padding-top:3mm;">电商专配</div></td>
                </tr>
                <tr>
                    <td style="border-bottom:solid 1px #999;">
                        <div style="font-family:黑体;font-size:18pt;text-align:center">代收货款</div>
                        <div style="font-family:黑体;font-size:8pt;">卡号:8888888888</div>
                        <div style="font-family:黑体;font-size:14pt";text-align:right>$<?php echo sprintf('%.2f',$order->price - $order->prepayment_amount);?>元</div>		    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" valign="bottom" style="height:13mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" height="100%" style="font-family:黑体;font-size:10pt;border-right:solid 1px #999;height:13mm;"><div align="center">目的地</div></td>
                    <td style="font-family:黑体;font-size:20pt;">香港</td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" style="height:13mm;border-bottom:solid 1px #999;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" height="100%" style="font-family:黑体;font-size:10pt;border-right:solid 1px #999;height:13mm;"><div align="center">收件人 </div></td>
                    <td style="font-family:黑体;font-size:14pt;"><?php echo $order->name,' ',$order->mobile,' ',$order->address;?></td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" style="height:10mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" height="100%" style="font-family:黑体;font-size:10pt;border-right:solid 1px #999;height:10mm;"><div align="center">寄件人</div></td>
                    <td style="font-family:黑体;font-size:8pt;">Kevin 985625083@qq.com <br />Building, Room 535, No. 288 Xixiang Dadao,, Baoan, Shenzhen, China</td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" style="height:13mm;border-bottom:solid 1px #999;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" style="height:20mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:5mm;height:10mm;border-right:solid 1px #999;font-family:黑体;font-size:8pt;border-bottom:solid 1px #999;"><div align="center">托寄物</div></td>
                    <td colspan="2" valign="middle" style="width:68mm;height:10mm;border-right:solid 1px #999;border-bottom:solid 1px #999;font-size:8pt;"><div align="center">李厚霖dfdf</div></td>
                    <td valign="top" style="font-family:黑体;font-size:8pt;">签收:</td>
                </tr>
                <tr>
                    <td style="width:5mm;height:10mm;border-right:solid 1px #999;font-family:黑体;font-size:8pt;"><div align="center">备注</div></td>
                    <td align="center" valign="middle" style="width:43mm;height:10mm;border-right:solid 1px #999;font-family:黑体;font-size:8pt;"><?php echo $order->comment;?></td>
                    <td style="width:25mm;height:10mm;border-right:solid 1px #999;font-family:黑体;font-size:8pt;">收件员:123456<br />寄件日期: <?php echo date('m');?>月<?php echo date('d');?>日<br />派件员:</td>
                    <td align="right" valign="bottom" style="font-family:黑体;font-size:8pt;">月 日 </td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" align="center" style="height:15mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="26%" align="left" style="border-right:solid 1px #999;">

                        <img src="/img/sf-phone.jpg" style="height:14mm;" />		</td>
                    <td width="74%" align="center"><img style="height:10mm;" src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=10&text=<?php echo $order->lc_number?>&thickness=25&start=C&code=BCGcode128" /></td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" style="height:10mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" height="100%" style="font-family:黑体;font-size:10pt;border-right:solid 1px #999;height:10mm;"><div align="center">寄件人</div></td>
                    <td style="font-family:黑体;font-size:8pt;">Kevin 985625083@qq.com <br />
                        Building, Room 535, No. 288 Xixiang Dadao,, Baoan, Shenzhen, China</td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3" style="height:10mm;border-bottom:solid 1px #999;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" height="100%" style="font-family:黑体;font-size:10pt;border-right:solid 1px #999;height:13mm;"><div align="center">收件人 </div></td>
                    <td style="font-family:黑体;font-size:8pt;"><?php echo $order->name,' ',$order->mobile,' ',$order->address;?></td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
</table>
</body>

</html>
