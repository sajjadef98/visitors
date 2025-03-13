<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نمودار بازدید😎</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/md.bootstrappersiandatetimepicker@4.2.6/dist/mds.bs.datetimepicker.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/md.bootstrappersiandatetimepicker@4.2.6/dist/mds.bs.datetimepicker.style.min.css" rel="stylesheet">

    <style>
        #top-urls a {
            color: #0066cc;
            transition: color 0.3s ease;
        }
        #top-urls a:hover {
            color: #004080;
            text-decoration: underline !important;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 500px;
            width: 100%;
            max-width: 1200px;
        }
        @media (max-width: 768px) {
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12 my-3">
            <a href="<?=$POST_URL?>" class="btn btn-danger">  </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12 card p-3">
            <form id="dateFilterForm" class="row gy-2 gx-3 align-items-end">
                <!-- Start Date -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text cursor-pointer" id="TaghvimPiker1">📅</span>
                        </div>
                        <input type="text" class="form-control"
                               placeholder="تاریخ شروع"
                               name="start_date"
                               id="start_date"
                               data-name="dtp1-date">
                        <input type="text" class="form-control"
                               placeholder="انتخاب تاریخ"
                               data-name="dtp1-text">
                    </div>
                </div>

                <!-- End Date -->
                <div class="col-12 col-sm-6 col-md-4 mt-2 mt-sm-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text cursor-pointer" id="TaghvimPiker2">📅</span>
                        </div>
                        <input type="text" class="form-control"
                               placeholder="تاریخ پایان"
                               name="end_date"
                               id="end_date"
                               data-name="dtp2-date">
                        <input type="text" class="form-control"
                               placeholder="انتخاب تاریخ"
                               data-name="dtp2-text">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="col-12 col-md-4 mt-2 mt-md-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button"
                                class="btn btn-outline-primary w-100 w-md-auto"
                                onclick="applyDateFilter()">
                            اعمال فیلتر
                        </button>
                        <button type="button"
                                class="btn btn-outline-secondary w-100 w-md-auto mt-2 mt-md-0"
                                onclick="resetDateFilter()">
                            پاک کردن
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-12 card p-3">
            <div id="totalDisplay" class="text-center">
                <h3>مجموع بازدیدها: <span id="total-visits">0</span></h3>
                <h3>بازدیدهای یکتا: <span id="total-unique">0</span></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <canvas id="visitChart"></canvas>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <h3>20 URL برتر</h3>
            <div id="top-urls"></div>
        </div>
    </div>
</div>


<div class="container-fluid py-4 px-sm-3 px-md-5">
    <p class="m-0 text-center">
        © . All Rights Reserved <?=date('Y')?>.
        Made by <a class="font-weight-bold" href="https://www.instagram.com/phpdevelop.er/">Sajjad Eftekhari</a>
    </p>
</div>

<script>

    let visitChart;

    function initChart(data) {
        const ctx = document.getElementById('visitChart').getContext('2d');

        if (visitChart) visitChart.destroy();

        visitChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'کل بازدیدها',
                        data: data.visitCounts,
                        borderColor: '#ff6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4 ,
                        pointRadius: 6, // اندازه معمولی نقاط
                        pointHoverRadius: 8 // اندازه هنگام هاور
                    },
                    {
                        label: 'بازدیدهای یکتا',
                        data: data.uniqueVisitCounts,
                        borderColor: '#36a2eb',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.4 ,
                        pointRadius: 6, // اندازه معمولی نقاط
                        pointHoverRadius: 8 // اندازه هنگام هاور
                    }
                ]
            },
            options: {
                responsive: true, // پاسخگویی به اندازه کانتینر
                maintainAspectRatio: false, // عدم محدودیت نسبت ابعاد
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'تعداد بازدیدها'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'تاریخ'
                        }
                    }
                },
                plugins: {
                    // تنظیمات لجند
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }

    // تابع تنظیم مجدد نمودار در هنگام تغییر اندازه صفحه


    async function applyDateFilter() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        try {
            const response = await fetch('<?=$POST_URL?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            initChart(data);
            updateTotalDisplay(data);

        } catch (error) {
            console.error('Fetch error:', error);
            alert('خطا در دریافت اطلاعات');
        }
    }

    function resetDateFilter() {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        applyDateFilter();
    }

    function updateTotalDisplay(data) {
        document.getElementById('total-visits').textContent = data.totalVisits;
        document.getElementById('total-unique').textContent = data.totalUnique;

        // ایجاد جدول 20 URL برتر
        const topUrlsContainer = document.getElementById('top-urls');
        topUrlsContainer.innerHTML = ''; // پاک کردن محتوای قبلی

        const table = document.createElement('table');
        table.className = 'table table-striped table-bordered';

        // هدر جدول
        const thead = document.createElement('thead');
        thead.innerHTML = `
        <tr>
            <th class="text-end">ردیف</th>
            <th class="text-start">آدرس URL</th>
            <th class="text-end">تعداد بازدید</th>
        </tr>
    `;
        table.appendChild(thead);

        // بدنه جدول
        const tbody = document.createElement('tbody');
        data.topUrls.forEach((urlData, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td class="text-end">${index + 1}</td>
            <td class="text-start">
                <a href="${urlData.url}" target="_blank" class="text-decoration-none">
                    ${urlData.url}
                    🔗
                </a>
            </td>
            <td class="text-end">${urlData.visit_count}</td>
        `;
            tbody.appendChild(row);
        });
        table.appendChild(tbody);

        topUrlsContainer.appendChild(table);
    }

    window.onload = applyDateFilter;



    document.addEventListener('DOMContentLoaded', function () {
        // اعمال انتخاب‌کننده تاریخ شمسی برای "از تاریخ"

        const dtp1Instance = new mds.MdsPersianDateTimePicker(document.getElementById('TaghvimPiker1'), {
            targetTextSelector: '[data-name="dtp1-text"]',
            targetDateSelector: '[data-name="dtp1-date"]',
        });
        const dtp1Instance2 = new mds.MdsPersianDateTimePicker(document.getElementById('TaghvimPiker2'), {
            targetTextSelector: '[data-name="dtp2-text"]',
            targetDateSelector: '[data-name="dtp2-date"]',
        });
    });
</script>
</body>
</html>
