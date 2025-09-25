<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Client;

class GlobalFunction extends Model
{
    use HasFactory;

    public static function roundNumber($number)
    {
        return round($number, 2);
    }

    function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/firebase/service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }
    public static function sendPushNotificationToUsers($title, $message)
    {
        // Project ID بتاعك من Firebase Console
        $projectId = 'ershad-bba88'; // غيره للي عندك
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // هات الـ Access Token من service account
        $accessToken = (new self)->getAccessToken();

        // هات كل التوكينات من الجدول
        $tokens = User::pluck('device_token')->toArray();

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        foreach ($tokens as $token) {
            $fields = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $message,
                    ],
                    'android' => [
                        'priority' => 'high'
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ]
                        ]
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            $result = curl_exec($ch);

            if ($result === FALSE) {
                \Log::error('FCM v1 Send Error: ' . curl_error($ch));
            } else {
                \Log::info('FCM v1 Response: ' . $result);
            }

            curl_close($ch);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notifications sent successfully (FCM v1)'
        ]);
    }


    public static function sendPushNotificationToDoctors($title, $message)
    {   // Project ID بتاعك من Firebase Console
        $projectId = 'ershad-bba88'; // غيره للي عندك
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // هات الـ Access Token من service account
        $accessToken = (new self)->getAccessToken();

        // هات كل التوكينات من الجدول
        $tokens = Doctors::pluck('device_token')->toArray();

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        foreach ($tokens as $token) {
            $fields = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $message,
                    ],
                    'android' => [
                        'priority' => 'high'
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ]
                        ]
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            $result = curl_exec($ch);

            if ($result === FALSE) {
                \Log::error('FCM v1 Send Error: ' . curl_error($ch));
            } else {
                \Log::info('FCM v1 Response: ' . $result);
            }

            curl_close($ch);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notifications sent successfully (FCM v1)'
        ]);
    }


    public static function returnAppointmentStatus($status)
    {
        $statusPill = "";
        switch ($status) {
            case (Constants::orderPlacedPending):
                $statusPill = '<span class="badge bg-warning text-white">' . __('Pending') . '</span>';
                break;
            case (Constants::orderAccepted):
                $statusPill = '<span class="badge bg-primary text-white">' . __('Accepted') . '</span>';
                break;
            case (Constants::orderCompleted):
                $statusPill = '<span class="badge bg-success text-white">' . __('Completed') . '</span>';
                break;
            case (Constants::orderDeclined):
                $statusPill = '<span class="badge bg-danger text-white">' . __('Declined') . '</span>';
                break;
            case (Constants::orderCancelled):
                $statusPill = '<span class="badge bg-danger text-white">' . __('Cancelled') . '</span>';
                break;
        }
        return $statusPill;
    }

    public static function returnPromotionStatus($status)
    {
        return match ($status) {
            Constants::promotionPending => '<span class="badge bg-warning text-white">' . __('Pending') . '</span>',
            Constants::promotionApproved => '<span class="badge bg-success text-white">' . __('Approved') . '</span>',
            Constants::promotionRejected => '<span class="badge bg-danger text-white">' . __('Rejected') . '</span>',
        };
    }

    public static function sendSimpleResponse($status, $msg)
    {
        return response()->json(['status' => $status, 'message' => $msg]);
    }
    public static function sendDataResponse($status, $msg, $data, $numpages = 0, $currentPage = 0)
    {
        if ($numpages == 0)
            return response()->json(['status' => $status, 'message' => $msg, 'data' => $data]);
        else {
            return response()->json(['status' => $status, 'numberPages' => $numpages, 'currentPage' => $currentPage, 'message' => $msg, 'data' => $data]);
        }
    }

    public static function generateUserFullData($id)
    {
        $user = Users::where('id', $id)
            ->with(['patients'])
            ->first();
        $phone = explode(' ', $user->phone_number);
        if (isset($phone[0])) {
            $user->phone_number = $phone[0];
        }
        return $user;
    }
    public static function generateDoctorFullData($id)
    {
        $doctor = Doctors::where('id', $id)
            ->with([
                'services',
                'experience',
                'expertise',
                'serviceLocations',
                'awards',
                'slots',
                'holidays',
                'bankAccount',
            ])
            ->first();

        return $doctor;
    }


    public static function sendPushToDoctor($title, $message, $doctor)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = env('FCM_TOKEN');
        $notificationArray = array('title' => $title, 'body' => $message, 'sound' => 'default', 'badge' => '1');

        if ($doctor->is_notification == 1) {
            $fields = array('to' => "/token/" . $doctor->device_token, 'notification' => $notificationArray, 'priority' => 'high');
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $api_key
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            // print_r(json_encode($fields));

            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
                Log::debug(curl_error($ch));
            }
            curl_close($ch);

            if ($result) {
                $response['status'] = true;
                $response['message'] = 'Notification sent successfully !';
            } else {
                $response['status'] = false;
                $response['message'] = 'Something Went Wrong !';
            }
            // echo json_encode($response);
        }
    }
    public static function sendPushToUser($title, $message, $user)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = env('FCM_TOKEN');
        $notificationArray = array('title' => $title, 'body' => $message, 'sound' => 'default', 'badge' => '1');

        if ($user->is_notification == 1) {
            $fields = array('to' => "/token/" . $user->device_token, 'notification' => $notificationArray, 'priority' => 'high');
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $api_key
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            // print_r(json_encode($fields));

            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
                Log::debug(curl_error($ch));
            }
            curl_close($ch);

            if ($result) {
                $response['status'] = true;
                $response['message'] = 'Notification sent successfully !';
            } else {
                $response['status'] = false;
                $response['message'] = 'Something Went Wrong !';
            }
            // echo json_encode($response);
        }
    }

    public static function createMediaUrl($media)
    {
        $url = env('FILES_BASE_URL') . $media;
        return $url;
    }

    public static function uploadFilToS3($request, $key)
    {
        $s3 = Storage::disk('s3');
        $file = $request->file($key);
        $fileName = time() . $file->getClientOriginalName();
        $fileName = str_replace(" ", "_", $fileName);
        $filePath = 'uploads/' . $fileName;
        $result =  $s3->put($filePath, file_get_contents($file), 'public-read');
        return $filePath;
    }

    public static function point2point_distance($lat1, $lon1, $lat2, $lon2, $unit = 'K', $radius)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return (($miles * 1.609344) <= $radius);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    public static function detectPaymentGateway($gateway)
    {
        $name = "";
        switch ($gateway) {
            case (Constants::stripe):
                $name = 'Stripe';
                break;
            case (Constants::addedByAdmin):
                $name = 'Added By Admin';
                break;
            case (Constants::flutterWave):
                $name = 'Flutterwave';
                break;
            case (Constants::razorPay):
                $name = 'Razorpay';
                break;
            case (Constants::payStack):
                $name = 'Paystack';
                break;
            case (Constants::payPal):
                $name = 'PayPal';
                break;
            case (Constants::Fawaterk):
                $name = 'Fawaterk';
                break;
        }

        return $name;
    }

    public static function cleanString($string)
    {
        return  str_replace(array('<', '>', '{', '}', '[', ']', '`'), '', $string);
    }

    public static function deleteFile($filename)
    {
        if ($filename != null && file_exists(storage_path('app/public/' . $filename))) {
            unlink(storage_path('app/public/' . $filename));
        }
    }

    public static function saveFileAndGivePath($file)
    {
        if ($file != null) {
            $path = $file->store('uploads');
            return $path;
        } else {
            return null;
        }
    }

    public static function formateTimeString($timeString)
    {
        if ($timeString != null) {
            return substr_replace($timeString, ":", 2, 0);
        }
        return "";
    }

    public static function generatePlatformEarningHistoryNumber()
    {
        $token =  rand(100000, 999999);

        $first = Constants::prefixPlatformEarningHistory;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = PlatformEarningHistory::where('earning_number', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);

            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = PlatformEarningHistory::where('earning_number', $first)->count();
        }

        return $first;
    }
    public static function generateDoctorEarningHistoryNumber()
    {
        $token =  rand(100000, 999999);
        $first = Constants::prefixDoctorEarningHistory;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = DoctorEarningHistory::where('earning_number', $first)->count();

        while ($count >= 1) {
            $token =  rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = DoctorEarningHistory::where('earning_number', $first)->count();
        }

        return $first;
    }
    public static function generateServiceNumber()
    {
        $token =  rand(100000, 999999);
        $first = Constants::prefixServiceNumber;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = Services::where('service_number', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);

            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = Services::where('service_number', $first)->count();
        }

        return $first;
    }
    public static function generateDoctorNumber()
    {
        $token =  rand(100000, 999999);

        $first = Constants::prefixDoctorNumber;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = Doctors::where('doctor_number', $first)->count();

        while ($count >= 1) {
            $token =  rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = Doctors::where('doctor_number', $first)->count();
        }

        return $first;
    }

    public static function generateDoctorWithdrawRequestNumber()
    {
        $token =  rand(100000, 999999);
        $first = Constants::prefixDoctorWithDrawRequestNumber;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = DoctorPayoutHistory::where('request_number', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = DoctorPayoutHistory::where('request_number', $first)->count();
        }

        return $first;
    }
    public static function generateUserWithdrawRequestNumber()
    {
        $token =  rand(100000, 999999);
        $first = Constants::prefixUserWithDrawRequestNumber;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = UserWithdrawRequest::where('request_number', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = UserWithdrawRequest::where('request_number', $first)->count();
        }

        return $first;
    }
    public static function generateAppointmentNumber()
    {
        $token =  rand(100000, 999999);

        $first = Constants::prefixAppointmentNumber;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = Appointments::where('appointment_number', $first)->count();

        while ($count >= 1) {
            $token =  rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = Appointments::where('appointment_number', $first)->count();
        }

        return $first;
    }

    public static function addDoctorStatementEntry($doctorId, $appointmentNumber, $amount, $crOrDr, $type, $summary)
    {
        $stmt = new DoctorWalletStatements();
        $stmt->transaction_id = GlobalFunction::generateDoctorTransactionId();
        $stmt->doctor_id = $doctorId;
        $stmt->appointment_number = $appointmentNumber;
        $stmt->amount = $amount;
        $stmt->cr_or_dr = $crOrDr;
        $stmt->type = $type;
        $stmt->summary = $summary;
        $stmt->save();
    }
    public static function addUserStatementEntry($userId, $appointmentNumber, $amount, $crOrDr, $type, $summary)
    {
        $stmt = new UserWalletStatements();
        $stmt->transaction_id = GlobalFunction::generateTransactionId();
        $stmt->user_id = $userId;
        $stmt->appointment_number = $appointmentNumber;
        $stmt->amount = $amount;
        $stmt->cr_or_dr = $crOrDr;
        $stmt->type = $type;
        $stmt->summary = $summary;
        $stmt->save();
    }

    public static function generateDoctorTransactionId()
    {
        $token =  rand(100000, 999999);

        $first = Constants::prefixDoctorTransactionId;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = DoctorWalletStatements::where('transaction_id', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);

            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = DoctorWalletStatements::where('transaction_id', $first)->count();
        }

        return $first;
    }
    public static function generateTransactionId()
    {
        $token =  rand(100000, 999999);
        $first = Constants::prefixUserTransactionId;
        $first .= GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = UserWalletStatements::where('transaction_id', $first)->count();

        while ($count >= 1) {

            $token =  rand(100000, 999999);

            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = UserWalletStatements::where('transaction_id', $first)->count();
        }

        return $first;
    }


    public static function generateRandomString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
