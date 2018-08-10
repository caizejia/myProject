    //弹窗
    function tan(data,is=false,p=false){
        if(is){
            var a = document.body.clientHeight;
            var b = a+48;
            $('#img2').css("display","block");
            $('#modal-overlay').css({"height":b+'px',"visibility":"visible","display":"block"});
            $('#modal-overlay').animate({opacity:1});
            $('#img2').animate({opacity:1});
        }else{
            var a = document.body.clientHeight;
            var b = a+48;
            $('#str').html(data);
            $('#modal-overlay').css({"height":b+'px',"visibility":"visible","display":"block"});
            $('#t').css("display","block");
            if(p==1){
                $("#t2").css("z-index","99");
            }else if(p==2){
                $("#t3").css("z-index","99");
            }
            $('#modal-overlay').animate({opacity:1});
            $('#t').animate({opacity:1});
        }
    }
    //关闭弹窗
    $('#button').click(function(){
        $('#t').animate({opacity:0},function(){
            $("#t").css("display","none");
        });
        if($("#t2").css("display")=="block"){
            $("#t2").css("z-index","99999");
        }else if($("#t3").css("display")=="block"){
            $("#t3").css("z-index","99999");
        }else{
            $('#modal-overlay').animate({opacity:0},function(){
                $("#modal-overlay").css({"visibility":"hidden","display":"none"})
            });
        }

    })

    $('#btnAppr').click(function(){
        pt();
    })
    //关闭评论弹窗
    $('#x').click(function(){
        $('#t2').animate({opacity:0},function(){
            $("#t2").css("display","none");
        });
        $('#modal-overlay').animate({opacity:0},function(){
            $("#modal-overlay").css({"visibility":"hidden","display":"none"})
        });
    })
    //评论弹窗
    function pt(){
        var a = document.body.clientHeight;
        var b = a+48;
        var c = document.body.clientWidth-32;
        $('#modal-overlay').css({"height":b+'px',"visibility":"visible","display":"block"});
        $('#t2').css({"display":"block","width":c});
        $('#modal-overlay').animate({opacity:1});
        $('#t2').animate({opacity:1});
    }
    //提交评论
    $('#smt').click(function(){
        if ($("input[name='re_name']").val() == '') {
            tan('Name is required',false,1);
            return false;
        }
        if ($("input[name='re_mobile']").val() == '') {
            tan('Mobile is required',false,1);
            return false;
        }
        if ($("[name=re_star]").val() == '') {
            tan('score is required',false,1);
            return false;
        }
        $.post('./site/review',$("#my").serialize(),function(e){
            var data = JSON.parse(e);
            if(data.code==200){
                $('#t2').css({"display":"none"});
                $('#t2').animate({opacity:0});
                tan('Comment successful');
            }else{
                tan('Comment failed,please try again',false,1);
            }
        })

    })

    $("#cha").click(function(){
        $('#t3').animate({opacity:0},function(){
            $("#t3").css("display","none");
        });
        $('#modal-overlay').animate({opacity:0},function(){
            $("#modal-overlay").css({"visibility":"hidden","display":"none"})
        });
        $(".imagecon").css("background-color","#fff");
    })

//关于弹窗
function guanyu(obj,w,country,token){
    $(obj).css("background-color","#e0dddd");
    var biaoti = "";
    var neirong = "";

    if(country=='tw'){
        if(w==1){
            biaoti = "關於我們";
            neirong = "<p>WOWMALL 嚴選商城，秉承一貫的嚴謹態度,深入世界各地,嚴格把關所有商品的產地、工藝、原材料,甄選服飾、鞋包、居家、廚房、運動等各類商品,力求給你最優質的商品。</p>";
        }else if(w==2){
            biaoti = "用戶須知";
            neirong = "<p>本產品的實際使用效果根據個人情況決定，不保證每位用戶都能享受到所宣傳的效果。若有疑問請諮詢在線客服或通過電子郵箱( <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a> )聯絡我們，本公司享有最終解釋權。</p>";
        }else if(w==3){
            biaoti = "配送細節";
            neirong = "<p>下單成功之後，我們會按照下單先後順序進行配貨，配貨週期為3個工作日左右，一般到達時間為7個工作日左右。</p>";
        }else if(w==4){
            biaoti = "聯繫方式";
            neirong = "<p>24小時在線客服：<img src='../themes/angeltmall/images/service.png' style=' width:15px; height:auto;'><br> 郵箱：<a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a><br>如有任何問題，請致電或咨詢在線客服，謝謝您的配合。</p>";
        }else if(w==5){
            biaoti = "退換貨流程";
            neirong = "<p>如何退換：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;1.由於個人原因產生的退換貨：至收到商品之日起7天內，在不影響二次銷 售的情況下請聯繫我們的在線客服或發郵件至 <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>，售後 客服會在收到消息後的1-3個工作日內受理您的請求，退換貨所產生的運費 需自行承擔。</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;2.由於質量原因產生的退換貨：至收到商品之日起7天內，向售後服務中心發送郵件至 <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>，客服會在收到郵件後的1-3個工作日內受 理您的請求，退換貨所產生的運費由我方承擔。</p>\
                       <p>退換貨流程：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;確認收貨—申請退換貨—客服審核通過—用戶寄回商品—倉庫簽收驗貨—退 換貨審核—退款/換貨；退換貨請註明：訂單號、姓名、電話。</p>";
        }else if(w==6){
            biaoti = "物流查詢";
            neirong = "<input type='number' style='width:90%'/><a id='search' onclick=\"cc('"+token+"','"+country+"');\"><img src='../themes/angeltmall/images/search2.png' style='width:20px;height:20px;float:right;'/></a><div id='content' style='margin-top:20px;'></div>";
        }    
    }else if(country=='th'){
        if(w==1){
            biaoti = "เกี่ยวกับเรา";
            neirong = "<p>WOWMALL ให้ความสำคัญในด้านการเลือกที่ผลิต งานฝีมือและวัตถุดิบของสินค้าอย่างเข้มงวด เช่น เครื่องแต่งกาย กระเป๋า เครื่องครัว เครื่องกีฬา เพื่อให้สินค้าที่มีคุณภาพดีที่สุดกับคุณ.</p>";
        }else if(w==2){
            biaoti = "การแจ้งเตือนผู้ใช้";
            neirong = "<p>การใช้ผลิตภัณฑ์นี้จะขึ้นอยู่กับแต่ละสถานการณ์ ไม่มีการรับประกันว่าผู้ใช้ทุกคนจะได้รับผลลัพธ์ที่โฆษณา หากมีข้อสงสัย กรุณาติดต่อฝ่ายบริการลูกค้าออนไลน์หรือติดต่อทาง e-mail ( <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a> ) บริษัทของเรามีสิทธิในการตีความ.</p>";
        }else if(w==3){
            biaoti = "รายละเอียดการจัดส่ง";
            neirong = "<p>ทางเราจะจัดส่งสินค้าภายใน 3 วันโดยตามลำดับหลังจากสั่งซื้อสินค้าสำเร็จ และจะต้องใช้ระยะเวลาอีก 7 วันสำหรับการขนส่ง</p>";
        }else if(w==4){
            biaoti = "วิธีการติดต่อ";
            neirong = "<p>บริการลูกค้าออนไลน์ตลอด 24 ชม：<img src='../themes/angeltmall/images/service.png' style=' width:15px; height:auto;'><br> อีเมล：<a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a><br>หากคุณมีคำถามใด ๆ  โปรดติดต่อหรือปรึกษาบริการลูกค้าออนไลน์ของเรา ขอบคุณมาก.</p>";
        }else if(w==5){
            biaoti = "กระบวนการส่งคืน";
            neirong = "<p>วิธีการเปลี่ยน/คืน:</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;1.การเปลี่ยน/คืนสินค้าส่วนตัว: ภายใน 7 วันนับจากวันที่ได้รับสินค้า โปรดติดต่อฝ่ายบริการลูกค้าออนไลน์ของเราหรือส่งอีเมลไปที่ <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>,โดยไม่มีผลต่อยอดขายรอง ฝ่ายบริการลูกค้าจะตอบแทนภายใน 1-3 วันหลังจากได้รับข้อความ คุณต้องการชำระเงินของค่าขนส่ง.</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;2.เหตุผลเกี่ยวกับคุณภาพ: ภายใน 7 วันนับจากวันที่ได้รับสินค้า โปรดติดต่อฝ่ายบริการลูกค้าออนไลน์ของเราหรือส่งอีเมลไปที่ <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>,โดยไม่มีผลต่อยอดขายรอง ฝ่ายบริการลูกค้าจะตอบแทนภายใน 1-3 วันหลังจากได้รับข้อความ ทางเราจะชำระเงินของค่าขนส่ง.</p>\
                       <p>กระบวนการส่งคืน:</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;ได้รับสินค้า - ใบสมัครสำหรับการรับคืน - การตรวจสอบการบริการลูกค้า - ส่งคืนสินค้า - การตรวจสอบฝ่ายคลังสินค้า - การตรวจสอบผลตอบแทน - การคืนเงิน / การแลกเปลี่ยนสินค้า      กรุณาระบุ: เลขที่ใบสั่งซื้อ ชื่อ เบอร์โทรศัพท์.</p>";
        }else if(w==6){
            biaoti = "สอบถามเกี่ยวกับโลจิสติกส์";
            neirong = "<input type='number' style='width:90%'/><a id='search' onclick=\"cc('"+token+"','"+country+"');\"><img src='../themes/angeltmall/images/search2.png' style='width:20px;height:20px;float:right;'/></a><div id='content' style='margin-top:20px;'></div>";
        }
    }else if(country=='id'){
        if(w==1){
            biaoti = "Tentang Kami";
            neirong = "<p>Angeltmall Toko online terbaik,  berpegang sikap serius untuk berkeliling di seluruh dunia mencari produk yg baik, dan mengendalikan kualitas material produk, tersedia produk pakaian, tas, sepatu, dapur,olahraga dll, kami berupaya untuk menyediakan anda produk unggulan.</p>";
        }else if(w==2){
            biaoti = "Pemberitahuan Pelanggan";
            neirong = "<p>Efek pengguna produk ini tergantung pada kondisi pribadi, kami tidak jamin setiap pelanggan bisa capai efek pengguna seperti iklannya. Jika ada pertanyaan silakan hubungi layan online atau kirim email ke ( <a href='mailto:support"+country+"@angeltmall.store' style='color:#F8770E'>Support"+country+"@angeltmall.store</a> ), perusahaan kami memiliki kekuatan interpretasi terakhir.</p>";
        }else if(w==3){
            biaoti = "Tentang Pengiriman";
            neirong = "<p>Setelah sukses memesan, kami akan cepat memproses pesanan dengan urutannya, waktu persiapan sekitar 3 hari kerja, waktu pengiriman biasanya sekitar 7 hari kerja.</p>";
        }else if(w==4){
            biaoti = "Hubungi Kami";
            neirong = "<p>Layanan Online Chat 24 Jam ：<img src='../themes/angeltmall/images/service.png' style=' width:15px; height:auto;float:none;'><br> Eamil：<a href='mailto:support"+country+"@angeltmall.store' style='color:#F8770E'>Support"+country+"@angeltmall.store</a><br>Jika ada pertanyaan apapun, silakan hubungi staf layanan pelanggan, terima kasih atas kerjasamanya.</p>";
        }else if(w==5){
            biaoti = "Prosedur penukaran atau pengembalian produk";
            neirong = "<p>Cara menukar dan mengembali produk：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;1.Jika karena alasan pribadi untuk mengajukan permohonan pengembalian: dalam 7 hari setelah tanda terima barang, kirim email ke pusat purja jual kami <a href='mailto:support"+country+"@angeltmall.store' style='color:#F8770E'>Support"+country+"@angeltmall.store</a>，staf layanan pelanggan kami akan menangani permintaan Anda dalam 1-3 hari, dan biaya ongkir produk sepenuhnya ditanggung oleh konsumen.</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;2.Jika karena masalah kualitas produk untuk mengajukan permohonan pengembalian: dalam 7 hari setelah tanda terima barang, kirim email ke pusat purja jual kami <a href='mailto:support"+country+"@angeltmall.store' style='color:#F8770E'>Support"+country+"@angeltmall.store</a>， staf layanan pelanggan kami akan menangani permintaan Anda dalam 1-3 hari, dan biaya ongkir akan ditanggung oleh pihak kami.</p>\
                       <p>Prosedur penukaran atau pengembalian produk：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;Konfirmasi terima barang ----Mengajukan permohonan pengembalian---Staf layanan pelanggan menyetujui----Konsumen kirim kembali barangnya-----Gudang kami terima dan periksa---Audit penukaran atau pengembalian-----Penukaran atau pengembalian produk, silakan cacatan : nomor pesanan, nama lengkap, nomor HP.</p>";
        }else if(w==6){
            biaoti = "Lacak pesanan ";
            neirong = "<input type='number' style='width:90%'/><a id='search' onclick=\"cc('"+token+"','"+country+"');\"><img src='../themes/angeltmall/images/search2.png' style='width:20px;height:20px;float:right;'/></a><div id='content' style='margin-top:20px;'></div>";
        }
    }else if(country=='uae'){
        if(w==1){
            biaoti = "عنا";
            neirong = "<p>لمختار مول،تلتزم بالموقف الصارم الثابت،وتتعمق في جميع أنحاء العالم،وتتحقق بدقة من المنشأ ، والحرف والمواد الخام من جميع WOWMALL. السلع،واختيار أنواع مختلفة من السلع مثل الملابس ،والأحذية والحقائب ،والمنزل ،والمطبخ،والرياضة،والسعي اإعطائك أفضل المنتجات عالية الجودة</p>";
        }else if(w==2){
            biaoti = "تعليمات المستخدم";
            neirong = "<p>،يتم تحديد تأثير الاستخدام الفعلي لهذا المنتج حسب ظروف فردية ، ولا يمكن الضمان أن تمتع كل مستخدم بتأثير العرض الترويجي. إذا كان لديك أي أسئلة  . تتمتع الشركة بحق التفسير النهائي ، ( <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a> )يرجى الاتصال بخدمة العملاء عبر الإنترنت أو الاتصال بنا عبر البريد الإلكتروني.</p>";
        }else if(w==3){
            biaoti = "تفاصيل التسليم";
            neirong = "<p>. بعد تقديم الطلب بنجاح ، سنقوم بترتيب المنتجات وفقا لترتيب الطلبات.ستكون وقت التسليم حوالي 3 أيام ، وسوف يكون وقت الوصول العام حوالي 7 أيام</p>";
        }else if(w==4){
            biaoti = "طريقة الاتصال";
            neirong = "<p>خدمة العملاء عبر الإنترنت على  24 ساعة：<img src='../themes/angeltmall/images/service.png' style=' width:15px; height:auto;'><br> <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>صندوق البريد<br>إذا كان لديك أي أسئلة ، يرجى الاتصال بخدمة العملاء عبر الإنترنت أو التشاور ، وشكرا لتعاونكم</p>";
        }else if(w==5){
            biaoti = "عملية التغيير والتراجع";
            neirong = "<p>كيفية التغيير والتراجع：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;1.التغيير والتراجع لأسباب شخصية : في غضون 7 أيام من تاريخ استلام البضاعة و لا تؤثر في حالة البيع مرة أخري،يرجى الاتصال بخدمة العملاء عبر خدمة العملاء لما بعد البيع طلبك في غضون 1-3 أيام عمل بعد استلام الرسالة <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>，الإنترنت أو إرسال بريد إلكتروني إلي . يجب أن يتحمل العميل تكاليف شحن التغيير والتراجع،.</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;2.<a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>التغيير والتراجع  بسبب أسباب الجودة :في غضون 7 أيام من تاريخ استلام البضاعة، إرسال بريد إلكتروني إلى مركز خدمة ما بعد البيع . العملاء لما بعد البيع طلبك في غضون 1-3 أيام عمل بعد استلام الرسالة ، نتحمل العميل تكاليف شحن التغيير والتراجع،</p>\
                       <p>: عملية التغيير والتراجع</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;;تأكيد الاستلام - طلب التغيير والتراجع - موافقة خدمة العملاء - البضائع المعادة من قبل المستخدم - استلام التخزين للتفتيش - مراجعة التغيير والتراجع - الاسترداد / التغيير يرجى التحديد: رقم الطلب ، الاسم ، رقم الهاتف</p>";
        }else if(w==6){
            biaoti = "استعلام اللوجستية";
            neirong = "<input type='number' style='width:90%'/><a id='search' onclick=\"cc('"+token+"','"+country+"');\"><img src='../themes/angeltmall/images/search2.png' style='width:20px;height:20px;float:right;'/></a><div id='content' style='margin-top:20px;'></div>";
        }
    }else{
        if(w==1){
            biaoti = "About us";
            neirong = "<p>WOWMALL selective mall, adhering to the consistent rigorous attitude, goes deep into all parts of the world, strictly checks the origin, crafts, and raw materials of all commodities, selects various kinds of commodities such as clothing, shoes and bags, home, kitchen and sports, and strives to give you the best quality products.</p>";
        }else if(w==2){
            biaoti = "User instructions";
            neirong = "<p>The actual application effect of this product is based on individual circumstances, and it is not guaranteed that every user can enjoy the effect of the promotion. If you have any questions, please contact online customer service or contact us via e-mail ( <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a> ).The company reserves the right of final explanation.</p>";
        }else if(w==3){
            biaoti = "Delivery details";
            neirong = "<p>We will arrange the products by the order sequence after the success of order .The lead time is about 3 days, in general, the delivery time is about 7 days.</p>";
        }else if(w==4){
            biaoti = "Contact us";
            neirong = "<p>24-hour online customer service：<img src='../themes/angeltmall/images/service.png' style=' width:15px; height:auto;'><br> E-mail：<a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a><br>If you have any questions, please call or consult online customer service, thank you for your cooperation.</p>";
        }else if(w==5){
            biaoti = "Returns and exchanges process";
            neirong = "<p>How to return and exchange：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;1.Return and exchange due to personal reasons: Within 7 days from the date of receipt of the goods, please contact our online customer service or send an e-mail to <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>,without affecting the secondary sales. After-sales customer service will handle it within 1-3 working days upon the acceptance of your request, the freight generated by the return and exchange shall be borne by the customers themselves.</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;2.Return and exchange due to quality reasons: Within 7 days from the date of receipt of the goods, please send an e-mail to after-sale service center, email address <a href='mailto:support"+country+"@wowmall.store' style='color:#F8770E'>Support"+country+"@wowmall.store</a>,After-sales customer service will handle it within 1-3 working days upon the acceptance of your request, the freight generated by the return and exchange must be borne by our party.</p>\
                       <p>Returns and exchanges process：</p>\
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;Confirm receipt - apply for returns - customer service approves - users return goods - warehouse receipt check – exchanges verification- refunds /  exchanges; refunds notices: order number, name, telephone number must be noted.</p>";
        }else if(w==6){
            biaoti = "Logistic query";
            neirong = "<input type='number' style='width:90%'/><a id='search' onclick=\"cc('"+token+"','"+country+"');\"><img src='../themes/angeltmall/images/search2.png' style='width:20px;height:20px;float:right;'/></a><div id='content' style='margin-top:20px;'></div>";
        }
    }



    var a = document.body.clientHeight;
    var b = a+48;
    var c = document.body.clientWidth-32;
    $('#biaoti').html(biaoti);
    $('#neirong').html(neirong);
    $('#modal-overlay').css({"height":b+'px',"visibility":"visible","display":"block"});
    $('#t3').css({"display":"block","width":c});
    $('#modal-overlay').animate({opacity:1});
    $('#t3').animate({opacity:1});
}

function cc(csrfToken,country2){
      var search = $("input[type='number']").val().trim();
      if(search==""||search===null){
        if(country2=='tw'){
          tan("搜索内容不能为空",false,2);
        }else if(country2=='uae'){
          tan("لا يمكن أن يكون رقم الطلب فارغا",false,2);
        }else if(country2=='id'){
          tan("Nomor pesanan tidak boleh kosng",false,2);
        }else if(country2=='th'){
          tan("เลขที่สั่งซื้อต้องเต็มไว้",false,2);
        }else{
          tan("The order number is not empty",false,2);
        }
        
        return false;
      }

      $.post('./site/logistics?country='+country2,{'_csrf':csrfToken,'order_id':search},function(e){
        var data = JSON.parse(e);
        if(data.code==200){
          var content = data.data;
          var str = "";
          for(var i=0;i<content.length;i++){
            str += "<div style='white-space:normal;'>"+content[i].status+"</div>\
                    <div style='margin-bottom:10px;'>\
                      <div>"+content[i].time+"------"+content[i].mailing+"</div>\
                    </div>";
          }


          $('#content').html(str);
          
        }else{

          $('#content').html('');
          tan(data.data,false,2);
        }
      })
};