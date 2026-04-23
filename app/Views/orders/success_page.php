<style>
  .my-card-body {
    display: flex;
    justify-content: center;
    align-items: center;
/*    height: 100vh; /* Adjust as needed to center vertically */*/
  }

  .my-card {
    text-align: center;
    background: white;
    padding: 60px;
    border-radius: 4px;
    box-shadow: 0 2px 3px #C8D0D8;
  }

  .my-checkmark-container {
    border-radius: 50%;
    height: 200px;
    width: 200px;
    background: #F8FAF5;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
  }

  .my-checkmark {
    color: #9ABC66;
    font-size: 100px;
  }

  .my-heading {
    color: #88B04B;
    font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
    font-weight: 900;
    font-size: 40px;
    margin-bottom: 10px;
  }

  .my-p-tag {
    color: #404F5E;
    font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
    font-size: 20px;
    margin: 0;
  }
  a.edit.setup_community_button {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #2e4053;
    border-color: #2e4053;
    color: #fff;
    margin: 5% 0%;
    padding: 5% 0%;
    border-radius: 10px;
}
</style>
   
<div id="page-content" class="page-wrapper clearfix my-card-body">
  <div class="my-card">
    <div class="my-checkmark-container">
      <i class="my-checkmark">✓</i>
    </div>
    <h1 class="my-heading">Success</h1>
    <p class="my-p-tag">Payment successful!<br /> Thank you for your purchase.</p>
    <?php
    if(isset($is_community_set) && empty($is_community_set)){
        echo modal_anchor(get_uri("orders/modal_community?order_id=".($order_id)), "Setup Your Community", array("class" => "edit setup_community_button btn-primary", "title" => "Setup Community"));
    }?>
    
  </div>
</div>