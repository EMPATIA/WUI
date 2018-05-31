@extends('private._private.index')

@section('content')

<!-- Posts Manager List -->
<div class="card flat">
    <div class="card-header">{{ trans('privatePosts.posts') }}</div>
    <div class="box-body">
        
        <div class="adv-search">
            
            <form name="advSearch" class="form-horizontal">
                <fieldset>

                    <!-- Advanced search -->
                    <legend>{{ trans("privatePosts.advancedSearch")}}</legend>

                    <div class="form-group">
                      <label class="col-sm-12 col-md-2 form-control-label" for="advancedFilter">{{ trans("privatePosts.advFilter")}}</label>
                      <div class="col-sm-12 col-md-10">
                        <select id="advancedFilter" name="advancedFilter" multiple="multiple" class="select2privatePosts" onchange="showPostManagerDataTable()">
                          <option @if($showWithAbuses==1) selected @endif value="showWithAbuses">{{ trans("privatePosts.showWithAbuses") }}</option>
                          <option @if($showCommentsNeedsAuth==1) selected @endif value="showCommentsNeedsAuth">{{ trans("privatePosts.showCommentsNeedsAuth")}}</option>
                        </select>
                      </div>
                    </div>

                </fieldset>
            </form>

        </div>

        <table id="postManager_list" class="table table-responsive table-hover">
            <thead>
            <tr>
                <th style="width:40%">{{ trans('privatePosts.topic') }}</th>
                <th style="width:30%">{{ trans('privatePosts.message') }}</th>
                <th>{{ trans('privatePosts.postsAbuses') }}</th>
                <th>{{ trans('privatePosts.postsCreated') }}</th>
                <th></th>
            </tr>
            </thead>

        </table>
    </div>
</div>
@endsection


@section('scripts')
    <script>
            showPostManagerDataTable();

            function showPostManagerDataTable(){
                
                var showCommentsNeedsAuth = 0;
                var showWithAbuses = 0;
                
                if( $("#advancedFilter").val() != null && jQuery.inArray( "showCommentsNeedsAuth", $("#advancedFilter").val() )  > -1){
                    showCommentsNeedsAuth = 1;
                }
                if( $("#advancedFilter").val() != null && jQuery.inArray( "showWithAbuses", $("#advancedFilter").val() ) > -1 ){
                    showWithAbuses = 1;   
                }

                // Posts List
                $('#postManager_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                        search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    bDestroy: true,
                    ajax: {
                            "url" : '{!! action('PostManagerController@getIndexTable') !!}',
                            "type": "POST",
                            "data" : {
                                "showCommentsNeedsAuth" : showCommentsNeedsAuth,
                                "showWithAbuses": showWithAbuses
                            }   
                    },
                    columns: [
                        { data: 'topic', name: 'topic' },
                        { data: 'message', name: 'message' },
                        { data: 'abuses_count', name: 'abuses_count' },
                        { data: 'created_by', name: 'created_by', width: '15%' },                                                
                        { data: 'action', name: 'action', searchable: false, orderable: false, width: '15px' }
                    ],
                    order: [['3', 'desc']]
                });
            }
            
            $(".select2privatePosts").select2({
              templateResult: function (data) {
                var $res = $('<span></span>');
                var $check = $('<input type="checkbox" class="inputCheckBoxSelect2" style="margin-right:5px;" />');

                $res.text(data.text);

                if (data.element) {
                  $res.prepend($check);
                  $check.prop('checked', data.element.selected);
                }

                return $res;
              }
            });            
    </script>
@endsection
    
    
    
@section('header_styles')
<style>
.adv-search{
     margin-top: 20px;
     margin-bottom: 30px;
}    
    
.select2-container--default .select2-search--inline .select2-search__field {
    border: 0;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #f4f4f5;
}
.select2privatePosts{
    width: 80%;
}
</style>
@endsection
    