@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <form action="/upload" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="files[]" multiple>
        <button type="submit">Upload</button>
    </form>
</div>



@endsection

@section('js')

<script>


</script>

@endsection