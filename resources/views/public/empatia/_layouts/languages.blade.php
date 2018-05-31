
<ul id="lang" class="nav navbar-nav hidden-sm hidden-xs">
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">{{strtoupper(ONE::getAppLanguageCode())}}<span class="caret"></span></a>
        <ul class="dropdown-menu">
            @foreach(ONE::getAllLanguages() as $language)
                <li class="langList">
                    <a href="#" onclick="updateLanguage('{{$language->code}}')"  {{ONE::getAppLanguageCode() == $language->code? 'selected' : ''}}>{{$language->name}}</a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>


<li id="lang" class="nav navbar-nav visible-sm visible-xs">
    <ul class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">{{strtoupper(ONE::getAppLanguageCode())}}<span class="caret"></span></a>
        <li class="dropdown-menu">
            @foreach(ONE::getAllLanguages() as $language)
                <ul class="langList">
                    <a href="#" onclick="updateLanguage('{{$language->code}}')"  {{ONE::getAppLanguageCode() == $language->code? 'selected' : ''}}>{{$language->name}}</a>
                </ul>
            @endforeach
        </li>
    </ul>
</li>


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