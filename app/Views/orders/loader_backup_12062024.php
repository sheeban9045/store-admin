<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>

	@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap');

	* {
		box-sizing: border-box;
	}
	.loading-container {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-image: linear-gradient( 95.2deg, rgba(173,252,234,1) 26.8%, rgba(192,229,246,1) 64% );
		/* Semi-transparent black overlay */
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 9999;
		flex-direction: column;

		&:before {
			content: '';
			position: absolute;
			width: 100%;
			height: 3px;
			background-color: #fff;
			bottom: 0;
			left: 0;
			border-radius: 10px;
			animation: movingLine 2.4s infinite ease-in-out;
		}
	}

	@keyframes movingLine {
		0% {
			opacity: 0;
			width: 0;
		}

		33.3%,
		66% {
			opacity: 0.8;
			width: 100%;
		}

		85% {
			width: 0;
			left: initial;
			right: 0;
			opacity: 1;
		}

		100% {
			opacity: 0;
			width: 0;
		}
	}
	.progressbar {
	  position: relative;
	  max-width: 500px;
	  width: 100%;
	  margin: 30px auto 0;
	  height: 30px;
	  background: #274545;
	  overflow: hidden;
	}

	span.progress {
	  position: absolute;
	  left: 0;
	  top: 0;
	  bottom: 0;
	  width: 0;
	  background: #F6635C;
	  transition: all .3s;
	}
	.counter{
		font-size: 30px;
	}

</style>
<div class="loading-container">
	<div class="counter">0</div>
	<div class="progressbar">
	  <span class="progress"></span>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var progress = $('.progressbar .progress');
		var counter = $('.counter');

		function counterInit(fValue, lValue, delay) {
			var counterValue = parseInt(counter.text());
			counterValue = fValue; // Start from a specific value
			progress.css({ 'width': counterValue + '%' });

			var interval = setInterval(function() {
				counter.text(counterValue + '%');
				progress.css({ 'width': counterValue + '%' });

				counterValue++;

				if (counterValue > lValue) {
					clearInterval(interval); // Stop the interval when the countdown reaches the end value
				}
			}, delay);
		}

		counterInit(0, 90, 600); // Start from 0%, end at 90%, and update every 200 milliseconds
        
		// Uncomment the AJAX code if you want to trigger it after the countdown
		$.ajax({
		    url: 'install_community?order_id=<?php echo $order_id; ?>',
		    method: 'GET',
		    success: function(response) {
		        console.log("Success");
		      //  window.location.href = "http://<?php echo $domain; ?>.webhut.net";
		      window.location.href = "http://webhut.net/<?php echo $domain; ?>";
		    },
		    error: function() {
		        // Handle errors here
		    }
		});
	});
</script>