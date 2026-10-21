<?php
// Os backups antigos usam nomes de imagens anteriores aos arquivos atuais.
function imagemProduto(array $produto): string
{
    $arquivo = basename((string) ($produto['imagem'] ?? ''));
    $fotos = require __DIR__ . '/produtos/fotos-catalogo.php';
    $nome = mb_strtolower(trim((string) ($produto['nome'] ?? '')), 'UTF-8');
    $foto = $fotos[$nome] ?? null;
    if (!$foto && $arquivo !== '') {
        foreach ($fotos as $candidata) {
            if (in_array($arquivo, $candidata['anteriores'], true)) {
                $foto = $candidata;
                break;
            }
        }
    }
    $originalExiste = $arquivo !== '' && is_file(__DIR__ . '/produtos/' . $arquivo);
    // Novos uploads continuam tendo prioridade sobre a coleção de demonstração.
    if ($foto && (!$originalExiste || in_array($arquivo, $foto['anteriores'], true))) {
        $novo = '/produtos/padronizados/' . $foto['arquivo'];
        if (is_file(__DIR__ . $novo)) return '/brutus' . $novo;
    }
    if ($arquivo !== '' && is_file(__DIR__ . '/produtos/' . $arquivo)) {
        return '/brutus/produtos/' . rawurlencode($arquivo);
    }
    $imagens = [
        'Burguer BBQ' => '683dc0f002fdc.jpg',
        'Burguer do Chef' => '683dc13c83624.png',
        'Brutu Bacon Especial' => '683dc19e9e66f.png',
        'Mini Brutu Cheese' => '683dc244d71bf.jpeg',
        'Kids Brutu' => '683dc29674610.jpg',
        'Combo Classico' => '683dc2eb7eba4.jpg',
        'Double Brutu Bacon' => '683dc3265398a.jpg',
        'Combo kids' => '683dc37e361d7.jpg',
        'Combo Kids 2' => '683dc43388796.jpg',
        'Combo Veggie' => '683dc4967d868.jpg',
        'Combo supreme' => '683dc4ed0b32e.jpg',
        'Batata Rusticas' => '683dc51791fd2.jpg',
        'Onion Rings' => '683dc540e9e64.jpg',
        'Nuggets' => '683dc57cdc776.jpg',
        'Chips de Batata Doce' => '683dc5ada6b6c.jpg',
        'Refrigerante' => '683dc5d17165b.png',
        'Suco' => '683dc5f54b946.png',
        'Cerveja Artesanal' => '683dc61b739cc.jpg',
        'Milkshake' => '683dc640cca31.jpg',
        'Mini Churrus' => '683dc66d53acb.jpg',
        'Brownie com Sorvete' => '683dc6983dbbc.jpg',
    ];
    $arquivo = $imagens[trim((string) ($produto['nome'] ?? ''))] ?? null;
    return $arquivo ? '/brutus/produtos/' . rawurlencode($arquivo) : '/brutus/img/favicon.svg';
}
