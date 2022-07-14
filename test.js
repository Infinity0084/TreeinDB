const limitOrdersRequest = new XMLHttpRequest();

limitOrdersRequest.onreadystatechange = function(){
    if(this.readyState === 4){
        if(this.status === 200){
            limitOrdersAnswer=this.responseText;

            alert(limitOrdersAnswer);
        }
        else{
            console.log("Статус запроса: "+this.status+".Не возможно обновить значения.");
        }
    }

}

limitOrdersRequest.open('GET', 'test.php');
limitOrdersRequest.send();