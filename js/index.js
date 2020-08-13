
$(document).ready(function(){
  $('#myclass').hide();
  $('#myterm').hide();
  // Initialize Tooltip
  $('[data-toggle="tooltip"]').tooltip(); 
    
  // Add smooth scrolling to all links in navbar + footer link
  $(".navbar a, footer a[href='#home']").on('click', function(event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {

      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (900) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 900, function(){
   
        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });
    
    // variable to store school form to be processed i.e traco
    var schoolType = '';
    //when 'TRACO' is selected as the login type pass value to 'schoolType'
    $('#tracoForm').on('click', function(event){
        event.preventDefault() ;
        schoolType = 'traco';
    });
    //when 'TRIHS' is selected as the login type pass value to 'schoolType'
    $('#trihsForm').on('click', function(event){
        event.preventDefault() ;
        schoolType = 'trihs';
    });
    //login form submission 
    $(document).on('submit', '#login_form', function(event){
        event.preventDefault();
        var username = $('#username').val();
        var password = $('#password').val();
        if (schoolType == '') {
            alert('Please Select the school login from navigation bar !');
        }else{
          $.ajax({
              url:'process/user.php',
              method:"POST",
              data:{username:username, password:password, schoolType:schoolType, login:"login"},
              dataType:'json',
              success:function(data){
                  if (data.msg === 'success') {
                    if (schoolType == 'traco') {
                      document.location.href="http://www.traco.tracoportal.com/visitor/login.php?username="+username+"&password="+password+"&login=login";
                    }else if(schoolType == 'trihs'){
                      document.location.href="http://www.trihs.tracoportal.com/visitor/login.php?username="+username+"&password="+password+"&login=login";
                    }
                  }else{
                    alert(data.msg);                    
                  }
              }
          });
        }
    });
    //update's class & term dropdown when school is selected
    $(document).on('change', '#myschool', function(event){
        event.preventDefault();
        var schoolType = $('#myschool').val();
        $.ajax({
          url:'process/user.php',
          method:'POST',
          data:{schoolType:schoolType, loadClass:'loadClass'},
          dataType:'json',
          success:function(data){
            $('#myclass').html(data.class);
            $('#myterm').html(data.term);
            $('#myclass').show();
            $('#myterm').show();
          }
        });
    });
    //result checker form submition
    $(document).on('submit', '#result_form', function(event){
        event.preventDefault();
        //collecting values from result checker form, storing them in variables
        var admno     = $('#admno').val();
        var myschool = $('#myschool').val();
        var myclass   = $('#myclass').val();
        var myterm   = $('#myterm').val();
        var card        = $('#card').val();
        // variable validation, checking for empty variable
        if (admno == '' || myschool == '' || myclass == '' || myterm == '' || card == '') {
            //alert msg if any empty variable is found
            alert("All field in the result checker is required");
        }else{
          //processing result using functions stored in process/resultValidator.js
            resultChecker(admno, myschool, myclass, myterm, card);
        }

    }); 
});