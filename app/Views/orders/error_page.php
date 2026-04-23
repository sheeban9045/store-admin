    <style>
        .my-card-body {
            display: flex;
            justify-content: center;
            align-items: center;
            
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
            color: #E74C3C; /* Use a different color for "Failed" */
            font-size: 100px;
        }

        .my-heading {
            color: #E74C3C; /* Use a different color for "Failed" */
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
    </style>

<div id="page-content" class="page-wrapper clearfix my-card-body">
    <div class="my-card">
        <div class="my-checkmark-container">
            <i class="my-checkmark">✗</i> <!-- Use "✗" or another symbol for "Failed" -->
        </div>
        <h1 class="my-heading">Failed</h1>
        <p class="my-p-tag">Sorry, your request has failed;<br /> please try again later.</p>
    </div>
</div>



