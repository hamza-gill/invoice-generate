<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MicrosoftToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MicrosoftController extends Controller
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

    /**
     * Step 1: Redirect user to Microsoft login
     */
    public function redirectToMicrosoft()
    {
        $provider = $this->provider();

        $authorizationUrl = $provider->getAuthorizationUrl();
        Session::put('oauth2state', $provider->getState());

        return redirect($authorizationUrl);
    }

    /**
     * Step 2: Handle Microsoft callback
     */
    public function handleCallback(Request $request)
    {
        $provider = $this->provider();

        if ($request->get('state') !== Session::get('oauth2state')) {
            Session::forget('oauth2state');
            return response()->json(['error' => 'Invalid OAuth state']);
        }

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            // Save token in database (replace existing)
            MicrosoftToken::truncate(); // keep only one record
            MicrosoftToken::create([
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_in'    => $accessToken->getExpires(),
                'expires_at'    => Carbon::createFromTimestamp($accessToken->getExpires())
            ]);

            return redirect('/send-test-email');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    protected function refreshAccessToken()
    {
        $provider = $this->provider();
        $record = MicrosoftToken::first();

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
            return null; // fallback to re-login
        }
    }
    /**
     * Step 3: Send email using stored token
     */
    public function sendTestEmail()
    {
        $record = MicrosoftToken::first();

        // If expired, refresh
        if (!$record || now()->greaterThan($record->expires_at)) {
            $newToken = $this->refreshAccessToken();
            if (!$newToken) {
                return redirect('/auth/redirect'); // if refresh fails
            }
            $token = $newToken;
        } else {
            $token = $record->access_token;
        }

        // Create Symfony transport with token as password
        $transport = new EsmtpTransport('smtp.office365.com', 587, false);
        $transport->setUsername(env('MAIL_USERNAME'));   // info@nycce.co
        $transport->setPassword($token);                 // OAuth2 access token

        // Create Symfony mailer instance
        $symfonyMailer = new SymfonyMailer($transport);

        // Send email
        Mail::mailer('smtp')
            ->setSymfonyTransport($transport)
            ->raw(
                'This is a test email via Microsoft OAuth2 SMTP with DB token & refresh',
                function ($message) {
                    $message->to('hamzagill415@gmail.com')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->subject('OAuth2 SMTP Test');
                }
            );

        return "✅ Test email sent successfully with OAuth2 refresh handling!";
    }
}
