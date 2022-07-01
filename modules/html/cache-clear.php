<?php
/**
 * Очистка кеша
 */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */

require_once ROOT_DIR . '/lib/Optimize.php';

/**
 * Удаление данных в директории
 *
 * @param string $path
 * @return bool
 */
function Delete(string $path): bool
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..', '.gitignore'));
        foreach ($files as $file) {
            Delete(realpath($path) . '/' . $file);
        }
        return @rmdir($path);
    } else if (is_file($path) === true) {
        return @unlink($path);
    }
    return false;
}

$cache->clear();

Delete(ROOT_DIR . '/cache/');

$html = $twig->render('blank.twig', [
    'title' => 'Очистка кеша',
    'typeAlert' => 'success',
    'description' => 'Очистка кеша успешна выполена'
]);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}

