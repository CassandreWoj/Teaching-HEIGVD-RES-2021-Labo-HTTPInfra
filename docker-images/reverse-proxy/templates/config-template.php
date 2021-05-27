<?php
$ip_static1 = getenv('STATIC_APP1');
$ip_static2 = getenv('STATIC_APP2');
$ip_dynamic1 = getenv('DYNAMIC_APP1');
$ip_dynamic2 = getenv('DYNAMIC_APP2');
?>

<VirtualHost *:80>
	ServerName res.labo.ch

    <Proxy balancer://dynamic-app>
        BalancerMember 'http://<?php echo "$ip_dynamic1"?>'
        BalancerMember 'http://<?php echo "$ip_dynamic2"?>'
    </Proxy>

    <Proxy balancer://static-app>
        BalancerMember 'http://<?php echo "$ip_static1"?>'
        BalancerMember 'http://<?php echo "$ip_static2"?>'
    </Proxy>

    ProxyPass '/api/addresses/' 'balancer://dynamic-app/'
    ProxyPassReverse '/api/addresses/' 'balancer://dynamic-app/'

	ProxyPass '/' 'balancer://static-app/'
	ProxyPassReverse '/' 'balancer://static-app/'

</VirtualHost>