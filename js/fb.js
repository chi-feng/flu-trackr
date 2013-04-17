
var user = [];
var permissions = ['user_status', 'publish_checkins', 'user_likes'];

// Pre-fetch data, mainly used for requests and feed publish dialog
var nonAppFriendIDs = [];
var appFriendIDs = [];
var friendIDs = [];
var friendsInfo = [];
var appFriendsInfo = [];

// UI stuff

var first_time = true;

// Initialize the Facebook SDK
var gAppID = '528792943826588';
window.fbAsyncInit = function() {
  FB.init({ appId: gAppID, status: true, cookie: true, xfbml: true, frictionlessRequests: true, useCachedDialogs: true, oauth: true });
  FB.getLoginStatus(handleStatusChange);
  authUser();
  updateAuthElements();
};

// Load the SDK Asynchronously
$(document).ready(function() {
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));
});

// show a loading screen when launched, until we get the user's session back
setAction("Loading Flu-Trackr", true);

var first_time = true;

// swaps the pages out when the user taps on a choice
function openPage(pageName, ignoreHistoryPush) {
  
  window.scrollTo(0,1);
  var els = document.getElementsByClassName('page');
  if (!first_time) { $('.page').fadeOut(200); }
  first_time = false;
  var page = document.getElementById('page-' + pageName);
  
  if (pageName =='root' || pageName == 'Go-Viral') {
    update_score();
  }
  
  if (pageName == 'Scoreboard') {
    $('#scoreboard').hide();
  }
  
  if (pageName == 'Flu-Trends') {
    setAction("Loading Surveillance Data", true);
  }

  $('#page-' + pageName).delay(200).fadeIn(300, function() {

    if (pageName == 'Vaccine-Locator') {
      initialize_vf();
    }
    
    if (pageName == 'Scoreboard') {
      initialize_scoreboard();
    }
    
    if (pageName == 'Flu-Trends') {
      initialize_surveillance();
    }

  });
  
  var image = '<img src="/assets/tinylogo.png" height="32" />';
  
  title = (pageName == 'root') ? image : pageName.replace(/-/g, ' ');

  document.getElementById('title').innerHTML = title;
  if (ignoreHistoryPush != true) {
    window.history.pushState({page: pageName}, '', document.location.origin + document.location.pathname + "#" + pageName);
  }
  document.getElementById('back').style.display = (pageName == 'root') ? 'none' : 'block';
}

window.onpopstate = function(e) {
  if (e.state != null) {
    console.log(e.state);
    openPage(e.state.page);
  } else {
    openPage('root', true);
  }
}

openPage('root', true);

// Shows a modal dialog when fetching data from Facebook
function setAction(msg, hideBackground) {
  document.getElementById('action').style.display = 'block';
  if (hideBackground) {
    document.getElementById('action').style.opacity = '100';
  } else {
    document.getElementById('action').style.opacity = '.9';
  }
  document.getElementById('msg').innerHTML = msg;
  window.scrollTo(0, 1);
}

// Clears the modal dialog
function clearAction() {
  document.getElementById('msg').innerHTML = '';
  $('#action').fadeOut(200);
}

// Automatically scroll away the address bar
addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);

function hideURLbar() {
  window.scrollTo(0,1);
}

function hideButton(button) {
  button.style.display = 'none';
}

// Detect when Facebook tells us that the user's session has been returned
function authUser() {
  FB.Event.subscribe('auth.statusChange', handleStatusChange);
}

// Handle status changes
function handleStatusChange(session) {
    if (session.authResponse) {
        document.body.className = 'connected';
        // Fetch user's id, name, and picture
        FB.api('/me', { fields: 'name, picture' },
        function(response) {
          if (!response.error) {
            user = response;
            logged_in = true;
            $.post("/ajax.php", 
              { action: 'user_checkin', id: user.id, name: user.name },
              function(data) { }, 'json'
             );
            // $('#title').html(user.name);
            $('#user-name').html(user.name);
            $('#user-picture').attr('src', user.picture.data.url);
          }
          clearAction();
        });
    }
    else  {
      document.body.className = 'not_connected';
      clearAction();
    }
}

function promptLogin() {
  FB.login(null, {scope: 'email'});
  openPage('root');
}

function promptPermission(permission) {
  FB.login(function(response) {
    if (response.authResponse) {
      checkUserPermissions(permission)
    }
  }, {scope: permission});
}

function uninstallApp() {
  FB.api('/me/permissions', 'DELETE',
    function(response) {
      window.location.reload();
  });
}

function logout() {
  FB.logout(function(response) {
    window.location.reload();
  });
}

//Detect when Facebook tells us that the user's session has been returned
function updateAuthElements() {
  FB.Event.subscribe('auth.statusChange', function(session) {
    if (session.authResponse) { 
      preFetchData();
    }
  });
}


function preFetchData() {
  // First, get friends that are using the app
  FB.api({method: 'friends.getAppUsers'}, function(appFriendResponse) {
    appFriendIDs = appFriendResponse;
    // Now fetch all of the user's friends so that we can determine who hasn't used the app yet
    FB.api('/me/friends', { fields: 'id, name, picture' }, function(friendResponse) {
      friends = friendResponse.data;
      var appFriendsData = { appFriends: appFriendIDs };
      $.post("/ajax.php", 
        { action: 'user_checkin_friends', id: user.id, num_friends: friends.length, app_friends: appFriendIDs },
        function(data) { $('#user-score').html(data.score); }, 'json');
      for (var k = 0; k < friends.length; k++) {
        var friend = friends[k];
        var index = 1;
        friendIDs[k] = friend.id;
        friendsInfo[k] = friend;  
        for (var i = 0; i < appFriendIDs.length; i++) {
          if (appFriendIDs[i] == friend.id) { index = -1; appFriendsInfo.push(friend); }
        }
        if (index == 1) { nonAppFriendIDs.push(friend.id); }
      }
    });
  });
}


// Send an invite to friends that haven't logged into the app yet
function sendRequestInvite() {
  FB.ui({
    method: 'apprequests',
    suggestions: nonAppFriendIDs,
    message: 'Get vaccinated and become a Flu Hero!',
  }, function(response) {
    console.log(response);
    console.log('sendRequestInvite UI response: ', response);
 if(response instanceof Object == true){
        $.post("/ajax.php",
          { action: 'user_share', id: user.id },
          function(data) {
            if (typeof data.message != 'undefined') {
              $.ambiance({message: data.message});
                update_score();
           }
        },
        'json'
       );     
}
  });
}

// Publish a story to the user's own wall
function publishStory() {
  FB.ui({
    method: 'feed',
    name: 'I\'m using the Flu-Trackr web app',
    caption: 'Flu-Trackr',
    description: 'Get vaccinated and become a flu hero!',
    link: 'http://flu-trackr.com/',
    picture: 'http://flu-trackr.com/img/facebook_icon_large.png',
    actions: [{ name: 'Get Started', link: 'http://flu-trackr.com/' }],
  }, 
  function(response) {
    console.log('publishStory UI response: ', response);
    if(response instanceof Object == true){
        $.post("/ajax.php", 
          { action: 'user_share', id: user.id },
          function(data) {
            if (typeof data.message != 'undefined') {
              $.ambiance({message: data.message});
		update_score();
	   }
        },
        'json'
       );               



    }

  });
}







