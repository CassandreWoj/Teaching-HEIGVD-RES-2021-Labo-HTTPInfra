<?php
$ip_static1 = getenv('STATIC_APP1');
$ip_static2 = getenv('STATIC_APP2');
$ip_dynamic1 = getenv('DYNAMIC_APP1');
$ip_dynamic2 = getenv('DYNAMIC_APP2');
?>

<VirtualHost *:80>
	ServerName res.labo.ch

    # Set cookie containing the route
    Header add Set-Cookie "ROUTEID=.%{BALANCER_WORKER_ROUTE}e; path=/" env=BALANCER_ROUTE_CHANGED

    <Proxy balancer://dynamic-app>
        BalancerMember 'http://<?php echo "$ip_dynamic1"?>'
        BalancerMember 'http://<?php echo "$ip_dynamic2"?>'
    </Proxy>

    <Proxy balancer://static-app>
        BalancerMember 'http://<?php echo "$ip_static1"?>' route=1
        BalancerMember 'http://<?php echo "$ip_static2"?>' route=2
        ProxySet lbmethod=byrequests
        ProxySet stickysession=ROUTEID
    </Proxy>

    <Location "/balancer-manager">
        SetHandler balancer-manager
    </Location>

    ProxyPass '/balancer-manager' !

    ProxyPass '/api/addresses/' 'balancer://dynamic-app/'
    ProxyPassReverse '/api/addresses/' 'balancer://dynamic-app/'

	ProxyPass '/' 'balancer://static-app/'
	ProxyPassReverse '/' 'balancer://static-app/'

</VirtualHost>