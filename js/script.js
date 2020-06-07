// jQuery-funktio tallentaa kirjoitetun kommentin Enter-näppäimen
// painalluksesta (ei käytetä buttonia, button piilotettu ja sen id=submit), blog_text.php
$(function () {
    $("#tekstiarea").keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code === 13) {  // Enter
            $("#submit").trigger('click');
            // Ei lue Enteriä rivinvaihdoksi
            return false;
        }
    });
});