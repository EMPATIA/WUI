<?php
/**
 * Created by PhpStorm.
 * User: pedrobc
 * Date: 09/11/2016
 * Time: 17:19
 *
 * Includes Annotator.js plugin
 *
 * Dependencies for this Macro - to include in page styles/scripts:
 *
 *      <!-- Annotator test libraries - CSS/js-->
 *      <script type="text/javascript"  src="{{ asset('js/annotator/annotator-full.min.js')}}"></script>
 *      <link rel="stylesheet" href="{{ asset('css/annotator/annotator.min.css')}}">
 *      <link rel="stylesheet" href="{{ asset('css/annotator/annotator-main.css')}}">
 *
 *
 * @param $mainDiv, $data
 * @return html
 *
 */


Html::macro('oneAnnotator', function($mainDiv, $topicKey , $userKey, $data = null) {
    $html = "";

    ?>

    <script>

        //  ajax setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo csrf_token()?>'
            }
        });

        // ========================================================================================================================  Global VARs
        var content;
        var tags;
        var mainDiv = '<?php echo $mainDiv ?>';
        var userKey = '<?php echo Session::get('user')->user_key ?>';

        // ======================================================================================================================== Plugin Development
        Annotator.Plugin.NewField = function (element, tags) {
            var plugin = {};

            plugin.pluginInit = function () {

                var annotator = this.annotator;

                annotator.subscribe("beforeAnnotationCreated", function(annotation){
                    newFieldConstruction();
                   /* $('.new-field').empty();

                    var select = "<select class=\"tags\" name=\"select2test\" multiple=\"multiple\"></select>";

                    $('form > ul > li:last').append(select);

                    $(".tags").select2({
                        data: tags,
                        //tags: true,
                        //tokenSeparators: [','],
                        placeholder: "Select"           //TODO - translation field
                    });*/
                })

                .subscribe("annotationEditorShown", function(annotation){
                    newFieldConstruction();
                })

                .subscribe("annotationCreated", function (annotation) {
                    console.info("The annotation: %o has just been created!", annotation)
                })
                .subscribe("annotationUpdated", function (annotation) {
                    console.info("The annotation: %o has just been updated!", annotation)
                })

                //TODO --> Temporary patch for .annotator-controls duplicate - needs solution
                $(mainDiv).click(function () {
                    $('.new-field').find('.annotator-controls').remove();
                })

            };

            return plugin;
        }

        var newFieldConstruction = function () {
            $('.new-field').empty();

            var select = "<select class=\"tags\" name=\"select2test\" multiple=\"multiple\"></select>";

            //var radio = "<label class=\"radio-inline\"><input type=\"radio\" name=\"optradio\">Option 1</label><label class=\"radio-inline\"><input type=\"radio\" name=\"optradio\">Option 2</label>";

            /*
                        <div class="radio">
                            <label><input type="radio" name="optradio">Option 1</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Option 2</label>
                        </div>
                        <div class="radio disabled">
                            <label><input type="radio" name="optradio" disabled>Option 3</label>
                        </div>


             <label class="radio-inline">
             <input type="radio" name="survey" id="Radios1" value="Yes">
             Yes
             </label>

            */
            /*for (var i = 0; i < tags.length; i++) {
                console.log(tags[i]);
                //Do something
            }*/


            $('form > ul > li:last').append(select);

            $(".tags").select2({
                data: tags,
                //tags: true,
                //tokenSeparators: [','],
                placeholder: "Select"           //TODO - translation field
            });
        }

        var annotatorMain = function () {

            content = $(mainDiv).annotator();

            // On submit action, includes select2 value(s) inside hidden TAG inputs
            $('.annotator-save').click(function () {

                var tagArray = $('.tags').val();

                //Create input string of tags from array
                var inputText = tagArray.join(" ");

                $('#annotator-field-1').val(""+inputText+"");

            });

            //Annotator - Tags plugin ADD
            content.annotator('addPlugin', 'Tags');
            content.annotator('addPlugin', 'NewField', tags);

            content.annotator('addPlugin','Permissions', {
                user: userKey,

                showViewPermissionsCheckbox: false,
                showEditPermissionsCheckbox: false
            });

            //Hide input created by TAGs plugin
            $('#annotator-field-1').parent().addClass('hidden');


            //Add new li element to Annotator widget
            var list = "<li class=\"annotator-item new-field\"></li>";
            //var select = "<select class=\"js-data-example-array\" name=\"select2test\" multiple=\"multiple\"></select>";

            $('form > ul').append(list);

            /*var select2_box = $("<li class=\"annotator-item\"><select class=\"js-data-example-array\" name=\"select2test\" multiple=\"multiple\"></select></li>");*/
            //$('.new-field').find('.annotator-controls').remove();


            //Annotator - Store plugin ADD
            content.annotator('addPlugin', 'Store', {

                // The endpoint of the store on your server.
                prefix: ' ',

                urls: {
                    // These are the default URLs.
                    create: '<?php echo action('AnnotatorController@store',$topicKey, false) ?>',
                    update: '<?php echo action('AnnotatorController@update', false) ?>/:id',
                    destroy: '<?php echo action('AnnotatorController@destroy',false) ?>/:id',
                    search: '<?php echo action('AnnotatorController@show', $topicKey, false) ?>'
        },

            loadFromSearch: {
                //'limit': 20,
                 //   'all_fields': 1,
                 //   'uri': 'https://empatia-dev.onesource.pt:5803/annotator'
            },

            annotationData: {
                /*'uri': 'https://empatia-dev.onesource.pt:6712/annotator',*/
                'topicKey': '<?php echo $topicKey ?>',
            }

        });



        }


        /**
         * @param $topic_key
         * @return Exception|\Illuminate\Http\JsonResponse
         *
         * ======================================================================================================================== Main function
         *
         */
        $(document).ready(function(){


            //  GET data for tags/types of annotations

            $.post('<?php echo URL::action('AnnotatorController@tags', $topicKey ) ?>',  //TODO - change plain text parameter to variable
                function (data) {

                    tags = data;

                })
            .done(function (result) {

                annotatorMain();

            })
            .fail(function (xhr, textStatus, errorThrown) {
            })
            .always(function () {
            });


        });
    </script>


    <?php

    return $html;
});