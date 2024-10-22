// setting cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  //Getting cookie
  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  //Cookies banner
  if(getCookie('teepee-cookie-accepted') != "accepted"){
    document.getElementById('cookie-banner').style.display = "block";
  }
  else{
    document.getElementById('cookie-banner').style.display = "none";
  }

  function acceptCookies(){
    setCookie('teepee-cookie-accepted','accepted',365);
    document.getElementById('cookie-banner').style.display = "none";
  }