@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
    .annex-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e0e0e0;
    }

    .tab {
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #666;
        background-color: #f5f5f5;
        border: 1px solid #e0e0e0;
        border-bottom: none;
        transition: all 0.3s ease;
        position: relative;
        top: 2px;
    }

    .tab:first-child {
        border-top-left-radius: 4px;
    }

    .tab:last-child {
        border-top-right-radius: 4px;
    }

    .tab:hover {
        background-color: #fff;
        color: #333;
    }

    .tab.active {
        background-color: #fff;
        color: #000;
        border-bottom: 2px solid #fff;
        font-weight: 600;
    }

    .tab:not(:last-child) {
        border-right: none;
    }

    .loading {
        opacity: 0.5;
        pointer-events: none;
    }

</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card"> 
    <div class="card"> 
        <div class="card-body"> 
            <div class="d-flex justify-content-between align-items-center"> 
                <div> 
                <h1 class="card-title" style="display: flex; align-items: center; gap: 20px;">
                    <span style="font-size: 22px;">
                        {{ strtoupper($facility) }}
                    </span>
                    <a href="{{ route('fur.facilities') }}" class="return-link" style="
                        display: inline-flex; 
                        align-items: center; 
                        gap: 8px; 
                        text-decoration: none; 
                        color: #3498db; 
                        font-size: 14px; 
                        font-weight: 500;
                        padding: 6px 12px;
                        border-radius: 4px;
                        transition: all 0.3s ease;
                        background-color: #f8f9fa;
                        border: 1px solid #e0e0e0;
                        "onmouseover="this.style.backgroundColor='#3498db'; this.style.color='#fff'; this.style.borderColor='#3498db';" 
                        onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.color='#3498db'; this.style.borderColor='#e0e0e0';">
                        <i class="fa fa-arrow-left" style="font-size: 12px;"></i>
                        <span style="text-transform: capitalize;">Main View</span>
                    </a>
                </h1>
                    <p class="card-description">MAIF-IPP</p> 
                </div> 
                <div class="annex-tabs">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="tab annex_a {{ $tab_type == 1 ? 'active' : '' }}" data-url="{{ route('fur.annex_a', ['id'=> $id, 'year' => $year]) }}">ANNEX A</div>
                    <div class="tab {{ $tab_type == 2 ? 'active' : '' }}" data-url="{{ route('fur.fc_annex_b', ['id'=> $id, 'year' => $year, 'viewAll' => $viewAll]) }}">ANNEX B</div>
                </div>
            </div> 
            <div class="table-responsive mt-3" id="patient_table_container"> 
                {{-- Initial content will be loaded here --}}
            </div> 
        </div> 
    </div> 
</div>
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    
    setInterval(function() {
        if($('#yearPicker').length > 0){
            $('#yearPicker').datepicker({
                format: "yyyy",
                viewMode: "years", 
                minViewMode: "years",
                autoclose: true,
                orientation: "bottom auto"
            }).on('changeDate', function(e) {
                this.form.submit();
            });
            $('#yearPicker').removeAttr('disabled');
        } 
    }, 1000);

    document.addEventListener('DOMContentLoaded', function() {
        var tabs = document.querySelectorAll('.tab');
        var container = document.getElementById('patient_table_container');
        
        var activeTab = document.querySelector('.tab.active');
        if (activeTab) {
            loadContent(activeTab.getAttribute('data-url'));
        } else {
            loadContent(tabs[0].getAttribute('data-url'));
        }
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                
                this.classList.add('active');
                
                var url = this.getAttribute('data-url');
                
                loadContent(url);
            });
        });
        
        function loadContent(url) {
            container.classList.add('loading');
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    container.classList.remove('loading');
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    container.innerHTML = '<p class="text-danger">Error loading content. Please try again.</p>';
                    container.classList.remove('loading');
                });
        }
    });
</script>
    
@endsection