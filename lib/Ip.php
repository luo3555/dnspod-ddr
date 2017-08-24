<?php

/**
 *
 * @authors Daniel Luo (luo3555@qq.com)
 * @date    2017-08-24 13:37:44
 * @version $Id$
 */
class Ip
{
    const IP_SERVER = 'http://1212.ip138.com/ic.asp';

    protected $ip;

    public function getAddress()
    {
        return $this->ip;
    }

    public function __construct()
    {
        $response = Requests::get(
            self::IP_SERVER,
            [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:55.0) Gecko/20100101 Firefox/55.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'zh-CN,en-US;q=0.8,zh;q=0.5,en;q=0.3',
                'Cache-Control' => 'max-age=0'
            ],
            [
                'useragent' => '',
                'timeout' => 100,
                'connect_timeout' => 100
            ]
        );

        preg_match('/(\[\d+\.\d+\.\d+\.\d+\])/', $response->body, $match);

        if (!empty($match)) {
            $this->ip = str_replace('[', '', $match[0]);
            $this->ip = str_replace(']', '', $this->ip);
        } else {
            $this->ip = null;
        }
    }
}