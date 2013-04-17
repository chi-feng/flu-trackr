<?php
session_start();
?>
<html xmlns:og="http://ogp.me/ns" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en">
<head>
  <title>Flu-Trackr</title>
  <!-- See https://developers.facebook.com/docs/opengraph/ -->
  <meta property="og:title" content="Flu-Trackr" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://flu-tracker.com" />
  <meta property="og:site_name" content="Flu-Trackr" />
  <meta property="og:image" content="/img/facebook_icon_large.png"/>
  <meta property="fb:admins" content="20901205" />
  <meta property="fb:app_id" content="528792943826588" />
  
  <!-- See http://developer.apple.com/library/safari/#documentation/appleapplications/reference/SafariHTMLRef/Articles/MetaTags.html -->
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  
  <!-- See https://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
  <link rel="apple-touch-icon" href="/img/iphone_icon.png" />
  <link rel="apple-touch-startup-image" href="/img/iphone_splash.png" />
  <link rel="apple-touch-icon-precomposed" href="/img/iphone_icon.png" />
  
  <!-- See http://davidbcalhoun.com/2010/viewport-metatag for information on the viewport tag. -->
  <meta name="viewport" content="initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />

  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="css/font-awesome.css"  rel="stylesheet" type="text/css" />
  <link href="css/jquery.ambiance.css"  rel="stylesheet" type="text/css" />


<script type="text/javascript" charset="utf-8">
  window.twttr = (function (d,s,id) {
    var t, js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
    js.src="//platform.twitter.com/widgets.js"; 
    fjs.parentNode.insertBefore(js, fjs);
    return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
  }(document, "script", "twitter-wjs"));
</script>
  
  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  
</head>

<body onload="window.top.scrollTo(0,1);" style="min-height: 120%">
 
  <div id="mobile-warning" onClick="$('#mobile-warning').fadeOut(500);">
    <img src="assets/biglogo.png" width="500" height="150" />
    <p>This app was optimized to be viewed on mobile devices, e.g. iOS/Android</p>
    <p>This message is only shown if you view this site on a desktop browser.</p>
    <p>See the app in action on a smartphone <a style="color:#6bf" target="_blank" href="http://www.youtube.com/watch?v=HvpqaBb9LJc">[youtube]</a></p>
    <div id="mobile-okay">Click to continue</div>
  </div>
   
  <div id="fb-root"></div>
  <div id="header">
    <div id="back-link" onclick="openPage('root')"><div id="back">Back</div></div>
    <div id="title-wrap">
      <div id="title"><img src="assets/tinylogo.png" height="36" /></div>
    </div>
    <div id="right-block">
      <div class="show_when_not_connected">
        <div id="login_button" class="login-button" onclick="promptLogin()" /><i class="icon-facebook-sign">&nbsp;</i> Login</div>
      </div>
      <div class="show_when_connected">
        <!--<div id="login_button" class="login-button logout-button" onclick="logout()" /><i class="icon-signout">&nbsp;</i></div>-->
        <!-- <div id="login_button" class="button" onclick="uninstallApp()" />Uninstall app</div> -->
      </div>
    </div>
  </div>
  
  <div id="action">
    <div id="loading-icon"></div>
    <div id="msg"></div>
  </div>
  
  <!-- Page: Homepage -->
  <div id="page-root" class="page" style="display: none">
    
    <div class="show_when_connected">
      <div id="welcome">
        <img id="user-picture" />
        <div id="user-name"></div>
        <div id="welcome-score">Flu-Hero Score: <span id="user-score"></span></div>
        <div id="hiscores" onclick="openPage('Scoreboard');"><i class="icon-trophy golden"></i></div>
      </div>
    </div>

    
    <ul class="menu clearfix"> 

      <li class="menu-item" onclick="openPage('Flu-Trends');">
        <i class="icon-bar-chart">&nbsp;</i>
        <div class="label">
          <span class="top">Flu Heat Map</span>
          <span class="bottom">Track and explore local and national flu trends.</span>
        </div>
      </li>
      
      <li class="menu-item" onclick="openPage('Vaccine-Locator');">
        <i class="icon-search">&nbsp;</i>
        <div class="label">
          <span class="top">Vaccine Locator</span>
          <span class="bottom">Find vaccine providers close to your current location.</span>
        </div>
      </li>

      <li class="menu-item" onclick="openPage('Go-Viral');">
        <i class="icon-group">&nbsp;</i>
        <div class="label">
          <span class="top">Go Viral with Flu-Hero</span>
          <span class="bottom">Become a Flu-Hero by helping others get vaccinated.</span>
        </div>
      </li>

      <li class="menu-item" onclick="openPage('Report-Vaccination');">
        <i class="icon-plus-sign">&nbsp;</i>
        <div class="label">
          <span class="top">Report a Vaccination</span>
          <span class="bottom">Increase your score by reporting recent vaccinations!</span>
        </div>
      </li>
      
       <li class="menu-item" onclick="openPage('Flu-Alerts');">
         <i class="icon-warning-sign">&nbsp;</i>
         <div class="label">
           <span class="top">Flu Alerts</span>
           <span class="bottom">Sign up for vaccination and outbreak information via text or email.</span>
         </div>
       </li>

       <li class="menu-item" onclick="openPage('About-Us');">
         <i class="icon-question-sign">&nbsp;</i>
         <div class="label">
           <span class="top">What is Flu-Trackr?</span>
           <span class="bottom">How our app promotes flu vaccinations and reduces infections.</span>
         </div>
       </li>
       
   </ul>

   <div class="show_when_not_connected">
     <ul class="menu clearfix" style="box-shadow:none;"> 
        <li class="menu-item" onclick="openPage('Scoreboard');">
          <i class="icon-trophy golden">&nbsp;</i>
          <div class="label">
            <span class="top" style="margin-top: 10px">View Leaderboard</span>
            <span class="bottom">Check out the top scoring Flu Heros</span>
          </div>
        </li>
      </ul>
   </div>   
   
   <!--<div id="like-button"><fb:like url="http://flu-trackr.com/" width="275"></fb:like></div>-->
  </div>
  
  <!-- Page: Surveillance -->
  <div id="page-Flu-Trends" class="page" style="display: none">
  <div id="flu-trends">
  <div id="trend-plot">
    <div id="trend-plot-title">Loading flu trend data</div>
    <div id="trend-plot-canvas">
      Waiting for reverse-geolocation and flu trend data.
    </div>  
  </div>
  <h4>National Flu Trends</h4>
  <p>Click on a state to view its trend</p>
  <div id="usmap" style="width:320px;height:190px"></div>
  <img src="assets/plt.png" width="320" />
  <h4>How the trends are computed</h4>
  <p>Flu-Trackr uses Google Flu Trends data that is calibrated using the weekly CDC regional data and presented using the same risk categories as used by the U.S. ILINet reports.</p>
  <h4>Powered by</h4>
  <img src="/assets/google.png" alt="google flue trends" height="25" style="margin:0 15px;"  />
  <img src="/assets/cdc.png" alt="cdc"  height="25" style="margin:0 15px;" />
  </div>
</div>  

  <!-- Page: Leaderboard --> 
  <div id="page-Scoreboard" class="page"  style="display: none"> 
    <div id="loading-scores">Loading Scores</div>
    <div id="scoreboard" style="display:none">
      <h3>Global Leaderboard</h3>
      <div id="global-leaderboard" class="clearfix"></div>
      <h3>Friends Leaderboard</h3>
      <div id="friends-leaderboard" class="clearfix"></div>
      <p>&nbsp;</p>
    </div>
  </div>
  
  <!-- Page: Vaccine Locator -->
  <div id="page-Vaccine-Locator" class="page" style="display: none">
    <div id="loading-vf-map" style="background:#fafafa; width: 100%; max-width: 320px; line-height: 250px; text-align: center; position: absolute; top:44px;">Loading Map</div>
    <div id="vf-canvas" style="min-width: 320px; width: 100%; height: 250px; opacity: 0;"></div>
    <div id="vf-searchbox" class="clearfix">
      <form onsubmit="vf_search(); return false;">
        <label>Search near address or zip code:</label>
        <input type="text" id="vf-address" value="My Location" /> 
        <input type="button" id="vf-address-search" onClick="vf_search();" value="Go" />
        <script type="text/javascript">
          $('#vf-address').focus(function() {
            if ($('#vf-address').val() == 'My Location') {
              $('#vf-address').val('');
            }
          });
          $('#vf-address').blur(function() {
            if ($('#vf-address').val() == '') {
              $('#vf-address').val('My Location');
            }
          });
        </script>
      </form>
    </div>
    <ul class="vf-list"></ul>
    <h4>Powered By</h4>
    <img src="/assets/hm.png" alt="healthmap.org"  height="25" style="margin:0 15px;" />
  </div>
  
  <!-- Page: Requests -->
  <div id="page-Report-Vaccination" class="page" style="display: none"> 
    <p>&nbsp;</p>
    <p>
      Report a recent vaccination to earn points for yourself and your friends!<br />
      <strong>Note:</strong> You can only report one vaccine every 6 months.
    </p>
    <form id="rv-form">
      <label>Date:</label><br />
      <select id="rv-month">
        <?php $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
          $current_month = date('n') - 1;
          for ($i = 0; $i < 12; $i++) {
            $selected = ($i == $current_month) ? ' selected' : '';
            echo '<option value="'.($i+1).'"'.$selected.'>'.$months[$i].'</option>';
          } ?>
      </select>
      <select id="rv-day">
        <?php for ($i = 1; $i < 32; $i++) {
            $selected = ($i == date('j')) ? ' selected' : '';
            echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
          } ?>
      </select>
      <select id="rv-year">
        <?php echo '<option value="'.(date('Y')-1).'">'.(date('Y')-1).'</option>';
          echo '<option value="'.date('Y').'" selected>'.date('Y').'</option>'; ?>
      </select><br />
      <label>Location:</label>
      <input type="text" id="rv-location" /><br />
      <input type="button" id="rv-button" value="Submit" onclick="rv_submit();">
    </form>

  </div>
  
  <!-- Page: Player Score -->
  <div id="page-Player-Score" class="page" style="display: none">
    <h4>Score breakdown for:
      <span id="player-name"></span>
    </h4>
    <table class="worksheet">
      <tr>
        <td><strong>Base score</strong><span class="explanation">+10 points for every friend that uses this app</span></td>
        <td class="right"><span id="player-basescore"></span></td>
        <td>&times;</td><td class="right">10</td><td>=</td>
        <td class="right"><span id="player-basescore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Friend vaccinations</strong><span class="explanation">+100 points for every friend that recently got vaccinated</span></td>
        <td class="right"><span id="player-friendscore"></span></td>
        <td>&times;</td><td class="right">100</td><td>=</td>
        <td class="right"><span id="player-friendscore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Self Vaccinations</strong><span class="explanation">+100 points for a recent vaccination</span></td>
        <td class="right"><span id="player-vacscore"></span></td>
        <td>&times;</td><td class="right">100</td><td>=</td>
        <td class="right"><span id="player-vacscore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Shares</strong><span class="explanation">+20 points for spreading the word on Facebook/Twitter</span></td>
        <td class="right"><span id="player-sharescore"></span></td>
        <td>&times;</td><td class="right">20</td><td>=</td>
        <td class="right"><span id="player-sharescore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Total Score</strong></td>
        <td></td>
        <td></td><td></td><td></td>
        <td class="right total"><span id="player-score-total"></span></td>
      </tr>
    </table>
  </div>

  <!-- Page: Requests -->
  <div id="page-Go-Viral" class="page" style="display: none"> 
    
    <table class="worksheet">
      <tr>
        <td><strong>Base score</strong><span class="explanation">+10 points for every friend that uses this app</span></td>
        <td class="right"><span id="user-basescore"></span></td>
        <td>&times;</td><td class="right">10</td><td>=</td>
        <td class="right"><span id="user-basescore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Friend vaccinations</strong><span class="explanation">+100 points for every friend that recently got vaccinated</span></td>
        <td class="right"><span id="user-friendscore"></span></td>
        <td>&times;</td><td class="right">100</td><td>=</td>
        <td class="right"><span id="user-friendscore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Your vaccinations</strong><span class="explanation">+100 points for a recent vaccination</span></td>
        <td class="right"><span id="user-vacscore"></span></td>
        <td>&times;</td><td class="right">100</td><td>=</td>
        <td class="right"><span id="user-vacscore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Your shares</strong><span class="explanation">+20 points for spreading the word on Facebook/Twitter</span></td>
        <td class="right"><span id="user-sharescore"></span></td>
        <td>&times;</td><td class="right">20</td><td>=</td>
        <td class="right"><span id="user-sharescore-value"></span></td>
      </tr>
      <tr>
        <td><strong>Total Score</strong></td>
        <td></td>
        <td></td><td></td><td></td>
        <td class="right total"><span id="user-score-total"></span></td>
      </tr>
    </table>

    <h3>How can I increase my score?</h3>
    <div id="request_button" class="button button-requires-connect" onclick="sendRequestInvite()" />Send invite (+20 pts)</div>
    <div id="publish_button" class="button button-requires-connect" onclick="publishStory()" />Publish to Facebook wall (+20 pts)</div>
<a href="https://twitter.com/intent/tweet?url=flutrackr.com&text=Get%20vaccinated%20and%20become%20a%20flu%20hero%20at%20http%3A%2F%2Fflu-trackr.com&via=FluTrackr">
    <div id="twtShareLink" class="button button-requires-connect"/>
Publish to Twitter feed (+20 pts)
</div></a>
    <div id="report_button" class="button button-requires-connect" onclick="openPage('Report-Vaccination');">Report a vaccination (+100 pts)</div>

    <h3>The point award system:</h3>
    <img src="/assets/network.png" width="300" style="margin: 0 auto;" />
    <p>
      The goal of Flu-Trackr is to encourage as many people to get vaccinated as possible. Social networking research shows that 
rewarding the messengers is the best way to get a message across as much of a network as possible. Thus, in addition to getting rewarded for getting vaccinated yourself, you receive points for spreading the word to your friends and for when your friends get vaccinated.
    </p>
  </div>

  <!-- Page: Flu Alerts -->
  <div id="page-Flu-Alerts" class="page" style="display: none"> 
    <p>&nbsp;</p>
    <p>
      Sign up for timely flu information alerts via text messaging or email. We'll alert you when the flu vaccine becomes available or if there is an outbreak in your area. We respect your privacy and will never use your information for any other purpose or share it with anyone else.
    </p>
    <form id="fa-phone-form">
      <label>Phone Number:</label> <input type="text" id="fa-phone" />
      <input type="button" id="fa-button" value="Submit" onclick="fa_phone_submit();"> <br />
      <label>Email:</label> <input type="text" id="fa-email" />
      <input type="button" id="fa-button" value="Submit" onclick="fa_email_submit();">
    </form>
  </div>

  <!-- Page: About Us -->
  <div id="page-About-Us" class="page" style="display: none">
<div class="markdown">
<h3>What is Flu-Trackr?</h3>
<p>Getting the flu is not only unpleasant, but can be deadly. The flu causes over 200,000 hospitalizations and 3,000 deaths a year in the United States alone. Its highly mutagenic nature means 
that you should get vaccinated every year to protect yourself&mdash;and others&mdash;from the latest strain.

<p>Flu-Trackr is an app designed to get as many people 
vaccinated against the flu as possible by answering the five W's:</p>
<p><strong>Who?</strong> You and as many of your friends as possible, and your friends' friends, and your friends' friends' friends...</p>
<p><strong>What?</strong> The 
seasonal flu vaccine, which typically comes out in the fall before the peak of the annual flu season in the United States.</p>
<p><strong>When?</strong> Check our interactive Flu Trends and sign up for Flu Alerts by text/email to learn when to get the flu vaccine.</p>
<p><strong>Where?</strong> The Vaccine Locator feature will find 
the clinics closest to you. </p>
<p><strong>Why?</strong> Getting vaccinated and spreading the word will prevent unpleasantness and sick days, and earn you Flu Hero Points as well!</p>
<h3>Disclaimer</h3>

<p>Flu-Trackr is not meant to replace medical professionals. Consult a physician before getting the flu vaccine if you have an egg allergy, have had a severe reaction to the influenza vaccine, are currently ill, or have had Guillain-Barre Syndrome.
</p>
      <h3>The Flu-Trackr Team</h3>
        <p>ZeNan Chang (UCLA)<p>
        <p>Chi Feng (MIT)</p>
        <p>Allen Lin (Harvard)<p>
        <p>Sourav Sinha (Dartmouth)<p>
        <p>Helena Zhang (MIT)</p>
    </div>
    <h4>Flu-Trackr is powered by</h4>

    <img src="/assets/aws.png" alt="aws"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/php.gif" alt="php"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/mysql.png" alt="php"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/twitter.png" alt="twitter"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/Jquery-mobile-logo.png" alt="php"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/facebook-developers.png" alt="php"  height="30" style="margin:0 0 15px 15px;" />
    <img src="/assets/gd.png" alt="php"  height="30" style="margin:0 0 15px 15px;" />
  </div>

  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQOHsYmVnsBjWkvLJZfelxvHoeRh7IarM&sensor=true"></script> 

  <script type="text/javascript" src="js/app.js"></script>
  <script type="text/javascript" src="js/fb.js"></script>
  <script type="text/javascript" src="js/twitter.js"></script>

  <script type="text/javascript" src="js/jquery.ambiance.js"></script>
  <script type="text/javascript" src="js/jquery.flot.min.js"></script>

  <script type="text/javascript" src="js/raphael.js"></script>
  <script type="text/javascript" src="js/color.jquery.js"></script>
  <script type="text/javascript" src="js/jquery.usmap.js"></script>
</body>
</html>
