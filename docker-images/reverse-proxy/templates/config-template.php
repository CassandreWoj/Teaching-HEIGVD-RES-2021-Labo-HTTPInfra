<?php
$ip_static = getenv('STATIC_APP');
$ip_dynamic = getenv('DYNAMIC_APP');
?>

<VirtualHost *:80>
	ServerName res.labo.ch
	ProxyPass '/api/addresses/' 'http://<?php echo "$ip_dynamic"?>/'
	ProxyPassReverse '/api/addresses/' 'http://<?php echo "$ip_dynamic"?>/'

	ProxyPass '/' 'http://<?php echo "$ip_static"?>/'
	ProxyPassReverse '/' 'http://<?php echo "$ip_static"?>'
</VirtualHost>