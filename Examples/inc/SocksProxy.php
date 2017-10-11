<?php
/************
@WeCanCo
@WeCanGP
WeCan-Co.ir
*************/

class SocksProxy implements \danog\MadelineProto\Proxy
{
    private $domain;
    private $type;
    private $protocol;
    private $extra;
    private $sock;
    public function __construct(int $domain, int $type, int $protocol) {
        if (!in_array($domain, [AF_INET, AF_INET6])) {
            throw new \danog\MadelineProto\Exception('Wrong protocol family provided');
        }
        if (!in_array($type, [SOCK_STREAM, SOCK_DGRAM])) {
            throw new \danog\MadelineProto\Exception('Wrong connection type provided');
        }
        if (!in_array($protocol, [getprotobyname('tcp'), getprotobyname('udp')])) {
            throw new \danog\MadelineProto\Exception('Wrong protocol provided');
        }
        $this->domain = $domain;
        $this->type = $type;
        $this->protocol = $protocol;
    }
    public function setExtra(array $extra) {
        $this->extra = $extra;
        $this->sock = new \Socket(strlen(inet_pton($this->extra['address'])) === 4 ? \AF_INET : \AF_INET6, \SOCK_STREAM, getprotobyname('tcp'));
    }
    public function setOption(int $level, int $name, $value) {
        return $this->sock->setOption($level, $name, $value);
    }

    public function getOption(int $level, int $name) {
        return $this->sock->getOption($level, $name);
    }

    public function setBlocking(bool $blocking) {
        return $this->sock->setBlocking($blocking);
    }

    public function bind(string $address, int $port = 0) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }

    public function listen(int $backlog = 0) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }
    public function accept() {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }


    public function select(array &$read, array &$write, array &$except, int $tv_sec, int $tv_usec = 0) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }
    public function connect(string $address, int $port = 0) {
        $this->sock->connect($this->extra['address'], $this->extra['port']);
        $this->sock->write(pack("C3", 0x05, 0x01, 0x00));
        if ($this->socks5read(2) !== pack("C2", 0x05, 0x00)) {
            throw new \danog\MadelineProto\Exception('Wrong socks5 init reply');
        }
        $payload = pack("C3", 0x05 , 0x01 , 0x00);
        try {
            $ip = inet_pton($address);
            $payload .= pack("C1", strlen($ip) === 4 ? 0x01 : 0x04).$ip;
        } catch (\danog\MadelineProto\Exception $e) {
            $payload .= pack("C2", 0x03, strlen($address)).$address;
        }
        $payload .= pack("n", $port);
        $this->sock->write($payload);
        if (($res = $this->socks5read(2)) !== pack("C2", 0x05, 0x00)) {
            throw new \danog\MadelineProto\Exception('A SOCKS error occurred: '.ord($res[1]));
        }
        if ($this->socks5read(1) !== chr(0)) {
            throw new \danog\MadelineProto\Exception('Wrong socks5 final RSV');
        }
        switch (ord($this->socks5read(1))) {
            case 1:
                $ip = inet_ntop($this->socks5read(4));
                break;
            case 4:
                $ip = inet_ntop($this->socks5read(16));
                break;
            case 3:
                $ip = $this->socks5read(ord($this->socks5read(1)));
                break;
        }
        $port = unpack("n", $this->socks5read(2))[1];
        \danog\MadelineProto\Logger::log(['Connected to '.$ip.':'.$port.' via socks5']);
        return true;
    }
    private function socks5read(int $length) {
        $packet = '';
        while (strlen($packet) < $length) {
            $packet .= $this->sock->read($length - strlen($packet));
            if ($packet === false || strlen($packet) === 0) {
                throw new \danog\MadelineProto\NothingInTheSocketException(\danog\MadelineProto\Lang::$current_lang['nothing_in_socket']);
            }
        }
        return $packet;
    }
    public function read(int $length, int $flags = 0) {
        return $this->sock->read($length, $flags);
    }

    public function write(string $buffer, int $length = -1) {
        return $this->sock->write($buffer, $length);
    }

    public function send(string $data, int $length, int $flags) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }

    public function close() {
        $this->sock->close();
    }

    public function getPeerName(bool $port = true) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }

    public function getSockName(bool $port = true) {
        throw new \danog\MadelineProto\Exception('Not Implemented');
    }
}
