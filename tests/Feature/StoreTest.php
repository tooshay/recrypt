<?php

use Illuminate\Encryption\Encrypter;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('Refuses an incorrect key', function () {
    $data = [
        'id' => 1,
        'value' => 'test',
        'encryption_key' => 'keytooshort'
    ];

    $result = $this->json('POST', '/api/data', $data);

    expect($result->getStatusCode())->toBe(Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('Encrypts values', function () {
    $symmetricalKey = Str::random(16);
    $value = 'test value';

    $data = [
        'id' => 1,
        'value' => $value,
        'encryption_key' => $symmetricalKey
    ];

    $result = $this->post('/api/data', $data);
    $decrypter = new Encrypter($symmetricalKey);
    $data = \App\Models\Data::find(1);

    expect($result->getStatusCode())->toBe(Symfony\Component\HttpFoundation\Response::HTTP_OK);
    expect($decrypter->decrypt($data->value))->toBe($value);
});

it('Overrides existing value with new one', function () {
    $symmetricalKey = Str::random(16);
    $value = 'test value';

    $data = [
        'id' => 1,
        'value' => $value,
        'encryption_key' => $symmetricalKey
    ];

    $this->json('POST', '/api/data', $data);

    $newValue = 'new value';
    $data = [
        'id' => 1,
        'value' => $newValue,
        'encryption_key' => $symmetricalKey
    ];

    $this->json('POST', '/api/data', $data);

    $decrypter = new Encrypter($symmetricalKey);
    $data = \App\Models\Data::find(1);

    expect($decrypter->decrypt($data->value))->toBe($newValue);
});
