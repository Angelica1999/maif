@extends('layouts.app')

@section('content')
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Fundsource" value="">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">Admin Cost</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(count($fundsources) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:150px;">SAA NO.</th>
                        <th style="width:150px;">Admin Cost</th>
                        <th style="width:150px;">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fundsources as $fundsource)
                        <tr>
                            <td>{{ $fundsource->saa }}</td>
                            <td>{{ number_format($fundsource->admin_cost, 2, '.', ',') }}</td>
                            <td>{{ $fundsource->user->lname . ', '. $fundsource->user->fname}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No facility found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $fundsources->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        
    </script>
@endsection