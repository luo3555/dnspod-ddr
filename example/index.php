<?php
/**
 * Enter file
 * @authors Daniel Luo (luo3555@qq.com)
 * @date    2017-08-24 13:35:30
 * @version $Id$
 */

require '../vendor/autoload.php';
require '../etc/config.php';
require '../lib/Ip.php';
require '../lib/Dnspod.php';

// get current ip
$ip = new Ip();

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

// Example 2
// Get Domain List
$dnspod->setUserToken(TOKEN);
$dnspod->getDomainList();
print_r($dnspod->getResponse());

// Example 3
// Create new Subdomain
$data = [
    'user_token' => TOKEN,  // Example 1 can get this value
    'domain_id'  => 2411248,  // Your domain id, Example 2 can get this value
    'sub_domain' => 'mysubdomain',
    'value'      => '127.0.0.1',
];
$dnspod->setData($data);
$dnspod->createRecord();
print_r($dnspod->getResponse());


// Example 4
// Get Subdomain list
$data = [
    'user_token' => TOKEN,  // Example 1 can get this value
    'domain_id'  => 2411248,  // Your domain id, Example 2 can get this value
];
$dnspod->setData($data);
$dnspod->getRecordList();
print_r($dnspod->getResponse());

// Example 5
// Update Subdomain
$data = [
    'user_token' => TOKEN,      // Example 1 can get this value
    'domain_id'  => 2411248,    // Your domain id, Example 2 can get this value
    'record_id'  => 15166745,   // subdomain id, Example 4 can get this value
    'sub_domain' => 'mysubdomain-update', // Your subdomain
    'value'      => '127.0.0.2' // Your IP address
];
$dnspod->setData($data);
$dnspod->updateRecord();
print_r($dnspod->getResponse());

// Example 6
// update Dynamic DNS Record
$data = [
    'user_token' => TOKEN,      // Example 1 can get this value
    'domain_id'  => 2411248,    // Your domain id, Example 2 can get this value
    'record_id'  => 15166745,   // subdomain id, Example 4 can get this value
    'sub_domain' => 'mysubdomain-update', // Your subdomain
    'value'      => $ip->getAddress() // Your IP address
];
$dnspod->setData($data);
$dnspod->updateDynamicDnsRecord();
print_r($dnspod->getResponse());

// removeReocrd, markRecord, getRecordInfo, setRecordStatus
// please see file lib/dnspod.php