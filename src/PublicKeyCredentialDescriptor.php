<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Webauthn;

use Assert\Assertion;
use JsonSerializable;
use function Safe\base64_decode;
use function Safe\json_decode;

class PublicKeyCredentialDescriptor implements JsonSerializable
{
    public const CREDENTIAL_TYPE_PUBLIC_KEY = 'public-key';

    public const AUTHENTICATOR_TRANSPORT_USB = 'usb';
    public const AUTHENTICATOR_TRANSPORT_NFC = 'nfc';
    public const AUTHENTICATOR_TRANSPORT_BLE = 'ble';
    public const AUTHENTICATOR_TRANSPORT_INTERNAL = 'internal';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string[]
     */
    protected $transports;

    /**
     * @param string[] $transports
     */
    public function __construct(string $type, string $id, array $transports = [])
    {
        $this->type = $type;
        $this->id = $id;
        $this->transports = $transports;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getTransports(): array
    {
        return $this->transports;
    }

    /**
     * @deprecated will be removed in v2.0. Use "createFromArray" instead
     */
    public static function createFromJson(array $json): self
    {
        return self::createFromArray($json);
    }

    public static function createFromString(string $data): self
    {
        $data = json_decode($data, true);
        Assertion::isArray($data, 'Invalid data');

        return self::createFromArray($data);
    }

    public static function createFromArray(array $json): self
    {
        Assertion::keyExists($json, 'type', 'Invalid input. "type" is missing.');
        Assertion::keyExists($json, 'id', 'Invalid input. "id" is missing.');

        return new self(
            $json['type'],
            base64_decode($json['id'], true),
            $json['transports'] ?? []
        );
    }

    public function jsonSerialize(): array
    {
        $json = [
            'type' => $this->type,
            'id' => base64_encode($this->id),
        ];
        if (0 !== \count($this->transports)) {
            $json['transports'] = $this->transports;
        }

        return $json;
    }
}
