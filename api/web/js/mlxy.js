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

dsy.add("0",["Perlis","Kedah","Pulau Pinang","Kelantan","Terengganu","Pahang","Perak","Selangor","Wilayah Persekutuan Kuala Lumpur","Wilayah Persekutuan Putrajaya","Negeri Sembilan","Melaka","Johor","Sabah","rawak"]);

dsy.add("0_0",["Arau","Kaki Bukit","Kangar","Kuala Perlis","Padang Besar","Simpang Ampat"]);

dsy.add("0_1",["Alor Setar","Ayer Hitam","Baling","Bandar Baharu","Bedong","Bukit Kayu Hitam","Changloon","Gurun","Jeniang","Jitra","Karangan","Kepala Batas","Kodiang","Kota Kuala Muda","Kota Sarang Semut","Kuala Kedah","Kuala Ketil","Kuala Nerang","Kuala Pegang","Kulim","Kupang","Langgar","Langkawi","Lunas","Merbok","Padang Serai","Pendang","Pokok Sena","Serdang","Sik","Simpang Empat","Sungai Petani","Universiti Utara Malaysia","Yan"]);

dsy.add("0_2",["Ayer Itam","Balik Pulau","Batu Ferringhi","Batu Maung","Bayan Lepas","Bukit Mertajam","Butterworth","Gelugor","Georgetown","Jelutong","Kepala Batas","Kubang Semang","Nibong Tebal","Penaga","Penang Hill","Perai","Permatang Pauh","Simpang Ampat","Sungai Jawi","Tanjong Bungah","Tanjung Bungah","Tasek Gelugor","Tasek Gelugur","USM Pulau Pinang"]);

dsy.add("0_3",["Ayer Lanas","Bachok","Cherang Ruku","Dabong","Gua Musang","Jeli","Kem Desa Pahlawan","Ketereh","Kota Bharu","Kuala Balah","Kuala Krai","Machang","Melor","Pasir Mas","Pasir Puteh","Pulai Chondong","Rantau Panjang","Selising","Tanah Merah","Temangan","Tumpat","Wakaf Bharu"]);

dsy.add("0_4",["Ajil","Al Muktatfi Billah Shah","Ayer Puteh","Bukit Besi","Bukit Payong","Ceneh","Chalok","Cukai","Dungun","Jerteh","Kampung Raja","Kemasek","Kerteh","Ketengah Jaya","Kijal","Kuala Berang","Kuala Besut","Kuala Terengganu","Marang","Paka","Permaisuri","Sungai Tong"]);

dsy.add("0_5",["Balok","Bandar Bera","Bandar Pusat Jengka","Bandar Tun Abdul Razak","Benta","Bentong","Brinchang","Bukit Fraser","Bukit Goh","Cameron Highlands","Chenor","Chini","Damak","Dong","Gambang","Genting Highlands","Jerantut","Karak","Kemayan","Kuala Krau","Kuala Lipis","Kuala Rompin","Kuantan","Lanchang","Lurah Bilut","Maran","Mentakab","Muadzam Shah","Padang Tengku","Pekan","Raub","Ringlet","Sega","Sungai Koyan","Sungai Lembing","Temerloh","Triang"]);

dsy.add("0_6",["Ayer Tawar","Bagan Datoh","Bagan Serai","Bandar Seri Iskandar","Batu Gajah","Batu Kurau","Behrang Stesen","Bidor","Bota","Bruas","Changkat Jering","Changkat Keruing","Chemor","Chenderiang","Chenderong Balai","Chikus","Enggor","Gerik","Gopeng","Hutan Melintang","Intan","Ipoh","Jeram","Kampar","Kampung Gajah","Kampung Kepayang","Kamunting","Kinta","Kuala Kangsar","Kuala Kurau","Kuala Sepetang","Lambor Kanan","Langkap","Lenggong","Lumut","Malim Nawar","Manong","Matang","Padang Rengas","Pangkor","Pantai Remis","Parit","Parit Buntar","Pengkalan Hulu","Pusing","Rantau Panjang","Sauk","Selama","Selekoh","Seri Manjong","Seri Manjung","Simpang","Simpang Ampat Semanggol","Sitiawan","Slim River","Sungai Siput","Sungai Sumun","Sungkai","TLDM Lumut","Taiping","Tanjong Malim","Tanjong Piandang","Tanjong Rambutan","Tanjong Tualang","Tapah","Tapah Road","Teluk Intan","Temoh","Trolak","Trong","Tronoh","Ulu Bernam","Ulu Kinta"]);

dsy.add("0_7",["Ampang","Bandar Baru Bangi","Bandar Puncak Alam","Banting","Batang Berjuntai","Batang Kali","Batangkali","Batu Arang","Batu Caves","Beranang","Bukit Rotan","Cheras","Cyberjaya","Dengkil","Hulu Langat","Jenjarom","Jeram","KLIA","Kajang","Kapar","Kerling","Klang","Kuala Kubu Baru","Kuala Selangor","Pelabuhan Klang","Petaling Jaya","Puchong","Pulau Carey","Pulau Indah","Pulau Ketam","Rasa","Rawang","Sabak Bernam","Sekinchan","Semenyih","Sepang","Serdang","Serendah","Seri Kembangan","Shah Alam","Subang Jaya","Sungai Ayer Tawar","Sungai Besar","Sungai Buloh","Sungai Pelek","Tanjong Karang","Tanjong Sepat","Telok Panglima Garang"]);

dsy.add("0_8",["Kuala Lumpur","Sungai Besi"]);

dsy.add("0_9",["Putrajaya"]);

dsy.add("0_10",["Bahau","Bandar Enstek","Bandar Seri Jempol","Batu Kikir","Gemas","Gemencheh","Johol","Kota","Kuala Klawang","Kuala Pilah","Labu","Linggi","Mantin","Nilai","Port Dickson","Pusat Bandar Palong","Rantau","Rembau","Rompin","Seremban","Si Rusa","Simpang Durian","Simpang Pertang","Tampin","Tanjong Ipoh"]);


dsy.add("0_11",["Alor Gajah","Asahan","Ayer Keroh","Bemban","Durian Tunggal","Jasin","Kem Trendak","Kuala Sungai Baru","Lubok China","Masjid Tanah","Melaka","Merlimau","Selandar","Sungai Rambai","Sungai Udang","Tanjong Kling"]);


dsy.add("0_12",["Ayer Baloi","Ayer Hitam","Ayer Tawar 2","Ayer Tawar 3","Ayer Tawar 4","Ayer Tawar 5","Bandar Penawar","Bandar Tenggara","Batu Anam","Batu Pahat","Bekok","Benut","Bukit Gambir","Bukit Pasir","Chaah","Endau","Gelang Patah","Gerisek","Gugusan Taib Andak","Jementah","Johor Bahru","Kahang","Kluang","Kota Tinggi","Kukup","Kulai","Labis","Layang-Layang","Masai","Mersing","Muar","Nusajaya","Pagoh","Paloh","Panchor","Parit Jawa","Parit Raja","Parit Sulong","Pasir Gudang","Pekan Nenas","Pengerang","Pontian","Rengam","Rengit","Segamat","Semerah","Senai","Senggarang","Seri Gading","Seri Medan","Simpang Rengam","Skudai","Sungai Mati","Tangkak","Ulu Tiram","Yong Peng"]);

//dsy.add("0_13",[""]);"Wilayah Persekutuan Labuan"

dsy.add("0_13",["Beaufort","Beluran","Beverly","Bongawan","Inanam","Keningau","Kota Belud","Kota Kinabalu","Kota Kinabatangan","Kota Marudu","Kuala Penyu","Kudat","Kunak","Lahad Datu","Likas","Membakut","Menumbok","Nabawan","Pamol","Papar","Penampang","Putatan","Ranau","Sandakan","Semporna","Sipitang","Tambunan","Tamparuli","Tanjung Aru","Tawau","Telupid","Tenghilan","Tenom","Tuaran"]);

dsy.add("0_14",["Asajaya","Balingian","Baram","Bau","Bekenu","Belaga","Belawai","Betong","Bintangor","Bintulu","Dalat","Daro","Debak","Engkilili","Julau","Kabong","Kanowit","Kapit","Kota Samarahan","Kuching","Lawas","Limbang","Lingga","Long Lama","Lubok Antu","Lundu","Lutong","Matu","Miri","Mukah","Nanga Medamit","Niah","Pusa","Roban","Saratok","Sarikei","Sebauh","Sebuyau","Serian","Sibu","Siburan","Simunjan","Song","Spaoh","Sri Aman","Sundar","Tatau"]);

dsy.add("0",["Perlis","Kedah","Pulau Pinang","Kelantan","Terengganu","Pahang","Perak","Selangor","Wilayah Persekutuan Kuala Lumpur","Wilayah Persekutuan Putrajaya","Negeri Sembilan","Melaka","Johor","Sabah","rawak"]);



var s=["s_province","s_city"];//三个select的name
var opt0 = ["Province", "City"];//初始值
function _init_area(){  //初始化函数
    for(i=0;i<s.length-1;i++){
        document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
    }
    change(0);
}