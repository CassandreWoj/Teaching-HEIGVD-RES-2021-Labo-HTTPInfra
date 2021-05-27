$(function(){
    console.log("lOADING ADDRESSES");

    function loadAddresses(){
        $.getJSON("/api/addresses/", function(addresses){
            console.log(addresses);
            var message = "No address";
            if(addresses.length >= 3){
                message = addresses[0].street + ', ' + addresses[0].city + '<br>' + addresses[0].country;
            }
            $(".address").html(message);
        });

    };
    loadAddresses();
    setInterval(loadAddresses, 2000);
});
