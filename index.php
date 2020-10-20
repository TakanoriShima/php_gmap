<?php
    session_start();
    $keyword = "";
    if($_SESSION["keyword"] !== ""){
        $keyword = $_SESSION["keyword"];
    }else{
        $keyword = '';
    }
    if($keyword === 'undefined'){
        $keyword = "";
        session_destroy();
    }
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="shortcut icon" href="map.ico">
        <title>試験会場一覧</title>
        
        <style>
            h1{
                color: red;
                border: solid 2px red;
                border-radius: 30px;
                padding: 10px;
            }
            #target {
                border: 1px outset gray;
                width: 950px;
                height: 800px;
            }
            #sidebar {
                border: 1px solid #666;
                padding: 6px;
                background-color: white;
                font-family: Meriyo UI;
                font-size: 12px;
                overflow: auto;
                width: 237px;
                height: 786px;
            }
            .icon{
                width: 200px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-10 mt-3">
                    <h1 class="text-center">試験会場一覧</h1>
                </div>
                <div class="col-sm-2 mt-4">
                    <a href="new.php" class="btn btn-primary">新規試験情報登録</a>
                </div>
            </div>
            <div class="row mt-3">
                <form class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">試験名</label>
                        <div class="col-sm-10">
                            <input type="search" id="keyword" value="<?php print $keyword; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-2 col-sm-4">
                            <button type="submit" id="search" class="btn btn-primary form-control">検索</button>
                        </div>
                        <div class="offset-sm-2 col-sm-2">
                            <button type="reset" id="reset" class="btn btn-danger form-control">リセット</button>
                        </div>
                    </div>
                </form>
                <table class="col-sm-12 table">
                    <tr>
                        <td><div id="target"></div></td>
                        <td><div id="sidebar"></div></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- MarkerCluster -->
        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <!-- Google MAP API KEY -->
        <script src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key=AIzaSyALg70uaMcYjkzto9oPmiXyODIXCvpvAzg&callback=initMap" async defer></script>
        <!-- データファイルの読み込み -->
        <script src="https://code.jquery.com/jquery-2.1.1.js" integrity="sha256-FA/0OOqu3gRvHOuidXnRbcmAWVcJORhz+pv3TX2+U6w=" crossorigin="anonymous"></script>
        <script>
        
            var map;
            var markerD = [];
            var marker = [];
            var infoWindow = [];
            var openWindow;
            
            function initMap() {
                //マップ初期表示の位置設定
                var target = document.getElementById('target');
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // 現在地の緯度経度所得
                        lat = position.coords.latitude;
                        lng = position.coords.longitude;
                        
                        var centerp = {lat: lat, lng: lng};
                        
                        //マップ表示
                        map = new google.maps.Map(target, {
                            center: centerp,
                            zoom: 10,
                        });
                        
                        // マーカーの新規出力
                        new google.maps.Marker({
                            map: map,
                            position: centerp,
                            icon: {
                        		path: 'M -8,-8 8,8 M 8,-8 -8,8',     //座標（×）
                        		strokeColor: "#000000",              //線の色
                        		strokeWeight: 4.0                    //線の太さ
                        	}
                        });
                    }
                );  
            }
            
         
            $(function(){
                var fd = new FormData();
                var keyword = $("#keyword").val();
                fd.append('keyword', keyword);
                
                $.ajax({
                    type: "POST",
                    url: "get_exams.php",
                    dataType: "json",
                    data: fd,
                    processData: false,
                    contentType: false
                })
                .done(function( data, textStatus, jqXHR ) {
                    // 送信成功
                    markerD = data;
                    setMarker(markerD);
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    // 送信失敗
                });
            });
    
             $('#search').click(function(){
                var fd = new FormData();
                var keyword = $("#keyword").val();
                fd.append('keyword', keyword);
                
                $.ajax({
                    type: "POST",
                    url: "get_exams.php",
                    dataType: "json",
                    data: fd,
                    processData: false,
                    contentType: false
                })
                .done(function( data, textStatus, jqXHR ) {
                    // 送信成功
                    markerD = data;
                    setMarker(markerD);
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    // 送信失敗
                });
                 
             });
             
             $('#reset').click(function(){
                 
                var fd = new FormData();
                var keyword = "";
                
                fd.append('keyword', keyword);
                
                $.ajax({
                    type: "POST",
                    url: "get_exams.php",
                    dataType: "json",
                    data: fd,
                    processData: false,
                    contentType: false
                })
                .done(function( data, textStatus, jqXHR ) {
                    // 送信成功
                    markerD = data;
                    setMarker(markerD);
                    $("#keyword").val("");
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    // 送信失敗
                });
                 
             });


            function setMarker(markerData) {
            
                //マーカー生成
                var sidebar_html = "";
                var icon;
                
                for (var i = 0; i < markerData.length; i++) {
                    console.log(i + "番目：" + markerData[i]['address'] + "/" + markerData[i]['lat'] + "/" + markerData[i]['lng']);
            
                    var latNum = parseFloat(markerData[i]['lat']);
                    var lngNum = parseFloat(markerData[i]['lng']);
                    // マーカー位置セット
                    var markerLatLng = new google.maps.LatLng({
                        lat: latNum,
                        lng: lngNum
                    });
                    
                    
                    // マーカーのセット
                    marker[i] = new google.maps.Marker({
                        position: markerLatLng,          // マーカーを立てる位置を指定
                        map: map,                        // マーカーを立てる地図を指定
                        // icon: {
                        //     url: 'spot.png',
                        //     size : new google.maps.Size(19, 25)
                        // }
                    });
                    
                    // 吹き出しの追加
                    infoWindow[i] = new google.maps.InfoWindow({
                        content: markerData[i]['year'] + '/' + markerData[i]['month'] + '/' + markerData[i]['day'] + ':　'+ markerData[i]['exam_name'] + '<br><br>' +'<a href="' + markerData[i]['url'] + '" target="_blank">' + markerData[i]['address'] + '</a>'
                    });
                    // サイドバー
                    
                    sidebar_html +=  '● '+ '<a href="javascript:myclick(' + i + ')">' + markerData[i]['exam_name'] + '<\/a><br />';
                    // マーカーにクリックイベントを追加
                    markerEvent(i);
                }
                
               // Marker clusterの追加
                var markerCluster = new MarkerClusterer(
                    map,
                    marker,
                    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'}
                );
                
                // サイドバー
                document.getElementById("sidebar").innerHTML = sidebar_html;
            }
    
    
            function markerEvent(i) {
                marker[i].addListener('click', function() {
                    myclick(i);
                });
            }
    
            function myclick(i) {
                if(openWindow){
                    openWindow.close();
                }
                infoWindow[i].open(map, marker[i]);
                openWindow = infoWindow[i];
            }
    
        </script>
    
    </body>
</html>
