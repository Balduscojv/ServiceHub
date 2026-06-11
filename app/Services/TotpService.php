<?php

namespace App\Services;

class TotpService
{
    private const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD = 30;
    private const DIGITS = 6;
    private const WINDOW = 1;

    public function generateSecret(): string
    {
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= self::CHARS[random_int(0, 31)];
        }
        return $secret;
    }

    public function verify(string $secret, string $code): bool
    {
        $counter = (int) floor(time() / self::PERIOD);

        for ($offset = -self::WINDOW; $offset <= self::WINDOW; $offset++) {
            if ($this->computeCode($secret, $counter + $offset) === $code) {
                return true;
            }
        }

        return false;
    }

    public function getQrUri(string $email, string $secret, string $issuer = 'ServiceHub'): string
    {
        return 'otpauth://totp/'
            .rawurlencode($issuer).':'
            .rawurlencode($email)
            .'?secret='.$secret
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits=6&period=30';
    }

    private function computeCode(string $secret, int $counter): string
    {
        $secretBytes = $this->base32Decode($secret);
        $time = pack('J', $counter);
        $hash = hash_hmac('sha1', $time, $secretBytes, true);
        $offset = ord($hash[19]) & 0x0f;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff)
        ) % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $map = array_flip(str_split(self::CHARS));
        $input = strtoupper(rtrim($input, '='));
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;

        foreach (str_split($input) as $char) {
            if (! isset($map[$char])) {
                continue;
            }
            $buffer = ($buffer << 5) | $map[$char];
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }

        return $output;
    }
}
