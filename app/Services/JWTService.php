<?php

namespace App\Services;

use Base32\Base32;
use Closure;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\Log;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256 as ES256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Ramsey\Uuid\Uuid;

class JWTService
{
    private Configuration $config;
    private string $kid;
    private InMemory $publicKey;
    private InMemory $privateKey;
    private InMemory $certificate;

    public function __construct()
    {
        $signer = new ES256();

        $this->privateKey = InMemory::file(env('JWT_PRIVATE_KEY'));
        $this->publicKey = InMemory::file(env('JWT_PUBLIC_KEY'));
        $this->certificate = InMemory::file(env('JWT_CERTIFICATE'));

        $this->kid = self::generateKeyId($this->privateKey->contents);

        $this->config = Configuration::forAsymmetricSigner(
            $signer,
            $this->privateKey,
            $this->publicKey
        );
    }

    private static function generateKeyId($privateKeyContent): string
    {
        // Extract public key from private key
        $privateKey = openssl_pkey_get_private($privateKeyContent);
        $publicKeyContent = openssl_pkey_get_details($privateKey)['key'];

        // Clean pem header and footer
        $pattern = '/-----BEGIN [^-]*-----\r?\n?|-----END [^-]*-----\r?\n?/';
        $cleanedPem = trim(preg_replace($pattern, '', $publicKeyContent));

        // Convert to der
        $der = base64_decode(preg_replace('/\s+/', '', $cleanedPem));

        // Calculate digest
        $algorithm = hash_init('sha256');
        hash_update($algorithm, $der);
        $digest = hash_final($algorithm, true);

        // Shorten digest to 30 bytes
        $digest = substr($digest, 0, 30);

        // Use Base32\Base32 to encode digest
        $source = Base32::encode($digest);
        $source = str_replace('=', '', $source);

        // Format with :
        $result = [];
        for ($i = 0; $i < strlen($source); $i += 4) {
            $result[] = substr($source, $i, 4);
        }

        return implode(':', $result);
    }

    /**
     * @param Closure $closure
     * @return UnencryptedToken
     * @throws Exception
     */
    public function createToken(Closure $closure): UnencryptedToken
    {
        $builder = $this->config->builder();

        $builder = $builder
            ->issuedAt(new DateTimeImmutable(now()))
            ->canOnlyBeUsedAfter(new DateTimeImmutable(now()))
            ->expiresAt(new DateTimeImmutable(now()->addMinutes(5)))
            ->identifiedBy(Uuid::uuid4()->toString())
            ->withHeader('kid', $this->kid);

        return $closure($builder)->getToken($this->config->signer(), $this->config->signingKey());
    }

    /**
     * @param Closure $closure
     * @return UnencryptedToken
     * @throws Exception
     */
    public function generalToken(Closure $closure): UnencryptedToken
    {
        $builder = $this->config->builder();

        $builder = $builder
            ->issuedAt(new DateTimeImmutable(now()))
            ->canOnlyBeUsedAfter(new DateTimeImmutable(now()))
            ->expiresAt(new DateTimeImmutable(now()->addYear()))
            ->identifiedBy(Uuid::uuid4()->toString());

        return $closure($builder)->getToken($this->config->signer(), $this->config->signingKey());
    }

    public function validateToken(string $token, Closure $closure): bool
    {
        // Parse token
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($token);

        // Validate token
        $validator = new Validator();

        try {
            $closure($validator, $token);
            $validator->assert($token, new SignedWith($this->config->signer(), $this->config->verificationKey()));
            $validator->assert($token, new LooseValidAt(new FrozenClock(now()->toDateTimeImmutable())));

            return true;
        } catch (RequiredConstraintsViolated $e) {
            foreach ($e->violations() as $v) {
                Log::channel('stderr')->info($v);
            }
            return false;
        }
    }
}
