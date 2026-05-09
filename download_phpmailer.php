<?php
$url = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip';
$context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
file_put_contents('phpmailer.zip', file_get_contents($url, false, $context));
$zip = new ZipArchive;
if ($zip->open('phpmailer.zip') === TRUE) {
    $zip->extractTo('.');
    $zip->close();
}
unlink('phpmailer.zip');
if (!is_dir('src/PHPMailer')) {
    rename('PHPMailer-6.9.1/src', 'src/PHPMailer');
}
// Clean up the rest
array_map('unlink', glob("PHPMailer-6.9.1/*.*"));
@rmdir('PHPMailer-6.9.1/language');
@rmdir('PHPMailer-6.9.1');
echo "PHPMailer downloaded.\n";
