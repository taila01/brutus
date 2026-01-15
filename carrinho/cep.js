function buscaCep() {
    let cep = document.getElementById('CEP').value;

    if (cep !== "") {
        let url = "https://brasilapi.com.br/api/cep/v1/" + cep;
        let req = new XMLHttpRequest();

        req.open("GET", url);
        req.send();

        req.onload = function() {
            if (req.status === 404) {
                alert("CEP inválido");
            }
        }
    }
}

window.onload = function() {
    let txtCEP = document.getElementById("CEP");
    txtCEP.addEventListener("blur", buscaCep);
}
