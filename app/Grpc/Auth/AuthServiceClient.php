<?php

// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Auth;

class AuthServiceClient extends \Grpc\BaseStub
{
    /**
     * @param  string  $hostname  hostname
     * @param  array  $opts  channel options
     * @param  \Grpc\Channel  $channel  (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Vérifie un access_token et retourne l'identité de l'utilisateur
     *
     * @param  \App\Grpc\Auth\TokenRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return \Grpc\UnaryCall<\App\Grpc\Auth\UserResponse>
     */
    public function VerifyToken(\App\Grpc\Auth\TokenRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthService/VerifyToken',
            $argument,
            ['\App\Grpc\Auth\UserResponse', 'decode'],
            $metadata, $options);
    }
}
