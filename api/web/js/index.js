require(['jquery','swiper','widget'],function($, Swiper){
    var mySwiper1 = new window.Swiper('.swiper-container', {
        autoplay: 2000,
        loop: false,
        autoHeight:true,
        pagination: '.swiper-pagination',
        paginationType: 'custom',
        paginationCustomRender: function(swiper, current, total) {
            var text = "";
            text = '<span class="swiper-pagination-current">' + current + ' / <span class="swiper-pagination-total">'+ total +'</span>';
            return text;
        }
    });
    //倒计时
    widget.timeSet(2018, 4, 20, hours); //timer:倒计时的id，tw:地区语言

})


//返回顶部
$('.top').on('click',function(){
    $('body,html').animate({ scrollTop: 0 }, 500);
})

//最新订单
require(['commentsScroll']);
var liNum = $('.picList li');
for (var i =0; i <= liNum.length; i++) {
    liNum.eq(2*i+1).addClass('odd');
}

var price = parseInt($('.combo.tab-sel').attr('data-price'));

function addnumber(){
    $('#num').val(parseInt($('#num').val())+1);
    var num = $('#num').val();
    $('.textWrap .tt span').html(num);
    refresh_price(num);
}
function minnumber(){
    if($('#num').val() > 1){
        $('#num').val(parseInt($('#num').val())-1);
        var num = $('#num').val();
        $('.textWrap .tt span').html(num);
        refresh_price(num);
    }
}
function refresh_price(num){
    $('input[name="price"]').val(num*price);
}
//懒加载
function lazyload(){
    var screenHeight = $(window).height();
    var imgdata = $('.m-img').html();
    var img = imgdata.replace(/<img src="/g,'<img class="lazyload" src="" data-img="');
    $('.m-img').html(img);
    showImg(screenHeight);
    window.addEventListener('scroll', function(){
        var img = $('.lazyload');
        if(img.length<=0){
            window.removeEventListener('scroll',arguments.callee);
            return false;
        }else{
            var screenHeight = $(document).scrollTop();
            setTimeout(function(){
                showImg(screenHeight+screenHeight);
            },300)
        }
            
    })
}
function showImg(height){
    var img = $('.lazyload');
    for (var i = 0; i < img.length; i++) {
        var top = img.eq(i).offset().top;
        var src = img.eq(i).attr('data-img');
        if (top<=height) {
            img.eq(i).attr('src',src);
        }
    }
}