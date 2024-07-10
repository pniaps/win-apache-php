<?php
$file = "C:\\Windows\\System32\\drivers\\etc\\hosts";
$conf_folder = __DIR__.'\Apache-2.4-win64\conf\configs';
$hosts_lines = file($file, FILE_IGNORE_NEW_LINES);
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
];

function show_usage()
{
    print("
This utility creates virtualhosts in apache and add the domain in 'hosts' file.

usage:
    - vh show <domain> <folder> <php>
        Shows the configuration that will be generated for the virtualhost. The
        'php' parameter is optional but can be from 'php53' to 'php83'.

    - vh add <domain> <folder> <php>
        Add a <domain> with it's virtual host in <folder>. The
        'php' parameter is optional but can be from 'php53' to 'php83'.

    - vh remove <domain>
        Removes a <domain> and it's virtual host

    - vh fix
        Add existing domains to hosts file

    - vh list
        Lists all domains with it's own virtual host
");
}

function generate_conf($domain, $folder, $php)
{
    global $php_folders;
    $lines = [];
    $lines[] = "<VirtualHost *:\${WAP_PORT}>";
    $lines[] = "\tServerName $domain";
    $lines[] = "\tErrorLog \"logs/$domain-error.log\"";
    $lines[] = "\tCustomLog \"logs/$domain-access.log\" common";
    $lines[] = "\tAlias /phpinfo \${WAP_SERVER}/Apache-2.4-win64/htdocs/phpinfo.php";
    if($folder) {
        $lines[] = "\tDocumentRoot $folder";
        $lines[] = "\t<Directory \"$folder\">";
        $lines[] = "\t\tOptions +Indexes +FollowSymLinks";
        $lines[] = "\t\tAllowOverride All";
        $lines[] = "\t\tRequire all granted";
        $lines[] = "\t</Directory>";
    }

    if($php && $php != 'php83') {
        if(isset($php_folders[$php])) {
            $lines[] = "\t<FilesMatch \.php$>";
            $lines[] = "\t\tAddHandler fcgid-script .php";
            $lines[] = "\t</FilesMatch>";
            $lines[] = "\tFcgidWrapper \"\${WAP_SERVER}/".$php_folders[$php]."/php-cgi.exe\" .php";
            $lines[] = "\tFcgidInitialEnv PHPRC \"\${WAP_SERVER}/".$php_folders[$php]."\"";
            $lines[] = "\tOptions +ExecCGI";
        }else{
            print('PHP Configuration \''.$php.'\' not found. Using PHP 8.3');
        }
    }

    $lines[] = "</VirtualHost>";

    if(file_exists(__DIR__.'/Apache-2.4-win64/conf/configs/httpd-ssl.conf')){
        $lines[] = "<VirtualHost *:443>";
        $lines[] = "\tServerName $domain";
        $lines[] = "\tErrorLog \"logs/$domain-error.log\"";
        $lines[] = "\tCustomLog \"logs/$domain-access.log\" common";
        $lines[] = "\tAlias /phpinfo \${WAP_SERVER}/Apache-2.4-win64/htdocs/phpinfo.php";
        $lines[] = "\tSSLEngine on";
        $lines[] = "\tSSLCertificateFile \"\${WAP_SERVER}/certs/$domain.pem\"";
        $lines[] = "\tSSLCertificateKeyFile \"\${WAP_SERVER}/certs/$domain-key.pem\"";
        if($folder) {
            $lines[] = "\tDocumentRoot $folder";
            $lines[] = "\t<Directory \"$folder\">";
            $lines[] = "\t\tOptions +Indexes +FollowSymLinks";
            $lines[] = "\t\tAllowOverride All";
            $lines[] = "\t\tRequire all granted";
            $lines[] = "\t</Directory>";
        }
        if($php && $php != 'php83') {
            if(isset($php_folders[$php])) {
                $lines[] = "\t<FilesMatch \.php$>";
                $lines[] = "\t\tAddHandler fcgid-script .php";
                $lines[] = "\t</FilesMatch>";
                $lines[] = "\tFcgidWrapper \"\${WAP_SERVER}/".$php_folders[$php]."/php-cgi.exe\" .php";
                $lines[] = "\tOptions +ExecCGI";
            }else{
                print('PHP Configuration \''.$php.'\' not found. Using PHP 8.2');
            }
        }
        $lines[] = "</VirtualHost>";
    }
    return implode("\n",$lines);
}

function restart_apache()
{
    shell_exec(__DIR__.'\Apache-2.4-win64\bin\httpd.exe -k restart');
}

function read_domains()
{
    global $php_folders, $conf_folder, $hosts_lines;
    $dominios = [];

    foreach($hosts_lines as $number => $line){
        if(preg_match("/127.0.0.1\t([\w.-]+)$/", $line, $matches)){
            if(!file_exists($conf_folder.'/'.$matches[1].'.conf')){
                print('Domain {'.$matches[1].'} from hosts file is not configured in web server'."\n");
            }else{
                $dominios[$matches[1]] = [
                    'domain' => $matches[1],
                    'php' => 'php83',
                    'folder' => '',
                ];
            }
        }
    }

    $domain_max_length = 0;
    $folder_max_length = 0;
    foreach($dominios as $dominio => $valor){
        $domain_max_length = max($domain_max_length, strlen($dominio));
        $conf_file = $conf_folder.'/'.$dominio.'.conf';
        $lineasvh = file($conf_file, FILE_IGNORE_NEW_LINES);
        $host_validated = false;
        foreach($lineasvh as $number => $line){
            if(preg_match("/ServerName (.*)/", $line, $matches)){
                if($matches[1] != $dominio){
                    print('Domain {'.$dominio.'} has a different ServerName {'.$matches[1].'} in file {'.$conf_file.'}'."\n");
                }
                $host_validated = true;
            }
            if(preg_match("/DocumentRoot (.*)$/", $line, $matches)){
                $dominios[$dominio]['folder'] = $matches[1];
                $folder_max_length = max($folder_max_length, strlen($matches[1]));
            }
            if(preg_match("/{WAP_SERVER}\/(.*)\/php-cgi.exe/", $line, $matches)){
                $dominios[$dominio]['php'] = array_search($matches[1], $php_folders);
            }
        }
    }

    ksort($dominios);

    return [$dominios, $domain_max_length, $folder_max_length];
}

if(count($argv) < 2){
    show_usage();
}else if($argv[1]=='show' and (count($argv)==4 || count($argv)==5)){
    $domain = $argv[2];
    $folder = $argv[3];
    $php = count($argv)==5 ? $argv[4] : 'php83';
    print(generate_conf($domain, $folder, $php));
}else if($argv[1]=='add'  and (count($argv)==4 || count($argv)==5)){
    $domain = $argv[2];
    $folder = $argv[3];
    $php = count($argv)==5 ? $argv[4] : 'php83';
    $conf_file = $conf_folder.'/'.$domain.'.conf';
    file_put_contents($conf_file, generate_conf($domain, $folder, $php));

    if(file_exists(__DIR__.'/Apache-2.4-win64/conf/configs/httpd-ssl.conf')) {
        putenv('CAROOT='.realpath(__DIR__ . '\certs'));
        shell_exec(__DIR__ . '\certs\mkcert-v1.4.4-windows-amd64.exe -cert-file certs\\'.$domain.'.pem -key-file certs\\'.$domain.'-key.pem '.$domain);
    }

    $found = false;
    foreach($hosts_lines as $number => $line){
        if(preg_match("/127.0.0.1\t$domain/", $line)){
            $found = true;
            break;
        }
    }
    if(!$found){
        $hosts_lines[] = "127.0.0.1\t$domain";
        file_put_contents($file, implode("\r\n", $hosts_lines));
    }
    restart_apache();
    $port = getenv('WAP_PORT');
    print('New host active in http://'.$domain.($port != 80 ? ':'.$port : ''));
}else if($argv[1]=='remove' and count($argv)==3){
    $domain = $argv[2];
    $conf_file = $conf_folder.'/'.$domain.'.conf';
    if(file_exists($conf_file) && unlink($conf_file)){
        print("File '".$conf_file."' has been deleted.\n");
    }
    if(file_exists(__DIR__.'/Apache-2.4-win64/conf/configs/httpd-ssl.conf')) {
        $certs_folder = __DIR__.'\certs';
        foreach ([
                     $certs_folder.DIRECTORY_SEPARATOR.$domain.'.pem',
                     $certs_folder.DIRECTORY_SEPARATOR.$domain.'-key.pem'
                 ] as $cert_file){
            if(file_exists($cert_file) && unlink($cert_file)){
                print("File '".$cert_file."' has been deleted.\n");
            }
        }
    }

    foreach($hosts_lines as $number => $line){
        if(preg_match("/127.0.0.1\t$domain/", $line)){
            unset($hosts_lines[$number]);
        }
    }
    file_put_contents($file, implode("\r\n", $hosts_lines));
    restart_apache();
}else if($argv[1]=='fix'){
    $dominios = [];
    //recorro dominios que tienen archivos de configuración
    foreach (glob($conf_folder."/*.conf") as $filename) {
        $domain = pathinfo(basename($filename), PATHINFO_FILENAME);
        if($domain != 'httpd-ssl'){
            $dominios[] = $domain;
        }
    }

    //quito los que ya están en hosts
    $save_hosts = false;
    foreach($hosts_lines as $number => $line){
        if(preg_match("/127.0.0.1\t([\w.-]+)$/", $line, $matches)){
            if (($key = array_search($matches[1], $dominios)) !== false) {
                unset($dominios[$key]);
            }elseif(!file_exists($conf_folder.'/'.$matches[1].'.conf')){
                print('Host removed \''.$matches[1].'\''."\n");
                unset($hosts_lines[$number]);
                $save_hosts = true;
            }
        }
    }

    //añado a hosts los que faltan
    if(count($dominios) || $save_hosts){
        foreach ($dominios as $domain){
            print('Host added \''.$domain.'\'')."\n";
            $hosts_lines[] = "127.0.0.1\t$domain";
        }
        file_put_contents($file, implode("\r\n", $hosts_lines));
        print('Hosts file has been updated!')."\n";
        restart_apache();
    }else{
        print('All domains are in hosts file. Nothing to do!')."\n";
    }
}else if($argv[1]=='regenerate'){

    list($dominios, $domain_max_length, $folder_max_length) = read_domains();
    foreach ($dominios as $domain){
        $conf_file = $conf_folder.'/'.$domain['domain'].'.conf';
        file_put_contents($conf_file, generate_conf($domain['domain'], $domain['folder'], $domain['php']));
    }

    restart_apache();

}else if($argv[1]=='list'){

    list($dominios, $domain_max_length, $folder_max_length) = read_domains();

    $port = getenv('WAP_PORT');
    if($port != 80){
        $domain_max_length += strlen($port) + 1; //puerto y los dos puntos
    }
    $domain_max_length += 7; // 'http://'
    print(str_pad('',$domain_max_length + $folder_max_length + 9, '-').PHP_EOL);
    foreach ($dominios as $dominio){
        $url = 'http://'.$dominio['domain'];
        if($port!= 80){
            $url .= ':'.$port;
        }
        print (str_pad($url,$domain_max_length).'  '.$dominio['php'].'  '.$dominio['folder'].PHP_EOL);
    }
}else{
    show_usage();
}

