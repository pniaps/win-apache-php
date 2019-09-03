# win-apache-php

This is my local apache server with different php versions.

 - Apache/2.4.25 (Win64)
 - PHP Version 5.3.29
 - PHP Version 5.4.45
 - PHP Version 5.5.38
 - PHP Version 5.6.40
 - PHP Version 7.0.33
 - PHP Version 7.1.32
 - PHP Version 7.2.22
 - PHP Version 7.3.9 (default)

### Installation

You have to define two environment variables for the `ServerRoot` and `DocumentRoot` (for example):  
```
WAP_SERVER => D:/win-apache-php  
WAP_DOCUMENT_ROOT => D:/web
```

Apache requires [Microsoft Visual C++ Redistributable for Visual Studio 2017](https://go.microsoft.com/fwlink/?LinkId=746572). You may need to install it.

Each version of php is used in a virtualhost:
 - virtualhost `php53` uses PHP Version 5.3.29
 - virtualhost `php54` uses PHP Version 5.4.45
 - virtualhost `php55` uses PHP Version 5.5.38
 - virtualhost `php56` uses PHP Version 5.6.40
 - virtualhost `php70` uses PHP Version 7.0.33
 - virtualhost `php71` uses PHP Version 7.1.32
 - virtualhost `php72` uses PHP Version 7.2.22
 - virtualhost `php73` uses PHP Version 7.3.9
 - All other hosts uses PHP Version 7.3.9
 
You can add those hosts in the following file `c:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1 php53 php54 php55 php56 php70 php71 php72 php73
```
Or executing the folling command as administrator
```
(echo. & echo 127.0.0.1 php53 php54 php55 php56 php70 php71 php72 php73) >> C:\Windows\System32\drivers\etc\hosts
```

Add folder `%WAP_SERVER%\php-7.3-Win32-VC15-x64` to path if you want to execute `php` or `composer` from the command line.

Finally, install apache as service (run as administrator)
```
%WAP_SERVER%\Apache-2.4-win64\bin\httpd.exe -k install
```

You can install it automatically executing `configure.bat` as administrator. It will create the folder `web` next to `win-apache-php` if does not exists.

Once installed and service started, you can test the `phpinfo()` for each version to check all is working correctly (phpinfo is only accesible from each php host and localhost).
- http://php53/phpinfo
- http://php54/phpinfo
- http://php55/phpinfo
- http://php56/phpinfo
- http://php70/phpinfo
- http://php71/phpinfo
- http://php72/phpinfo
- http://php73/phpinfo
- http://localhost/phpinfo

### ImageMagick (older, not tested)

ImageMagick is configured in PHP, but you have to install on windows.

1. Download and install version 6.9.3-7 x64 from  `http://ftp.icm.edu.pl/packages/ImageMagick/binaries/ImageMagick-6.9.3-7-Q16-x64-dll.exe`, on last step check "Add application directory to your system path"
2. Copy all files from `C:\Program Files\ImageMagick-6.9.3-Q16\modules\coders` and `C:\Program Files\ImageMagick-6.9.3-Q16\modules\filters` to `C:\Program Files\ImageMagick-6.9.3-Q16` to load ImageMagick supported formats
3. Go to Control Panel -> System -> Advanced Settings -> Environment Variables -> New System Variable -> MAGICK_HOME = C:\Program Files\ImageMagick-6.9.3-Q16
4. Restart apache service
