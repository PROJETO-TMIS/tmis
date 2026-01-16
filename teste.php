<?php
$dir = __DIR__ . '/contracts';

if (!is_dir($dir)) {
    echo '❌ Pasta contracts NÃO existe';
    exit;
}

if (!is_writable($dir)) {
    echo '❌ Pasta contracts NÃO tem permissão de escrita';
    exit;
}

echo '✅ Pasta OK e gravável';