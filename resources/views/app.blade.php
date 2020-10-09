<!DOCTYPE html>
<script>
  var login = '{{ url('login') }}';
  var site_url = window.location.origin;  
</script>
<?php 
  if(!Session::get('login') or Session::get('login')==false)
  {
    echo "<script>window.location = login; </script>";
  }
?>
<html lang="en">
  <style type="text/css">
    #mydiv {  
      position:fixed;
      top:0;
      left:0;
      width:100%;
      height: auto !important;
      min-height: 100%;
      z-index:10002;
      background-color:white;
      opacity: .8;
    }

    .ajax-loader {
      position: absolute;
      left: 50%;
      top: 50%;
      margin-left: -32px; /* -1 * image width / 2 */
      margin-top: -32px;  /* -1 * image height / 2 */
      display: block;     
    }
  </style>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Insentif Koja | Dashboard !</title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('/gentelella/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ URL::asset('/gentelella/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ URL::asset('/gentelella/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
	 
    <!-- Datatables -->
    <!-- <link href="{{ URL::asset('/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet"> -->

    <!-- bootstrap-progressbar -->
    <link href="{{ URL::asset('/gentelella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{ URL::asset('/gentelella/vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{ URL::asset('/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
<!--     <link href="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.css') }}../" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.buttons.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.nonblock.css') }}" rel="stylesheet"> -->
    <!-- Custom Theme Style -->
    <link href="{{ URL::asset('/gentelella/build/css/custom.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/sweetalert/sweetalert/dist/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/angular-ngprogress/ngProgress.css') }}" rel="stylesheet">    
    <link href="{{ URL::asset('/please-wait-master/build/please-wait.css') }}" rel="stylesheet">    


  </head>

  <body class="nav-md" ng-app="insentif_koja">
    <div id="mydiv">
        <img src="{{ URL::asset('/loading/DoubleRing.gif') }}" class="ajax-loader"/>
    </div>
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="/#/" class="site_title"><i class="fa fa-money"></i> <span>Insentif Koja</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="{{ URL::asset('/gentelella/production/images/img.jpg') }}" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>{{ Session::get('priv') }}</span>
                <h2>{{ Session::get('name') }}</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a href="/#/"><i class="fa fa-home"></i> Home</a>
                  </li>
                  @if(Session::get('priv_id') == '1' || Session::get('priv_id') == '2')
                  <li><a><i class="fa fa-edit"></i>Master<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/#/grouplist">Master Group</a></li>
                      <li><a href="/#/divisionlist">Master Divisi</a></li>
                      <li><a href="/#/positionlist">Master Position</a></li>
                      <li><a href="/#/employeelist">Master Employee</a></li>
                      @if(Session::get('priv_id') == '1')
                      <li><a href="/#/userlist">Master User</a></li>
                      @endif
                      <!-- <li><a href="/#/adjustparam">Master Adjust Param</a></li> -->
                    </ul>
                  </li>
                  @endif
                  <li><a href="/#/inputinsentif"><i class="fa fa-money"></i>Input Insentif</a></li>
                  <li><a href="/#/postedinsentif"><i class="fa fa-upload"></i>Posted Insentif</a></li>
                  @if(Session::get('priv_id') == '1' || Session::get('priv_id') == '2')
                  <li><a href="/#/adjustparam"><i class="fa fa-gear"></i>Setting Adjust Param</a></li>
                  @endif
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

            
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="{{ URL::asset('/gentelella/production/images/img.jpg') }}" alt="">{{ Session::get('name') }}
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="{{ URL::to('doLogout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown">
                  <!-- <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a> -->
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col inner" role="main">
        	<ng-view></ng-view> {{--menampilkan laman dinamis--}}
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            2017
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="{{ URL::asset('/gentelella/vendors/jquery/dist/jquery.min.js') }}"></script>
    {{-- Angular JS --}}
  	<script src="{{ URL::asset('/js/angular.min.js') }}"></script>  
  	<script src="{{ URL::asset('/js/angular-route.min.js') }}"></script>
    {{-- SWAL --}}
    <script src="{{ URL::asset('/sweetalert/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('/sweetalert/ngSweetAlert/SweetAlert.js') }}"></script>
    {{-- NG PROGRESS --}}
    <script src="{{ URL::asset('/angular-ngprogress/ngProgress.min.js') }}"></script>

    {{-- ANGULAR UPLOAD --}}
    <script src="{{ URL::asset('ng-upload/ng-file-upload-shim.js') }}"></script>
    <script src="{{ URL::asset('ng-upload/ng-file-upload.js') }}"></script>

    {{-- PLEASE WAIT --}}
    <script src="{{ URL::asset('please-wait-master/build/please-wait.min.js') }}"></script>    

  	{{-- App --}}
  	<script src="{{ URL::asset('/app/routes.js') }}"></script>	
  	<script src="{{ URL::asset('/app/packages/dirPagination.js') }}"></script>
  	<script src="{{ URL::asset('/app/services/myServices.js') }}"></script>
  	<script src="{{ URL::asset('/app/helper/myHelper.js') }}"></script>
  	{{-- App Controller --}}
  	<script src="{{ URL::asset('/app/controllers/AdminController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/GroupController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/DivisionController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/PositionController.js') }}"></script>
  	<script src="{{ URL::asset('/app/controllers/EmployeeController.js') }}"></script>
  	<script src="{{ URL::asset('/app/controllers/UserController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/InputInsentifController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/AdjustParamController.js') }}"></script>
    <script src="{{ URL::asset('/app/controllers/PostedInsentifController.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ URL::asset('/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ URL::asset('/gentelella/vendors/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ URL::asset('/gentelella/vendors/nprogress/nprogress.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ URL::asset('/gentelella/vendors/Chart.js/dist/Chart.min.js') }}"></script>
    <!-- gauge.js -->
    <script src="{{ URL::asset('/gentelella/vendors/gauge.js/dist/gauge.min.js') }}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{ URL::asset('/gentelella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ URL::asset('/gentelella/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- jQuery Smart Wizard -->
    <script src="{{ URL::asset('/gentelella/vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js') }}"></script>
    <!-- Mask Money -->
    <script src="{{ URL::asset('/jquery-maskmoney-master/dist/jquery.maskMoney.js') }}"></script>
    <!-- PNotify 
    <script src="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.js') }}../"></script>
    <script src="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.buttons.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/pnotify/dist/pnotify.nonblock.js') }}"></script> -->
    <!-- Skycons -->
    <script src="{{ URL::asset('/gentelella/vendors/skycons/skycons.js') }}"></script>
    <!-- Flot -->
    <script src="{{ URL::asset('/gentelella/vendors/Flot/jquery.flot.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/Flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/Flot/jquery.flot.time.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/Flot/jquery.flot.stack.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/Flot/jquery.flot.resize.js') }}"></script>

    <!-- Datatables -->
    <!-- <script src="{{ asset('/gentelella/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/jszip/dist/jszip.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/gentelella/vendors/pdfmake/build/vfs_fonts.js') }}"></script> -->

    <!-- Flot plugins -->
    <script src="{{ URL::asset('/gentelella/vendors/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/flot.curvedlines/curvedLines.js') }}"></script>
    <!-- DateJS -->
    <script src="{{ URL::asset('/gentelella/vendors/DateJS/build/date.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ URL::asset('/gentelella/vendors/jqvmap/dist/jquery.vmap.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="{{ URL::asset('/gentelella/vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- Custom Theme Scripts -->
    <script src="{{ URL::asset('/gentelella/build/js/custom.min.js') }}"></script>    
	
  </body>
</html>
