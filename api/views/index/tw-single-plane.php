<?php
/**
 * Created by PhpStorm.
 * User: 秦洋
 * Date: 2018/3/8
 * Time: 17:20
 */

$order = new \app\models\Orders();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pdf['id'];?></title>
    <style>
        .product-info td{
            border-bottom: solid 1px #000;
        }
        .product-infos td{
            border-bottom: solid 1px #000;
        }
        .product-infos td{
            border-right: solid 1px #000;
        }
    </style>
</head>
<body>
<table style="width: 90%;height: 90%;margin: auto;border: 1px solid #000" cellpadding="0" cellspacing="0">
    <tr><td style="text-align: center;font-size: 28px;border-bottom: 1px solid #000"><b>臺灣郵政</b></td></tr>
    <tr><td style="border-bottom: 1px solid #000"><table><tr><td width="60px">收件人</td><td><?php echo $pdf['name'];?></td></tr><tr><td></td><td><?php echo $pdf['address'];?></td></tr><tr><td></td><td><?php echo $pdf['mobile'];?></td></tr></table></td></tr>
    <tr><td style="border-bottom: 1px solid #000"><table>
                <tr>
                    <td width="70px">訂單編號</td>
                    <td width="180px"><?php echo $pdf['id'];?></td>
                    <td rowspan="2">
                        <table style="border: 1px solid #000">
                                <tr><td>出货日期</td></tr>
                                <tr><td><?php echo date('Y-m-d',strtotime($pdf['shipping_date']));?></td></tr>
                            </table>
                    </td>
                </tr>
                <tr>
                    <td>派送單號</td>
                    <td><?php echo $pdf['lc_number'];?></td>
                </tr>
                <tr>
                    <td width="70px">派送條碼</td>
                    <td width="180px"><img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=0&text=<?php echo $pdf['lc_number']; ?>&thickness=25&start=C&code=BCGcode128"></td>
                    <td rowspan="2"><table>
                            <tr><td>代收貨款</td></tr>
                            <tr><td><?php echo $pdf['price'];?></td></tr>
                        </table></td>
                </tr>
            </table></td></tr>
    <tr><td style="text-align: center;font-size: 25px;border-bottom: 1px solid #000"><b>INVOICE</b></td></tr>
    <tr>
        <td style="border-bottom: 1px solid #000">
            <table cellpadding="0" cellspacing="0" class="product-infos" style="width: 100%">
                <tr class="product-info">
                    <td width="32%">貨品品名</td>
                    <td width="10%">件數</td>
                    <td width="10%">單位</td>
                    <td  width="15%" style="">重量KG</td>
                    <td>單價</td>
                    <td style="border-right: none" width="auto">總價TWD</td>

                </tr>
                <?php $order_item = Yii::$app->db->createCommand("select * from orders_item WHERE order_id = $pdf[id]")->queryAll();
                $package = Yii::$app->db->createCommand("select * from order_package_wz WHERE order_id = $pdf[id]")->queryOne();
                $weight = $package['weight'];
                $jian = count($order_item);
                foreach($order_item as $k=>$v){
                    $product_id = strpos($v['product'],'产品ID:');
                    $product_id = mb_substr($v['product'],$product_id + 5,5,'utf-8');
                    $product_id = $pdf['website'];
                    $product = Yii::$app->db->createCommand("select * from products WHERE id = '{$product_id}'")->queryOne();
                 ?>
                <tr class="product-infos">
                    <td><?php echo $product['declaration_cname'];?></td>
                    <td><?php echo $v['qty'];?></td>
                    <td>件</td>
                    <td><?php echo sprintf('%.2f',$weight / $jian),'kg';?></td>
                    <td><?php echo sprintf('%.2f',$v['price'] / $v['qty']);?></td>
                    <td  style="border-right: none" ><?php echo $v['price'];?></td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td style="text-align: right;width: 100px">發件人:</td>
                    <td>WOWMALL</td>
                </tr>
            </table>
        </td></tr>
    <tr><td style="border-bottom: 1px solid #000">
            <table>
                <tr>
                    <td style="text-align: right;width: 100px">發件地址:</td>
                    <td>進口</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td style="text-align: right;width: 100px">備註:</td>
                    <td><?php echo $pdf['comment'];?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>





