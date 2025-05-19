<?php
    // Helper functions for holiday and attendance checks
    function isSchoolHoliday($pdo, $udiseCode, $schoolType, $finYear)
    {
        // Check for standard holiday in school calendar
        $holiday = $pdo->prepare("SELECT holid_ay FROM sms.school_holiday
                                 WHERE finyr = :fy
                                 AND da_te = :currentDate
                                 AND (sch_typ = :schoolType OR sch_typ = 'SU' OR sch_typ = 'SA')
                                 LIMIT 1");
        $holiday->execute([
            'fy' => $finYear,
            'currentDate' => date('Y-m-d'),
            'schoolType' => $schoolType
        ]);

        return !empty($holiday->fetchColumn());
    }

    function isLocalHoliday($pdo, $udiseCode)
    {
        // Check for school-specific local holiday
        $localHoliday = $pdo->prepare("SELECT holid_ay FROM sms.local_holiday
                                      WHERE :currentDate BETWEEN from_dt AND till_dt
                                      AND udise_cd = :udiseCode
                                      LIMIT 1");
        $localHoliday->execute([
            'udiseCode' => $udiseCode,
            'currentDate' => date('Y-m-d')
        ]);

        return !empty($localHoliday->fetchColumn());
    }

    function isTeacherOnLeave($pdo, $teacherId)
    {
        // Check if teacher is on leave today
        $leaveCheck = $pdo->prepare("SELECT COUNT(*) FROM sms.leave_mgmt
                                    WHERE slno = :teacherId
                                    AND to_char(current_date,'yyyy-mm-dd') BETWEEN from_dt AND till_dt
                                    AND pres_status = 'SH'");
        $leaveCheck->execute(['teacherId' => $teacherId]);

        return $leaveCheck->fetchColumn() > 0;
    }

    function isWithinSchoolHours()
    {
        // Check if current time is within school hours (8:00 AM - 3:00 PM)
        date_default_timezone_set('Asia/Kolkata');
        $schoolStartTime = DateTime::createFromFormat('H:i', '8:00');
        $schoolEndTime = DateTime::createFromFormat('H:i', '16:00');
        $currentTime = new DateTime();

        return ($currentTime >= $schoolStartTime && $currentTime <= $schoolEndTime);
    }

    function isHolidayOverridden($pdo, $udiseCode, $finYear)
    {
        // Check if holiday has been explicitly overridden for this school
        $overrideCheck = $pdo->prepare("SELECT COUNT(*) FROM sms.skip_holiday
                                       WHERE finyr = :fy
                                       AND da_te = :currentDate
                                       AND udise_cd = :udiseCode");
        $overrideCheck->execute([
            'fy' => $finYear,
            'currentDate' => date('Y-m-d'),
            'udiseCode' => $udiseCode
        ]);

        return $overrideCheck->fetchColumn() > 0;
    }

    function getSchoolType($schoolCategory)
    {
        // Map school category ID to school type code
        $primarySchoolCats = [1, 2, 3, 6, 12];
        $middleSchoolCats = [2, 3, 4, 5, 6, 7];
        $secondarySchoolCats = [3, 5, 6, 7, 8, 10, 11];

        if (in_array($schoolCategory, $primarySchoolCats)) {
            return 'PS'; // Primary School
        } elseif (in_array($schoolCategory, $middleSchoolCats)) {
            return 'MS'; // Middle School
        } elseif (in_array($schoolCategory, $secondarySchoolCats)) {
            return 'SS'; // Secondary School
        }

        return '';
    }

    function getFinancialYear($pdo)
    {
        $finYearQuery = $pdo->query("SELECT max(finyr) as finyr FROM sms.finyr");
        $result = $finYearQuery->fetch(PDO::FETCH_ASSOC);

        return $result['finyr'] ?? '';
    }

    function checkAttendanceAccess($pdo, $teacherId)
    {
        // Main function to determine if attendance can be marked
        $teacherQuery = $pdo->prepare("SELECT a.udise_sch_code, a.sch_category_id
                                      FROM mizoram115.school_master a, sms.tch_profile b
                                      WHERE a.udise_sch_code = b.udise_sch_code
                                      AND trim(b.slno) = trim(:teacherId)");
        $teacherQuery->execute(['teacherId' => $teacherId]);
        $schoolInfo = $teacherQuery->fetch(PDO::FETCH_ASSOC);

        if (!$schoolInfo) {
            return [
                'canMarkAttendance' => false,
                'message' => 'Teacher not found or not assigned to a school'
            ];
        }

        $udiseCode = $schoolInfo['udise_sch_code'];
        $schoolCategory = $schoolInfo['sch_category_id'];
        $finYear = getFinancialYear($pdo);
        $schoolType = getSchoolType($schoolCategory);

        // No school type detected - can't determine holiday status
        if (empty($schoolType)) {
            return [
                'canMarkAttendance' => false,
                'message' => 'Unknown school type'
            ];
        }

        // Check holiday conditions in sequence
        $isHoliday = false;
        $holidayReason = '';

        // 1. Check standard school holidays
        if (isSchoolHoliday($pdo, $udiseCode, $schoolType, $finYear)) {
            $isHoliday = true;
            $holidayReason = 'School Holiday';
        }

        // 2. Check local holidays
        if (!$isHoliday && isLocalHoliday($pdo, $udiseCode)) {
            $isHoliday = true;
            $holidayReason = 'Local Holiday';
        }

        // 3. Check if teacher is on leave
        if (!$isHoliday && isTeacherOnLeave($pdo, $teacherId)) {
            $isHoliday = true;
            $holidayReason = 'Teacher on Leave';
        }

        // 4. Check school hours
        if (!$isHoliday && !isWithinSchoolHours()) {
            $isHoliday = true;
            $holidayReason = 'Outside School Hours (8:00 AM - 4:00 PM)';
        }

        // 5. Check if holiday has been overridden
        if ($isHoliday && isHolidayOverridden($pdo, $udiseCode, $finYear)) {
            $isHoliday = false;
            $holidayReason = '';
        }

        return [
            'canMarkAttendance' => !$isHoliday,
            'message' => $holidayReason
        ];
    }

    // Main code execution
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include '../../conn/dbconn.php';
    $logoutTrigger = false;
    $isHoliday = 1;  // Default to holiday (no attendance marking)
    $holidayReason = 'Attendance Marking Time (8 AM till 4 PM Excluding Holidays)';

    if (!isset($_SESSION['username'])) {
        // Session expired, trigger logout
        $logoutTrigger = true;
    } else {
        // Set cookies if needed
        if (!isset($_COOKIE['empcd'])) {
            $defaultValue = $_SESSION['username'];
            setcookie('empcd', $defaultValue, time() + (86400 * 1), "/");
            $_SESSION['empcd'] = $defaultValue;
        } else {
            $_SESSION['empcd'] = $_COOKIE['empcd'];
        }

        if (!isset($_COOKIE['tme'])) {
            $lockDuration = 600 * 60;
            setcookie('tme', $lockDuration, time() + (86400 * 1), "/");
            $_SESSION['attendance_locked'] = $lockDuration;
        } else {
            $_SESSION['attendance_locked'] = $_COOKIE['tme'];
        }

        // Check if attendance can be marked
        $tid = $_SESSION['username'];
        $attendanceStatus = checkAttendanceAccess($pdo, $tid);

        $isHoliday = $attendanceStatus['canMarkAttendance'] ? 0 : 1;
        if (!empty($attendanceStatus['message'])) {
            $holidayReason = $attendanceStatus['message'];
        }
    }
?>
 <script type="text/javascript">
        // This JavaScript runs only if the PHP variable $logoutTrigger is set to true
        <?php if ($logoutTrigger): ?>
            // Prevent default link behavior and submit the logout form
            window.onload = function() {
                event.preventDefault();  // Prevent default action of the link
                document.getElementById('logout-form').submit(); // Submit the logout form
            }
        <?php endif; ?>
    </script>

<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{asset('icon/s2.png')}}" alt="" height="60">
            </span>
            <span class="logo-lg">
                {{-- <img src="assets/images/logo-dark.png" alt="" height="17"> --}}
            </span>
        </a>

        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{asset('icon/s2.png')}}" alt="" height="60">
            </span>
            <span class="logo-lg fs-4 text-white">
                {{-- <img src="{{asset()}}" alt="" height="17"> --}}
                VSK SAMAGRA
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link text-white" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="dashboard-analytics.html" class="nav-link" data-key="t-analytics"> Analytics </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-crm.html" class="nav-link" data-key="t-crm"> CRM </a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
            </ul> --}}

            <li class="nav-item">
                <a class="nav-link menu-link text-white" href="{{ route('vskdashboard') }}"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                </a>
                <hr class="text-white">
                {{-- <a class="nav-link menu-link text-white" href="vskdashboard"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Master Attendance</span>
                </a> --}}


                <li class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->routeIs('students.*') ? 'active text-primary' : 'text-white' }}"
                       href="#sidebarLayouts" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->routeIs('students.*') ? 'true' : 'false' }}"
                       aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">STUDENTS</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->routeIs('students.*') ? 'show' : '' }}" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
{{--                        <a class="nav-link menu-link text-white" href="../../report/stud_list.php"   role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Students List </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link {{ request()->routeIs('students.index') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{ route('students.index') }}" role="button">
                                <i class="ri-drag-move-2-line"></i> <span data-key="t-dashboards">Students List</span>
                            </a>
                            <a class="nav-link menu-link" href="#sidebarLayoutudi" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                               <i class="ri-layout-3-line"></i> <span data-key="t-layouts">Check UDISE+</span> <span class="badge badge-pill bg-danger" data-key="t-hot"></span>
                           </a>
                          <div class="collapse menu-dropdown" id="sidebarLayoutudi">
                            <ul class="nav nav-sm flex-column">

                                <a class="nav-link menu-link text-white"  href="../../attend/stud_udise_pen.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Verify Student ID</span>
                                </a>
                                <a class="nav-link menu-link text-white"  href="../../attend/stud_udise_dropbox.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Student Not Found in VSK</span>
                                </a>
                                <a class="nav-link menu-link text-white"  href="../../attend/stud_udise_add.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Add Student from UDISE into VSK</span>
                                </a>
                                <a class="nav-link menu-link text-white"  href="../../attend/stud_udise_update.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Class/Gender/Social Category/Date of Birth Not Matching with the UDISE on VSK</span>
                                </a>
                                <a class="nav-link menu-link text-white"  href="../../attend/stud_udise_update_father.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Student / Father / Mother Name Not Matching with the UDISE on VSK</span>
                                </a>
                            </ul>
                            </div>
                            <a class="nav-link menu-link {{ request()->routeIs('students.correction') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{ route('students.correction') }}" role="button">
                                <i class="ri-drag-move-2-line"></i> <span data-key="t-dashboards">Data Correction</span>
                            </a>

{{--                              <a class="nav-link menu-link text-white" href="../../attend/stud_correct_all.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Data Correction </span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_pen_modify.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Student PEN Modification</span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_pstatus.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Present Status</span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_dropbox.php"   role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class="ri-drag-move-fill"></i> <span data-key="t-dashboards">Student DropBox </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link {{ request()->routeIs('students.dropbox') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{ route('students.dropbox') }}" role="button">
                                <i class="ri-drag-move-2-line"></i> <span data-key="t-dashboards">Student Dropbox</span>
                            </a>

                            <a class="nav-link menu-link {{ request()->routeIs('students.status') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{ route('students.status') }}" role="button">
                                <i class="ri-drag-move-2-line"></i> <span data-key="t-dashboards">present Status</span>
                            </a>

{{--                             <a class="nav-link menu-link text-white" href="../../attend/stud_trf.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Transfer One Student from other School </span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="https://vskmizoram.com/tms/public/getProfiles"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Student Search (By Name) </span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../report/stud_pen_search.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Student Search (By PEN ID) </span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_prom_demot.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Promote/Retain </span>--}}
{{--                            </a>--}}
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_shift.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Transfer All the Students from Other School </span>--}}
{{--                            </a>--}}

{{--                            <a class="nav-link menu-link text-white" href="../../attend/student_add.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Add Students </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link {{ request()->routeIs('students.add') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{ route('students.add') }}" role="button">
                                <i class="ri-drag-move-2-line"></i> <span data-key="t-dashboards">Add Students</span>
                            </a>
                            <?php if ($isHoliday == 0): ?>
                            <a class="nav-link menu-link {{ request()->routeIs('students.attendance') ? 'active bg-soft-primary text-primary' : 'text-white' }}"
                               href="{{route('students.attendance')}}" role="button">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Mark Attendance</span>
                            </a>
                            <?php else: ?>
                            <a class="nav-link menu-link text-white" href="#" role="button">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards"><?= $holidayReason ?></span>
                            </a>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayout1" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">TEACHERS</span> <span class="badge badge-pill bg-danger" data-key="t-hot"></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayout1">
                        <ul class="nav nav-sm flex-column">
                        <?php if ($isHoliday == 0): ?>
                              <!--  <a class="nav-link menu-link text-white" href="../../tchmgmt/tch_attend.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Mark Attendance - 1 </span>
                            </a>   -->

                            <a class="nav-link menu-link text-white" href="../../tchmgmt/tch_attend_dmeter2.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Mark Attendance</span>
                            </a>


                            <?php else: ?>
                            <a class="nav-link menu-link text-white" href="#" role="button">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards"><?= $holidayReason ?></span>
                            </a>
                            <?php endif; ?>


                            <a class="nav-link menu-link text-white" href="../../report/tch_leave_register.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Attendance Register</span>
                            </a>
                        </ul>
                    </div>
                </li>
                 <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayout" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">LIFE SKILL SESSION MONITORING(Magic Bus)</span> <span class="badge badge-pill bg-danger" data-key="t-hot"></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayout">
                        <ul class="nav nav-sm flex-column">
                        <a class="nav-link menu-link text-white" href="../../magicbus/index.php" target = _blank role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Add Session</span>
                            </a>

                        </ul>
                    </div>
                </li>
            </li> <!-- end Dashboard Menu -->
        </ul>
        </div>

    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
