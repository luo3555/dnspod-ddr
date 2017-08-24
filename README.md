##DNSPod 动态域名解析
[DNSPod](https://www.dnspod.com/)
[DNSPod API](https://www.dnspod.com/docs/index.html)


1. 获取DNSPod TOKEN

```php
$dnspod = new DNSpod();

// Set request header
$headers = [
    'UserAgent' => 'DDR/1.0(luo3555@qq.com)'
];
$dnspod->setHeaders($headers);

// Example 1
// Get Access Token
$data = [
    'login_email' => ACCOUNT,
    'login_password' => PASSWOR
];
$dnspod->setData($data);
$dnspod->getAccessToken();
print_r($dnspod->getResponse());
```

2. 动态修改IP

```php
$ip = new Ip();

$data = [
    'user_token' => TOKEN,      // Example 1 can get this value
    'domain_id'  => 2411248,    // Your domain id, Example 2 can get this value
    'record_id'  => 15166745,   // subdomain id, Example 4 can get this value
    'sub_domain' => 'mysubdomain-update', // Your subdomain
    'value'      => $ip->getAddress() // Your Current IP address
];
$dnspod->setData($data);
$dnspod->updateDynamicDnsRecord();
```

3. Cronjob 动态设置IP

```shell
*/5 * * * * php yourScript.php
```