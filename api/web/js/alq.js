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

dsy.add("0",["ABU DHABI","DUBAI","SHARJAH","Ras Al Khaimah","UMM AL QUWAIN","Ajman","Fujairah"]);

dsy.add("0_0",["Abu Dhabi","Al Ain","Al Shahama","Al Tawelah","Al Shalelah","Al Shamkha","Bani Yas City","Al Wathba","Swehan","Khalifa Port","Port Zayed"]);

dsy.add("0_1",["Abu Hail","Al Awir First","Al Awir Second","Al Bada","Al Baraha","Al Barsha First","Al Barsha Second","Al Barsha South First","Al Barsha South Second","Al Barsha South Third","Al Barsha Third","Al Buteen","Al Dhagaya","Al Garhoud","Al Guoz Fourth","Al Hamriya Dubai","Al Hamriya Port","Al Hudaiba","Al Jaddaf","Al Jafiliya","Al Karama","Al Khabisi","Al Khwaneej First","Al Khwaneej Second","Al Kifaf","Al Mamzar","Al Manara","Al Merkad","Al Mina","Al Mizhar First","Al Mizhar Second","Al Muraqqabat","Al Murar","Al Sabkha","Al Muteena","Al Nahda First","Al Nahda Second","Al Quoz First","Al Quoz Industrial First","Al Quoz Industrial Fourth","Al Quoz Industrial Second","Al Quoz Industrial Third","Al Quoz Second","Al Quoz Third","Al Qusais First","Al Qusais Industrial Fifth","Al Qusais Industrial First","Al Qusais Industrial Fourth","Al Qusais Industrial Second","Al Qusais Industrial Third","Al Qusais Second","Al Qusais Third","Al Raffa","Al Ras","Al Rashidiya","Al Rigga","Al Safa First","Al Safa Second","Al Safouh First","Al Safouh Second","Al Satwa","Al Shindagha","Al Souq Al Kabeer","Al Twar First","Al Twar Second","Al Twar Third","Al Warqa'a Fifth","Al Warqa'a First","Al Warqa'a Fourth","Al Warqa'a Second","Al Warqa'a Third","Al Wasl","Al Waheda","Ayal Nasir","Aleyas","Bu Kadra","Dubai Investment park First","Dubai Investment Park Second","Emirates Hill First","Emirates Hill Second","Emirates Hill Third","Hatta","Hor Al Anz","Hor Al Anz East","Jebel Ali 1","Jebel Ali 2","Jebel Ali Industrial","Jebel Ali Palm","Jumeira First","Palm Jumeira","Jumeira Second","Jumeira Third","Al Mankhool","Marsa Dubai","Mirdif","Muhaisanah Fourth","Muhaisanah Second","Muhaisanah Third","Muhaisnah First","Al Mushrif","Nad Al Hammar","Nadd Al Shiba Fourth","Nadd Al Shiba Second","Nadd Al Shiba Third","Nad Shamma","Naif","Al Muteena First","Al Muteena Second","Al Nasr","Port Saeed","Arabian Ranches","Ras Al Khor","Ras Al Khor Industrial First","Ras Al Khor Industrial Second","Ras Al Khor Industrial Third","Rigga Al Buteen","Trade Centre 1","Trade Centre 2","Umm Al Sheif","Umm Hurair First","Umm Hurair Second","Umm Ramool","Umm Suqeim First","Umm Suqeim Second","Umm Suqeim Third","Wadi Alamardi","Warsan First","Warsan Second","Za'abeel First","Za'abeel Second"]);

dsy.add("0_2",["SHARJAH","Kalba","Dibba Al-Hisn","Khor Fakkan"]);

dsy.add("0_3",["Ras Al Khaimah","Al Jazirah Al Hamra","Ar-Rams","Khawr Khuwayr","Diqdaqah","Khatt","Masafi","Huwaylat"]);

dsy.add("0_4",["UMM AL QUWAIN"]);

dsy.add("0_5",["Ajman"]);

dsy.add("0_6",["Fujairah"]);

dsy.add("0",["ABU DHABI","DUBAI","SHARJAH","Ras Al Khaimah","UMM AL QUWAIN","Ajman","Fujairah"]);

  

var s=["s_province","s_city"];//三个select的name
var opt0 = ["Province", "City"];//初始值
function _init_area(){  //初始化函数
    for(i=0;i<s.length-1;i++){
        document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
    }
    change(0);
}