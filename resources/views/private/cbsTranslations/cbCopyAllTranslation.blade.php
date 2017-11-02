@if($code !='code')

<div class="card-header">
          {{trans('privateCbsTranslations.CbTranslation')}}
      </div>
      <div class="card-body" style="overflow: scroll;">

      <div class="row">
        <div class="col-6 col-md-3" style="margin-top:20px; margin-left:15px;">
            <label for="name">{{trans("privateCbsTranslations.code")}}</label>
            <input type="text" name="{{$code}}" id="{{$code}}"  value="{{$code}}" class="form-control codes code"></input></br>
        </div>
        <div class="" style="margin-top:35px;">
        <a type="button" class="btn btn-flat btn-danger pull-right" style="margin-right:15px;" href="javascript:deleteTranslation({{$code}})"><i class="fa fa-times" aria-hidden="true"></i></a>

        <a type="button" class="btn btn-flat btn-success pull-right" style="margin-right:25px;" href=""><i class="fa fa-files-o" aria-hidden="true"></i></a>
        </div>
      </div>

        <div class="row">
        </br>
        <div class="col-6 col-md-1" style="margin-top:20px; margin-left:15px;" >
          <label for="name">{{trans("privateCbsTranslations.during")}}</label>
        </div>

        <div style="margin-left:250px;">
          @if(! empty($languages))
            @foreach ($languages as $language)
              @foreach ($translations as $key =>$trans)
                @if($key==$language->code)
                  <div class="col-6 col-md-2">
                    <label for="name">{{$language->name}}</label>
                    <input type="text" name="during_{{$language->code}}" id="during_{{$language->code}}" value="{{$trans}}" class="form-control translations translation {{$code}}"></input>
                </div>
              @endif
              @endforeach
            @endforeach
          @endif
       </div>

     </div>


      <div class="row">
        </br>
        <div class="col-6 col-md-1" style="margin-top:25px; margin-left:15px;">
          <label for="name">{{trans("privateCbsTranslations.before")}}</label>
        </div>

        <div style="margin-left:250px;">
          @if(! empty($languages))
            @foreach ($languages as $language)
              @foreach ($translations as $key=> $trans)
                  @if($key==$language->code)
                  <div class="col-6 col-md-2">
                  </br>
                  <input type="text" name="before_{{$language->code}}" id="before_{{$language->code}}" value="{{$trans}}" class="form-control translations translation {{$code}}"></input>
                 </div>
                @endif
            @endforeach
          @endforeach
          @endif

        </div>
      </div>

      <div class="row">
        </br>
        <div class="col-6 col-md-1" style="margin-top:30px; margin-left:15px;">
          <label for="name">{{trans("privateCbsTranslations.after")}}</label>
        </div>
        <div style="margin-left:250px;">
          @if(! empty($languages))
            @foreach ($languages as $language)
              @foreach ($translations as $key=> $trans)
                  @if($key==$language->code)
              <div class="col-6 col-md-2">
                  </br>
                <input type="text" name="after_{{$language->code}}" id="after_{{$language->code}}" value="{{$trans}}" class="form-control translations translation {{$code}}"></input>
              </div>
              @endif
            @endforeach
          @endforeach

          @endif

        </div>

      </div>

      </br>
      <div style="margin-left:15px;">
        <a type="" class="btn btn-flat empatia pull-left" href="javascript:createTranslation({{$code}})">{!! trans("privateCbsTranslations.createOrEdit") !!}</a>
        <a type="" class="btn btn-flat btn-preview pull-right" href="javascript:cancel()">{!! trans("privateCbsTranslations.cancel") !!}</a>
  </div>

     </div>
     <label id="erro"></label>

   @else

           <div class="row">
             </br>
             <div class="col-6 col-md-1" style="margin-top:25px; margin-left:15px;">
               <label for="name">{{trans("privateCbsTranslations.before")}}</label>
             </div>

             <div style="margin-left:250px;">
               @if(! empty($languages))
                  @foreach ($languages as $language)
                    @foreach ($translations as $key=>$trans)
                      @if($key==$language->code)
                     <div class="col-6 col-md-2">
                     </br>
                     <input type="text" name="before_{{$language->code}}" id="before_{{$language->code}}" value="{{$trans}}" class="form-control translationsNew translationNew"></input>
                    </div>

                  @endif
                  @endforeach
                 @endforeach

               @endif

             </div>
           </div>

           <div class="row">
             </br>
             <div class="col-6 col-md-1" style="margin-top:30px; margin-left:15px;">
               <label for="name">{{trans("privateCbsTranslations.after")}}</label>
             </div>
             <div style="margin-left:250px;">
               @if(! empty($languages))
                 @foreach ($languages as $language)
                   @foreach ($translations as $key=>$trans)
                     @if($key==$language->code)
                   <div class="col-6 col-md-2">
                       </br>
                     <input type="text" name="after_{{$language->code}}" id="after_{{$language->code}}" value="{{$trans}}" class="form-control translationsNew translationNew"></input>
                   </div>
                 @endif
                 @endforeach
                @endforeach
               @endif

             </div>

           </div>


   @endif
