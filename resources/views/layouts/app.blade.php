<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/typicons.font/font/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject --> 
    <!-- plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Lobibox/lobibox.css') }}"/>
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <!-- End plugin css for this page -->  
    <link rel="shortcut icon" href="{{ asset('images/doh-logo.png') }}" />
    <!-- Scripts -->
    <!-- datatables -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <!-- datarangepicker -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/daterangepicker-master/daterangepicker.css') }}">
    <style>
      html {
         font-size: 13px; /* Increasing this will scale everything using rem or em */
      }
   </style>
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    @yield('css')

</head>
<body>
    <div id="app" >
        <div class="container-scroller" >
            @include('layouts.partials._navbar')
            <div class="container-fluid page-body-wrapper">
                {{-- @include('layouts.partials._settings-panel') --}}
                @include('layouts.partials._sidebar')
                <div class="main-panel">   
                    <div class="content-wrapper">
                        <div class="text-center p-2" style="background-color: #59ab91;width:100%;margin-bottom:30px;">
                            <img src="{{ asset('images/banner_maif_2024.png') }}" alt="banner"/>  
                        </div>   
                        <div class="row">
                            @yield('content')
                        </div>
                    </div>
                    @include('layouts.partials._footer')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>

    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/todolist.js') }}"></script>
    <script src="{{ asset('admin/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('Lobibox/lobibox.js?v=').date('His') }}"></script>
    
    <!-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
      <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
      <script>
         Pusher.logToConsole = true;
         var pusher = new Pusher('f19cadb6835e985350a0', {
            cluster: 'ap1'
         });
         var channel = pusher.subscribe('my-channel');
         channel.bind('form-submitted', function(data) {
            if (data && data.post && data.post.author && data.post.title) {
               toastr.success('New Post Created', 'Author: ' + data.post.author + '<br>Title: ' + data.post.title, {
                  timeOut: 0,  
                  extendedTimeOut: 0,  
               });
            } else {
            console.error('Invalid data structure received:', data);
            }
         });
         // toastr.success( 'The system is able to send mails now.','Reminders:', {
         //    timeOut: 1000000,  
         //    extendedTimeOut: 1000000, 
         //    closeButton: true, 
         //    progressBar: true,  
         //    positionClass: 'toast-top-right' 
         // });
    </script> -->

    <script>
        var path_gif = "{{ asset('images/loading.gif') }}";
        var loading = '<center><img src="'+path_gif+'" alt=""></center>';
      
        @if(session('facility_save'))
             <?php session()->forget('facility_save'); ?>
             Lobibox.notify('success', {
                msg: 'Successfully saved Facility!'
             });
        @endif
        @if(session('patient_update'))
            <?php session()->forget('patient_update'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully updated patient!'
            });
        @endif
        @if(session('patient_save'))
            <?php session()->forget('patient_save'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully saved patient!'
            });
        @endif
        @if(session('fundsource_save'))
            <?php session()->forget('fundsource_save'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully saved Fund Source!'
            });
        @endif
        @if(session('fundsource_update'))
            <?php session()->forget('fundsource_update'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully update Fund Source!'
            });
        @endif
        @if(session('dv_create'))
           <?php session()->forget('dv_create'); ?>
           Lobibox.notify('success', {
              msg: 'Disbursement was Created!'
           });
        @endif
        @if(session('dv_update'))
           <?php session()->forget('dv_create'); ?>
           Lobibox.notify('success', {
              msg: 'Disbursement was updated!'
           });
        @endif
        @if(session('fund_transfer'))
           <?php session()->forget('fund_transfer'); ?>
           Lobibox.notify('success', {
              msg: 'Funds successfuly transferred!'
           });
        @endif
        @if(session('actual_amount'))
           <?php session()->forget('actual_amount'); ?>
           Lobibox.notify('success', {
              msg: 'Actual Amount successfuly updated!'
           });
        @endif
        @if(session('save_group'))
           <?php session()->forget('save_group'); ?>
           Lobibox.notify('success', {
              msg: 'Group successfuly saved!'
           });
        @endif
        @if(session('create_dv2'))
           <?php session()->forget('save_group'); ?>
           Lobibox.notify('success', {
              msg: 'Disbursement Voucher V2 successfuly created!'
           });
        @endif
        @if(session('email_sent'))
            <?php session()->forget('email_sent'); ?>
            Lobibox.notify('success', {
              msg: 'Sucessfully sent an email!'
           });
        @endif
        @if(session('email_unsent'))
        <?php session()->forget('email_unsent'); ?>
            window.close();
            Lobibox.notify('error', {
              msg: 'Cannot send an email, please provide correct email!'
           });
        @endif
        @if(session('save_patientgroup'))
           <?php session()->forget('save_patientgroup'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully added a patient in a group!'
           });
        @endif
        @if(session('update_group'))
           <?php session()->forget('update_group'); ?>
           Lobibox.notify('success', {
              msg: 'Please update disbursement voucher!'
           });
        @endif
        @if(session('remove_patientgroup'))
           <?php session()->forget('remove_patientgroup'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed a message!'
           });
        @endif
        @if(session('breakdowns_created'))
           <?php session()->forget('breakdowns_created'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully created a breakdowns!'
           });
        @endif
        @if(session('obligate'))
           <?php session()->forget('obligate'); ?>
           Lobibox.notify('success', {
              msg: 'Dv was successfully obligated!'
           });
        @endif
        @if(session('saa_exist'))
           <?php session()->forget('saa_exist'); ?>
           Lobibox.notify('error', {
              msg: 'Saa exists already!'
           });
        @endif
        @if(session()->has('patient_exist'))
            <?php $patientCount = session('patient_exist'); ?>
            <?php session()->forget('patient_exist'); ?>
            Lobibox.notify('success', {
               msg: 'Successfully added a patient! This patient has been added {{ $patientCount}} time(s).'
            });
         @endif
         @if(session('pay_dv'))
           <?php session()->forget('pay_dv'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully paid the disbursement!'
           });
        @endif
        @if(session('releaseAdded'))
           <?php session()->forget('releaseAdded'); ?>
         //   <div class="alert alert-success">
         //       <i class="fa fa-check"></i> Successfully released!
         //    </div>
           Lobibox.notify('success', {
              msg: ' Successfully released! '
           });
        @endif
        @if(session('add_dvno'))
           <?php session()->forget('add_dvno'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully add dv no!'
           });
        @endif
        @if(session('update_dv2'))
           <?php session()->forget('update_dv2'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully update dv2!'
           });
        @endif
        @if(session('add_deductions'))
           <?php session()->forget('add_deductions'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully added a usage in administrative cost!'
           });
        @endif
        @if(session('upload_files'))
           <?php session()->forget('upload_files'); ?>
           Lobibox.notify('success', {
              msg: 'File uploaded successfully!'
           });
        @endif
        @if(session('dv2_remove'))
           <?php session()->forget('dv2_remove'); ?>
           Lobibox.notify('error', {
              msg: 'Disbursement voucher version 2 successfully removed!'
           });
        @endif
        @if(session('dv_remove'))
           <?php session()->forget('dv_remove'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed Disbursement Voucher!'
           });
        @endif
        @if(session('remove_patient'))
           <?php session()->forget('remove_patient'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed the patient!'
           });
        @endif
        @if(session('update_fac'))
           <?php session()->forget('update_fac'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully updated facility list!'
           });
        @endif
        @if(session('update_proponent'))
           <?php session()->forget('update_proponent'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully updated the proponent!'
           });
        @endif
        @if(session('unreachable'))
           <?php session()->forget('unreachable'); ?>
           Lobibox.notify('error', {
              msg: 'Cannot find proponent!'
           });
        @endif
        @if(session('dv3'))
           <?php session()->forget('dv3'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully created disbursement voucher version 3!'
           });
        @endif
        @if(session('dv3_update'))
           <?php session()->forget('dv3_update'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully updated this disbursement voucher version 3!'
           });
        @endif
        @if(session('dv3_obligate'))
           <?php session()->forget('dv3_obligate'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully obligated this disbursement voucher version 3!'
           });
        @endif
        @if(session('dv3_paid'))
           <?php session()->forget('dv3_paid'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully paid this disbursement voucher version 3!'
           });
        @endif
        @if(session('note'))
           <?php session()->forget('note'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully created a note!'
           });
        @endif
        @if(session('note_update'))
           <?php session()->forget('note_update'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully updated this note!'
           });
        @endif
        @if(session('note_delete'))
           <?php session()->forget('note_delete'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed this note!'
           });
        @endif
        @if(session('dv3_remove'))
           <?php session()->forget('dv3_remove'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed this disbursement voucher v3!'
           });
        @endif
        @if(session('notes_update'))
           <?php session()->forget('notes_update'); ?>
           Lobibox.notify('success', {
              msg: 'Done!'
           });
        @endif
        @if(session('update_remarks'))
           <?php session()->forget('update_remarks'); ?>
           Lobibox.notify('success', {
              msg: 'Done!'
           });
        @endif
        @if(session('pre_dv'))
           <?php session()->forget('pre_dv'); ?>
           Lobibox.notify('success', {
              msg: 'Success!'
           });
        @endif
        @if(session('pre_dv_error'))
           <?php session()->forget('pre_dv_error'); ?>
           Lobibox.notify('error', {
              msg: 'Data not found!'
           });
        @endif
        @if(session('remove_pre_dv'))
           <?php session()->forget('remove_pre_dv'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed!'
           });
        @endif
        @if(session('pre_dv_update'))
           <?php session()->forget('pre_dv_update'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully updated!'
           });
        @endif
        @if(session('pre_dv_remove'))
           <?php session()->forget('pre_dv_remove'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully removed!'
           });
        @endif
        @if(session('process_bills'))
           <?php session()->forget('process_bills'); ?>
           Lobibox.notify('succes', {
              msg: 'Successfully process!'
           });
        @endif
        @if(session('facility_send'))
           <?php session()->forget('facility_send'); ?>
           Lobibox.notify('succes', {
              msg: 'Patient was successfully send to facility!'
           });
        @endif
        @if(session('return_gl'))
           <?php session()->forget('return_gl'); ?>
           Lobibox.notify('error', {
              msg: 'Return patient successfully!'
           });
        @endif
        @if(session('activate_user'))
           <?php session()->forget('activate_user'); ?>
           Lobibox.notify('success', {
              msg: 'User was successfully activated!'
           });
        @endif
        @if(session('not_found'))
           <?php session()->forget('not_found'); ?>
           Lobibox.notify('error', {
              msg: 'User was not found!'
           });
        @endif
        @if(session('logbook'))
           <?php session()->forget('logbook'); ?>
           Lobibox.notify('success', {
              msg: 'Successfully added into log!'
           });
        @endif
        @if(session('trans_return'))
           <?php session()->forget('trans_return'); ?>
           Lobibox.notify('error', {
              msg: 'Successfully return the transmittal!'
           });
        @endif
    </script>
    @yield('js')
</body>
</html>
