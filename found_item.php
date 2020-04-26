<?php

    if( $subpage == "" ){
        header("HTTP/1.0 422 Unprocessable Entity");
        die();
    }
    
    $item_id = $subpage;
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Item.php';
    
    use dbapi\Models\Item;
    
    try{
        $item = Item::find($item_id);
        if( is_null($item) ){
            include_once $_SERVER['DOCUMENT_ROOT'] . '/404.php';
            die();
        }
    }catch (Exception $e){
        header("HTTP/1.0 422 Unprocessable Entity");
        die();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>FindMe</title>
    <script
      src="https://code.jquery.com/jquery-3.4.1.min.js"
      integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
      crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body .centered{
            width: 368px;
            margin: 10px auto;
            padding: 0 0.2rem;
            text-align: center;
        }
        
        .title{
            background: #D81B60;
            color: #fff;
            padding-bottom: 0.3rem;
        }
        
        .header{
            margin-bottom: 1rem;
            padding-bottom: 0.2rem;
            border-bottom: 1px solid #4e4e4e;
        }
        
        .header .info{
            text-align: left;
            margin-bottom: 0.2rem;
            display: table;
        }
        
        .header .info label{
            padding-right: 0.5rem;
            font-weight: 400;
            font-size: 1.1rem;
            margin-bottom: auto;
            height: 100%;
            display: table-cell;
        }
        
        .header .info p{
            display: table-cell;
            font-weight: 200;
            font-size: 1.1rem;
        }
    
        .error span{
            display: block;
            margin: 0 0.2rem 1.5rem 0.2rem;
            padding: 0.2rem 0.1rem;
            background: #ffe7e7;
            border: 1px solid #ff8e86;
            border-radius: 0.2rem;
            color: #a00b00;
        }
        
        .success{
            display: block;
            margin: 0rem 0.2rem;
            padding: 0.2rem 0.1rem;
            border: 1px solid #005203;
            border-radius: 0.2rem;
            color: #005203;
            font-size: 1.5rem;
        }
        
        form{
            padding: 0.5rem 0;
        }
        
        .input-wrapper{
            text-align: left;
        }
        
        .input-wrapper label{
            width: 80px;
        }
        
        .input-wrapper input{
            border: none;
            border-bottom: 1px solid #000000;
            background: #000000;
            padding: 0 0.2rem;
            width: 150px;
        }
        
        .input-wrapper input:disabled{
            border-bottom: 1px solid #4c4c4c;
            background: #eaeaea;
        }
        
        .input-wrapper textarea{
            width: 100%;
        }
        
        .location-wrapper{
            position: relative;
            margin-bottom: 0.5rem;
        }
        
        #location-btn{
            position: absolute;
            top: 0.1rem;
            right: 0;
            height: calc(100% - 0.5rem);
        }
    </style>
</head>
<body>

    <div class="centered">
        <h2 class="title">FindMe App</h2>
        <div class="header">
            <div class="info">
                <label>Found Item:</label>
                <p>
                    <?php echo $item->getName(); ?>
                </p>
            </div>
            <div class="info">
                <label>Description:</label>
                <p>
                    <?php echo $item->getDescription(); ?>
                </p>
            </div>
        </div>
        <form 
            id="found-item"
            action="/found-item-message/add"
            method="post"
        >
            <div id="error" class="error">
                <span data-case="location" style="display:none;">
                    Could not get location. Please make sure you give this site permission to access your location.
                    <br>
                    <a target="_blank" href="https://support.google.com/chrome/answer/142065?hl=en">How to enable location in Chrome.</a>
                </span>
                <span data-case="required" style="display:none;">Please either enter a message or set your location</span>
                <span data-case="error" style="display:none;"></span>
            </div>
            
            <div class="location-wrapper">
                <div class="input-wrapper">
                    <label for="lat">Latitude</label>
                    <input type="text" id="lat" name="lat" value="" disabled>
                </div>
                
                <div class="input-wrapper">
                    <label for="lng">Longitude</label>
                    <input type="text" id="lng" name="lng" value="" disabled>
                </div>
                <button id="location-btn">Set Location</button>
            </div>
            
            <div class="input-wrapper">
                <label for="message">Message</label>
                <textarea id="message" name="message" form="found-item" rows="4" cols="45" placeholder="Enter message here..."></textarea>
            </div>
            
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>" >
            <br>
            <input id="submit-message" type=submit onclick="event.preventDefault(); submitMessage();" value="Send Message" >
            <button id="processing" style="display:none;" disabled>Processing...</button>
        </form>
        <div id="success" class="success" style="display:none;">
            <span>Thank you for contacting the item's owner!</span>
        </div>
    </div>
    

    <footer>
        <div class="container clearfix">
        </div>
    </footer>

</body>
<script>
    const form = $('#found-item');
    const lat = document.getElementById("lat");
    const lng = document.getElementById("lng");
    const error = document.getElementById("error");
    const success = document.getElementById("success");
    
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, locationError);
      } else {
        $(error).find('[data-case="location"]').show();
      }
    }
    
    function showPosition(position) {
        $(error).find('span').hide();
        $(lat).val( position.coords.latitude.toFixed(8) );
        $(lng).val( position.coords.longitude.toFixed(8) );
    }
    
    function locationError(){
        $(error).find('span').hide();
        $(error).find('[data-case="location"]').show();
    }
    
    function processing(){
        $('#submit-message').hide();
        $('#processing').show();
        $(error).find('span').hide();
    }
    
    function finished(){
        $('#processing').hide();
        $('#submit-message').show();
    }
    
    function done(){
        $('#processing').hide();
        $(success).show();
        $(form).hide();
    }
    
    function submitMessage(){
        let item_id = $(form).find('[name="item_id"]').val();
        let lat = $(form).find('[name="lat"]').val().trim();
        let lng = $(form).find('[name="lng"]').val().trim();
        let message = $(form).find('[name="message"]').val().trim();
        
        processing();
        
        if( (lat == '' || lng == '') && message == '' ){
            $(error).find('[data-case="required"]').show();
            finished();
            return;
        }
    
        $.ajax({
        	url: $(form).attr('action'),
        	type: 'POST',
        	data: { 'item_id': item_id, 'lat': lat, 'lng': lng, 'message': message }, 
        	dataType: 'json',
        	error: function( xhr, code, error ){
        	    finished();
                $(error).find('[data-case="error"]').html("Could not submit messsage.").show();
        	},
        	success: function( data ){
        	    if( data.result == "success" ){
        	        done();
        	    }else{
        	        //error
        	        finished();
        	        if( data.error != '' ){
        	            $(error).find('[data-case="error"]').html(data.error).show();
        	        }else{
        	            $(error).find('[data-case="error"]').html(data.exception).show();
        	        }
        	    }
        	}
        });
    }
    
    $('#location-btn').on('click',function(e){
        e.stopPropagation();
        e.preventDefault();
        getLocation();
    });
</script>
</html>