<?php

namespace App\Services;

use App\Models\MicrosoftToken;
use Carbon\Carbon;
use League\OAuth2\Client\Provider\GenericProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MicrosoftMailer
{
    protected function provider()
    {
        return new GenericProvider([
            'clientId'                => env('AZURE_CLIENT_ID'),
            'clientSecret'            => env('AZURE_CLIENT_SECRET'),
            'redirectUri'             => env('AZURE_REDIRECT_URI'),
            'urlAuthorize'            => 'https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes'                  => 'offline_access https://outlook.office365.com/SMTP.Send'
        ]);
    }

    protected function refreshAccessToken($organizationId = null)
    {
        $provider = $this->provider();
        $record = $this->tokenRecord($organizationId);

        if (!$record) {
            return null;
        }

        try {
            $newToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $record->refresh_token
            ]);

            $record->update([
                'access_token'  => $newToken->getToken(),
                'refresh_token' => $newToken->getRefreshToken() ?? $record->refresh_token,
                'expires_in'    => $newToken->getExpires(),
                'expires_at'    => Carbon::createFromTimestamp($newToken->getExpires())
            ]);

            return $record->access_token;
        } catch (\Exception $e) {
            \Log::error("❌ Microsoft OAuth2 refresh failed: " . $e->getMessage());
            return null;
        }
    }

    protected function tokenRecord($organizationId = null)
    {
        if ($organizationId) {
            return MicrosoftToken::where('organization_id', $organizationId)->first();
        }

        return MicrosoftToken::first();
    }

    public function getValidToken($organizationId = null)
    {
        $record = $this->tokenRecord($organizationId);

        if (!$record) {
            return null;
        }

        if (now()->greaterThan($record->expires_at)) {
            $newToken = $this->refreshAccessToken($organizationId);
            if ($newToken) {
                return $newToken;
            }
            return null;
        }

        return $record->access_token;
    }

    public function getTransport($organizationId = null, $username = null)
    {
        $token = $this->getValidToken($organizationId);

        if (!$token) {
            throw new \Exception("No valid Microsoft OAuth2 token available. Please re-authenticate.");
        }

        $transport = new EsmtpTransport('smtp.office365.com', 587, false);
        $transport->setUsername($username ?: env('MAIL_USERNAME'));
        $transport->setPassword($token);

        return $transport;
    }
}
