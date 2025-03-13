<?php
namespace Visitors;

use Exception;

use MysqliDb;

class Visitors
{
    public $db;
    private $viewPath;
    private $POST_URL;

    public function __construct(MysqliDb $db,$POST_URL='' ,string $viewPath = null )
    {
        $this->db = $db;
        $this->POST_URL = $POST_URL;
        $this->viewPath = $viewPath ?? __DIR__.DIRECTORY_SEPARATOR;
    }

    public function chart()
    {
        // دریافت پارامترهای فیلتر تاریخ
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;

        // ساخت شرط فیلتر
        $filter = [];
        if ($start_date && $end_date) {
            $filter['condition'] = "DATE(created_at) BETWEEN ? AND ?";
            $filter['params'] = [$start_date, $end_date];
        }

        // تابع کمکی برای اجرای کوئری
        $executeQuery = function ($select, $filter) {
            $query = "$select FROM device_info";
            if (!empty($filter['condition'])) {
                $query .= " WHERE " . $filter['condition'];
            }
            $query .= " GROUP BY visit_date ORDER BY visit_date";

            return !empty($filter['params'])
                ? $this->db->rawQuery($query, $filter['params'])
                : $this->db->rawQuery($query);
        };

        // دریافت آمار بازدید
        $visits = $executeQuery(
            "SELECT DATE(created_at) AS visit_date, COUNT(*) AS visit_count",
            $filter
        );

        $uniqueVisits = $executeQuery(
            "SELECT DATE(created_at) AS visit_date, COUNT(DISTINCT ip_address) AS unique_visit_count",
            $filter
        );

        // آماده‌سازی داده‌ها برای نمودار
        $labels = array_column($visits, 'visit_date');
        $visitCounts = array_column($visits, 'visit_count');
        $uniqueVisitCounts = array_column($uniqueVisits, 'unique_visit_count');

        // محاسبه مجموع کل
        $totalVisits = array_sum($visitCounts);
        $totalUnique = array_sum($uniqueVisitCounts);

        // اطلاعات تکمیلی
        $data = [
            'labels' => $labels,
            'visitCounts' => $visitCounts,
            'uniqueVisitCounts' => $uniqueVisitCounts,
            'totalVisits' => $totalVisits,
            'totalUnique' => $totalUnique,
            'start_date' => $start_date,
            'end_date' => $end_date ,
            'POST_URL' => $this->POST_URL
        ];


        // نمایش ویو
        $this->renderView('chart', $data);

    }

    public function chartApi()
    {
        header('Content-Type: application/json');

        try {
            // دریافت پارامترهای فیلتر از درخواست POST
            $input = json_decode(file_get_contents('php://input'), true);
            $start_date = $input['start_date'] ?? null;
            $end_date = $input['end_date'] ?? null;

            // ساخت شرط فیلتر
            $filter = [];
            if ($start_date && $end_date) {
                $filter['condition'] = "DATE(created_at) BETWEEN ? AND ?";
                $filter['params'] = [$start_date, $end_date];
            }

            // اجرای کوئری برای آمار کلی
            $query = "SELECT 
                    DATE(created_at) AS visit_date, 
                    COUNT(*) AS visit_count,
                    COUNT(DISTINCT ip_address) AS unique_visit_count
                  FROM device_info
                  " . (!empty($filter['condition']) ? " WHERE {$filter['condition']}" : "") . "
                  GROUP BY visit_date
                  ORDER BY visit_date";

            $result = !empty($filter['params'])
                ? $this->db->rawQuery($query, $filter['params'])
                : $this->db->rawQuery($query);

            // اجرای کوئری برای 20 URL برتر
            $urlQuery = "SELECT 
                        url, 
                        COUNT(*) AS url_visit_count
                     FROM device_info
                     " . (!empty($filter['condition']) ? " WHERE {$filter['condition']}" : "") . "
                     GROUP BY url
                     ORDER BY url_visit_count DESC
                     LIMIT 20";

            $urlResult = !empty($filter['params'])
                ? $this->db->rawQuery($urlQuery, $filter['params'])
                : $this->db->rawQuery($urlQuery);

            // آماده‌سازی داده‌ها
            $labels = array_column($result, 'visit_date');
            $visitCounts = array_column($result, 'visit_count');
            $uniqueVisitCounts = array_column($result, 'unique_visit_count');
            $totalVisits = array_sum($visitCounts);
            $totalUnique = array_sum($uniqueVisitCounts);

            // آماده‌سازی داده‌های URL
            $topUrls = array_map(function ($item) {
                return [
                    'url' => $item['url'],
                    'visit_count' => $item['url_visit_count']
                ];
            }, $urlResult);

            // پاسخ JSON
            echo json_encode([
                'labels' => $labels,
                'visitCounts' => $visitCounts,
                'uniqueVisitCounts' => $uniqueVisitCounts,
                'totalVisits' => $totalVisits,
                'totalUnique' => $totalUnique,
                'topUrls' => $topUrls
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
    private function renderView(string $view, array $data): void
    {
        extract($data);
        require $this->viewPath . $view . '.php';
    }

    private function handleError(string $message): void
    {
        error_log($message);
        http_response_code(500);
        echo "Error: " . $message;
    }
}
