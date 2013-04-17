
var logged_in = false;

function update_score() {
  if (logged_in) {
    $.post("/ajax.php", 
      { action: 'getscore', id: user.id },
      function(data) {
        
        user.basescore = parseInt(data.basescore);
        user.friendscore = parseInt(data.friendscore);
        user.sharescore = parseInt(data.sharescore);
        user.vacscore = parseInt(data.vacscore);

        user.score = user.basescore + user.friendscore + user.sharescore + user.vacscore;

        $('#user-basescore').html(user.basescore/10);
        $('#user-friendscore').html(user.friendscore/100);
        $('#user-sharescore').html(user.sharescore/20);
        $('#user-vacscore').html(user.vacscore/100);
        $('#user-score-total').html(user.score)
        $('#user-score').html(user.score)

        $('#user-basescore-value').html(user.basescore);
        $('#user-friendscore-value').html(user.friendscore);
        $('#user-sharescore-value').html(user.sharescore);
        $('#user-vacscore-value').html(user.vacscore);
        $('#user-score-total-value').html(user.score)
        $('#user-score-value').html(user.score)         

      },
    'json'
    );
  }
}

function update_player_score(player_id, player_name) {
  openPage('Player-Score');
  $.post("/ajax.php", 
    { action: 'getscore', id: player_id },
    function(data) {
      var player = Object();
      player.basescore = parseInt(data.basescore);
      player.friendscore = parseInt(data.friendscore);
      player.sharescore = parseInt(data.sharescore);
      player.vacscore = parseInt(data.vacscore);

      player.score = player.basescore + player.friendscore + player.sharescore + player.vacscore;

      $('#player-basescore').html(player.basescore/10);
      $('#player-friendscore').html(player.friendscore/100);
      $('#player-sharescore').html(player.sharescore/20);
      $('#player-vacscore').html(player.vacscore/100);
      $('#player-score-total').html(player.score)
      $('#player-score').html(player.score)

      $('#player-basescore-value').html(player.basescore);
      $('#player-friendscore-value').html(player.friendscore);
      $('#player-sharescore-value').html(player.sharescore);
      $('#player-vacscore-value').html(player.vacscore);
      $('#player-score-total-value').html(player.score)
      $('#player-score-value').html(player.score)         

      $('#player-name').html(player_name);
    },
  'json'
  );
}

var initialLocation = new google.maps.LatLng(42.360, -71.103); // boston
var browserSupportFlag = new Boolean();
var vf_map;
var geolocate_complete = false;
var geolocate_position;

function geocode(address) {
  var url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&sensor=false';
  var position = [0, 0];
  $.ajax({
     type: 'GET',
      url: url,
      async: false,
      dataType: 'json',
      success: function(data) {
        position[0] = data.results[0].geometry.location.lat;
        position[1] = data.results[0].geometry.location.lng;
        console.log(position);
        return position;
      },
      error: function(e) {
         console.log(e.message);
      }
  });
  return position;
  /*
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { address: address },
    function(results_array, status) { 
      console.log(results_array);
      var lat = results_array[0].geometry.location.jb;
      var lng = results_array[0].geometry.location.kb;
      return {lat: lat, lng: lng};
  });
  return {lat: 0, lng: 0};
  */
}

function vf_search() {
  var position = geocode($('#vf-address').val());
  console.log(position);
  vf_update(position[0], position[1]);
}

function geolocate(callback) {
  if (geolocate_complete) {
    callback(geolocate_position);
  } else if(navigator.geolocation) {
    browserSupportFlag = true;
    navigator.geolocation.getCurrentPosition(function(position) {
      geolocate_complete = true;
      geolocate_position = position;
      callback(position);
    }, function() {
      handleNoGeolocation(browserSupportFlag);
    });
  } else {
    browserSupportFlag = false;
    handleNoGeolocation(browserSupportFlag);
  }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag == true) {
    alert("Geolocation service failed.");
  } else {
    alert("Warning: Your browser doesn't support geolocation.");
  }
  vf_map.setCenter(initialLocation);
}

function vf_update(lat, lng) {
  initialLocation = new google.maps.LatLng(lat,lng);
  vf_map.setCenter(initialLocation);
  
  var request = $.ajax({
    url: "/vf.php",
    type: "POST",
    data: {id : user.id, lat:initialLocation.lat(), lng:initialLocation.lng()},
    dataType: "json",
    success: function(data) {
      $.ambiance({message: 'Retrieved <strong>' + data.markers.length + '</strong> markers from server'});
      vf_map.setZoom(parseInt(data.zoom));
      var items = [];

      var you_icon = new google.maps.MarkerImage('img/you.png', new google.maps.Size(24, 20), new google.maps.Point(0,0), new google.maps.Point(0, 20));
      var mapmarker = new google.maps.Marker({
	position: new google.maps.LatLng(initialLocation.lat(), initialLocation.lng()),
	map: vf_map,
	icon: you_icon,
	title: 'You are here' });

      $.each(data.markers, function(key, marker) {
        var mapmarker = new google.maps.Marker({
          position: new google.maps.LatLng(marker.lat, marker.lon),
          map: vf_map
        });
      mapmarker.setAnimation(google.maps.Animation.DROP);	

        var iw = new google.maps.InfoWindow({
          content: '<strong>' + marker.provider + '</strong><br />' + marker.html_marker
        });


var R = 3959; // miles
var d = Math.acos(Math.sin(marker.lat)*Math.sin(initialLocation.lat()) + 
                  Math.cos(marker.lat)*Math.cos(initialLocation.lat()) *
                  Math.cos(marker.lon-initialLocation.lng())) * R;


        google.maps.event.addListener(mapmarker, "click", function (e) { iw.open(vf_map, mapmarker); });
        items.push('<li><strong>' + marker.provider + '</strong><br />' + marker.html_side + '</li>');
      });
      $('.vf-list').html(items.join(''));
    },
    error:function(jqXHR, textStatus) {
      $.ambiance({message: 'Failed to retrieve markers from server'});
      alert( "Request failed: " + textStatus );
    }

  });
}

function initialize_vf() {
  
  var zoom = 5;
  if (browserSupportFlag) {
    zoom = 10;
  }
  
  var mapOptions = {
    center: new google.maps.LatLng(42.360, -71.103),
    zoom: zoom,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  
  vf_map = new google.maps.Map(document.getElementById("vf-canvas"), mapOptions);
  var mapLoaded = false;
  google.maps.event.addListener(vf_map, 'idle', function(){
    if (!mapLoaded) {
      mapLoaded = true;   
      $('#vf-canvas').animate({
        opacity: 1.0
      }, 500, function() {
        // Animation complete.
      });
    }
  });
  
  // Try W3C Geolocation (Preferred)
  geolocate(function(position) {
    vf_update(position.coords.latitude, position.coords.longitude);
  });

}




function player_to_html(player) {
  var location;
  if (player.state == 'United States' || player.state == '') {
    location = '';
  } else {
    location = player.city + ', ' + player.state;
  }
  return '<div class="player clearfix" onclick="update_player_score(\''+player.id+'\',\''+(player.name)+'\');">' 
    + '<img src="https://graph.facebook.com/' + player.id + '/picture" />'
    + '<span class="player-name">' + player.name 
    + '<span class="player-location">' + location +'</span></span>'
    + '<span class="player-score">' + player.score + '</span>'
    + '</div>';
}

function initialize_scoreboard() {

  $('#scoreboard,#global-leaderboard,#friends-leaderboard').hide();

  $.post("/ajax.php", 
    { action: 'scoreboard', id: user.id },
    function(data) {

      if (typeof data.message != 'undefined') {
          $.ambiance({message: data.message});
      }
      
      var global_list = [
        '<div class="player-picture-label"></div>',
        '<div class="player-name-label"></div>',
        '<div class="player-score-label"></div>' ];
      $.each(data.global, function(index, player) {
        global_list.push(player_to_html(player));
      });

      var friends_list = [
        '<div class="player-picture-label"></div>',
        '<div class="player-name-label"></div>',
        '<div class="player-score-label"></div>' ];
      $.each(data.friends, function(index, player) {
        friends_list.push(player_to_html(player));
      });

      $('#global-leaderboard').html(global_list.join(''));
      
      if (logged_in) {
        $('#friends-leaderboard').html(friends_list.join(''));
      } else {
        $('#friends-leaderboard').html('<div class="button" onclick="promptLogin();">Log in to compare to friends</div>');
      }      
      
      $('#loading-scores').hide();
      $('#scoreboard').fadeIn(300);
      $('#global-leaderboard').slideDown(300);
      $('#friends-leaderboard').slideDown(300);

    },
    'json'
   );
}


function rv_submit() {
  var date = $('#rv-year').val() + '-' + $('#rv-month').val() + '-' + $('#rv-day').val();
  var location = $('#rv-location').val();
  $.post("/ajax.php", 
    { action: 'vaccine_checkin', id: user.id, date: date, location: location },
    function(data) {
      if (typeof data.message != 'undefined') {
        $.ambiance({message: data.message});
      }
      if (data.valid == 1) {
        $('#rv-form').html('<div id="rv-success">Vaccine record accepted!<br /></div>');
      }
      $('#user-score').html(data.score);
  },
  'json'
 );
}




function initialize_surveillance() {
  geolocate(function(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    var latlng = lat + ',' + lng;
    var url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + latlng + '&sensor=false';
    $.getJSON(url, null, function(data) {

      var city = 'none';
      var state = 'United States';
      var state_short = 'US';
      
      for (var i = 0; i < data.results.length-1; i++) {
        for (var j = 0; j < data.results[i].address_components.length; j++) {
          var address_component = data.results[i].address_components[j];
          if (address_component.types[0] == 'locality') {
            city = address_component.long_name;
          }
          if (address_component.types[0] == 'administrative_area_level_1') {
            state = address_component.long_name;
            state_short = address_component.short_name;
          }
        }
      }
      
      if (logged_in) {
        $.post("/ajax.php", 
          { action: 'user_location', id: user.id, city: city, state: state},
          function(data) {
            if (typeof data.message != 'undefined') {
                $.ambiance({message: data.message});
            }
          },
          'json'
        );
      }
      
      $.post("/ajax.php", 
        { action: 'trend', state: state.replace(/ /g,"-") },
        function(data) {

          if (typeof data.message != 'undefined') {
              $.ambiance({message: data.message});
          }
          
          $('#trend-plot-canvas').html('');
          
          var trends = data.trends;
          var national = data.national;
          var trends_max = 0;
          var national_max = 1;
          
          // format into not strings
          for (var i = 0; i < trends.length; i++) {
            trends[i][0] = parseInt(trends[i][0]);
            national[i][0] = parseInt(trends[i][0]);
            trends[i][1] = parseFloat(trends[i][1]);
            national[i][1] = parseFloat(national[i][1]);
          }
          
          // rescale seconds into milliseconds
          for (var i = 0; i < trends.length; i++) {
            trends[i][0] *= 1000;
            national[i][0] *= 1000;
          }
          
          var xmin = data.start*1000;
          var xmax = data.end*1000;
          
          $.plot($("#trend-plot-canvas"), 
            [ { label: state, data: trends, color: "#369" },
              { label: 'US Average', data: national, color: "#963" } ], 
            { grid: { backgroundColor: { colors: ["#fff", "#ddd"] } }, 
              yaxis: { min:0, max: 1}, 
              xaxis: { mode: "time", timeformat: "%b", min: xmin, max: xmax, tickSize: [1, "month"]  } } 
          ); 
          $('#trend-plot-title').html('Flu trend data for ' + state);
          
          
        },
        'json'
      );
      
    });
  });
}



      function fa_phone_submit() {
        var phone = $('#fa-phone').val();
        $.post("/ajax.php", 
          { action: 'phone_alert', id: user.id, phone: phone },
          function(data) {
            if (typeof data.message != 'undefined') {
              $.ambiance({message: data.message});
            }
            $('#user-score').html(data.score);
        },
        'json'
       );
      }
      function fa_email_submit() {
        var email = $('#fa-email').val();
        $.post("/ajax.php", 
          { action: 'email_alert', id: user.id, email: email },
          function(data) {
            if (typeof data.message != 'undefined') {
              $.ambiance({message: data.message});
            }
            $('#user-score').html(data.score);
        },
        'json'
       );
      }
