/* 类别跳转 */
function display(ID)
{
	if (document.getElementById(ID).style.display == "none") {
		document.getElementById(ID).style.display = "";
	}else{
		document.getElementById(ID).style.display = "none";
	}
}
         function openn(){
          var args = openn.arguments;
          if(args[0] == 0) return;
          location.href=(args[0]);
          }