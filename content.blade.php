@extends('layouts.app')

@section('content')    
   <main class="app-content">  
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h4 mb-3">Page Content</h1>
                        <form method="POST" action="{{ route('updatecontent') }}" >
                            @csrf

                            @if(session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @endif
                            <input id="page_key" type="hidden" class="form-control" name="page_key" value="{{ $pageKey }}">
                            <div class="form-group">
                                <label for="name" class="col-form-label">Title</label>
                                <input id="title" type="text" class="form-control" name="title" value="{{ old('title', ( isset( $data->pc_title )?$data->pc_title:'' )) }}" placeholder="Enter page title">
                                {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-form-label">Page Content</label>
                                <textarea id="description" class="tinymce-editor form-control" name="description" placeholder="Enter page content">{{( isset( $data->pc_description )?$data->pc_description:'' )}}</textarea>
                                {!! $errors->first('description', '<p class="text-danger">:message</p>') !!}
                            </div>

                            <div class="form-group mb-0 mt-4">
                                <button type="submit" class="btn btn-md btn-primary">
                                    Update content
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>  
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            height: 600,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_css: '//www.tiny.cloud/css/codepen.min.css'
        });
    </script>
@endsection

