<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
      include '../../conn/dbconn.php';
      if (!isset($_SESSION['username'])) {

        $isHoliday = 1;
            // If the session variable is not set, we'll use JavaScript to trigger the logout process
            $logoutTrigger = true;
        }   else {
            if (!isset($_COOKIE['empcd'])) {
                $defaultValue =   $_SESSION['username']  ;
                setcookie('empcd', $defaultValue, time() + (86400 * 1), "/"); // Expires in 30 days
                $_SESSION['empcd'] = $defaultValue;
            }  else
            {
                $_SESSION['empcd'] = $_COOKIE['empcd'];
            }

            if (!isset($_COOKIE['tme'])) {
                $lockDuration = 600 * 60;
                setcookie('tme', $lockDuration, time() + (86400 * 1), "/"); // Expires in 30 days
                $_SESSION['attendance_locked'] = $lockDuration;
            }   else {

                 $_SESSION['attendance_locked'] = $_COOKIE['tme'];
            }

            $logoutTrigger = false;
            $tid = $_SESSION['username'];

            $tch = $pdo->prepare("select a.udise_sch_code ,a.sch_category_id from mizoram115.school_master a, sms.tch_profile b  where a.udise_sch_code = b.udise_sch_code and trim(b.slno) = trim(:tchd)");
            $tch->execute(['tchd' => $tid]);
            $rw = $tch->fetchAll(PDO::FETCH_ASSOC);
            $isHoliday = 0;
            foreach($rw as $r1) {

                $udisecd = $r1['udise_sch_code'];
                $cat = $r1['sch_category_id'];

                $fyr = $pdo->query("select max(finyr) as finyr from sms.finyr  ")->fetchAll(PDO::FETCH_ASSOC);
                $yr ='';

                foreach ($fyr as $row1) {
                    $yr =   $row1['finyr']  ;
                }
                $typ ='';
                if ($cat == 1 || $cat == 2 || $cat == 3 || $cat == 6 || $cat == 12) {
                    $typ = 'PS';
                }else if ($cat == 2 || $cat == 3 || $cat == 4 || $cat == 5 || $cat == 6|| $cat == 7) {
                    $typ = 'MS';
                }else if ($cat == 3 || $cat == 5 || $cat == 6|| $cat == 7 || $cat == 8|| $cat == 10) {
                    $typ = 'SS';
                }else if ($cat == 3 || $cat == 5 || $cat == 10|| $cat == 11 ) {
                    $typ = 'SS';
                }

                if (!empty($typ)){
                    $dy = '';
                    $hol = $pdo->prepare("select  holid_ay from sms.school_holiday where finyr = :fy and da_te = :cd and (sch_typ = :ty or sch_typ = 'SU' or sch_typ = 'SA') limit 1");
                    $hol->execute(['fy' => $yr,'cd'=>date('Y-m-d'),'ty'=>$typ]);
                    $hl = $hol->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($hl as $hls) {
                        $dy =   $hls['holid_ay']  ;
                    }
                    if (!empty($dy)) {
                        $isHoliday = 1;
                    }  else
                    {
                        $isHoliday = 0;
                    }

                    if ($isHoliday == 0) {

                        $loc = $pdo->prepare("select  holid_ay from sms.local_holiday where :dt between from_dt and till_dt and udise_cd = :uid limit 1");
                        $loc->execute(['uid' => $udisecd,'dt'=>date('Y-m-d')]);
                        $ll = $loc->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($ll as $hll) {
                            $dy =   $hll['holid_ay']  ;
                        }
                        if (!empty($dy)) {
                            $isHoliday = 1;
                        }  else
                        {
                            $isHoliday = 0;
                        }
                    }
                }

            $ck = 0;

            if ($isHoliday == 0) {
                $lvmgt = $pdo->prepare("select count(*) as nsc from sms.leave_mgmt where   slno = :sln and to_char(current_date,'yyyy-mm-dd') between from_dt and till_dt and pres_status = 'SH'");
                $lvmgt->execute(['sln'=>$tid]);
                $lvm = $lvmgt->fetchAll();
                foreach ($lvm as $hls1) {
                    $ck =   $hls1['nsc']  ;
                }

                if ($ck > 0) {
                    $isHoliday = 1;
                } else
                {
                    $isHoliday = 0;
                }
            }

            if ($isHoliday == 0) {
                date_default_timezone_set('Asia/Kolkata');
                $startTime = "8:00";
                $endTime = "16:00";
                $startDateTime = DateTime::createFromFormat('H:i', $startTime);
                $endDateTime = DateTime::createFromFormat('H:i', $endTime);

                $currentDateTime = new DateTime();



                if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
                    $isHoliday = 0;
                }  else
                {
                    $isHoliday = 1;
                }
            }

                if ($isHoliday == 1) {
                    $hol = $pdo->prepare("select count(*) as prs from sms.skip_holiday where finyr = :fy and da_te = :cd and udise_cd = :ucd ");
                    $hol->execute(['fy' => $yr,'cd'=>date('Y-m-d'),'ucd'=>$udisecd]);
                    $hl = $hol->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($hl as $hls) {
                        $ck =   $hls['prs']  ;

                    }
                    if (!empty($ck)) {
                        $isHoliday = 0;
                    }  else
                    {
                        $isHoliday = 1;
                    }
                }


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
                <a class="nav-link menu-link text-white" href="vskdashboard"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                </a>
                <hr class="text-white">
                {{-- <a class="nav-link menu-link text-white" href="vskdashboard"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Master Attendance</span>
                </a> --}}


                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">STUDENTS</span> <span class="badge badge-pill bg-danger" data-key="t-hot"></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
{{--                        <a class="nav-link menu-link text-white" href="../../report/stud_list.php"   role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Students List </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link text-white" href="{{ route('students.index') }}"   role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Students List </span>
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

                            <a class="nav-link menu-link text-white" href="{{ route('students.correction') }}"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Data Correction </span>
                            </a>

{{--                              <a class="nav-link menu-link text-white" href="../../attend/stud_correct_all.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Data Correction </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link text-white" href="../../attend/stud_pen_modify.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Student PEN Modification</span>
                            </a>
                            <a class="nav-link menu-link text-white" href="../../attend/stud_pstatus.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Present Status</span>
                            </a>
{{--                            <a class="nav-link menu-link text-white" href="../../attend/stud_dropbox.php"   role="button" aria-expanded="false" aria-controls="sidebarDashboards">--}}
{{--                                <i class="ri-drag-move-fill"></i> <span data-key="t-dashboards">Student DropBox </span>--}}
{{--                            </a>--}}
                            <a class="nav-link menu-link text-white" href="{{ route('student-dropbox') }}"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Student Dropbox </span>
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

                            <a class="nav-link menu-link text-white" href="../../attend/student_add.php"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-drag-move-2-line"></i> <span data-key="t-dashboards">Add Students </span>
                            </a>
                            <?php if ($isHoliday == 0): ?>
                                <a class="nav-link menu-link text-white" href="{{route('openstudentattendance')}}"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards">Mark Attendance </span>
                                </a>
                              <?php else : ?>
                                <?php if (empty($dy)) {
                                       $dy = 'Attenance Marking Time (8 AM till 4 PM Excluding Holidays)';
                                  } ?>

                                    <a class="nav-link menu-link text-white" href="#"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                          <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards"><?= $dy ?></span>
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


                            <?php else : ?>
                                <?php if (empty($dy)) {
                                       $dy = 'Attenance Marking Time (8 AM till 4 PM Excluding Holidays)';
                                  } ?>

                                <a class="nav-link menu-link text-white" href="#"  role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-mark-pen-line"></i> <span data-key="t-dashboards"><?= $dy ?> </span>
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
