<div class="langMenu">
    @foreach(ONE::getAllLanguages() as $language)
        <div class="langBtn">
            @if(ONE::getAppLanguageCode() == $language->code)
                <a href="#" class="active" onclick="updateLanguage('{{$language->code}}')">{{strtoupper($language->code)}}</a>
            @else
                <a href="#" onclick="updateLanguage('{{$language->code}}')">{{strtoupper($language->code)}}</a>
            @endif
        </div>
        @if(!$loop->last)
            <span>|</span>
        @endif
    @endforeach
</div>


<script>
    function updateLanguage(langCode){

        $.ajax({
            url: '{{action("OneController@setLanguage")}}',
            method: 'POST',
            data: {
                langCode: langCode,
                _token: "{{ csrf_token()}}"
            },
            success: function(action){
                location.reload();
            },
            error: function(msg){
                console.log(msg);
                alert('failure');
            }
        });
    }
</script>