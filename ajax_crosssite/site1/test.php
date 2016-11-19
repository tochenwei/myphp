<hrml>
<body>
<script language="javascript" src="http://www.4399.com/jss/jquery-1.6.1.min.js"></script>
<script type="text/javascript">  
$(function(){  
$.ajax(  
    {  
        type:'get',  
        url :'http://test.cc/index.php?loginuser=lee&loginpass=123456',  
        dataType : 'jsonp',  
        jsonp:"jsoncallback",  
        success: function(data) {  
            alert("用户名："+ data.user +" 密码："+ data.pass);  
        },  
        error : function() {  
            alert('fail');  
        }  
    }  
);  
})  
</script>  
</body>
</html>