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
  <meta property="og:image" content="http://www.facebookmobileweb.com/hackbook/img/facebook_icon_large.png"/>
  <meta property="fb:admins" content="20901205" />
  <meta property="fb:app_id" content="528792943826588" />
  
  <!-- See http://developer.apple.com/library/safari/#documentation/appleapplications/reference/SafariHTMLRef/Articles/MetaTags.html -->
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  
  <!-- See https://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
  <link rel="apple-touch-icon" href="http://www.facebookmobileweb.com/hackbook/img/iphone_icon.png" />
  <link rel="apple-touch-startup-image" href="http://www.facebookmobileweb.com/hackbook/img/iphone_splash.png" />
  <link rel="apple-touch-icon-precomposed" href="http://www.facebookmobileweb.com/hackbook/img/iphone_icon.png" />
  
  <!-- See http://davidbcalhoun.com/2010/viewport-metatag for information on the viewport tag. -->
  <meta name="viewport" content="initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />

  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="css/font-awesome.css"  rel="stylesheet" type="text/css" />
  <link href="css/jquery.ambiance.css"  rel="stylesheet" type="text/css" />
  
  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  
</head>

<body onload="window.top.scrollTo(0,1);" style="min-height: 120%">
 
  <div id="mobile-warning" onClick="$('#mobile-warning').fadeOut(500);">
    <img src="assets/biglogo.png" width="500" height="150" />
    <p>This app was optimized to be viewed on mobile devices, e.g. iOS/Android</p>
    <p>This message is only shown if you view this site on a desktop browser.</p>
    <div id="mobile-okay">Click to continue</div>
  </div>
   
  <div id="fb-root"></div>
  <div id="header">
    <div id="back-link" onclick="openPage('root')"><div id="back">Back</div></div>
    <div id="title-wrap">
      <div id="title">Title</div>
    </div>
    <div id="right-block">
      <div class="show_when_not_connected">
        <div id="login_button" class="login-button" onclick="promptLogin()" /><i class="icon-facebook-sign">&nbsp;</i> Login</div>
      </div>
      <div class="show_when_connected">
        <!-- div id="login_button" class="login-button logout-button" onclick="logout()" /><i class="icon-signout">&nbsp;</i></div>-->
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
        <div id="welcome-score">Flu Hero Score: <span id="user-score"></span></div>
        <div id="hiscores" onclick="openPage('Scoreboard');"><i class="icon-trophy golden"></i></div>
      </div>
    </div>

    
    <ul class="menu clearfix"> 
       <li class="menu-item" onclick="openPage('About-Us');">
         <i class="icon-question-sign">&nbsp;</i>
         <div class="label">
           <span class="top">What is Flu-Trackr?</span>
           <span class="bottom">How our app promotes flu vaccinations and reduces infections.</span>
         </div>
       </li>
       <li class="menu-item" onclick="openPage('Flu-Trends');">
         <i class="icon-bar-chart">&nbsp;</i>
         <div class="label">
           <span class="top">Flu Heat Map</span>
           <span class="bottom">Explore local and national flu trends and risk levels.</span>
         </div>
       </li>
      <li class="menu-item" onclick="openPage('Go-Viral');">
        <i class="icon-group">&nbsp;</i>
        <div class="label">
          <span class="top">Go Viral!</span>
          <span class="bottom">Learn about the Flu-Hero point system and how to increase your score!</span>
        </div>
      </li>
       <li class="menu-item" onclick="openPage('Vaccine-Finder');">
         <i class="icon-search">&nbsp;</i>
         <div class="label">
           <span class="top">Vaccine Finder</span>
           <span class="bottom">Find vaccine providers close to your current location.</span>
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
   
   <div id="like-button"><fb:like url="http://flu-trackr.com/" width="275"></fb:like></div>
  </div>
  
  <!-- Page: Surveillance -->
  <div id="page-Flu-Trends" class="page" style="display: none">
  <div id="trend-plot">
    <div id="trend-plot-title">Loading flu trend data</div>
    <div id="trend-plot-canvas">
      Waiting for reverse-geolocation and flu trend data.
    </div>  
  </div>
   <img src="/assets/map-placeholder.png" width="100%" /><br />
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
  
  <!-- Page: Vaccine Finder -->
  <div id="page-Vaccine-Finder" class="page" style="display: none">
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
  <div id="page-Player-Score" class="page">
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
  <div id="page-Go-Viral" class="page"> 
    
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
    <div id="publish_button" class="button button-requires-connect" onclick="publishStory()" />Publish to Twitter feed (+20 pts)</div>
    <div id="report_button" class="button button-requires-connect" onclick="openPage('Report-Vaccination');">Report a vaccination (+100 pts)</div>
    <div class="button button-requires-connect"><a href="https://twitter.com/share" data-lang="en" data-related="FluTrackr:The Javascript API" data-count="none">Tweet</a></div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    <h3>What is the rationale behind the points?</h3>
    <p>
      The goal of Flu-Trackr is to encourage as many people to get vaccinated as possible. Social networking research shows that 
rewarding the messengers is the best way to get a message across much of a network as possible. This is why you are rewarded for
 both getting vaccinated yourself and spreading the word to your friends and if your friends get vaccinated.
    </p>
    <img src="/assets/network.png" width="300" style="margin: 0 auto;" />
  </div>

  <!-- Page: Flu Alerts -->
  <div id="page-Flu-Alerts" class="page" style="display: none"> 
    <p>&nbsp;</p>
    <p>
      Sign up for flu information alerts via text messaging or email. We'll alert you when the flu vaccine becomes available or if there is an outbreak in your area. We respect your privacy and will never use your information for any other purpose or share it with anyone else.
    </p>
    <form id="fa-phone-form">
      <label>Phone Number:</label> <input type="text" id="fa-phone" />
      <input type="button" id="fa-button" value="Submit" onclick="fa_phone_submit();"> <br />
      <label>Email:</label> <input type="text" id="fa-email" />
      <input type="button" id="fa-button" value="Submit" onclick="fa_email_submit();">
    </form>
  </div>

  <!-- Page: About Us -->
  <div id="page-About-Us" class="page">
<div class="markdown">
<h3>What is Flu-Trackr?</h3>
<p>Getting the flu is not only unpleasant, but can be deadly. The flu causes over 200,000 cases of hospitalization and 3,000 deaths a year in the United States alone. Its highly mutagenic nature means 
that you should get vaccinated every year to protect yourself&mdash;and others&mdash;from the latest strain.

<p>Flu-Trackr is an app designed to get as many people 
vaccinated against the flu as possible by answering the five W's:</p>
<p><strong>Who?</strong> You and as many of your friends as possible, and their friends, and friends of friends of friends...</p>
<p><strong>What?</strong> The 
seasonal flu vaccine that usually come out in the fall before the mid-winter flu season peak.</p>
<p><strong>When?</strong> Check our Flu Trends and sign up for text/email alerts on when to get the flu vaccine.</p>
<p><strong>Where?</strong> The Vaccine Finder feature will find 
the clinics closest to you. </p>
<p><strong>Why?</strong> Getting vaccinated and spreading the word will not only prevent unpleasantness and sick days, but earn you Flu Hero Points as well.</p>
</div>
    <div class="markdown">
      <h3>The Flu-Trackr Team</h3>
      <p>Something about something</p>
      <h3>Meet the team</h3>
      <div class="team-member">
        <h4>Chi Feng (MIT)</h4>
        <p>Something about Chi</p>
      </div>
      <div class="team-member">
        <h4>Helena Zhang (MIT)</h4>
        <p></p>
      </div>
      <div class="team-member">
        <h4>ZeNan Chang (UCLA)</h4>
        <p>Something about ZeNan</p>
      </div>
      <div class="team-member">
        <h4>Allen Lin (Harvard)</h4>
        <p>Something about Allen</p>
      </div>
      <div class="team-member">
        <h4>Sourav Sinha (Dartmouth)</h4>
        <p>Something about Sourav</p>
      </div>
    <div>
  </div>

  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQOHsYmVnsBjWkvLJZfelxvHoeRh7IarM&sensor=true"></script> 

  <script type="text/javascript" src="js/app.js"></script>
  <script type="text/javascript" src="js/fb.js"></script>

  <script type="text/javascript" src="js/jquery.ambiance.js"></script>
  <script type="text/javascript" src="js/jquery.flot.min.js"></script>

  <script type="text/javascript" src="js/raphael.js"></script>
  <script type="text/javascript" src="js/color.jquery.js"></script>
</body>
</html>
