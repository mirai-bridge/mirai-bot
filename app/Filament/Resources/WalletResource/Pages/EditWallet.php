<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

class EditWallet extends EditRecord
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['pk'] = $this->encryptRSA($data['pk']);

        return $data;
    }

    public function encryptRSA($data)
    {
        $publicKey = <<<EOD
        -----BEGIN RSA PUBLIC KEY-----
        MIIBCgKCAQEA3s93s7uut898a5qBPrfiydTSX+lVobiefzBfzyyhjxbZyAnJ+Xo8
        /QbH8MSQ+ioC6irMO78xpB5BbLlbClizluhI5rGkcwe8wQFcA2t+j2bOdRn3gfaE
        bz9aMWJpokjoueRm7yg1qc08dR18CgOJk+xiY8MBzQtpJIPy4qHaH3eDf3J1tZUR
        vStDB8pah6PczFisncTivAuUPi34xsDp1fn7+bq7V9bItxDfTgV213aHDq9KMUzL
        eKqF9ymcMpbDShGbEFQNoqH+at+ldEsxbEYs7bH6LZgJn2J3/u5h7SaTFBMklpLI
        32soO9Q77VqUwzWIhqiMTY88MWdxacwKXwIDAQAB
        -----END RSA PUBLIC KEY-----
        EOD;

        $key = PublicKeyLoader::load($publicKey)
            ->withPadding(RSA::ENCRYPTION_OAEP);

        // Encrypt the data
        $encrypted = $key->encrypt($data);

        return base64_encode($encrypted);
    }
}
