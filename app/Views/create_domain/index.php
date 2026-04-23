


<html lang="en">
<head>
 <title>Matt Community |Domain Register</title>

</head>
<body><center>
<h2>Creation Your community custom domain</h2><br><br>

<!-- <form name="abc" method="POST" action=".htaccess"> -->
     <form action="models/Create_domain_model.php" method="post">
    <label>Enter Your sub-domain</label>
    <input type="text" name="name" placeholder="type your subdomain" required></input></br></br>
     <label>email</label>
     <input type="text" name="email" placeholder="type your email" required></input></br></br>
   <button type="submit" >Create Community</button></br></br></br></center>
   </html>
</body>






<script>
      var site_url = "<?php echo site_url() ?>";
      var available_events = <?php echo !empty($domain)? $domain: '[]'; ?>;

      console.log(available_events);

    </script> 