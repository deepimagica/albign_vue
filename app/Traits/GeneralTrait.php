<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

trait GeneralTrait
{
    public function get_unique_number($tbl, $col, $length = 15, $isZero = false)
    {
        static $loop = 0;

        $alphabet = $isZero ? "0123456789" : "123456789";
        $token = "";
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $token .= $alphabet[$n];
        }

        $exists = DB::table($tbl)->where($col, $token)->exists();

        if ($exists && $loop < 100) {
            $loop++;
            return $this->get_unique_number($tbl, $col, $length, $isZero);
        }
        return $token;
    }

    public function getIndianCurrency($number)
    {
        $no = str_replace(',', '', $number);
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
                $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else $str[] = null;
        }
        $rupees = implode('', array_reverse($str));
        return ($rupees ? ucwords(trim($rupees)) : "");
    }

    public function getIPDetail()
    {
        $result = [];

        $location = $this->getLocationByIp();
        if (!is_null($location) && !empty($location) && is_array($location)) {
            $result = $location;
        }

        $userAgent = request()->server('HTTP_USER_AGENT', 'Unknown');
        $parsedAgent = $this->parseUserAgent($userAgent);

        $result['browser'] = $parsedAgent['browser'];
        $result['browser_version'] = $parsedAgent['browser_version'];
        $result['os'] = $parsedAgent['os'];
        $result['ip_address'] = $this->clientIp();

        return json_encode($result);
    }


    public function clientIp($deep_detect = TRUE)
    {
        $ip = $_SERVER["REMOTE_ADDR"];

        if ($deep_detect) {
            $http_client_ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
            $http_x_forwarded_for = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
            if (filter_var($http_x_forwarded_for, FILTER_VALIDATE_IP))
                $ip = $http_x_forwarded_for;
            if (filter_var($http_client_ip, FILTER_VALIDATE_IP))
                $ip = $http_client_ip;
        }
        return $ip;
    }

    public function getLocationByIp($purpose = 'location', $ip = null, $deepDetect = true)
    {
        $output = null;

        if (is_null($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $this->clientIp($deepDetect);
        }

        if ($ip == '127.0.0.1' || $ip == '::1') {
            $ip = '123.201.3.127'; //common surat city ip 
            // $ip = '182.237.8.5'; //common ahemedabad city ip 
        }

        $purpose = str_replace(["name", "\n", "\t", " ", "-", "_"], "", strtolower(trim($purpose)));
        $supported = ['continentcode', 'country', 'countrycode', 'state', 'region', 'city', 'location', 'address'];

        $continents = [
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        ];
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $supported)) {
            $json = @file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);
            if ($json) {
                $ipData = json_decode($json);
                if (isset($ipData->geoplugin_countryCode) && strlen(trim($ipData->geoplugin_countryCode)) === 2) {
                    switch ($purpose) {
                        case 'location':
                            $output = [
                                'city'           => $ipData->geoplugin_city,
                                'state'          => $ipData->geoplugin_regionName,
                                'country'        => $ipData->geoplugin_countryName,
                                'country_code'   => $ipData->geoplugin_countryCode,
                                'continent'      => isset($continents[strtoupper($ipData->geoplugin_continentCode)])
                                    ? $continents[strtoupper($ipData->geoplugin_continentCode)]
                                    : null,
                                'continent_code' => $ipData->geoplugin_continentCode,
                            ];
                            break;
                        case 'address':
                            $address = [];
                            if (!empty($ipData->geoplugin_countryName)) {
                                $address[] = $ipData->geoplugin_countryName;
                            }
                            if (!empty($ipData->geoplugin_regionName)) {
                                $address[] = $ipData->geoplugin_regionName;
                            }
                            if (!empty($ipData->geoplugin_city)) {
                                $address[] = $ipData->geoplugin_city;
                            }
                            $output = implode(", ", array_reverse($address));
                            break;
                        case 'city':
                            $output = $ipData->geoplugin_city;
                            break;
                        case 'state':
                        case 'region':
                            $output = $ipData->geoplugin_regionName;
                            break;
                        case 'country':
                            $output = $ipData->geoplugin_countryName;
                            break;
                        case 'countrycode':
                            $output = $ipData->geoplugin_countryCode;
                            break;
                        case 'continentcode':
                            $output = $ipData->geoplugin_continentCode;
                            break;
                    }
                }
            }
        }
        return $output;
    }

    public function parseUserAgent($userAgent)
    {
        $browser = 'Unknown Browser';
        $browserVersion = 'Unknown Version';
        $os = 'Unknown OS';

        if (preg_match('/MSIE (.*?);/', $userAgent, $matches)) {
            $browser = 'Internet Explorer';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Firefox\/(.*?)(\s|$)/', $userAgent, $matches)) {
            $browser = 'Firefox';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Chrome\/(.*?)(\s|$)/', $userAgent, $matches)) {
            $browser = 'Chrome';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Safari\/(.*?)(\s|$)/', $userAgent, $matches)) {
            $browser = 'Safari';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Opera\/(.*?)(\s|$)/', $userAgent, $matches)) {
            $browser = 'Opera';
            $browserVersion = $matches[1];
        }

        // Simple OS detection based on keywords.
        if (stripos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (stripos($userAgent, 'Mac') !== false) {
            $os = 'Mac OS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (stripos($userAgent, 'iPhone') !== false) {
            $os = 'iOS';
        }

        return [
            'browser'         => $browser,
            'browser_version' => $browserVersion,
            'os'              => $os,
        ];
    }

    public function getOTP($length = 15, $iszero = false)
    {
        if ($iszero) {
            $alphabet = "0123456789";
        } else {
            $alphabet = "123456789";
        }
        $token = "";
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $token .= $alphabet[$n];
        }
        return $token;
    }

    public function sendWhatsAppMessage($to, $otp)
    {
        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => TEMPLATE_NAME,
                "language" => [
                    "code" => LANGUAGE_CODE
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n" .
                    "Authorization: Bearer " . ACCESS_TOKEN . "\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents(ENDPOINT, false, $context);



        if ($result === FALSE) {
            // Handle error
            die('Error sending message.');
        }

        return $result;
    }


    public function createImageFromBase64($oldimg, $newimg, $type)
    {
        $basePaths = [
            1 => 'assets/img/signature',
            2 => 'assets/img/document',  
            3 => 'assets/img/usersignature',
        ];

        if ($type !== 1) {
            return null; 
        }

        $path = $basePaths[1];
        $directoryPath = public_path($path);

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0775, true);
        }

        if (!empty($oldimg)) {
            $oldImagePath = $directoryPath . '/' . $oldimg;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $file_data = str_replace('data:image/png;base64,', '', $newimg);
        $file_data = str_replace('[removed]', '', $file_data);
        $file_data = str_replace(' ', '+', $file_data);

        $file_name =  time() . '.png';

        $filePath = $directoryPath . '/' . $file_name;
        file_put_contents($filePath, base64_decode($file_data));

        return $file_name;
    }

    public function decryptData($encryptedData)
    {
        $secretKey = env('VITE_SECRET_KEY');

        $secretKey = substr(hash('sha256', $secretKey, true), 0, 16);

        $decodedData = base64_decode($encryptedData);
        if (!$decodedData) {
            return null;
        }

        $decryptedData = openssl_decrypt($decodedData, 'AES-128-ECB', $secretKey, OPENSSL_RAW_DATA);

        return json_decode($decryptedData, true);
    }
}
