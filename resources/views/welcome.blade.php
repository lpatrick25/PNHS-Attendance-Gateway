<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Palale National High School Student Attendance Portal">
    <meta name="keywords" content="Palale National High School, Student Portal, attendance, RFID, Abuyog">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PNHS Attendance - {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/PNHS_Logo.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            background-image: url('img/main_bg.jpg');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Source Sans Pro', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            width: 100%;
            max-width: 1400px;
            margin: auto;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        p {
            font-weight: 600;
            text-transform: uppercase;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background-color: #003087;
            padding: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            color: #FFFFFF;
        }

        .card-body {
            padding: 20px;
        }

        .school-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            object-fit: cover;
        }

        .attendance-card img {
            width: 100px;
            height: 175px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .error-message {
            color: #F44336;
            text-align: center;
            margin-top: 10px;
            display: none;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 10px;
        }

        .loading-spinner .fas {
            font-size: 1.5rem;
            color: #FFC107;
        }

        .attendance-card .border-bottom,
        .attendance-card .border-top {
            border-color: #4CAF50 !important;
        }

        @media (max-width: 767.98px) {
            .content-wrapper {
                padding: 15px;
            }

            .school-header img {
                width: 60px;
                height: 60px;
            }

            .attendance-card img {
                width: 80px;
                height: 140px;
            }
        }
    </style>
</head>

<body>
    <main class="content-wrapper">
        <div class="container-fluid">
            <!-- School Header -->
            <div class="card mb-4">
                <div class="card-header bg-gray d-flex align-items-center">
                    <div class="school-header me-3">
                        <img src="{{ asset('img/PNHS_Logo.png') }}" alt="Palale National High School Logo">
                    </div>
                    <div>
                        <h3 class="mb-0">Palale National High School</h3>
                        <h5 class="text-muted mb-0">Brgy. Palale, Mac Arthur, Leyte</h5>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Gate Keeping, RFID Scanner, System Message -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <!-- Gate Keeping -->
                    <div class="card mb-3">
                        <div class="card-header bg-gray text-center">
                            <h3 class="mb-0">Gate Keeping</h3>
                        </div>
                        <div class="card-body text-center">
                            <h1 class="mb-0" id="time_now" aria-live="polite"></h1>
                        </div>
                        <div class="card-footer bg-gray text-center">
                            <h5 class="mb-0">{{ Str::upper(date('l - M d, Y')) }}</h5>
                        </div>
                    </div>

                    <!-- RFID Scanner -->
                    <div class="card mb-3">
                        <div class="card-header bg-gray text-center">
                            <h3 class="mb-0">RFID Scanner</h3>
                        </div>
                        <div class="card-body">
                            <form id="rfid-form" aria-label="RFID Scanner Form">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="rfid_no" id="rfid_no"
                                        placeholder="Scan RFID" autocomplete="off" autofocus
                                        aria-describedby="rfid-icon">
                                    <span class="input-group-text" id="rfid-icon"><i class="fas fa-tag"></i></span>
                                </div>
                                <div class="error-message" id="rfid-error"></div>
                                <div class="loading-spinner" id="rfid-loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- System Message -->
                    <div class="card">
                        <div class="card-header bg-gray text-center">
                            <h3 class="mb-0">System Message</h3>
                        </div>
                        <div class="card-body text-center">
                            <h1 class="mb-0" id="failedScan">0</h1>
                        </div>
                        <div class="card-footer bg-gray text-center">
                            <h5 class="mb-0">No. Failed Scans</h5>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Attendance Lists -->
                <div class="col-lg-8 col-md-12">
                    <div class="row">
                        <!-- Time In List -->
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-gray text-center">
                                    <h3 class="mb-0">Time In</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="attendance-list-login" aria-label="Time In Attendance List">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Time Out List -->
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-gray text-center">
                                    <h3 class="mb-0">Time Out</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="attendance-list-logout"
                                        aria-label="Time Out Attendance List"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function updateClock() {
                $('#time_now').text(new Date().toLocaleTimeString('en-US', {
                    hour12: true
                }));
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Store attendance data for login and logout
            const attendanceData = {
                login: [],
                logout: []
            };
            const maxAttendance = 8;
            let lastScanTime = 0; // Track last scan time for debouncing

            // Initialize form validation
            $('#rfid-form').validate({
                rules: {
                    rfid_no: {
                        required: true,
                        minlength: 1
                    }
                },
                messages: {
                    rfid_no: {
                        required: "Please scan an RFID tag",
                        minlength: "RFID number is too short"
                    }
                },
                errorPlacement: function(error, element) {
                    $('#rfid-error').text(error.text()).show();
                },
                success: function() {
                    $('#rfid-error').hide();
                },
                submitHandler: function(form) {
                    const rfid_no = $('#rfid_no').val();
                    const currentTime = Date.now();

                    // Debounce: Prevent scanning within 2 seconds
                    if (currentTime - lastScanTime < 2000) {
                        $('#rfid-error').text('Please wait 2 seconds between scans.').show();
                        return;
                    }
                    lastScanTime = currentTime;

                    showLoading(true);

                    // Check the most recent log to determine login or logout
                    $.ajax({
                        method: 'GET',
                        url: '/recent-logs',
                        data: {
                            rfid_no: rfid_no
                        },
                        dataType: 'json',
                        cache: false,
                        success: function(response) {
                            const recentLog = response.logs[0]; // Get the most recent log
                            const logType = (!recentLog || recentLog.log_type ===
                                'logout') ? 'login' : 'logout';
                            const endpoint = logType === 'login' ? '/login-logs' :
                                '/logout-logs';

                            $.ajax({
                                method: 'POST',
                                url: endpoint,
                                data: {
                                    rfid_no,
                                },
                                dataType: 'json',
                                cache: false,
                                success: function(response) {
                                    showLoading(false);
                                    $('#rfid_no').val('');
                                    if (response.valid) {
                                        attendanceData[logType].push({
                                            rfid_no: response.rfid_no,
                                            name: response.fullName,
                                            lrn: response.student_lrn,
                                            img: response.image ||
                                                '/img/avatar.png',
                                            date: response.date,
                                            time: response.time,
                                            timeInOut: response
                                                .timeInOut,
                                        });

                                        if (attendanceData[logType].length >
                                            maxAttendance) {
                                            attendanceData[logType].shift();
                                        }

                                        renderAttendance(logType);
                                        $('#rfid_no').focus();
                                    } else {
                                        $('#failedScan').text(parseInt($(
                                            '#failedScan').text()) + 1);
                                        $('#rfid-error').text(response.error ||
                                            'Invalid RFID scan.').show();
                                    }
                                },
                                error: function(jqXHR) {
                                    showLoading(false);
                                    $('#rfid_no').val('');
                                    $('#failedScan').text(parseInt($(
                                        '#failedScan').text()) + 1);
                                    $('#rfid-error').text(jqXHR.responseJSON
                                        ?.error ||
                                        'Failed to log attendance.').show();
                                }
                            });
                        },
                        error: function(jqXHR) {
                            showLoading(false);
                            $('#rfid_no').val('');
                            $('#failedScan').text(parseInt($('#failedScan').text()) + 1);
                            $('#rfid-error').text(jqXHR.responseJSON?.error ||
                                'Failed to determine log type.').show();
                        }
                    });
                }
            });

            function showLoading(show) {
                $('#rfid-loading').toggle(show);
            }

            function renderAttendance(logType) {
                const attendanceContainer = $(`#attendance-list-${logType}`);
                attendanceContainer.empty();

                attendanceData[logType].forEach(student => {
                    attendanceContainer.append(`
                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <div class="card attendance-card h-100">
                        <div class="card-body d-flex">
                            <div class="me-3">
                                <img src="${student.img}" alt="${student.name}'s avatar">
                            </div>
                            <div class="flex-grow-1 text-center">
                                <p class="mb-1 border-bottom">${student.name}</p>
                                <p class="mb-1 small text-muted">Student Name</p>
                                <p class="mb-1 border-bottom">${student.lrn}</p>
                                <p class="mb-1 small text-muted">Student LRN</p>
                                <p class="mb-0 border-top">${student.timeInOut}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
                });

                for (let i = attendanceData[logType].length; i < maxAttendance; i++) {
                    attendanceContainer.append(`
                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <div class="card attendance-card h-100">
                        <div class="card-body d-flex">
                            <div class="me-3">
                                <img src="/img/avatar.png" alt="Placeholder avatar">
                            </div>
                            <div class="flex-grow-1 text-center">
                                <p class="mb-1 border-bottom">-</p>
                                <p class="mb-1 small text-muted">Student Name</p>
                                <p class="mb-1 border-bottom">-</p>
                                <p class="mb-1 small text-muted">Student LRN</p>
                                <p class="mb-0 border-top"><span>Time ${logType === 'login' ? 'In' : 'Out'}: <strong class="text-danger">-</strong></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
                }
            }

            // Real-time updates via polling
            function fetchRecentLogs() {
                $.ajax({
                    method: 'GET',
                    url: '/recent-logs',
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        response.logs.forEach(log => {
                            const logType = log.log_type;
                            if (!attendanceData[logType].some(item => item.rfid_no === log
                                    .rfid_no && item.time === log.time)) {
                                attendanceData[logType].push(log);
                            }
                        });
                        if (attendanceData.login.length > maxAttendance) {
                            attendanceData.login.splice(0, attendanceData.login.length - maxAttendance);
                        }
                        if (attendanceData.logout.length > maxAttendance) {
                            attendanceData.logout.splice(0, attendanceData.logout.length -
                                maxAttendance);
                        }
                        renderAttendance('login');
                        renderAttendance('logout');
                    },
                    error: function(jqXHR) {
                        console.error('Failed to fetch recent logs:', jqXHR);
                    }
                });
            }

            // setInterval(fetchRecentLogs, 5000); // Poll every 5 seconds (disabled for now)

            // Initial render
            renderAttendance('login');
            renderAttendance('logout');
        });
    </script>
</body>

</html>
