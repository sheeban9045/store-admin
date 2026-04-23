<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<main>
<div class="cs-loader">
  <div class="cs-loader-inner">
    <label>●</label>
    <label>●</label>
    <label>●</label>
    <label>●</label>
    <label>●</label>
    <label>●</label>
  </div>
    <div class="message-container">
    <p class="loading-text">Setting up your website. Please wait...</p>
  </div>
</div>

</main>
<style type="text/css">
	@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap');
  * {
  border: 0;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
:root {
  --hue: 223;
  --bg: hsl(var(--hue), 90%, 95%);
  --fg: hsl(var(--hue), 90%, 5%);
  --trans-dur: 0.3s;
  font-size: calc(16px + (24 - 16) * (100vw - 320px) / (1280 - 320));
}
body {
  background-color: var(--bg);
  color: var(--fg);
  font: 1em/1.5 sans-serif;
  height: 100vh;
  display: grid;
  place-items: center;
  transition: background-color var(--trans-dur);
}
main {
  padding: 1.5em 0;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  gap: 25px;
}
body {
  margin: 0;
  padding: 0;
  background:#59B4FF;
}

.cs-loader {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.cs-loader-inner {
  transform: translateY(-50%);
  top: 45%;
  position: absolute;
  width: calc(100% - 200px);
  color: #FFF;
  padding: 0 100px;
  text-align: center;
}

.cs-loader-inner label {
  font-size: 20px;
  opacity: 0;
  display:inline-block;
}

@keyframes lol {
  0% {
    opacity: 0;
    transform: translateX(-300px);
  }
  33% {
    opacity: 1;
    transform: translateX(0px);
  }
  66% {
    opacity: 1;
    transform: translateX(0px);
  }
  100% {
    opacity: 0;
    transform: translateX(300px);
  }
}

@-webkit-keyframes lol {
  0% {
    opacity: 0;
    -webkit-transform: translateX(-300px);
  }
  33% {
    opacity: 1;
    -webkit-transform: translateX(0px);
  }
  66% {
    opacity: 1;
    -webkit-transform: translateX(0px);
  }
  100% {
    opacity: 0;
    -webkit-transform: translateX(300px);
  }
}

.cs-loader-inner label:nth-child(6) {
  -webkit-animation: lol 3s infinite ease-in-out;
  animation: lol 3s infinite ease-in-out;
}

.cs-loader-inner label:nth-child(5) {
  -webkit-animation: lol 3s 100ms infinite ease-in-out;
  animation: lol 3s 100ms infinite ease-in-out;
}

.cs-loader-inner label:nth-child(4) {
  -webkit-animation: lol 3s 200ms infinite ease-in-out;
  animation: lol 3s 200ms infinite ease-in-out;
}

.cs-loader-inner label:nth-child(3) {
  -webkit-animation: lol 3s 300ms infinite ease-in-out;
  animation: lol 3s 300ms infinite ease-in-out;
}

.cs-loader-inner label:nth-child(2) {
  -webkit-animation: lol 3s 400ms infinite ease-in-out;
  animation: lol 3s 400ms infinite ease-in-out;
}

.cs-loader-inner label:nth-child(1) {
  -webkit-animation: lol 3s 500ms infinite ease-in-out;
  animation: lol 3s 500ms infinite ease-in-out;
}

.message-container {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 10%;
}
.loading-text {
  font-size: 1.2rem;
  opacity: 0;
  animation: fadeIn 0.5s ease-in-out 1s forwards;
  color: white;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		// Uncomment the AJAX code if you want to trigger it after the countdown
		$.ajax({
		    url: 'install_community?order_id=<?php echo $order_id; ?>',
		    method: 'GET',
		    dataType: 'json',
		    success: function(response) {
		        console.log("Success");
		      //  window.location.href = "http://<?php echo $domain; ?>.webhut.net";
		      if(response.success){ 
		        // window.location.href = "http://webhut.net/<?php echo $domain; ?>/login/community_success/1";
            window.location.href = "http://<?php echo $domain; ?>/index.php/login/community_success/1/user_email/<?php echo $user_email?>/orderid/<?php echo $order_id ?>";

		      } else {
		        alert("Some error occured while setup the community please try later!");
		        // window.location.href = "http://webhut.net/store-admin";
            window.location.href = "/store-admin";
            
		      }
		    },
		    error: function() {
		        // Handle errors here
		    }
		});
	});
</script>