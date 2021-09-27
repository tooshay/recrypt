<?php

use Illuminate\Encryption\Encrypter;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('Retrieves and decrypts single value', function () {
    $symmetricalKey = Str::random(16);
    $encrypter = new Encrypter($symmetricalKey);
    $value = 'test';

    \App\Models\Data::create([
        'id' => 1,
        'value' => $encrypter->encrypt($value)
    ]);

    $result = $this->json('GET', '/api/data/1', [
        'decryption_key' => $symmetricalKey
    ]);

    expect($result->getStatusCode())->toBe(Symfony\Component\HttpFoundation\Response::HTTP_OK);
});

it('Retrieves and decrypts wildcard values', function () {
    $symmetricalKey = Str::random(16);
    $encrypter = new Encrypter($symmetricalKey);
    $values = collect(['test', 'test1', 'test2']);

    $values->each(function ($value) use ($encrypter) {
        \App\Models\Data::create([
            'id' => 'key-' . Str::random(10),
            'value' => $encrypter->encrypt($value)
        ]);
    });

    $result = $this->json('GET', '/api/data/key-*', [
        'decryption_key' => $symmetricalKey
    ]);

    expect($result->getStatusCode())->toBe(Symfony\Component\HttpFoundation\Response::HTTP_OK);
    expect($result->json())->toBe($values->toArray());
});
