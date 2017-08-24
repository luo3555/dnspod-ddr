<?php
/**
 * 
 * @authors Daniel Luo (luo3555@qq.com)
 * @date    2017-08-24 13:37:44
 * @version $Id$
 */
define('DS', DIRECTORY_SEPARATOR);

class DNSpod
{
    const API_ADDRESS = 'https://api.dnspod.com/';

    const FORMAT_JSON = 'json';

    protected $_data = [];

    protected $_response = null;

    public function __call($name, $arguments)
    {
        $prefix = strtolower(substr($name, 0, 3));
        $key = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", substr($name, 3)));

        switch ($prefix) {
            case 'set':
                return $this->setData($key, (isset($arguments[0]) ? $arguments[0] : null));
            break;
            case 'get':
                return $this->getData($key, (isset($arguments[0]) ? $arguments[0] : null));
            break;
            default:
//                throw new Exception(sprintf('function %s not exist!', $key));
                break;
        }
    }

    public function getData($key=null, $defaultValue=null)
    {
        if (is_null($key)) {
            $defaultValue = $this->_data;
        } else {
            if ($this->hasData($key)) {
                $defaultValue = $this->_data[$key];
            }
        }
        return $defaultValue;
    }

    public function hasData($key)
    {
        return isset($this->_data[$key]);
    }

    public function setData($key, $value=null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_data[$k] = $v;
            }
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function getUrl($uri=null)
    {
        return is_null($uri) ? self::API_ADDRESS : self::API_ADDRESS . $uri;
    }

    public function log($data)
    {
        $data = PHP_EOL . date('Y-m-d H:i:s') . PHP_EOL . $data;
        $file = dirname(dirname(__FILE__)) . DS . 'log' . DS . date('Y-m-d') . '.log';
        $fp = fopen($file, 'a+');
        fwrite($fp, $data);
        fclose($fp);
    }

    public function invoke($url, $params)
    {
        try {
            // set default format
            $params['format'] = self::FORMAT_JSON;
            $response = Requests::post($url, $this->getHeaders(), $params);

            $body = json_decode($response->body);
            if ($body->status->code == 1) {
                $this->_response = $body;
            } else {
                // show error message
                print_r(json_decode($response->body));
                $this->log(
                    'Url:' . $url . PHP_EOL .
                    'Request:' . json_encode($params) . PHP_EOL .
                    'Response:' . $response->body
                );
            }
        } catch (Exception $e) {
            $this->log(
                'Url:' . $url . PHP_EOL .
                'Request:' . json_encode($params) . PHP_EOL .
                'Response:' . $response->body . PHP_EOL .
                'Error Msg: ' . $e->getMessage()
            );
        }
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get Auth
     * @return mixed
     */
    public function getAuth()
    {
        return $this->invoke(
            $this->getUrl('Auth'),
            [
                'login_email' => $this->getLoginEmail(),
                'login_password' => $this->getLoginPassword()
            ]
        );
    }

    public function getAccessToken()
    {
        $this->getAuth();
        return isset($this->getResponse()->user_token) ? $this->getResponse()->user_token : '';
    }

    public function getDomainList()
    {
        return $this->invoke(
            $this->getUrl('Domain.List'),
            [
                'user_token' => $this->getUserToken()
            ]
        );
    }

    /**
     * Create record
     *
     * user_token your token
     * domain_id  domain id
     * sub_domain eg: www
     * record_type default is A
     * record_line default is default
     * value your IP address or other value
     *
     * @return DNSpod
     */
    public function createRecord()
    {
        return $this->invoke(
            $this->getUrl('Record.Create'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'sub_domain' => $this->getSubDomain(),
                'record_type' => $this->getRecordType('A'),
                'record_line' => $this->getRecordLine('default'),
                'value'      => $this->getValue(),
            ]
        );
    }

    public function getRecordList()
    {
        return $this->invoke(
            $this->getUrl('Record.List'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId()
            ]
        );
    }

    public function updateRecord()
    {
        return $this->invoke(
            $this->getUrl('Record.Modify'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId(),
                'sub_domain' => $this->getSubDomain(),
                'value'      => $this->getValue(),
                'record_type' => $this->getRecordType('A'),
                'record_line' => $this->getRecordLine('default'),

            ]
        );
    }

    public function updateDynamicDnsRecord()
    {
        return $this->invoke(
            $this->getUrl('Record.Ddns'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId(),
                'sub_domain' => $this->getSubDomain(),
                'record_line' => $this->getRecordLine('default'),
                'value'      => $this->getValue()
            ]
        );
    }

    public function removeReocrd()
    {
        return $this->invoke(
            $this->getUrl('Record.Remark'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId()
            ]
        );
    }

    public function markRecord()
    {
        return $this->invoke(
            $this->getUrl('Record.Remark'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId(),
                'remark'      => $this->getRemark()
            ]
        );
    }

    public function getRecordInfo()
    {
        return $this->invoke(
            $this->getUrl('Record.Info'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId()
            ]
        );
    }

    /**
     * @param status enable|disable
     */
    public function setRecordStatus()
    {
        return $this->invoke(
            $this->getUrl('Record.Info'),
            [
                'user_token' => $this->getUserToken(),
                'domain_id'  => $this->getDomainId(),
                'record_id'  => $this->getRecordId(),
                'status'     => $this->getStatus('enable')
            ]
        );
    }
}