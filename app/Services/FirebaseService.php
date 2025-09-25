<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/firebase/service-account.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->client->fetchAccessTokenWithAssertion();

        $this->accessToken = $this->client->getAccessToken()['access_token'];
    }

    public function sendNotification($deviceToken, $title, $body, $data = null)
    {
        try {
            $url = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';

            $message = [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];

            // Add data payload if provided
            if ($data) {
                $message['data'] = $data;
            }

            $payload = ['message' => $message];

            $response = Http::withToken($this->accessToken)
                ->timeout(30)
                ->post($url, $payload);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'message' => 'Notification sent successfully',
                    'firebase_response' => $response->json(),
                ];
            }

            // Handle specific FCM errors
            $responseData = $response->json();
            $errorCode = $responseData['error']['details'][0]['errorCode'] ?? 'UNKNOWN';
            
            return [
                'status' => false,
                'message' => $this->getErrorMessage($errorCode),
                'error_code' => $errorCode,
                'firebase_response' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::error('Firebase Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Firebase Exception: ' . $e->getMessage(),
            ];
        }
    }

    public function sendMulticastNotification(array $deviceTokens, $title, $body, $data = null)
    {
        try {
            $url = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';
            
            $results = [];
            $validTokens = [];
            $invalidTokens = [];

            foreach ($deviceTokens as $token) {
                $message = [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ];

                if ($data) {
                    $message['data'] = $data;
                }

                $payload = ['message' => $message];
                $response = Http::withToken($this->accessToken)->post($url, $payload);

                if ($response->successful()) {
                    $validTokens[] = $token;
                    $results[] = [
                        'token' => $token,
                        'status' => 'success',
                        'response' => $response->json()
                    ];
                } else {
                    $responseData = $response->json();
                    $errorCode = $responseData['error']['details'][0]['errorCode'] ?? 'UNKNOWN';
                    
                    if ($errorCode === 'UNREGISTERED') {
                        $invalidTokens[] = $token;
                    }

                    $results[] = [
                        'token' => $token,
                        'status' => 'failed',
                        'error_code' => $errorCode,
                        'response' => $responseData
                    ];
                }
            }

            return [
                'status' => true,
                'valid_tokens' => $validTokens,
                'invalid_tokens' => $invalidTokens,
                'results' => $results,
                'summary' => [
                    'total' => count($deviceTokens),
                    'successful' => count($validTokens),
                    'failed' => count($deviceTokens) - count($validTokens)
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Firebase Multicast Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Firebase Exception: ' . $e->getMessage(),
            ];
        }
    }

    public function validateToken($deviceToken)
    {
        $response = $this->sendNotification($deviceToken, 'Test', 'Token validation', ['test' => 'true']);
        
        if (!$response['status'] && isset($response['error_code']) && $response['error_code'] === 'UNREGISTERED') {
            return false;
        }
        
        return true;
    }

    public function getProjectInfo()
    {
        try {
            // Get project info from service account
            $serviceAccount = json_decode(file_get_contents(storage_path('app/firebase/service-account.json')), true);
            
            return [
                'service_account_project_id' => $serviceAccount['project_id'] ?? null,
                'env_project_id' => env('FIREBASE_PROJECT_ID'),
                'sender_id' => $serviceAccount['client_id'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Could not read service account file: ' . $e->getMessage()
            ];
        }
    }

    private function getErrorMessage($errorCode)
    {
        $messages = [
            'UNREGISTERED' => 'The device token is no longer valid. The app may have been uninstalled or the token expired.',
            'SENDER_ID_MISMATCH' => 'The FCM token was created for a different Firebase project. Check your project ID and service account.',
            'INVALID_ARGUMENT' => 'Invalid message format or parameters.',
            'NOT_FOUND' => 'The specified project ID was not found.',
            'INTERNAL' => 'Internal server error occurred.',
            'UNAVAILABLE' => 'FCM service is temporarily unavailable.',
            'QUOTA_EXCEEDED' => 'Quota exceeded for this project.',
        ];

        return $messages[$errorCode] ?? 'Unknown FCM error occurred.';
    }
}