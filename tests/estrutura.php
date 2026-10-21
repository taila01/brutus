<?php
// Verifica referências que podem quebrar ao mover arquivos entre diretórios.
$root = dirname(__DIR__);
$errors = [];
$count = 0;
foreach (['app', 'public', 'config', 'scripts', 'tests'] as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if (strtolower($file->getExtension()) !== 'php' || $file->getPathname() === __FILE__) continue;
        $source = file_get_contents($file->getPathname());
        preg_match_all('~\b(?:require_once|include_once|require|include)\s+__DIR__\s*\.\s*[\'"]([^\'"]+)[\'"]~', $source, $includes);
        foreach ($includes[1] as $include) {
            if (str_ends_with($include, '/.env.local.php')) continue; // Configuração local opcional.
            ++$count;
            if (!is_file($file->getPath() . $include)) $errors[] = $file->getPathname() . ': include inexistente ' . $include;
        }
        preg_match_all('~(?:src|href)=[\'"](/brutus/(?:assets|produtos)/[^\'"?<>]+)~', $source, $assets);
        foreach ($assets[1] as $asset) {
            ++$count;
            $path = $root . '/public/' . substr($asset, strlen('/brutus/'));
            if (!is_file($path)) $errors[] = $file->getPathname() . ': arquivo estático inexistente ' . $asset;
        }
    }
}
foreach (['app', 'config', 'database', 'certs', 'scripts', 'tests', '.env.local.php'] as $private) {
    if (file_exists($root . '/public/' . $private)) $errors[] = 'Arquivo privado dentro da pasta pública: ' . $private;
}
if ($errors) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}
echo "Estrutura OK: $count referências verificadas; arquivos privados fora de public/.\n";
