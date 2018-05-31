@if(isset($filesByType) && !empty($filesByType))
    <div class="files">
        @foreach($filesByType as $files)
            @foreach($files as $file)
                <div class="file-row">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{$file->name}}<a href="{{ action('FilesController@download', ['id' => $file->file_id,'code' => $file->file_code])}}" class="file-download-btn"><i class="fa fa-download" aria-hidden="true"></i></a>
                </div>
                {{--<p><a class="files-link" href="{{ action('FilesController@download', ['id' => $file->file_id,'code' => $file->file_code])}}"><span class="fa fa-download" style="margin-right: 10px" aria-hidden="true"></span>{{$file->name}}</a></p>--}}
            @endforeach
        @endforeach
    </div>
    @if(isset($filesByType->{"images"}) && !empty($filesByType->{"images"}))
        <div id="carouselImages" class="row carousel slide image-gallery" data-ride="carousel">
            <div class="col-12 content-title">{{ ONE::transCb("cb_image_gallery"}}</div>
            <div class="carousel-inner" role="listbox" style="margin-top:15px;">
                {{--<div class="col-md-4">--}}
                {{--<div class="card" data-toggle="modal" data-target="#imgModal">--}}
                {{--<div class="card-img-top card-img-top-250">--}}
                {{--<img class="img-fluid" src="{{action('FilesController@download', [$filesByType->{"images"}[0]->file_id, $filesByType->{"images"}[0]->file_code])}}" alt="Carousel 1">--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                @php $j = 0; @endphp
                @for($i=0; $i<count($filesByType->{"images"}); $i++)
                    @if($j == 0)
                        <div class="carousel-item {{ $i == 0 ? ' active' : '' }}">
                            @endif

                            <div class="col-md-4">
                                <div class="card" data-toggle="modal" data-target="#imgModal">
                                    <div class="card-img-top card-img-top-250">
                                        <img class="img-fluid" src="{{action('FilesController@download', [$filesByType->{"images"}[$i]->file_id, $filesByType->{"images"}[$i]->file_code])}}" alt="Carousel 1">
                                    </div>
                                </div>
                            </div>
                            @php $j++; @endphp
                            @if($j == 3 || $i == (count($filesByType->{"images"}) - 1))
                        </div>
                        @php $j = 0; @endphp
                    @endif
                @endfor
                {{--</div>--}}
            </div>

            @if(count($filesByType->{"images"})>=3)
                <a class="carousel-control-prev" href="#carouselImages" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">{{ ONE::transCb("cb_previous_image"}}</span>
                </a>
                <a class="carousel-control-next" href="#carouselImages" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">{{ ONE::transCb("cb_next_image"}}</span>
                </a>
            @endif
        </div>

        <!-- Modal to open image -->
        <div class="modal fade" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog image" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="Image" id="exampleModalLabel">{{ ONE::transCb("cb_image_modal_title"}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img id="imgModalContent">
                    </div>
                </div>
            </div>
        </div>

        <!-- To open the image inside modal -->
        <script>
            var button = document.getElementsByClassName('img-fluid');
            var modalContent = document.getElementById('imgModalContent');
            for (var i = 0; i < button.length; i++) {
                (function(index) {
                    button[index].onclick = function() {
                        $('#imgModal').modal('show');
                        modalContent.src = this.src;
                    }
                }(i));
            }
        </script>
    @endif
@endif