//add/remove loader during image upload
function add_loader()
{
	$("#page_loader_img").css("display","block"); 
}
function remove_loader()
{
	$("#page_loader_img").css("display","none"); 
}
function date_time(id){
    date = new Date;
    year = date.getFullYear();
    month = date.getMonth();
    months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    d = date.getDate();
    day = date.getDay();
    days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    h = date.getHours();
    if(h<10)
    {
            h = "0"+h;
    }
    m = date.getMinutes();
    if(m<10)
    {
            m = "0"+m;
    }
    s = date.getSeconds();
    if(s<10)
    {
            s = "0"+s;
    }
    //result = ''+days[day]+' '+months[month]+' '+d+' '+year+' '+h+':'+m+':'+s;
    result = ''+d+' '+months[month]+' '+year+' - '+h+':'+m+':'+s;
    document.getElementById(id).innerHTML = result;
    setTimeout('date_time("'+id+'");','1000');
    return true;
}

function updateClock (id,serverdate,sec,offset){
	
	
	//var d = new Date ();
	//alert(d);

	var d = new Date(serverdate);
 
	d.setSeconds(d.getSeconds() + sec);
 
	
	utc = d.getTime() + (d.getTimezoneOffset() * 60000);
	
  var currentTime = new Date (utc + (3600000*offset));

  // day, month and year
  day = currentTime.getDate();
  month = currentTime.getMonth();
  months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  year = currentTime.getFullYear();
  
  
  
  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
  var currentSeconds = currentTime.getSeconds ( );

  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

  // Choose either "AM" or "PM" as appropriate
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

  // Convert the hours component to 12-hour format if needed
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

  // Convert an hours component of "0" to "12"
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;

  // Compose the string for display
  var currentTimeString = day + " " + months[month] + " " + year + " - " +currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
	
	
	document.getElementById(id).innerHTML = currentTimeString;
	
	//scnds++;
	sec = sec+1;
	setTimeout("updateClock('"+id+"','"+serverdate+"',"+sec+",'"+offset+"')",1000);
}