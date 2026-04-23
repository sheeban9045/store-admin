
   
    <link rel="stylesheet" href="<?php echo base_url("assets/css/simple-calendar.css"); ?>" />
    <script src="<?php echo base_url("assets/js/simple-calendar/fullcalendar.js"); ?>"></script>


    <link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.css"); ?>" />
   <script src = "<?php echo base_url("assets/js/toastr/toastr.js"); ?>"></script>
    

    <div class="container">
        <h3 style="text-align: center"><?php echo app_lang('holiday/vacation'); ?></h3>
        <div id="calendar"></div>
    </div>
       
    <script>
      var site_url = "<?php echo site_url() ?>";
      var available_events = <?php echo !empty($holidays)? $holidays: '[]'; ?>;

      console.log(available_events);

    </script>

<script src="<?php echo base_url("assets/js/simple-calendar/calendar.js"); ?>"></script>
  
