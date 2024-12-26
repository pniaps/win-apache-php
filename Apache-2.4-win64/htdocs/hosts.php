<?php
$file = "C:\\Windows\\System32\\drivers\\etc\\hosts";
$conf_folder = dirname(__DIR__).'\conf\configs';
$lines = file($file, FILE_IGNORE_NEW_LINES);
$php_default = 'php84';
$php_folders = [
    'php53' => 'php-5.3-Win32-VC9-x64',
    'php54' => 'php-5.4-Win32-VC9-x64',
    'php55' => 'php-5.5-Win32-VC11-x64',
    'php56' => 'php-5.6-Win32-VC11-x64',
    'php70' => 'php-7.0-Win32-VC14-x64',
    'php71' => 'php-7.1-Win32-VC14-x64',
    'php72' => 'php-7.2-Win32-VC15-x64',
    'php73' => 'php-7.3-Win32-VC15-x64',
    'php74' => 'php-7.4-Win32-VC15-x64',
    'php80' => 'php-8.0-Win32-vs16-x64',
    'php81' => 'php-8.1-Win32-vs16-x64',
    'php82' => 'php-8.2-Win32-vs16-x64',
    'php83' => 'php-8.3-Win32-vs16-x64',
    'php84' => 'php-8.4-Win32-vs17-x64',
];

$dominios = [];

foreach($lines as $number => $line){
    if(preg_match("/127.0.0.1\t([\w.-]+)$/", $line, $matches)){
        if(!file_exists($conf_folder.'/'.$matches[1].'.conf')){
            print('Domain {'.$matches[1].'} from hosts file is not configured in web server'."\n");
        }else{
            $dominios[$matches[1]] = [
                'domain' => $matches[1],
                'php' => $php_default,
                'folder' => '',
            ];
        }
    }
}

foreach($dominios as $dominio => $valor){
    $conf_file = $conf_folder.'/'.$dominio.'.conf';
    $lineasvh = file($conf_file, FILE_IGNORE_NEW_LINES);
    $host_validated = false;
    foreach($lineasvh as $number => $line){
        if(preg_match("/ServerName (.*)/", $line, $matches)){
            $host_validated = true;
        }
        if(preg_match("/DocumentRoot (.*)$/", $line, $matches)){
            $dominios[$dominio]['folder'] = $matches[1];
        }
        if(preg_match("/{WAP_SERVER}\/(.*)\/php-cgi.exe/", $line, $matches)){
            $dominios[$dominio]['php'] = array_search($matches[1], $php_folders);
        }
    }
}

ksort($dominios);

$port = getenv('WAP_PORT');

echo '<!doctype html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<title>Defined hosts</title>';
echo '<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">';
echo '<style>';
echo 'html {
font-family: Sarabun, sans-serif;
  box-sizing: border-box;
  font-size: 16px;
}

*, *:before, *:after {
  box-sizing: inherit;
}

body, h1, h2, h3, h4, h5, h6, p, ol, ul {
  margin: 0;
  padding: 0;
  font-weight: normal;
}

ol, ul {
  list-style: none;
}

img {
  max-width: 100%;
  height: auto;
}
table{
  width: 100%;
  border-collapse: collapse;
}
table.border td{border: 1px solid #ccc}
table th{text-align: left}
table td{padding: 5px;}
table thead tr{ background-color: #0000FF; color: #FFFFFF; }
table tbody tr:nth-of-type(odd){ background-color: #eee; }
table tbody tr:hover{ background-color: #ddd; }
';
echo '</style>';
echo '</head>';
echo '<body style="padding: 20px">';
echo '<h1 style="text-align: center; padding: 15px">win-apache-php defined hosts</h1>';
echo '<table class="border">';
echo '<thead><tr><th>Domain</th><th>PHP Version</th><th>Folder</th></tr></thead><tbody>';
foreach ($dominios as $dominio){
    $url = 'http://'.$dominio['domain'];
    if($port && $port!= 80){
        $url .= ':'.$port;
    }
    echo '<tr><td><a href="'.$url.'">'.$url.'</a></td><td><a href="'.$url.'/phpinfo">'.$dominio['php'].'</a></td><td>'.$dominio['folder'].'</td></tr>';
}
echo '';
echo '</tbody></table>';
echo '<h1 style="text-align: center; padding: 15px">win-apache-php configuration</h1>';
echo '<table class="border">';
echo '<thead><tr><th>Configuration</th><th>Value</th></tr></thead><tbody>';
echo '<tr><td>WAP_SERVER</td><td>'.getenv('WAP_SERVER').'</td></tr>';
echo '<tr><td>WAP_DOCUMENT_ROOT</td><td>'.getenv('WAP_DOCUMENT_ROOT').'</td></tr>';
echo '</tbody></table>';
echo '</body>';
echo '</html>';
