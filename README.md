# win-apache-php

This is my local apache server with different php versions.

 - Apache/2.4.25 (Win64)
 - PHP Version 5.3.29
 - PHP Version 5.4.45
 - PHP Version 5.5.38
 - PHP Version 5.6.30
 - PHP Version 7.0.16 (default)
 - PHP Version 7.1.2

### Installation

This repository has to be cloned in the folder `d:\servidor` and `DocumentRoot` is `d:\web`

Each version of php is used in a virtualhost:
 - virtualhost `php53` uses PHP Version 5.3.29
 - virtualhost `php54` uses PHP Version 5.4.45
 - virtualhost `php55` uses PHP Version 5.5.38
 - virtualhost `php56` uses PHP Version 5.6.30
 - virtualhost `php70` uses PHP Version 7.0.16
 - virtualhost `php71` uses PHP Version 7.1.2
 - All other hosts uses PHP Version 7.0.16
 
You can add those hosts in the following file `c:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1 php53 php54 php55 php56 php70 php71
```

Finally, install apache as service (run as administrator)
```
d:\servidor\Apache-2.4-win64\bin\httpd.exe -k install
```