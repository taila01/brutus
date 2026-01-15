function buscaCep(){
    let cep = document.getElementById('CEP').value;
    if(cep!==""){
        let url = "https://brasilapi.com.br/api/cep/v1/" + cep;
        let req = new XMLHttpRequest();
        req.open("GET", url);
        req.send();

        req.onload = function(){
            if(req.status === 200){
                let endereco = JSON.parse(req.response);
                document.getElementById("rua").value = endereco.street;
                document.getElementById("bairro").value = endereco.neighborhood;
                document.getElementById("cidade").value = endereco.city + " - " + endereco.state;
            }
            else if(req.status===404){
                alert("CEP inválido");
            }
            else{
                alert ("Erro ao fazer a requisição");
            }
        }
    }
}

window.onload = function(){
    let txtCEP = document.getElementById("CEP");
    txtCEP.addEventListener("blur", buscaCep);
}