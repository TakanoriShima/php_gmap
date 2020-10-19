<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>試験会場一覧</title>
    <style>
      #header {
        font-family: Meriyo UI;
        font-size: 14px;
        color: white;
        font-weight: bold;
        background-color: darkblue;
        padding: 3px;
        width: 1200px;
        border: 1px outset gray;
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
    <div id="header">情報処理技術者試験一覧</div>
    <table>
      <tr>
        <td><div id="target"></div></td>
        <td><div id="sidebar"></div></td>
      </tr>
    </table>

    <!-- MarkerCluster -->
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>

    <!-- Google MAP API KEY -->
    <script src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key=AIzaSyALg70uaMcYjkzto9oPmiXyODIXCvpvAzg&callback=initMap" async defer></script>

    <!-- データファイルの読み込み -->
    <script src="https://code.jquery.com/jquery-2.1.1.js" integrity="sha256-FA/0OOqu3gRvHOuidXnRbcmAWVcJORhz+pv3TX2+U6w=" crossorigin="anonymous"></script>

    <script>

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
                    });
                }
            );  
        }
        
        var markerD = [];
        
        $(function(){
            $.ajax({
                type: "POST",
                url: "all_exams.php",
                dataType: "json",
                success: function(data){
                    markerD = data;
                    setMarker(markerD);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert('Error : ' + errorThrown);
                }
            });
        });


      var marker = [];
      var infoWindow = [];

      function setMarker(markerData) {

        //マーカー生成
        var sidebar_html = "";
        var icon;

        for (var i = 0; i < markerData.length; i++) {

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': markerData[i]['address']}, function(results, status){
            if(status == google.maps.GeocoderStatus.OK) {
                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();
                 icon = new google.maps.MarkerImage('./list1.png');
          // マーカーのセット
          marker[i] = new google.maps.Marker({
            position:  new google.maps.LatLng({lat: lat, lng: lng}),         // マーカーを立てる位置を指定
            map: map,                        // マーカーを立てる地図を指定
             icon: {
                url: './list1.png',
                size : new google.maps.Size(19, 25)
             }
            
          });
            }
        });
    

          

          // マーカー位置セット
          var markerLatLng = new google.maps.LatLng({
            lat: lat,
            lng: lng
          });
          
          
          // マーカーのセット
          marker[i] = new google.maps.Marker({
            position: markerLatLng,          // マーカーを立てる位置を指定
            map: map,                        // マーカーを立てる地図を指定
             icon: {
                url: './list1.png',
                size : new google.maps.Size(19, 25)
             }
            
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

        var openWindow;
        
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
