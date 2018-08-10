/*
*	全国三级城市联动 js版
*/
function Dsy(){
    this.Items = {};
}
Dsy.prototype.add = function(id,iArray){
    this.Items[id] = iArray;
}
Dsy.prototype.Exists = function(id){
    if(typeof(this.Items[id]) == "undefined") return false;
    return true;
}

function change(v){
    var str="0";
    for(i=0;i<v;i++){
        str+=("_"+(document.getElementById(s[i]).selectedIndex-1));
    };
    var ss=document.getElementById(s[v]);
    with(ss){
        length = 0;
        options[0]=new Option(opt0[v],opt0[v]);
        if(v && document.getElementById(s[v-1]).selectedIndex>0 || !v){
            if(dsy.Exists(str)){
                ar = dsy.Items[str];
                for(i=0;i<ar.length;i++){
                    options[length]=new Option(ar[i],ar[i]);
                }//end for
                if(v){ options[0].selected = true; }
            }
        }//end if v
        if(++v<s.length){change(v);}
    }//End with
}

var dsy = new Dsy();


dsy.add("0",["Hà Nội","TP.Hồ Chí Minh","An Giang","Bà Rịa -  Vũng Tàu","Bắc Giang","Bắc Kạn","Bạc Liêu","Bắc Ninh","Bến Tre","Bình Dương","Bình Định ","Bình Phước ","Bình Thuận","Cà Mau","Cần Thơ","Cao Bằng","Đà Nẵng","Đăk Lăk ","Đăk Nông","Điện Biên","Đồng Nai","Đồng Tháp","Gia Lai","Hà Giang","Hà Nam","Hà Tĩnh","Hải Dương","Hậu Giang","Hòa Bình","Hưng Yên","Khánh Hòa","Kiên Giang","Kon Tum","Lai Châu","Lâm Đồng","Lạng Sơn","Lào Cai","Long An ","Nam Định","Nghệ An","Ninh Bình","Ninh Thuận","Phú Thọ","Phú Yên","Quảng Bình","Quảng Nam ","Quảng Ngãi","Quảng Ninh","Quảng Trị","Sóc Trăng","Sơn La","Tây Ninh","Thái Bình","Thái Nguyên","Thanh Hóa","Thừa Thiên Huế","Tiền Giang","Trà Vinh","Tuyên Quang","Vĩnh Long","Vĩnh Phúc","Yên Bái","Hải Phòng"]);

dsy.add("0_0",["Thị Xã Sơn Tây","Quận Thanh Xuân","Quận Tây Hồ","Quận Nam Từ Liêm","Quận Long Biên","Quận Hoàng Mai","Quận Hoàn Kiếm","Quận Hai Bà Trưng","Quận Hà Đông","Quận Đống Đa","Quận Cầu Giấy","Quận Bắc Từ Liêm","Quận Ba Đình","Huyện Ứng Hòa","Huyện Thường Tín","Huyện Thanh Trì","Huyện Thanh Oai","Huyện Thạch Thất","Huyện Sóc Sơn","Huyện Quốc Oai","Huyện Phúc Thọ","Huyện Phú Xuyên","Huyện Mỹ Đức","Huyện Mê Linh","Huyện Hoài Đức","Huyện Gia Lâm","Huyện Đông Anh","Huyện Đan Phượng","Huyện Chương Mỹ","Huyện Ba Vì"]);

dsy.add("0_1",["Quận Thủ Đức","Quận Tân Phú","Quận Tân Bình","Quận Phú Nhuận","Quận Gò Vấp","Quận Bình Thạnh","Quận Bình Tân","Quận 9","Quận 8","Quận 7","Quận 6","Quận 5","Quận 4","Quận 3","Quận 2","Quận 12","Quận 11","Quận 10","Quận 1","Huyện Nhà Bè","Huyện Hóc Môn","Huyện Củ Chi","Huyện Cần Giờ","Huyện Bình Chánh"]);

dsy.add("0_2",["Thị Xã Tân Châu","Thị Xã Châu Đốc","Thành Phố Long Xuyên","Huyện Tri Tôn","Huyện Tịnh Biên","Huyện Thoại Sơn","Huyện Phú Tân","Huyện Chợ Mới","Huyện Châu Thành","Huyện Châu Phú","Huyện An Phú"]);

dsy.add("0_3",["Thành Phố Vũng Tàu","Thành Phố Bà Rịa","Huyện Xuyên Mộc","Huyện Tân Thành","Huyện Long Thành","Huyện Long điền","Huyện Đất Đỏ","Huyện Côn đảo","Huyện Châu đức"]);

dsy.add("0_4",["Thành Phố Bắc Giang","Huyện Yên Thế","Huyện Yên Dũng","Huyện Việt Yên","Huyện Tân Yên","Huyện Sơn Động","Huyện Lục Ngạn","Huyện Lục Nam","Huyện Lạng Giang","Huyện Hiệp Hòa"]);

dsy.add("0_5",["Thành Phố Bắc Kạn","Huyện Pắc Nặm","Huyện Ngân Sơn","Huyện Na Rì","Huyện Chợ Mới","Huyện Chợ đồn","Huyện Bạch Thông","Huyện Ba Bể"]);

dsy.add("0_6",["Thành Phố Bạc Liêu","Huyện Vĩnh Lợi","Huyện Phước Long","Huyện Hồng Dân","Huyện Hòa Bình","Huyện Giá Rai","Huyện Đông Hải"]);

dsy.add("0_7",["Thành Phố Bắc Ninh","Huyện Yên Phong","Huyện Từ Sơn","Huyện Tiên Du","Huyện Thuận Thành","Huyện Quế Võ","Huyện Lương Tài","Huyện Gia Bình"]);

dsy.add("0_8",["Thành Phố Bến Tre","Huyện Thạnh Phú","Huyện Mỏ Cày Nam","Huyện Mỏ Cày Bắc","Huyện Giồng Trôm","Huyện Chợ Lách","Huyện Châu Thành","Huyện Bình Đại","Huyện Ba Tri"]);

dsy.add("0_9",["Thành Phố Thủ Dầu Một","Huyện Thuận An","Huyện Tân Uyên","Huyện Phú Giáo","Huyện Dĩ An","Huyện Dầu Tiếng","Huyện Bến Cát","Huyện Bàu Bàng","Huyện Bắc Tân Uyên"]);

dsy.add("0_10",["Thành Phố Quy Nhơn","Huyện Vĩnh Thạnh","Huyện Vân Canh","Huyện Tuy Phước","Huyên Tây Sơn","Huyện Phù Mỹ","Huyện Phù Cát","Huyện Hoài Nhơn","Huyện Hoài ân","Huyện An Nhơn","Huyện An Lão"]);

dsy.add("0_11",["Thị Xã Phước Long","Thị Xã Đồng Xoài","Thị Xã Bình Long","Huyện Phú Riềng","Huyện Lộc Ninh","Huyện Hớn Quản","Huyện Đồng Phú","Huyện Chơn Thành","Huyện Bù Gia Mập","Huyện Bù đốp","Huyện Bù đăng"]);

dsy.add("0_12",["Thị Xã La Gi","Thành Phố Phan Thiết","Huyện Tuy Phong","Huyện Tánh Linh","Huyện Hàm Thuận Nam","Huyện Hàm Thuận Bắc","Huyện Hàm Tân","Huyện Đức Linh","Huyện Đảo Phú Quý","Huyện Bắc Bình"]);

dsy.add("0_13",["Thành Phố Cà Mau","Huyện U Minh","Huyện Trần Văn Thời","Huyện Thới Bình","Huyện Phú Tân","Huyện Ngọc Hiển","Huyện Năm Căn","Huyện Đầm Dơi","Huyện Cái Nước"]);

dsy.add("0_14",["Thị Xã Ngã Bảy","Quận Thốt Nốt","Quận Ô Môn","Quận Ninh Kiều","Quận Cái Răng","Quận Bình Thủy","Huyện Vĩnh Thạnh","Huyện Thới Lai","Huyện Phụng Hiệp","Huyện Phong điền","Huyện Cờ Đỏ","Huyện Châu Thành A"]);

dsy.add("0_15",["Thành Phố Cao Bằng","Huyện Trùng Khánh","Huyện Trà Lĩnh","Huyện Thông Nông","Huyện Thạch An","Huyện Quảng Uyên","Huyện Phục Hòa","Huyện Nguyên Bình","Huyện Hòa An","Huyện Hà Quảng","Huyện Hạ Lang","Huyện Bảo Lâm","Huyện Bảo Lạc"]);

dsy.add("0_16",["Quận Thanh Khê","Quận Sơn Trà","Quận Ngũ Hành Sơn","Quận Liên Chiểu","Quận Hải Châu","Quận Cẩm Lệ","Huyện Hoàng Sa","Huyện Hòa Vang"]);

dsy.add("0_17",["Thị Xã Buôn Hồ","Thành Phố Buôn Ma Thuột","Huyện M'Đrắk","Huyện Lắk","Huyện Krông Pắk","Huyện Krông Năng","Huyện Krông Búk","Huyện Krông Bông","Huyện Krông A Na","Huyện Ea Súp","Huyện Ea Kar","Huyện Ea H'leo","Huyện Cư M'gar","Huyện Cư Kuin","Huyện Buôn đôn"]);

dsy.add("0_18",["Thị Xã Gia Nghĩa","Huyện Tuy đức","Huyện Krông Nô","Huyện Đắk Song","Huyện Đăk R'lấp","Huyện Đắk Mil","Huyện Đắk Glong","Huyện Cư Jút"]);

dsy.add("0_19",["Thị Xã Mường Lay","Thành Phố Điện Biên Phủ","Huyện Tuần Giáo","Huyện Tủa Chùa","Huyện Nậm Pồ","Huyện Mường Nhé","Huyện Mường Chà","Huyện Mường Áng","Huyện Điện Biên Đông","Huyện Điện Biên"]);

dsy.add("0_20",["Thị Xã Long Khánh","Thành Phố Biên Hòa","Huyện Xuân Lộc","Huyện Vĩnh Cửu","Huyện Trảng Bom","Huyện Thống Nhất","Huyện Tân Phú","Huyện Nhơn Trạch","Huyện Long Thành","Huyện Định Quán","Huyện Cẩm Mỹ"]);

dsy.add("0_21",["Thị Xã Sa Đéc","Thành Phố Cao Lãnh","Huyện Tháp Mười","Huyện Thanh Bình","Huyện Tân Hồng","Huyện Tam Nông","Huyện Lấp Vò","Huyện Lai Vung","Huyện Hồng Ngự","Huyện Châu Thành","Huyện Cao Lãnh"]);

dsy.add("0_22",["Thị Xã Ayun Pa","Thị Xã An Khê","Thành Phố Pleiku","Huyện Phú Thiện","Huyện Mang Yang","Huyện Krông Pa","Huyện Kông Chro","Huyện Kbang","Huyện Ia Pa","Huyện Ia Grai","Huyện Đức Cơ","Huyện Đăk Pơ","Huyện Đăk Đoa","Huyện Chư Sê","Huyện Chư Pưh","Huyện Chư Prông","Huyện Chư Păh"]);

dsy.add("0_23",["Thành Phố Hà Giang","Huyện Yên Minh","Huyện Xín Mần","Huyện Vị Xuyên","Huyện Quang Bình","Huyện Quản Bạ","Huyện Mèo Vạc","Huyện Hoàng Su Phì","Huyện Đồng Văn","Huyện Bắc Quang","Huyện Bắc Mê"]);

dsy.add("0_24",["Thành Phố Phủ Lý","Huyện Thanh Liêm","Huyện Lý Nhân","Huyện Kim Bảng","Huyện Duy Tiên","Huyện Bình Lục"]);

dsy.add("0_25",["Thị Xã Hồng Lĩnh","Thành Phố Hà Tĩnh","Huyện Vũ Quang","Huyện Thạch Hà","Huyện Nghi Xuân","Huyện Lộc Hà","Huyện Kỳ Anh","Huyện Hương Sơn","Huyện Hương Khê","Huyện Đức Thọ","Huyện Can Lộc","Huyện Cẩm Xuyên"]);

dsy.add("0_26",["Thị Xã Chí Linh","Thành Phố Hải Dương","Huyện Tứ Kỳ","Huyện Thanh Miện","Huyện Thanh Hà ","Huyện Ninh Giang","Huyện Nam Sách","Huyện Kinh Môn","Huyện Kim Thành ","Huyện Gia Lộc","Huyện Cẩm Giàng","Huyện Bình Giang"]);

dsy.add("0_27",["Thị Xã Ngã Bảy","Thành Phố Vị Thanh","Huyện Vị Thủy","Huyện Phụng Hiệp","Huyện Long Mỹ","Huyện Châu Thành A","Huyện Châu Thành "]);

dsy.add("0_28",["Thành Phố Hòa Bình","Huyện Yên Thủy","Huyện Tân Lạc","Huyện Mai Châu","Huyện Lương Sơn","Huyện Lạc Thủy","Huyện Lạc Sơn","Huyện Kỳ Sơn","Huyện Kim Bôi","Huyện Đà Bắc","Huyện Cao Phong"]);

dsy.add("0_29",["Thành Phố Hưng Yên","Huyện Yên Mỹ","Huyện Văn Lâm","Huyện Văn Giang","Huyện Tiên Lữ ","Huyện Phù Cừ","Huyện Mỹ Hào","Huyện Kim Động","Huyện Khoái Châu","Huyện Ân Thi"]);

dsy.add("0_30",["Thành Phố Nha Trang","Thành Phố Cam Ranh","Huyện Vạn Ninh","Huyện Ninh Hòa","Huyện Khánh Vĩnh","Huyện Khánh Sơn","Huyện Đảo Trường Sa","Huyện Diên Khánh","Huyện Cam Lâm"]);

dsy.add("0_31",["Thị Xã Hà Tiên","Thành Phố Rạch Giá","Huyện Vĩnh Thuận","Huyện U Minh Thượng","Huyện Tân Hiệp","Huyện Kiên Lương","Huyện Kiên Hải","Huyện Hòn Đất","Huyện Gò Quao","Huyện Giồng Riềng","Huyên Giang Thành","Huyện Đảo Phú Quốc","Huyện Châu Thành","Huyện An Minh","Huyện An Biên"]);

dsy.add("0_32",["Thành Phố Kon Tum","Huyện Tu Mơ Rông","Huyện Sa Thầy","Huyện Ngọc Hồi","Huyện Kon Rẫy","Huyện Kon Plông","Huyện Ia H'Drai","Huyện Đắk Tô","Huyện Đắk Hà","Huyện Đắk Glei"]);

dsy.add("0_33",["Thành phố Lai Châu","Huyện Than Uyên","Huyện Tân Uyên","Huyện Tam Đường","Huyện Sìn Hồ","Huyện Phong Thổ","Huyện Nậm Nhùn","Huyện Mường Tè"]);

dsy.add("0_34",["Thị Xã Bảo Lộc","Thành Phố Đà Lạt","Huyện Lâm Hà","Huyện Lạc Dương","Huyện Đức Trọng","Huyện Đơn Dương","Huyện Đam Rông","Huyện Đạ Tẻh","Huyện Đạ Huoai","Huyện Di Linh","Huyện Cát Tiên","Huyện Bảo Lâm"]);

dsy.add("0_35",["Thành Phố Lạng Sơn","Huyện Văn Quan","Huyện Văn Lãng","Huyện Tràng Định","Huyện Lộc Bình","Huyện Hữu Lũng","Huyện Đình Lập","Huyện Chi Lăng","Huyện Cao Lộc","Huyện Bình Gia","Huyện Bắc Sơn"]);

dsy.add("0_36",["Thành Phố Lào Cai","Huyện Văn Bàn","Huyện Si Ma Cai","Huyện Sa Pa","Huyện Mường Khương","Huyện Bát Xát","Huyện Bảo Yên","Huyện Bảo Thắng","Huyện Bắc Hà"]);

dsy.add("0_37",["Thị Xã Kiến Tường","Thành Phố Tân An","Huyện Vĩnh Hưng","Huyện Thủ Thừa","Huyện Thạnh Hóa","Huyện Tân Trụ","Huyện Tân Thạnh","Huyện Tân Hưng","Huyện Mộc Hóa","Huyện Đức Huệ","Huyện Đức Hòa","Huyện Châu Thành","Huyện Cần Guộc","Huyện Cần đước","Huyện Bến Lức"]);

dsy.add("0_38",["Thành Phố Nam định","Huyện Ý Yên","Huyện Xuân Trường","Huyện Vụ Bản","Huyện Trực Ninh","Huyện Nghĩa Hưng","Huyện Nam Trực","Huyện Mỹ Lộc","Huyện Hải Hậu","Huyện Giao Thủy"]);

dsy.add("0_39",["Thị Xã Thái Hòa","Thị xã Hoàng Mai","Thị Xã Cửa Lò","Thành Phố Vinh","Huyện Yên Thành","Huyện Tương Dương","Huyện Thanh Chương","Huyện Tân Kỳ","Huyện Quỳnh Lưu","Huyện Quỳ Hợp","Huyện Quỳ Châu","Huyện Quế Phong","Huyện Nghĩa Đàn","Huyện Nghi Lộc","Huyện Nam Đàn","Huyện Kỳ Sơn","Huyện Hưng Nguyên","Huyện Đô Lương","Huyện Diễn Châu","Huyện Con Cuông","Huyện Anh Sơn"]);

dsy.add("0_40",["Thị Xã Tam điệp","Thành Phố Ninh Bình","Huyện Yên Mô","Huyện Yên Khánh","Huyện Ý Yên","Huyện Nho Quan","Huyện Kim Sơn","Huyện Hoa Lư","Huyện Gia Viễn"]);

dsy.add("0_41",["Thành phố Phan Rang - Tháp Chàm","Huyện Thuận Nam","Huyện Thuận Bắc","Huyện Ninh Sơn","Huyện Ninh Phước","Huyện Ninh Hải","Huyện Bác Ái"]);

dsy.add("0_42",["Thi Xã Phú Thọ","Thành Phố Việt Trì","Huyện Yên Lập","Huyện Thanh Thủy","Huyện Thanh Sơn","Huyện Thanh Ba","Huyện Tân Sơn","Huyện Tam Nông","Huyện Phù Ninh","Huyện Lâm Thao","Huyện Hạ Hòa","Huyện Đoan Hùng","Huyện Cẩm Khê"]);

dsy.add("0_43",["Thị Xã Sông Cầu","Thành Phố Tuy Hòa","Huyện Tuy An","Huyện Tây Hòa","Huyện Sông Hinh","Huyện Sơn Hòa","Huyện Phú Hòa","Huyện Đồng Xuân","Huyện Đông Hòa"]);

dsy.add("0_44",["Thị xã Ba Đồn","Thành Phố Đồng Hới","Huyện Tuyên Hóa","Huyện Quảng Trạch","Huyện Quảng Ninh","Huyện Minh Hóa","Huyện Lệ Thủy","Huyện Bố Trạch"]);

dsy.add("0_45",["Thành Phố Tam Kỳ","Thành Phố Hội An","Huyện Tiên Phước","Huyện Thăng Bình","Huyện Tây Giang","Huyện Quế Sơn","Huyện Phước Sơn","Huyện Phú Ninh","Huyện Núi Thành","Huyện Nông Sơn","Huyện Nam Trà My","Huyện Nam Giang","Huyện Hiệp Đức","Huyện Đông Giang","Huyện Điện Bàn","Huyện Đại Lộc","Huyện Duy Xuyên","Huyện Bắc Trà My"]);

dsy.add("0_46",["Thành Phố Quảng Ngãi","Huyện Tư Nghĩa","Huyện Trà Bồng","Huyện Tây Trà","Huyện Sơn Tịnh","Huyện Sơn Tây","Huyện Sơn Hà","Huyện Nghĩa Hành","Huyện Mộ đức","Huyện Minh Long","Huyện Lý Sơn","Huyện đức Phổ","Huyện Bình Sơn","Huyện Ba Tơ"]);

dsy.add("0_47",["Thị Xã Quảng Yên","Thành phố Uông Bí","Thành Phố Móng Cái","Thành Phố Hạ Long","Thành phố Cẩm Phả","Huyện Vân Đồn","Huyện Tiên Yên","Huyện Hoành Bồ","Huyện Hải Hà","Huyện Đông Triều","Huyện Đầm Hà","Huyện Cô Tô","Huyện Bình Liêu","Huyện Ba Chẽ"]);

dsy.add("0_48",["Thị Xã Quảng Trị","Thành Phố Đông Hà","Huyện Vĩnh Linh","Huyện Triệu Phong","Huyện Hướng Hóa","Huyện Hải Lăng","Huyện Gio Linh","Huyện Đảo Cồn Cỏ","Huyện Đa Krông","Huyện Cam Lộ"]);

dsy.add("0_49",["Thị Xã Vĩnh Châu","Thị trấn Trần Đề","Thành Phố Sóc Trăng","Huyện Thạnh Trị","Huyện Ngã Năm","Huyện Mỹ Xuyên","Huyện Mỹ Tú","Huyện Long Phú","Huyện Kế Sách","Huyện Cù Lao Dung","Huyện Châu Thành"]);

dsy.add("0_50",["Thành phố Sơn La","Huyện Yên Châu","Huyện Vân Hồ","Huyện Thuận Châu","Huyện Sốp Cộp","Huyện Sông Mã","Huyện Quỳnh Nhai","Huyện Phù Yên","Huyện Mường La","Huyện Mộc Châu","Huyện Mai Sơn","Huyện Bắc Yên"]);

dsy.add("0_51",["Thành Phố Tây Ninh","Huyện Trảng Bàng","Huyện Tân Châu","Huyện Tân Biên","Huyện Hòa Thành","Huyện Gò Dầu","Huyện Dương Minh Châu","Huyện Châu Thành","Huyện Bến Cầu"]);

dsy.add("0_52",["Thành Phố Thái Bình","Huyện Vũ Thư","Huyện Tiền Hải","Huyện Thái Thụy","Huyện Quỳnh Phụ","Huyện Quỳnh Côi","Huyện Kiến Xương","Huyện Hưng Hà","Huyện Đông Hưng"]);

dsy.add("0_53",["Thị Xã Sông Công","Thành Phố Thái Nguyên","Huyện Võ Nhai","Huyện Phú Lương ","Huyện Phú Bình","Huyện Phổ Yên","Huyện Đồng Hỷ","Huyện Định Hóa","Huyện Đại Từ"]);

dsy.add("0_54",["Thị Xã Sầm Sơn","Thị Xã Bỉm Sơn","Thành Phố Thanh Hóa","Huyện Yên định","Huyện Vĩnh Lộc","Huyện Triệu Sơn","Huyện Tĩnh Gia","Huyện Thường Xuân","Huyện Thọ Xuân","Huyện Thiệu Hóa","Huyện Thạch Thành","Huyện Quảng Xương","Huyện Quan Sơn","Huyện Quan Hóa","Huyện Nông Cống","Huyện Như Xuân","Huyện Như Thanh","Huyện Ngọc Lặc","Huyện Nga Sơn","Huyện Mường Lát","Huyện Lang Chánh","Huyện Hoằng Hóa","Huyện Hậu Lộc","Huyện Hà Trung","Huyện Đông Sơn","Huyện Cẩm Thủy","Huyện Bá Thước"]);

dsy.add("0_55",["Thị Xã Hương Thủy","Thành Phố Huế","Huyện Quảng Điền","Huyện Phú Vang","Huyện Phú Lộc","Huyện Phong Điền","Huyện Nam đông","Huyện Hương Trà","Huyện A Lưới"]);

dsy.add("0_56",["Thị Xã Gò Công","Thị xã Cai Lậy","Thành Phố Mỹ Tho","Huyện Tân Phước","Huyện Tân Phú Đông","Huyện Gò Công Tây","Huyện Gò Công Đông","Huyện Chợ Gạo","Huyện Châu Thành","Huyện Cai Lậy","Huyện Cái Bè"]);

dsy.add("0_57",["Thành Phố Trà Vinh","Huyện Trà Cú","Huyện Tiểu Cần","Huyện Duyên Hải","Huyện Châu Thành","Huyện Cầu Ngang","Huyện Cầu Kè","Huyện Càng Long"]);

dsy.add("0_58",["Thành phố Tuyên Quang","Huyện Yên Sơn","Huyện Sơn Dương","Huyện Nà Hang","Huyện Lâm Bình","Huyện Hàm Yên","Huyện Chiêm Hóa"]);

dsy.add("0_59",["Thành Phố Vĩnh Long","Huyện Vũng Liêm","Huyện Trà Ôn","Huyện Tam Bình","Huyện Mang Thít","Huyện Long Hồ","Huyện Bình Tân","Huyện Bình Minh"]);

dsy.add("0_60",["Thị Xã Phúc Yên","Thành Phố Vĩnh Yên","Huyên Yên Lạc","Huyện Vĩnh Tường","Huỵên Tam Đảo","Huyện Tam Dương","Huyện Sông Lô","Huyện Lập Thạch","Huyện Bình Xuyên"]);

dsy.add("0_61",["Thị Xã Nghĩa Lộ","Thành Phố Yên Bái","Huyện Yên Bình","Huyện Văn Yên","Huyện Văn Chấn","Huyện Trấn Yên","Huyện Trạm Tấu","Huyện Mù Căng Chải","Huyện Lục Yên"]);

dsy.add("0_62",["Quận Ngô Quyền","Quận Lê Chân","Quận Kiến An","Quận Hồng Bàng","Quận Hải An","Quận Đồ Sơn","Quận Dương Kinh","Huyện Vĩnh Bảo","Huyện Tiên Lãng","Huyện Thủy Nguyên","Huyện Kiến Thụy","Huyện Đảo Cát Hải","Huyện Đảo Bạch Long Vĩ","Huyện An Lão","Huyện An Dương"]);

dsy.add("0",["Hà Nội","TP.Hồ Chí Minh","An Giang","Bà Rịa -  Vũng Tàu","Bắc Giang","Bắc Kạn","Bạc Liêu","Bắc Ninh","Bến Tre","Bình Dương","Bình Định ","Bình Phước ","Bình Thuận","Cà Mau","Cần Thơ","Cao Bằng","Đà Nẵng","Đăk Lăk ","Đăk Nông","Điện Biên","Đồng Nai","Đồng Tháp","Gia Lai","Hà Giang","Hà Nam","Hà Tĩnh","Hải Dương","Hậu Giang","Hòa Bình","Hưng Yên","Khánh Hòa","Kiên Giang","Kon Tum","Lai Châu","Lâm Đồng","Lạng Sơn","Lào Cai","Long An ","Nam Định","Nghệ An","Ninh Bình","Ninh Thuận","Phú Thọ","Phú Yên","Quảng Bình","Quảng Nam ","Quảng Ngãi","Quảng Ninh","Quảng Trị","Sóc Trăng","Sơn La","Tây Ninh","Thái Bình","Thái Nguyên","Thanh Hóa","Thừa Thiên Huế","Tiền Giang","Trà Vinh","Tuyên Quang","Vĩnh Long","Vĩnh Phúc","Yên Bái","Hải Phòng"]);

var s=["s_province","s_city"];//三个select的name
var opt0 = ["Province", "City"];//初始值
function _init_area(){  //初始化函数
    for(i=0;i<s.length-1;i++){
        document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
    }
    change(0);
}