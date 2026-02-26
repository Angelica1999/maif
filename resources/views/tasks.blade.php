@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="Search..." value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button>
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">View All</button>
                    </div>
                </div>
            </form>
            <h4 class="card-title">NOTES</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            @if(isset($notes) && $notes->count() > 0)
                <div class="row">
                    @foreach($notes as $row)
                        <div class="col-md-3  ">
                        <div class="form-group">
                            @if($row->status == 0)
                                <textarea name="note" class="form-control" rows="10" style="color:blue; resize: vertical;" onclick="displayNote({{ $row->id }})" readonly>{{ $row->notes }}</textarea>
                            @else
                                <textarea name="note" class="form-control" rows="10" style="color:green; resize: vertical;" onclick="displayNote({{ $row->id }})" readonly>{{ $row->notes }}</textarea>
                            @endif
                        </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                <i class="typcn typcn-times menu-icon"></i>
                    <strong>No notes found!</strong>
                </div>
            @endif
            <div class="pl-5 pr-5 mt-5">
                {!! $notes->appends(request()->query())->links('pagination::bootstrap-5') !!}  
            </div>
        </div>
    </div>
</div>
<div class="modal fade" data-backdrop="static" id="update_note" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route('update.note') }}" method="POST" style="background-color: #fff3cd;">
                @csrf
                <input type='hidden' name='id', id='id'>
                <div class="" style="padding:10px">
                    <h4 class="text-success">
                        <i style="font-size:30px" class="typcn typcn-document-text menu-icon"></i> New Note
                    </h4>
                    <hr />
                    <div class="form-group">
                        <textarea name="note" id="note_t" class="form-control note_text" rows="25" style="resize: vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="background-color:lightgray" class="btn btn-xs btn-default" data-dismiss="modal">
                        <i class="typcn typcn-times menu-icon"></i> Close
                    </button>
                    <button type="button" class="btn btn-danger btn-xs">
                        <i class="typcn typcn-location-arrow menu-icon"></i> Delete
                    </button>
                    <button type="submit" class="btn btn-success btn-xs btn-submit">
                        <i class="typcn typcn-location-arrow menu-icon"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $('#update_note').on('hide.bs.modal', function () {
        $(this).find('input, select, textarea, button').blur();
    });
    
    function displayNote(id){
        $('#update_note').modal('show');
        var data = @json($all);
        data = data.filter(item=>item.id == id);
        $('#id').val(id);
        $('.note_text').val(data[0].notes);

    }
    $('.btn-danger').on('click', function(){
        window.location.href = "notepad/remove/" + $('#id').val();
    });
</script>
@endsection
