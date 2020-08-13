$(document).ready(function () {
	//global variable holding card availability
	var cardAvai;
	var resultAvai = 'snow';
});
//function to check for card availability using ajax through user.php
function resultChecker(admno, school, myclass, term, pin) {
	//using ajax to process data
	$.ajax({
      //process page
      url:'process/user.php',
      //process method
      method:'post',
      //variables gotten from this func param to be processed at the user.php,
      data:{admno:admno, schoolType:school, myclass:myclass, term:term, pin:pin},
      //return data format
      dataType:'json',
      success:function(data){
        //returning response output to index.html
        cardAvai =  data.output;
        //card availability response
        if (cardAvai == 'invalid') {
            alert('Error ! incorrect pin');
        }else if(cardAvai == 'used by another') {
            alert('Error ! Pin used by another customer');
        }else if(cardAvai == 'used by me' || cardAvai == 'used by none') {
            $.ajax({
		      //process page
		      url:'process/user.php',
		      //process method
		      method:'post',
		      //variables gotten from this func param to be processed at the user.php,
		      data:{cardAvai:cardAvai, radmno:admno, rschool:school, rclass:myclass, rterm:term, rpin:pin},
		      //return data format
		      dataType:'json',
		      success:function(data){
		        //returning response output to index.html
		        resultAvai =  data.output;
		        //result availability response
		        if (resultAvai == 'not avaliable') {
                    alert('Notice ! Result not avaliable.');
                }else if(resultAvai == 'avaliable') {
                    //result download for secondary school
                    var res = myclass.substring(0, 3);
                    if (res == 'SSS' || res == 'SS') {
                        document.location.href="process/studentResult/sssResult.php?school="+school+"&admno="+admno+"&card="+pin+"&term="+term+"&class="+myclass;
                    }else if(res == 'JSS' || res == 'JS'){
                        document.location.href="process/studentResult/jssResult.php?school="+school+"&admno="+admno+"&card="+pin+"&term="+term+"&class="+myclass;
                    }else{
                    	//result download for primary
                    	  document.location.href="process/studentResult/priResult.php?admno="+admno+"&card="+pin+"&term="+term+"&class="+myclass;
                	}
                } 
		      }
		    });
        }

      }
    });
}
