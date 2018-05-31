<div id="parametersGroupDiv_{{ $parameterCounter }}" class="parametersGroupDiv" style="display:none;">          
    <!-- Parameter -->
    <div id="parameters_add_{{ $parameterCounter }}">
        <!-- Templates -->
        <div id="paramTemplateSelectDiv{{ $parameterCounter }}" class="form-group">
            <label for="title">{!! trans("parameter.template") !!}</label>
            <select id="paramTemplateSelect{{ $parameterCounter }}" class="form-control" name="paramTemplateSelect{{ $parameterCounter }}"
                    onchange="chooseTemplate(this.value,{{ $parameterCounter }})">
                <option value="">{!! trans("privateCbs.selectOneParameterType") !!}</option>
                @foreach($parameterTemplates as $template)
                    <option value="{{$template->id}}">{{$template->parameter}}</option>
                @endforeach
            </select>
            <!--
            <a class="btn btn-success btn-sm" onclick="chooseTemplate( $('#paramTemplateSelect{{ $parameterCounter }}').val() ,{{ $parameterCounter }})">{{ trans("cb.use") }}</a>
            -->
            <hr/>
        </div>

        <div id="template_selected_{{ $parameterCounter }}">

        </div>
    </div>                
</div>

