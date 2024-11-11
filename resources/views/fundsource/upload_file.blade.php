@extends('layouts.app')

@section('content')
<style>
</style>
<div class="container-fluid col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group float-right w-50" style="min-width: 600px;">
                    <input type="text" class="form-control" name="keyword" placeholder="SAA NO." value="{{$keyword}}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info" type="submit"><img src="\maif\public\images\icons8_search_16.png">Search</button> 
                        <button class="btn btn-sm btn-warning text-white" type="submit" name="viewAll" value="viewAll"><img src="\maif\public\images\icons8_eye_16.png">&nbsp;View All</button>
                        <button type="button" href="#upload_files" data-backdrop="static" data-toggle="modal" class="btn btn-success btn-md"><img src="\maif\public\images\icons8_upload_16.png">&nbsp;Upload</button> 
                    </div>
                </div>
            </form>
            <h4 class="card-title">FUNDSOURCE FILES</h4>
            <p class="card-description">
                MAIF-IPP
            </p>
            <div class="row">
            @if(count($files)>0)
                @foreach($files as $file)
                    <div class="col-md-4 mt-2 grid-margin grid-margin-md-0 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <img src="{{ url('storage/app/' . $file->path) }}" alt="Image" class="img-fluid mb-2" style="width: 100%;">
                                </div>
                                <div class="text-center">
                                    <a href="#sample" data-toggle="modal" onclick="image('{{ $file->path }}')">{{ $file->saa_no }}</a>
                                    <img src="\maif\public\images\icons8_delete_16.png" onclick="deleteImage({{$file->id}})">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-danger" role="alert" style="width: 100%;">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No image for fundsource uploaded yet!</strong>
                </div>
            @endif
            </div>
            <div class="pl-5 pr-5 mt-5">
                {!! $files  ->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sample" tabindex="-1" role="dialog" aria-hidden="true" style="background: transparent; border: none;">
    <div class="modal-dialog" role="document" style="background: transparent; border: none;">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal_body" style="background: transparent; border: none;">
                <div id="sample_modal" style="background: transparent; border: none;">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="upload_files" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-success"><i style = "font-size:30px"class="typcn typcn-location-arrow menu-icon"></i>Upload Files</h4><hr />
            </div>
            <form action="{{route('upload.files')}}" method="post" enctype="multipart/form-data">
                <div class="modal_body" style="padding:15px">
                    @csrf
                    <div class="form-group">
                        <b><label>Files:</label><b><br>
                        <input style="" class="form-control" type="file"  id="file-upload" name="files[]" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style = "background-color:lightgray"  class="btn btn-default" data-dismiss="modal"><i class="typcn typcn-times menu-icon"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-submit" onclick=""><img src="\maif\public\images\icons8_upload_16.png">&nbsp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script>

       function image(path) {
            console.log('click');
            $('#sample_modal').html('<img src="{{ url('storage/app/') }}/' + path + '" alt="Image" class="img-fluid mb-2" style="width: 100%;">');
        }

        function deleteImage(id){
            var answer = confirm('Are you sure you wanted to remove this image?');
            if(answer){
                var url = "{{ url('/fundsources/remove').'/' }}"+id;
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(result) {
                        Lobibox.notify('success', {
                            msg: 'Image successfully removed!'
                        });
                        location.reload();
                    }
                });
            }
        }

    </script>
    
@endsection