<?php
/**
 * Created by PhpStorm.
 * User: 秦洋
 * Date: 2018/3/8
 * Time: 17:20
 */

$product = Yii::$app->db->createCommand("select * from products WHERE id = $pdf[website]")->queryOne();
$order = new \app\models\Orders();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pdf['id'];?></title>
</head>
<style>
    table {
        border-collapse: collapse;
        line-height: 1.3rem;
        font-size: 1.0rem;
        width: 100%;
        font-family: "Courier New", Courier, monospace;
    }

    td {
        border: 1px solid black;
        padding: 2px 5px;

    }

    .title {
        display: inline-block;
        padding: 2px 0px;
        font-weight: bold;
    }

    .cont {
        display: inline-block;
        width: 80px;
        padding: 5px 0px;

    }

    .name {
        font-weight: 700
    }


</style>

<body>
    <table>
        <!-- 第一行 -->
        <tr>
            <td colspan="5">
                <div>
                    <span class="title">Consignee:</span>

                    <span class="cont"><?php echo $pdf['name'];?></span><br>
                    <div style="padding-left:100px"><?php echo $pdf['address'];?> <?php echo $pdf['area'];?></div>
                </div>
                <div style="margin-top:40px">
                    <span class="title">City:</span>
                    <span class="cont"><?php echo $pdf['district'];?></span><br>
                    <span class="title">Province:</span>
                    <span class="cont"><?php echo $pdf['city'];?></span>
                </div>
                <div>
                    <span class="title">Phone:</span>
                    <span class="cont"><?php echo $pdf['mobile'];?></span>
                    <span class="title">Zip code:</span>
                    <span class="cont"><?php echo $pdf['post_code'];?></span>
                </div>

            </td>

        </tr>

        <!-- 第二行 -->
        <tr>
            <td colspan="5" style="position: relative">
                <div>
                    <span class="title">Shipper:</span>
                    <span class="cont">Orko Technologies</span>
                    <div style="margin-left:94px">Wu Sha Xing Yi Road 118</div>
                </div>
                <div style="margin-top:20px;">
                    <span class="title">City:</span>
                    <span class="cont">DongGuan</span><br>
                    <span class="title">Province:</span>
                    <span class="cont">GuangDong</span>
                </div>
                <div>
                    <span class="title">Phone:</span>
                    <span class="cont">133 4263 6965</span><br>
                    <span class="title">Zip code:</span>
                    <span class="cont">850001</span>
                </div>

                <div style="border-left: 0;text-align: center;font-size: 1.6rem;position: absolute;top: 160px;right: 20px;"><strong>REG<br>COD</strong></div>

            </td>


        </tr>
        <!-- 第三行 -->
        <tr>
            <td colspan="5" style="height: 100px;text-align:center">
    <img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=16&text=<?php echo $pdf['lc_number'];?>&thickness=30&start=C&code=BCGcode128">
            </td>
        </tr>
        <!-- 第四行 -->
        <tr style="vertical-align: top">
            <td height="100" colspan="2" style="vertical-align: top;font-size: 0.6rem;width: 35%">
                <div class="name">Instruction</div>
                <div><?php echo $pdf['comment'];?></div>
            </td>
            <td colspan="2" style="vertical-align: top;font-size: 0.6rem;width: 35%">
                <div class="name">Good Description</div>
                <div><?php echo $product['declaration_ename'];?></div>
            </td>
            <td colspan="1" style="width: 30%;">
                <span class="name">QTY</span> :
                <span><?php echo $pdf['qty'];?></span>
            </td>
        </tr>
        <!-- 第五行 -->
        <tr>
            <td height="40" colspan="5" style="border-bottom: 0;font-size: 20px;"><b>COD Amount: <?=$order->currencyType[$pdf['county']]?>.<?php echo number_format($pdf['price'] - $pdf['prepayment_amount'],0,",",",");?></b></td>
        </tr>
        <tr>
            <td colspan="5" style="border-bottom: none">
                ON:<?=$pdf['id']?> <?=$pdf['channel_type'];?>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="border-top: 0;font-size: 12px;vertical-align: bottom;text-align: right">Printed <?php echo date('d-m-Y');?></td>
        </tr>
    </table>
</body>

</html>





