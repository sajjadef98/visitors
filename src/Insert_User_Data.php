<?php
namespace Visitors;

use MysqliDb;

class Insert_User_Data extends Visitors
{
    public $ip ;

   public function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->ip =$ip ;
        return $ip;
    }

// Function to get the device model (basic extraction from User-Agent)
    public function getDeviceModel()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return 'Unknown';
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $device = 'Unknown';

        // آرایه‌ای از الگوهای دستگاه‌ها با استفاده از عبارات منظم
        $patterns = [
            // iPhone Models
            '/iPhone\sOS\s(\d+)_\d+.*\s([\w\s]+)\sBuild\//i' => 'iPhone $2',
            '/iPhone\s([\w\s]+)\sBuild\//i' => 'iPhone $1',

            // iPad Models
            '/iPad;\sCPU\sOS\s(\d+)_\d+/i' => 'iPad OS $1',
            '/iPad\s([\w\s]+)\sBuild\//i' => 'iPad $1',

            // Android Devices
            '/Android\s+([\d.]+)[;\s]\s*([\w\s\-]+)\sBuild\//i' => 'Android $1 - $2',
            '/Android\s+([\d.]+)\sBuild\//i' => 'Android $1',

            // Samsung Devices
            '/Samsung\s+SM-([\w\-]+)/i' => 'Samsung SM-$1',
            '/Samsung\s+([\w\s\-]+)\sBuild\//i' => 'Samsung $1',

            // Google Pixel
            '/Pixel\s(\d+)/i' => 'Google Pixel $1',

            // OnePlus Devices
            '/OnePlus\s+([\w\s]+)/i' => 'OnePlus $1',

            // Huawei Devices
            '/Huawei\s+([\w\-]+)/i' => 'Huawei $1',

            // Xiaomi Devices
            '/MI\s+([\w\-]+)/i' => 'Xiaomi MI $1',

            // Oppo Devices
            '/OPPO\s+([\w\-]+)/i' => 'OPPO $1',

            // Vivo Devices
            '/vivo\s+([\w\-]+)/i' => 'vivo $1',

            // Lenovo Devices
            '/Lenovo\s+([\w\-]+)/i' => 'Lenovo $1',

            // Dell Devices
            '/Dell\s+([\w\-]+)/i' => 'Dell $1',

            // HP Devices
            '/HP\s+([\w\-]+)/i' => 'HP $1',

            // MacBook Models
            '/MacBook\sPro\s([\w\s]+)/i' => 'MacBook Pro $1',
            '/MacBook\sAir\s([\w\s]+)/i' => 'MacBook Air $1',

            // Windows Computers (خواندن مدل ممکن نیست، اغلب فقط سیستم عامل)
            '/Windows\sNT\s[\d.]+/i' => 'Windows PC',

            // その他のデバイス
            // می‌توانید الگوهای بیشتری اضافه کنید
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $userAgent, $matches)) {
                $device = preg_replace($pattern, $replacement, $userAgent);
                return $device;
            }
        }

        // اگر هیچ الگویی پیدا نشد، سعی می‌کنیم نوع کلی دستگاه را شناسایی کنیم
        if (strpos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($userAgent, 'iPad') !== false) {
            return 'iPad';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            return 'Mac PC';
        }

        return $device;
    }

// Function to get the phone model (basic extraction from User-Agent)
    public function getPhoneModel()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        // Basic extraction, you can improve this with more advanced logic
        if (preg_match('/\((.*?)\)/', $userAgent, $matches)) {
            return $matches[1];
        }
        return 'Unknown';
    }
    public function city()
    {
        $ip = $this->ip ;
        $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
        if($query && $query['status'] == 'success') {
            $hh = $query['country'].', '.$query['city'].'!';
            return $hh ;

        } else {
         return 0 ;
        }

    }
    public function GUDI()
    {
        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // ip_address , device_model ,phone_model ,user_agent ,url ,created_at
        $data =[
            'ip_address' => self::sanitizeInput($this->getIpAddress()),
          'device_model' =>  self::sanitizeInput($this->getDeviceModel()),
          'phone_model' => self::sanitizeInput($this->getPhoneModel()) ,
          'user_agent' => self::sanitizeInput($_SERVER['HTTP_USER_AGENT']),
          'url' => self::sanitizeInput(urldecode($url)),
        ];
		 $this->db->insert ('device_info', $data);
    }
    public function  sanitizeInput($input, $isArray = false) {
    // Define an array of dangerous SQL injection patterns
        $dangerousPatterns = [
            '/\b(ALTER|CREATE|DELETE|DROP|EXEC|INSERT|SELECT|UNION|UPDATE|TRUNCATE)\b/i', // SQL keywords
            '/--/', // SQL comments
            '/\/\*.*\*\//', // Multiline SQL comments
            '/;/', // SQL command separator
            '/\bOR\b/i', // Dangerous OR statements
            '/\bAND\b/i', // Dangerous AND statements
            '/=[\s]*\d+/', // Numerical equality checks
            '/[\s]+LIKE[\s]+/', // Dangerous LIKE statements
            '/[\s]+IN[\s]+\(/', // Dangerous IN statements
            '/[\s]+BETWEEN[\s]+/', // Dangerous BETWEEN statements
            '/[\s]+JOIN[\s]+/', // Dangerous JOIN statements
            '/[\s]+FROM[\s]+/', // Dangerous FROM statements
            '/[\s]+WHERE[\s]+/', // Dangerous WHERE statements
        ];

        // If the input is an array, recursively sanitize each element
        if ($isArray && is_array($input)) {
            return array_map(function($value) use ($dangerousPatterns) {
                return preg_replace($dangerousPatterns, '', $value);
            }, $input);
        }

        // If the input is a string, sanitize it directly
        if (is_string($input)) {
            return preg_replace($dangerousPatterns, '', $input);
        }

        // Return the input as-is if it's not a string or array
        return $input;
    }


}



