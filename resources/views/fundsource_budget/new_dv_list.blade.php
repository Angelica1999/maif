<?php
use App\Models\TrackingMaster;
use App\Models\TrackingDetails; 
?>
@extends('layouts.app')
@section('content')
<style>
    .doc-viewer-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
        overflow: hidden;
    }

    .swal2-container {
        z-index: 10000 !important;
    }

    .swal2-backdrop-show {
        z-index: 9999 !important;
    }

    .doc-viewer-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .doc-viewer-container {
        position: relative;
        width: 90%;
        height: 90%;
        max-width: 1200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .doc-viewer-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .doc-viewer-content img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        user-select: none;
    }

    .doc-viewer-content iframe {
        width: 100%;
        height: 95%;
        border: none;
        background: white;
    }

    .doc-viewer-close {
        position: absolute;
        top: 60px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        transition: color 0.3s;
    }

    .doc-viewer-close:hover {
        color: #ff4444;
    }

    .doc-viewer-delete {
        position: absolute;
        top: 70px;
        right: 90px;
        color: white;
        font-size: 15px;
        cursor: pointer;
        z-index: 1001;
        transition: all 0.3s;
        background: rgba(255, 68, 68, 0.3);
        padding: 10px 20px;
        border-radius: 5px;
        border: 2px solid rgba(255, 68, 68, 0.5);
    }

    .doc-viewer-delete:hover {
        background: rgba(255, 68, 68, 0.6);
        border-color: #ff4444;
        transform: scale(1.05);
    }

    .doc-viewer-delete:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .doc-viewer-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 50px;
        cursor: pointer;
        padding: 20px;
        user-select: none;
        transition: all 0.3s;
        z-index: 1001;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 5px;
    }

    .doc-viewer-nav:hover {
        background: rgba(0, 0, 0, 0.8);
        color: #4CAF50;
    }

    .doc-viewer-prev {
        left: 20px;
    }

    .doc-viewer-next {
        right: 20px;
    }

    .doc-viewer-nav.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .doc-viewer-info {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        background: rgba(0, 0, 0, 0.7);
        padding: 15px 30px;
        border-radius: 25px;
        z-index: 1001;
        text-align: center;
    }

    .doc-viewer-info .doc-name {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .doc-viewer-info .doc-counter {
        font-size: 14px;
        opacity: 0.8;
    }

    .doc-viewer-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 50px;
    }

    .doc-viewer-loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .doc-viewer-unsupported {
        color: white;
        text-align: center;
        padding: 40px;
    }

    .doc-viewer-unsupported i {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .doc-viewer-unsupported h3 {
        margin-bottom: 15px;
    }

    .doc-viewer-unsupported .btn-download {
        margin-top: 20px;
        padding: 12px 30px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }

    .doc-viewer-unsupported .btn-download:hover {
        background: #45a049;
    }

    .doc-viewer-message {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 30px;
        border-radius: 5px;
        z-index: 10001;
        font-size: 16px;
        font-weight: bold;
        animation: slideDown 0.3s ease-out;
    }

    .doc-viewer-message.success {
        background: #4CAF50;
        color: white;
    }

    .doc-viewer-message.error {
        background: #ff4444;
        color: white;
    }

    .doc-viewer-message.fade-out {
        animation: fadeOut 0.3s ease-out;
        opacity: 0;
    }

    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Facility/Route No" value="{{ $keyword }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                        <button type="button" id="release_btn" data-target="#releaseTo" style="display:none; background:teal; color:white" onclick="putRoutes($(this))" data-target="#releaseTo" data-backdrop="static" data-toggle="modal" class="btn btn-md">Release All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">PRE - DV (v2)</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="table-responsive">
            @if(count($results) > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="padding:5px; min-width:150px"></th>
                            <th style="min-width:100px">Route</th>
                            <th>Facility</th>
                            <th>Proponent</th>
                            <th style="min-width:100px">Grand Total</th>
                            <th>Created By</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $row)
                            <tr>
                                <td class="td" style="padding: 12px; text-align: center; max-width:300px">
                                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                        <button type="button"  class="btn btn-sm"  style="background: linear-gradient(135deg, #165A54 0%, #1a6e66 100%); width:80px; color: white;
                                            border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(22, 90, 84, 0.2);"
                                            data-toggle="modal" href="#iframeModal" data-routeId="{{$row->new_dv->route_no}}" id="track_load" onclick="openModal()"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(22, 90, 84, 0.3)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(22, 90, 84, 0.2)';">
                                            <i class="fa fa-map-marker" style="margin-right: 6px;"></i>Track
                                        </button>
                                        <a href="{{ route('new_dv.pdf', ['id' => $row->id]) }}" target="_blank" type="button" class="btn btn-sm"
                                            style="background: linear-gradient(135deg, #1B5E20 0%, #2aa02a 100%); color: white; width:80px; border: none; border-radius: 6px; 
                                                padding: 8px 16px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease;
                                                box-shadow: 0 2px 4px rgba(34, 139, 34, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(34, 139, 34, 0.3)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(34, 139, 34, 0.2)';">
                                            <i class="fa fa-print" style="margin-right: 6px;"></i>Print
                                        </a>

                                        @if(in_array($type, ["disbursed","deffered"]))
                                            <button class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #1ab5ce 100%); color: white; border: none;
                                                width:100px; border-radius: 6px; padding: 8px 16px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2);"
                                                onclick="loadAndViewDocuments('{{ $row->new_dv->route_no }}')" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(23, 162, 184, 0.3)';"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(23, 162, 184, 0.2)';">
                                                <i class="fa fa-eye" style="margin-right: 6px;"></i>LDDAP
                                            </button>
                                            <button type="button"  class="btn btn-sm" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%); color: white;
                                                border: none; width:100px; border-radius: 6px; padding: 8px 16px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(27, 67, 50, 0.2);"
                                                onclick="lddap('{{ $row->new_dv?$row->new_dv->route_no :0 }}')" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(27, 67, 50, 0.3)';"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(27, 67, 50, 0.2)';">
                                            <i class="fa fa-upload" style="margin-right: 6px;"></i>LDDAP
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td><a class="text-info" data-toggle="modal" data-backdrop="static" onclick="viewV1('{{ $row->new_dv->route_no }}', {{ $row->id }}, {{ $row->new_dv->id }}, '{{ $row->new_dv->confirm }}')">{{ $row->new_dv->route_no }}</a></td>
                                <td class="td">{{ $row->facility->name }}</td>
                                <td class="td">
                                    @foreach($row->extension as $index => $data)
                                        {{ $data->proponent->proponent }}
                                        {{ $index < count($row->extension) - 1 ? ',' : '' }}
                                        {!! ($index + 1) % 3 == 0 ? '<br>' : '' !!}
                                    @endforeach
                                </td>
                                <td class="td">{{ number_format($row->grand_total,2,'.',',') }}</td>
                                <td class="td">{{ $row->user->lname .', '.$row->user->fname }}</td>
                                <td class="td">{{ date('F j, Y', strtotime($row->new_dv->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No data found!</strong>
                </div>
            @endif
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $results->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view_v2" role="dialog" style="overflow-y:scroll;">
    <div class="modal-dialog modal-lg" role="document" style="width:1000px">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17c964;padding:15px; color:white">
                <h4 class="modal-title"><i class="fa fa-plus" style="margin-right:auto;"></i>DV ( new version )</h4>
                <button type="button" class="close" id="exit" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="pre_body" style="display: flex; flex-direction: column; align-items: center; padding:15px">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Upload Documents</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Documents</label>
                        <input type="file" name="documents[]" id="documents" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">You can select multiple files</small>
                    </div>
                    <input type="hidden" name="route_no" id="route_no" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('modal')
@endsection
@section('js')
<script src="{{ asset('admin/vendors/sweetalert2/sweetalert2.js?v=1') }}"></script>
<script>

    var currentDocuments = [];
    var currentDocIndex = 0;

    function openDocViewer(documents, startIndex = 0) {
        currentDocuments = documents;
        currentDocIndex = startIndex;
        
        var modal = document.getElementById('docViewerModal');
        modal.classList.add('active');
        
        displayDocument();
        updateNavButtons();
        
        document.addEventListener('keydown', handleKeyPress);
    }

    function closeDocViewer() {
        var modal = document.getElementById('docViewerModal');
        modal.classList.remove('active');
        
        document.removeEventListener('keydown', handleKeyPress);
    }

    function displayDocument() {
        var content = document.getElementById('docViewerContent');
        var nameEl = document.getElementById('docViewerName');
        var counterEl = document.getElementById('docViewerCounter');
        
        var doc = currentDocuments[currentDocIndex];
        
        nameEl.textContent = doc.display_name;
        counterEl.textContent = `${currentDocIndex + 1} of ${currentDocuments.length}`;
        
        content.innerHTML = '<div class="doc-viewer-loading"><i class="fas fa-spinner"></i></div>';
        
        var extension = doc.extension.toLowerCase();
        
        if(['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
            var img = new Image();
            img.onload = function() {
                content.innerHTML = '';
                content.appendChild(img);
            };
            img.onerror = function() {
                showUnsupported(doc);
            };
            img.src = doc.url;
            
        } else if (extension === 'pdf') {
            content.innerHTML = `<iframe src="${doc.url}"></iframe>`;
            
        } else {
            showUnsupported(doc);
        }
    }

    function showUnsupported(doc) {
        var content = document.getElementById('docViewerContent');
        content.innerHTML = `
            <div class="doc-viewer-unsupported">
                <i class="fas fa-file"></i>
                <h3>Preview not available</h3>
                <p>This file type (${doc.extension.toUpperCase()}) cannot be previewed in the browser.</p>
                <a href="${doc.url}" download class="btn-download">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
        `;
    }

    function deleteCurrentDocument() {
        var doc = currentDocuments[currentDocIndex];
        console.log('doc', doc);

        Swal.fire({
            title: 'Delete Document?',
            html: `Are you sure you want to delete<br><strong>"${doc.display_name}"</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, delete it!',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel',
            reverseButtons: true,
            backdrop: 'rgba(0,0,0,0.8)'
        }).then((result) => {
            if (result.isConfirmed) {
                performDelete(doc);
            }
        });
    }

    function performDelete(doc) {
        var deleteBtn = document.querySelector('.doc-viewer-delete');
        var originalHTML = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        deleteBtn.disabled = true;
        
        $.ajax({
            url: '/maif/documents/delete/' + encodeURIComponent(doc.filename),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    currentDocuments.splice(currentDocIndex, 1);
                    
                    if (currentDocuments.length === 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Document deleted successfully. No more documents to display.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            closeDocViewer();
                            location.reload();
                        });
                    } else {
                        if (currentDocIndex >= currentDocuments.length) {
                            currentDocIndex = currentDocuments.length - 1;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Document has been deleted.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        displayDocument();
                        updateNavButtons();
                        
                        deleteBtn.innerHTML = originalHTML;
                        deleteBtn.disabled = false;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: response.message || 'Failed to delete document'
                    });
                    deleteBtn.innerHTML = originalHTML;
                    deleteBtn.disabled = false;
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'Failed to delete document';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                deleteBtn.innerHTML = originalHTML;
                deleteBtn.disabled = false;
            }
        });
    }

    function showTemporaryMessage(message, type = 'success') {
        var messageEl = document.createElement('div');
        messageEl.className = `doc-viewer-message ${type}`;
        messageEl.textContent = message;
        document.body.appendChild(messageEl);
        
        setTimeout(() => {
            messageEl.classList.add('fade-out');
            setTimeout(() => messageEl.remove(), 300);
        }, 2000);
    }

    function changeDocument(direction) {
        var newIndex = currentDocIndex + direction;
        
        if (newIndex >= 0 && newIndex < currentDocuments.length) {
            currentDocIndex = newIndex;
            displayDocument();
            updateNavButtons();
        }
    }

    function updateNavButtons() {
        var prevBtn = document.querySelector('.doc-viewer-prev');
        var nextBtn = document.querySelector('.doc-viewer-next');
        
        if (currentDocIndex === 0) {
            prevBtn.classList.add('disabled');
        } else {
            prevBtn.classList.remove('disabled');
        }
        
        if (currentDocIndex === currentDocuments.length - 1) {
            nextBtn.classList.add('disabled');
        } else {
            nextBtn.classList.remove('disabled');
        }
        
        if (currentDocuments.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'block';
        }
    }

    function handleKeyPress(e) {
        if (e.key === 'ArrowLeft') {
            changeDocument(-1);
        } else if (e.key === 'ArrowRight') {
            changeDocument(1);
        } else if (e.key === 'Escape') {
            closeDocViewer();
        } else if (e.key === 'Delete') {
            deleteCurrentDocument();
        }
    }

    var touchStartX = 0;
    var touchEndX = 0;

    // document.getElementById('docViewerModal').addEventListener('touchstart', function(e) {
    //     touchStartX = e.changedTouches[0].screenX;
    // }, false);

    document.getElementById('docViewerModal').addEventListener(
        'touchstart',
        function (e) {
            touchStartX = e.changedTouches[0].screenX;
        },
        { passive: true }
    );


    // document.getElementById('docViewerModal').addEventListener('touchend', function(e) {
    //     touchEndX = e.changedTouches[0].screenX;
    //     handleSwipe();
    // }, false);
    document.getElementById('docViewerModal').addEventListener(
        'touchend',
        function (e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        },
        { passive: true }
    );

    function handleSwipe() {
        var swipeThreshold = 50;
        
        if (touchEndX < touchStartX - swipeThreshold) {
            changeDocument(1);
        }
        
        if (touchEndX > touchStartX + swipeThreshold) {
            changeDocument(-1);
        }
    }

    function loadAndViewDocuments(routeNo) {
        console.log('sample')
        $.ajax({
            url: '/maif/documents/' + routeNo,
            type: 'GET',
            success: function(response) {
                if(response.success && response.documents.length > 0) {
                    console.log(response.documents);
                    openDocViewer(response.documents, 0);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'No LDDAP Found!',
                        text: 'No documents found for this route.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            },
            error: function() {
                alert('Failed to load documents.');
            }
        });
    }

    $(document).on('click', '.btn-view-doc', function() {
        var routeNo = $(this).data('route-no');
        loadAndViewDocuments(routeNo);
    });

    $(document).on('click', '.btn-view-single', function() {
        var documents = $(this).data('documents');
        var index = $(this).data('index');
        openDocViewer(documents, index);
    });

    $(document).on('click', '.doc-viewer-delete', function() {
        deleteCurrentDocument();
    });

    function lddap(routeNo){
        $('#route_no').val(routeNo);
        $('#uploadModal').modal('show');
    }

    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("documents.upload") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false
                    });
                    $('#uploadModal').modal('hide');
                    $('#uploadForm')[0].reset();
                } else {
                    // alert('Error: ' + response.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'Upload failed. Please try again.';
                // alert('Error: ' + message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    $('.filter-division').select2();
    $('.filter-section').select2();

    var doc_type = @json($type);
    var dv_id = 0;
    var id = 0;
    var con = 0;
    var util_id = 0;
    
    function confirmed(){

        var cs = $('.editable-input').val();
        $('#checkbox_' + util_id).prop('checked', true);

        var checkboxes = $('.confirm_check');
        var allChecked = checkboxes.filter(':not(:checked)').length == 0;

        if (allChecked) {
            $('.budget_obligate').css('display', 'block');
        } else {
            $('.budget_obligate').css('display', 'none');
        }
        $('#budget_confirm').modal('hide');
    }

    function displayFunds(route_no, proponent, id){
        util_id = id;
        $('#budget_confirm').modal('show');
        $('.confirm_budget').html(loading);
        $.get("{{ url('confirm-budget').'/' }}" + id, function(result) {
            $('.confirm_budget').html(result);
        });
    }

    function viewV1(route_no, d_id, d_dv_id, confirmation) {
        dv_id = d_dv_id;
        id = d_id;
        
        $('#view_v2').modal('show');
        $('.pre_body').html(loading);
        $.get("{{ url('pre-dv/budget/v2/').'/' }}" + doc_type + '/' + id, function(result) {
            $('.pre_body').html(result);
        });
    }

    function confirm(){
        $.get("{{ url('confirm').'/' }}" + dv_id, function(result) {
            Swal.fire({
                icon: 'success',
                title: 'Confirmed!',
                text: 'Disbursement was successfully confirmed!',
                timer: 1000, 
                showConfirmButton: false
            }).then(() => {
                if(con == 0){
                    location.reload(); 
                }
            });
        });
    }

    function obligate(){
        $('#confirm_dv').modal('hide');
        con = 1;
        confirm();
        $('#view_v2').modal('show');
        $('.pre_body').html(loading);
        $.get("{{ url('pre-dv/budget/v2/').'/' }}" + doc_type + '/' + id, function(result) {
            $('.pre_body').html(result);
        });
    }

    function openModal() {
        var routeNoo = event.target.getAttribute('data-routeId'); 
        var src = "http://192.168.110.17/dts/document/trackMaif/" + routeNoo;

        var base_url = "{{ url('/') }}";
        $('.modal-body').append('<img class="loadingGif" src="' + base_url + '/public/images/loading.gif" alt="Loading..." style="display:block; margin:auto;">');

        var iframe = $('#trackIframe');

        iframe.hide();

        iframe.attr('src', src);
    
        iframe.on('load', function() {
            iframe.show(); 
            $('.loadingGif').css('display', 'none');
        });

        $('#myModal').modal('show');
    }

    function putRoutes(form){
        $('#route_no').val($('#all_route').val());
        $('#currentID').val($('#release_btn').val());
        $('#multiple').val('multiple');
        $('#op').val(0);
    }

    function putRoute(form){
        var route_no = form.data('route_no');
        $('#route_no').val(route_no);
        $('#op').val(0);
        $('#currentID').val(form.data('id'));
        $('#multiple').val('single');
    }

    $('.filter-division').on('change',function(){
        // checkDestinationForm();
        var id = $(this).val();
        $('.filter-section').html('<option value="">Select section...</option>')
        $.get("{{ url('getsections').'/' }}"+id, function(result) {
            $.each(result, function(index, optionData) {
                $('.filter-section').append($('<option>', {
                    value: optionData.id,
                    text: optionData.description
                }));  
            });
        });
    });

    var s_ident = 0; 

     //select_all
    $('.select_all').on('click', function(){
        if(s_ident == 0){
            document.getElementById('release_btn').style.display = 'inline-block';
            $('.group-releaseDv').prop('checked', true);
            $('.group-releaseDv').trigger('change');
            s_ident = 1; 
        }else{
            document.getElementById('release_btn').style.display = 'none';
            $('.group-releaseDv').prop('checked', false);
            $('.group-releaseDv').trigger('change');
            s_ident = 0; 
        }
    });

</script>
@endsection